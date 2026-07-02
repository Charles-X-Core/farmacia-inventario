<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Terminal;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'terminal_id' => 'required|exists:terminals,id',
            'warehouses_id' => 'required|array',
            'warehouses_id.*' => 'exists:warehouses,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Security check: terminal belongs to company?
        $terminal = Terminal::whereHas('branch', function($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->find($request->terminal_id);

        if (!$terminal) {
            return response()->json(['message' => 'Prohibido: La terminal no pertenece a su compañía.'], 403);
        }

        $branch = $terminal->branch;

        // Check if all requested warehouses belong to the terminal's branch
        foreach ($request->warehouses_id as $warehouseId) {
            $existsInBranch = DB::table('warehouse_branches')
                ->where('branch_id', $branch->id)
                ->where('warehouse_id', $warehouseId)
                ->exists();

            if (!$existsInBranch) {
                return response()->json([
                    'message' => "El almacén con ID $warehouseId no pertenece a la sucursal de la terminal seleccionada."
                ], 400);
            }
        }

        try {
            return DB::transaction(function () use ($request, $terminal, $branch) {
                $user = User::create([
                    'name' => $request->name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'company_id' => auth()->user()->company_id,
                    'terminal_id' => $terminal->id,
                    'is_admin' => false,
                ]);

                $user->branches()->attach($branch->id);
                $user->warehouses()->attach($request->warehouses_id);

                return response()->json([
                    'message' => 'Usuario creado exitosamente',
                    'user' => $user
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear usuario: ' . $e->getMessage()], 500);
        }
    }

    public function showRegister()
    {
        return view('auth.Registro');
    }

    public function update(Request $request, $id)
    {
        $user = User::where('company_id', auth()->user()->company_id)->find($id);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado o no pertenece a su compañía'], 404);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'password' => 'string|min:6',
            'terminal_id' => 'exists:terminals,id',
            'warehouses_id' => 'array',
            'warehouses_id.*' => 'exists:warehouses,id',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            return DB::transaction(function () use ($request, $user) {
                $data = $request->only(['name', 'last_name', 'email', 'status']);
                if ($request->has('password')) {
                    $data['password'] = Hash::make($request->password);
                }

                if ($request->has('terminal_id')) {
                    $terminal = Terminal::whereHas('branch', function($q) {
                        $q->where('company_id', auth()->user()->company_id);
                    })->find($request->terminal_id);

                    if (!$terminal) {
                        return response()->json(['message' => 'Prohibido: La terminal no pertenece a su compañía.'], 403);
                    }
                    $data['terminal_id'] = $terminal->id;

                    // Update branch as well
                    $user->branches()->sync([$terminal->branch_id]);
                }

                $user->update($data);

                if ($request->has('warehouses_id')) {
                    // Security check: ensure warehouses belong to the company
                    foreach ($request->warehouses_id as $whId) {
                        $wh = Warehouse::whereHas('branches', function($q) {
                            $q->where('company_id', auth()->user()->company_id);
                        })->find($whId);
                        if (!$wh) return response()->json(['message' => "Prohibido: Almacén $whId no válido."], 403);
                    }
                    $user->warehouses()->sync($request->warehouses_id);
                }

                return response()->json([
                    'message' => 'Usuario actualizado exitosamente',
                    'user' => $user->load(['terminal', 'warehouses', 'branches'])
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar usuario: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::where('company_id', auth()->user()->company_id)->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado o no pertenece a su compañía'], 404);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No puedes eliminarte a ti mismo'], 400);
        }

        $user->update([
            'status' => false,
            'email' => null,
        ]);

        $user->delete(); // Soft delete

        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }

    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)->get();
        return response()->json($users);
    }
}
