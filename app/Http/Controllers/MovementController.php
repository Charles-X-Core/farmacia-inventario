<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Allotment;
use App\Models\AllotmentDetail;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\MovementsExport;
use Maatwebsite\Excel\Facades\Excel;


class MovementController extends Controller
{
    public function incoming(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_type_id' => 'required|exists:movement_types,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_in' => 'required|integer|min:1',
            'products.*.price_cost' => 'nullable|numeric|min:0',
            'products.*.selected_lots' => 'nullable|array',
            'products.*.selected_lots.*.isNew' => 'required_with:products.*.selected_lots|boolean',
            'products.*.selected_lots.*.allotmentId' => 'nullable|exists:allotments,id',
            'products.*.selected_lots.*.code' => 'nullable|string|max:255',
            'products.*.selected_lots.*.expiration_date' => 'nullable|date',
            'products.*.selected_lots.*.quantity' => 'required_with:products.*.selected_lots|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();

        $warehouse = Warehouse::whereHas('branches', function ($query) use ($user) {
                           $query->where('company_id', $user->company_id);
                       })->find($request->warehouse_id);

        if (!$warehouse) {
            return response()->json(['message' => 'Almacén no encontrado o no pertenece a su compañía'], 404);
        }

        if (!$user->terminal) {
            return response()->json(['message' => 'Usuario no tiene terminal asignada'], 400);
        }

        $series = Series::where('terminal_id', $user->terminal->id)
                     ->where('movement_type_id', $request->movement_type_id)
                     ->first();

        if (!$series) {
            return response()->json(['message' => 'Serie no encontrada para esta terminal y tipo de movimiento'], 404);
        }

        DB::beginTransaction();

        try {
            $movement = Movement::create([
                'warehouse_id' => $request->warehouse_id,
                'terminal_id' => $user->terminal->id,
                'serie' => $series->serie,
                'number' => $series->number,
                'employee_id' => $user->id,
                'movement_type_id' => $request->movement_type_id,
            ]);

            foreach ($request->products as $productData) {
                $product = Product::where('company_id', $user->company_id)->find($productData['product_id']);

                if (!$product) {
                    DB::rollBack();
                    return response()->json(['message' => 'Producto no encontrado'], 404);
                }

                if (!$product->is_inventoriable) {
                    DB::rollBack();
                    return response()->json(['message' => "El producto {$product->name} no es inventariable"], 400);
                }

                $warehouseProduct = $product->warehouses()->where('warehouse_id', $request->warehouse_id)->first();

                if (!$warehouseProduct) {
                    $product->warehouses()->attach($request->warehouse_id, ['stock_available' => 0]);
                    $warehouseProduct = $product->warehouses()->where('warehouse_id', $request->warehouse_id)->first();
                }

                $newStock = $warehouseProduct->pivot->stock_available + $productData['quantity_in'];
                $warehouseProduct->pivot->stock_available = $newStock;
                $warehouseProduct->pivot->save();

                MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'quantity_in' => $productData['quantity_in'],
                    'quantity_out' => 0,
                    'quantity_now' => $newStock,
                    'price_cost' => $productData['price_cost'] ?? $product->price_cost,
                    'price_cost_now' => $productData['price_cost'] ?? $product->price_cost,
                    'utilites' => 0,
                ]);

                if ($product->type_management_id === 2) {
                    if (!isset($productData['selected_lots']) || empty($productData['selected_lots'])) {
                        DB::rollBack();
                        return response()->json(['message' => 'Para productos con gestión por lotes, debe seleccionar/crear al menos un lote'], 400);
                    }

                    foreach ($productData['selected_lots'] as $lot) {
                        if ($lot['isNew']) {
                            $allotment = Allotment::create([
                                'movement_id' => $movement->id,
                                'warehouse_id' => $request->warehouse_id,
                                'code' => $lot['code'],
                                'expiration_date' => $lot['expiration_date'] ?: null,
                                'flag_closed' => false,
                            ]);

                            AllotmentDetail::create([
                                'allotment_id' => $allotment->id,
                                'product_id' => $product->id,
                                'quantity' => $lot['quantity'],
                                'quantity_dispatch' => 0,
                            ]);
                        } else {
                            $allotmentDetail = AllotmentDetail::where('allotment_id', $lot['allotmentId'])
                                                          ->where('product_id', $product->id)
                                                          ->first();
                            if (!$allotmentDetail) {
                                DB::rollBack();
                                return response()->json(['message' => 'Detalle de lote no encontrado'], 404);
                            }
                            $allotmentDetail->quantity += $lot['quantity'];
                            $allotmentDetail->save();
                        }
                    }
                }
            }

            $series->increment('number');

            DB::commit();

            return response()->json($movement->load(['movementDetails', 'movementDetails.product', 'allotments', 'allotments.allotmentDetails']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al procesar el movimiento', 'error' => $e->getMessage()], 500);
        }
    }

