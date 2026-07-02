<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->is_admin) {
            return response()->json(Branch::where('company_id', $user->company_id)->get());
        }
        return response()->json($user->branches);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $branch = Branch::create([
            'name' => $request->name,
            'company_id' => auth()->user()->company_id,
        ]);

        return response()->json($branch, 201);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::where('company_id', auth()->user()->company_id)->find($id);
        if (!$branch) return response()->json(['message' => 'Sucursal no encontrada o no pertenece a su compañía'], 404);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'warehouses_id' => 'array',
            'warehouses_id.*' => 'exists:warehouses,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('name')) {
            $branch->update(['name' => $request->name]);
        }

        if ($request->has('warehouses_id')) {
            // Security check: ensure all warehouses belong to the company
            foreach ($request->warehouses_id as $whId) {
                $wh = Warehouse::whereHas('branches', function($q) {
                    $q->where('company_id', auth()->user()->company_id);
                })->find($whId);

                if (!$wh) {
                    return response()->json(['message' => "Prohibido: El almacén con ID $whId no pertenece a su compañía."], 403);
                }
            }
            $branch->warehouses()->sync($request->warehouses_id);
        }

        return response()->json($branch->load('warehouses'));
    }

    public function destroy($id)
    {
        $branch = Branch::where('company_id', auth()->user()->company_id)->find($id);
        if (!$branch) return response()->json(['message' => 'Sucursal no encontrada o no pertenece a su compañía'], 404);

        $branch->update(['status' => false]);
        $branch->delete();

        return response()->json(['message' => 'Sucursal eliminada exitosamente']);
    }
}
