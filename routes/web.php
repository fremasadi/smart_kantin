<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TerimaTransaksiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierAuthController;
use App\Http\Controllers\SupplierDashboardController;
use App\Http\Controllers\Supplier\ProductController;
use App\Http\Controllers\Supplier\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Halaman login untuk supplier
Route::get('/login', [SupplierAuthController::class, 'showLoginForm'])->name('supplier.login');
Route::post('/login', [SupplierAuthController::class, 'login'])->name('supplier.login.submit');

// Dashboard supplier (proteksi dengan middleware)
Route::middleware(['auth', 'role:supplier'])->group(function () {
    Route::get('/supplier/dashboard', [SupplierDashboardController::class, 'index'])->name('supplier.dashboard');
    Route::resource('products', ProductController::class);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('supplier.dashboard');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');
    
});



Route::put('/transaksi/{id}/terima', [TerimaTransaksiController::class, 'terima'])->name('transaksi.terima');
Route::get('/order/{order}/print', [OrderController::class, 'print'])
    ->name('order.print');