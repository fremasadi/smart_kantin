@extends('layouts.supplier')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Dashboard Supplier</h1>
<p>Selamat datang, {{ auth()->user()->name }}!</p>

<div class="row">
    <!-- Produk -->
    <div class="col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Produk</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahProduk }}</div>
            </div>
        </div>
    </div>

    <!-- Pesanan -->
    <div class="col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jumlah Pesanan</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPesanan }}</div>
            </div>
        </div>
    </div>

    <!-- Pendapatan -->
    <div class="col-md-4 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pendapatan</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mt-4">
    <!-- Chart Pendapatan Bulanan -->
    <div class="col-md-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pendapatan Bulanan (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div style="height: 300px; border: 1px solid #ddd; background-color: #fafafa; padding: 10px;">
                    <canvas id="pendapatanChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Produk Terlaris -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Produk Terlaris</h6>
            </div>
            <div class="card-body">
                <div style="height: 300px; border: 1px solid #ddd; background-color: #fafafa; padding: 10px;">
                    <canvas id="produkChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Penjualan Mingguan -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Penjualan Mingguan (4 Minggu Terakhir)</h6>
            </div>
            <div class="card-body">
                <div style="height: 300px; border: 1px solid #ddd; background-color: #fafafa; padding: 10px;">
                    <canvas id="mingguanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');

    // Debug: Cek data yang diterima
    const chartData = {!! json_encode($chartData) !!};
    console.log('Chart Data:', chartData);

    // Cek apakah canvas ada
    const pendapatanCanvas = document.getElementById('pendapatanChart');
    const produkCanvas = document.getElementById('produkChart');
    const mingguanCanvas = document.getElementById('mingguanChart');

    console.log('Canvas elements:', {
        pendapatan: pendapatanCanvas,
        produk: produkCanvas,
        mingguan: mingguanCanvas
    });

    if (!pendapatanCanvas || !produkCanvas || !mingguanCanvas) {
        console.error('Canvas elements not found!');
        return;
    }

    // Chart Pendapatan Bulanan
    try {
        const pendapatanCtx = pendapatanCanvas.getContext('2d');
        new Chart(pendapatanCtx, {
            type: 'line',
            data: {
                labels: chartData.bulan,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: chartData.pendapatan,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
        console.log('Pendapatan chart created successfully');
    } catch (error) {
        console.error('Error creating pendapatan chart:', error);
    }

    // Chart Produk Terlaris
    try {
        const produkCtx = produkCanvas.getContext('2d');
        const produkData = chartData.produkTerlaris;

        if (Object.keys(produkData).length === 0) {
            produkCanvas.parentElement.innerHTML =
                '<div class="text-center text-muted py-4">Belum ada data penjualan produk</div>';
        } else {
            new Chart(produkCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(produkData),
                    datasets: [{
                        data: Object.values(produkData),
                        backgroundColor: [
                            '#4e73df',
                            '#1cc88a',
                            '#36b9cc',
                            '#f6c23e',
                            '#e74a3b'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            console.log('Produk chart created successfully');
        }
    } catch (error) {
        console.error('Error creating produk chart:', error);
    }

    // Chart Penjualan Mingguan
    try {
        const mingguanCtx = mingguanCanvas.getContext('2d');
        new Chart(mingguanCtx, {
            type: 'bar',
            data: {
                labels: chartData.minggu,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: chartData.pesananMingguan,
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: '#1cc88a',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        console.log('Mingguan chart created successfully');
    } catch (error) {
        console.error('Error creating mingguan chart:', error);
    }
});
</script>
@endpush