@extends('layouts.admin')

@section('title', 'Riwayat Barang Masuk')
@section('page-title', 'Riwayat Barang Masuk')

@section('content')
<div class="space-y-4">

    {{-- 1. Header Ringkas --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Riwayat Barang Masuk
        </h1>

        <span class="text-xs text-slate-500 font-medium">
            {{ $stokMasuks->total() }} riwayat
        </span>
    </div>

    {{-- 2. Ringkasan Restok & Nilai Modal --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
        {{-- Card 1: Total Fisik Masuk --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Total Fisik Masuk</span>
                <i class="fa-solid fa-boxes-stacked text-xs text-blue-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight tabular-nums">
                +{{ number_format($kpi['total_unit']) }} <span class="text-xs font-normal text-slate-500">unit</span>
            </div>
            <p class="text-[11px] text-slate-400">
                {{ $kpi['total_frekuensi'] }} kali restok
            </p>
        </div>

        {{-- Card 2: Total Modal Masuk --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Total Modal Masuk</span>
                <i class="fa-solid fa-coins text-xs text-amber-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight tabular-nums">
                Rp {{ number_format($kpi['total_modal'], 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400">
                Biaya modal
            </p>
        </div>

        {{-- Card 3: Estimasi Nilai Jual --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Estimasi Nilai Jual</span>
                <i class="fa-solid fa-tags text-xs text-indigo-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-indigo-700 tracking-tight tabular-nums">
                Rp {{ number_format($kpi['total_omzet'], 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400">
                Nilai jual
            </p>
        </div>

        {{-- Card 4: Potensi Margin --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Potensi Margin</span>
                <i class="fa-solid fa-chart-line text-xs text-emerald-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-emerald-700 tracking-tight tabular-nums">
                +Rp {{ number_format($kpi['potensi_margin'], 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400">
                Estimasi laba
            </p>
        </div>
    </div>

    {{-- 3. Toolbar Filter Terpadu --}}
    <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs space-y-2.5">
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
                    'all'        => 'Semua',
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

        {{-- Form Filter Kategori, Merk, Petugas & Search --}}
        <form method="GET" action="{{ route('admin.owner.barang-masuk.index') }}" class="grid grid-cols-2 sm:grid-cols-12 gap-2 text-xs pt-2 border-t border-slate-100">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">

            {{-- Filter Merk --}}
            <div class="col-span-1 sm:col-span-3">
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
            <div class="col-span-1 sm:col-span-3">
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
            <div class="col-span-1 sm:col-span-2">
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
            <div class="col-span-1 sm:col-span-3 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari helm, varian, atau catatan..." 
                       class="w-full h-8.5 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition-colors">
            </div>

            {{-- Tombol Cari & Reset --}}
            <div class="col-span-2 sm:col-span-1 flex items-center gap-1.5">
                <button type="submit" class="flex-1 h-8.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs transition-colors cursor-pointer inline-flex items-center justify-center">
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

    {{-- 4. Daftar Riwayat Barang Masuk (Minimalist Text Ledger) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Ledger Header --}}
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
            <span class="font-semibold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-slate-400 text-[11px]"></i>
                <span>Log Pencatatan Barang Masuk</span>
            </span>
            <span class="text-slate-500 font-medium text-[11px]">
                Total: +{{ number_format($kpi['total_unit']) }} unit
            </span>
        </div>

        {{-- Ledger List Items (100% Anti-Patah & Tipografi Murni Tanpa Background Badge) --}}
        <div class="divide-y divide-slate-100">
            @forelse ($stokMasuks as $item)
                @php
                    $subtotalModal = $item->jumlah * ($item->harga_awal ?? 0);
                @endphp
                <div class="px-3.5 sm:px-4 py-3 sm:py-3.5 hover:bg-slate-50/70 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        {{-- Kolom Kiri: Helm, Varian, Petugas & Waktu --}}
                        <div class="min-w-0 flex-1 space-y-0.5">
                            {{-- Baris 1: Nama Helm & Merk --}}
                            <div class="text-xs sm:text-sm font-semibold text-slate-900 truncate">
                                {{ $item->helm->nama_helm ?? 'Helm #' . $item->id_helm }}
                                @if($item->helm && $item->helm->merk)
                                    <span class="text-slate-400 font-normal">· {{ $item->helm->merk->nama_merk }}</span>
                                @endif
                                @if($item->helm && $item->helm->kategori)
                                    <span class="text-slate-400 font-normal">({{ $item->helm->kategori->nama_kategori }})</span>
                                @endif
                            </div>

                            {{-- Baris 2: Varian & Keterangan --}}
                            <div class="text-[11px] sm:text-xs text-slate-600 truncate">
                                <span class="font-medium">{{ $item->warna }}</span>, <span>{{ $item->ukuran }}</span>
                                @if($item->keterangan)
                                    <span class="text-slate-300 mx-1">·</span>
                                    <span class="text-slate-500 font-normal">{{ $item->keterangan }}</span>
                                @endif
                            </div>

                            {{-- Baris 3: Tanggal/Waktu & Petugas Penerima --}}
                            <div class="text-[10px] sm:text-[11px] text-slate-400 truncate pt-0.5">
                                <span>{{ $item->tgl_masuk ? $item->tgl_masuk->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
                                <span class="text-slate-300 mx-1">·</span>
                                <span>Oleh: {{ $item->user->nama_lengkap ?? 'Kasir' }}</span>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Kuantitas Masuk, Nilai Modal & Rincian Harga --}}
                        <div class="text-right shrink-0 space-y-0.5 pl-2">
                            {{-- Baris 1: Jumlah Fisik Masuk --}}
                            <div class="text-xs sm:text-sm font-bold text-emerald-600 tabular-nums">
                                +{{ number_format($item->jumlah) }} unit
                            </div>

                            {{-- Baris 2: Subtotal Belanja Modal --}}
                            <div class="text-xs sm:text-sm font-bold text-slate-900 tabular-nums">
                                Rp {{ number_format($subtotalModal, 0, ',', '.') }}
                            </div>

                            {{-- Baris 3: Harga Modal Satuan vs Harga Jual --}}
                            <div class="text-[10px] sm:text-[11px] text-slate-400">
                                <span>Modal: Rp {{ number_format($item->harga_awal, 0, ',', '.') }}</span>
                                <span class="text-slate-300 mx-0.5">·</span>
                                <span>Jual: Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400 space-y-1">
                    <i class="fa-solid fa-inbox text-2xl text-slate-300 block mb-1"></i>
                    <p class="text-xs font-medium text-slate-600">Tidak ada riwayat barang masuk</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 5. Pagination --}}
    @if ($stokMasuks->hasPages())
        <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs">
            {{ $stokMasuks->links('partials.pagination-admin') }}
        </div>
    @endif

</div>
@endsection
