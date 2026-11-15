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
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari order, pelanggan..." 
                       class="w-full h-8.5 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition-colors">
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

        {{-- Feed List (Sleek Tappable Items) --}}
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

                    $badge = match($pesanan->status) {
                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                        'ready' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                        'processing' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                        'paid' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                        'unpaid_pickup' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                        'pending' => 'bg-slate-100 text-slate-700 border-slate-200/80',
                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                        default => 'bg-slate-100 text-slate-700 border-slate-200/80',
                    };

                    $metodeBadge = match($pesanan->metode_bayar) {
                        'qris' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                        'ewallet' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                        'tunai' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                        'transfer' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                        default => 'bg-slate-100 text-slate-700 border-slate-200/80',
                    };
                @endphp
                <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                   wire:navigate
                   class="group block px-4 py-3 hover:bg-slate-50/70 transition-colors">
                    <div class="flex items-center justify-between gap-3">
                        {{-- Left Info: Order, Pelanggan, Produk, Kasir & Metode --}}
                        <div class="min-w-0 flex-1 space-y-1">
                            {{-- Row A: Order ID, Waktu & Badge Status --}}
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-xs sm:text-sm text-slate-900 group-hover:text-blue-600 transition-colors">
                                    {{ $pesanan->no_pesanan }}
                                </span>
                                <span class="text-slate-300 text-xs">·</span>
                                <span class="text-[11px] text-slate-400">
                                    {{ $pesanan->created_at->translatedFormat('d M, H:i') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badge }}">
                                    {{ $pesanan->status_label }}
                                </span>
                            </div>

                            {{-- Row B: Pelanggan & Rincian Helm --}}
                            <div class="flex items-center gap-1.5 text-xs text-slate-700 truncate">
                                <span class="font-medium text-slate-900 truncate">
                                    {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Langsung') }}
                                </span>
                                <span class="text-slate-300">·</span>
                                <span class="text-slate-600 truncate">
                                    {{ $firstDetail ? $firstDetail->qty . 'x ' . ($firstDetail->helm->nama_helm ?? 'Helm') : 'Tanpa item' }}
                                    @if($firstDetail)
                                        <span class="text-slate-400">({{ $firstDetail->warna }}, {{ $firstDetail->ukuran }})</span>
                                    @endif
                                </span>
                                @if($otherCount > 0)
                                    <span class="text-[10px] font-medium text-blue-600 shrink-0">+{{ $otherCount }} lainnya</span>
                                @endif
                            </div>

                            {{-- Row C: Kasir & Metode Bayar --}}
                            <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                <span>Kasir: <strong class="text-slate-700 font-medium">{{ $kasirNama }}</strong></span>
                                <span class="text-slate-300">·</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold border {{ $metodeBadge }}">
                                    {{ $pesanan->metode_bayar_label }}
                                </span>
                            </div>
                        </div>

                        {{-- Right Info: Total Nominal & Chevron --}}
                        <div class="text-right shrink-0 flex items-center gap-2.5">
                            <div>
                                <div class="font-bold text-sm sm:text-base text-slate-900 tabular-nums">
                                    {{ number_format($pesanan->grand_total, 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-slate-400">
                                    {{ $pesanan->details->count() }} item
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all"></i>
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
