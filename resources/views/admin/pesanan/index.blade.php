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

    {{-- 1. Bilah Cepat Validasi Kode Pickup --}}
    <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="POST" action="{{ route('admin.pickup.validasi') }}" class="flex items-center gap-2">
            @csrf
            <div class="relative flex-1">
                <i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text"
                       name="kode_pickup"
                       class="w-full h-10 pl-10 pr-4 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider transition-all placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400"
                       placeholder="Masukkan kode pickup..."
                       value="{{ old('kode_pickup') }}"
                       autocomplete="off"
                       required>
            </div>
            <button type="submit" 
                    class="h-10 px-4 sm:px-5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs sm:text-sm font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer shrink-0">
                <i class="fa-solid fa-qrcode text-xs sm:text-sm"></i>
                <span>Validasi</span>
            </button>
        </form>
        @error('kode_pickup')
            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- 3. Rekap Transaksi (Kasir: Rekap Kasir Hari Ini; Owner: Hari Ini & Bulan Ini) --}}
    @php
        $isOwner = auth('web')->user()?->isOwner() ?? false;
    @endphp
    <div class="grid grid-cols-1 {{ $isOwner ? 'md:grid-cols-2' : 'max-w-md' }} gap-3">
        {{-- Card A: Hari Ini --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                    Pendapatan Hari Ini
                </h3>
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

        {{-- Card B: Bulan Ini (Khusus Owner) --}}
        @if($isOwner)
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
        @endif
    </div>

    {{-- 4. Clean Minimalist Status Tabs --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
        @php
            $tabs = [
                ['id' => 'all',        'label' => 'Semua',            'count' => $statusCounts['all'] ?? 0],
                ['id' => 'ready',      'label' => 'Siap Diambil',     'count' => $statusCounts['ready'] ?? 0],
                ['id' => 'pending',    'label' => 'Perlu Verifikasi', 'count' => $statusCounts['pending'] ?? 0],
                ['id' => 'paid',       'label' => 'Sudah Bayar',      'count' => $statusCounts['paid'] ?? 0],
                ['id' => 'processing', 'label' => 'Sedang Disiapkan', 'count' => $statusCounts['processing'] ?? 0],
                ['id' => 'completed',  'label' => 'Selesai',          'count' => $statusCounts['completed'] ?? 0],
                ['id' => 'cancelled',  'label' => 'Dibatalkan',       'count' => $statusCounts['cancelled'] ?? 0],
            ];
        @endphp
        @foreach($tabs as $tab)
            @php
                $isActive = ($currentStatus === $tab['id']) || ($tab['id'] === 'all' && (!$currentStatus || $currentStatus === 'all'));
            @endphp
            <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => $tab['id']])) }}"
               wire:navigate
               class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $isActive ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
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
                       wire:navigate
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

                    $metodeLabel = match($pesanan->metode_bayar) {
                        'tunai'    => 'Bayar di Toko',
                        'transfer' => 'Transfer Bank',
                        'qris'     => 'QRIS',
                        default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
                    };
                @endphp

                <div class="bg-white rounded-xl border border-slate-200/80 p-3 space-y-2">
                    {{-- Row 1: No. Transaksi, Waktu, Pickup & Status --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" wire:navigate class="font-semibold text-xs text-slate-900 hover:text-blue-600 block truncate">
                                {{ $pesanan->no_pesanan }}
                            </a>
                            <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                <span>{{ $pesanan->created_at->format('d M Y, H:i') }}</span>
                                @if($pesanan->kode_pickup)
                                    <span class="text-slate-300">·</span>
                                    <span class="font-mono text-slate-600 font-medium">{{ $pesanan->kode_pickup }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            @if(in_array($pesanan->status, ['ready', 'unpaid_pickup']))
                                <span class="text-xs font-semibold text-blue-600">Siap Diambil</span>
                            @elseif($pesanan->status === 'pending')
                                <span class="text-xs font-semibold text-amber-600">Perlu Verifikasi</span>
                            @elseif($pesanan->status === 'paid')
                                <span class="text-xs font-medium text-slate-700">Sudah Bayar</span>
                            @elseif($pesanan->status === 'processing')
                                <span class="text-xs font-medium text-indigo-600">Sedang Disiapkan</span>
                            @elseif($pesanan->status === 'completed')
                                <span class="text-xs font-medium text-slate-500">Selesai</span>
                            @elseif($pesanan->status === 'cancelled')
                                <span class="text-xs font-medium text-rose-500">Dibatalkan</span>
                            @else
                                <span class="text-xs font-medium text-slate-600">{{ ucfirst($pesanan->status) }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Row 2: Customer & Phone --}}
                    <div class="text-xs text-slate-700">
                        <span class="font-medium text-slate-900">{{ $customerName }}</span>
                        @if($customerPhone)
                            <span class="text-slate-400 text-[11px] font-mono">({{ $customerPhone }})</span>
                        @endif
                    </div>

                    {{-- Row 3: Items Summary --}}
                    @if($firstDetail)
                        @php
                            $helmName = $firstDetail->helm->nama_helm ?? ($firstDetail->nama_helm ?? 'Helm');
                            $variantParts = array_filter([$firstDetail->warna, $firstDetail->ukuran]);
                            $variantText = implode(', ', $variantParts);
                        @endphp
                        <div class="text-[11px] text-slate-600">
                            <span>{{ $firstDetail->qty ?? 1 }}x {{ $helmName }}</span>
                            @if($variantText)
                                <span class="text-slate-400">· {{ $variantText }}</span>
                            @endif
                            @if($extraItems > 0)
                                <span class="text-slate-500">· +{{ $extraItems }} lainnya</span>
                            @endif
                        </div>
                    @endif

                    {{-- Row 4: Total & Action Button --}}
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 block leading-none">{{ $metodeLabel }}</span>
                            <span class="text-xs font-semibold text-slate-900">
                                {{ format_rupiah($pesanan->grand_total) }}
                            </span>
                        </div>

                        <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                           wire:navigate
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 rounded-lg transition-colors">
                            Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- B. DESKTOP VIEW (Clean Minimal Table) --}}
        <div class="hidden md:block bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium">
                            <th class="py-3 px-4 font-medium">No. Transaksi</th>
                            <th class="py-3 px-4 font-medium">Pelanggan</th>
                            <th class="py-3 px-4 font-medium">Produk</th>
                            <th class="py-3 px-4 font-medium">Pembayaran</th>
                            <th class="py-3 px-4 font-medium text-right">Total</th>
                            <th class="py-3 px-4 font-medium">Status</th>
                            <th class="py-3 px-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pesanans as $pesanan)
                            @php
                                $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum');
                                $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
                                $firstDetail   = $pesanan->details->first();
                                $extraItems    = $pesanan->details->count() - 1;

                                $metodeLabel = match($pesanan->metode_bayar) {
                                    'tunai'    => 'Bayar di Toko',
                                    'transfer' => 'Transfer Bank',
                                    'qris'     => 'QRIS',
                                    default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                {{-- 1. No. Transaksi & Waktu --}}
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                                       wire:navigate
                                       class="font-medium text-slate-900 hover:text-blue-600 transition-colors block">
                                        {{ $pesanan->no_pesanan }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                        <span>{{ $pesanan->created_at->format('d M Y, H:i') }} WIB</span>
                                        @if($pesanan->kode_pickup)
                                            <span class="text-slate-300">·</span>
                                            <span class="font-mono text-slate-600 font-medium">{{ $pesanan->kode_pickup }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 2. Pelanggan --}}
                                <td class="py-3.5 px-4 max-w-[170px]">
                                    <p class="font-medium text-slate-900 truncate">
                                        {{ $customerName }}
                                    </p>
                                    @if($customerPhone)
                                        <p class="text-[11px] text-slate-400 font-mono truncate mt-0.5">
                                            {{ $customerPhone }}
                                        </p>
                                    @endif
                                </td>

                                {{-- 3. Produk --}}
                                <td class="py-3.5 px-4 max-w-[220px]">
                                    @if($firstDetail)
                                        @php
                                            $helmName = $firstDetail->helm->nama_helm ?? ($firstDetail->nama_helm ?? 'Helm');
                                            $variantParts = array_filter([$firstDetail->warna, $firstDetail->ukuran]);
                                            $variantText = implode(', ', $variantParts);
                                        @endphp
                                        <p class="text-slate-800 truncate">
                                            <span class="font-medium text-slate-900">{{ $firstDetail->qty ?? 1 }}x</span> {{ $helmName }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                            {{ $variantText ?: 'Standar' }}
                                            @if($extraItems > 0)
                                                <span class="text-slate-500">· +{{ $extraItems }} lainnya</span>
                                            @endif
                                        </p>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Tanpa rincian</span>
                                    @endif
                                </td>

                                {{-- 4. Pembayaran --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $metodeLabel }}
                                </td>

                                {{-- 5. Total --}}
                                <td class="py-3.5 px-4 text-right font-semibold text-slate-900 whitespace-nowrap">
                                    {{ format_rupiah($pesanan->grand_total) }}
                                </td>

                                {{-- 6. Status: tanpa background biru, teksnya aja biru untuk siap diambil --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if(in_array($pesanan->status, ['ready', 'unpaid_pickup']))
                                        <span class="text-xs font-semibold text-blue-600">Siap Diambil</span>
                                    @elseif($pesanan->status === 'pending')
                                        <span class="text-xs font-semibold text-amber-600">Perlu Verifikasi</span>
                                    @elseif($pesanan->status === 'paid')
                                        <span class="text-xs font-medium text-slate-700">Sudah Bayar</span>
                                    @elseif($pesanan->status === 'processing')
                                        <span class="text-xs font-medium text-indigo-600">Sedang Disiapkan</span>
                                    @elseif($pesanan->status === 'completed')
                                        <span class="text-xs font-medium text-slate-500">Selesai</span>
                                    @elseif($pesanan->status === 'cancelled')
                                        <span class="text-xs font-medium text-rose-500">Dibatalkan</span>
                                    @else
                                        <span class="text-xs font-medium text-slate-600">{{ ucfirst($pesanan->status) }}</span>
                                    @endif
                                </td>

                                {{-- 7. Aksi --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                                       wire:navigate
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 rounded-lg transition-colors">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        {{ $pesanans->onEachSide(1)->links('partials.pagination-admin') }}

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
                       wire:navigate
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
