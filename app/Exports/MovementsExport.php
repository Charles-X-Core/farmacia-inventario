<?php

namespace App\Exports;

use App\Models\Movement;
use Maatwebsite\Excel\Concerns\FromArray; // Cambia esto

class MovementsExport implements FromArray // Cambia esto
{
    /**
    * @return array
    */
    public function array(): array
    {
        $user = auth()->user();

        // Obtenemos los movimientos filtrados
        $movements = Movement::with(['movementType', 'warehouse'])
            ->whereHas('warehouse.branches', function($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })
            ->latest()
            ->get()
            ->map(function($m) {
                return [
                    $m->created_at ? $m->created_at->format('d/m/Y H:i') : 'N/A',
                    $m->movementType->name ?? 'N/A',
                    $m->warehouse->name ?? 'N/A',
                    $m->serie . '-' . $m->number, // Concatenar serie y numero
                    // sumar los detalles: $m->movementDetails->sum('quantity_in')
                    $m->movementDetails->sum('quantity_in') + $m->movementDetails->sum('quantity_out'),
                ];
            })->toArray();

        // Preparamos el encabezado
        $header = [
            ['Reporte de Movimientos de Almacén'],
            ['Generado por:', $user->name],
            ['Correo:', $user->email],
            ['Fecha:', now()->format('d/m/Y H:i')],
            [''],
            ['Fecha Registro', 'Tipo Movimiento', 'Almacén', 'Referencia', 'Cantidad'],
        ];

        return array_merge($header, $movements);
    }

}
