<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TerimaTransaksiController;
use App\Http\Controllers\OrderController;


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
Route::put('/transaksi/{id}/terima', [TerimaTransaksiController::class, 'terima'])->name('transaksi.terima');
Route::get('/order/{order}/print', [OrderController::class, 'print'])
    ->name('order.print');