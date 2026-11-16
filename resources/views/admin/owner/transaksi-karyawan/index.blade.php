@extends('layouts.admin')

@section('title', 'Transaksi Gerai')
@section('page-title', 'Transaksi Gerai')

@section('content')
<div class="space-y-4">

    {{-- 1. Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Transaksi Gerai
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pengawasan arus transaksi kasir dan pembayaran gerai
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.owner.laporan.penjualan') }}" 
               wire:navigate
               class="h-8.5 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors">
                <i class="fa-solid fa-file-lines text-[11px]"></i>
                <span>Laporan Penjualan</span>
            </a>
        </div>
    </div>

    {{-- 2. Ringkasan Kas --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
        {{-- Card 1: Omzet Kasir Selesai --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Omzet Kasir Selesai</span>
                <i class="fa-solid fa-circle-check text-xs text-emerald-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight tabular-nums">
                {{ number_format($kpi['total_omzet'], 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500">
                {{ $kpi['completed_count'] }} transaksi tuntas
            </p>
        </div>

        {{-- Card 2: Uang Tunai Laci --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Uang Tunai Laci</span>
                <i class="fa-solid fa-money-bill-wave text-xs text-amber-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-amber-700 tracking-tight tabular-nums">
                {{ number_format($kpi['tunai_total'], 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500">
                {{ $kpi['tunai_count'] }} transaksi tunai
            </p>
        </div>

        {{-- Card 3: Online / Non-Tunai --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Online / Non-Tunai</span>
                <i class="fa-solid fa-qrcode text-xs text-blue-600"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-blue-700 tracking-tight tabular-nums">
                {{ number_format($kpi['online_total'], 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500">
                {{ $kpi['online_count'] }} transaksi non-tunai
            </p>
        </div>

        {{-- Card 4: Total Transaksi --}}
        <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Total Transaksi</span>
                <i class="fa-solid fa-clock text-xs text-slate-500"></i>
            </div>
            <div class="text-base sm:text-xl font-bold text-slate-900 tracking-tight tabular-nums">
                {{ $kpi['total_transaksi'] }} <span class="text-xs font-normal text-slate-500">Order</span>
            </div>
            <p class="text-[11px] text-slate-500">
                {{ $kpi['pending_count'] }} dalam proses
            </p>
        </div>
    </div>

    {{-- 3. Filter Kasir (Clean Horizontal Scrollable Pills) --}}
    @if(count($cashierStats) > 0)
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Kasir:</span>
        
        {{-- Semua Kasir --}}
        <a href="{{ route('admin.owner.transaksi-karyawan.index', array_merge(request()->except(['karyawan', 'page']), ['karyawan' => 'all'])) }}"
           wire:navigate
           class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-all shrink-0 {{ $selectedKaryawan === 'all' || empty($selectedKaryawan) ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            Semua Kasir
        </a>

        @foreach ($cashierStats as $stat)
            @php
                $isFiltered = ($selectedKaryawan === $stat['user']->username);
            @endphp
            <a href="{{ route('admin.owner.transaksi-karyawan.index', array_merge(request()->except(['karyawan', 'page']), ['karyawan' => $stat['user']->username])) }}"
               wire:navigate
               class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-all shrink-0 {{ $isFiltered ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ $stat['user']->nama_lengkap }}
            </a>
        @endforeach
    </div>
    @endif

    {{-- 4. Toolbar Periode & Filter --}}
    <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs space-y-2.5">
        {{-- Tab Periode --}}
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
                <a href="{{ route('admin.owner.transaksi-karyawan.index', array_merge(request()->except(['period', 'page', 'start_date', 'end_date']), ['period' => $key])) }}"
                   wire:navigate
                   class="px-2.5 py-1 rounded-lg text-xs font-medium whitespace-nowrap transition-all {{ $selectedPeriod === $key ? 'bg-slate-900 text-white font-semibold shadow-2xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Dropdown Status, Metode & Search --}}
        <form method="GET" action="{{ route('admin.owner.transaksi-karyawan.index') }}" class="grid grid-cols-2 sm:grid-cols-12 gap-2 text-xs pt-2 border-t border-slate-100">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">
            @if($selectedKaryawan !== 'all')
                <input type="hidden" name="karyawan" value="{{ $selectedKaryawan }}">
            @endif

            {{-- Filter Status --}}
            <div class="col-span-1 sm:col-span-3">
                <select name="status" class="w-full h-8.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="ready" {{ $selectedStatus === 'ready' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="processing" {{ $selectedStatus === 'processing' ? 'selected' : '' }}>Sedang Disiapkan</option>
                    <option value="paid" {{ $selectedStatus === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                    <option value="unpaid_pickup" {{ $selectedStatus === 'unpaid_pickup' ? 'selected' : '' }}>Menunggu Diambil</option>
                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            {{-- Filter Metode Bayar --}}
            <div class="col-span-1 sm:col-span-3">
                <select name="metode_bayar" class="w-full h-8.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                    <option value="all" {{ $selectedMetode === 'all' ? 'selected' : '' }}>Semua Metode</option>
                    <option value="tunai" {{ $selectedMetode === 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ $selectedMetode === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ $selectedMetode === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="ewallet" {{ $selectedMetode === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
            </div>

            {{-- Search Box --}}
            <div class="col-span-2 sm:col-span-4 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari order, pelanggan..." 
                       class="w-full h-8.5 pl-9 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition-colors">
            </div>

            {{-- Tombol Terapkan & Reset --}}
            <div class="col-span-2 sm:col-span-2 flex items-center gap-1.5">
                <button type="submit" class="flex-1 h-8.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs transition-colors cursor-pointer inline-flex items-center justify-center">
                    Cari
                </button>
                @if(request()->hasAny(['search', 'status', 'metode_bayar', 'karyawan']) && (request('status') !== 'all' || request('metode_bayar') !== 'all' || request('karyawan') !== 'all' || request('search')))
                    <a href="{{ route('admin.owner.transaksi-karyawan.index', ['period' => $selectedPeriod]) }}" 
                       wire:navigate 
                       class="h-8.5 px-2.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs inline-flex items-center justify-center transition-colors"
                       title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 5. Mobile-First POS Transaction Feed --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Feed Header --}}
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
            <span class="font-semibold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-slate-400 text-[11px]"></i>
                <span>Daftar Transaksi Gerai</span>
            </span>
            <span class="text-slate-400 text-[11px]">
                {{ $pesanans->total() }} order
            </span>
        </div>

        {{-- Feed List (Minimalist Text Ledger ala Apple Wallet / Revolut) --}}
        <div class="divide-y divide-slate-100">
            @forelse ($pesanans as $pesanan)
                @php
                    $staffLog = $pesanan->logs->first(function($l) {
                        return !in_array(strtolower($l->diubah_oleh ?? ''), ['pelanggan', 'system', '']);
                    });
                    $kasirUser = $staffLog ? ($usersByUsername[$staffLog->diubah_oleh] ?? null) : null;
                    $kasirNama = $kasirUser ? $kasirUser->nama_lengkap : ($staffLog->diubah_oleh ?? 'Ari Akbar');
                    $firstDetail = $pesanan->details->first();
                    $otherCount = $pesanan->details->count() - 1;

                    $statusColor = match($pesanan->status) {
                        'completed' => 'text-emerald-600',
                        'ready' => 'text-blue-600',
                        'processing' => 'text-amber-600',
                        'paid' => 'text-sky-600',
                        'unpaid_pickup' => 'text-amber-600',
                        'pending' => 'text-slate-500',
                        'cancelled' => 'text-rose-600',
                        default => 'text-slate-500',
                    };
                @endphp
                <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                   wire:navigate
                   class="group block px-3.5 sm:px-4 py-3 sm:py-3.5 hover:bg-slate-50/70 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        {{-- Kolom Kiri: Pelanggan, Produk, No. Order & Kasir (Anti-Patah dengan Truncate) --}}
                        <div class="min-w-0 flex-1 space-y-0.5">
                            {{-- Baris 1: Nama Pelanggan --}}
                            <div class="text-xs sm:text-sm font-semibold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Langsung') }}
                            </div>

                            {{-- Baris 2: Rincian Produk Helm --}}
                            <div class="text-[11px] sm:text-xs text-slate-600 truncate">
                                {{ $firstDetail ? $firstDetail->qty . 'x ' . ($firstDetail->helm->nama_helm ?? 'Helm') : 'Tanpa item' }}
                                @if($firstDetail)
                                    <span class="text-slate-400 font-normal">({{ $firstDetail->warna }}, {{ $firstDetail->ukuran }})</span>
                                @endif
                                @if($otherCount > 0)
                                    <span class="text-[10px] font-medium text-blue-600 ml-0.5">+{{ $otherCount }} item</span>
                                @endif
                            </div>

                            {{-- Baris 3: No. Order, Waktu, dan Nama Kasir --}}
                            <div class="text-[10px] sm:text-[11px] text-slate-400 truncate pt-0.5">
                                <span>{{ $pesanan->no_pesanan }}</span>
                                <span class="mx-1 text-slate-300">·</span>
                                <span>{{ $pesanan->created_at->translatedFormat('d M, H:i') }}</span>
                                <span class="mx-1 text-slate-300">·</span>
                                <span>Kasir: {{ $kasirNama }}</span>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Nominal Bersih, Status Teks Murni & Metode Bayar (Shrink-0) --}}
                        <div class="text-right shrink-0 space-y-0.5 pl-2">
                            {{-- Baris 1: Nominal Transaksi (Tabular Nums, Tebal, Bersih) --}}
                            <div class="text-xs sm:text-sm font-bold text-slate-900 tabular-nums">
                                {{ number_format($pesanan->grand_total, 0, ',', '.') }}
                            </div>

                            {{-- Baris 2: Status Teks Murni (Tanpa Background Badge yang Mengganggu) --}}
                            <div class="text-[10px] sm:text-[11px] font-semibold {{ $statusColor }}">
                                {{ $pesanan->status_label }}
                            </div>

                            {{-- Baris 3: Metode Bayar Teks Netral (QRIS, Tunai, Transfer, E-Wallet) --}}
                            <div class="text-[10px] sm:text-[11px] text-slate-500 font-medium">
                                {{ $pesanan->metode_bayar_label }}
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-14 text-center text-slate-400 space-y-2">
                    <div class="w-11 h-11 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </div>
                    <p class="text-xs font-medium text-slate-600">Tidak ada transaksi ditemukan</p>
                    <p class="text-[11px] text-slate-400">Coba ubah filter periode atau pencarian Anda</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 6. Pagination --}}
    @if ($pesanans->hasPages())
        <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs">
            {{ $pesanans->links('partials.pagination-admin') }}
        </div>
    @endif

</div>
@endsection
