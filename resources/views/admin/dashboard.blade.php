@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 text-white-50 small fw-semibold text-uppercase">Total Produk</p>
                    <h3 class="mb-0 text-white fw-bold">{{ $total_produk }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-helmet-safety"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 text-white-50 small fw-semibold text-uppercase">Pesanan Hari Ini</p>
                    <h3 class="mb-0 text-white fw-bold">{{ $pesanan_hari_ini }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 text-white-50 small fw-semibold text-uppercase">Siap Diambil</p>
                    <h3 class="mb-0 text-white fw-bold">{{ $pesanan_siap_ambil }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 text-white-50 small fw-semibold text-uppercase">Omzet Bulan Ini</p>
                    <h3 class="mb-0 text-white fw-bold">{{ format_rupiah($omzet_bulan) }}</h3>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-money-bill-trend-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="card card-bayeng mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Grafik Penjualan {{ date('Y') }}</h5>
    </div>
    <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

{{-- Two Columns --}}
<div class="row g-4">
    {{-- Pesanan Terbaru --}}
    <div class="col-lg-7">
        <div class="card card-bayeng">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fas fa-clock-rotate-left me-2"></i>Pesanan Terbaru</h5>
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm btn-primary-admin">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pesanan_terbaru as $pesanan)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="fw-semibold text-decoration-none">
                                            {{ $pesanan->no_pesanan }}
                                        </a>
                                    </td>
                                    <td>{{ $pesanan->pelanggan->nama ?? '-' }}</td>
                                    <td>{!! $pesanan->status_badge !!}</td>
                                    <td class="text-end">{{ format_rupiah($pesanan->grand_total) }}</td>
                                    <td class="text-muted small">{{ $pesanan->created_at->format('d M H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada pesanan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Stok Menipis --}}
    <div class="col-lg-5">
        <div class="card card-bayeng">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-triangle-exclamation me-2 text-warning"></i>Stok Menipis</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse ($stok_menipis as $helm)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <span>{{ $helm->nama_helm }}</span>
                            <span class="badge bg-danger rounded-pill">{{ $helm->stok }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Semua stok aman</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart_labels) !!},
            datasets: [{
                label: 'Penjualan (Rp)',
                data: {!! json_encode($chart_values) !!},
                backgroundColor: 'rgba(37, 99, 235, 0.7)',
                borderColor: '#2563eb',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return 'Rp ' + ctx.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(val) {
                            return 'Rp ' + (val / 1000000).toFixed(0) + ' jt';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
