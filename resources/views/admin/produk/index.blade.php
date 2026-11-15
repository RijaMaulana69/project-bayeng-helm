@extends('layouts.admin')

@section('title', 'Katalog & Master Produk')
@section('page-title', 'Katalog & Master Produk')

@section('content')
<div class="space-y-4" x-data="{ 
    deleteModal: false, 
    deleteUrl: '', 
    deleteName: '',
    confirmDelete(url, name) {
        this.deleteUrl = url;
        this.deleteName = name;
        this.deleteModal = true;
    }
}">

    {{-- 1. Header Section: Title & Add Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Katalog Produk Helm
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Master data model helm, varian warna & ukuran, serta penetapan harga jual toko
            </p>
        </div>

        <a href="{{ route('admin.produk.create') }}" 
           class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-2 shadow-2xs transition-colors self-start sm:self-auto">
            <i class="fa-solid fa-plus text-[11px]"></i>
            <span>Tambah Produk</span>
        </a>
    </div>

    {{-- 2. Stat Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        {{-- Card 1: Total Model Produk --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-medium text-slate-400 block uppercase tracking-wider">
                    Total Model Helm
                </span>
                <span class="text-xl sm:text-2xl font-bold text-slate-900 mt-1 block">
                    {{ number_format($totalProduk) }}
                </span>
                <span class="text-[11px] text-slate-500 mt-0.5 block">
                    Tersebar di {{ $totalMerk }} merk helm
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-helmet-safety"></i>
            </div>
        </div>

        {{-- Card 2: Total Unit Fisik --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-medium text-slate-400 block uppercase tracking-wider">
                    Total Unit Persediaan
                </span>
                <span class="text-xl sm:text-2xl font-bold text-slate-900 mt-1 block">
                    {{ number_format($totalStokFisik) }}
                </span>
                <span class="text-[11px] text-slate-500 mt-0.5 block">
                    Akumulasi seluruh varian gerai
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        {{-- Card 3: Stok Habis Total --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-medium text-rose-600 block uppercase tracking-wider">
                    Produk Kosong (0 Unit)
                </span>
                <span class="text-xl sm:text-2xl font-bold text-rose-600 mt-1 block">
                    {{ number_format($produkHabis) }}
                </span>
                <span class="text-[11px] text-rose-500 mt-0.5 block">
                    Semua varian habis di rak
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200/80 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-box-open"></i>
            </div>
        </div>
    </div>

    {{-- 3. Filter & Search Toolbar --}}
    <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs space-y-3">
        <form method="GET" action="{{ route('admin.produk.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            {{-- Search Bar --}}
            <div class="relative lg:col-span-2">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama helm, merk, atau kategori..." 
                       class="w-full h-9 pl-8 pr-8 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-slate-900 transition-all">
                @if($search)
                    <a href="{{ route('admin.produk.index', array_merge(request()->except('search', 'page'))) }}" 
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
                <input type="hidden" name="stok" value="{{ $stokFilter }}">
            @endif
        </form>

        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-1.5 pt-2 border-t border-slate-100 overflow-x-auto text-xs">
            <a href="{{ route('admin.produk.index', array_merge(request()->except('stok', 'page'), ['stok' => 'all'])) }}"
               class="px-3 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ $stokFilter === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Semua Produk ({{ $totalProduk }})
            </a>
            <a href="{{ route('admin.produk.index', array_merge(request()->except('stok', 'page'), ['stok' => 'tersedia'])) }}"
               class="px-3 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ $stokFilter === 'tersedia' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Tersedia ({{ $totalProduk - $produkHabis }})
            </a>
            <a href="{{ route('admin.produk.index', array_merge(request()->except('stok', 'page'), ['stok' => 'habis'])) }}"
               class="px-3 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-colors {{ $stokFilter === 'habis' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Habis Total ({{ $produkHabis }})
            </a>

            @if($search || $idMerk || $idKategori || $stokFilter !== 'all')
                <a href="{{ route('admin.produk.index') }}" 
                   class="ml-auto text-xs text-rose-600 hover:underline inline-flex items-center gap-1 whitespace-nowrap">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>
    </div>

    {{-- 4. Product List --}}
    @if ($produks->count() > 0)

        {{-- A. MOBILE VIEW (Flat Cards) --}}
        <div class="md:hidden space-y-3">
            @foreach ($produks as $produk)
                @php
                    $isHabis = ($produk->stok <= 0);
                    $margin = max(0, $produk->harga_jual - $produk->harga_awal);
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

                    {{-- Stock & Variant Summary --}}
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

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-1">
                            <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
                               target="_blank"
                               title="Lihat di Web"
                               class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>

                            <a href="{{ route('admin.stok.index', ['search' => $produk->nama_helm]) }}" 
                               title="Buka Stok Gerai"
                               class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors">
                                <i class="fa-solid fa-boxes-stacked text-[10px]"></i>
                            </a>

                            <a href="{{ route('admin.produk.edit', $produk->id_helm) }}" 
                               class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-medium inline-flex items-center gap-1 transition-colors">
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

        {{-- B. DESKTOP VIEW (Minimal Table) --}}
        <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium">
                        <th class="py-3 px-4">Produk Helm</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga Modal</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Persediaan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($produks as $produk)
                        @php
                            $isHabis = ($produk->stok <= 0);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- 1. Helm & Merk --}}
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

                            {{-- 2. Kategori --}}
                            <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">
                                    {{ $produk->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>

                            {{-- 3. Harga Modal --}}
                            <td class="py-3 px-4 text-right text-slate-500 font-mono whitespace-nowrap">
                                {{ format_rupiah($produk->harga_awal) }}
                            </td>

                            {{-- 4. Harga Jual --}}
                            <td class="py-3 px-4 text-right font-bold text-slate-900 font-mono whitespace-nowrap">
                                {{ format_rupiah($produk->harga_jual) }}
                            </td>

                            {{-- 5. Persediaan & Varian --}}
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

                            {{-- 6. Aksi --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Lihat di Web --}}
                                    <a href="{{ route('katalog.detail', $produk->id_helm) }}" 
                                       target="_blank"
                                       title="Lihat di Web Pelanggan"
                                       class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>

                                    {{-- Kelola Stok di Gudang --}}
                                    <a href="{{ route('admin.stok.index', ['search' => $produk->nama_helm]) }}" 
                                       title="Kelola Stok Fisik"
                                       class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-boxes-stacked text-[10px]"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.produk.edit', $produk->id_helm) }}" 
                                       title="Edit Produk"
                                       class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-medium inline-flex items-center gap-1 transition-colors">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                        <span>Edit</span>
                                    </a>

                                    {{-- Hapus --}}
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

        {{-- Pagination --}}
        {{ $produks->onEachSide(1)->links('partials.pagination-admin') }}

    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-8 sm:p-12 text-center shadow-2xs space-y-3">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-helmet-safety"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">
                    Tidak Ada Produk Ditemukan
                </h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Tidak ada model helm yang cocok dengan filter atau kata kunci saat ini. Coba sesuaikan kata kunci pencarian.
                </p>
            </div>
            @if($search || $idMerk || $idKategori || $stokFilter !== 'all')
                <div class="pt-2">
                    <a href="{{ route('admin.produk.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- 5. Delete Confirmation Modal (Alpine.js) --}}
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
