@extends('layouts.admin')

@section('title', 'Riwayat Barang Masuk')
@section('page-title', 'Riwayat Barang Masuk')

@section('content')
<div class="space-y-4" x-data="{ showFilter: false }">

    {{-- 1. Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Riwayat & Log Barang Masuk
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Monitoring persediaan helm masuk ke gerai, audit restok fisik oleh staf, dan rekapitulasi nilai modal belanja stok.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" 
                    @click="showFilter = !showFilter"
                    class="h-9 px-3.5 rounded-lg border border-slate-200/90 hover:bg-slate-50 text-slate-700 font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-filter text-[11px] text-slate-500"></i>
                <span>Filter Lanjutan</span>
                @if($selectedUser !== 'all' || $selectedMerk !== 'all' || $selectedKategori !== 'all')
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                @endif
            </button>

            <a href="{{ route('admin.owner.laporan.penjualan') }}" 
               wire:navigate
               class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-chart-line text-[11px]"></i>
                <span>Laporan Penjualan</span>
            </a>
        </div>
    </div>

    {{-- 2. Period Filter Bar (Pills) --}}
    <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex items-center gap-1.5 text-xs overflow-x-auto pb-1 sm:pb-0 w-full sm:w-auto">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Periode:</span>
            @php
                $periodTabs = [
                    'today'      => 'Hari Ini',
                    'yesterday'  => 'Kemarin',
                    '7days'      => '7 Hari',
                    'this_month' => 'Bulan Ini',
                    '30days'     => '30 Hari',
                    'all'        => 'Semua Waktu',
                ];
            @endphp
            @foreach ($periodTabs as $key => $label)
                <a href="{{ route('admin.owner.barang-masuk.index', array_merge(request()->except(['period', 'page', 'start_date', 'end_date']), ['period' => $key])) }}"
                   wire:navigate
                   class="px-2.5 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-all {{ $selectedPeriod === $key ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Search Input --}}
        <form method="GET" action="{{ route('admin.owner.barang-masuk.index') }}" class="w-full sm:w-72 flex items-center gap-1.5">
            @foreach (request()->except(['search', 'page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari nama helm / warna / keterangan..." 
                       class="w-full h-8 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition-colors">
            </div>
            <button type="submit" class="h-8 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                Cari
            </button>
        </form>
    </div>

    {{-- Advanced Filter Dropdown Bar --}}
    <div x-show="showFilter" 
         x-cloak 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.owner.barang-masuk.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            {{-- Filter Petugas Karyawan --}}
            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Petugas Penerima</label>
                <select name="user_id" class="w-full h-9 px-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-slate-400">
                    <option value="all">Semua Petugas</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id_user }}" {{ $selectedUser == $u->id_user ? 'selected' : '' }}>
                            {{ $u->nama_lengkap }} ({{ $u->username }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Merk Helm --}}
            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Merk Helm</label>
                <select name="id_merk" class="w-full h-9 px-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-slate-400">
                    <option value="all">Semua Merk</option>
                    @foreach ($merks as $m)
                        <option value="{{ $m->id_merk }}" {{ $selectedMerk == $m->id_merk ? 'selected' : '' }}>
                            {{ $m->nama_merk }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori --}}
            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Kategori Helm</label>
                <select name="id_kategori" class="w-full h-9 px-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-slate-400">
                    <option value="all">Semua Kategori</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id_kategori }}" {{ $selectedKategori == $k->id_kategori ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit Filter --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 h-9 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs transition-colors cursor-pointer inline-flex items-center justify-center gap-1">
                    <i class="fa-solid fa-check text-[11px]"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.owner.barang-masuk.index') }}" wire:navigate class="h-9 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs inline-flex items-center justify-center transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 3. Summary KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- Card 1: Total Unit Masuk --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Fisik Masuk</span>
                <i class="fa-solid fa-boxes-stacked text-xs text-blue-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight font-mono">
                +{{ number_format($kpi['total_unit']) }} <span class="text-xs font-normal text-slate-500">Unit</span>
            </div>
            <p class="text-[10px] text-slate-400">
                {{ $kpi['total_frekuensi'] }}x pencatatan restok
            </p>
        </div>

        {{-- Card 2: Total Nilai Modal Belanja --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Modal Masuk</span>
                <i class="fa-solid fa-coins text-xs text-amber-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight font-mono">
                {{ format_rupiah($kpi['total_modal']) }}
            </div>
            <p class="text-[10px] text-slate-400">
                Akumulasi harga awal modal
            </p>
        </div>

        {{-- Card 3: Estimasi Nilai Jual --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Estimasi Omzet Jual</span>
                <i class="fa-solid fa-tags text-xs text-indigo-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-indigo-700 tracking-tight font-mono">
                {{ format_rupiah($kpi['total_omzet']) }}
            </div>
            <p class="text-[10px] text-slate-400">
                Potensi pendapatan kotor
            </p>
        </div>

        {{-- Card 4: Potensi Laba Kotor (Gross Margin) --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Potensi Gross Profit</span>
                <i class="fa-solid fa-chart-line text-xs text-emerald-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-emerald-700 tracking-tight font-mono">
                +{{ format_rupiah($kpi['potensi_margin']) }}
            </div>
            <p class="text-[10px] text-slate-400">
                Selisih harga jual - modal
            </p>
        </div>
    </div>

    {{-- 4. Tabel Daftar Riwayat Barang Masuk --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Table Header --}}
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                    Log Pencatatan Barang Masuk
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Daftar seluruh helm yang ditambahkan ke stok fisik toko oleh staf kasir / gudang
                </p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                {{ $stokMasuks->total() }} Data Masuk
            </span>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium bg-slate-50/50">
                        <th class="py-3 px-4 font-medium">Waktu Masuk</th>
                        <th class="py-3 px-4 font-medium">Produk Helm</th>
                        <th class="py-3 px-4 font-medium">Varian</th>
                        <th class="py-3 px-4 font-medium text-center">Jumlah Masuk</th>
                        <th class="py-3 px-4 font-medium text-right">Modal Satuan</th>
                        <th class="py-3 px-4 font-medium text-right">Subtotal Modal</th>
                        <th class="py-3 px-4 font-medium text-right">Harga Jual</th>
                        <th class="py-3 px-4 font-medium">Petugas Penerima</th>
                        <th class="py-3 px-4 font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($stokMasuks as $item)
                        @php
                            $subtotalModal = $item->jumlah * ($item->harga_awal ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- 1. Tanggal & Waktu --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-medium text-slate-900 block">
                                    {{ $item->tgl_masuk ? $item->tgl_masuk->translatedFormat('d M Y') : '-' }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono block">
                                    {{ $item->tgl_masuk ? $item->tgl_masuk->format('H:i') . ' WIB' : '-' }}
                                </span>
                            </td>

                            {{-- 2. Produk Helm --}}
                            <td class="py-3 px-4 min-w-[160px]">
                                <span class="font-bold text-slate-900 block">
                                    {{ $item->helm->nama_helm ?? 'Helm #' . $item->id_helm }}
                                </span>
                                <span class="text-[11px] text-slate-400 block">
                                    {{ $item->helm->merk->nama_merk ?? '-' }} · {{ $item->helm->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>

                            {{-- 3. Varian --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">
                                    {{ $item->warna }} <span class="font-mono font-bold">({{ $item->ukuran }})</span>
                                </span>
                            </td>

                            {{-- 4. Jumlah Masuk --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i>
                                    <span>+{{ $item->jumlah }}</span>
                                </span>
                            </td>

                            {{-- 5. Harga Modal Satuan --}}
                            <td class="py-3 px-4 text-right font-mono text-slate-500 whitespace-nowrap">
                                {{ format_rupiah($item->harga_awal) }}
                            </td>

                            {{-- 6. Subtotal Modal --}}
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ format_rupiah($subtotalModal) }}
                            </td>

                            {{-- 7. Harga Jual --}}
                            <td class="py-3 px-4 text-right font-mono text-slate-700 whitespace-nowrap">
                                {{ format_rupiah($item->harga_jual) }}
                            </td>

                            {{-- 8. Petugas Penerima --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-slate-800 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($item->user->nama_lengkap ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900 text-xs">{{ $item->user->nama_lengkap ?? 'Staf Kasir' }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ '@' . ($item->user->username ?? 'user') }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- 9. Keterangan --}}
                            <td class="py-3 px-4 max-w-[200px] text-slate-500 text-xs">
                                <p class="truncate" title="{{ $item->keterangan }}">
                                    {{ $item->keterangan ?? 'Penambahan stok fisik gerai' }}
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-medium">Belum ada riwayat barang masuk untuk periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card List --}}
        <div class="md:hidden divide-y divide-slate-100">
            @forelse ($stokMasuks as $item)
                @php
                    $subtotalModal = $item->jumlah * ($item->harga_awal ?? 0);
                @endphp
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-bold text-xs text-slate-900 block">
                                {{ $item->helm->nama_helm ?? 'Helm #' . $item->id_helm }}
                            </span>
                            <span class="text-[11px] text-slate-400">
                                {{ $item->warna }} ({{ $item->ukuran }}) · {{ $item->tgl_masuk ? $item->tgl_masuk->translatedFormat('d M, H:i') : '-' }}
                            </span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            +{{ $item->jumlah }} Unit
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-lg text-xs font-mono">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-sans">Harga Modal:</span>
                            <span class="text-slate-700 font-medium">{{ format_rupiah($item->harga_awal) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-sans">Total Modal:</span>
                            <span class="font-bold text-slate-900">{{ format_rupiah($subtotalModal) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                        <span>Petugas: <strong class="text-slate-800">{{ $item->user->nama_lengkap ?? 'Kasir' }}</strong></span>
                        <span class="truncate max-w-[150px] text-slate-400 italic">{{ $item->keterangan }}</span>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 text-xs">
                    Belum ada riwayat barang masuk.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($stokMasuks->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $stokMasuks->links('partials.pagination-admin') }}
            </div>
        @endif
    </div>

</div>
@endsection
