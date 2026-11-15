@extends('layouts.admin')

@section('title', 'Riwayat Kasir')
@section('page-title', 'Riwayat Kasir')

@section('content')
<div class="space-y-3 max-w-5xl mx-auto">

    {{-- 1. Header Bersih --}}
    <div>
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Riwayat Kasir
        </h1>
        <p class="text-xs text-slate-500">
            {{ $periodDateLabel }}
        </p>
    </div>

    {{-- 2. Tab Periode (Mobile-First Full Width) --}}
    <div class="grid grid-cols-2 gap-1 p-1 bg-slate-200/70 rounded-xl text-xs font-medium">
        <a href="{{ route('admin.karyawan.riwayat', array_merge(request()->except(['period', 'page']), ['period' => 'today'])) }}"
           wire:navigate
           class="py-2 text-center rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $period === 'today' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Hari Ini</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $period === 'today' ? 'bg-slate-900 text-white' : 'bg-slate-300 text-slate-700' }}">
                {{ $kpi['count_today'] }}
            </span>
        </a>

        <a href="{{ route('admin.karyawan.riwayat', array_merge(request()->except(['period', 'page']), ['period' => 'this_month'])) }}"
           wire:navigate
           class="py-2 text-center rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $period === 'this_month' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Bulan Ini</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $period === 'this_month' ? 'bg-slate-900 text-white' : 'bg-slate-300 text-slate-700' }}">
                {{ $kpi['count_month'] }}
            </span>
        </a>
    </div>

    {{-- 3. Ringkasan Kas (2x2 di Mobile, 4 Kolom di Desktop - Tipografi Natural Tanpa Monospace Kaku) --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
        {{-- Total Penjualan --}}
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs space-y-0.5">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Penjualan</span>
                <i class="fa-solid fa-circle-check text-xs text-emerald-600"></i>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight truncate">
                <span class="text-xs font-semibold text-slate-400 mr-0.5">Rp</span>{{ number_format($kpi['total_penjualan'], 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400">
                {{ $kpi['total_transaksi'] }} transaksi
            </p>
        </div>

        {{-- Kas Tunai --}}
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs space-y-0.5">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Kas Tunai</span>
                <i class="fa-solid fa-money-bill-wave text-xs text-amber-600"></i>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight truncate">
                <span class="text-xs font-semibold text-slate-400 mr-0.5">Rp</span>{{ number_format($kpi['uang_tunai'], 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400">
                {{ $kpi['uang_tunai_count'] }} transaksi
            </p>
        </div>

        {{-- Non-Tunai --}}
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs space-y-0.5">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Non-Tunai</span>
                <i class="fa-solid fa-qrcode text-xs text-blue-600"></i>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight truncate">
                <span class="text-xs font-semibold text-slate-400 mr-0.5">Rp</span>{{ number_format($kpi['non_tunai'], 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400">
                {{ $kpi['non_tunai_count'] }} transaksi
            </p>
        </div>

        {{-- Helm Terjual --}}
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs space-y-0.5">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Helm Terjual</span>
                <i class="fa-solid fa-helmet-safety text-xs text-slate-500"></i>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight truncate">
                {{ $kpi['helm_terjual'] }} <span class="text-xs font-normal text-slate-500">unit</span>
            </div>
            <p class="text-[10px] text-slate-400">
                Helm diserahkan
            </p>
        </div>
    </div>

    {{-- 4. Pencarian & Filter Metode --}}
    <div class="space-y-2">
        <form method="GET" action="{{ route('admin.karyawan.riwayat') }}" class="flex items-center gap-1.5">
            <input type="hidden" name="period" value="{{ $period }}">
            @if($metodeBayar !== 'all')
                <input type="hidden" name="metode_bayar" value="{{ $metodeBayar }}">
            @endif

            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari no. order, pembeli, pickup..." 
                       class="w-full h-9 pl-8 pr-3 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-slate-400 transition-colors">
            </div>

            <button type="submit" class="h-9 px-3.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition-colors shrink-0 cursor-pointer">
                Cari
            </button>

            @if(request('search') || $metodeBayar !== 'all')
                <a href="{{ route('admin.karyawan.riwayat', ['period' => $period]) }}" wire:navigate class="h-9 px-2.5 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs shrink-0" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>

        {{-- Filter Chips Metode Pembayaran --}}
        <div class="flex items-center gap-1 overflow-x-auto pb-0.5 text-xs">
            <a href="{{ route('admin.karyawan.riwayat', array_merge(request()->except(['metode_bayar', 'page']), ['metode_bayar' => 'all'])) }}"
               wire:navigate
               class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-colors {{ $metodeBayar === 'all' ? 'bg-slate-900 text-white font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.karyawan.riwayat', array_merge(request()->except(['metode_bayar', 'page']), ['metode_bayar' => 'tunai'])) }}"
               wire:navigate
               class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-colors {{ $metodeBayar === 'tunai' ? 'bg-amber-600 text-white font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Tunai
            </a>
            <a href="{{ route('admin.karyawan.riwayat', array_merge(request()->except(['metode_bayar', 'page']), ['metode_bayar' => 'non_tunai'])) }}"
               wire:navigate
               class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-colors {{ $metodeBayar === 'non_tunai' ? 'bg-blue-600 text-white font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Non-Tunai
            </a>
        </div>
    </div>

    {{-- 5. DAFTAR RIWAYAT TRANSAKSI SELESAI (Desain Modern, Bersih & Mobile-First) --}}
    <div class="space-y-2">
        @forelse ($pesanans as $pesanan)
            @php
                $isCash = ($pesanan->metode_bayar === 'tunai');
                $customerName = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan');
                $firstItem = $pesanan->details->first();
                $extraCount = $pesanan->details->count() - 1;
            @endphp
            <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
               wire:navigate
               class="block bg-white rounded-xl border border-slate-200/90 p-3 sm:p-3.5 hover:border-slate-300 hover:shadow-xs transition-all active:bg-slate-50">
                <div class="flex items-start justify-between gap-3">
                    
                    {{-- Sisi Kiri: Informasi Transaksi --}}
                    <div class="min-w-0 flex-1 space-y-1">
                        {{-- Baris 1: Nama Pelanggan & No. Pesanan --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs sm:text-sm font-bold text-slate-900">
                                {{ $customerName }}
                            </span>
                            <span class="text-[11px] text-slate-400">
                                #{{ $pesanan->no_pesanan }}
                            </span>
                        </div>

                        {{-- Baris 2: Item Helm & Varian --}}
                        <div class="text-xs text-slate-600 truncate">
                            @if($firstItem)
                                <span>{{ $firstItem->qty }}x {{ $firstItem->helm->nama_helm ?? ($firstItem->nama_helm ?? 'Helm') }}</span>
                                @if($firstItem->warna || $firstItem->ukuran)
                                    <span class="text-slate-400 text-[11px]">({{ implode(', ', array_filter([$firstItem->warna, $firstItem->ukuran])) }})</span>
                                @endif
                                @if($extraCount > 0)
                                    <span class="text-slate-400 text-[11px] font-medium">+{{ $extraCount }} item</span>
                                @endif
                            @else
                                <span class="text-slate-400">Tidak ada detail item</span>
                            @endif
                        </div>

                        {{-- Baris 3: Jam, Metode Bayar & Kode Pickup (Tanpa Titik / Bullets) --}}
                        <div class="flex items-center gap-1.5 text-xs pt-0.5 flex-wrap">
                            <span class="text-[11px] text-slate-500 font-medium">
                                {{ $pesanan->created_at->format('H:i') }} WIB
                            </span>
                            @if($isCash)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                    Tunai
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">
                                    {{ strtoupper($pesanan->metode_bayar ?? 'Non-Tunai') }}
                                </span>
                            @endif
                            @if($pesanan->kode_pickup)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">
                                    {{ $pesanan->kode_pickup }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Sisi Kanan: Nominal Bersih & Tombol Aksi --}}
                    <div class="text-right shrink-0 space-y-0.5">
                        <div class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                            <span class="text-xs font-semibold text-slate-400 mr-0.5">Rp</span>{{ number_format($pesanan->grand_total, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] text-blue-600 flex items-center justify-end gap-1">
                            <span>Lihat nota</span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                        </div>
                    </div>

                </div>
            </a>
        @empty
            <div class="py-12 text-center text-slate-400 text-xs bg-white rounded-xl border border-slate-200/90">
                <i class="fa-solid fa-inbox text-2xl text-slate-300 block mb-1.5"></i>
                <p class="font-semibold text-slate-700 text-sm">Tidak ada transaksi selesai</p>
                <p class="text-xs text-slate-400 mt-0.5">Belum ada transaksi selesai pada periode ini.</p>
            </div>
        @endforelse
    </div>

    {{-- 6. Pagination --}}
    @if ($pesanans->hasPages())
        <div class="p-2 sm:p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            {{ $pesanans->links('partials.pagination-admin') }}
        </div>
    @endif

</div>
@endsection
