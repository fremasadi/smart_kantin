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
                <div style="position: relative; height: 300px;">
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
                <div style="position: relative; height: 300px;" id="produkChartContainer">
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
                <div style="position: relative; height: 300px;">
                    <canvas id="mingguanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');

    // Parse data JSON dengan aman
    let chartData;
    try {
        chartData = {!! json_encode($chartData) !!};
        console.log('Chart Data:', chartData);
    } catch (error) {
        console.error('Error parsing chart data:', error);
        return;
    }

    // Validasi data
    if (!chartData || typeof chartData !== 'object') {
        console.error('Invalid chart data');
        return;
    }

    // Get canvas elements
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

        // Validasi data pendapatan
        const pendapatanLabels = chartData.bulan || [];
        const pendapatanValues = chartData.pendapatan || [];

        console.log('Pendapatan data:', { labels: pendapatanLabels, values: pendapatanValues });

        new Chart(pendapatanCtx, {
            type: 'line',
            data: {
                labels: pendapatanLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: pendapatanValues,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#007bff',
                    pointRadius: 5
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
                },
                plugins: {
                    legend: {
                        display: true
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
        const produkData = chartData.produkTerlaris || {};

        console.log('Produk data:', produkData);

        if (Object.keys(produkData).length === 0) {
            document.getElementById('produkChartContainer').innerHTML =
                '<div class="d-flex align-items-center justify-content-center h-100">' +
                '<div class="text-center text-muted">' +
                '<i class="fas fa-chart-pie fa-3x mb-3"></i><br>' +
                'Belum ada data penjualan produk' +
                '</div></div>';
        } else {
            new Chart(produkCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(produkData),
                    datasets: [{
                        data: Object.values(produkData),
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#17a2b8',
                            '#ffc107',
                            '#dc3545'
                        ],
                        borderColor: '#fff',
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

        // Validasi data mingguan
        const mingguanLabels = chartData.minggu || [];
        const mingguanValues = chartData.pesananMingguan || [];

        console.log('Mingguan data:', { labels: mingguanLabels, values: mingguanValues });

        new Chart(mingguanCtx, {
            type: 'bar',
            data: {
                labels: mingguanLabels,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: mingguanValues,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: '#007bff',
                    borderWidth: 2
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
                },
                plugins: {
                    legend: {
                        display: true
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