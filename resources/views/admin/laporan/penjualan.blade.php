@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="space-y-4 max-w-6xl mx-auto">

    @php
        $grandTotal = 0;
        $grandPesanan = 0;
        foreach ($report_data ?? [] as $row) {
            $grandTotal += $row['total_omzet'] ?? 0;
            $grandPesanan += $row['total_pesanan'] ?? 0;
        }
        $avgOrder = $grandPesanan > 0 ? $grandTotal / $grandPesanan : 0;
    @endphp

    {{-- 1. Header Minimalist --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Laporan Penjualan
        </h1>

        {{-- Export Action Buttons --}}
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" 
               target="_blank"
               class="h-8 sm:h-9 px-3 rounded-lg border border-rose-200 bg-white hover:bg-rose-50 text-rose-700 text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-file-pdf text-rose-600 text-xs"></i>
                <span>PDF</span>
            </a>

            <a href="{{ route('admin.laporan.export-csv', request()->query()) }}" 
               class="h-8 sm:h-9 px-3 rounded-lg border border-emerald-200 bg-white hover:bg-emerald-50 text-emerald-700 text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-file-excel text-emerald-600 text-xs"></i>
                <span>CSV</span>
            </a>
        </div>
    </div>

    {{-- 2. Filter Bar --}}
    <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.laporan.penjualan') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 text-xs">
            <div class="w-full sm:w-48 space-y-1">
                <label class="block font-semibold text-slate-700">Jenis Laporan</label>
                <select name="jenis_laporan" class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                    <option value="bulanan" {{ request('jenis_laporan', $jenis_laporan ?? 'bulanan') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ request('jenis_laporan', $jenis_laporan ?? 'bulanan') === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            <div class="w-full sm:w-40 space-y-1">
                <label class="block font-semibold text-slate-700">Tahun</label>
                <select name="tahun" class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                    @foreach ($available_years as $year)
                        <option value="{{ $year }}" {{ (string) request('tahun', $tahun ?? date('Y')) === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" 
                    class="h-9 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-filter text-[10px]"></i>
                <span>Terapkan</span>
            </button>
        </form>
    </div>

    {{-- 3. Compact KPI Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
        <div class="p-3 sm:p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 block">
                Total Omzet
            </span>
            <p class="text-sm sm:text-lg font-bold text-slate-900 mt-0.5 truncate font-mono">
                Rp {{ number_format($grandTotal, 0, ',', '.') }}
            </p>
        </div>

        <div class="p-3 sm:p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 block">
                Total Transaksi
            </span>
            <p class="text-sm sm:text-lg font-bold text-slate-900 mt-0.5 font-mono">
                {{ number_format($grandPesanan, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">selesai</span>
            </p>
        </div>

        <div class="col-span-2 sm:col-span-1 p-3 sm:p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 block">
                Rata-rata Transaksi
            </span>
            <p class="text-sm sm:text-lg font-bold text-blue-600 mt-0.5 font-mono">
                Rp {{ number_format($avgOrder, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- 4. Chart Card --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-blue-600 text-xs"></i>
                <h2 class="text-xs sm:text-sm font-bold text-slate-900">
                    Grafik Tren Penjualan {{ ($jenis_laporan ?? 'bulanan') === 'bulanan' ? 'Tahun ' . ($tahun ?? date('Y')) : 'Tahunan' }}
                </h2>
            </div>
            <span class="text-[11px] text-slate-400 font-mono">Satuan: Rupiah</span>
        </div>
        <div class="relative w-full h-64 sm:h-72">
            <canvas id="reportChart"></canvas>
        </div>
    </div>

    {{-- 5. Data Table --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-3 sm:p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-table-list text-slate-500 text-xs"></i>
                <span>Rincian Data Penjualan</span>
            </h2>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                {{ count($report_data ?? []) }} Periode
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4">{{ request('jenis_laporan', $jenis_laporan ?? 'bulanan') === 'tahunan' ? 'Tahun' : 'Bulan' }}</th>
                        <th class="py-2.5 px-4 text-center">Total Pesanan</th>
                        <th class="py-2.5 px-4 text-right">Total Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report_data ?? [] as $row)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-4 font-semibold text-slate-900">
                                {{ $row['label'] ?? $row['periode'] ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-center font-mono font-medium text-slate-800">
                                {{ number_format($row['total_pesanan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($row['total_omzet'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-2xl text-slate-300 block mb-1"></i>
                                Belum ada data penjualan pada periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (!empty($report_data) && count($report_data) > 0)
                    <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-slate-900">
                        <tr>
                            <td class="py-3 px-4 uppercase tracking-wider text-[11px]">Total Keseluruhan</td>
                            <td class="py-3 px-4 text-center font-mono text-xs">{{ number_format($grandPesanan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-xs text-blue-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
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
    (function() {
        const canvas = document.getElementById('reportChart');
        if (!canvas) return;

        const existingChart = Chart.getChart(canvas);
        if (existingChart) {
            existingChart.destroy();
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chart_labels ?? []) !!},
                datasets: [{
                    label: 'Omzet',
                    data: {!! json_encode($chart_data ?? []) !!},
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    hoverBackgroundColor: '#1d4ed8',
                    borderRadius: 6,
                    maxBarThickness: 38,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Omzet: Rp ' + Number(context.raw || 0).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 10 },
                            color: '#64748b',
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
