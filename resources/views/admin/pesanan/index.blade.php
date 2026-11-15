@extends('layouts.admin')

@section('title', 'Transaksi')
@section('page-title', 'Transaksi')

@section('content')
<div class="space-y-3.5 max-w-6xl mx-auto">

    @php
        $currentStatus = request('status', 'all');
        $currentMetode = request('metode_bayar', 'all');
        $searchQuery   = request('search', '');
        $hasFilters    = ($currentStatus && $currentStatus !== 'all') || ($currentMetode && $currentMetode !== 'all') || !empty($searchQuery);
    @endphp

    {{-- 1. Header Ringkas --}}
    <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <h2 class="text-base font-bold text-slate-900">Transaksi Gerai</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Manajemen Pesanan masuk, kasir serah terima pickup, dan rekap pendapatan operasional.
        </p>
    </div>

    {{-- 2. Bilah Cepat Validasi Kode Pickup --}}
    <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="POST" action="{{ route('admin.pickup.validasi') }}" class="flex items-center gap-2">
            @csrf
            <div class="relative flex-1">
                <i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text"
                       name="kode_pickup"
                       class="w-full h-10 pl-10 pr-4 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider transition-all placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400"
                       placeholder="Scan / ketik kode pickup pelanggan (cth: PKP-XXXXXX)..."
                       value="{{ old('kode_pickup') }}"
                       autocomplete="off"
                       required>
            </div>
            <button type="submit" 
                    class="h-10 px-4 sm:px-5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs sm:text-sm font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer shrink-0">
                <i class="fa-solid fa-qrcode text-xs sm:text-sm"></i>
                <span>Validasi Ambil</span>
            </button>
        </form>
        @error('kode_pickup')
            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- 3. Rekap Pendapatan Karyawan (Hari Ini & Bulan Ini) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        {{-- Card A: Hari Ini --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pendapatan Hari Ini</h3>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                    {{ $rekapPendapatan['hari_ini']['count'] }} Pesanan
                </span>
            </div>

            <div class="mt-1.5">
                <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    {{ format_rupiah($rekapPendapatan['hari_ini']['total']) }}
                </span>
            </div>

            {{-- Rincian Uang di Kasir (Tunai vs Online) --}}
            <div class="grid grid-cols-2 gap-2 mt-3 pt-2.5 border-t border-slate-100 text-xs">
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100/80">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mb-0.5">
                        <span class="font-medium text-slate-600">Tunai Toko</span>
                        <span class="text-[10px] text-slate-400">{{ $rekapPendapatan['hari_ini']['tunai_count'] }}x</span>
                    </div>
                    <p class="font-bold text-slate-800 text-xs sm:text-sm">
                        {{ format_rupiah($rekapPendapatan['hari_ini']['tunai']) }}
                    </p>
                </div>

                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100/80">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mb-0.5">
                        <span class="font-medium text-slate-600">Online / QRIS</span>
                        <span class="text-[10px] text-slate-400">{{ $rekapPendapatan['hari_ini']['online_count'] }}x</span>
                    </div>
                    <p class="font-bold text-slate-800 text-xs sm:text-sm">
                        {{ format_rupiah($rekapPendapatan['hari_ini']['online']) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Card B: Bulan Ini --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pendapatan Bulan Ini</h3>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                    {{ $rekapPendapatan['bulan_ini']['count'] }} Pesanan
                </span>
            </div>

            <div class="mt-1.5">
                <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    {{ format_rupiah($rekapPendapatan['bulan_ini']['total']) }}
                </span>
            </div>

            {{-- Rincian Akumulasi Bulanan --}}
            <div class="grid grid-cols-2 gap-2 mt-3 pt-2.5 border-t border-slate-100 text-xs">
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100/80">
                    <span class="font-medium text-slate-600 text-[11px] block mb-0.5">Akumulasi Tunai</span>
                    <p class="font-bold text-slate-800 text-xs sm:text-sm">
                        {{ format_rupiah($rekapPendapatan['bulan_ini']['tunai']) }}
                    </p>
                </div>

                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100/80">
                    <span class="font-medium text-slate-600 text-[11px] block mb-0.5">Akumulasi Online</span>
                    <p class="font-bold text-slate-800 text-xs sm:text-sm">
                        {{ format_rupiah($rekapPendapatan['bulan_ini']['online']) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Clean Minimalist Status Tabs --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
        @php
            $tabs = [
                ['id' => 'all',        'label' => 'Semua',            'count' => $statusCounts['all'] ?? 0],
                ['id' => 'ready',      'label' => 'Siap Ambil',       'count' => $statusCounts['ready'] ?? 0, 'dot' => 'bg-emerald-500'],
                ['id' => 'pending',    'label' => 'Perlu Verifikasi', 'count' => $statusCounts['pending'] ?? 0, 'dot' => 'bg-amber-500'],
                ['id' => 'paid',       'label' => 'Sudah Bayar',      'count' => $statusCounts['paid'] ?? 0],
                ['id' => 'processing', 'label' => 'Sedang Disiapkan', 'count' => $statusCounts['processing'] ?? 0],
                ['id' => 'completed',  'label' => 'Riwayat Selesai',  'count' => $statusCounts['completed'] ?? 0],
                ['id' => 'cancelled',  'label' => 'Dibatalkan',       'count' => $statusCounts['cancelled'] ?? 0],
            ];
        @endphp
        @foreach($tabs as $tab)
            @php
                $isActive = ($currentStatus === $tab['id']) || ($tab['id'] === 'all' && (!$currentStatus || $currentStatus === 'all'));
            @endphp
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => $tab['id']])) }}"
               class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $isActive ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                @if(isset($tab['dot']) && $tab['count'] > 0)
                    <span class="w-1.5 h-1.5 rounded-full {{ $tab['dot'] }}"></span>
                @endif
                <span>{{ $tab['label'] }}</span>
                <span class="text-[11px] {{ $isActive ? 'text-slate-300' : 'text-slate-400' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- 5. Search & Filter Bar --}}
    <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.pesanan.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            @if($currentStatus && $currentStatus !== 'all')
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif

            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $searchQuery }}" 
                       placeholder="Cari no. pesanan, nama pembeli, kode pickup..." 
                       class="w-full h-9 pl-8 pr-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div class="shrink-0 w-full sm:w-44">
                <select name="metode_bayar" 
                        onchange="this.form.submit()" 
                        class="w-full h-9 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer transition-all">
                    <option value="all" {{ ($currentMetode === 'all' || !$currentMetode) ? 'selected' : '' }}>Semua Pembayaran</option>
                    <option value="tunai" {{ $currentMetode === 'tunai' ? 'selected' : '' }}>Bayar di Toko (Tunai)</option>
                    <option value="transfer" {{ $currentMetode === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ $currentMetode === 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <button type="submit" 
                        class="flex-1 sm:flex-initial h-9 px-3.5 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Cari</span>
                </button>

                @if($hasFilters)
                    <a href="{{ route('admin.pesanan.index') }}" 
                       class="h-9 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-xs font-medium rounded-lg transition-colors inline-flex items-center justify-center gap-1 shrink-0"
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

        {{-- A. MOBILE VIEW (Clean Minimal Cards) --}}
        <div class="md:hidden space-y-2.5">
            @foreach ($pesanans as $pesanan)
                @php
                    $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum');
                    $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
                    $firstDetail   = $pesanan->details->first();
                    $extraItems    = $pesanan->details->count() - 1;

                    $statusBadge = match($pesanan->status) {
                        'pending'       => ['label' => 'Perlu Verifikasi', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/60', 'dot' => 'bg-amber-500'],
                        'unpaid_pickup' => ['label' => 'Siap Ambil (COD)', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'dot' => 'bg-emerald-500'],
                        'paid'          => ['label' => 'Sudah Bayar',     'class' => 'bg-blue-50 text-blue-700 border-blue-200/60',       'dot' => 'bg-blue-500'],
                        'processing'    => ['label' => 'Sedang Disiapkan', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/60',   'dot' => 'bg-indigo-500'],
                        'ready'         => ['label' => 'Siap Ambil',      'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'dot' => 'bg-emerald-500'],
                        'completed'     => ['label' => 'Selesai',         'class' => 'bg-slate-50 text-slate-600 border-slate-200/60',     'dot' => 'bg-slate-400'],
                        'cancelled'     => ['label' => 'Dibatalkan',       'class' => 'bg-rose-50 text-rose-700 border-rose-200/60',       'dot' => 'bg-rose-500'],
                        default         => ['label' => ucfirst($pesanan->status), 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'],
                    };

                    $metodeLabel = match($pesanan->metode_bayar) {
                        'tunai'    => 'Bayar di Toko',
                        'transfer' => 'Transfer Bank',
                        'qris'     => 'QRIS',
                        default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
                    };
                @endphp

                <div class="bg-white rounded-xl border border-slate-200/80 p-3 space-y-2">
                    {{-- Row 1: No. Pesanan, Waktu & Status Badge --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="font-bold text-xs text-slate-900 truncate">
                                #{{ $pesanan->no_pesanan }}
                            </span>
                            <span class="text-[10px] text-slate-400 shrink-0">
                                • {{ $pesanan->created_at->format('d M, H:i') }}
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border shrink-0 {{ $statusBadge['class'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                            <span>{{ $statusBadge['label'] }}</span>
                        </span>
                    </div>

                    {{-- Row 2: Customer, Phone & Pickup Code --}}
                    <div class="flex items-center justify-between gap-2 text-xs">
                        <div class="min-w-0">
                            <span class="font-medium text-slate-800 truncate block">
                                {{ $customerName }}
                            </span>
                            @if($customerPhone)
                                <span class="text-[11px] text-slate-400 truncate block">
                                    {{ $customerPhone }}
                                </span>
                            @endif
                        </div>

                        @if($pesanan->kode_pickup)
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-mono font-bold text-xs border border-slate-200/80 shrink-0">
                                {{ $pesanan->kode_pickup }}
                            </span>
                        @endif
                    </div>

                    {{-- Row 3: Items Summary --}}
                    @if($firstDetail)
                        @php
                            $helmName = $firstDetail->helm->nama_helm ?? ($firstDetail->nama_helm ?? 'Helm');
                            $variantParts = array_filter([$firstDetail->warna, $firstDetail->ukuran]);
                            $variantText = implode(', ', $variantParts);
                        @endphp
                        <div class="text-[11px] text-slate-600 truncate">
                            <span class="font-semibold text-slate-700">{{ $firstDetail->qty ?? 1 }}x</span> {{ $helmName }}
                            @if($variantText)
                                <span class="text-slate-400">({{ $variantText }})</span>
                            @endif
                            @if($extraItems > 0)
                                <span class="text-blue-600 font-medium">+{{ $extraItems }} lainnya</span>
                            @endif
                        </div>
                    @endif

                    {{-- Row 4: Total & Action Buttons --}}
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 block leading-none">{{ $metodeLabel }}</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900">
                                {{ format_rupiah($pesanan->grand_total) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            @if(in_array($pesanan->status, ['ready', 'unpaid_pickup']) && $pesanan->kode_pickup)
                                <a href="{{ route('admin.pickup.validasi', ['kode' => $pesanan->kode_pickup]) }}" 
                                   class="h-8 px-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg text-xs font-semibold inline-flex items-center gap-1 transition-colors">
                                    <i class="fa-solid fa-qrcode text-[10px]"></i>
                                    <span>Serahkan</span>
                                </a>
                            @endif

                            <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                               class="h-8 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg text-xs inline-flex items-center gap-1 transition-colors">
                                <span>Detail</span>
                                <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- B. DESKTOP VIEW (Clean Minimal Table) --}}
        <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[11px] font-semibold">
                            <th class="py-2.5 px-3.5">Pesanan</th>
                            <th class="py-2.5 px-3.5">Pelanggan</th>
                            <th class="py-2.5 px-3.5">Item</th>
                            <th class="py-2.5 px-3.5">Metode</th>
                            <th class="py-2.5 px-3.5 text-right">Total</th>
                            <th class="py-2.5 px-3.5 text-center">Status</th>
                            <th class="py-2.5 px-3.5 text-right">Aksi</th>
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
                                    'pending'       => ['label' => 'Perlu Verifikasi', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/60', 'dot' => 'bg-amber-500'],
                                    'unpaid_pickup' => ['label' => 'Siap Ambil (COD)', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'dot' => 'bg-emerald-500'],
                                    'paid'          => ['label' => 'Sudah Bayar',     'class' => 'bg-blue-50 text-blue-700 border-blue-200/60',       'dot' => 'bg-blue-500'],
                                    'processing'    => ['label' => 'Sedang Disiapkan', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/60',   'dot' => 'bg-indigo-500'],
                                    'ready'         => ['label' => 'Siap Ambil',      'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'dot' => 'bg-emerald-500'],
                                    'completed'     => ['label' => 'Selesai',         'class' => 'bg-slate-50 text-slate-600 border-slate-200/60',     'dot' => 'bg-slate-400'],
                                    'cancelled'     => ['label' => 'Dibatalkan',       'class' => 'bg-rose-50 text-rose-700 border-rose-200/60',       'dot' => 'bg-rose-500'],
                                    default         => ['label' => ucfirst($pesanan->status), 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'],
                                };

                                $metodeLabel = match($pesanan->metode_bayar) {
                                    'tunai'    => 'Bayar di Toko',
                                    'transfer' => 'Transfer Bank',
                                    'qris'     => 'QRIS',
                                    default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                {{-- 1. No. Pesanan & Waktu --}}
                                <td class="py-2.5 px-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                                           class="font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                            #{{ $pesanan->no_pesanan }}
                                        </a>
                                        @if($pesanan->kode_pickup)
                                            <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 font-mono font-bold text-[10px] border border-slate-200">
                                                {{ $pesanan->kode_pickup }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">
                                        {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                </td>

                                {{-- 2. Pelanggan --}}
                                <td class="py-2.5 px-3.5 max-w-[170px]">
                                    <p class="font-medium text-slate-800 truncate">
                                        {{ $customerName }}
                                    </p>
                                    @if($customerPhone)
                                        <span class="text-[11px] text-slate-400 truncate block">
                                            {{ $customerPhone }}
                                        </span>
                                    @endif
                                </td>

                                {{-- 3. Item Helm --}}
                                <td class="py-2.5 px-3.5 max-w-[200px]">
                                    @if($firstDetail)
                                        @php
                                            $helmName = $firstDetail->helm->nama_helm ?? ($firstDetail->nama_helm ?? 'Helm');
                                            $variantParts = array_filter([$firstDetail->warna, $firstDetail->ukuran]);
                                            $variantText = implode(', ', $variantParts);
                                        @endphp
                                        <p class="font-medium text-slate-800 truncate">
                                            <span class="font-bold">{{ $firstDetail->qty ?? 1 }}x</span> {{ $helmName }}
                                        </p>
                                        @if($variantText)
                                            <span class="text-[11px] text-slate-400 truncate block">
                                                {{ $variantText }}
                                            </span>
                                        @endif
                                        @if($extraItems > 0)
                                            <span class="text-[10px] text-blue-600 font-medium block">
                                                +{{ $extraItems }} item lainnya
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>

                                {{-- 4. Metode Bayar --}}
                                <td class="py-2.5 px-3.5">
                                    <span class="text-slate-700 font-medium">
                                        {{ $metodeLabel }}
                                    </span>
                                </td>

                                {{-- 5. Total --}}
                                <td class="py-2.5 px-3.5 text-right font-bold text-slate-900">
                                    {{ format_rupiah($pesanan->grand_total) }}
                                </td>

                                {{-- 6. Status --}}
                                <td class="py-2.5 px-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border {{ $statusBadge['class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                        <span>{{ $statusBadge['label'] }}</span>
                                    </span>
                                </td>

                                {{-- 7. Aksi --}}
                                <td class="py-2.5 px-3.5 text-right">
                                    <div class="inline-flex items-center gap-1 justify-end">
                                        @if(in_array($pesanan->status, ['ready', 'unpaid_pickup']) && $pesanan->kode_pickup)
                                            <a href="{{ route('admin.pickup.validasi', ['kode' => $pesanan->kode_pickup]) }}" 
                                               class="h-7 px-2 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-[11px] inline-flex items-center gap-1 transition-colors border border-emerald-200/80"
                                               title="Serahkan Pesanan">
                                                <i class="fa-solid fa-qrcode text-[9px]"></i>
                                                <span>Serahkan</span>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                                           class="h-7 px-2.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-[11px] inline-flex items-center gap-1 transition-colors">
                                            <span>Detail</span>
                                            <i class="fa-solid fa-chevron-right text-[8px] text-slate-400"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Clean Minimal Pagination --}}
        @if ($pesanans->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 text-xs text-slate-500">
                <p class="text-center sm:text-left text-[11px]">
                    Menampilkan <span class="font-semibold text-slate-800">{{ $pesanans->firstItem() ?? 0 }}-{{ $pesanans->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-800">{{ $pesanans->total() }}</span> transaksi
                </p>
                <div class="flex items-center gap-1.5">
                    @if ($pesanans->onFirstPage())
                        <span class="h-8 px-3 rounded-lg border border-slate-200 text-slate-300 text-xs inline-flex items-center cursor-not-allowed">
                            &larr; Sebelumnya
                        </span>
                    @else
                        <a href="{{ $pesanans->previousPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-medium text-xs transition-colors inline-flex items-center">
                            &larr; Sebelumnya
                        </a>
                    @endif

                    <span class="px-2 text-xs text-slate-600 font-semibold">
                        {{ $pesanans->currentPage() }} / {{ $pesanans->lastPage() }}
                    </span>

                    @if ($pesanans->hasMorePages())
                        <a href="{{ $pesanans->nextPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-medium text-xs transition-colors inline-flex items-center">
                            Selanjutnya &rarr;
                        </a>
                    @else
                        <span class="h-8 px-3 rounded-lg border border-slate-200 text-slate-300 text-xs inline-flex items-center cursor-not-allowed">
                            Selanjutnya &rarr;
                        </span>
                    @endif
                </div>
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-8 sm:p-12 text-center shadow-2xs space-y-3">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
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
                       class="h-8 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
