<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierDashboardController extends Controller
{
    public function index()
    {
        return view('supplier.dashboard');
    }
}
