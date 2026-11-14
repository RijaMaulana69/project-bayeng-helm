@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Ringkasan Toko')

@section('content')
<div class="space-y-6" x-data="dashboardHub()">

    {{-- 1. Merchant Header & Quick Pickup Input --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            {{-- User greeting & store status --}}
            <div class="space-y-1">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Halo, {{ $user->nama_lengkap }}
                    </h1>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">
                        {{ $user->isOwner() ? 'Pemilik Toko' : 'Kasir & Staf' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Operasional Toko Aktif</span>
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    Operasional Toko &bull; {{ now()->translatedFormat('l, d F Y') }} &bull; Periode: <span class="font-medium text-slate-700">{{ $periode_label }}</span>
                </p>
            </div>

            {{-- Quick Pickup Code Validator (In-Dashboard Cashier Tool) --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5">
                <form action="{{ route('admin.pickup.validasi') }}" method="POST" class="flex items-center gap-1.5">
                    @csrf
                    <div class="relative">
                        <i class="fa-solid fa-qrcode absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               name="kode_pickup" 
                               placeholder="Scan / Kode Pickup (PKP-...)" 
                               required 
                               autocomplete="off"
                               class="w-52 sm:w-60 h-9 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider transition-all placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400">
                    </div>
                    <button type="submit" 
                            class="h-9 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer shrink-0">
                        <span>Cek</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.pickup.index') }}" 
                       class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 transition-colors shrink-0"
                       title="Buka Kasir Pickup">
                        <i class="fa-solid fa-store text-xs text-slate-500"></i>
                        <span class="hidden sm:inline">Kasir Pickup</span>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" 
                       class="h-9 px-3 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 transition-colors shrink-0"
                       title="Buka Toko Online">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400"></i>
                        <span class="hidden sm:inline">Lihat Toko</span>
                    </a>
                </div>
            </div>

        </div>

        {{-- Dynamic Period Filter Tabs --}}
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="font-medium text-slate-400 text-[11px] uppercase tracking-wider">Filter Periode:</span>
                <div class="inline-flex rounded-xl bg-slate-100/90 p-1 border border-slate-200/60">
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
                           class="px-2.5 py-1 rounded-lg text-xs font-medium transition-all {{ $selected_period === $key ? 'bg-white text-blue-600 font-semibold shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="text-[11px] text-slate-400 flex items-center gap-1">
                <i class="fa-solid fa-rotate text-[10px]"></i>
                <span>Data diperbarui otomatis</span>
            </div>
        </div>
    </div>

    {{-- 2. Penting Hari Ini (Action Required Cards) --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Penting Hari Ini
                </h2>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                    Prioritas Kasir
                </span>
            </div>
            <span class="text-[11px] text-slate-400 font-medium">Prioritas Kasir & Operasional</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            {{-- Antrean Siap Diambil di Toko (BOPIS) --}}
            <a href="{{ route('admin.pickup.index') }}" 
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
                    <span>Buka Kasir Pickup</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Pesanan Masuk Hari Ini & Pending --}}
            <a href="{{ route('admin.pesanan.index', ['status' => 'pending']) }}" 
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
                    <span>Lihat Semua Pesanan</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Stok Kritis (< 5) & Varian Habis --}}
            <a href="{{ route('admin.stok.index') }}" 
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
                        <span class="text-xs text-slate-500 font-normal">SKU varian perlu restok</span>
                    </div>
                    <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                        @if($varian_habis_count > 0)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                {{ $varian_habis_count }} Habis (0 unit)
                            </span>
                        @endif
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                            {{ $varian_menipis_count }} Menipis (1-3)
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-medium text-amber-600">
                    <span>Update Stok Masuk</span>
                    <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>
    </div>

    {{-- 3. Ringkasan Finansial & Katalog Toko (Dynamic Period KPIs) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        {{-- Omzet Selesai --}}
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Omzet Selesai Hari Ini</span>
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

        {{-- Total Pesanan Masuk Periode Ini --}}
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

        {{-- Rata-rata Nilai Pesanan (AOV) --}}
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Rata-rata Order (AOV)</span>
                <i class="fa-solid fa-chart-line text-xs text-slate-400"></i>
            </div>
            <p class="text-xl font-bold text-slate-900 mt-2 tracking-tight">
                {{ format_rupiah($period_aov) }}
            </p>
            <div class="mt-1 text-[11px] text-slate-400">
                <span>Nilai rata-rata per transaksi selesai</span>
            </div>
        </div>

        {{-- Total Katalog Helm & SKU --}}
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Katalog & SKU Aktif</span>
                <i class="fa-solid fa-helmet-safety text-xs text-slate-400"></i>
            </div>
            <p class="text-xl font-bold text-slate-900 mt-2 tracking-tight">
                {{ $total_produk }} <span class="text-xs font-normal text-slate-500">model</span>
            </p>
            <div class="mt-1 text-[11px] text-slate-400">
                <span>{{ $total_varian }} varian ukuran & warna terdaftar</span>
            </div>
        </div>
    </div>

    {{-- 4. Tren Penjualan Bulanan (Interactive Dynamic Chart.js) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Tren Penjualan Bulanan</h3>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600" x-text="activeMetric === 'omzet' ? 'Omzet (Rp)' : 'Volume (Qty)'"></span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Data transaksi selesai &bull; Klik tombol untuk beralih antara nilai Rupiah dan volume order
                </p>
            </div>

            {{-- Chart Controls: Switch Metric & Timeframe --}}
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Metric Switcher --}}
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

                {{-- Timeframe Switcher --}}
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

                @if(Auth::guard('web')->user()->isOwner())
                    <a href="{{ route('admin.owner.laporan.penjualan') }}" 
                       class="h-7 px-2.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-medium inline-flex items-center gap-1 transition-colors">
                        <span>Laporan Detail</span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Canvas --}}
        <div class="h-64 sm:h-72 w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- 5. Dua Kolom: Pesanan Masuk Terbaru & Peringatan Stok Menipis --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        {{-- Pesanan Masuk Terbaru (7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden" x-data="{ orderFilter: 'all' }">
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Pesanan Masuk Terbaru</h3>
                    <p class="text-[11px] text-slate-400">Daftar transaksi pelanggan terkini di gerai</p>
                </div>

                {{-- Status Filter Tabs --}}
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

                    <a href="{{ route('admin.pesanan.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
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
                                <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="font-mono font-bold text-xs text-blue-600 hover:underline">
                                    #{{ $pesanan->no_pesanan }}
                                </a>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $statusBadgeClass }}">
                                    {{ $statusBadgeLabel }}
                                </span>
                                @if($pesanan->metode_bayar === 'bayar_di_toko')
                                    <span class="text-[10px] text-slate-500 font-medium bg-slate-100 px-1.5 py-0.5 rounded">
                                        Tunai di Kasir
                                    </span>
                                @endif
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
                            @if(in_array($pesanan->status, ['ready', 'unpaid_pickup']))
                                <a href="{{ route('admin.pickup.index') }}" 
                                   class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold inline-flex items-center gap-1 shadow-2xs transition-colors">
                                    <i class="fa-solid fa-hand-holding-dollar text-[10px]"></i>
                                    <span>Serahkan</span>
                                </a>
                            @else
                                <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="text-[11px] font-medium text-slate-400 hover:text-blue-600 transition-colors inline-flex items-center gap-1">
                                    <span>Detail</span>
                                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        Belum ada pesanan masuk.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Peringatan Stok Menipis (5 Cols) - Variant SKU Level & Helm Level --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Peringatan Stok Menipis</h3>
                    <p class="text-[11px] text-slate-400">SKU varian kritis &lt; 5 unit di etalase</p>
                </div>
                <a href="{{ route('admin.stok.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
                    <span>Atur Stok</span>
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
                        {{-- Thumbnail --}}
                        <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                            @if($helm && $helm->gambar_url)
                                <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-helmet-safety text-xs"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-slate-900 truncate">
                                {{ $helm->nama_helm ?? 'Helm' }}
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                <span class="font-medium text-slate-700">{{ $varian->warna }}</span> &bull; Ukuran {{ $varian->ukuran }}
                            </p>
                        </div>

                        {{-- Stock Pill & Quick Add --}}
                        <div class="text-right shrink-0 flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isZero ? 'bg-rose-100 text-rose-700 border border-rose-300' : 'bg-amber-50 text-amber-700 border border-amber-200/80' }}">
                                {{ $isZero ? 'Habis (0)' : 'Sisa ' . $varian->stok }}
                            </span>
                            <a href="{{ route('admin.stok.index') }}" 
                               class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-600 flex items-center justify-center text-xs transition-colors"
                               title="Tambah Stok">
                                <i class="fa-solid fa-plus text-[10px]"></i>
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

            {{-- Footer Summary --}}
            <div class="p-3 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Total {{ $varian_habis_count }} varian stok 0 &bull; {{ $varian_menipis_count }} menipis</span>
                <a href="{{ route('admin.stok.index') }}" class="font-semibold text-blue-600 hover:underline">
                    Restok Cepat &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    function dashboardHub() {
        return {
            activeMetric: 'omzet',     // 'omzet' | 'orders'
            activeTimeframe: 'monthly', // 'monthly' | 'daily'
            chartInstance: null,

            // Raw Datasets from Server
            chartData: {
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
            },

            init() {
                this.$nextTick(() => {
                    this.initChart();
                });
            },

            initChart() {
                const canvas = document.getElementById('salesChart');
                if (!canvas) return;

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
                            duration: 400,
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
    }
</script>
@endpush
