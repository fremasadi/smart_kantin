<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Tanpa filter supplier, ambil semua data
        $jumlahProduk = Product::count();

        $orderItems = OrderItem::all();

        $jumlahPesanan = $orderItems->count();

        // Total pendapatan dihitung berdasarkan harga jual produk
        $totalPendapatan = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('order_items.jumlah * products.harga'));

        // Data untuk Chart
        $chartData = $this->getChartData();

        return view('supplier.dashboard', compact(
            'jumlahProduk',
            'jumlahPesanan',
            'totalPendapatan',
            'chartData'
        ));
    }

    private function getChartData()
    {
        // 1. Data Pendapatan Bulanan (6 bulan terakhir)
        $pendapatanBulanan = OrderItem::select(
                DB::raw('MONTH(order_items.created_at) as bulan'),
                DB::raw('YEAR(order_items.created_at) as tahun'),
                DB::raw('SUM(order_items.jumlah * products.harga) as total')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.created_at', '>=', Carbon::now()->subMonths(6))
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

            $nilai = $pendapatan ? (float)$pendapatan->total : 0;
            $pendapatanData[] = $nilai;
        }

        // 2. Produk Terlaris (Top 5)
        $produkTerlaris = OrderItem::select('products.nama_produk', DB::raw('SUM(order_items.jumlah) as total_terjual'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->nama_produk => (int)$item->total_terjual];
            })
            ->toArray();

        // 3. Penjualan Mingguan (4 minggu terakhir)
        $penjualanMingguan = [];
        $mingguLabels = [];

        for ($i = 3; $i >= 0; $i--) {
            $startWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endWeek = Carbon::now()->subWeeks($i)->endOfWeek();

            $mingguLabels[] = $i === 0 ? 'Minggu Ini' : 'Minggu ke-' . (4 - $i);

            $jumlahPesanan = OrderItem::whereBetween('created_at', [$startWeek, $endWeek])->count();

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
