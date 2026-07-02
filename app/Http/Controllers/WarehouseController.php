<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->is_admin) {
            // Admin sees all warehouses of the company
            return response()->json(Warehouse::whereHas('branches', function($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->get());
        }

        // Normal user sees only assigned warehouses
        return response()->json($user->warehouses);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Security check: Does the branch belong to the user's company?
        $branch = Branch::where('id', $request->branch_id)
                        ->where('company_id', auth()->user()->company_id)
                        ->first();

        if (!$branch) {
            return response()->json(['message' => 'Prohibido: La sucursal no pertenece a su compañía.'], 403);
        }

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
        ]);

        // Automatically associate with the branch in pivot table
        $warehouse->branches()->attach($request->branch_id);

        return response()->json($warehouse, 201);
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::whereHas('branches', function($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->find($id);

        if (!$warehouse) return response()->json(['message' => 'Almacén no encontrado o no pertenece a su compañía'], 404);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'branch_id' => 'exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('branch_id')) {
            $branch = Branch::where('id', $request->branch_id)
                            ->where('company_id', auth()->user()->company_id)
                            ->first();
            if (!$branch) {
                return response()->json(['message' => 'Prohibido: La sucursal destino no pertenece a su compañía.'], 403);
            }
        }

        $warehouse->update($request->all());

        if ($request->has('branch_id')) {
            $warehouse->branches()->sync([$request->branch_id]);
        }

        return response()->json($warehouse);
    }

    public function destroy($id)
    {
        $warehouse = Warehouse::whereHas('branches', function($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->find($id);

        if (!$warehouse) return response()->json(['message' => 'Almacén no encontrado o no pertenece a su compañía'], 404);

        $warehouse->update(['status' => false]);
        $warehouse->delete(); // Soft delete

        return response()->json(['message' => 'Almacén eliminado exitosamente']);
    }
}
