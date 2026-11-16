@extends('layouts.admin')

@section('title', $isOwner ? 'Dashboard Bisnis' : 'Dashboard Kasir')
@section('page-title', $isOwner ? 'Ringkasan Toko' : 'Dashboard Kasir')

@section('content')

{{-- ========================================================================= --}}
{{-- A. TAMPILAN KHUSUS KARYAWAN (Kasir & Staf Gerai - Frontline Operations)   --}}
{{-- ========================================================================= --}}
@if(!$isOwner)
<div class="space-y-6">

    {{-- 1. Header Kasir & Validasi Cepat --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-3.5">
        <div class="flex items-center justify-between gap-3">
            <h1 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">
                Halo, {{ $user->nama_lengkap }}
            </h1>
            <div>
                <a href="{{ route('admin.karyawan.riwayat') }}" 
                   wire:navigate
                   class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-clock-rotate-left text-xs text-blue-600"></i>
                    <span>Riwayat Kasir</span>
                </a>
            </div>
        </div>

        {{-- Clean Voucher Pickup Input Bar --}}
        <div>
            <form action="{{ route('admin.pickup.validasi') }}" method="POST" class="flex items-center gap-2">
                @csrf
                <div class="relative flex-1">
                    <i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" 
                           id="pickup_input_karyawan"
                           name="kode_pickup" 
                           placeholder="Masukkan kode pickup" 
                           required 
                           autocomplete="off"
                           class="w-full h-11 pl-10 pr-4 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider transition-all placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400">
                </div>
                <button type="submit" 
                        class="h-11 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs sm:text-sm font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer shrink-0">
                    <span>Validasi</span>
                </button>
            </form>
        </div>
    </div>

    {{-- 2. Antrean Tindakan Kasir --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            {{-- 1. Siap Diambil di Gerai --}}
            <a href="{{ route('admin.pickup.index') }}" 
               wire:navigate
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-emerald-400 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">Ambil di Toko</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-box-open"></i>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl font-bold text-slate-900">{{ $pesanan_siap_ambil }}</span>
                        <span class="text-xs text-slate-500 font-normal">pelanggan</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-emerald-600">
                    <span>Layani Pelanggan</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- 2. Perlu Disiapkan di Rak --}}
            <a href="{{ route('admin.pesanan.index', ['status' => 'processing']) }}" 
               wire:navigate
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-sky-400 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">Perlu Disiapkan</span>
                        <span class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-dolly"></i>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl font-bold text-slate-900">{{ $pesanan_perlu_disiapkan }}</span>
                        <span class="text-xs text-slate-500 font-normal">paket</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-sky-600">
                    <span>Siapkan Barang</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- 3. Verifikasi Bukti Transfer --}}
            <a href="{{ route('admin.pesanan.index', ['status' => 'pending']) }}" 
               wire:navigate
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-amber-400 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">Pembayaran</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-file-invoice"></i>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl font-bold {{ $pesanan_pending_count > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                            {{ $pesanan_pending_count }}
                        </span>
                        <span class="text-xs text-slate-500 font-normal">menunggu cek</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-amber-600">
                    <span>Cek Pembayaran</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- 4. Stok Fisik Habis di Rak --}}
            <a href="{{ route('admin.produk.index', ['stok_filter' => 'habis']) }}" 
               wire:navigate
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-rose-400 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">Stok Habis</span>
                        <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl font-bold {{ $varian_habis_count > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $varian_habis_count }}
                        </span>
                        <span class="text-xs text-slate-500 font-normal">varian habis</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-rose-600">
                    <span>Input Stok Masuk</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>

    {{-- 3. Meja Kasir: Antrean Pelanggan & Cek Fisik Helm --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        {{-- Antrean Siap Ambil (7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden" x-data="{ pickupFilter: 'all' }">
            <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Antrean Ambil Pesanan</h3>
                </div>
                <a href="{{ route('admin.pickup.index') }}" wire:navigate class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            {{-- Segmented Control Tabs (Clean on Mobile) --}}
            <div class="px-3 sm:px-4 py-2 bg-slate-50/60 border-b border-slate-100">
                <div class="flex p-0.5 bg-slate-200/60 rounded-xl text-xs">
                    <button type="button" 
                            @click="pickupFilter = 'all'"
                            :class="pickupFilter === 'all' ? 'bg-white text-slate-900 font-semibold shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-medium'"
                            class="flex-1 py-1.5 text-center rounded-lg text-[11px] sm:text-xs transition-all cursor-pointer">
                        Semua ({{ $pesanan_siap_ambil }})
                    </button>
                    <button type="button" 
                            @click="pickupFilter = 'unpaid'"
                            :class="pickupFilter === 'unpaid' ? 'bg-white text-amber-700 font-semibold shadow-2xs' : 'text-slate-500 hover:text-amber-700 font-medium'"
                            class="flex-1 py-1.5 text-center rounded-lg text-[11px] sm:text-xs transition-all cursor-pointer">
                        <span class="sm:hidden">Bayar</span><span class="hidden sm:inline">Bayar di Toko</span> ({{ $pickup_unpaid_count }})
                    </button>
                    <button type="button" 
                            @click="pickupFilter = 'ready'"
                            :class="pickupFilter === 'ready' ? 'bg-white text-emerald-700 font-semibold shadow-2xs' : 'text-slate-500 hover:text-emerald-700 font-medium'"
                            class="flex-1 py-1.5 text-center rounded-lg text-[11px] sm:text-xs transition-all cursor-pointer">
                        <span class="sm:hidden">Lunas</span><span class="hidden sm:inline">Sudah Lunas</span> ({{ $pickup_ready_count }})
                    </button>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($pesanan_pickup as $pesanan)
                    @php
                        $firstDetail = $pesanan->details->first();
                        $itemsSummary = $firstDetail ? ($firstDetail->helm->nama_helm ?? 'Helm') : 'Item Helm';
                        $extraCount = $pesanan->details->count() - 1;
                        $isPaid = $pesanan->status === 'ready';
                    @endphp
                    <form action="{{ route('admin.pickup.validasi') }}" method="POST"
                          x-show="pickupFilter === 'all' || (pickupFilter === 'unpaid' && '{{ $pesanan->status }}' === 'unpaid_pickup') || (pickupFilter === 'ready' && '{{ $pesanan->status }}' === 'ready')">
                        @csrf
                        <input type="hidden" name="kode_pickup" value="{{ $pesanan->kode_pickup }}">
                        <button type="submit" class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors group cursor-pointer">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline gap-2">
                                    <span class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-blue-600 transition-colors shrink-0">
                                        {{ $pesanan->kode_pickup }}
                                    </span>
                                    <span class="text-xs text-slate-500 truncate">
                                        {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan') }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-700 truncate mt-1">
                                    {{ $itemsSummary }}{{ $extraCount > 0 ? ' +' . $extraCount . ' item' : '' }}
                                </p>
                            </div>

                            <div class="text-right shrink-0 flex items-center gap-2">
                                @if($isPaid)
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        Bayar di Toko
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-blue-600 transition-colors"></i>
                            </div>
                        </button>
                    </form>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        Tidak ada antrean pickup yang siap saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Stok Kritis di Rak Toko (5 Cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Peringatan Stok</h3>
                </div>
                <a href="{{ route('admin.produk.index', ['stok_filter' => 'kritis_habis']) }}" wire:navigate class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                    <span>Kelola Stok</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($stok_kritis_helm as $helm)
                    @php
                        $habisCount = $helm->varian_habis_count;
                        $menipisCount = $helm->varian_menipis_count;
                        $sampleVarians = $helm->varians->take(2)->map(fn($v) => $v->warna . ' (' . $v->ukuran . ')')->join(', ');
                        $extraVariansCount = $helm->varians->count() - 2;
                    @endphp
                    <a href="{{ route('admin.produk.index', ['search' => $helm->nama_helm]) }}" wire:navigate class="p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors group">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-semibold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                {{ $helm->nama_helm }}
                            </h4>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                {{ $sampleVarians }}{{ $extraVariansCount > 0 ? ' +' . $extraVariansCount . ' varian' : '' }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            @if($habisCount > 0)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                    {{ $habisCount }} Habis
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                    {{ $menipisCount }} Menipis
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        Seluruh stok varian di rak aman.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

{{-- ========================================================================= --}}
{{-- B. TAMPILAN KHUSUS OWNER (Pemilik Toko - Financial & Executive Control)    --}}
{{-- ========================================================================= --}}
@else
<script>
    (function() {
        window.dashboardChartData = {
            monthly: {
                labels: {!! json_encode($chart_labels) !!},
                omzet: {!! json_encode($chart_values) !!},
                orders: {!! json_encode($chart_orders) !!},
            },
            daily: {
                labels: {!! json_encode($daily_labels) !!},
                omzet: {!! json_encode($daily_values) !!},
                orders: {!! json_encode($daily_orders) !!},
            }
        };

        window.dashboardHub = function() {
            return {
                activeMetric: 'omzet',
                activeTimeframe: 'monthly',
                chartInstance: null,
                chartData: window.dashboardChartData,

                init() {
                    this.$nextTick(() => {
                        this.initChart();
                    });
                },

                destroy() {
                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                        this.chartInstance = null;
                    }
                },

                initChart() {
                    const canvas = document.getElementById('salesChart');
                    if (!canvas || typeof Chart === 'undefined') return;

                    const existingChart = Chart.getChart(canvas);
                    if (existingChart) {
                        existingChart.destroy();
                    }

                    const ctx = canvas.getContext('2d');
                    const initialDataset = this.getCurrentDataset();

                    this.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: initialDataset.labels,
                            datasets: [{
                                label: initialDataset.label,
                                data: initialDataset.data,
                                backgroundColor: initialDataset.bgColor,
                                hoverBackgroundColor: initialDataset.hoverColor,
                                borderRadius: 6,
                                maxBarThickness: 32,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 300,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    titleFont: { size: 12, weight: 'bold', family: "'Inter', sans-serif" },
                                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: (context) => {
                                            const val = context.raw || 0;
                                            if (this.activeMetric === 'omzet') {
                                                return 'Omzet: Rp ' + Number(val).toLocaleString('id-ID');
                                            }
                                            return 'Pesanan: ' + Number(val).toLocaleString('id-ID') + ' transaksi';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 11, family: "'Inter', sans-serif" },
                                        color: '#64748b'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.7)'
                                    },
                                    ticks: {
                                        font: { size: 11, family: "'Inter', sans-serif" },
                                        color: '#64748b',
                                        callback: (val) => {
                                            if (this.activeMetric === 'omzet') {
                                                if (val >= 1000000) return (val / 1000000).toFixed(0) + ' jt';
                                                if (val >= 1000) return (val / 1000).toFixed(0) + ' rb';
                                                return val;
                                            }
                                            return Number.isInteger(val) ? val : '';
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                getCurrentDataset() {
                    const tf = this.chartData[this.activeTimeframe] || this.chartData.monthly;
                    const isOmzet = this.activeMetric === 'omzet';

                    return {
                        labels: tf.labels,
                        label: isOmzet ? 'Omzet' : 'Jumlah Pesanan',
                        data: isOmzet ? tf.omzet : tf.orders,
                        bgColor: isOmzet ? 'rgba(37, 99, 235, 0.85)' : 'rgba(16, 185, 129, 0.85)',
                        hoverColor: isOmzet ? '#1d4ed8' : '#059669',
                    };
                },

                updateChart() {
                    if (!this.chartInstance) return;
                    const current = this.getCurrentDataset();

                    this.chartInstance.data.labels = current.labels;
                    this.chartInstance.data.datasets[0].label = current.label;
                    this.chartInstance.data.datasets[0].data = current.data;
                    this.chartInstance.data.datasets[0].backgroundColor = current.bgColor;
                    this.chartInstance.data.datasets[0].hoverBackgroundColor = current.hoverColor;
                    this.chartInstance.update();
                },

                switchMetric(metric) {
                    if (this.activeMetric === metric) return;
                    this.activeMetric = metric;
                    this.updateChart();
                },

                switchTimeframe(tf) {
                    if (this.activeTimeframe === tf) return;
                    this.activeTimeframe = tf;
                    this.updateChart();
                }
            };
        };

        // Safety cleanup before Livewire navigates away to prevent memory leaks or canvas reuse errors
        document.addEventListener('livewire:navigating', function cleanupChart() {
            const canvas = document.getElementById('salesChart');
            if (canvas && typeof Chart !== 'undefined') {
                const c = Chart.getChart(canvas);
                if (c) c.destroy();
            }
        }, { once: true });
    })();
</script>

<div class="space-y-6" x-data="dashboardHub()">

    {{-- 1. Merchant Header & Period Selector --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Halo, {{ $user->nama_lengkap }}
                    </h1>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                        Pemilik Toko
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            {{-- Quick Navigation to Owner Monitoring Features --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.owner.transaksi-karyawan.index') }}" 
                   wire:navigate
                   class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-cash-register text-xs text-blue-600"></i>
                    <span>Transaksi Gerai</span>
                </a>
                <a href="{{ route('admin.owner.barang-masuk.index') }}" 
                   wire:navigate
                   class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-dolly text-xs text-emerald-600"></i>
                    <span>Barang Masuk</span>
                </a>
            </div>
        </div>

        {{-- Dynamic Period Filter Tabs for Owner (Mobile-Friendly Horizontal Scroll) --}}
        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div class="flex items-center gap-2 text-xs text-slate-500 w-full sm:w-auto overflow-hidden">
                <span class="font-semibold text-slate-500 text-[11px] uppercase tracking-wider shrink-0">Filter Periode:</span>
                <div class="flex items-center gap-1 overflow-x-auto p-1 bg-slate-100/90 rounded-xl border border-slate-200/60 w-full sm:w-auto">
                    @php
                        $periods = [
                            'today'      => 'Hari Ini',
                            'yesterday'  => 'Kemarin',
                            '7days'      => '7 Hari',
                            '30days'     => '30 Hari',
                            'this_month' => 'Bulan Ini',
                            'all'        => 'Semua Waktu',
                        ];
                    @endphp
                    @foreach ($periods as $key => $label)
                        <a href="{{ route('admin.dashboard', ['period' => $key]) }}"
                           wire:navigate
                           class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-all shrink-0 {{ $selected_period === $key ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Penting Hari Ini (Action Required) --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                Penting Hari Ini
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            {{-- Antrean Siap Diambil di Toko --}}
            <a href="{{ route('admin.owner.transaksi-karyawan.index', ['status' => 'ready']) }}" 
               wire:navigate
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-emerald-400 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">Siap Diambil di Toko</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-box-open"></i>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl font-bold text-slate-900">{{ $pesanan_siap_ambil }}</span>
                        <span class="text-xs text-slate-500 font-normal">pesanan antre</span>
                    </div>
                    <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            {{ $pickup_ready_count }} Lunas
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">
                            {{ $pickup_unpaid_count }} Bayar di Kasir
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-medium text-emerald-600">
                    <span>Lihat Antrean</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Pesanan Masuk Hari Ini --}}
            <a href="{{ route('admin.owner.transaksi-karyawan.index', ['period' => 'today']) }}" 
               wire:navigate
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-blue-400 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">Pesanan Masuk Hari Ini</span>
                        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl font-bold text-slate-900">{{ $pesanan_hari_ini }}</span>
                        <span class="text-xs text-slate-500 font-normal">transaksi baru</span>
                    </div>
                    <div class="mt-2 flex items-center gap-1.5">
                        @if($pesanan_pending_count > 0)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                {{ $pesanan_pending_count }} Menunggu Verifikasi
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-50 text-slate-600 border border-slate-200">
                                Semua Pembayaran Terverifikasi
                            </span>
                        @endif
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-medium text-blue-600">
                    <span>Lihat Transaksi</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Stok Kritis (< 5) --}}
            <a href="{{ route('admin.owner.barang-masuk.index') }}" 
               wire:navigate
               class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-amber-400 hover:shadow-xs transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">Stok Kritis (&lt; 5)</span>
                        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </span>
                    </div>
                    <div class="mt-2.5 flex items-baseline gap-2">
                        <span class="text-2xl font-bold {{ $total_stok_kritis > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $total_stok_kritis }}
                        </span>
                        <span class="text-xs text-slate-500 font-normal">varian kritis</span>
                    </div>
                    <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                        @if($varian_habis_count > 0)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                {{ $varian_habis_count }} Habis
                            </span>
                        @endif
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                            {{ $varian_menipis_count }} Menipis
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-medium text-amber-600">
                    <span>Lihat Barang Masuk</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>
    </div>

    {{-- 3. Ringkasan Finansial Toko --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">
                    {{ $selected_period === 'today' ? 'Omzet Selesai Hari Ini' : 'Omzet Selesai' }}
                </span>
                <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded">
                    {{ $selected_period === 'today' ? 'Hari Ini' : $selected_period }}
                </span>
            </div>
            <p class="text-xl font-bold text-slate-900 mt-2 tracking-tight">
                {{ format_rupiah($period_omzet) }}
            </p>
            <div class="mt-1 flex items-center justify-between text-[11px] text-slate-400">
                <span>{{ $period_completed_pesanan }} transaksi selesai</span>
                <span>Bulan ini: {{ format_rupiah($omzet_bulan) }}</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Total Transaksi</span>
                <i class="fa-solid fa-receipt text-xs text-slate-400"></i>
            </div>
            <p class="text-xl font-bold text-slate-900 mt-2 tracking-tight">
                {{ $period_total_pesanan }} <span class="text-xs font-normal text-slate-500">pesanan</span>
            </p>
            <div class="mt-1 text-[11px] text-slate-400">
                <span>Total sepanjang waktu: {{ $total_pesanan }} transaksi</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Rata-rata Transaksi</span>
                <i class="fa-solid fa-chart-line text-xs text-slate-400"></i>
            </div>
            <p class="text-xl font-bold text-slate-900 mt-2 tracking-tight">
                {{ format_rupiah($period_aov) }}
            </p>
            <div class="mt-1 text-[11px] text-slate-400">
                <span>Rata-rata belanja per transaksi</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Katalog Produk</span>
                <i class="fa-solid fa-helmet-safety text-xs text-slate-400"></i>
            </div>
            <p class="text-xl font-bold text-slate-900 mt-2 tracking-tight">
                {{ $total_produk }} <span class="text-xs font-normal text-slate-500">model</span>
            </p>
            <div class="mt-1 text-[11px] text-slate-400">
                <span>{{ $total_varian }} varian aktif</span>
            </div>
        </div>
    </div>

    {{-- 4. Tren Penjualan Bulanan (Interactive Chart.js) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Tren Penjualan Bulanan</h3>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600" x-text="activeMetric === 'omzet' ? 'Omzet (Rp)' : 'Volume (Qty)'"></span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <div class="inline-flex rounded-xl bg-slate-100 p-0.5 border border-slate-200/80 text-xs">
                    <button type="button" 
                            @click="switchMetric('omzet')" 
                            :class="activeMetric === 'omzet' ? 'bg-white text-blue-600 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        Omzet (Rp)
                    </button>
                    <button type="button" 
                            @click="switchMetric('orders')" 
                            :class="activeMetric === 'orders' ? 'bg-white text-blue-600 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        Jumlah Pesanan
                    </button>
                </div>

                <div class="inline-flex rounded-xl bg-slate-100 p-0.5 border border-slate-200/80 text-xs">
                    <button type="button" 
                            @click="switchTimeframe('monthly')" 
                            :class="activeTimeframe === 'monthly' ? 'bg-white text-blue-600 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        Bulanan {{ date('Y') }}
                    </button>
                    <button type="button" 
                            @click="switchTimeframe('daily')" 
                            :class="activeTimeframe === 'daily' ? 'bg-white text-blue-600 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                        14 Hari Terakhir
                    </button>
                </div>

                <a href="{{ route('admin.owner.laporan.penjualan') }}" 
                   wire:navigate
                   class="h-7 px-2.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-medium inline-flex items-center gap-1 transition-colors">
                    <span>Laporan Detail</span>
                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                </a>
            </div>
        </div>

        <div class="h-64 sm:h-72 w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- 5. Dua Kolom: Pesanan Masuk Terbaru & Peringatan Stok Menipis --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden" x-data="{ orderFilter: 'all' }">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Pesanan Masuk Terbaru</h3>

                <div class="flex items-center gap-2">
                    <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-[11px]">
                        <button type="button" @click="orderFilter = 'all'" :class="orderFilter === 'all' ? 'bg-white text-slate-900 font-semibold shadow-2xs' : 'text-slate-600'" class="px-2 py-1 rounded transition-all">
                            Semua
                        </button>
                        <button type="button" @click="orderFilter = 'pickup'" :class="orderFilter === 'pickup' ? 'bg-white text-blue-600 font-semibold shadow-2xs' : 'text-slate-600'" class="px-2 py-1 rounded transition-all">
                            Antre Ambil
                        </button>
                        <button type="button" @click="orderFilter = 'completed'" :class="orderFilter === 'completed' ? 'bg-white text-emerald-600 font-semibold shadow-2xs' : 'text-slate-600'" class="px-2 py-1 rounded transition-all">
                            Selesai
                        </button>
                    </div>

                    <a href="{{ route('admin.owner.transaksi-karyawan.index') }}" wire:navigate class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                        <span>Semua</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($pesanan_terbaru as $pesanan)
                    @php
                        $statusBadgeClass = match($pesanan->status) {
                            'pending'       => 'bg-amber-50 text-amber-700 border-amber-200/80',
                            'unpaid_pickup' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                            'ready'         => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                            'paid', 'processing' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                            'completed'     => 'bg-slate-100 text-slate-700 border-slate-200',
                            'cancelled'     => 'bg-rose-50 text-rose-700 border-rose-200/80',
                            default         => 'bg-slate-50 text-slate-600 border-slate-200',
                        };

                        $statusBadgeLabel = match($pesanan->status) {
                            'pending'       => 'Menunggu Bayar',
                            'unpaid_pickup' => 'Siap Diambil (Bayar Kasir)',
                            'ready'         => 'Siap Diambil (Lunas)',
                            'paid'          => 'Sudah Dibayar',
                            'processing'    => 'Sedang Disiapkan',
                            'completed'     => 'Selesai',
                            'cancelled'     => 'Dibatalkan',
                            default         => $pesanan->status_label,
                        };

                        $isPickupStatus = in_array($pesanan->status, ['ready', 'unpaid_pickup', 'paid', 'processing']);
                        $firstDetail = $pesanan->details->first();
                        $itemsSummary = $firstDetail ? ($firstDetail->helm->nama_helm ?? 'Helm') : 'Item Helm';
                        $extraCount = $pesanan->details->count() - 1;
                    @endphp
                    <div class="p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors"
                         x-show="orderFilter === 'all' || (orderFilter === 'pickup' && {{ $isPickupStatus ? 'true' : 'false' }}) || (orderFilter === 'completed' && '{{ $pesanan->status }}' === 'completed')">
                        
                        <div class="min-w-0 space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" wire:navigate class="font-mono font-bold text-xs text-blue-600 hover:underline">
                                    #{{ $pesanan->no_pesanan }}
                                </a>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $statusBadgeClass }}">
                                    {{ $statusBadgeLabel }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-800 font-medium truncate">
                                {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum') }}
                                <span class="text-slate-400 font-normal">&bull; {{ $itemsSummary }}{{ $extraCount > 0 ? ' +' . $extraCount . ' item' : '' }}</span>
                            </p>

                            <p class="text-[11px] text-slate-400">
                                {{ $pesanan->created_at->format('d M Y, H:i') }} WIB &bull; Kode: <span class="font-mono font-medium text-slate-600">{{ $pesanan->kode_pickup }}</span>
                            </p>
                        </div>

                        <div class="text-right shrink-0 space-y-1">
                            <span class="text-xs sm:text-sm font-bold text-slate-900 block tracking-tight">
                                {{ format_rupiah($pesanan->grand_total) }}
                            </span>
                            <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" wire:navigate class="text-[11px] font-medium text-slate-400 hover:text-blue-600 transition-colors inline-flex items-center gap-1">
                                <span>Audit</span>
                                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        Belum ada pesanan masuk.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Peringatan Stok Menipis</h3>
                <a href="{{ route('admin.owner.barang-masuk.index') }}" wire:navigate class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                    <span>Log Masuk</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($stok_kritis_varian as $varian)
                    @php
                        $helm = $varian->helm;
                        $isZero = $varian->stok === 0;
                    @endphp
                    <div class="p-3 sm:p-3.5 flex items-center gap-3 hover:bg-slate-50/70 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                            @if($helm && $helm->gambar_url)
                                <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-helmet-safety text-xs"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-slate-900 truncate">
                                {{ $helm->nama_helm ?? 'Helm' }}
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                <span class="font-medium text-slate-700">{{ $varian->warna }}</span> &bull; Ukuran {{ $varian->ukuran }}
                            </p>
                        </div>

                        <div class="text-right shrink-0 flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isZero ? 'bg-rose-100 text-rose-700 border border-rose-300' : 'bg-amber-50 text-amber-700 border border-amber-200/80' }}">
                                {{ $isZero ? 'Habis (0)' : 'Sisa ' . $varian->stok }}
                            </span>
                            <a href="{{ route('admin.owner.barang-masuk.index', ['search' => $helm->nama_helm ?? '']) }}" 
                               wire:navigate
                               class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-600 flex items-center justify-center text-xs transition-colors"
                               title="Riwayat Masuk">
                                <i class="fa-solid fa-dolly text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 space-y-1">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg block mb-1"></i>
                        <p class="font-medium text-slate-700">Semua Stok Aman</p>
                        <p class="text-[11px]">Seluruh SKU varian memiliki stok &ge; 5 unit.</p>
                    </div>
                @endforelse
            </div>

            <div class="p-3 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Total {{ $varian_habis_count }} varian stok 0 &bull; {{ $varian_menipis_count }} menipis</span>
                <a href="{{ route('admin.owner.barang-masuk.index') }}" wire:navigate class="font-semibold text-blue-600 hover:underline">
                    Riwayat Barang Masuk &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
@endif

@endsection
