<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $supplierId = Auth::id();
            Log::info('=== DASHBOARD DEBUG START ===');
            Log::info('Supplier ID: ' . $supplierId);

            // Data yang sudah ada
            $jumlahProduk = Product::where('supplier_id', $supplierId)->count();
            Log::info('Jumlah Produk: ' . $jumlahProduk);

            $orderItems = OrderItem::whereHas('product', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })->get();

            $jumlahPesanan = $orderItems->count();
            $totalPendapatan = $orderItems->sum('subtotal');

            Log::info('Jumlah Pesanan: ' . $jumlahPesanan);
            Log::info('Total Pendapatan: ' . $totalPendapatan);

            // Data untuk Chart
            $chartData = $this->getChartData($supplierId);

            // Log untuk debugging
            Log::info('Final Chart Data:');
            Log::info('- Bulan labels: ' . json_encode($chartData['bulan']));
            Log::info('- Pendapatan data: ' . json_encode($chartData['pendapatan']));
            Log::info('- Produk terlaris: ' . json_encode($chartData['produkTerlaris']));
            Log::info('- Minggu labels: ' . json_encode($chartData['minggu']));
            Log::info('- Pesanan mingguan: ' . json_encode($chartData['pesananMingguan']));

            // Test JSON encoding
            $jsonTest = json_encode($chartData);
            if ($jsonTest === false) {
                Log::error('JSON encoding failed: ' . json_last_error_msg());
            } else {
                Log::info('JSON encoding successful, length: ' . strlen($jsonTest));
            }

            Log::info('=== DASHBOARD DEBUG END ===');

            return view('supplier.dashboard', compact(
                'jumlahProduk',
                'jumlahPesanan',
                'totalPendapatan',
                'chartData'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Controller Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function getChartData($supplierId)
    {
        try {
            Log::info('=== CHART DATA GENERATION START ===');

            // 1. Data Pendapatan Bulanan (6 bulan terakhir)
            Log::info('Generating monthly revenue data...');

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

            Log::info('Raw pendapatan data: ' . $pendapatanBulanan->toJson());

            $bulanLabels = [];
            $pendapatanData = [];

            // Fix: Buat array 6 bulan terakhir dengan benar
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $bulanLabels[] = $date->format('M Y');

                $pendapatan = $pendapatanBulanan->where('bulan', $date->month)
                                              ->where('tahun', $date->year)
                                              ->first();

                // Fix: Convert to float/int to ensure proper JSON encoding
                $nilai = $pendapatan ? (float)$pendapatan->total : 0;
                $pendapatanData[] = $nilai;

                Log::info("Month {$date->format('M Y')}: {$nilai}");
            }

            // 2. Produk Terlaris (Top 5)
            Log::info('Generating top products data...');

            $produkQuery = OrderItem::select('products.nama_produk', DB::raw('SUM(order_items.jumlah) as total_terjual'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.supplier_id', $supplierId)
                ->groupBy('products.id', 'products.nama_produk')
                ->orderBy('total_terjual', 'desc')
                ->limit(5);

            Log::info('Top products query: ' . $produkQuery->toSql());
            Log::info('Query bindings: ' . json_encode($produkQuery->getBindings()));

            $produkTerlaris = $produkQuery->get()
                ->mapWithKeys(function ($item) {
                    Log::info("Product: {$item->nama_produk}, Sold: {$item->total_terjual}");
                    // Fix: Ensure numeric values
                    return [$item->nama_produk => (int)$item->total_terjual];
                })
                ->toArray();

            Log::info('Final produk terlaris: ' . json_encode($produkTerlaris));

            // 3. Penjualan Mingguan (4 minggu terakhir)
            Log::info('Generating weekly sales data...');

            $penjualanMingguan = [];
            $mingguLabels = [];

            // Fix: Revisi logika minggu labels
            for ($i = 3; $i >= 0; $i--) {
                $startWeek = Carbon::now()->subWeeks($i)->startOfWeek();
                $endWeek = Carbon::now()->subWeeks($i)->endOfWeek();

                // Fix: Label minggu yang lebih jelas
                if ($i == 0) {
                    $mingguLabels[] = 'Minggu Ini';
                } else {
                    $mingguLabels[] = 'Minggu ke-' . (4 - $i);
                }

                $jumlahPesanan = OrderItem::whereHas('product', function ($q) use ($supplierId) {
                        $q->where('supplier_id', $supplierId);
                    })
                    ->whereBetween('created_at', [$startWeek, $endWeek])
                    ->count();

                $penjualanMingguan[] = $jumlahPesanan;

                Log::info("Week {$startWeek->format('Y-m-d')} to {$endWeek->format('Y-m-d')}: {$jumlahPesanan} orders");
            }

            $result = [
                'bulan' => $bulanLabels,
                'pendapatan' => $pendapatanData,
                'produkTerlaris' => $produkTerlaris,
                'minggu' => $mingguLabels,
                'pesananMingguan' => $penjualanMingguan
            ];

            Log::info('=== CHART DATA GENERATION END ===');

            return $result;

        } catch (\Exception $e) {
            Log::error('Error in getChartData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return empty data structure to prevent blade errors
            return [
                'bulan' => [],
                'pendapatan' => [],
                'produkTerlaris' => [],
                'minggu' => [],
                'pesananMingguan' => []
            ];
        }
    }
}