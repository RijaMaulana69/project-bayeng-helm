@extends('layouts.admin')

@section('title', 'Transaksi Karyawan')
@section('page-title', 'Transaksi Karyawan')

@section('content')
<div class="space-y-4" x-data="{ showFilter: false }">

    {{-- 1. Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Transaksi Karyawan
        </h1>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" 
                    @click="showFilter = !showFilter"
                    class="h-9 px-3.5 rounded-lg border border-slate-200/90 hover:bg-slate-50 text-slate-700 font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                <i class="fa-solid fa-filter text-[11px] text-slate-500"></i>
                <span>Filter Lanjutan</span>
                @if($selectedKaryawan !== 'all' || $selectedStatus !== 'all' || $selectedMetode !== 'all')
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                @endif
            </button>

            <a href="{{ route('admin.owner.laporan.penjualan') }}" 
               wire:navigate
               class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-file-invoice-dollar text-[11px]"></i>
                <span>Laporan Omzet</span>
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
                <a href="{{ route('admin.owner.transaksi-karyawan.index', array_merge(request()->except(['period', 'page', 'start_date', 'end_date']), ['period' => $key])) }}"
                   wire:navigate
                   class="px-2.5 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-all {{ $selectedPeriod === $key ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Search Input --}}
        <form method="GET" action="{{ route('admin.owner.transaksi-karyawan.index') }}" class="w-full sm:w-72 flex items-center gap-1.5">
            @foreach (request()->except(['search', 'page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari no. transaksi / pelanggan / kasir..." 
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
        <form method="GET" action="{{ route('admin.owner.transaksi-karyawan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            {{-- Filter Karyawan --}}
            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Pilih Staf Kasir</label>
                <select name="karyawan" class="w-full h-9 px-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-slate-400">
                    <option value="all">Semua Karyawan</option>
                    @foreach ($karyawanUsers as $kUser)
                        <option value="{{ $kUser->username }}" {{ $selectedKaryawan === $kUser->username ? 'selected' : '' }}>
                            {{ $kUser->nama_lengkap }} ({{ $kUser->username }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Status Pesanan</label>
                <select name="status" class="w-full h-9 px-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-slate-400">
                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Selesai (Diambil)</option>
                    <option value="ready" {{ $selectedStatus === 'ready' ? 'selected' : '' }}>Siap Diambil di Gerai</option>
                    <option value="processing" {{ $selectedStatus === 'processing' ? 'selected' : '' }}>Sedang Disiapkan</option>
                    <option value="paid" {{ $selectedStatus === 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                    <option value="unpaid_pickup" {{ $selectedStatus === 'unpaid_pickup' ? 'selected' : '' }}>Bayar di Kasir</option>
                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Menunggu Bayar</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            {{-- Filter Metode Pembayaran --}}
            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Metode Bayar</label>
                <select name="metode_bayar" class="w-full h-9 px-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-slate-400">
                    <option value="all" {{ $selectedMetode === 'all' ? 'selected' : '' }}>Semua Metode</option>
                    <option value="tunai" {{ $selectedMetode === 'tunai' ? 'selected' : '' }}>Tunai di Gerai</option>
                    <option value="transfer" {{ $selectedMetode === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ $selectedMetode === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="ewallet" {{ $selectedMetode === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
            </div>

            {{-- Submit Filter --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 h-9 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs transition-colors cursor-pointer inline-flex items-center justify-center gap-1">
                    <i class="fa-solid fa-check text-[11px]"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.owner.transaksi-karyawan.index') }}" wire:navigate class="h-9 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs inline-flex items-center justify-center transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 3. Summary Metric Cards (Executive Oversight) --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- Card 1: Total Omzet Selesai --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Omzet Kasir Selesai</span>
                <i class="fa-solid fa-sack-dollar text-xs text-emerald-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight font-mono">
                {{ format_rupiah($kpi['total_omzet']) }}
            </div>
            <p class="text-[10px] text-slate-400">
                Dari {{ $kpi['completed_count'] }} transaksi tuntas
            </p>
        </div>

        {{-- Card 2: Setoran Tunai Kasir (Fisik Laci) --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Uang Tunai Laci</span>
                <i class="fa-solid fa-money-bill-wave text-xs text-amber-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-amber-700 tracking-tight font-mono">
                {{ format_rupiah($kpi['tunai_total']) }}
            </div>
            <p class="text-[10px] text-slate-400">
                {{ $kpi['tunai_count'] }} transaksi pembayaran tunai
            </p>
        </div>

        {{-- Card 3: Pembayaran Online / QRIS --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Online / Non-Tunai</span>
                <i class="fa-solid fa-qrcode text-xs text-blue-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-blue-700 tracking-tight font-mono">
                {{ format_rupiah($kpi['online_total']) }}
            </div>
            <p class="text-[10px] text-slate-400">
                {{ $kpi['online_count'] }} transaksi non-tunai
            </p>
        </div>

        {{-- Card 4: Total Volume Transaksi --}}
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Transaksi</span>
                <i class="fa-solid fa-receipt text-xs text-slate-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight font-mono">
                {{ $kpi['total_transaksi'] }} <span class="text-xs font-normal text-slate-500">Order</span>
            </div>
            <p class="text-[10px] text-slate-400">
                {{ $kpi['pending_count'] }} dalam proses berjalan
            </p>
        </div>
    </div>

    {{-- 4. Kinerja Ringkas Kasir (Leaderboard / Staf Gerai) --}}
    @if(count($cashierStats) > 0)
    <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-users text-blue-600 text-xs"></i>
                <span>Aktivitas Staf Kasir</span>
            </h3>
            <span class="text-[11px] text-slate-400">Total {{ count($cashierStats) }} staf kasir terdaftar</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
            @foreach ($cashierStats as $stat)
                @php
                    $isFiltered = ($selectedKaryawan === $stat['user']->username);
                @endphp
                <a href="{{ route('admin.owner.transaksi-karyawan.index', array_merge(request()->except(['karyawan', 'page']), ['karyawan' => $stat['user']->username])) }}"
                   wire:navigate
                   class="p-3 rounded-lg border transition-all flex items-center justify-between gap-2.5 {{ $isFiltered ? 'bg-blue-50/50 border-blue-300 ring-1 ring-blue-400' : 'bg-slate-50/60 border-slate-200/70 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($stat['user']->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate">
                                {{ $stat['user']->nama_lengkap }}
                            </p>
                            <p class="text-[10px] text-slate-400 font-mono truncate">
                                {{ '@' . $stat['user']->username }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-bold text-slate-900 font-mono block">
                            {{ $stat['selesai_count'] }}x Selesai
                        </span>
                        <span class="text-[10px] text-emerald-600 font-mono font-medium block">
                            {{ format_rupiah($stat['total_omzet']) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 5. Tabel Daftar Transaksi --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Table Header --}}
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                    Daftar Transaksi Karyawan
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Menampilkan rincian transaksi gerai beserta staf kasir penanggung jawab
                </p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                {{ $pesanans->total() }} Transaksi
            </span>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium bg-slate-50/50">
                        <th class="py-3 px-4 font-medium">No. Transaksi</th>
                        <th class="py-3 px-4 font-medium">Staf Kasir</th>
                        <th class="py-3 px-4 font-medium">Pelanggan</th>
                        <th class="py-3 px-4 font-medium">Item Produk</th>
                        <th class="py-3 px-4 font-medium">Pembayaran</th>
                        <th class="py-3 px-4 font-medium text-right">Nominal</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pesanans as $pesanan)
                        @php
                            // Ambil log staf kasir terakhir yang mengubah status
                            $staffLog = $pesanan->logs->first(function($l) {
                                return !in_array(strtolower($l->diubah_oleh ?? ''), ['pelanggan', 'system', '']);
                            });
                            $kasirUser = $staffLog ? ($usersByUsername[$staffLog->diubah_oleh] ?? null) : null;
                            $kasirNama = $kasirUser ? $kasirUser->nama_lengkap : ($staffLog->diubah_oleh ?? 'Kasir Gerai');
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- 1. No Transaksi & Kode Pickup --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                                   wire:navigate
                                   class="font-medium text-slate-900 hover:text-blue-600 transition-colors block">
                                    {{ $pesanan->no_pesanan }}
                                </a>
                                <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5 font-mono">
                                    <span>{{ $pesanan->kode_pickup ?? '-' }}</span>
                                    <span class="text-slate-300">·</span>
                                    <span>{{ $pesanan->created_at->translatedFormat('d M, H:i') }}</span>
                                </div>
                            </td>

                            {{-- 2. Staf Kasir --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($kasirNama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-slate-900 truncate text-xs">{{ $kasirNama }}</p>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $staffLog->diubah_oleh ?? 'kasir' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. Pelanggan --}}
                            <td class="py-3.5 px-4 max-w-[160px]">
                                <p class="font-medium text-slate-900 truncate">
                                    {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Walk-in') }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono truncate mt-0.5">
                                    {{ $pesanan->telepon ?? ($pesanan->pelanggan->telepon ?? '-') }}
                                </p>
                            </td>

                            {{-- 4. Item Produk --}}
                            <td class="py-3.5 px-4 max-w-[200px]">
                                @php
                                    $firstDetail = $pesanan->details->first();
                                    $otherCount = $pesanan->details->count() - 1;
                                @endphp
                                @if($firstDetail)
                                    <p class="text-slate-800 truncate">
                                        <span class="font-medium text-slate-900">{{ $firstDetail->qty }}x</span> {{ $firstDetail->helm->nama_helm ?? 'Helm' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                        {{ $firstDetail->warna ?? '-' }}, {{ $firstDetail->ukuran ?? '-' }}
                                        @if($otherCount > 0)
                                            <span class="text-blue-600 font-medium">+{{ $otherCount }} lainnya</span>
                                        @endif
                                    </p>
                                @else
                                    <span class="text-slate-400 italic">Tanpa rincian</span>
                                @endif
                            </td>

                            {{-- 5. Pembayaran --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($pesanan->metode_bayar === 'tunai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        <i class="fa-solid fa-money-bill-wave text-[10px]"></i>
                                        <span>Tunai Gerai</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">
                                        <i class="fa-solid fa-credit-card text-[10px]"></i>
                                        <span>{{ ucfirst($pesanan->metode_bayar) }}</span>
                                    </span>
                                @endif
                            </td>

                            {{-- 6. Nominal Total --}}
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900 font-mono whitespace-nowrap">
                                {{ format_rupiah($pesanan->grand_total) }}
                            </td>

                            {{-- 7. Status --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $badge = match($pesanan->status) {
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'ready' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'processing' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'paid' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'unpaid_pickup' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'pending' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium border {{ $badge }}">
                                    {{ $pesanan->status_label }}
                                </span>
                            </td>

                            {{-- 8. Aksi Detail --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                                   wire:navigate
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 rounded-lg transition-colors">
                                    <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                                    <span>Audit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-medium">Tidak ada transaksi ditemukan untuk filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Responsive Card List --}}
        <div class="md:hidden divide-y divide-slate-100">
            @forelse ($pesanans as $pesanan)
                @php
                    $staffLog = $pesanan->logs->first(function($l) {
                        return !in_array(strtolower($l->diubah_oleh ?? ''), ['pelanggan', 'system', '']);
                    });
                    $kasirUser = $staffLog ? ($usersByUsername[$staffLog->diubah_oleh] ?? null) : null;
                    $kasirNama = $kasirUser ? $kasirUser->nama_lengkap : ($staffLog->diubah_oleh ?? 'Kasir Gerai');
                    $firstDetail = $pesanan->details->first();
                @endphp
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" wire:navigate class="font-bold text-xs text-slate-900 hover:text-blue-600 block">
                                {{ $pesanan->no_pesanan }}
                            </a>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $pesanan->kode_pickup }} · {{ $pesanan->created_at->translatedFormat('d M, H:i') }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-900 font-mono">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 p-2 rounded-lg">
                        <span class="text-[11px] text-slate-500">Staf Kasir:</span>
                        <span class="font-semibold text-slate-800">{{ $kasirNama }}</span>
                    </div>

                    @if($firstDetail)
                    <div class="text-[11px] text-slate-500">
                        <span>{{ $firstDetail->qty }}x {{ $firstDetail->helm->nama_helm ?? 'Helm' }}</span>
                        <span class="text-slate-400">({{ $firstDetail->warna }}, {{ $firstDetail->ukuran }})</span>
                    </div>
                    @endif

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-[11px] font-medium text-slate-600">
                            {{ $pesanan->metode_bayar === 'tunai' ? '💵 Tunai Gerai' : '💳 ' . ucfirst($pesanan->metode_bayar) }}
                        </span>
                        <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" wire:navigate class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg">
                            Audit Transaksi
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 text-xs">
                    Tidak ada transaksi ditemukan.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($pesanans->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $pesanans->links('partials.pagination-admin') }}
            </div>
        @endif
    </div>

</div>
@endsection
