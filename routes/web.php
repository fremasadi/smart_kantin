<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TerimaTransaksiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierAuthController;
use App\Http\Controllers\SupplierDashboardController;
use App\Http\Controllers\Supplier\ProductController;
use App\Http\Controllers\Supplier\DashboardController;
use App\Http\Controllers\Supplier\LaporanController;
use App\Http\Controllers\Supplier\KasirController;
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
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/supplier/dashboard', [SupplierDashboardController::class, 'index'])->name('supplier.dashboard');
    Route::resource('products', ProductController::class);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('supplier.dashboard');
    Route::get('/laporan-penjualan', [LaporanController::class, 'index'])->name('supplier.laporan');
    Route::get('/laporan-penjualan/export', [LaporanController::class, 'export'])->name('supplier.laporan.export');
// Route baru untuk kasir
Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [KasirController::class, 'index'])->name('index');
    Route::get('/products', [KasirController::class, 'getProducts'])->name('products');
    Route::get('/customers', [KasirController::class, 'getCustomers'])->name('customers');
    Route::get('/produk-populer', [KasirController::class, 'getProdukPopuler'])->name('produk-populer');
    Route::get('/transaksi-terakhir', [KasirController::class, 'getTransaksiTerakhir'])->name('transaksi-terakhir');
    Route::post('/store', [KasirController::class, 'store'])->name('store');
    Route::get('/order/{id}', [KasirController::class, 'show'])->name('show');
    Route::get('/print/{id}', [KasirController::class, 'printReceipt'])->name('print');
});
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');

});



Route::put('/transaksi/{id}/terima', [TerimaTransaksiController::class, 'terima'])->name('transaksi.terima');
Route::get('/order/{order}/print', [OrderController::class, 'print'])
    ->name('order.print');


// Tambahkan di routes/web.php
Route::post('/log-frontend', function (Request $request) {
    $level = $request->input('level', 'info');
    $message = $request->input('message', '');
    $data = $request->input('data');
    $url = $request->input('url', '');
    $timestamp = $request->input('timestamp', now());

    $logMessage = "[FRONTEND] {$message}";
    if ($url) {
        $logMessage .= " | URL: {$url}";
    }
    if ($data) {
        $logMessage .= " | Data: " . json_encode($data);
    }

    switch ($level) {
        case 'error':
            Log::error($logMessage);
            break;
        case 'warning':
            Log::warning($logMessage);
            break;
        case 'debug':
            Log::debug($logMessage);
            break;
        default:
            Log::info($logMessage);
    }

    return response()->json(['status' => 'logged']);
})->middleware(['web', 'auth']);