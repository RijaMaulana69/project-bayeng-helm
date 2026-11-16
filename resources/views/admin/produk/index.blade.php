@extends('layouts.admin')

@section('title', 'Data Produk Helm')
@section('page-title', 'Data Produk Helm')

@section('content')
@php
    $authUser = auth('web')->user();
    $isOwner = $authUser ? $authUser->isOwner() : false;
@endphp
<div class="space-y-3 max-w-6xl mx-auto" 
     x-data="{ 
        tab: '{{ $tab }}',
        deleteModal: false, 
        deleteUrl: '', 
        deleteName: '',
        confirmDelete(url, name) {
            this.deleteUrl = url;
            this.deleteName = name;
            this.deleteModal = true;
        }
     }">

    {{-- 1. Header Minimalist --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Data Produk Helm
        </h1>

        <div class="flex items-center gap-1.5 shrink-0">
            @if ($tab === 'produk')
                <a href="{{ route('admin.produk.create') }}" 
                   class="h-8 sm:h-9 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span class="hidden sm:inline">Tambah Produk Baru</span>
                    <span class="sm:hidden">Tambah Produk</span>
                </a>
            @endif
        </div>
    </div>

    {{-- 2. Ringkasan Stok & Filter Cepat (3 Kolom Responsif) --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-3">
        {{-- Semua Produk --}}
        <a href="{{ route('admin.produk.index', array_filter(['tab' => 'produk', 'search' => $search, 'merk' => $idMerk, 'kategori' => $idKategori])) }}" 
           class="p-2.5 sm:p-3 rounded-xl border transition-all cursor-pointer {{ $tab === 'produk' && $stokFilter === 'all' ? 'bg-slate-900 text-white border-slate-900 shadow-2xs' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-slate-300' : 'text-slate-500' }}">Semua Produk</span>
                <i class="fa-solid fa-helmet-safety text-[11px] {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <div class="text-base sm:text-xl font-extrabold tracking-tight mt-0.5 {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-white' : 'text-slate-900' }}">
                {{ $totalProduk }} <span class="text-[11px] font-normal {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-slate-300' : 'text-slate-400' }}">model</span>
            </div>
            <div class="text-[10px] truncate mt-0.5 {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-slate-300' : 'text-slate-400' }}">
                {{ number_format($totalStokFisik, 0, ',', '.') }} unit fisik
            </div>
        </a>

        {{-- Perlu Restok (Stok <= 3 unit) --}}
        <a href="{{ route('admin.produk.index', array_filter(['tab' => 'produk', 'stok_filter' => 'kritis_habis', 'search' => $search, 'merk' => $idMerk, 'kategori' => $idKategori])) }}" 
           class="p-2.5 sm:p-3 rounded-xl border transition-all cursor-pointer {{ $stokFilter === 'kritis_habis' ? 'bg-amber-600 text-white border-amber-600 shadow-2xs' : ($countPerluRestok > 0 ? 'bg-amber-50/70 border-amber-200/80 text-amber-900 hover:border-amber-300' : 'bg-white border-slate-200/90 text-slate-800') }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $stokFilter === 'kritis_habis' ? 'text-amber-100' : 'text-amber-800' }}">Perlu Restok</span>
                <i class="fa-solid fa-triangle-exclamation text-[11px] {{ $stokFilter === 'kritis_habis' ? 'text-amber-100' : 'text-amber-600' }}"></i>
            </div>
            <div class="text-base sm:text-xl font-extrabold tracking-tight mt-0.5 {{ $stokFilter === 'kritis_habis' ? 'text-white' : 'text-amber-900' }}">
                {{ $countPerluRestok }} <span class="text-[11px] font-normal {{ $stokFilter === 'kritis_habis' ? 'text-amber-100' : 'text-amber-700' }}">model</span>
            </div>
            <div class="text-[10px] truncate mt-0.5 {{ $stokFilter === 'kritis_habis' ? 'text-amber-100' : 'text-amber-600' }}">
                Stok menipis (≤ 3)
            </div>
        </a>

        {{-- Stok Habis (Stok 0 unit) --}}
        <a href="{{ route('admin.produk.index', array_filter(['tab' => 'produk', 'stok_filter' => 'habis', 'search' => $search, 'merk' => $idMerk, 'kategori' => $idKategori])) }}" 
           class="p-2.5 sm:p-3 rounded-xl border transition-all cursor-pointer {{ $stokFilter === 'habis' ? 'bg-rose-600 text-white border-rose-600 shadow-2xs' : ($countHabis > 0 ? 'bg-rose-50/70 border-rose-200/80 text-rose-900 hover:border-rose-300' : 'bg-white border-slate-200/90 text-slate-800') }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $stokFilter === 'habis' ? 'text-rose-100' : 'text-rose-800' }}">Stok Habis</span>
                <i class="fa-solid fa-circle-xmark text-[11px] {{ $stokFilter === 'habis' ? 'text-rose-100' : 'text-rose-600' }}"></i>
            </div>
            <div class="text-base sm:text-xl font-extrabold tracking-tight mt-0.5 {{ $stokFilter === 'habis' ? 'text-white' : 'text-rose-900' }}">
                {{ $countHabis }} <span class="text-[11px] font-normal {{ $stokFilter === 'habis' ? 'text-rose-100' : 'text-rose-700' }}">model</span>
            </div>
            <div class="text-[10px] truncate mt-0.5 {{ $stokFilter === 'habis' ? 'text-rose-100' : 'text-rose-600' }}">
                0 unit di rak
            </div>
        </a>
    </div>

    {{-- 3. Tab Navigasi Minimalist --}}
    <div class="grid grid-cols-2 gap-1 p-1 bg-slate-200/70 rounded-xl text-xs font-medium">
        <a href="{{ route('admin.produk.index') }}" 
           class="py-2 text-center rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $tab === 'produk' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Daftar Produk</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'produk' ? 'bg-slate-900 text-white' : 'bg-slate-300 text-slate-700' }}">
                {{ $totalProduk }}
            </span>
        </a>

        <a href="{{ route('admin.produk.index', ['tab' => 'riwayat']) }}" 
           class="py-2 text-center rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $tab === 'riwayat' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Riwayat Stok Masuk</span>
        </a>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 1: DAFTAR PRODUK HELM                                                 --}}
    {{-- ========================================================================= --}}
    @if ($tab === 'produk')

        {{-- Toolbar Pencarian & Filter Dropdown --}}
        <div>
            <form method="GET" action="{{ route('admin.produk.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 text-xs">
                @if($stokFilter !== 'all')
                    <input type="hidden" name="stok_filter" value="{{ $stokFilter }}">
                @endif

                {{-- Kolom Input Pencarian --}}
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari helm, merk, warna..." 
                           class="w-full h-9 pl-9 pr-8 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-slate-400 transition-colors">
                    @if($search)
                        <a href="{{ route('admin.produk.index', array_filter(['stok_filter' => $stokFilter !== 'all' ? $stokFilter : null, 'merk' => $idMerk, 'kategori' => $idKategori])) }}" 
                           class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

                {{-- Filter Dropdown Merk, Kategori, Tombol Cari & Reset --}}
                <div class="flex items-center gap-1.5 shrink-0 flex-wrap sm:flex-nowrap">
                    <select name="merk" onchange="this.form.submit()" class="h-9 px-2.5 text-xs rounded-xl bg-white border border-slate-200 text-slate-700 focus:outline-none cursor-pointer">
                        <option value="all">Semua Merk</option>
                        @foreach ($merks as $m)
                            <option value="{{ $m->id_merk }}" {{ $idMerk == $m->id_merk ? 'selected' : '' }}>
                                {{ $m->nama_merk }}
                            </option>
                        @endforeach
                    </select>

                    <select name="kategori" onchange="this.form.submit()" class="h-9 px-2.5 text-xs rounded-xl bg-white border border-slate-200 text-slate-700 focus:outline-none cursor-pointer">
                        <option value="all">Semua Kategori</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id_kategori }}" {{ $idKategori == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="h-9 px-3.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition-colors cursor-pointer shrink-0">
                        Cari
                    </button>

                    @if($search || $idMerk || $idKategori || $stokFilter !== 'all')
                        <a href="{{ route('admin.produk.index') }}" 
                           class="h-9 px-2.5 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs shrink-0" 
                           title="Reset Semua Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Daftar Kartu Produk (Mobile & Desktop) --}}
        @if ($produks->count() > 0)
            <div class="space-y-2">
                @foreach ($produks as $produk)
                    @php
                        $isHabis = ($produk->stok <= 0);
                        $hasCritical = $produk->varians->contains(fn($v) => $v->stok <= 3);
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3 sm:p-3.5 hover:border-slate-300 transition-all shadow-2xs space-y-2.5"
                         x-data="{ showVariants: false }">
                        
                        {{-- Baris Utama Produk --}}
                        <div class="flex items-start gap-3">
                            {{-- Foto Helm (Klik untuk buka katalog publik) --}}
                            <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
                               target="_blank"
                               title="Lihat produk di etalase web"
                               class="w-13 h-13 sm:w-14 sm:h-14 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center hover:opacity-80 transition-opacity">
                                <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_helm }}" class="w-full h-full object-cover">
                            </a>

                            {{-- Informasi Produk --}}
                            <div class="min-w-0 flex-1 space-y-0.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        {{ $produk->merk->nama_merk ?? 'Helm' }}
                                    </span>
                                    <span class="text-slate-300 text-[10px]">&bull;</span>
                                    <span class="text-[10px] text-slate-500 font-medium">
                                        {{ $produk->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </div>

                                {{-- Judul Helm dengan link langsung ke katalog --}}
                                <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
                                   target="_blank"
                                   title="Lihat produk di etalase web"
                                   class="group font-bold text-slate-900 text-xs sm:text-sm truncate hover:text-blue-600 transition-colors inline-flex items-center gap-1 max-w-full">
                                    <span class="truncate">{{ $produk->nama_helm }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-300 group-hover:text-blue-600 transition-colors shrink-0"></i>
                                </a>

                                {{-- Harga --}}
                                <div class="flex items-center gap-2 pt-0.5">
                                    <span class="sr-only">Harga Jual</span>
                                    <span class="text-xs sm:text-sm font-extrabold text-slate-900">
                                        <span class="text-[11px] font-semibold text-slate-400 mr-0.5">Rp</span>{{ number_format($produk->harga_jual, 0, ',', '.') }}
                                    </span>

                                    {{-- Khusus Owner: Tampilkan Modal & Margin --}}
                                    @if($isOwner)
                                        <span class="text-[10px] text-slate-400">
                                            Modal: Rp {{ number_format($produk->harga_awal, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] text-emerald-600 font-semibold">
                                            (+Rp {{ number_format($produk->harga_jual - $produk->harga_awal, 0, ',', '.') }})
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Stok Badge (Kanan Atas) --}}
                            <div class="shrink-0 text-right">
                                @if($isHabis)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        Habis
                                    </span>
                                @elseif($hasCritical)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $produk->stok }} unit
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ $produk->stok }} unit
                                    </span>
                                @endif
                                <span class="block text-[10px] text-slate-400 mt-0.5">
                                    {{ $produk->varians_count }} varian
                                </span>
                            </div>
                        </div>

                        {{-- Rincian Varian Ringkas (Pills Informasi Stok) --}}
                        <div class="pt-2 border-t border-slate-100 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                {{-- Preview Varian --}}
                                <div class="flex items-center gap-1 overflow-hidden flex-wrap flex-1">
                                    @foreach ($produk->varians->take(3) as $v)
                                        @php
                                            $vHabis = ($v->stok <= 0);
                                            $vKritis = ($v->stok > 0 && $v->stok <= 3);
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] border {{ $vHabis ? 'bg-rose-50 border-rose-200 text-rose-700 font-semibold' : ($vKritis ? 'bg-amber-50 border-amber-200 text-amber-800 font-medium' : 'bg-slate-50 border-slate-200/80 text-slate-600') }}">
                                            <span>{{ $v->warna }} ({{ $v->ukuran }})</span>
                                            <span class="font-bold {{ $vHabis ? 'text-rose-600' : ($vKritis ? 'text-amber-700' : 'text-slate-800') }}">: {{ $v->stok }}</span>
                                        </span>
                                    @endforeach

                                    @if($produk->varians->count() > 3)
                                        <button type="button" 
                                                @click="showVariants = !showVariants"
                                                class="text-[10px] font-semibold text-blue-600 hover:underline px-1 py-0.5 cursor-pointer">
                                            <span x-text="showVariants ? 'Tutup' : '+{{ $produk->varians->count() - 3 }} varian'"></span>
                                        </button>
                                    @endif
                                </div>

                                {{-- Tombol Aksi Cepat --}}
                                <div class="flex items-center gap-1 shrink-0">
                                    {{-- Edit Produk --}}
                                    <a href="{{ route('admin.produk.edit', $produk->id_helm) }}" 
                                       title="Edit Produk"
                                       class="h-7 px-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Edit</span>
                                    </a>

                                    {{-- Aksi Hapus (Owner Only) --}}
                                    @if($isOwner)
                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.produk.destroy', $produk->id_helm) }}', '{{ addslashes($produk->nama_helm) }}')"
                                            title="Hapus Produk"
                                            class="w-7 h-7 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 flex items-center justify-center text-xs transition-colors cursor-pointer">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Expanded Sisa Varian --}}
                            @if($produk->varians->count() > 3)
                                <div x-show="showVariants" x-cloak class="pt-2 flex items-center gap-1 flex-wrap">
                                    @foreach ($produk->varians->slice(3) as $v)
                                        @php
                                            $vHabis = ($v->stok <= 0);
                                            $vKritis = ($v->stok > 0 && $v->stok <= 3);
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] border {{ $vHabis ? 'bg-rose-50 border-rose-200 text-rose-700 font-semibold' : ($vKritis ? 'bg-amber-50 border-amber-200 text-amber-800 font-medium' : 'bg-slate-50 border-slate-200/80 text-slate-600') }}">
                                            <span>{{ $v->warna }} ({{ $v->ukuran }})</span>
                                            <span class="font-bold {{ $vHabis ? 'text-rose-600' : ($vKritis ? 'text-amber-700' : 'text-slate-800') }}">: {{ $v->stok }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($produks->hasPages())
                <div class="p-2 sm:p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
                    {{ $produks->onEachSide(1)->links('partials.pagination-admin') }}
                </div>
            @endif
        @else
            <div class="py-12 text-center text-slate-400 text-xs bg-white rounded-xl border border-slate-200/90 space-y-1.5">
                <i class="fa-solid fa-inbox text-2xl text-slate-300 block mb-1"></i>
                <p class="font-semibold text-slate-700 text-sm">Tidak ada produk ditemukan</p>
                <p class="text-xs text-slate-400">Tidak ada model helm yang cocok dengan filter atau kata kunci saat ini.</p>
                @if($search || $idMerk || $idKategori || $stokFilter !== 'all')
                    <div class="pt-2">
                        <a href="{{ route('admin.produk.index') }}" 
                           wire:navigate
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                            <span>Reset Filter</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

    {{-- ========================================================================= --}}
    {{-- TAB 2: RIWAYAT BARANG MASUK                                               --}}
    {{-- ========================================================================= --}}
    @elseif ($tab === 'riwayat')
        @if ($stok_masuks->count() > 0)
            <div class="space-y-2">
                @foreach ($stok_masuks as $stok)
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3 sm:p-3.5 shadow-2xs flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 space-y-0.5">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900 truncate">
                                    {{ $stok->helm->nama_helm ?? 'Helm' }}
                                </h3>
                                <span class="text-slate-400 text-[11px]">
                                    {{ $stok->warna }} ({{ $stok->ukuran }})
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                <span>{{ $stok->tgl_masuk ? $stok->tgl_masuk->format('d M Y, H:i') : '-' }} WIB</span>
                                <span>&bull;</span>
                                <span>Oleh: {{ $stok->user->nama_lengkap ?? ($stok->user->username ?? 'Staf') }}</span>
                                @if($stok->keterangan)
                                    <span>&bull;</span>
                                    <span class="text-slate-500 italic">{{ $stok->keterangan }}</span>
                                @endif
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-lg font-bold text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            +{{ $stok->jumlah }} unit
                        </span>
                    </div>
                @endforeach
            </div>

            @if ($stok_masuks->hasPages())
                <div class="p-2 sm:p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
                    {{ $stok_masuks->onEachSide(1)->links('partials.pagination-admin') }}
                </div>
            @endif
        @else
            <div class="py-12 text-center text-slate-400 text-xs bg-white rounded-xl border border-slate-200/90">
                <i class="fa-solid fa-inbox text-2xl text-slate-300 block mb-1"></i>
                <p class="font-semibold text-slate-700 text-sm">Belum ada barang masuk</p>
                <p class="text-xs text-slate-400 mt-0.5">Catatan stok masuk akan tersimpan otomatis saat ada restok helm.</p>
            </div>
        @endif
    @endif



    {{-- ========================================================================= --}}
    {{-- MODAL KONFIRMASI HAPUS PRODUK (OWNER ONLY)                                --}}
    {{-- ========================================================================= --}}
    @if($isOwner)
    <div x-show="deleteModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
         @keydown.escape.window="deleteModal = false">
        
        <div class="bg-white w-full max-w-sm rounded-2xl border border-slate-200 shadow-xl overflow-hidden p-5 space-y-4"
             @click.outside="deleteModal = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg mx-auto">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="text-center space-y-1">
                <h3 class="font-bold text-slate-900 text-sm">Hapus Produk Helm?</h3>
                <p class="text-xs text-slate-500">
                    Produk <span class="font-semibold text-slate-800" x-text="deleteName"></span> dan seluruh data variannya akan dihapus dari etalase.
                </p>
            </div>

            <form :action="deleteUrl" method="POST" class="flex items-center gap-2 pt-1">
                @csrf
                @method('DELETE')
                <button type="button" 
                        @click="deleteModal = false"
                        class="flex-1 h-9 rounded-xl border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 h-9 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-2xs transition-colors cursor-pointer">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
