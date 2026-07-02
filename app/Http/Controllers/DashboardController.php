<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // 1. Total de productos (Seguro y directo)
        $totalProductos = Product::where('company_id', $user->company_id)->count();

        $stockBajo = 0;
        $porVencer = 0;
        $ultimosMovimientos = [];

        // Retornamos las variables limpias a tu vista de frontend
        return view('dashboard', compact('totalProductos', 'stockBajo', 'porVencer', 'ultimosMovimientos'));
    }
}
