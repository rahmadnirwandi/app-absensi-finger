@extends('layouts.master')

@section('title-header', 'Dashboard Absensi')

@section('content')
    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .border-left-danger {
            border-left: 4px solid #e74a3b !important;
        }

        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }

        .chart-pie,
        .chart-bar,
        .chart-area {
            position: relative;
            width: 100%;
        }

        .chart-pie {
            height: 250px;
        }

        .chart-bar {
            height: 300px;
        }

        .chart-area {
            height: 320px;
        }

        @media (max-width: 768px) {

            .chart-pie,
            .chart-bar,
            .chart-area {
                height: 250px;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Karyawan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalKaryawan ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Tepat Waktu
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalTepatWaktu ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-danger h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Terlambat
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalTerlambat ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Alpa
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalAlpa ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Kehadiran</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="pieChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Tepat Waktu
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-danger"></i> Terlambat
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-warning"></i> Alpa
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Absensi Bulanan</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Chart -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tren Kehadiran Mingguan</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        (function() {
            var totalTepatWaktu = {{ $totalTepatWaktu ?? 75 }};
            var totalTerlambat = {{ $totalTerlambat ?? 20 }};
            var totalAlpa = {{ $totalAlpa ?? 5 }};

            @if (isset($dataBulanan))
                var dataBulanan = @json($dataBulanan);
            @else
                var dataBulanan = [{
                        bulan: 'Jan',
                        tepat_waktu: 80,
                        terlambat: 15,
                        alpa: 5
                    },
                    {
                        bulan: 'Feb',
                        tepat_waktu: 85,
                        terlambat: 10,
                        alpa: 5
                    },
                    {
                        bulan: 'Mar',
                        tepat_waktu: 78,
                        terlambat: 17,
                        alpa: 5
                    },
                    {
                        bulan: 'Apr',
                        tepat_waktu: 90,
                        terlambat: 7,
                        alpa: 3
                    },
                    {
                        bulan: 'Mei',
                        tepat_waktu: 88,
                        terlambat: 8,
                        alpa: 4
                    },
                    {
                        bulan: 'Jun',
                        tepat_waktu: 82,
                        terlambat: 12,
                        alpa: 6
                    }
                ];
            @endif

            @if (isset($dataMingguan))
                var dataMingguan = @json($dataMingguan);
            @else
                var dataMingguan = [{
                        hari: 'Senin',
                        tepat_waktu: 45,
                        terlambat: 3,
                        alpa: 2
                    },
                    {
                        hari: 'Selasa',
                        tepat_waktu: 48,
                        terlambat: 1,
                        alpa: 1
                    },
                    {
                        hari: 'Rabu',
                        tepat_waktu: 44,
                        terlambat: 4,
                        alpa: 2
                    },
                    {
                        hari: 'Kamis',
                        tepat_waktu: 47,
                        terlambat: 2,
                        alpa: 1
                    },
                    {
                        hari: 'Jumat',
                        tepat_waktu: 42,
                        terlambat: 5,
                        alpa: 3
                    },
                    {
                        hari: 'Sabtu',
                        tepat_waktu: 30,
                        terlambat: 10,
                        alpa: 5
                    }
                ];
            @endif

            // Pie Chart - Flat 2D
            var pieCtx = document.getElementById('pieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Tepat Waktu', 'Terlambat', 'Alpa'],
                        datasets: [{
                            data: [totalTepatWaktu, totalTerlambat, totalAlpa],
                            backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e'],
                            borderWidth: 1,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            // Bar Chart - Flat 2D
            var barCtx = document.getElementById('barChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: dataBulanan.map(function(item) {
                            return item.bulan;
                        }),

                        datasets: [{
                                label: 'Tepat Waktu',
                                data: dataBulanan.map(function(item) {
                                    return item.tepat_waktu;
                                }),
                                backgroundColor: '#1cc88a'
                            },
                            {
                                label: 'Terlambat',
                                data: dataBulanan.map(function(item) {
                                    return item.terlambat;
                                }),
                                backgroundColor: '#e74a3b'
                            },
                            {
                                label: 'Alpa',
                                data: dataBulanan.map(function(item) {
                                    return item.alpa;
                                }),
                                backgroundColor: '#f6c23e'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

            // Line Chart - Flat 2D
            var lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: dataMingguan.map(function(item) {
                            return item.hari;
                        }),
                        datasets: [{
                                label: 'Tepat Waktu',
                                data: dataMingguan.map(function(item) {
                                    return item.tepat_waktu;
                                }),
                                borderColor: '#1cc88a',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0
                            },
                            {
                                label: 'Terlambat',
                                data: dataMingguan.map(function(item) {
                                    return item.terlambat;
                                }),
                                borderColor: '#e74a3b',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0
                            },
                            {
                                label: 'Alpa',
                                data: dataMingguan.map(function(item) {
                                    return item.alpa;
                                }),
                                borderColor: '#f6c23e',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }
        })();
    </script>
@endsection
