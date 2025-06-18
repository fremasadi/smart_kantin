<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('supplier.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (auth()->user()->role === 'supplier') {
                return redirect()->route('supplier.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Bukan akun supplier.']);
            }
        }

        return back()->withErrors(['email' => 'Login gagal.']);
    }
}
