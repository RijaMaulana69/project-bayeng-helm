@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
{{-- Filter Bar --}}
<div class="card card-bayeng mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.laporan.penjualan') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Jenis Laporan</label>
                <select name="jenis_laporan" class="form-select">
                    <option value="bulanan" {{ request('jenis_laporan', 'bulanan') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ request('jenis_laporan') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tahun</label>
                <select name="tahun" class="form-select">
                    @foreach ($available_years as $year)
                        <option value="{{ $year }}" {{ request('tahun', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-admin w-100">
                    <i class="fas fa-filter me-1"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="btn btn-outline-danger" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('admin.laporan.export-csv', request()->query()) }}" class="btn btn-outline-success">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Chart --}}
<div class="card card-bayeng mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Grafik Penjualan</h5>
    </div>
    <div class="card-body">
        <canvas id="reportChart" height="100"></canvas>
    </div>
</div>

{{-- Data Table --}}
<div class="card card-bayeng">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Data Laporan</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ request('jenis_laporan') == 'tahunan' ? 'Tahun' : 'Bulan' }}</th>
                        <th class="text-center">Total Pesanan</th>
                        <th class="text-end">Total Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; $grandPesanan = 0; @endphp
                    @forelse ($report_data ?? [] as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row['label'] ?? $row['periode'] ?? '-' }}</td>
                            <td class="text-center">{{ $row['total_pesanan'] }}</td>
                            <td class="text-end">{{ format_rupiah($row['total_omzet']) }}</td>
                        </tr>
                        @php $grandTotal += $row['total_omzet']; $grandPesanan += $row['total_pesanan']; @endphp
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
                @if (!empty($report_data) && count($report_data) > 0)
                    <tfoot>
                        <tr class="fw-bold table-light">
                            <td>Total</td>
                            <td class="text-center">{{ $grandPesanan }}</td>
                            <td class="text-end text-primary">{{ format_rupiah($grandTotal) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart_labels ?? []) !!},
            datasets: [{
                label: 'Omzet (Rp)',
                data: {!! json_encode($chart_data ?? []) !!},
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
