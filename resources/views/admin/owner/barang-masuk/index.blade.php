@extends('layouts.admin')

@section('title', 'Riwayat Barang Masuk')
@section('page-title', 'Riwayat Barang Masuk')

@section('content')
<div class="space-y-4">

    {{-- 1. Header Ringkas --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Riwayat Barang Masuk
            </h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                {{ $stokMasuks->total() }} Catatan
            </span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.owner.laporan.penjualan') }}" 
               wire:navigate
               class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-file-lines text-[11px]"></i>
                <span>Laporan Penjualan</span>
            </a>
        </div>
    </div>

    {{-- 2. Ringkasan Restok & Nilai Modal --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- Card 1: Total Fisik Masuk --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Total Fisik Masuk</span>
                <i class="fa-solid fa-boxes-stacked text-xs text-blue-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight font-mono">
                +{{ number_format($kpi['total_unit']) }} <span class="text-xs font-normal text-slate-500">Unit</span>
            </div>
            <p class="text-[11px] text-slate-500">
                {{ $kpi['total_frekuensi'] }}x pencatatan restok
            </p>
        </div>

        {{-- Card 2: Total Modal Masuk --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Total Modal Masuk</span>
                <i class="fa-solid fa-coins text-xs text-amber-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight font-mono">
                {{ format_rupiah($kpi['total_modal']) }}
            </div>
            <p class="text-[11px] text-slate-500">
                Total belanja modal stok
            </p>
        </div>

        {{-- Card 3: Estimasi Nilai Jual --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Estimasi Nilai Jual</span>
                <i class="fa-solid fa-tags text-xs text-indigo-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-indigo-700 tracking-tight font-mono">
                {{ format_rupiah($kpi['total_omzet']) }}
            </div>
            <p class="text-[11px] text-slate-500">
                Potensi nilai jual ritel
            </p>
        </div>

        {{-- Card 4: Potensi Margin Kotor --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Potensi Margin</span>
                <i class="fa-solid fa-chart-line text-xs text-emerald-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-emerald-700 tracking-tight font-mono">
                +{{ format_rupiah($kpi['potensi_margin']) }}
            </div>
            <p class="text-[11px] text-slate-500">
                Selisih jual terhadap modal
            </p>
        </div>
    </div>

    {{-- 3. Toolbar Filter Terpadu --}}
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-2xs space-y-3">
        {{-- Tab Periode Cepat --}}
        <div class="flex items-center gap-1.5 text-xs overflow-x-auto pb-1 sm:pb-0">
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

        {{-- Form Filter Kategori & Search --}}
        <form method="GET" action="{{ route('admin.owner.barang-masuk.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-2 text-xs pt-2 border-t border-slate-100">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">

            {{-- Filter Merk --}}
            <div class="sm:col-span-3">
                <select name="id_merk" class="w-full h-8.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                    <option value="all">Semua Merk</option>
                    @foreach ($merks as $m)
                        <option value="{{ $m->id_merk }}" {{ $selectedMerk == $m->id_merk ? 'selected' : '' }}>
                            {{ $m->nama_merk }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori --}}
            <div class="sm:col-span-3">
                <select name="id_kategori" class="w-full h-8.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                    <option value="all">Semua Kategori</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id_kategori }}" {{ $selectedKategori == $k->id_kategori ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Petugas --}}
            <div class="sm:col-span-2">
                <select name="user_id" class="w-full h-8.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                    <option value="all">Semua Petugas</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id_user }}" {{ $selectedUser == $u->id_user ? 'selected' : '' }}>
                            {{ $u->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Search Box --}}
            <div class="sm:col-span-3 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari helm / varian / keterangan..." 
                       class="w-full h-8.5 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition-colors">
            </div>

            {{-- Tombol Cari & Reset --}}
            <div class="sm:col-span-1 flex items-center gap-1.5">
                <button type="submit" class="w-full h-8.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs transition-colors cursor-pointer inline-flex items-center justify-center">
                    Cari
                </button>
                @if(request()->hasAny(['search', 'id_merk', 'id_kategori', 'user_id']) && (request('id_merk') !== 'all' || request('id_kategori') !== 'all' || request('user_id') !== 'all' || request('search')))
                    <a href="{{ route('admin.owner.barang-masuk.index', ['period' => $selectedPeriod]) }}" 
                       wire:navigate 
                       class="h-8.5 px-2.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs inline-flex items-center justify-center transition-colors"
                       title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. Tabel Daftar Riwayat Barang Masuk --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Table Title (Log Pencatatan Barang Masuk) --}}
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                Log Pencatatan Barang Masuk
            </h2>
            <span class="text-xs text-slate-500 font-mono">
                Total Restok: +{{ number_format($kpi['total_unit']) }} Unit
            </span>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 text-[11px] font-semibold bg-slate-50/70">
                        <th class="py-3 px-4">Waktu Masuk</th>
                        <th class="py-3 px-4">Produk Helm</th>
                        <th class="py-3 px-4">Varian</th>
                        <th class="py-3 px-4 text-center">Jumlah Masuk</th>
                        <th class="py-3 px-4 text-right">Modal Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal Modal</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4">Petugas Penerima</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($stokMasuks as $item)
                        @php
                            $subtotalModal = $item->jumlah * ($item->harga_awal ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
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
                                <span class="font-semibold text-slate-900 block">
                                    {{ $item->helm->nama_helm ?? 'Helm #' . $item->id_helm }}
                                </span>
                                <span class="text-[11px] text-slate-400 block">
                                    {{ $item->helm->merk->nama_merk ?? '-' }} &bull; {{ $item->helm->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>

                            {{-- 3. Varian --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-medium">
                                    {{ $item->warna }} <span class="font-mono">({{ $item->ukuran }})</span>
                                </span>
                            </td>

                            {{-- 4. Jumlah Masuk --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span>+{{ $item->jumlah }} Unit</span>
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

                            {{-- 8. Petugas Penerima (Clean Text) --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-medium text-slate-800">{{ $item->user->nama_lengkap ?? 'Staf Kasir' }}</span>
                            </td>

                            {{-- 9. Keterangan --}}
                            <td class="py-3 px-4 max-w-[200px] text-slate-500 text-xs">
                                <span class="truncate block" title="{{ $item->keterangan }}">
                                    {{ $item->keterangan ?? 'Restok gerai' }}
                                </span>
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
                                {{ $item->warna }} ({{ $item->ukuran }}) &bull; {{ $item->tgl_masuk ? $item->tgl_masuk->translatedFormat('d M, H:i') : '-' }}
                            </span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
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
                        <span>Penerima: <strong class="text-slate-800">{{ $item->user->nama_lengkap ?? 'Kasir' }}</strong></span>
                        <span class="truncate max-w-[150px] text-slate-400 italic">{{ $item->keterangan ?? '-' }}</span>
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