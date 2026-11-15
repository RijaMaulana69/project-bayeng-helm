@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="space-y-4 max-w-6xl mx-auto">

    @php
        $grandTotal = 0;
        $grandPesanan = 0;
        $grandModal = 0;
        $grandLaba = 0;
        $grandTunai = 0;
        $grandNonTunai = 0;

        foreach ($report_data ?? [] as $row) {
            $grandTotal += $row['total_omzet'] ?? 0;
            $grandPesanan += $row['total_pesanan'] ?? 0;
            $grandModal += $row['total_modal'] ?? 0;
            $grandLaba += $row['total_laba'] ?? 0;
            $grandTunai += $row['tunai_total'] ?? 0;
            $grandNonTunai += $row['non_tunai_total'] ?? 0;
        }

        $avgOrder = $grandPesanan > 0 ? $grandTotal / $grandPesanan : 0;
        $marginPercent = $grandTotal > 0 ? ($grandLaba / $grandTotal) * 100 : 0;

        $periodeLabel = match($jenis_laporan) {
            'harian'  => 'Bulan ' . ($nama_bulan[$bulan] ?? '') . ' ' . $tahun,
            'bulanan' => 'Tahun ' . $tahun,
            'tahunan' => 'Semua Periode Tahunan',
            default   => 'Periode Laporan'
        };
    @endphp

    {{-- 1. Header Minimalist & Aksi Export --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Laporan Penjualan
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Rekapitulasi omzet gerai, beban modal (HPP), dan estimasi laba bersih
            </p>
        </div>

        {{-- Export Action Buttons (Clean Slate Monochrome) --}}
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" 
               target="_blank"
               class="h-8.5 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-file-pdf text-rose-500 text-xs"></i>
                <span>Unduh PDF</span>
            </a>

            <a href="{{ route('admin.laporan.export-csv', request()->query()) }}" 
               class="h-8.5 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-file-excel text-emerald-600 text-xs"></i>
                <span>Unduh CSV</span>
            </a>
        </div>
    </div>

    {{-- 2. Filter Bar Cerdas (Dinamis & Anti-Ambigu) --}}
    <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-3">
        {{-- Granularity Tabs --}}
        <div class="flex items-center gap-1.5 text-xs overflow-x-auto pb-1 sm:pb-0">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Mode:</span>
            @php
                $modes = [
                    'harian'  => 'Harian (Per Tanggal)',
                    'bulanan' => 'Bulanan (12 Bulan)',
                    'tahunan' => 'Tahunan',
                ];
            @endphp
            @foreach ($modes as $key => $label)
                <a href="{{ route('admin.laporan.penjualan', ['jenis_laporan' => $key, 'tahun' => $tahun, 'bulan' => $bulan]) }}"
                   wire:navigate
                   class="px-2.5 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-all {{ $jenis_laporan === $key ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Dropdowns Filter Form --}}
        <form method="GET" action="{{ route('admin.laporan.penjualan') }}" class="grid grid-cols-2 sm:grid-cols-12 gap-2 text-xs pt-2.5 border-t border-slate-100 items-end">
            <input type="hidden" name="jenis_laporan" value="{{ $jenis_laporan }}">

            {{-- Dropdown Bulan (Only shown when Harian) --}}
            @if ($jenis_laporan === 'harian')
                <div class="col-span-1 sm:col-span-4 space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-600">Pilih Bulan</label>
                    <select name="bulan" class="w-full h-8.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                        @foreach ($nama_bulan as $num => $nama)
                            <option value="{{ $num }}" {{ (int) $bulan === (int) $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Dropdown Tahun (Shown for Harian and Bulanan, hidden for Tahunan) --}}
            @if ($jenis_laporan !== 'tahunan')
                <div class="col-span-1 {{ $jenis_laporan === 'harian' ? 'sm:col-span-4' : 'sm:col-span-6' }} space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-600">Pilih Tahun</label>
                    <select name="tahun" class="w-full h-8.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                        @foreach ($available_years as $year)
                            <option value="{{ $year }}" {{ (int) $tahun === (int) $year ? 'selected' : '' }}>
                                Tahun {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="col-span-2 sm:col-span-8 flex items-center text-xs text-slate-500 py-1.5">
                    <i class="fa-solid fa-circle-info text-blue-500 mr-2"></i>
                    <span>Mode Tahunan menyajikan rekapitulasi data penjualan historis seluruh tahun.</span>
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="{{ $jenis_laporan === 'harian' ? 'col-span-2 sm:col-span-4' : ($jenis_laporan === 'bulanan' ? 'col-span-2 sm:col-span-6' : 'col-span-2 sm:col-span-4') }}">
                <button type="submit" 
                        class="w-full h-8.5 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    {{-- 3. Ringkasan Kinerja Keuangan (4 KPI Cards Tanpa Rp Berulang & Tanpa Font-Mono) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        {{-- Card 1: Total Omzet --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Total Omzet</span>
                <i class="fa-solid fa-circle-check text-xs text-blue-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight tabular-nums">
                {{ number_format($grandTotal, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500">
                {{ number_format($grandPesanan, 0, ',', '.') }} transaksi selesai
            </p>
        </div>

        {{-- Card 2: Beban Modal Stok (HPP) --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Beban Modal (HPP)</span>
                <i class="fa-solid fa-boxes-stacked text-xs text-amber-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-700 tracking-tight tabular-nums">
                {{ number_format($grandModal, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500">
                Modal barang terjual
            </p>
        </div>

        {{-- Card 3: Estimasi Laba Bersih --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Estimasi Laba Kotor</span>
                <i class="fa-solid fa-chart-line text-xs text-emerald-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-emerald-600 tracking-tight tabular-nums">
                +{{ number_format($grandLaba, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500">
                Margin: {{ number_format($marginPercent, 1, ',', '.') }}% dari omzet
            </p>
        </div>

        {{-- Card 4: Arus Kas Tunai vs Non-Tunai --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Metode Bayar</span>
                <i class="fa-solid fa-wallet text-xs text-indigo-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-indigo-700 tracking-tight tabular-nums">
                {{ number_format($grandTunai, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500 truncate">
                Tunai · Non-Tunai: {{ number_format($grandNonTunai, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- 4. Grafik & Top Helm Terlaris --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        {{-- Chart Card --}}
        <div class="{{ $top_products->isNotEmpty() ? 'lg:col-span-8' : 'lg:col-span-12' }} bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-blue-600 text-xs"></i>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900">
                        Grafik Tren Penjualan ({{ $periodeLabel }})
                    </h2>
                </div>
                <span class="text-[11px] text-slate-400">Nominal Penjualan</span>
            </div>
            <div class="relative w-full h-60 sm:h-64">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        {{-- Top 5 Produk Helm Terlaris --}}
        @if ($top_products->isNotEmpty())
            <div class="lg:col-span-4 bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-fire text-amber-500 text-xs"></i>
                        <span>Helm Terlaris</span>
                    </h2>
                    <span class="text-[10px] text-slate-400 font-medium">Periode Ini</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach ($top_products as $index => $prod)
                        <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-5 h-5 rounded-full {{ $index === 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }} text-[10px] font-bold inline-flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <span class="font-medium text-slate-900 truncate">
                                    {{ $prod->nama_helm }}
                                </span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-slate-900 tabular-nums block">
                                    {{ $prod->total_qty }} Unit
                                </span>
                                <span class="text-[10px] text-slate-400 tabular-nums block">
                                    {{ number_format($prod->total_subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- 5. Data Table / Ledger Rincian --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-3 sm:p-4 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
            <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-table-list text-slate-400 text-xs"></i>
                <span>Rincian Data Penjualan</span>
            </h2>
            <span class="text-[11px] text-slate-500">
                {{ count($report_data ?? []) }} Baris Terdata
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-400 font-semibold text-[11px] border-b border-slate-100">
                    <tr>
                        <th class="py-2.5 px-4 font-medium">
                            {{ $jenis_laporan === 'tahunan' ? 'Tahun' : ($jenis_laporan === 'harian' ? 'Tanggal' : 'Bulan') }}
                        </th>
                        <th class="py-2.5 px-4 font-medium text-center">Pesanan</th>
                        <th class="py-2.5 px-4 font-medium text-right">Total Omzet</th>
                        <th class="py-2.5 px-4 font-medium text-right hidden sm:table-cell">Modal (HPP)</th>
                        <th class="py-2.5 px-4 font-medium text-right">Laba Kotor</th>
                        <th class="py-2.5 px-4 font-medium text-right hidden md:table-cell">Tunai / Online</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report_data ?? [] as $row)
                        @php
                            $omzet = (float) ($row['total_omzet'] ?? 0);
                            $modal = (float) ($row['total_modal'] ?? 0);
                            $laba = (float) ($row['total_laba'] ?? 0);
                            $pesanan = (int) ($row['total_pesanan'] ?? 0);
                            $tunai = (float) ($row['tunai_total'] ?? 0);
                            $nonTunai = (float) ($row['non_tunai_total'] ?? 0);
                            $isZero = $omzet <= 0 && $pesanan <= 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors {{ $isZero ? 'opacity-40' : '' }}">
                            <td class="py-2.5 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $row['label'] ?? '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-center tabular-nums text-slate-700">
                                {{ number_format($pesanan, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right tabular-nums font-bold text-slate-900 whitespace-nowrap">
                                {{ number_format($omzet, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right tabular-nums text-slate-500 whitespace-nowrap hidden sm:table-cell">
                                {{ number_format($modal, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right tabular-nums font-semibold {{ $laba > 0 ? 'text-emerald-600' : 'text-slate-400' }} whitespace-nowrap">
                                {{ $laba > 0 ? '+' : '' }}{{ number_format($laba, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right tabular-nums text-[11px] text-slate-500 whitespace-nowrap hidden md:table-cell">
                                <span>{{ number_format($tunai, 0, ',', '.') }}</span>
                                <span class="text-slate-300 mx-1">/</span>
                                <span>{{ number_format($nonTunai, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-2xl text-slate-300 block mb-1"></i>
                                Belum ada data penjualan pada periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (!empty($report_data) && count($report_data) > 0)
                    <tfoot class="bg-slate-50/80 font-bold border-t border-slate-200 text-slate-900 text-xs">
                        <tr>
                            <td class="py-3 px-4 uppercase tracking-wider text-[11px]">Total Keseluruhan</td>
                            <td class="py-3 px-4 text-center tabular-nums">{{ number_format($grandPesanan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums text-blue-700">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums text-slate-700 hidden sm:table-cell">{{ number_format($grandModal, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums text-emerald-700">+{{ number_format($grandLaba, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums text-[11px] text-slate-700 hidden md:table-cell">
                                {{ number_format($grandTunai, 0, ',', '.') }} / {{ number_format($grandNonTunai, 0, ',', '.') }}
                            </td>
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
                    borderRadius: 4,
                    maxBarThickness: 32,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 8,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return 'Omzet: ' + Number(context.raw || 0).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 9 },
                            color: '#64748b',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + ' rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
