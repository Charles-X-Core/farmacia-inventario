<?php

namespace App\Http\Controllers;

use App\Models\Allotment;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AllotmentsExport;
use Maatwebsite\Excel\Facades\Excel;

class AllotmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Allotment::with([
            'allotmentDetails.product',
            'movement',
            'warehouse'
        ]);

        if ($request->filled('product_id')) {
            $query->whereHas('allotmentDetails', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        if ($request->filled('movement_id')) {
            $query->where('movement_id', $request->movement_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Solo lotes de la empresa del usuario
        $query->whereHas('warehouse.branches', function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        });

        $allotments = $query->latest()->get();

        return view('allotments.index', compact('allotments'));
    }

    public function pdf()
    {
        $user = auth()->user();

        $allotments = Allotment::with([
                'allotmentDetails.product',
                'movement',
                'warehouse'
            ])
            ->whereHas('warehouse.branches', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })
            ->latest()
            ->get();

        $pdf = Pdf::loadView('PDF.LotePDF', [
            'allotments' => $allotments
        ]);

        return $pdf->download('Lotes.pdf');
    }

    public function exportExcel()
    {
        $filename = 'Lotes_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new AllotmentsExport, $filename);
    }
}
