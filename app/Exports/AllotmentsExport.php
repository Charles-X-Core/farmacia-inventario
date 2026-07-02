<?php

namespace App\Exports;

use App\Models\Allotment;
use Maatwebsite\Excel\Concerns\FromArray;

class AllotmentsExport implements FromArray
{
    public function array(): array
    {
        $user = auth()->user();

        $allotments = Allotment::with(['allotmentDetails.product', 'movement'])
            ->whereHas('warehouse.branches', function($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })
            ->latest()
            ->get()
            ->map(function($a) {
                $expiration = $a->expiration_date ? \Carbon\Carbon::parse($a->expiration_date) : null;
                $daysLeft = $expiration ? $expiration->diffInDays(now(), false) : 'N/A';

                return [
                    $a->created_at->format('d/m/Y'),
                    $a->movement->serie . '-' . $a->movement->number,
                    $a->code,
                    $a->allotmentDetails->first()->product->name ?? 'N/A',
                    $a->expiration_date ? $expiration->format('d/m/Y') : 'N/A',
                    $daysLeft, // Días restantes
                    $a->allotmentDetails->sum('quantity'), // Stock Total
                ];
            })->toArray();

        $header = [
            ['Reporte Detallado de Lotes'],
            ['Generado por:', $user->name],
            ['Fecha:', now()->format('d/m/Y H:i')],
            [''],
            ['F. Ingreso', 'Serie-Corr', 'Lote', 'Producto', 'Vencimiento', 'Días Restantes', 'Stock Total'],
        ];

        return array_merge($header, $allotments);
    }
}