    public function outgoing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_type_id' => 'required|exists:movement_types,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_out' => 'required|integer|min:1',
            'products.*.allotment_id' => 'nullable|exists:allotments,id',
            'products.*.selected_lots' => 'nullable|array',
            'products.*.selected_lots.*.allotmentId' => 'required_with:products.*.selected_lots|exists:allotments,id',
            'products.*.selected_lots.*.quantity' => 'required_with:products.*.selected_lots|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();

        $warehouse = Warehouse::whereHas('branches', function ($query) use ($user) {
                           $query->where('company_id', $user->company_id);
                       })->find($request->warehouse_id);

        if (!$warehouse) {
            return response()->json(['message' => 'Almacén no encontrado o no pertenece a su compañía'], 404);
        }

        if (!$user->terminal) {
            return response()->json(['message' => 'Usuario no tiene terminal asignada'], 400);
        }

        $series = Series::where('terminal_id', $user->terminal->id)
                     ->where('movement_type_id', $request->movement_type_id)
                     ->first();

        if (!$series) {
            return response()->json(['message' => 'Serie no encontrada para esta terminal y tipo de movimiento'], 404);
        }

        DB::beginTransaction();

        try {
            $movement = Movement::create([
                'warehouse_id' => $request->warehouse_id,
                'terminal_id' => $user->terminal->id,
                'serie' => $series->serie,
                'number' => $series->number,
                'employee_id' => $user->id,
                'movement_type_id' => $request->movement_type_id,
            ]);

            foreach ($request->products as $productData) {
                $product = Product::where('company_id', $user->company_id)->find($productData['product_id']);

                if (!$product) {
                    DB::rollBack();
                    return response()->json(['message' => 'Producto no encontrado'], 404);
                }

                if (!$product->is_inventoriable) {
                    DB::rollBack();
                    return response()->json(['message' => "El producto {$product->name} no es inventariable"], 400);
                }

                $warehouseProduct = $product->warehouses()->where('warehouse_id', $request->warehouse_id)->first();

                if (!$warehouseProduct || $warehouseProduct->pivot->stock_available < $productData['quantity_out']) {
                    DB::rollBack();
                    return response()->json(['message' => "Stock insuficiente para el producto {$product->name}"], 400);
                }

                if ($product->type_management_id === 2) {
                    if (!isset($productData['selected_lots']) || empty($productData['selected_lots'])) {
                        DB::rollBack();
                        return response()->json(['message' => 'Para productos con gestión por lotes, debe seleccionar al menos un lote'], 400);
                    }

                    foreach ($productData['selected_lots'] as $lot) {
                        $allotmentDetail = AllotmentDetail::where('allotment_id', $lot['allotmentId'])
                                                          ->where('product_id', $product->id)
                                                          ->first();

                        if (!$allotmentDetail) {
                            DB::rollBack();
                            return response()->json(['message' => 'Detalle de lote no encontrado'], 404);
                        }

                        $availableAllotment = $allotmentDetail->quantity - $allotmentDetail->quantity_dispatch;
                        if ($availableAllotment < $lot['quantity']) {
                            DB::rollBack();
                            return response()->json(['message' => "Stock insuficiente en el lote para el producto {$product->name}"], 400);
                        }

                        $allotmentDetail->quantity_dispatch += $lot['quantity'];
                        $allotmentDetail->save();
                    }
                }

                $newStock = $warehouseProduct->pivot->stock_available - $productData['quantity_out'];
                $warehouseProduct->pivot->stock_available = $newStock;
                $warehouseProduct->pivot->save();

                MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'quantity_in' => 0,
                    'quantity_out' => $productData['quantity_out'],
                    'quantity_now' => $newStock,
                    'price_cost' => $product->price_cost,
                    'price_cost_now' => $product->price_cost,
                    'utilites' => 0,
                ]);
            }

            $series->increment('number');

            DB::commit();

            return response()->json($movement->load(['movementDetails', 'movementDetails.product']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al procesar el movimiento', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Movement::findOrFail($id)->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function index()
    {
        $movements = Movement::with(['movementDetails.product'])
            ->select('*') // importante si antes lo estabas limitando
            ->latest()
            ->get();

        return view('movements.index', compact('movements'));
    }

    public function pdf()
    {
        $user = auth()->user();

        $movements = Movement::with([
                'movementDetails.product',
                'allotments'
            ])
            ->whereHas('warehouse.branches', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })
            ->latest()
            ->get();

        $pdf = Pdf::loadView('pdf.MovimientoPDF', [
            'movements' => $movements
        ]);

        return $pdf->download('MovimientoPDF.pdf');
    }

    public function exportExcel()
    {
        // Nombre del archivo dinámico
        $filename = 'Movimientos_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new MovementsExport, $filename);
    }
}
