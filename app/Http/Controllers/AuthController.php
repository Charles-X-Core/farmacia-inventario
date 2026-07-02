<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Terminal;
use App\Models\Series;
use App\Models\MovementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_document_id' => 'required|exists:type_documents,id',
            'document' => 'required|string',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'company_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create Company
                $company = Company::create([
                    'name' => $request->company_name,
                    'type_document_id' => $request->type_document_id,
                    'document' => $request->document,
                ]);

                // 2. Create Branch
                $branch = Branch::create([
                    'company_id' => $company->id,
                    'name' => 'SUCURSAL 1',
                ]);

                // 3. Create Warehouse
                $warehouse = Warehouse::create([
                    'name' => 'ALMACEN 1',
                    'branch_id' => $branch->id,
                ]);

                // Associate Warehouse with Branch in pivot table
                $branch->warehouses()->attach($warehouse->id);

                // 4. Create Terminal
                $terminal = Terminal::create([
                    'name' => 'terminal ' . $company->name,
                    'branch_id' => $branch->id,
                ]);

                // 5. Create Default Series
                $seriesData = [
                    ['serie' => 'NV01', 'movement_type_id' => 3], // Venta
                    ['serie' => 'SM01', 'movement_type_id' => 2], // Salida
                    ['serie' => 'IM01', 'movement_type_id' => 1], // Ingreso
                ];

                foreach ($seriesData as $s) {
                    Series::create([
                        'terminal_id' => $terminal->id,
                        'movement_type_id' => $s['movement_type_id'],
                        'serie' => $s['serie'],
                        'number' => 1,
                    ]);
                }

                // 6. Create Admin User
                $user = User::create([
                    'name' => $request->name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'company_id' => $company->id,
                    'terminal_id' => $terminal->id,
                    'is_admin' => true,
                ]);

                // 7. Fill Pivot Tables for User
                $user->branches()->attach($branch->id);
                $user->warehouses()->attach($warehouse->id);

                $token = JWTAuth::fromUser($user);

                return response()->json([
                    'message' => 'Registro exitoso',
                    'user' => $user,
                    'token' => $token
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el registro: ' . $e->getMessage()], 500);
        }
    }

    public function showRegister()
    {
        return view('auth.Registro');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $user = auth()->user();

        if (!$user->status) {
            return response()->json(['error' => 'Usuario desactivado'], 403);
        }

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Sesión cerrada exitosamente y token invalidado']);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Error al intentar cerrar sesión'], 500);
        }
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function showLogin()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (\Illuminate\Support\Facades\Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }
}
