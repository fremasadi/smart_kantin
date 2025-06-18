@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
<script>
// Pastikan jQuery sudah loaded
$(document).ready(function() {
    console.log('=== FRONTEND CHART DEBUG START ===');

    // Debug info element
    const debugInfo = document.getElementById('debugInfo');
    function addDebug(message) {
        console.log(message);
        if (debugInfo) {
            debugInfo.textContent += new Date().toLocaleTimeString() + ': ' + message + '\n';
        }
    }

    addDebug('jQuery loaded, DOM ready, starting chart initialization');

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
    if (Chart.getChart('pendapatanChart')) {
        Chart.getChart('pendapatanChart').destroy();
    }
    if (Chart.getChart('produkChart')) {
        Chart.getChart('produkChart').destroy();
    }
    if (Chart.getChart('mingguanChart')) {
        Chart.getChart('mingguanChart').destroy();
    }

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
            $('#produkChartContainer').html(
                '<div class="d-flex align-items-center justify-content-center" style="height: 300px;">' +
                '<div class="text-center text-muted">' +
                '<i class="fas fa-chart-pie fa-3x mb-3"></i><br>' +
                'Belum ada data penjualan produk' +
                '</div></div>'
            );
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