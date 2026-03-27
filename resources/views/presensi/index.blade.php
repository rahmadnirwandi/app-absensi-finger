@extends('layouts.master')

@section('title-header', 'Presensi Fingerprint')

@section('content')
    <div class="container-fluid px-2 px-md-4">

        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-4">
                <div class="card border-dark-subtle h-100">
                    <div class="card-header py-3 border-bottom">
                        <h6 class="m-0 font-weight-bold text-dark text-uppercase small">
                            Ringkasan Bulan Ini
                        </h6>
                    </div>

                    <div class="card-body">
                        <div style="position: relative; height: 200px; width: 100%;">
                            <canvas id="chartKehadiranBulanan"></canvas>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="small">
                                <i class="fa-solid fa-square me-2" style="color: #2ecc71;"></i>
                                Tepat Waktu
                            </span>
                                <span class="fw-bold small">
                                {{ $bulanan['tepat'] ?? 0 }} Hari
                            </span>
                            </div>

                            <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="small">
                                <i class="fa-solid fa-square me-2" style="color: #e74c3c;"></i>
                                Terlambat
                            </span>
                                <span class="fw-bold small">
                                {{ $bulanan['telat'] ?? 0 }} Hari
                            </span>
                            </div>

                            <div class="d-flex justify-content-between py-2">
                            <span class="small">
                                <i class="fa-solid fa-square me-2" style="color: #f1c40f;"></i>
                                Alpa
                            </span>
                                <span class="fw-bold small">
                                {{ $bulanan['alpa'] ?? 0 }} Hari
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8">
                <div class="card border-dark-subtle h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center border-bottom">
                        <h6 class="m-0 font-weight-bold text-dark text-uppercase small">
                            Log Fingerprint Terakhir
                        </h6>
                        <span class="badge border text-dark fw-normal small">
                        STATUS: ONLINE
                    </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="thead-custom text-center small">
                                <tr>
                                    <th class="py-3">TANGGAL</th>
                                    <th>MASUK</th>
                                    <th>PULANG</th>
                                    <th>STATUS</th>
                                </tr>
                                </thead>
                                <tbody class="text-center small">

                                @forelse($log->groupBy(function($item){
                                    return \Carbon\Carbon::parse($item->waktu)->format('Y-m-d');
                                }) as $tanggal => $items)

                                    @php
                                        $masuk = \Carbon\Carbon::parse($items->first()->waktu)->format('H:i:s');
                                        $pulang = $items->count() > 1
                                            ? \Carbon\Carbon::parse($items->last()->waktu)->format('H:i:s')
                                            : '--:--:--';
                                    @endphp

                                    <tr>
                                        <td class="fw-bold">
                                            {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                                        </td>
                                        <td>{{ $masuk }}</td>
                                        <td>{{ $pulang }}</td>
                                        <td>
                                            @if($pulang != '--:--:--')
                                                <span class="text-success fw-bold">HADIR</span>
                                            @else
                                                <span class="text-warning fw-bold">BELUM PULANG</span>
                                            @endif
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">
                                            Tidak ada data presensi
                                        </td>
                                    </tr>
                                @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= STATUS HARI INI ================= --}}
        <div class="row g-3">
            <div class="col-12">
                <div class="card border-dark-subtle bg-light">
                    <div class="card-body p-3 p-md-4">
                        <div class="row align-items-center">

                            <div class="col-12 col-md-6 mb-3 mb-md-0 text-center text-md-start">
                                <h6 class="fw-bold text-uppercase mb-1 small text-primary">
                                    Status Presensi Hari Ini
                                </h6>
                                <p class="mb-0 text-muted fw-bold small">
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </p>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column flex-sm-row justify-content-md-end gap-2">

                                    <div class="border bg-white p-2 flex-fill text-center">
                                        <small class="text-muted d-block small-label">
                                            SCAN MASUK
                                        </small>
                                        <span class="fw-bold d-block">
                                        {{ $hariIni['scanMasuk'] ?? '--:--:--' }}
                                    </span>
                                    </div>

                                    <div class="border bg-white p-2 flex-fill text-center">
                                        <small class="text-muted d-block small-label">
                                            SCAN PULANG
                                        </small>
                                        <span class="fw-bold d-block text-muted">
                                        {{ $hariIni['scanPulang'] ?? '--:--:--' }}
                                    </span>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <p class="text-muted mb-0" style="font-size: 11px;">
                        * Sinkronisasi otomatis dari mesin fingerprint ke sistem pada
                        {{ date('H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{-- ================= STYLE ================= --}}
    <style>
        .card { box-shadow: none !important; border-radius: 0px !important; }
        .thead-custom { background-color: #a3abb3ff !important; color: #fff; }
        .small-label { font-size: 10px; font-weight: bold; letter-spacing: 0.5px; }
    </style>


    {{-- ================= CHART ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartKehadiranBulanan').getContext('2d');

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Tepat Waktu', 'Terlambat', 'Alpa'],
                datasets: [{
                    data: [
                        {{ $bulanan['tepat'] ?? 0 }},
                        {{ $bulanan['telat'] ?? 0 }},
                        {{ $bulanan['alpa'] ?? 0 }}
                    ],
                    backgroundColor: ['#2ecc71', '#e74c3c', '#f1c40f'],
                    borderWidth: 1,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    </script>

@endsection