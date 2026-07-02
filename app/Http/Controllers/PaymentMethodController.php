<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;


class PaymentMethodController extends Controller
{


    public function index()
    {

        $methods = PaymentMethod::all();

        return view(
            'payment_methods.index',
            compact('methods')
        );

    }



    public function store(Request $request)
    {

        $request->validate([
            'name'=>'required'
        ]);


        PaymentMethod::create([
            'name'=>$request->name
        ]);


        return back();

    }



    public function destroy(PaymentMethod $paymentMethod)
    {

        $paymentMethod->delete();

        return back();

    }

}
