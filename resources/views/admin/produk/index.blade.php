@extends('layouts.admin')

@section('title', 'Katalog & Stok')
@section('page-title', 'Katalog & Stok')

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
        },

        // Modal Restok
        restockOpen: false, 
        selectedHelm: '', 
        selectedVarian: '', 
        helmVariants: {{ Js::from($helm_variants ?? []) }}, 
        jumlah: 1, 
        keterangan: '',
        openRestock(helmId = '', varianId = '') {
            this.selectedHelm = helmId ? String(helmId) : '';
            this.selectedVarian = varianId ? String(varianId) : '';
            
            // Auto-select lowest stock variant if helm is selected but no variant specified
            if (this.selectedHelm && !this.selectedVarian && this.helmVariants[this.selectedHelm] && this.helmVariants[this.selectedHelm].length > 0) {
                let sorted = [...this.helmVariants[this.selectedHelm]].sort((a, b) => a.stok - b.stok);
                this.selectedVarian = String(sorted[0].id_varian);
            }
            
            this.jumlah = 1;
            this.keterangan = '';
            this.restockOpen = true;
        },
        onHelmChange() {
            this.selectedVarian = '';
            if (this.selectedHelm && this.helmVariants[this.selectedHelm] && this.helmVariants[this.selectedHelm].length > 0) {
                let sorted = [...this.helmVariants[this.selectedHelm]].sort((a, b) => a.stok - b.stok);
                this.selectedVarian = String(sorted[0].id_varian);
            }
        },
        get currentVariants() {
            return (this.selectedHelm && this.helmVariants[this.selectedHelm]) ? this.helmVariants[this.selectedHelm] : [];
        },
        get selectedVariantObj() {
            if (!this.selectedHelm || !this.selectedVarian || !this.helmVariants[this.selectedHelm]) return null;
            return this.helmVariants[this.selectedHelm].find(v => String(v.id_varian) === String(this.selectedVarian)) || null;
        },
        get currentStock() {
            return this.selectedVariantObj ? parseInt(this.selectedVariantObj.stok) : 0;
        },
        get newStock() {
            return this.currentStock + (parseInt(this.jumlah) || 0);
        },
        addQty(n) {
            let val = (parseInt(this.jumlah) || 0) + n;
            this.jumlah = Math.max(1, val);
        },
        setQty(n) {
            this.jumlah = Math.max(1, n);
        }
     }">

    {{-- 1. Header Minimalist --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Katalog & Stok
        </h1>

        <div class="flex items-center gap-1.5 shrink-0">
            @if ($tab === 'produk')
                <a href="{{ route('admin.produk.create') }}" 
                   wire:navigate
                   class="h-8 sm:h-9 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span class="hidden sm:inline">Tambah Produk Baru</span>
                    <span class="sm:hidden">Tambah Produk</span>
                </a>
            @elseif ($tab === 'riwayat')
                <button type="button" 
                        @click="openRestock()"
                        class="h-8 sm:h-9 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Catat Masuk</span>
                </button>
            @endif
        </div>
    </div>

    {{-- 2. Ringkasan Stok Kompak (3 Kolom Ringkas di Layar HP & Desktop) --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-3">
        {{-- Total Model Helm --}}
        <a href="{{ route('admin.produk.index') }}" 
           wire:navigate
           class="p-2.5 sm:p-3 rounded-xl border transition-all {{ $tab === 'produk' && $stokFilter === 'all' ? 'bg-slate-900 text-white border-slate-900 shadow-2xs' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-slate-300' : 'text-slate-500' }}">Model Helm</span>
                <i class="fa-solid fa-helmet-safety text-[11px] {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <div class="text-base sm:text-xl font-extrabold tracking-tight mt-0.5 {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-white' : 'text-slate-900' }}">
                {{ $totalProduk }} <span class="text-[11px] font-normal {{ $tab === 'produk' && $stokFilter === 'all' ? 'text-slate-300' : 'text-slate-400' }}">model</span>
            </div>
        </a>

        {{-- Total Unit Fisik --}}
        <a href="{{ route('admin.produk.index', ['stok_filter' => 'tersedia']) }}" 
           wire:navigate
           class="p-2.5 sm:p-3 rounded-xl border transition-all {{ $tab === 'produk' && $stokFilter === 'tersedia' ? 'bg-slate-900 text-white border-slate-900 shadow-2xs' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $tab === 'produk' && $stokFilter === 'tersedia' ? 'text-slate-300' : 'text-slate-500' }}">Stok Fisik</span>
                <i class="fa-solid fa-boxes-stacked text-[11px] {{ $tab === 'produk' && $stokFilter === 'tersedia' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <div class="text-base sm:text-xl font-extrabold tracking-tight mt-0.5 {{ $tab === 'produk' && $stokFilter === 'tersedia' ? 'text-white' : 'text-slate-900' }}">
                {{ number_format($totalStokFisik, 0, ',', '.') }} <span class="text-[11px] font-normal {{ $tab === 'produk' && $stokFilter === 'tersedia' ? 'text-slate-300' : 'text-slate-400' }}">unit</span>
            </div>
        </a>

        {{-- Perlu Restok --}}
        <a href="{{ route('admin.produk.index', ['stok_filter' => 'kritis_habis']) }}" 
           wire:navigate
           class="p-2.5 sm:p-3 rounded-xl border transition-all {{ $stokFilter === 'kritis_habis' ? 'bg-amber-500 text-white border-amber-500 shadow-2xs' : ($totalKritisHabis > 0 ? 'bg-amber-50/80 border-amber-200 text-amber-900 hover:border-amber-300' : 'bg-white border-slate-200/90 text-slate-800') }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $stokFilter === 'kritis_habis' ? 'text-amber-100' : 'text-amber-800' }}">Perlu Restok</span>
                <i class="fa-solid fa-triangle-exclamation text-[11px] {{ $stokFilter === 'kritis_habis' ? 'text-amber-100' : 'text-amber-600' }}"></i>
            </div>
            <div class="text-base sm:text-xl font-extrabold tracking-tight mt-0.5 {{ $stokFilter === 'kritis_habis' ? 'text-white' : 'text-amber-900' }}">
                {{ $totalKritisHabis }} <span class="text-[11px] font-normal {{ $stokFilter === 'kritis_habis' ? 'text-amber-100' : 'text-amber-700' }}">varian</span>
            </div>
        </a>
    </div>

    {{-- 3. Tab Navigasi Minimalist --}}
    <div class="grid grid-cols-2 gap-1 p-1 bg-slate-200/70 rounded-xl text-xs font-medium">
        <a href="{{ route('admin.produk.index') }}" 
           wire:navigate
           class="py-2 text-center rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $tab === 'produk' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Katalog Helm</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'produk' ? 'bg-slate-900 text-white' : 'bg-slate-300 text-slate-700' }}">
                {{ $totalProduk }}
            </span>
        </a>

        <a href="{{ route('admin.produk.index', ['tab' => 'riwayat']) }}" 
           wire:navigate
           class="py-2 text-center rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $tab === 'riwayat' ? 'bg-white text-slate-900 font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Barang Masuk</span>
        </a>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 1: KATALOG HELM & STOK FISIK                                          --}}
    {{-- ========================================================================= --}}
    @if ($tab === 'produk')

        {{-- Toolbar Pencarian & Filter Cepat --}}
        <div class="space-y-2">
            <form method="GET" action="{{ route('admin.produk.index') }}" class="flex items-center gap-1.5">
                @if($stokFilter !== 'all')
                    <input type="hidden" name="stok_filter" value="{{ $stokFilter }}">
                @endif
                @if($idMerk)
                    <input type="hidden" name="merk" value="{{ $idMerk }}">
                @endif
                @if($idKategori)
                    <input type="hidden" name="kategori" value="{{ $idKategori }}">
                @endif

                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari helm, merk, warna..." 
                           class="w-full h-9 pl-8 pr-8 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-slate-400 transition-colors">
                    @if($search)
                        <a href="{{ route('admin.produk.index', array_filter(['merk' => $idMerk, 'kategori' => $idKategori, 'stok_filter' => $stokFilter !== 'all' ? $stokFilter : null])) }}" 
                           wire:navigate
                           class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

                <button type="submit" class="h-9 px-3.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition-colors shrink-0 cursor-pointer">
                    Cari
                </button>

                @if($search || $idMerk || $idKategori || $stokFilter !== 'all')
                    <a href="{{ route('admin.produk.index') }}" 
                       wire:navigate 
                       class="h-9 px-2.5 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs shrink-0" 
                       title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>

            {{-- Filter Chips & Dropdowns --}}
            <div class="flex items-center justify-between gap-2 overflow-x-auto pb-0.5 text-xs">
                {{-- Status Stok Chips --}}
                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('admin.produk.index', array_merge(request()->except('stok_filter', 'page'), ['stok_filter' => 'all'])) }}"
                       wire:navigate
                       class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-colors {{ $stokFilter === 'all' ? 'bg-slate-900 text-white font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                        Semua
                    </a>

                    <a href="{{ route('admin.produk.index', array_merge(request()->except('stok_filter', 'page'), ['stok_filter' => 'kritis_habis'])) }}"
                       wire:navigate
                       class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-colors {{ $stokFilter === 'kritis_habis' ? 'bg-amber-600 text-white font-semibold' : 'bg-white border border-slate-200 text-amber-800 hover:bg-amber-50' }}">
                        Perlu Restok ({{ $totalKritisHabis }})
                    </a>

                    <a href="{{ route('admin.produk.index', array_merge(request()->except('stok_filter', 'page'), ['stok_filter' => 'tersedia'])) }}"
                       wire:navigate
                       class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-colors {{ $stokFilter === 'tersedia' ? 'bg-slate-900 text-white font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                        Tersedia
                    </a>

                    <a href="{{ route('admin.produk.index', array_merge(request()->except('stok_filter', 'page'), ['stok_filter' => 'habis'])) }}"
                       wire:navigate
                       class="px-2.5 py-1 rounded-lg text-xs whitespace-nowrap transition-colors {{ $stokFilter === 'habis' ? 'bg-slate-900 text-white font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                        Habis ({{ $produkHabis }})
                    </a>
                </div>

                {{-- Filter Dropdown Merk & Kategori --}}
                <form method="GET" action="{{ route('admin.produk.index') }}" class="flex items-center gap-1.5 shrink-0">
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if($stokFilter !== 'all')
                        <input type="hidden" name="stok_filter" value="{{ $stokFilter }}">
                    @endif

                    <select name="merk" onchange="this.form.submit()" class="h-7 px-2 text-[11px] rounded-lg bg-white border border-slate-200 text-slate-700 focus:outline-none cursor-pointer">
                        <option value="all">Semua Merk</option>
                        @foreach ($merks as $m)
                            <option value="{{ $m->id_merk }}" {{ $idMerk == $m->id_merk ? 'selected' : '' }}>
                                {{ $m->nama_merk }}
                            </option>
                        @endforeach
                    </select>

                    <select name="kategori" onchange="this.form.submit()" class="h-7 px-2 text-[11px] rounded-lg bg-white border border-slate-200 text-slate-700 focus:outline-none cursor-pointer">
                        <option value="all">Semua Kategori</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id_kategori }}" {{ $idKategori == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
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

                        {{-- Rincian Varian Ringkas (Pills Interaktif 1-Tap Restock) --}}
                        <div class="pt-2 border-t border-slate-100 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                {{-- Preview Varian (Tap Chip Langsung Buka Modal Restok untuk Varian Tersebut) --}}
                                <div class="flex items-center gap-1 overflow-hidden flex-wrap flex-1">
                                    @foreach ($produk->varians->take(3) as $v)
                                        @php
                                            $vHabis = ($v->stok <= 0);
                                            $vKritis = ($v->stok > 0 && $v->stok <= 3);
                                        @endphp
                                        <button type="button"
                                                @click="openRestock({{ $produk->id_helm }}, {{ $v->id_varian }})"
                                                title="Klik untuk langsung restok varian {{ $v->warna }} ({{ $v->ukuran }})"
                                                class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer hover:shadow-2xs active:scale-95 {{ $vHabis ? 'bg-rose-50 border-rose-200 text-rose-700 font-semibold hover:bg-rose-100' : ($vKritis ? 'bg-amber-50 border-amber-200 text-amber-800 font-medium hover:bg-amber-100' : 'bg-slate-50 border-slate-200/80 text-slate-600 hover:bg-slate-100') }}">
                                            <span>{{ $v->warna }} ({{ $v->ukuran }})</span>
                                            <span class="font-bold {{ $vHabis ? 'text-rose-600' : ($vKritis ? 'text-amber-700' : 'text-slate-800') }}">: {{ $v->stok }}</span>
                                            <i class="fa-solid fa-plus text-[8px] opacity-40 ml-0.5"></i>
                                        </button>
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
                                    {{-- Restok Cepat Helm Ini --}}
                                    <button type="button"
                                            @click="openRestock({{ $produk->id_helm }})"
                                            title="Restok Helm Ini"
                                            class="h-7 px-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold inline-flex items-center gap-1 shadow-2xs transition-colors cursor-pointer active:scale-95">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                        <span>Restok</span>
                                    </button>

                                    {{-- Edit Produk (Karyawan & Owner) --}}
                                    <a href="{{ route('admin.produk.edit', $produk->id_helm) }}" 
                                       wire:navigate
                                       title="Edit Produk"
                                       class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 flex items-center justify-center text-xs transition-colors">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
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
                                        <button type="button"
                                                @click="openRestock({{ $produk->id_helm }}, {{ $v->id_varian }})"
                                                title="Klik untuk langsung restok varian {{ $v->warna }} ({{ $v->ukuran }})"
                                                class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] border transition-all cursor-pointer hover:shadow-2xs active:scale-95 {{ $vHabis ? 'bg-rose-50 border-rose-200 text-rose-700 font-semibold hover:bg-rose-100' : ($vKritis ? 'bg-amber-50 border-amber-200 text-amber-800 font-medium hover:bg-amber-100' : 'bg-slate-50 border-slate-200/80 text-slate-600 hover:bg-slate-100') }}">
                                            <span>{{ $v->warna }} ({{ $v->ukuran }})</span>
                                            <span class="font-bold {{ $vHabis ? 'text-rose-600' : ($vKritis ? 'text-amber-700' : 'text-slate-800') }}">: {{ $v->stok }}</span>
                                            <i class="fa-solid fa-plus text-[8px] opacity-40 ml-0.5"></i>
                                        </button>
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
    {{-- MODAL RESTOK MASUK (ALPINE.JS - MOBILE TOUCH FRIENDLY)                    --}}
    {{-- ========================================================================= --}}
    <div x-show="restockOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="restockOpen = false">
        
        <div class="bg-white w-full max-w-md rounded-2xl border border-slate-200 shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" 
             @click.outside="restockOpen = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            {{-- Modal Header --}}
            <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Catat Stok Masuk</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Penambahan inventori fisik gerai</p>
                </div>
                <button type="button" @click="restockOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Form Restok --}}
            <form method="POST" action="{{ route('admin.stok.store') }}" class="p-3.5 sm:p-4 space-y-3.5 text-xs overflow-y-auto">
                @csrf

                {{-- 1. Pilih Produk Helm --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Pilih Produk Helm <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_helm" 
                            x-model="selectedHelm" 
                            @change="onHelmChange()"
                            required 
                            class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-400 cursor-pointer">
                        <option value="">-- Pilih Helm --</option>
                        @foreach ($helms as $h)
                            <option value="{{ $h->id_helm }}">{{ $h->nama_helm }} ({{ $h->varians->count() }} Varian)</option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. Pilih Varian via Touch Pills (Mobile-First) --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="font-medium text-slate-700">
                            Pilih Varian (Warna &amp; Ukuran) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] text-slate-400" x-show="currentVariants.length > 0" x-text="currentVariants.length + ' varian'"></span>
                    </div>

                    {{-- Hidden input for Form submission --}}
                    <input type="hidden" name="id_varian" :value="selectedVarian" required>

                    {{-- Empty placeholder when no helm selected --}}
                    <div x-show="!selectedHelm" class="p-3 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center text-slate-400 text-xs">
                        Pilih produk helm terlebih dahulu di atas
                    </div>

                    {{-- Touch-Friendly Variant Pills --}}
                    <div x-show="selectedHelm && currentVariants.length > 0" class="space-y-1.5">
                        <div class="grid grid-cols-2 gap-1.5 max-h-36 overflow-y-auto p-1 border border-slate-100 rounded-xl bg-slate-50/50">
                            <template x-for="v in currentVariants" :key="v.id_varian">
                                <button type="button" 
                                        @click="selectedVarian = String(v.id_varian)"
                                        class="p-2 rounded-lg border text-left transition-all cursor-pointer flex flex-col justify-between"
                                        :class="String(v.id_varian) === String(selectedVarian) ? 'bg-slate-900 border-slate-900 text-white shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'">
                                    <div class="font-bold text-[11px] truncate" x-text="v.warna"></div>
                                    <div class="flex items-center justify-between mt-1 text-[10px]">
                                        <span class="font-semibold uppercase" :class="String(v.id_varian) === String(selectedVarian) ? 'text-slate-300' : 'text-slate-500'" x-text="'Size ' + v.ukuran"></span>
                                        <span class="font-bold px-1 rounded text-[9px]"
                                              :class="String(v.id_varian) === String(selectedVarian) ? (v.stok <= 0 ? 'bg-rose-500 text-white' : 'bg-slate-800 text-slate-200') : (v.stok <= 0 ? 'bg-rose-50 text-rose-600 border border-rose-200' : (v.stok <= 3 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600'))"
                                              x-text="v.stok <= 0 ? 'Habis (0)' : v.stok + ' unit'"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 3. Live Stock Calculation Preview --}}
                <div x-show="selectedVariantObj" 
                     x-transition
                     class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 flex items-center justify-between text-xs">
                    <div class="text-left">
                        <span class="text-[10px] text-slate-400 block font-medium">Stok Saat Ini</span>
                        <span class="font-bold text-slate-800" x-text="currentStock + ' unit'"></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <span class="text-[10px] font-semibold text-emerald-600" x-text="'+' + (parseInt(jumlah) || 0)"></span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block font-medium">Setelah Restok</span>
                        <span class="font-extrabold text-emerald-600" x-text="newStock + ' unit'"></span>
                    </div>
                </div>

                {{-- 4. Stepper & Presets Kuantitas (Mobile Friendly) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="font-medium text-slate-700">
                            Jumlah Unit Masuk <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="setQty(1)" class="px-2 py-0.5 rounded-md text-[10px] font-semibold border border-slate-200 hover:bg-slate-100 text-slate-600 cursor-pointer active:scale-95 transition-transform">+1</button>
                            <button type="button" @click="setQty(5)" class="px-2 py-0.5 rounded-md text-[10px] font-semibold border border-slate-200 hover:bg-slate-100 text-slate-600 cursor-pointer active:scale-95 transition-transform">+5</button>
                            <button type="button" @click="setQty(10)" class="px-2 py-0.5 rounded-md text-[10px] font-semibold border border-slate-200 hover:bg-slate-100 text-slate-600 cursor-pointer active:scale-95 transition-transform">+10</button>
                            <button type="button" @click="setQty(12)" class="px-2 py-0.5 rounded-md text-[10px] font-semibold border border-slate-200 hover:bg-slate-100 text-slate-600 cursor-pointer active:scale-95 transition-transform">+12</button>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                @click="addQty(-1)" 
                                class="w-10 h-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 flex items-center justify-center font-bold text-base shrink-0 cursor-pointer active:scale-95 transition-transform">
                            -
                        </button>
                        <input type="number" 
                               name="jumlah" 
                               x-model.number="jumlah" 
                               min="1" 
                               required 
                               class="flex-1 h-10 text-center font-extrabold text-base bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:border-slate-400">
                        <button type="button" 
                                @click="addQty(1)" 
                                class="w-10 h-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 flex items-center justify-center font-bold text-base shrink-0 cursor-pointer active:scale-95 transition-transform">
                            +
                        </button>
                    </div>
                </div>

                {{-- 5. Keterangan / Nomor Faktur Opsional --}}
                <div>
                    <label class="block font-medium text-slate-700 mb-1">
                        Keterangan / Faktur <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" 
                           name="keterangan" 
                           x-model="keterangan" 
                           placeholder="Contoh: Faktur No. 2026/XI/089 atau Nama Supplier" 
                           class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-slate-400">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100 shrink-0">
                    <button type="button" 
                            @click="restockOpen = false" 
                            class="h-9 px-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="!selectedVarian || !selectedHelm"
                            class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Simpan Stok Masuk</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

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
