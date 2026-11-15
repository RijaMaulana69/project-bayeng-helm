@extends('layouts.admin')

@section('title', 'Produk & Inventori')
@section('page-title', 'Produk & Inventori')

@section('content')
<div class="space-y-4" 
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
            this.jumlah = 1;
            this.keterangan = '';
            this.restockOpen = true;
        },
        get currentVariants() {
            return (this.selectedHelm && this.helmVariants[this.selectedHelm]) ? this.helmVariants[this.selectedHelm] : [];
        }
     }">

    {{-- 1. Header Section: Title & Unified Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Pusat Produk & Inventori
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Katalog model helm, monitoring persediaan rak gerai, dan pencatatan restok masuk
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            {{-- Tombol Tambah Produk (muncul saat tab katalog) --}}
            <a href="{{ route('admin.produk.create') }}" 
               x-show="tab === 'katalog'"
               class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-2 shadow-2xs transition-colors">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>Tambah Produk Baru</span>
            </a>

            {{-- Tombol Tambah Stok Masuk (muncul saat tab stok / riwayat) --}}
            <button type="button" 
                    x-show="tab !== 'katalog'"
                    @click="openRestock()"
                    class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-2 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-boxes-stacked text-[11px]"></i>
                <span>Tambah Stok Masuk</span>
            </button>
        </div>
    </div>

    {{-- 2. Stat Summary Overview --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        {{-- Card 1: Total Model Helm --}}
        <a href="{{ route('admin.produk.index', ['tab' => 'katalog']) }}" 
           class="p-4 rounded-xl border transition-all {{ $tab === 'katalog' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider {{ $tab === 'katalog' ? 'text-slate-300' : 'text-slate-400' }}">
                    Total Model Helm
                </span>
                <i class="fa-solid fa-helmet-safety text-xs {{ $tab === 'katalog' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <span class="text-xl sm:text-2xl font-bold mt-1.5 block {{ $tab === 'katalog' ? 'text-white' : 'text-slate-900' }}">
                {{ number_format($totalProduk) }} Model
            </span>
            <span class="text-[11px] mt-0.5 block {{ $tab === 'katalog' ? 'text-slate-300' : 'text-slate-500' }}">
                Tersebar di {{ $totalMerk }} merk helm resmi
            </span>
        </a>

        {{-- Card 2: Total Unit Fisik --}}
        <a href="{{ route('admin.produk.index', ['tab' => 'stok']) }}" 
           class="p-4 rounded-xl border transition-all {{ $tab === 'stok' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-800 border-slate-200/90 hover:border-slate-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider {{ $tab === 'stok' ? 'text-slate-300' : 'text-slate-400' }}">
                    Total Unit Fisik Gerai
                </span>
                <i class="fa-solid fa-boxes-stacked text-xs {{ $tab === 'stok' ? 'text-slate-300' : 'text-slate-400' }}"></i>
            </div>
            <span class="text-xl sm:text-2xl font-bold mt-1.5 block {{ $tab === 'stok' ? 'text-white' : 'text-slate-900' }}">
                {{ number_format($totalStokFisik) }} Unit
            </span>
            <span class="text-[11px] mt-0.5 block {{ $tab === 'stok' ? 'text-slate-300' : 'text-slate-500' }}">
                Dari {{ $totalVarian }} kombinasi warna & ukuran
            </span>
        </a>

        {{-- Card 3: Perlu Restok --}}
        <a href="{{ route('admin.produk.index', ['tab' => 'stok', 'filter' => 'kritis_habis']) }}" 
           class="p-4 rounded-xl border transition-all {{ $totalKritisHabis > 0 ? 'bg-amber-50/70 border-amber-200 hover:border-amber-300 text-amber-900' : 'bg-white border-slate-200/90 text-slate-800' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium uppercase tracking-wider text-amber-800">
                    Perlu Restok (&le; 3 Unit)
                </span>
                <i class="fa-solid fa-triangle-exclamation text-xs text-amber-700"></i>
            </div>
            <span class="text-xl sm:text-2xl font-bold mt-1.5 block text-amber-900">
                {{ number_format($totalKritisHabis) }} Varian
            </span>
            <span class="text-[11px] mt-0.5 block text-amber-700/90">
                {{ $kritisCount }} menipis · {{ $habisCount }} habis total
            </span>
        </a>
    </div>

    {{-- 3. Unified Tab Navigation --}}
    <div class="flex items-center gap-1 border-b border-slate-200 text-xs">
        {{-- Tab 1: Katalog & Etalase --}}
        <a href="{{ route('admin.produk.index', ['tab' => 'katalog']) }}" 
           class="px-4 py-2.5 font-medium border-b-2 transition-colors flex items-center gap-2 {{ $tab === 'katalog' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-layer-group text-[11px]"></i>
            <span>Katalog & Etalase</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                {{ $totalProduk }}
            </span>
        </a>

        {{-- Tab 2: Rak Gudang & Restok Fisik --}}
        <a href="{{ route('admin.produk.index', ['tab' => 'stok']) }}" 
           class="px-4 py-2.5 font-medium border-b-2 transition-colors flex items-center gap-2 {{ $tab === 'stok' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-boxes-stacked text-[11px]"></i>
            <span>Rak Gudang & Restok</span>
            @if($totalKritisHabis > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                    {{ $totalKritisHabis }}
                </span>
            @endif
        </a>

        {{-- Tab 3: Riwayat Barang Masuk --}}
        <a href="{{ route('admin.produk.index', ['tab' => 'riwayat']) }}" 
           class="px-4 py-2.5 font-medium border-b-2 transition-colors flex items-center gap-2 {{ $tab === 'riwayat' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
            <span>Riwayat Barang Masuk</span>
        </a>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 1: KATALOG & ETALASE                                                  --}}
    {{-- ========================================================================= --}}
    @if ($tab === 'katalog')
        {{-- Toolbar Filter & Search --}}
        <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs space-y-3">
            <form method="GET" action="{{ route('admin.produk.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <input type="hidden" name="tab" value="katalog">

                {{-- Search Bar --}}
                <div class="relative lg:col-span-2">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari nama helm, merk, atau kategori..." 
                           class="w-full h-9 pl-8 pr-8 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-slate-900 transition-all">
                    @if($search)
                        <a href="{{ route('admin.produk.index', ['tab' => 'katalog']) }}" 
                           class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

                {{-- Filter Merk --}}
                <div>
                    <select name="merk" 
                            onchange="this.form.submit()" 
                            class="w-full h-9 px-3 bg-slate-50 border border-slate-200/80 rounded-lg text-xs font-medium text-slate-700 focus:outline-none focus:bg-white focus:ring-1 focus:ring-slate-900 cursor-pointer">
                        <option value="all">Semua Merk</option>
                        @foreach ($merks as $m)
                            <option value="{{ $m->id_merk }}" {{ $idMerk == $m->id_merk ? 'selected' : '' }}>
                                {{ $m->nama_merk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Kategori --}}
                <div>
                    <select name="kategori" 
                            onchange="this.form.submit()" 
                            class="w-full h-9 px-3 bg-slate-50 border border-slate-200/80 rounded-lg text-xs font-medium text-slate-700 focus:outline-none focus:bg-white focus:ring-1 focus:ring-slate-900 cursor-pointer">
                        <option value="all">Semua Kategori</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id_kategori }}" {{ $idKategori == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($stokFilter !== 'all')
                    <input type="hidden" name="stok_filter" value="{{ $stokFilter }}">
                @endif
            </form>

            {{-- Status Tabs --}}
            <div class="flex items-center gap-1.5 pt-2 border-t border-slate-100 overflow-x-auto text-xs">
                <a href="{{ route('admin.produk.index', array_merge(request()->except('stok_filter', 'katalog_page'), ['tab' => 'katalog', 'stok_filter' => 'all'])) }}"
                   class="px-3 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ $stokFilter === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua Produk ({{ $totalProduk }})
                </a>
                <a href="{{ route('admin.produk.index', array_merge(request()->except('stok_filter', 'katalog_page'), ['tab' => 'katalog', 'stok_filter' => 'tersedia'])) }}"
                   class="px-3 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ $stokFilter === 'tersedia' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Tersedia ({{ $totalProduk - $produkHabis }})
                </a>
                <a href="{{ route('admin.produk.index', array_merge(request()->except('stok_filter', 'katalog_page'), ['tab' => 'katalog', 'stok_filter' => 'habis'])) }}"
                   class="px-3 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ $stokFilter === 'habis' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Habis Total ({{ $produkHabis }})
                </a>

                @if($search || $idMerk || $idKategori || $stokFilter !== 'all')
                    <a href="{{ route('admin.produk.index', ['tab' => 'katalog']) }}" 
                       class="ml-auto text-xs text-rose-600 hover:underline inline-flex items-center gap-1 whitespace-nowrap">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        <span>Reset Filter</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Product Catalog List --}}
        @if ($produks->count() > 0)
            {{-- Mobile Flat Cards --}}
            <div class="md:hidden space-y-3">
                @foreach ($produks as $produk)
                    @php
                        $isHabis = ($produk->stok <= 0);
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-14 h-14 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_helm }}" class="w-full h-full object-cover">
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                                        {{ $produk->merk->nama_merk ?? 'Helm' }}
                                    </span>
                                    <span class="text-slate-300">·</span>
                                    <span class="text-[10px] text-slate-500 font-medium">
                                        {{ $produk->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </div>

                                <h3 class="font-bold text-slate-900 text-xs mt-0.5 line-clamp-1">
                                    {{ $produk->nama_helm }}
                                </h3>

                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xs font-bold text-slate-900">
                                        {{ format_rupiah($produk->harga_jual) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        Modal: {{ format_rupiah($produk->harga_awal) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer & Actions --}}
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 text-[11px]">
                                    {{ $produk->varians_count }} Varian
                                </span>
                                <span class="text-slate-300">·</span>
                                @if($isHabis)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        Stok Habis
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ $produk->stok }} unit
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
                                   target="_blank"
                                   title="Lihat di Web"
                                   class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>

                                <button type="button"
                                        @click="openRestock({{ $produk->id_helm }})"
                                        title="Restok Helm Ini"
                                        class="h-7 px-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium inline-flex items-center gap-1 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Restok</span>
                                </button>

                                <a href="{{ route('admin.produk.edit', $produk->id_helm) }}" 
                                   class="h-7 px-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-medium inline-flex items-center gap-1 transition-colors">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                    <span>Edit</span>
                                </a>

                                <button type="button" 
                                        @click="confirmDelete('{{ route('admin.produk.destroy', $produk->id_helm) }}', '{{ addslashes($produk->nama_helm) }}')"
                                        class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center text-xs transition-colors cursor-pointer">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop Minimal Table --}}
            <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium">
                            <th class="py-3 px-4">Produk Helm</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4 text-right">Harga Modal</th>
                            <th class="py-3 px-4 text-right">Harga Jual</th>
                            <th class="py-3 px-4 text-center">Persediaan Fisik</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($produks as $produk)
                            @php
                                $isHabis = ($produk->stok <= 0);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                            <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_helm }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <span class="font-bold text-slate-900 block truncate" title="{{ $produk->nama_helm }}">
                                                {{ $produk->nama_helm }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 block">
                                                {{ $produk->merk->nama_merk ?? 'Helm' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">
                                        {{ $produk->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-right text-slate-500 font-mono whitespace-nowrap">
                                    {{ format_rupiah($produk->harga_awal) }}
                                </td>

                                <td class="py-3 px-4 text-right font-bold text-slate-900 font-mono whitespace-nowrap">
                                    {{ format_rupiah($produk->harga_jual) }}
                                </td>

                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($isHabis)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                                Habis (0 unit)
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                {{ $produk->stok }} unit
                                            </span>
                                        @endif
                                        <span class="text-[11px] text-slate-400">
                                            ({{ $produk->varians_count }} varian)
                                        </span>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
                                           target="_blank"
                                           title="Lihat di Web Pelanggan"
                                           class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>

                                        <button type="button"
                                                @click="openRestock({{ $produk->id_helm }})"
                                                title="Catat Restok Masuk"
                                                class="h-7 px-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium inline-flex items-center gap-1 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-plus text-[10px]"></i>
                                            <span>Restok</span>
                                        </button>

                                        <a href="{{ route('admin.produk.edit', $produk->id_helm) }}" 
                                           title="Edit Produk"
                                           class="h-7 px-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-medium inline-flex items-center gap-1 transition-colors">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                            <span>Edit</span>
                                        </a>

                                        <button type="button" 
                                                @click="confirmDelete('{{ route('admin.produk.destroy', $produk->id_helm) }}', '{{ addslashes($produk->nama_helm) }}')"
                                                title="Hapus Produk"
                                                class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $produks->onEachSide(1)->links('partials.pagination-admin') }}
        @else
            <div class="bg-white rounded-xl border border-slate-200/90 p-8 sm:p-12 text-center shadow-2xs space-y-3">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-helmet-safety"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Tidak Ada Produk Ditemukan</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Tidak ada model helm yang cocok dengan filter atau kata kunci saat ini.
                    </p>
                </div>
                @if($search || $idMerk || $idKategori || $stokFilter !== 'all')
                    <div class="pt-2">
                        <a href="{{ route('admin.produk.index', ['tab' => 'katalog']) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                            <span>Reset Filter</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

    {{-- ========================================================================= --}}
    {{-- TAB 2: RAK GUDANG & RESTOK FISIK                                          --}}
    {{-- ========================================================================= --}}
    @elseif ($tab === 'stok')
        {{-- Toolbar Filter & Search Varian --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
                <a href="{{ route('admin.produk.index', array_merge(request()->except('filter', 'stok_page'), ['tab' => 'stok', 'filter' => 'all'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $varianFilter === 'all' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Semua Varian</span>
                    <span class="text-[11px] {{ $varianFilter === 'all' ? 'text-slate-300' : 'text-slate-400' }}">{{ $totalVarian }}</span>
                </a>

                <a href="{{ route('admin.produk.index', array_merge(request()->except('filter', 'stok_page'), ['tab' => 'stok', 'filter' => 'kritis_habis'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $varianFilter === 'kritis_habis' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Kritis & Habis (&le; 3)</span>
                    <span class="text-[11px] {{ $varianFilter === 'kritis_habis' ? 'text-slate-300' : 'text-rose-500 font-semibold' }}">{{ $totalKritisHabis }}</span>
                </a>

                <a href="{{ route('admin.produk.index', array_merge(request()->except('filter', 'stok_page'), ['tab' => 'stok', 'filter' => 'habis'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $varianFilter === 'habis' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Habis Total (0)</span>
                    <span class="text-[11px] {{ $varianFilter === 'habis' ? 'text-slate-300' : 'text-rose-500 font-semibold' }}">{{ $habisCount }}</span>
                </a>

                <a href="{{ route('admin.produk.index', array_merge(request()->except('filter', 'stok_page'), ['tab' => 'stok', 'filter' => 'aman'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition-colors inline-flex items-center gap-1.5 {{ $varianFilter === 'aman' ? 'bg-slate-900 text-white font-semibold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80 font-medium' }}">
                    <span>Stok Aman (> 3)</span>
                    <span class="text-[11px] {{ $varianFilter === 'aman' ? 'text-slate-300' : 'text-slate-400' }}">{{ $amanCount }}</span>
                </a>
            </div>

            {{-- Search Box Varian --}}
            <form method="GET" action="{{ route('admin.produk.index') }}" class="relative min-w-[240px]">
                <input type="hidden" name="tab" value="stok">
                @if($varianFilter !== 'all')
                    <input type="hidden" name="filter" value="{{ $varianFilter }}">
                @endif
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari helm, merk, warna, ukuran..." 
                       class="w-full h-9 pl-8 pr-8 rounded-lg bg-white border border-slate-200/90 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
                @if($search)
                    <a href="{{ route('admin.produk.index', ['tab' => 'stok', 'filter' => $varianFilter]) }}" 
                       class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Variant List --}}
        @if ($varians->count() > 0)
            {{-- Mobile Cards --}}
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

            {{-- Desktop Clean Table --}}
            <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium">
                            <th class="py-3 px-4">Produk Helm</th>
                            <th class="py-3 px-4">Varian (Warna & Ukuran)</th>
                            <th class="py-3 px-4 text-center">Status Rak</th>
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
                                    <span class="text-slate-600 font-mono font-bold">{{ $varian->ukuran }}</span>
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
            </div>
        @endif

    {{-- ========================================================================= --}}
    {{-- TAB 3: RIWAYAT BARANG MASUK                                               --}}
    {{-- ========================================================================= --}}
    @elseif ($tab === 'riwayat')
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
                            <th class="py-3 px-4">Keterangan / Faktur</th>
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

            {{ $stok_masuks->onEachSide(1)->links('partials.pagination-admin') }}
        @else
            <div class="bg-white rounded-xl border border-slate-200/90 p-8 text-center shadow-2xs space-y-2">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-base">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800">Belum Ada Riwayat Barang Masuk</h3>
                <p class="text-[11px] text-slate-400 max-w-xs mx-auto">
                    Catatan stok masuk akan tersimpan otomatis setiap kali kasir atau staf menambah stok helm.
                </p>
            </div>
        @endif
    @endif

    {{-- ========================================================================= --}}
    {{-- MODAL RESTOK MASUK (ALPINE.JS)                                            --}}
    {{-- ========================================================================= --}}
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
                        Keterangan / Catatan Faktur <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" 
                           name="keterangan" 
                           x-model="keterangan" 
                           placeholder="Contoh: Faktur No. 2026/XI/089, Dus Box 2" 
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

    {{-- ========================================================================= --}}
    {{-- MODAL KONFIRMASI HAPUS PRODUK                                             --}}
    {{-- ========================================================================= --}}
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
                    Produk <span class="font-semibold text-slate-800" x-text="deleteName"></span> dan seluruh data variannya akan dihapus dari etalase. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <form :action="deleteUrl" method="POST" class="flex items-center gap-2 pt-1">
                @csrf
                @method('DELETE')
                <button type="button" 
                        @click="deleteModal = false"
                        class="flex-1 h-9 rounded-lg border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 h-9 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-2xs transition-colors cursor-pointer">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
