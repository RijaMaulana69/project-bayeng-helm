@extends('layouts.admin')

@section('title', 'Transaksi')
@section('page-title', 'Transaksi')

@section('content')
<div class="space-y-4 max-w-6xl mx-auto">

    @php
        $currentStatus = request('status', 'all');
        $currentMetode = request('metode_bayar', 'all');
        $searchQuery   = request('search', '');
        $hasFilters    = ($currentStatus && $currentStatus !== 'all') || ($currentMetode && $currentMetode !== 'all') || !empty($searchQuery);
    @endphp

    {{-- 1. Header & Live Clock / Shift Info --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base sm:text-lg font-bold text-slate-900">Transaksi Gerai</h2>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $statusCounts['all'] ?? 0 }} Total
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Manajemen Pesanan masuk, kasir serah terima pickup, dan rekap pendapatan operasional.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <div class="h-9 px-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold inline-flex items-center gap-1.5 shrink-0">
                <i class="fa-regular fa-clock text-slate-400 text-xs"></i>
                <span>{{ now()->translatedFormat('l, d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Bilah Cepat Validasi Kode Pickup (Fast Pickup Bar) --}}
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="POST" action="{{ route('admin.pickup.validasi') }}" class="flex items-center gap-2">
            @csrf
            <div class="relative flex-1">
                <i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text"
                       name="kode_pickup"
                       class="w-full h-11 pl-10 pr-4 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider transition-all placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400"
                       placeholder="Scan / ketik kode pickup pelanggan (cth: PKP-XXXXXX)..."
                       value="{{ old('kode_pickup') }}"
                       autocomplete="off"
                       required>
            </div>
            <button type="submit" 
                    class="h-11 px-4 sm:px-5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs sm:text-sm font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer shrink-0">
                <i class="fa-solid fa-qrcode text-xs sm:text-sm"></i>
                <span>Validasi Ambil</span>
            </button>
        </form>
        @error('kode_pickup')
            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- 3. Rekap Pendapatan Karyawan (Hari Ini & Bulan Ini) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        {{-- Card A: Hari Ini --}}
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pendapatan Hari Ini</h3>
                            <span class="text-[10px] text-slate-400">Shift & Transaksi Selesai</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        {{ $rekapPendapatan['hari_ini']['count'] }} Pesanan
                    </span>
                </div>

                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        {{ format_rupiah($rekapPendapatan['hari_ini']['total']) }}
                    </span>
                </div>

                {{-- Rincian Uang di Kasir (Tunai vs Online) --}}
                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-slate-100 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-0.5">
                            <span class="font-medium flex items-center gap-1">
                                <i class="fa-solid fa-money-bill-wave text-emerald-600 text-[10px]"></i>
                                <span>Tunai Toko</span>
                            </span>
                            <span class="text-[10px] font-bold text-slate-600">{{ $rekapPendapatan['hari_ini']['tunai_count'] }}x</span>
                        </div>
                        <p class="font-bold text-slate-900 text-xs sm:text-sm">
                            {{ format_rupiah($rekapPendapatan['hari_ini']['tunai']) }}
                        </p>
                        <span class="text-[9px] text-slate-400 block mt-0.5">Fisik laci kasir</span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-0.5">
                            <span class="font-medium flex items-center gap-1">
                                <i class="fa-solid fa-qrcode text-blue-600 text-[10px]"></i>
                                <span>Online / QRIS</span>
                            </span>
                            <span class="text-[10px] font-bold text-slate-600">{{ $rekapPendapatan['hari_ini']['online_count'] }}x</span>
                        </div>
                        <p class="font-bold text-slate-900 text-xs sm:text-sm">
                            {{ format_rupiah($rekapPendapatan['hari_ini']['online']) }}
                        </p>
                        <span class="text-[9px] text-slate-400 block mt-0.5">Transfer & QRIS</span>
                    </div>
                </div>
            </div>
            
            <p class="text-[10px] text-slate-400 mt-2.5 flex items-center gap-1">
                <i class="fa-solid fa-circle-info text-[9px] text-slate-400"></i>
                <span>Cocokkan nominal Tunai Toko dengan uang fisik di laci kasir saat tutup shift.</span>
            </p>
        </div>

        {{-- Card B: Bulan Ini --}}
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pendapatan Bulan Ini</h3>
                            <span class="text-[10px] text-slate-400">{{ now()->translatedFormat('F Y') }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                        {{ $rekapPendapatan['bulan_ini']['count'] }} Pesanan
                    </span>
                </div>

                <div class="mt-3">
                    <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        {{ format_rupiah($rekapPendapatan['bulan_ini']['total']) }}
                    </span>
                </div>

                {{-- Rincian Akumulasi Bulanan --}}
                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-slate-100 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-0.5">
                            <span class="font-medium flex items-center gap-1">
                                <i class="fa-solid fa-money-bill-wave text-emerald-600 text-[10px]"></i>
                                <span>Akumulasi Tunai</span>
                            </span>
                        </div>
                        <p class="font-bold text-slate-900 text-xs sm:text-sm">
                            {{ format_rupiah($rekapPendapatan['bulan_ini']['tunai']) }}
                        </p>
                        <span class="text-[9px] text-slate-400 block mt-0.5">Penerimaan toko</span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-0.5">
                            <span class="font-medium flex items-center gap-1">
                                <i class="fa-solid fa-qrcode text-blue-600 text-[10px]"></i>
                                <span>Akumulasi Online</span>
                            </span>
                        </div>
                        <p class="font-bold text-slate-900 text-xs sm:text-sm">
                            {{ format_rupiah($rekapPendapatan['bulan_ini']['online']) }}
                        </p>
                        <span class="text-[9px] text-slate-400 block mt-0.5">Transfer & QRIS</span>
                    </div>
                </div>
            </div>

            <p class="text-[10px] text-slate-400 mt-2.5 flex items-center gap-1">
                <i class="fa-solid fa-chart-line text-[9px] text-slate-400"></i>
                <span>Total kinerja transaksi gerai berjalan bulan ini.</span>
            </p>
        </div>
    </div>

    {{-- 4. Status Quick Tabs (Scrollable on Mobile) --}}
    <div class="overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex items-center gap-1.5 min-w-max">
            {{-- Tab: Semua --}}
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 {{ ($currentStatus === 'all' || !$currentStatus) ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80' }}">
                <span>Semua</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ ($currentStatus === 'all' || !$currentStatus) ? 'bg-slate-800 text-slate-300' : 'bg-slate-100 text-slate-600' }}">
                    {{ $statusCounts['all'] ?? 0 }}
                </span>
            </a>

            {{-- Tab: Siap Ambil (Ready & Unpaid Pickup) --}}
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'ready'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 {{ $currentStatus === 'ready' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80' }}">
                <i class="fa-solid fa-bell text-[11px]"></i>
                <span>Siap Ambil</span>
                @if(($statusCounts['ready'] ?? 0) > 0)
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ $currentStatus === 'ready' ? 'bg-emerald-700 text-emerald-100' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $statusCounts['ready'] }}
                    </span>
                @endif
            </a>

            {{-- Tab: Perlu Verifikasi (Pending) --}}
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80' }}">
                <span class="w-2 h-2 rounded-full {{ ($statusCounts['pending'] ?? 0) > 0 ? 'bg-amber-500 animate-pulse' : 'bg-slate-300' }}"></span>
                <span>Perlu Verifikasi</span>
                @if(($statusCounts['pending'] ?? 0) > 0)
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ $currentStatus === 'pending' ? 'bg-amber-700 text-amber-100' : 'bg-amber-100 text-amber-700' }}">
                        {{ $statusCounts['pending'] }}
                    </span>
                @endif
            </a>

            {{-- Tab: Sudah Bayar (Paid) --}}
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'paid'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 {{ $currentStatus === 'paid' ? 'bg-blue-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80' }}">
                <span>Sudah Bayar</span>
                @if(($statusCounts['paid'] ?? 0) > 0)
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ $currentStatus === 'paid' ? 'bg-blue-700 text-blue-100' : 'bg-blue-100 text-blue-700' }}">
                        {{ $statusCounts['paid'] }}
                    </span>
                @endif
            </a>

            {{-- Tab: Disiapkan (Processing) --}}
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'processing'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 {{ $currentStatus === 'processing' ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80' }}">
                <span>Sedang Disiapkan</span>
                @if(($statusCounts['processing'] ?? 0) > 0)
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ $currentStatus === 'processing' ? 'bg-indigo-700 text-indigo-100' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $statusCounts['processing'] }}
                    </span>
                @endif
            </a>

            {{-- Tab: Selesai (Completed - Riwayat) --}}
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'completed'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 {{ $currentStatus === 'completed' ? 'bg-slate-700 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80' }}">
                <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                <span>Riwayat Selesai</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ $currentStatus === 'completed' ? 'bg-slate-800 text-slate-300' : 'bg-slate-100 text-slate-600' }}">
                    {{ $statusCounts['completed'] ?? 0 }}
                </span>
            </a>

            {{-- Tab: Dibatalkan (Cancelled) --}}
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'cancelled'])) }}"
               class="px-3 py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1.5 {{ $currentStatus === 'cancelled' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80' }}">
                <span>Dibatalkan</span>
                @if(($statusCounts['cancelled'] ?? 0) > 0)
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ $currentStatus === 'cancelled' ? 'bg-rose-700 text-rose-100' : 'bg-rose-100 text-rose-700' }}">
                        {{ $statusCounts['cancelled'] }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    {{-- 5. Search & Filter Bar --}}
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.pesanan.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            {{-- Keep current status if set --}}
            @if($currentStatus && $currentStatus !== 'all')
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif

            {{-- Search Bar --}}
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $searchQuery }}" 
                       placeholder="Cari no. pesanan, nama pembeli, kode pickup..." 
                       class="w-full h-10 pl-9 pr-4 bg-slate-50 border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>

            {{-- Metode Bayar Filter --}}
            <div class="shrink-0 w-full sm:w-44">
                <select name="metode_bayar" 
                        onchange="this.form.submit()" 
                        class="w-full h-10 px-3 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer transition-all">
                    <option value="all" {{ ($currentMetode === 'all' || !$currentMetode) ? 'selected' : '' }}>Semua Pembayaran</option>
                    <option value="tunai" {{ $currentMetode === 'tunai' ? 'selected' : '' }}>Bayar di Toko (Tunai)</option>
                    <option value="transfer" {{ $currentMetode === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ $currentMetode === 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>

            {{-- Search & Reset Buttons --}}
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" 
                        class="flex-1 sm:flex-initial h-10 px-4 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-xs font-semibold rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Cari</span>
                </button>

                @if($hasFilters)
                    <a href="{{ route('admin.pesanan.index') }}" 
                       class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-xs font-medium rounded-xl transition-colors inline-flex items-center justify-center gap-1.5 shrink-0"
                       title="Reset Filter">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span class="sm:hidden">Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 6. Order List (Mobile Cards & Desktop Table) --}}
    @if ($pesanans->count() > 0)

        {{-- A. MOBILE VIEW (Cards Stack) --}}
        <div class="md:hidden space-y-3">
            @foreach ($pesanans as $pesanan)
                @php
                    $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum');
                    $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
                    $firstDetail   = $pesanan->details->first();
                    $extraItems    = $pesanan->details->count() - 1;

                    $statusBadge = match($pesanan->status) {
                        'pending'       => ['label' => 'Perlu Verifikasi', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/80', 'icon' => 'fa-regular fa-clock'],
                        'unpaid_pickup' => ['label' => 'Bayar di Toko',   'class' => 'bg-sky-50 text-sky-700 border-sky-200/80',       'icon' => 'fa-solid fa-store'],
                        'paid'          => ['label' => 'Sudah Bayar',     'class' => 'bg-blue-50 text-blue-700 border-blue-200/80',     'icon' => 'fa-regular fa-circle-check'],
                        'processing'    => ['label' => 'Sedang Disiapkan', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80', 'icon' => 'fa-solid fa-box'],
                        'ready'         => ['label' => 'Siap Ambil',      'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80', 'icon' => 'fa-solid fa-bell'],
                        'completed'     => ['label' => 'Selesai',         'class' => 'bg-slate-100 text-slate-700 border-slate-200',   'icon' => 'fa-solid fa-check-double'],
                        'cancelled'     => ['label' => 'Dibatalkan',       'class' => 'bg-rose-50 text-rose-700 border-rose-200/80',     'icon' => 'fa-solid fa-ban'],
                        default         => ['label' => $pesanan->status_label, 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'icon' => 'fa-solid fa-circle-info'],
                    };

                    $metodeLabel = match($pesanan->metode_bayar) {
                        'tunai'    => 'Bayar di Toko',
                        'transfer' => 'Transfer Bank',
                        'qris'     => 'QRIS',
                        default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 space-y-3">
                    {{-- Row 1: No. Pesanan & Status Badge --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-slate-900 block truncate">
                                #{{ $pesanan->no_pesanan }}
                            </span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">
                                {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold border shrink-0 {{ $statusBadge['class'] }}">
                            <i class="{{ $statusBadge['icon'] }} text-[10px]"></i>
                            <span>{{ $statusBadge['label'] }}</span>
                        </span>
                    </div>

                    {{-- Row 2: Customer Name, Phone & Pickup Code --}}
                    <div class="flex items-center justify-between gap-2 text-xs pt-0.5">
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 truncate">
                                {{ $customerName }}
                            </p>
                            @if($customerPhone)
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                    {{ $customerPhone }}
                                </p>
                            @endif
                        </div>

                        @if($pesanan->kode_pickup)
                            <div class="shrink-0 text-right">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200 font-mono">
                                    <i class="fa-solid fa-qrcode text-[10px] text-slate-400"></i>
                                    <span>{{ $pesanan->kode_pickup }}</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Row 3: Items Summary --}}
                    @if($firstDetail)
                        @php
                            $helmName = $firstDetail->helm->nama_helm ?? ($firstDetail->nama_helm ?? 'Helm');
                            $variantParts = array_filter([$firstDetail->warna, $firstDetail->ukuran]);
                            $variantText = implode(', ', $variantParts);
                        @endphp
                        <div class="bg-slate-50/70 rounded-xl p-2.5 text-xs text-slate-600 border border-slate-100">
                            <p class="truncate font-medium text-slate-800">
                                <span class="font-bold text-slate-900">{{ $firstDetail->qty ?? 1 }}x</span> {{ $helmName }}
                                @if($variantText)
                                    <span class="text-[11px] text-slate-500 font-normal">({{ $variantText }})</span>
                                @endif
                            </p>
                            @if($extraItems > 0)
                                <p class="text-[11px] text-blue-600 font-medium mt-0.5">
                                    +{{ $extraItems }} item lainnya
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- Row 4: Total & Action Button --}}
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 block leading-tight">{{ $metodeLabel }}</span>
                            <span class="text-sm font-bold text-slate-900">
                                {{ format_rupiah($pesanan->grand_total) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if(in_array($pesanan->status, ['ready', 'unpaid_pickup']) && $pesanan->kode_pickup)
                                <a href="{{ route('admin.pickup.validasi', ['kode' => $pesanan->kode_pickup]) }}" 
                                   class="h-9 px-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer shadow-2xs">
                                    <i class="fa-solid fa-qrcode text-xs"></i>
                                    <span>Serahkan</span>
                                </a>
                            @endif

                            <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                               class="h-9 px-3.5 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer shadow-2xs">
                                <span>Detail</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- B. DESKTOP VIEW (Modern Responsive Table) --}}
        <div class="hidden md:block bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[11px] font-semibold">
                            <th class="py-3 px-4">Pesanan</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Item Helm</th>
                            <th class="py-3 px-4">Metode / Pickup</th>
                            <th class="py-3 px-4 text-right">Total</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pesanans as $pesanan)
                            @php
                                $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum');
                                $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
                                $firstDetail   = $pesanan->details->first();
                                $extraItems    = $pesanan->details->count() - 1;

                                $statusBadge = match($pesanan->status) {
                                    'pending'       => ['label' => 'Perlu Verifikasi', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/80', 'icon' => 'fa-regular fa-clock'],
                                    'unpaid_pickup' => ['label' => 'Bayar di Toko',   'class' => 'bg-sky-50 text-sky-700 border-sky-200/80',       'icon' => 'fa-solid fa-store'],
                                    'paid'          => ['label' => 'Sudah Bayar',     'class' => 'bg-blue-50 text-blue-700 border-blue-200/80',     'icon' => 'fa-regular fa-circle-check'],
                                    'processing'    => ['label' => 'Sedang Disiapkan', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80', 'icon' => 'fa-solid fa-box'],
                                    'ready'         => ['label' => 'Siap Ambil',      'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80', 'icon' => 'fa-solid fa-bell'],
                                    'completed'     => ['label' => 'Selesai',         'class' => 'bg-slate-100 text-slate-700 border-slate-200',   'icon' => 'fa-solid fa-check-double'],
                                    'cancelled'     => ['label' => 'Dibatalkan',       'class' => 'bg-rose-50 text-rose-700 border-rose-200/80',     'icon' => 'fa-solid fa-ban'],
                                    default         => ['label' => $pesanan->status_label, 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'icon' => 'fa-solid fa-circle-info'],
                                };

                                $metodeLabel = match($pesanan->metode_bayar) {
                                    'tunai'    => 'Bayar di Toko',
                                    'transfer' => 'Transfer Bank',
                                    'qris'     => 'QRIS',
                                    default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                {{-- 1. No. Pesanan & Waktu --}}
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                                       class="font-bold text-slate-900 hover:text-blue-600 transition-colors block">
                                        #{{ $pesanan->no_pesanan }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 mt-0.5 block">
                                        {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                </td>

                                {{-- 2. Pelanggan --}}
                                <td class="py-3.5 px-4 max-w-[180px]">
                                    <p class="font-semibold text-slate-800 truncate">
                                        {{ $customerName }}
                                    </p>
                                    @if($customerPhone)
                                        <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                            {{ $customerPhone }}
                                        </p>
                                    @endif
                                </td>

                                {{-- 3. Item Helm --}}
                                <td class="py-3.5 px-4 max-w-[220px]">
                                    @if($firstDetail)
                                        @php
                                            $helmName = $firstDetail->helm->nama_helm ?? ($firstDetail->nama_helm ?? 'Helm');
                                            $variantParts = array_filter([$firstDetail->warna, $firstDetail->ukuran]);
                                            $variantText = implode(', ', $variantParts);
                                        @endphp
                                        <p class="font-medium text-slate-800 truncate">
                                            <span class="font-bold text-slate-900">{{ $firstDetail->qty ?? 1 }}x</span> {{ $helmName }}
                                        </p>
                                        @if($variantText)
                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                {{ $variantText }}
                                            </p>
                                        @endif
                                        @if($extraItems > 0)
                                            <span class="text-[10px] text-blue-600 font-semibold block mt-0.5">
                                                +{{ $extraItems }} item lainnya
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                {{-- 4. Metode & Pickup --}}
                                <td class="py-3.5 px-4">
                                    <span class="text-xs font-medium text-slate-700 block">
                                        {{ $metodeLabel }}
                                    </span>
                                    @if($pesanan->kode_pickup)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 mt-1 rounded bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200 font-mono">
                                            <i class="fa-solid fa-qrcode text-[9px] text-slate-400"></i>
                                            <span>{{ $pesanan->kode_pickup }}</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- 5. Total --}}
                                <td class="py-3.5 px-4 text-right">
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 block">
                                        {{ format_rupiah($pesanan->grand_total) }}
                                    </span>
                                </td>

                                {{-- 6. Status --}}
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold border {{ $statusBadge['class'] }}">
                                        <i class="{{ $statusBadge['icon'] }} text-[10px]"></i>
                                        <span>{{ $statusBadge['label'] }}</span>
                                    </span>
                                </td>

                                {{-- 7. Aksi --}}
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if(in_array($pesanan->status, ['ready', 'unpaid_pickup']) && $pesanan->kode_pickup)
                                            <a href="{{ route('admin.pickup.validasi', ['kode' => $pesanan->kode_pickup]) }}" 
                                               class="h-8 px-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs inline-flex items-center gap-1 transition-colors border border-emerald-200/80"
                                               title="Validasi Serah Terima">
                                                <i class="fa-solid fa-qrcode text-[10px]"></i>
                                                <span>Serahkan</span>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                                           class="h-8 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 font-semibold text-xs inline-flex items-center gap-1.5 transition-colors">
                                            <span>Detail</span>
                                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($pesanans->hasPages())
            <div class="pt-2">
                {{ $pesanans->links() }}
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-8 sm:p-12 text-center shadow-2xs space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">
                    Tidak Ada Transaksi Ditemukan
                </h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    @if($hasFilters)
                        Tidak ada transaksi yang cocok dengan kata kunci atau filter status saat ini.
                    @else
                        Belum ada transaksi gerai atau pesanan yang masuk ke toko Anda.
                    @endif
                </p>
            </div>
            @if($hasFilters)
                <div class="pt-2">
                    <a href="{{ route('admin.pesanan.index') }}" 
                       class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Reset Semua Filter</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
