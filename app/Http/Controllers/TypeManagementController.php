<?php

namespace App\Http\Controllers;

use App\Models\TypeManagement;
use Illuminate\Http\Request;

class TypeManagementController extends Controller
{
    public function index()
    {
        return response()->json(TypeManagement::all());
    }
}
