<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovementController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SaleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AllotmentController;

// Rutas de Autenticación
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'webLogin']);
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('webLogout');


//REGISTRO
Route::post('/registro',[AuthController::class, 'webRegister']);
Route::get('/registro', [AuthController::class, 'showRegister'])->name('registro');
//fin registro


// Módulos Protegidos por Sesión Web
Route::middleware(['auth:web'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // MÓDULO DE PRODUCTOS
    // ==========================================
    Route::get('/products', [ProductController::class, 'webIndex'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'webCreate'])->name('products.create');
    Route::post('/products', [ProductController::class, 'webStore'])->name('products.store');

    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/products/pdf', [ProductController::class, 'pdf'])->name('products.pdf');
    Route::get('/products/export-excel', [ProductController::class, 'exportExcel'])->name('products.excel');

    // ==========================================
    // MÓDULO DE LOTES
    // ==========================================
    Route::get('/allotments', function() {
        try {
            $user = auth()->user();
            $allotments = \App\Models\Allotment::whereHas('warehouse.branches', function($q) use ($user) {
                                    $q->where('company_id', $user->company_id);
                                })
                                ->with(['movement', 'allotmentDetails', 'allotmentDetails.product', 'allotmentDetails.product.warehouses'])
                                ->get()
                                ->map(function($allotment) {
                                    $detail = $allotment->allotmentDetails->first();
                                    $totalStock = 0;
                                    if ($detail && $detail->product) {
                                        $warehouseProduct = $detail->product->warehouses->where('id', $allotment->warehouse_id)->first();
                                        if ($warehouseProduct) {
                                            $totalStock = $warehouseProduct->pivot->stock_available;
                                        }
                                    }
                                    return (object)[
                                        'id' => $allotment->id,
                                        'code' => $allotment->code,
                                        'expiration_date' => $allotment->expiration_date,
                                        'warehouse_id' => $allotment->warehouse_id,
                                        'fecha_ingreso' => $allotment->movement ? $allotment->movement->created_at : null,
                                        'serie' => $allotment->movement ? $allotment->movement->serie : null,
                                        'correlativo' => $allotment->movement ? $allotment->movement->number : null,
                                        'product_name' => $detail ? $detail->product->name : null,
                                        'barcode' => $detail ? $detail->product->barcode : null,
                                        'stock_actual' => $detail ? $detail->quantity : 0,
                                        'despachado' => $detail ? $detail->quantity_dispatch : 0,
                                        'available' => $detail ? ($detail->quantity - $detail->quantity_dispatch) : 0,
                                        'total_stock' => $totalStock
                                    ];
                                });
        } catch (\Exception $e) {
            $allotments = [];
        }
        return view('allotments.index', compact('allotments'));
    })->name('allotments.index');

    Route::get('/allotments/pdf', [AllotmentController::class, 'pdf'])
    ->name('allotments.pdf');
    Route::get('/allotments/export-excel', [AllotmentController::class, 'exportExcel'])->name('allotments.excel');


    // ==========================================
    // MÓDULO DE MOVIMIENTOS
    // ==========================================
    Route::get('/movements', function() {
        try {
            $user = auth()->user();
            $movementIds = \Illuminate\Support\Facades\DB::table('movement_details')
                ->join('movements', 'movement_details.movement_id', '=', 'movements.id')
                ->join('products', 'movement_details.product_id', '=', 'products.id')
                ->where('products.company_id', $user->company_id)
                ->pluck('movements.id')
                ->unique();

            $movements = [];
            foreach ($movementIds as $movementId) {
                $movementData = DB::table('movements')
                    ->where('movements.id', $movementId)
                    ->select(
                        'movements.id',
                        'movements.created_at',
                        'movements.movement_type_id',
                        'movements.serie',
                        'movements.number'
                    )
                    ->first();

                $details = \Illuminate\Support\Facades\DB::table('movement_details')
                    ->join('products', 'movement_details.product_id', '=', 'products.id')
                    ->where('movement_details.movement_id', $movementId)
                    ->select(
                        'products.name as product_name',
                        \Illuminate\Support\Facades\DB::raw('(movement_details.quantity_in + movement_details.quantity_out) as quantity')
                    )
                    ->get();

                $allotmentCodes = \Illuminate\Support\Facades\DB::table('allotments')
                    ->where('allotments.movement_id', $movementId)
                    ->pluck('code')
                    ->toArray();

                // Also add allotments used in outgoing movements (from allotment_details)
                $outgoingAllotments = \Illuminate\Support\Facades\DB::table('allotment_details')
                    ->join('allotments', 'allotment_details.allotment_id', '=', 'allotments.id')
                    ->whereIn('allotments.id', function($query) use ($movementId) {
                        $query->select('allotment_id')
                            ->from('allotment_details')
                            ->where('allotment_details.quantity_dispatch', '>', 0);
                    })
                    ->pluck('allotments.code')
                    ->toArray();

                $allotmentCodes = array_unique(array_merge($allotmentCodes, $outgoingAllotments));

                foreach ($details as $detail) {
                    $movements[] = (object)[
                        'id' => $movementData->id,
                        'created_at' => $movementData->created_at,
                        'movement_type_id' => $movementData->movement_type_id,
                        'serie' => $movementData->serie,
                        'number' => $movementData->number,
                        'product_name' => $detail->product_name,
                        'quantity' => $detail->quantity,
                        'allotment_codes' => $allotmentCodes
                    ];
                }
            }
        } catch (\Exception $e) {
            $movements = [];
        }
        return view('movements.index', compact('movements'));
    })->name('movements.index');

    Route::get('/movements/create', function() {
        $user = auth()->user();

        // Get all allotments with their details
        $allotments = \App\Models\Allotment::whereHas('warehouse.branches', function($q) use ($user) {
                                $q->where('company_id', $user->company_id);
                            })
                            ->with(['allotmentDetails'])
                            ->get()
                            ->map(function($allotment) {
                                $detail = $allotment->allotmentDetails->first();
                                return [
                                    'id' => $allotment->id,
                                    'code' => $allotment->code,
                                    'expiration_date' => $allotment->expiration_date,
                                    'warehouse_id' => $allotment->warehouse_id,
                                    'quantity' => $detail ? $detail->quantity : 0,
                                    'quantity_dispatch' => $detail ? $detail->quantity_dispatch : 0,
                                    'available' => $detail ? ($detail->quantity - $detail->quantity_dispatch) : 0
                                ];
                            });

        // Get lot product mappings
        $loteProductos = \Illuminate\Support\Facades\DB::table('allotment_details')
            ->join('products', 'allotment_details.product_id', '=', 'products.id')
            ->where('products.company_id', $user->company_id)
            ->select('allotment_details.allotment_id', 'products.id as product_id', 'products.name as product_name')
            ->get();

        $branches = \App\Models\Branch::where('company_id', $user->company_id)->get();
        $warehouseId = \App\Models\Warehouse::whereHas('branches', function($q) use ($user) {
                            $q->where('company_id', $user->company_id);
                        })->value('id') ?? 1;

        $products = \App\Models\Product::where('company_id', $user->company_id)->where('is_inventoriable', true)->get();
        $movementTypes = \App\Models\MovementType::all();

        return view('movements.create', compact('allotments', 'loteProductos', 'branches', 'warehouseId', 'products', 'movementTypes'));
    })->name('movements.create');

    Route::delete('/movements/{id}', [MovementController::class, 'destroy'])
    ->name('movements.destroy');

    Route::post('/movements', function(\Illuminate\Http\Request $request) {
        return redirect()->route('movements.index')->with('error', 'Por favor usa el formulario de múltiples productos!');
    })->name('movements.store');

    Route::get('/movements/pdf', [MovementController::class, 'pdf'])
    ->name('movements.pdf');
    Route::get('/movements/export-excel', [MovementController::class, 'exportExcel'])->name('movements.excel');


    /*Ventas */
    Route::get('/ventas', [SaleController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/create', [SaleController::class, 'create'])->name('ventas.create');
    Route::post('/ventas', [SaleController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{sale}', [SaleController::class, 'show'])->name('ventas.show');
    Route::post('/ventas/{sale}/anular', [SaleController::class, 'anular'])->name('ventas.anular');
    Route::get('/ventas/{sale}/duplicate', [SaleController::class, 'duplicate'])->name('ventas.duplicate');
    Route::get('/ventas/pdf', [SaleController::class, 'pdf'])->name('ventas.pdf');
    Route::get('/ventas/export-excel', [SaleController::class, 'exportExcel'])->name('ventas.excel');

    /*Clientes */
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    /*Kardex */
    Route::get('/kardex', function() {
        $user = auth()->user();
        $products = \App\Models\Product::where('company_id', $user->company_id)
            ->get()
            ->map(function($product) use ($user) {
                $totalIn = \Illuminate\Support\Facades\DB::table('movement_details')
                    ->join('movements', 'movement_details.movement_id', '=', 'movements.id')
                    ->where('movement_details.product_id', $product->id)
                    ->whereIn('movements.movement_type_id', [1, 4])
                    ->sum('movement_details.quantity_in');

                $totalOut = \Illuminate\Support\Facades\DB::table('movement_details')
                    ->join('movements', 'movement_details.movement_id', '=', 'movements.id')
                    ->where('movement_details.product_id', $product->id)
                    ->whereIn('movements.movement_type_id', [2, 3])
                    ->sum('movement_details.quantity_out');

                $movements = \Illuminate\Support\Facades\DB::table('movement_details')
                    ->join('movements', 'movement_details.movement_id', '=', 'movements.id')
                    ->join('movement_types', 'movements.movement_type_id', '=', 'movement_types.id')
                    ->where('movement_details.product_id', $product->id)
                    ->orderBy('movements.created_at')
                    ->select(
                        'movements.created_at',
                        'movements.serie',
                        'movements.number',
                        'movement_types.name as type_name',
                        'movements.movement_type_id',
                        'movement_details.quantity_in',
                        'movement_details.quantity_out'
                    )
                    ->get();

                $accumulated = 0;
                foreach ($movements as $mov) {
                    $mov->is_entry = in_array($mov->movement_type_id, [1, 4]);
                    $mov->accumulated = $accumulated + $mov->quantity_in - $mov->quantity_out;
                    $accumulated = $mov->accumulated;
                }

                $warehouseProduct = \Illuminate\Support\Facades\DB::table('warehouse_products')
                    ->where('product_id', $product->id)
                    ->sum('stock_available');

                return (object)[
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'current_stock' => $warehouseProduct,
                    'total_in' => $totalIn,
                    'total_out' => $totalOut,
                    'movements' => $movements,
                ];
            });

        return view('kardex.index', compact('products'));
    })->name('kardex.index');

});
