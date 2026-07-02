<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromArray;

class SalesExport implements FromArray
{
    public function array(): array
    {
        $user = auth()->user();

        // 1. Obtenemos las ventas asociadas al usuario o sucursal
        $sales = Sale::with(['client', 'user'])
            ->where('user_id', $user->id) // Filtramos por el usuario que inició sesión
            ->latest()
            ->get()
            ->map(function($sale) {
                return [
                    $sale->created_at->format('d/m/Y H:i'),
                    $sale->client->name ?? 'Consumidor final',
                    $sale->total,
                    $sale->status ?? 'Completado',
                ];
            })->toArray();

        // 2. Preparamos el bloque de encabezado institucional
        $header = [
            ['Reporte de Ventas'],
            ['Generado por:', $user->name],
            ['Correo:', $user->email],
            ['Fecha:', now()->format('d/m/Y H:i')],
            [''],
            ['Fecha', 'Cliente', 'Total', 'Estado'],
        ];

        return array_merge($header, $sales);
    }
}
