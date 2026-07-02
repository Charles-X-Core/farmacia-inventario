<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        if ($user->is_admin) {
            $products = Product::where('company_id', $user->company_id)
                               ->with(['typeManagement', 'branches', 'warehouses'])
                               ->get();
        } else {
            $products = Product::whereHas('branches', function ($query) use ($user) {
                               $query->whereIn('branches.id', $user->branches->pluck('id'));
                           })
                           ->with(['typeManagement', 'branches', 'warehouses'])
                           ->get();
        }

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:products,code,NULL,id,company_id,' . $user->company_id
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,barcode,NULL,id,company_id,' . $user->company_id
            ],
            'price' => 'nullable|numeric|min:0',
            'price_cost' => 'nullable|numeric|min:0',
            'is_inventoriable' => 'required|boolean',
            'is_price_cost' => 'required|boolean',
            'is_sale' => 'required|boolean',
            'type_management_id' => 'required|exists:type_management,id',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('branch_ids')) {
            foreach ($request->branch_ids as $branchId) {
                $branch = Branch::where('id', $branchId)
                                ->where('company_id', $user->company_id)
                                ->first();
                if (!$branch) {
                    return response()->json(['message' => 'Prohibido: La sucursal no pertenece a su compañía.'], 403);
                }
            }
        }

        $price = $request->is_sale ? ($request->price ?? 0) : 0;
        $priceCost = $request->is_price_cost ? ($request->price_cost ?? null) : null;

        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => $request->name,
            'code' => $request->code,
            'barcode' => $request->barcode,
            'price' => $price,
            'price_cost' => $priceCost,
            'is_inventoriable' => $request->is_inventoriable,
            'is_price_cost' => $request->is_price_cost,
            'is_sale' => $request->is_sale,
            'type_management_id' => $request->type_management_id,
        ]);

        if ($request->has('branch_ids')) {
            $product->branches()->attach($request->branch_ids, ['status' => true]);
        }

        // Auto-assign all warehouses of the company with stock 0
        $companyWarehouses = Warehouse::whereHas('branches', function ($query) use ($user) {
                                    $query->where('company_id', $user->company_id);
                                })->get();

        foreach ($companyWarehouses as $warehouse) {
            $product->warehouses()->attach($warehouse->id, ['stock_available' => 0]);
        }

        return response()->json($product->load(['typeManagement', 'branches', 'warehouses']), 201);
    }

    public function show($id)
    {
        $user = auth()->user();
        if ($user->is_admin) {
            $product = Product::where('company_id', $user->company_id)
                               ->with(['typeManagement', 'branches', 'warehouses'])
                               ->find($id);
        } else {
            $product = Product::whereHas('branches', function ($query) use ($user) {
                               $query->whereIn('branches.id', $user->branches->pluck('id'));
                           })
                           ->with(['typeManagement', 'branches', 'warehouses'])
                           ->find($id);
        }

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $product = Product::where('company_id', $user->company_id)->find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,code,' . $id . ',id,company_id,' . $user->company_id
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,barcode,' . $id . ',id,company_id,' . $user->company_id
            ],
            'price' => 'nullable|numeric|min:0',
            'price_cost' => 'nullable|numeric|min:0',
            'is_inventoriable' => 'boolean',
            'is_price_cost' => 'boolean',
            'is_sale' => 'boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('branch_ids')) {
            foreach ($request->branch_ids as $branchId) {
                $branch = Branch::where('id', $branchId)
                                ->where('company_id', $user->company_id)
                                ->first();
                if (!$branch) {
                    return response()->json(['message' => 'Prohibido: La sucursal no pertenece a su compañía.'], 403);
                }
            }
        }

        $updateData = $request->only([
            'name',
            'code',
            'barcode',
            'price',
            'price_cost',
            'is_inventoriable',
            'is_price_cost',
            'is_sale',
        ]);

        $product->update($updateData);

        if ($request->has('branch_ids')) {
            $branchSyncData = [];
            foreach ($request->branch_ids as $branchId) {
                $branchSyncData[$branchId] = ['status' => true];
            }
            $product->branches()->sync($branchSyncData);
        }

        return response()->json($product->load(['typeManagement', 'branches', 'warehouses']));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $product = Product::where('company_id', $user->company_id)->find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->update(['status' => false]);
        $product->delete();

        return response()->json(['message' => 'Producto eliminado exitosamente']);
    }

    public function webIndex()
    {
        $user = auth()->user();
        $products = Product::where('company_id', $user->company_id)->get();
        return view('products.index', compact('products'));
    }

    public function webCreate()
    {
        $user = auth()->user();
        $typeManagement = \App\Models\TypeManagement::all();
        $branches = \App\Models\Branch::where('company_id', $user->company_id)->get();
        return view('products.create', compact('typeManagement', 'branches'));
    }

    public function webStore(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:products,code,NULL,id,company_id,' . $user->company_id
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,barcode,NULL,id,company_id,' . $user->company_id
            ],
            'price' => 'nullable|numeric|min:0',
            'price_cost' => 'nullable|numeric|min:0',
            'is_inventoriable' => 'required|in:0,1',
            'is_price_cost' => 'required|in:0,1',
            'is_sale' => 'required|in:0,1',
            'type_management_id' => 'required|exists:type_management,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $price = $request->is_sale ? ($request->price ?? 0) : 0;
        $priceCost = $request->is_price_cost ? ($request->price_cost ?? null) : null;

        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => $request->name,
            'code' => $request->code,
            'barcode' => $request->barcode,
            'price' => $price,
            'price_cost' => $priceCost,
            'is_inventoriable' => $request->is_inventoriable,
            'is_price_cost' => $request->is_price_cost,
            'is_sale' => $request->is_sale,
            'type_management_id' => $request->type_management_id,
        ]);

        // Auto-assign all warehouses of the company with stock 0
        $companyWarehouses = Warehouse::whereHas('branches', function ($query) use ($user) {
                                    $query->where('company_id', $user->company_id);
                                })->get();

        foreach ($companyWarehouses as $warehouse) {
            $product->warehouses()->attach($warehouse->id, ['stock_available' => 0]);
        }

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente!');
    }

    //PRODUCTO PDF
    public function pdf()
    {
        $products = Product::where(
            'company_id',
            auth()->user()->company_id
        )->get();

        $pdf = Pdf::loadView('pdf.ProductoPDF', [
            'products' => $products
        ]);

        return $pdf->download('ProductoPDF.pdf');
    }

    //PRODUCTO EXCEL//
    public function exportExcel()
    {
        return Excel::download(new ProductsExport, 'Catalogo_Productos.xlsx');
    }

}
