<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Statistik hari ini
        $todayOrders = Order::whereDate('tanggal_pesanan', today())->count();
        $todayRevenue = Order::whereDate('tanggal_pesanan', today())->sum('total_harga');
        
        // Statistik bulan ini
        $monthlyOrders = Order::whereMonth('tanggal_pesanan', now()->month)
            ->whereYear('tanggal_pesanan', now()->year)
            ->count();
        $monthlyRevenue = Order::whereMonth('tanggal_pesanan', now()->month)
            ->whereYear('tanggal_pesanan', now()->year)
            ->sum('total_harga');

        // Total produk dan stok rendah
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stok', '<=', 10)->count();
        
        // Total users
        $totalUsers = User::count();
        $totalSuppliers = User::where('role', 'supplier')->count();

        return [
            Stat::make('Pesanan Hari Ini', $todayOrders)
                ->description('Total pesanan masuk hari ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Total pendapatan hari ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pesanan Bulan Ini', $monthlyOrders)
                ->description('Total pesanan bulan ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($monthlyRevenue, 0, ',', '.'))
                ->description('Total pendapatan bulan ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Total Produk', $totalProducts)
                ->description('Jumlah produk tersedia')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Stok Rendah', $lowStockProducts)
                ->description('Produk dengan stok ≤ 10')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'warning' : 'success'),

            Stat::make('Total Pengguna', $totalUsers)
                ->description('Jumlah pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Supplier', $totalSuppliers)
                ->description('Jumlah supplier aktif')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),
        ];
    }
}