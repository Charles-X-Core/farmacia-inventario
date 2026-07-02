<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalePayment;
use App\Models\SaleDetailAllotment;
use App\Models\Client;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\Allotment;
use App\Models\AllotmentDetail;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $sales = Sale::with(['client', 'payments.paymentMethod', 'user', 'warehouse'])
            ->where('company_id', $user->company_id)
            ->latest()
            ->get();

        return view('ventas.index', compact('sales'));
    }

    public function create()
    {
        $user = auth()->user();

        $clients = Client::where('company_id', $user->company_id)
            ->where('status', true)
            ->get();

        $products = Product::where('company_id', $user->company_id)
            ->where('is_sale', true)
            ->where('status', true)
            ->get();

        $paymentMethods = PaymentMethod::where('status', true)->get();

        $warehouse = \App\Models\Warehouse::whereHas('branches', function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        })->first();

        $allotments = [];
        $loteProductos = [];

        if ($warehouse) {
            $allotmentsRaw = Allotment::where('warehouse_id', $warehouse->id)
                ->where('status', true)
                ->where('flag_closed', false)
                ->with(['allotmentDetails' => function ($q) {
                    $q->where('status', true);
                }])
                ->get();

            $allotments = $allotmentsRaw->map(function ($a) {
                $detail = $a->allotmentDetails->first();
                return [
                    'id' => $a->id,
                    'code' => $a->code,
                    'expiration_date' => $a->expiration_date,
                    'warehouse_id' => $a->warehouse_id,
                    'quantity' => $detail ? $detail->quantity : 0,
                    'quantity_dispatch' => $detail ? $detail->quantity_dispatch : 0,
                    'available' => $detail ? ($detail->quantity - $detail->quantity_dispatch) : 0,
                ];
            })->toArray();

            $loteProductos = DB::table('allotment_details')
                ->join('products', 'allotment_details.product_id', '=', 'products.id')
                ->where('products.company_id', $user->company_id)
                ->select('allotment_details.allotment_id', 'products.id as product_id', 'products.name as product_name')
                ->get()
                ->toArray();
        }

        $series = Series::whereHas('terminal', function ($q) use ($user) {
            $q->where('id', $user->terminal_id);
        })->where('movement_type_id', 3)->first();

        return view('ventas.create', compact('clients', 'products', 'paymentMethods', 'allotments', 'loteProductos', 'series', 'warehouse'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'discount' => 'nullable|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.selected_lots' => 'nullable|array',
            'products.*.selected_lots.*.allotmentId' => 'required_with:products.*.selected_lots|exists:allotments,id',
            'products.*.selected_lots.*.quantity' => 'required_with:products.*.selected_lots|integer|min:1',
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();

        $warehouse = \App\Models\Warehouse::whereHas('branches', function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        })->find($request->warehouse_id);

        if (!$warehouse) {
            return back()->withErrors(['warehouse_id' => 'Almacén no válido para su empresa.'])->withInput();
        }

        if (!$user->terminal) {
            return back()->withErrors(['terminal' => 'No tiene terminal asignada.'])->withInput();
        }

        $series = Series::where('terminal_id', $user->terminal->id)
            ->where('movement_type_id', 3)
            ->first();

        if (!$series) {
            return back()->withErrors(['series' => 'No serie configurada para ventas.'])->withInput();
        }

        $subtotalGeneral = 0;
        $productsData = [];

        foreach ($request->products as $idx => $pData) {
            $product = Product::where('company_id', $user->company_id)->find($pData['product_id']);
            if (!$product) {
                return back()->withErrors(["products.{$idx}" => 'Producto no encontrado.'])->withInput();
            }

            if ($product->is_inventoriable) {
                $warehouseProduct = $product->warehouses()->where('warehouse_id', $request->warehouse_id)->first();
                $stockActual = $warehouseProduct ? $warehouseProduct->pivot->stock_available : 0;

                if ($product->type_management_id == 2) {
                    $totalLotes = collect($pData['selected_lots'] ?? [])->sum('quantity');
                    if ($totalLotes < $pData['quantity']) {
                        return back()->withErrors(["products.{$idx}" => "Lotes seleccionados ({$totalLotes}) no cubren la cantidad ({$pData['quantity']})."])->withInput();
                    }
                    if ($stockActual < $pData['quantity']) {
                        return back()->withErrors(["products.{$idx}" => "Stock insuficiente. Disponible: {$stockActual}."])->withInput();
                    }
                } else {
                    if ($stockActual < $pData['quantity']) {
                        return back()->withErrors(["products.{$idx}" => "Stock insuficiente. Disponible: {$stockActual}."])->withInput();
                    }
                }
            }

            $subtotalLinea = $pData['price'] * $pData['quantity'];
            $subtotalGeneral += $subtotalLinea;

            $productsData[] = [
                'product' => $product,
                'quantity' => $pData['quantity'],
                'price' => $pData['price'],
                'subtotal' => $subtotalLinea,
                'selected_lots' => $pData['selected_lots'] ?? [],
            ];
        }

        $totalPayments = collect($request->payments)->sum('amount');
        $discount = $request->discount ?? 0;
        $totalVenta = $subtotalGeneral - $discount;

        if (abs($totalPayments - $totalVenta) > 0.01) {
            return back()->withErrors(['payments' => "El total de pagos (S/. {$totalPayments}) no coincide con el total de la venta (S/. {$totalVenta})."])->withInput();
        }

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'company_id' => $user->company_id,
                'client_id' => $request->client_id,
                'warehouse_id' => $request->warehouse_id,
                'terminal_id' => $user->terminal->id,
                'user_id' => $user->id,
                'serie' => $series->serie,
                'number' => $series->number,
                'status' => 'active',
                'date' => now(),
                'subtotal' => $subtotalGeneral,
                'discount' => $discount,
                'total' => $totalVenta,
            ]);

            $movement = Movement::create([
                'warehouse_id' => $request->warehouse_id,
                'terminal_id' => $user->terminal->id,
                'serie' => $series->serie,
                'number' => $series->number,
                'employee_id' => $user->id,
                'movement_type_id' => 3,
            ]);

            foreach ($productsData as $pData) {
                $product = $pData['product'];
                $warehouseProduct = null;

                if ($product->is_inventoriable) {
                    $warehouseProduct = $product->warehouses()->where('warehouse_id', $request->warehouse_id)->first();
                    $newStock = $warehouseProduct->pivot->stock_available - $pData['quantity'];
                    $warehouseProduct->pivot->stock_available = $newStock;
                    $warehouseProduct->pivot->save();

                    if ($product->type_management_id == 2) {
                        foreach ($pData['selected_lots'] as $lot) {
                            $allotmentDetail = AllotmentDetail::where('allotment_id', $lot['allotmentId'])
                                ->where('product_id', $product->id)
                                ->first();

                            if (!$allotmentDetail) {
                                DB::rollBack();
                                return back()->withErrors(['error' => 'Lote no encontrado para el producto ' . $product->name])->withInput();
                            }

                            $available = $allotmentDetail->quantity - $allotmentDetail->quantity_dispatch;
                            if ($available < $lot['quantity']) {
                                DB::rollBack();
                                return back()->withErrors(['error' => "Stock insuficiente en lote para {$product->name}."])->withInput();
                            }

                            $allotmentDetail->quantity_dispatch += $lot['quantity'];
                            $allotmentDetail->save();
                        }
                    }
                }

                $movementDetail = MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'quantity_in' => 0,
                    'quantity_out' => $pData['quantity'],
                    'quantity_now' => $product->is_inventoriable ? ($warehouseProduct->pivot->stock_available ?? 0) : 0,
                    'price_cost' => $product->price_cost,
                    'price_cost_now' => $product->price_cost,
                ]);

                $saleDetail = SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'movement_detail_id' => $movementDetail->id,
                    'quantity' => $pData['quantity'],
                    'price' => $pData['price'],
                    'subtotal' => $pData['subtotal'],
                ]);

                if ($product->is_inventoriable && $product->type_management_id == 2) {
                    foreach ($pData['selected_lots'] as $lot) {
                        SaleDetailAllotment::create([
                            'sale_detail_id' => $saleDetail->id,
                            'allotment_id' => $lot['allotmentId'],
                            'quantity' => $lot['quantity'],
                        ]);
                    }
                }
            }

            foreach ($request->payments as $payData) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $payData['payment_method_id'],
                    'amount' => $payData['amount'],
                ]);
            }

            $series->increment('number');

            DB::commit();

            return redirect()->route('ventas.show', $sale->id)->with('success', 'Venta registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar la venta: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $sale = Sale::with([
            'client',
            'user',
            'warehouse',
            'terminal',
            'details.product',
            'details.allotments.allotment',
            'payments.paymentMethod'
        ])->findOrFail($id);

        return view('ventas.show', compact('sale'));
    }

    public function anular($id)
    {
        $sale = Sale::with(['details.product', 'details.allotments.allotment', 'payments'])->findOrFail($id);

        if ($sale->status !== 'active') {
            return back()->withErrors(['error' => 'Esta venta ya fue anulada.']);
        }

        $user = auth()->user();

        DB::beginTransaction();

        try {
            $anulacionSeries = Series::where('terminal_id', $sale->terminal_id)
                ->where('movement_type_id', 4)
                ->first();

            if (!$anulacionSeries) {
                $anulacionSeries = Series::where('terminal_id', $sale->terminal_id)
                    ->where('movement_type_id', 1)
                    ->first();
            }

            if (!$anulacionSeries) {
                DB::rollBack();
                return back()->withErrors(['error' => 'No hay serie configurada para anulación.'])->withInput();
            }

            $movementAnulacion = Movement::create([
                'warehouse_id' => $sale->warehouse_id,
                'terminal_id' => $sale->terminal_id,
                'serie' => $anulacionSeries->serie,
                'number' => $anulacionSeries->number,
                'employee_id' => $user->id,
                'movement_type_id' => 4,
            ]);

            foreach ($sale->details as $detail) {
                $product = $detail->product;

                if ($product->is_inventoriable) {
                    $warehouseProduct = $product->warehouses()->where('warehouse_id', $sale->warehouse_id)->first();
                    if ($warehouseProduct) {
                        $newStock = $warehouseProduct->pivot->stock_available + $detail->quantity;
                        $warehouseProduct->pivot->stock_available = $newStock;
                        $warehouseProduct->pivot->save();
                    }

                    foreach ($detail->allotments as $sda) {
                        $allotmentDetail = AllotmentDetail::where('allotment_id', $sda->allotment_id)
                            ->where('product_id', $product->id)
                            ->first();
                        if ($allotmentDetail) {
                            $allotmentDetail->quantity_dispatch = max(0, $allotmentDetail->quantity_dispatch - $sda->quantity);
                            $allotmentDetail->save();
                        }
                    }

                    MovementDetail::create([
                        'movement_id' => $movementAnulacion->id,
                        'product_id' => $product->id,
                        'quantity_in' => $detail->quantity,
                        'quantity_out' => 0,
                        'quantity_now' => $warehouseProduct->pivot->stock_available ?? 0,
                        'price_cost' => $product->price_cost,
                        'price_cost_now' => $product->price_cost,
                    ]);
                }
            }

            $sale->update(['status' => 'voided']);

            $anulacionSeries->increment('number');

            DB::commit();

            return back()->with('success', 'Venta anulada. Stock devuelto.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al anular: ' . $e->getMessage()]);
        }
    }

    public function duplicate($id)
    {
        $original = Sale::with(['details.product', 'details.allotments', 'payments.paymentMethod', 'client'])->findOrFail($id);

        return redirect()->route('ventas.create')
            ->with('duplicate', [
                'client_id' => $original->client_id,
                'discount' => $original->discount,
                'products' => $original->details->map(function ($d) {
                    return [
                        'product_id' => $d->product_id,
                        'quantity' => $d->quantity,
                        'price' => $d->price,
                        'selected_lots' => $d->allotments->map(function ($a) {
                            return [
                                'allotmentId' => $a->allotment_id,
                                'quantity' => $a->quantity,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
                'payments' => $original->payments->map(function ($p) {
                    return [
                        'payment_method_id' => $p->payment_method_id,
                        'amount' => $p->amount,
                    ];
                })->toArray(),
            ]);
    }

    public function pdf()
    {
        $user = auth()->user();
        $sales = Sale::with(['client', 'payments.paymentMethod', 'details.product'])
            ->where('company_id', $user->company_id)
            ->latest()
            ->get();

        $pdf = Pdf::loadView('pdf.VentasPDF', compact('sales'));
        return $pdf->download('Reporte_Ventas_' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new SalesExport, 'Reporte_Ventas_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
