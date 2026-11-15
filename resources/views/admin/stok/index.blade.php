@extends('layouts.admin')

@section('title', 'Stok Fisik & Restok')
@section('page-title', 'Stok Fisik & Restok')

@section('content')
<div class="space-y-4" 
     x-data="{ 
        restockOpen: false, 
        selectedHelm: '', 
        selectedVarian: '', 
        helmVariants: {{ Js::from($helm_variants ?? []) }}, 
        jumlah: 1, 
        keterangan: '',
        openRestock(helmId = '', varianId = '') {
            this.selectedHelm = helmId ? String(helmId) : '';
            this.selectedVarian = varianId ? String(varianId) : '';
            this.jumlah = 1;
            this.keterangan = '';
            this.restockOpen = true;
        },
        get currentVariants() {
            return (this.selectedHelm && this.helmVariants[this.selectedHelm]) ? this.helmVariants[this.selectedHelm] : [];
        }
     }">

    {{-- 1. Header Section: Title & Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Persediaan Helm Gerai
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Monitoring stok varian fisik dan pencatatan restok masuk distributor
            </p>
        </div>

        <button type="button" 
                @click="openRestock()"
                class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-2 shadow-2xs transition-colors cursor-pointer self-start sm:self-auto">
            <i class="fa-solid fa-plus text-[11px]"></i>
            <span>Tambah Stok Masuk</span>
        </button>
    </div>

    {{-- 2. Quick Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        {{-- Card 1: Total Varian --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-medium text-slate-400 block uppercase tracking-wider">
                    Total Varian Helm
                </span>
                <span class="text-xl sm:text-2xl font-bold text-slate-900 mt-1 block">
                    {{ number_format($totalVariants) }}
                </span>
                <span class="text-[11px] text-slate-500 mt-0.5 block">
                    Semua kombinasi warna & ukuran
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        {{-- Card 2: Stok Kritis & Habis (<= 3) --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-medium text-amber-700 block uppercase tracking-wider">
                    Perlu Restok (&le; 3 Unit)
                </span>
                <span class="text-xl sm:text-2xl font-bold text-amber-900 mt-1 block">
                    {{ number_format($totalKritisHabis) }}
                </span>
                <span class="text-[11px] text-amber-700/80 mt-0.5 block">
                    {{ $kritisCount }} menipis · {{ $habisCount }} habis total
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/80 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        {{-- Card 3: Stok Habis Total (0) --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-medium text-rose-600 block uppercase tracking-wider">
                    Stok Habis (0 Unit)
                </span>
                <span class="text-xl sm:text-2xl font-bold text-rose-600 mt-1 block">
                    {{ number_format($habisCount) }}
                </span>
                <span class="text-[11px] text-rose-500 mt-0.5 block">
                    Katalog online dinonaktifkan
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/80 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-ban"></i>
            </div>
        </div>
    </div>

    {{-- 3. Tab Switcher: Monitoring Fisik vs Riwayat Masuk --}}
    <div class="flex items-center gap-1 border-b border-slate-200 text-xs">
        <a href="{{ route('admin.stok.index', ['tab' => 'stok']) }}" 
           class="px-4 py-2.5 font-medium border-b-2 transition-colors flex items-center gap-2 {{ $tab === 'stok' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-helmet-safety text-[11px]"></i>
            <span>Monitoring Persediaan</span>
            @if($totalKritisHabis > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                    {{ $totalKritisHabis }}
                </span>
            @endif
        </a>

        <a href="{{ route('admin.stok.index', ['tab' => 'riwayat']) }}" 
           class="px-4 py-2.5 font-medium border-b-2 transition-colors flex items-center gap-2 {{ $tab === 'riwayat' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
            <span>Riwayat Stok Masuk</span>
        </a>
    </div>

    {{-- ================= TAB 1: MONITORING STOK FISIK ================= --}}
    @if($tab === 'stok')
        {{-- Filter & Search Bar --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
            {{-- Filter Status Pills --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
                <a href="{{ route('admin.stok.index', array_merge(request()->except('filter', 'page'), ['filter' => 'all'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $filter === 'all' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Semua</span>
                    <span class="text-[11px] {{ $filter === 'all' ? 'text-slate-300' : 'text-slate-400' }}">{{ $totalVariants }}</span>
                </a>

                <a href="{{ route('admin.stok.index', array_merge(request()->except('filter', 'page'), ['filter' => 'kritis_habis'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $filter === 'kritis_habis' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Kritis & Habis (&le; 3)</span>
                    <span class="text-[11px] {{ $filter === 'kritis_habis' ? 'text-slate-300' : 'text-rose-500 font-semibold' }}">{{ $totalKritisHabis }}</span>
                </a>

                <a href="{{ route('admin.stok.index', array_merge(request()->except('filter', 'page'), ['filter' => 'habis'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $filter === 'habis' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Habis Total (0)</span>
                    <span class="text-[11px] {{ $filter === 'habis' ? 'text-slate-300' : 'text-rose-500 font-semibold' }}">{{ $habisCount }}</span>
                </a>

                <a href="{{ route('admin.stok.index', array_merge(request()->except('filter', 'page'), ['filter' => 'aman'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $filter === 'aman' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Stok Aman (> 3)</span>
                    <span class="text-[11px] {{ $filter === 'aman' ? 'text-slate-300' : 'text-slate-400' }}">{{ $amanCount }}</span>
                </a>
            </div>

            {{-- Search Box --}}
            <form method="GET" action="{{ route('admin.stok.index') }}" class="relative min-w-[240px]">
                <input type="hidden" name="tab" value="stok">
                @if($filter !== 'all')
                    <input type="hidden" name="filter" value="{{ $filter }}">
                @endif
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari helm, merk, warna, ukuran..." 
                       class="w-full h-9 pl-8 pr-8 rounded-lg bg-white border border-slate-200/90 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
                @if($search)
                    <a href="{{ route('admin.stok.index', ['tab' => 'stok', 'filter' => $filter]) }}" 
                       class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Data List: Monitoring Stok Fisik --}}
        @if ($varians->count() > 0)
            {{-- A. MOBILE VIEW (Flat Cards) --}}
            <div class="md:hidden space-y-2.5">
                @foreach ($varians as $varian)
                    @php
                        $helm = $varian->helm;
                        $merkName = $helm->merk->nama_merk ?? 'Helm';
                        $isHabis = ($varian->stok <= 0);
                        $isKritis = ($varian->stok > 0 && $varian->stok <= 3);
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                @if($helm && $helm->gambar_url)
                                    <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-helmet-safety text-slate-400 text-sm"></i>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">
                                    {{ $merkName }}
                                </span>
                                <h3 class="font-bold text-slate-900 text-xs truncate">
                                    {{ $helm->nama_helm ?? 'Helm' }}
                                </h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Warna: <span class="font-medium text-slate-800">{{ $varian->warna }}</span> · Ukuran: <span class="font-medium text-slate-800">{{ $varian->ukuran }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Stok Saat Ini</span>
                                @if($isHabis)
                                    <span class="text-xs font-bold text-rose-600">Habis (0 unit)</span>
                                @elseif($isKritis)
                                    <span class="text-xs font-bold text-amber-700">Kritis ({{ $varian->stok }} unit)</span>
                                @else
                                    <span class="text-xs font-semibold text-slate-700">{{ $varian->stok }} unit</span>
                                @endif
                            </div>

                            <button type="button" 
                                    @click="openRestock({{ $varian->id_helm }}, {{ $varian->id_varian }})"
                                    class="h-8 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-medium inline-flex items-center gap-1.5 transition-colors cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Restok</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- B. DESKTOP VIEW (Clean Table) --}}
            <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium">
                            <th class="py-3 px-4">Produk Helm</th>
                            <th class="py-3 px-4">Varian (Warna & Ukuran)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Stok Fisik</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($varians as $varian)
                            @php
                                $helm = $varian->helm;
                                $merkName = $helm->merk->nama_merk ?? 'Helm';
                                $isHabis = ($varian->stok <= 0);
                                $isKritis = ($varian->stok > 0 && $varian->stok <= 3);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if($helm && $helm->gambar_url)
                                                <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="fa-solid fa-helmet-safety text-slate-400 text-xs"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-900 block truncate">{{ $helm->nama_helm ?? 'Helm' }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $merkName }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-slate-700">
                                    <span class="font-medium text-slate-900">{{ $varian->warna }}</span>
                                    <span class="text-slate-400">·</span>
                                    <span class="text-slate-600 font-mono">{{ $varian->ukuran }}</span>
                                </td>

                                <td class="py-3 px-4 text-center">
                                    @if($isHabis)
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                            Habis Total
                                        </span>
                                    @elseif($isKritis)
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            Stok Kritis
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                            Tersedia
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-right">
                                    <span class="font-bold font-mono text-sm {{ $isHabis ? 'text-rose-600' : ($isKritis ? 'text-amber-800' : 'text-slate-900') }}">
                                        {{ $varian->stok }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 ml-0.5">unit</span>
                                </td>

                                <td class="py-3 px-4 text-right">
                                    <button type="button" 
                                            @click="openRestock({{ $varian->id_helm }}, {{ $varian->id_varian }})"
                                            class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 text-xs font-medium inline-flex items-center gap-1 transition-colors cursor-pointer">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                        <span>Restok</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            {{ $varians->onEachSide(1)->links('partials.pagination-admin') }}
        @else
            <div class="bg-white rounded-xl border border-slate-200/90 p-8 text-center shadow-2xs space-y-2">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Tidak Ada Varian Ditemukan</h3>
                <p class="text-[11px] text-slate-400 max-w-xs mx-auto">
                    Tidak ada persediaan varian helm yang cocok dengan filter atau kata kunci saat ini.
                </p>
                @if($search || $filter !== 'all')
                    <div class="pt-2">
                        <a href="{{ route('admin.stok.index', ['tab' => 'stok']) }}" class="text-xs text-blue-600 hover:underline font-medium">
                            Reset Filter & Pencarian
                        </a>
                    </div>
                @endif
            </div>
        @endif

    {{-- ================= TAB 2: RIWAYAT STOK MASUK ================= --}}
    @elseif($tab === 'riwayat')
        @if ($stok_masuks->count() > 0)
            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-2.5">
                @foreach ($stok_masuks as $stok)
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs space-y-2 text-xs">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-slate-900">
                                    {{ $stok->helm->nama_helm ?? 'Helm' }}
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $stok->warna }} · {{ $stok->ukuran }}
                                </p>
                            </div>
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                +{{ $stok->jumlah }} unit
                            </span>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $stok->tgl_masuk ? $stok->tgl_masuk->format('d M Y, H:i') : '-' }}</span>
                            <span>Oleh: {{ $stok->user->nama_lengkap ?? ($stok->user->username ?? 'Staf') }}</span>
                        </div>
                        @if($stok->keterangan)
                            <p class="text-[11px] text-slate-500 italic bg-slate-50 p-2 rounded">
                                {{ $stok->keterangan }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium">
                            <th class="py-3 px-4">Waktu Masuk</th>
                            <th class="py-3 px-4">Produk Helm</th>
                            <th class="py-3 px-4">Varian</th>
                            <th class="py-3 px-4 text-center">Jumlah</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4 text-right">Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($stok_masuks as $stok)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                    {{ $stok->tgl_masuk ? $stok->tgl_masuk->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    {{ $stok->helm->nama_helm ?? 'Helm' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $stok->warna }} · {{ $stok->ukuran }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        +{{ $stok->jumlah }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500">
                                    {{ $stok->keterangan ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-right text-slate-500">
                                    {{ $stok->user->nama_lengkap ?? ($stok->user->username ?? 'Staf') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            {{ $stok_masuks->onEachSide(1)->links('partials.pagination-admin') }}
        @else
            <div class="bg-white rounded-xl border border-slate-200/90 p-8 text-center shadow-2xs space-y-2">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Belum Ada Riwayat Stok Masuk</h3>
                <p class="text-[11px] text-slate-400 max-w-xs mx-auto">
                    Catatan stok masuk akan tersimpan otomatis setiap kali kasir atau staf menambah stok helm.
                </p>
            </div>
        @endif
    @endif

    {{-- ================= MODAL RESTOK MASUK (ALPINE.JS) ================= --}}
    <div x-show="restockOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
         @keydown.escape.window="restockOpen = false">
        
        <div class="bg-white w-full max-w-md rounded-2xl border border-slate-200 shadow-xl overflow-hidden" 
             @click.outside="restockOpen = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Tambah Stok Masuk</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Input stok barang fisik yang baru tiba dari distributor</p>
                </div>
                <button type="button" @click="restockOpen = false" class="text-slate-400 hover:text-slate-600 text-xs p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.stok.store') }}" class="p-4 space-y-3.5 text-xs">
                @csrf

                {{-- Pilih Produk Helm --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Pilih Produk Helm <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_helm" 
                            x-model="selectedHelm" 
                            @change="selectedVarian = ''"
                            required 
                            class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                        <option value="">-- Pilih Helm --</option>
                        @foreach ($helms as $h)
                            <option value="{{ $h->id_helm }}">{{ $h->nama_helm }} ({{ $h->varians->count() }} Varian)</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Varian (Warna & Ukuran) --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Pilih Varian (Warna & Ukuran) <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_varian" 
                            x-model="selectedVarian" 
                            required 
                            :disabled="!selectedHelm"
                            class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer disabled:bg-slate-50 disabled:text-slate-400">
                        <option value="">-- Pilih Varian --</option>
                        <template x-for="v in currentVariants" :key="v.id_varian">
                            <option :value="v.id_varian" x-text="v.label" :selected="v.id_varian == selectedVarian"></option>
                        </template>
                    </select>
                </div>

                {{-- Jumlah Masuk --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Jumlah Unit Masuk <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           name="jumlah" 
                           x-model="jumlah" 
                           min="1" 
                           required 
                           placeholder="Contoh: 10" 
                           class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900">
                </div>

                {{-- Keterangan Opsional --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Keterangan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" 
                           name="keterangan" 
                           x-model="keterangan" 
                           placeholder="Contoh: Restok supplier box 2, retur toko, dll" 
                           class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" 
                            @click="restockOpen = false" 
                            class="h-9 px-3.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-medium transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-9 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Simpan Stok Masuk</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
