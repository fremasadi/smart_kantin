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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logging functions untuk Laravel log
    function logToLaravel(level, message, data = null) {
        fetch('/log-frontend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                level: level,
                message: message,
                data: data,
                url: window.location.href,
                timestamp: new Date().toISOString()
            })
        }).catch(e => console.error('Failed to log to Laravel:', e));
    }

    logToLaravel('info', '=== FRONTEND CHART DEBUG START ===');
    logToLaravel('info', 'DOM loaded, starting chart initialization');

    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        logToLaravel('error', 'Chart.js library is not loaded!');
        return;
    } else {
        logToLaravel('info', 'Chart.js library loaded successfully, version: ' + Chart.version);
    }

    // Parse data JSON dengan aman
    let chartData;
    try {
        chartData = {!! json_encode($chartData) !!};
        logToLaravel('info', 'Chart data parsed successfully', chartData);
    } catch (error) {
        logToLaravel('error', 'Error parsing chart data: ' + error.message);
        return;
    }

    // Validasi data
    if (!chartData || typeof chartData !== 'object') {
        logToLaravel('error', 'Invalid chart data structure');
        return;
    }

    // Get canvas elements
    const pendapatanCanvas = document.getElementById('pendapatanChart');
    const produkCanvas = document.getElementById('produkChart');
    const mingguanCanvas = document.getElementById('mingguanChart');

    const canvasStatus = {
        pendapatan: !!pendapatanCanvas,
        produk: !!produkCanvas,
        mingguan: !!mingguanCanvas
    };

    logToLaravel('info', 'Canvas elements status', canvasStatus);

    if (!pendapatanCanvas || !produkCanvas || !mingguanCanvas) {
        logToLaravel('error', 'One or more canvas elements not found!');
        return;
    }

    // Chart Pendapatan Bulanan
    try {
        logToLaravel('info', 'Creating pendapatan chart...');
        const pendapatanCtx = pendapatanCanvas.getContext('2d');

        // Validasi data pendapatan
        const pendapatanLabels = chartData.bulan || [];
        const pendapatanValues = chartData.pendapatan || [];

        logToLaravel('info', 'Pendapatan chart data', {
            labels: pendapatanLabels,
            values: pendapatanValues,
            labelsLength: pendapatanLabels.length,
            valuesLength: pendapatanValues.length
        });

        const pendapatanChart = new Chart(pendapatanCtx, {
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
        logToLaravel('info', 'Pendapatan chart created successfully');
    } catch (error) {
        logToLaravel('error', 'Error creating pendapatan chart: ' + error.message, {
            stack: error.stack
        });
    }

    // Chart Produk Terlaris
    try {
        logToLaravel('info', 'Creating produk chart...');
        const produkCtx = produkCanvas.getContext('2d');
        const produkData = chartData.produkTerlaris || {};

        logToLaravel('info', 'Produk chart data', produkData);

        if (Object.keys(produkData).length === 0) {
            logToLaravel('info', 'No product data, showing empty state');
            document.getElementById('produkChartContainer').innerHTML =
                '<div class="d-flex align-items-center justify-content-center h-100">' +
                '<div class="text-center text-muted">' +
                '<i class="fas fa-chart-pie fa-3x mb-3"></i><br>' +
                'Belum ada data penjualan produk' +
                '</div></div>';
        } else {
            const produkChart = new Chart(produkCtx, {
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
            logToLaravel('info', 'Produk chart created successfully');
        }
    } catch (error) {
        logToLaravel('error', 'Error creating produk chart: ' + error.message, {
            stack: error.stack
        });
    }

    // Chart Penjualan Mingguan
    try {
        logToLaravel('info', 'Creating mingguan chart...');
        const mingguanCtx = mingguanCanvas.getContext('2d');

        // Validasi data mingguan
        const mingguanLabels = chartData.minggu || [];
        const mingguanValues = chartData.pesananMingguan || [];

        logToLaravel('info', 'Mingguan chart data', {
            labels: mingguanLabels,
            values: mingguanValues,
            labelsLength: mingguanLabels.length,
            valuesLength: mingguanValues.length
        });

        const mingguanChart = new Chart(mingguanCtx, {
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
        logToLaravel('info', 'Mingguan chart created successfully');
    } catch (error) {
        logToLaravel('error', 'Error creating mingguan chart: ' + error.message, {
            stack: error.stack
        });
    }

    logToLaravel('info', '=== FRONTEND CHART DEBUG END ===');
});
</script>
@endpush