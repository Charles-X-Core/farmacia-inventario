<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        $user = auth()->user();

        // Filtramos los productos que pertenecen a la empresa del usuario
        $products = Product::where('company_id', $user->company_id) // Ajusta 'company_id' si el nombre es diferente
            ->select('code', 'name', 'price')
            ->get()
            ->toArray();

        $header = [
            ['Generado por:', $user->name],
            ['Correo:', $user->email],
            ['Fecha:', now()->format('d/m/Y H:i')],
            [''],
            ['Código', 'Nombre', 'Precio'],
        ];

        return array_merge($header, $products);
    }

    public function headings(): array
    {

        return ['Reporte Excel de Productos'];
    }

}
