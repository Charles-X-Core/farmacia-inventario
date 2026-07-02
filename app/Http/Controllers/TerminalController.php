<?php

namespace App\Http\Controllers;

use App\Models\Terminal;
use App\Models\Series;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TerminalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->is_admin) {
            return response()->json(Terminal::whereHas('branch', function($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->with('series')->get());
        }
        return response()->json($user->terminal->load('series'));
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

        try {
            return DB::transaction(function () use ($request) {
                $terminal = Terminal::create([
                    'name' => $request->name,
                    'branch_id' => $request->branch_id,
                ]);

                // Create Default Series
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

                return response()->json($terminal->load('series'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear terminal: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $terminal = Terminal::whereHas('branch', function($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->find($id);

        if (!$terminal) return response()->json(['message' => 'Terminal no encontrada o no pertenece a su compañía'], 404);

        if ($request->has('branch_id')) {
            return response()->json(['message' => 'Prohibido: No se puede editar el branch_id de una terminal una vez creada.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $terminal->update($request->all());

        return response()->json($terminal);
    }

    public function updateSeries(Request $request, $terminalId, $seriesId)
    {
        // Ensure the terminal belongs to the company
        $terminal = Terminal::whereHas('branch', function($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->find($terminalId);

        if (!$terminal) return response()->json(['message' => 'Terminal no encontrada o no pertenece a su compañía'], 404);

        $series = Series::where('terminal_id', $terminalId)->where('id', $seriesId)->first();
        if (!$series) return response()->json(['message' => 'Serie no encontrada para esta terminal'], 404);

        $validator = Validator::make($request->all(), [
            'serie' => 'string|max:255',
            'number' => 'integer|min:1',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $series->update($request->all());

        return response()->json($series);
    }

    public function destroy($id)
    {
        $terminal = Terminal::whereHas('branch', function($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->find($id);

        if (!$terminal) return response()->json(['message' => 'Terminal no encontrada o no pertenece a su compañía'], 404);

        $terminal->update(['status' => false]);
        $terminal->delete();

        return response()->json(['message' => 'Terminal eliminada exitosamente']);
    }
}
