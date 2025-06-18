<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        $supplierId = Auth::id();

        $jumlahProduk = Product::where('supplier_id', $supplierId)->count();

        $orderItems = OrderItem::whereHas('product', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })->get();

        $jumlahPesanan = $orderItems->count();
        $totalPendapatan = $orderItems->sum(function ($item) {
            return $item->jumlah * $item->harga;
        });

        return view('supplier.dashboard', compact('jumlahProduk', 'jumlahPesanan', 'totalPendapatan'));
    }
}
