@extends('layouts.supplier')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Dashboard Supplier</h1>
<p>Selamat datang, {{ auth()->user()->name }}!</p>

<div class="row">
    <!-- Produk -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Produk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahProduk }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pesanan -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jumlah Pesanan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPesanan }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Chart Pendapatan Bulanan -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pendapatan Bulanan (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="pendapatanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Produk Terlaris -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Produk Terlaris</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <div style="position: relative; height: 245px; width: 100%;" id="produkChartContainer">
                        <canvas id="produkChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Penjualan Mingguan -->
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Penjualan Mingguan (4 Minggu Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="mingguanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Info Panel - Remove in Production -->
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Debug Information (Remove in Production)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Chart.js Status:</h6>
                        <div id="chartjsStatus" class="mb-3"></div>

                        <h6>Canvas Elements:</h6>
                        <div id="canvasStatus" class="mb-3"></div>
                    </div>
                    <div class="col-md-6">
                        <h6>Chart Data:</h6>
                        <pre id="chartDataDisplay" style="font-size: 11px; max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 3px;"></pre>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Debug Log:</h6>
                        <div id="debugLog" style="font-size: 11px; max-height: 150px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Pastikan kita menggunakan Chart.js yang kompatibel dengan SB Admin 2 -->
<script>
// First, check if Chart is already loaded by SB Admin 2
console.log('=== SB ADMIN 2 CHART DEBUG ===');
console.log('Chart object exists:', typeof Chart !== 'undefined');
console.log('Chart version:', typeof Chart !== 'undefined' ? Chart.version : 'Not loaded');

// Function to load Chart.js if not already loaded
function loadChartJS() {
    return new Promise((resolve, reject) => {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js already loaded, version:', Chart.version);
            resolve();
            return;
        }

        console.log('Loading Chart.js...');
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        script.onload = () => {
            console.log('Chart.js loaded successfully, version:', Chart.version);
            resolve();
        };
        script.onerror = () => {
            console.error('Failed to load Chart.js');
            reject(new Error('Failed to load Chart.js'));
        };
        document.head.appendChild(script);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const debugLog = document.getElementById('debugLog');
    const chartjsStatus = document.getElementById('chartjsStatus');
    const canvasStatus = document.getElementById('canvasStatus');
    const chartDataDisplay = document.getElementById('chartDataDisplay');

    function addDebug(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logMessage = `[${timestamp}] ${type.toUpperCase()}: ${message}`;
        console.log(logMessage);
        if (debugLog) {
            debugLog.innerHTML += logMessage + '\n';
            debugLog.scrollTop = debugLog.scrollHeight;
        }
    }

    addDebug('Dashboard initialization started');

    // Check Chart.js status
    if (chartjsStatus) {
        if (typeof Chart !== 'undefined') {
            chartjsStatus.innerHTML = `<span class="text-success">✓ Loaded (v${Chart.version})</span>`;
        } else {
            chartjsStatus.innerHTML = `<span class="text-danger">✗ Not loaded</span>`;
        }
    }

    // Parse chart data
    let chartData;
    try {
        chartData = {!! json_encode($chartData) !!};
        addDebug('Chart data parsed successfully');
        if (chartDataDisplay) {
            chartDataDisplay.textContent = JSON.stringify(chartData, null, 2);
        }
    } catch (error) {
        addDebug('Error parsing chart data: ' + error.message, 'error');
        return;
    }

    // Check canvas elements
    const pendapatanCanvas = document.getElementById('pendapatanChart');
    const produkCanvas = document.getElementById('produkChart');
    const mingguanCanvas = document.getElementById('mingguanChart');

    const canvasInfo = {
        pendapatan: {
            exists: !!pendapatanCanvas,
            visible: pendapatanCanvas ? window.getComputedStyle(pendapatanCanvas).display !== 'none' : false,
            dimensions: pendapatanCanvas ? `${pendapatanCanvas.offsetWidth}x${pendapatanCanvas.offsetHeight}` : 'N/A'
        },
        produk: {
            exists: !!produkCanvas,
            visible: produkCanvas ? window.getComputedStyle(produkCanvas).display !== 'none' : false,
            dimensions: produkCanvas ? `${produkCanvas.offsetWidth}x${produkCanvas.offsetHeight}` : 'N/A'
        },
        mingguan: {
            exists: !!mingguanCanvas,
            visible: mingguanCanvas ? window.getComputedStyle(mingguanCanvas).display !== 'none' : false,
            dimensions: mingguanCanvas ? `${mingguanCanvas.offsetWidth}x${mingguanCanvas.offsetHeight}` : 'N/A'
        }
    };

    if (canvasStatus) {
        let statusHTML = '';
        Object.keys(canvasInfo).forEach(key => {
            const info = canvasInfo[key];
            const status = info.exists && info.visible ? 'success' : 'danger';
            const icon = info.exists && info.visible ? '✓' : '✗';
            statusHTML += `<div><span class="text-${status}">${icon} ${key}: ${info.exists ? 'exists' : 'missing'} ${info.visible ? '(visible)' : '(hidden)'} - ${info.dimensions}</span></div>`;
        });
        canvasStatus.innerHTML = statusHTML;
    }

    addDebug('Canvas check completed');

    if (!pendapatanCanvas || !produkCanvas || !mingguanCanvas) {
        addDebug('One or more canvas elements not found!', 'error');
        return;
    }

    // Load Chart.js and create charts
    loadChartJS().then(() => {
        addDebug('Starting chart creation');

        // Destroy existing charts to prevent conflicts
        if (typeof Chart !== 'undefined') {
            Chart.getChart('pendapatanChart')?.destroy();
            Chart.getChart('produkChart')?.destroy();
            Chart.getChart('mingguanChart')?.destroy();
        }

        // Chart Pendapatan Bulanan
        try {
            addDebug('Creating pendapatan chart');
            const pendapatanCtx = pendapatanCanvas.getContext('2d');

            const pendapatanLabels = chartData.bulan || [];
            const pendapatanValues = (chartData.pendapatan || []).map(val => Number(val) || 0);

            addDebug(`Pendapatan data: ${pendapatanLabels.length} labels, ${pendapatanValues.length} values`);

            new Chart(pendapatanCtx, {
                type: 'line',
                data: {
                    labels: pendapatanLabels,
                    datasets: [{
                        label: 'Pendapatan',
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: pendapatanValues,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        },
                        y: {
                            display: true,
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value, index, values) {
                                    return 'Rp ' + number_format(value);
                                }
                            },
                            grid: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            titleMarginBottom: 10,
                            titleColor: '#6e707e',
                            titleFont: {
                                size: 14
                            },
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            caretPadding: 10,
                            callbacks: {
                                label: function(tooltipItem) {
                                    return 'Pendapatan: Rp ' + number_format(tooltipItem.parsed.y);
                                }
                            }
                        }
                    }
                }
            });
            addDebug('Pendapatan chart created successfully');
        } catch (error) {
            addDebug('Error creating pendapatan chart: ' + error.message, 'error');
        }

        // Chart Produk Terlaris
        try {
            addDebug('Creating produk chart');
            const produkCtx = produkCanvas.getContext('2d');
            const produkData = chartData.produkTerlaris || {};

            if (Object.keys(produkData).length === 0) {
                addDebug('No product data available');
                document.getElementById('produkChartContainer').innerHTML =
                    '<div class="d-flex align-items-center justify-content-center" style="height: 245px;">' +
                    '<div class="text-center text-muted">' +
                    '<i class="fas fa-chart-pie fa-3x mb-3"></i><br>' +
                    'Belum ada data penjualan produk' +
                    '</div></div>';
            } else {
                const produkLabels = Object.keys(produkData);
                const produkValues = Object.values(produkData).map(val => Number(val) || 0);

                new Chart(produkCtx, {
                    type: 'doughnut',
                    data: {
                        labels: produkLabels,
                        datasets: [{
                            data: produkValues,
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02424'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                caretPadding: 10,
                            },
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        },
                        cutout: 80,
                    },
                });
                addDebug('Produk chart created successfully');
            }
        } catch (error) {
            addDebug('Error creating produk chart: ' + error.message, 'error');
        }

        // Chart Penjualan Mingguan
        try {
            addDebug('Creating mingguan chart');
            const mingguanCtx = mingguanCanvas.getContext('2d');

            const mingguanLabels = chartData.minggu || [];
            const mingguanValues = (chartData.pesananMingguan || []).map(val => Number(val) || 0);

            new Chart(mingguanCtx, {
                type: 'bar',
                data: {
                    labels: mingguanLabels,
                    datasets: [{
                        label: "Pesanan",
                        backgroundColor: "#4e73df",
                        hoverBackgroundColor: "#2e59d9",
                        borderColor: "#4e73df",
                        data: mingguanValues,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 6
                            },
                        },
                        y: {
                            display: true,
                            ticks: {
                                maxTicksLimit: 6,
                                padding: 10,
                                beginAtZero: true,
                                stepSize: 1
                            },
                            grid: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            titleMarginBottom: 10,
                            titleColor: '#6e707e',
                            titleFont: {
                                size: 14
                            },
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            caretPadding: 10,
                            callbacks: {
                                label: function(tooltipItem) {
                                    return 'Pesanan: ' + tooltipItem.parsed.y;
                                }
                            }
                        }
                    }
                }
            });
            addDebug('Mingguan chart created successfully');
        } catch (error) {
            addDebug('Error creating mingguan chart: ' + error.message, 'error');
        }

        addDebug('All charts initialization completed');

    }).catch(error => {
        addDebug('Failed to load Chart.js: ' + error.message, 'error');
    });
});

// Helper function for number formatting
function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>
@endpush