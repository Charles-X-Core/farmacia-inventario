<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;

class VentasController extends Controller
{

    public function index()
    {

        $ventas = Sale::with([
            'client',
            'paymentMethod',
            'details.product'
        ])
        ->latest()
        ->get();


        $clients = Client::where('status', true)->get();


        $paymentMethods = PaymentMethod::where('status', true)->get();


        $products = Product::where('is_sale', true)
            ->where('status', true)
            ->get();


        return view('ventas.index', compact(
            'ventas',
            'clients',
            'paymentMethods',
            'products'
        ));

    }

}
