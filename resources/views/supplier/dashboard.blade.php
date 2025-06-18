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
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="pendapatanChart" style="display: block; width: 100%; height: 100%;"></canvas>
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
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;" id="produkChartContainer">
                    <canvas id="produkChart" style="display: block; width: 100%; height: 100%;"></canvas>
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
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="mingguanChart" style="display: block; width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Info (Remove in production) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-danger">Debug Info (Remove in production)</h6>
            </div>
            <div class="card-body">
                <pre id="debugInfo" style="font-size: 12px; background: #f8f9fa; padding: 10px; border-radius: 4px;"></pre>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Gunakan Chart.js versi 3.9.1 yang kompatibel -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== FRONTEND CHART DEBUG START ===');

    // Debug info element
    const debugInfo = document.getElementById('debugInfo');
    function addDebug(message) {
        console.log(message);
        if (debugInfo) {
            debugInfo.textContent += new Date().toLocaleTimeString() + ': ' + message + '\n';
        }
    }

    addDebug('DOM loaded, starting chart initialization');

    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        addDebug('ERROR: Chart.js library is not loaded!');
        return;
    } else {
        addDebug('Chart.js library loaded successfully, version: ' + Chart.version);
    }

    // Parse data JSON dengan aman
    let chartData;
    try {
        chartData = {!! json_encode($chartData) !!};
        addDebug('Chart data parsed successfully: ' + JSON.stringify(chartData));
    } catch (error) {
        addDebug('ERROR parsing chart data: ' + error.message);
        return;
    }

    // Validasi data
    if (!chartData || typeof chartData !== 'object') {
        addDebug('ERROR: Invalid chart data structure');
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

    addDebug('Canvas elements status: ' + JSON.stringify(canvasStatus));

    if (!pendapatanCanvas || !produkCanvas || !mingguanCanvas) {
        addDebug('ERROR: One or more canvas elements not found!');
        return;
    }

    // Destroy existing charts if they exist
    Chart.getChart('pendapatanChart')?.destroy();
    Chart.getChart('produkChart')?.destroy();
    Chart.getChart('mingguanChart')?.destroy();

    // Chart Pendapatan Bulanan
    try {
        addDebug('Creating pendapatan chart...');
        const pendapatanCtx = pendapatanCanvas.getContext('2d');

        // Validasi data pendapatan
        const pendapatanLabels = chartData.bulan || [];
        const pendapatanValues = (chartData.pendapatan || []).map(val => Number(val));

        addDebug('Pendapatan chart data - Labels: ' + JSON.stringify(pendapatanLabels));
        addDebug('Pendapatan chart data - Values: ' + JSON.stringify(pendapatanValues));

        if (pendapatanLabels.length === 0) {
            addDebug('WARNING: No pendapatan labels found');
        }

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
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            color: '#6c757d'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6c757d'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
        addDebug('Pendapatan chart created successfully');
    } catch (error) {
        addDebug('ERROR creating pendapatan chart: ' + error.message);
        console.error('Pendapatan chart error:', error);
    }

    // Chart Produk Terlaris
    try {
        addDebug('Creating produk chart...');
        const produkCtx = produkCanvas.getContext('2d');
        const produkData = chartData.produkTerlaris || {};

        addDebug('Produk chart data: ' + JSON.stringify(produkData));

        if (Object.keys(produkData).length === 0) {
            addDebug('No product data, showing empty state');
            document.getElementById('produkChartContainer').innerHTML =
                '<div class="d-flex align-items-center justify-content-center" style="height: 300px;">' +
                '<div class="text-center text-muted">' +
                '<i class="fas fa-chart-pie fa-3x mb-3"></i><br>' +
                'Belum ada data penjualan produk' +
                '</div></div>';
        } else {
            const produkLabels = Object.keys(produkData);
            const produkValues = Object.values(produkData).map(val => Number(val));

            const produkChart = new Chart(produkCtx, {
                type: 'doughnut',
                data: {
                    labels: produkLabels,
                    datasets: [{
                        data: produkValues,
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#17a2b8',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1',
                            '#fd7e14',
                            '#20c997'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + ' terjual';
                                }
                            }
                        }
                    }
                }
            });
            addDebug('Produk chart created successfully');
        }
    } catch (error) {
        addDebug('ERROR creating produk chart: ' + error.message);
        console.error('Produk chart error:', error);
    }

    // Chart Penjualan Mingguan
    try {
        addDebug('Creating mingguan chart...');
        const mingguanCtx = mingguanCanvas.getContext('2d');

        // Validasi data mingguan
        const mingguanLabels = chartData.minggu || [];
        const mingguanValues = (chartData.pesananMingguan || []).map(val => Number(val));

        addDebug('Mingguan chart data - Labels: ' + JSON.stringify(mingguanLabels));
        addDebug('Mingguan chart data - Values: ' + JSON.stringify(mingguanValues));

        const mingguanChart = new Chart(mingguanCtx, {
            type: 'bar',
            data: {
                labels: mingguanLabels,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: mingguanValues,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: '#007bff',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#6c757d'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6c757d'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Pesanan: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });
        addDebug('Mingguan chart created successfully');
    } catch (error) {
        addDebug('ERROR creating mingguan chart: ' + error.message);
        console.error('Mingguan chart error:', error);
    }

    addDebug('=== FRONTEND CHART DEBUG END ===');
});
</script>
@endpush