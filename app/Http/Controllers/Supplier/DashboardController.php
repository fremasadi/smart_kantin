<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $supplierId = Auth::id();

        // Data yang sudah ada
        $jumlahProduk = Product::where('supplier_id', $supplierId)->count();

        $orderItems = OrderItem::whereHas('product', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })->get();

        $jumlahPesanan = $orderItems->count();
        $totalPendapatan = $orderItems->sum('subtotal');

        // Data untuk Chart
        $chartData = $this->getChartData($supplierId);

        return view('supplier.dashboard', compact(
            'jumlahProduk',
            'jumlahPesanan',
            'totalPendapatan',
            'chartData'
        ));
    }

    private function getChartData($supplierId)
    {
        // 1. Data Pendapatan Bulanan (6 bulan terakhir)
        $pendapatanBulanan = OrderItem::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('SUM(subtotal) as total')
            )
            ->whereHas('product', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        $bulanLabels = [];
        $pendapatanData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulanLabels[] = $date->format('M Y');

            $pendapatan = $pendapatanBulanan->where('bulan', $date->month)
                                          ->where('tahun', $date->year)
                                          ->first();
            $pendapatanData[] = $pendapatan ? $pendapatan->total : 0;
        }

        // 2. Produk Terlaris (Top 5)
        $produkTerlaris = OrderItem::select('products.nama_produk', DB::raw('SUM(order_items.jumlah) as total_terjual'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.supplier_id', $supplierId)
            ->groupBy('products.id', 'products.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get()
            ->pluck('total_terjual', 'nama_produk')
            ->toArray();

        // 3. Penjualan Mingguan (4 minggu terakhir)
        $penjualanMingguan = [];
        $mingguLabels = [];

        for ($i = 3; $i >= 0; $i--) {
            $startWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endWeek = Carbon::now()->subWeeks($i)->endOfWeek();

            $mingguLabels[] = 'Minggu ' . ($i == 0 ? 'Ini' : $i + 1);

            $jumlahPesanan = OrderItem::whereHas('product', function ($q) use ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                })
                ->whereBetween('created_at', [$startWeek, $endWeek])
                ->count();

            $penjualanMingguan[] = $jumlahPesanan;
        }

        return [
            'bulan' => $bulanLabels,
            'pendapatan' => $pendapatanData,
            'produkTerlaris' => $produkTerlaris,
            'minggu' => $mingguLabels,
            'pesananMingguan' => $penjualanMingguan
        ];
    }
}