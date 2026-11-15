@extends('layouts.admin')

@section('title', 'Transaksi Gerai')
@section('page-title', 'Transaksi Gerai')

@section('content')
<div class="space-y-4">

    {{-- 1. Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Transaksi Gerai
        </h1>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.owner.laporan.penjualan') }}" 
               wire:navigate
               class="h-9 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors">
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
        <form method="GET" action="{{ route('admin.owner.transaksi-karyawan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-2 text-xs pt-2 border-t border-slate-100">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">
            @if($selectedKaryawan !== 'all')
                <input type="hidden" name="karyawan" value="{{ $selectedKaryawan }}">
            @endif

            {{-- Filter Status --}}
            <div class="sm:col-span-3">
                <select name="status" class="w-full h-8.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                    <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="ready" {{ $selectedStatus === 'ready' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="processing" {{ $selectedStatus === 'processing' ? 'selected' : '' }}>Sedang Disiapkan</option>
                    <option value="paid" {{ $selectedStatus === 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                    <option value="unpaid_pickup" {{ $selectedStatus === 'unpaid_pickup' ? 'selected' : '' }}>Bayar di Kasir</option>
                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Menunggu Bayar</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            {{-- Filter Metode Bayar --}}
            <div class="sm:col-span-3">
                <select name="metode_bayar" class="w-full h-8.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
                    <option value="all" {{ $selectedMetode === 'all' ? 'selected' : '' }}>Semua Metode</option>
                    <option value="tunai" {{ $selectedMetode === 'tunai' ? 'selected' : '' }}>Tunai di Gerai</option>
                    <option value="transfer" {{ $selectedMetode === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ $selectedMetode === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="ewallet" {{ $selectedMetode === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
            </div>

            {{-- Search Box --}}
            <div class="sm:col-span-4 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari order / pelanggan..." 
                       class="w-full h-8.5 pl-8 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:outline-none transition-colors">
            </div>

            {{-- Tombol Terapkan & Reset --}}
            <div class="sm:col-span-2 flex items-center gap-1.5">
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

    {{-- 5. Mobile-First Transactions Feed --}}

    {{-- A. MOBILE VIEW (Native App-like Order Feed) --}}
    <div class="md:hidden space-y-2.5">
        @forelse ($pesanans as $pesanan)
            @php
                $staffLog = $pesanan->logs->first(function($l) {
                    return !in_array(strtolower($l->diubah_oleh ?? ''), ['pelanggan', 'system', '']);
                });
                $kasirUser = $staffLog ? ($usersByUsername[$staffLog->diubah_oleh] ?? null) : null;
                $kasirNama = $kasirUser ? $kasirUser->nama_lengkap : ($staffLog->diubah_oleh ?? 'Kasir Gerai');
                $firstDetail = $pesanan->details->first();
                $otherCount = $pesanan->details->count() - 1;

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
            <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
                {{-- Header: Order No, Pickup, Status & Total --}}
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                           wire:navigate 
                           class="font-mono font-bold text-xs text-slate-900 hover:text-blue-600">
                            {{ $pesanan->no_pesanan }}
                        </a>
                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                            {{ $pesanan->kode_pickup ? $pesanan->kode_pickup . ' · ' : '' }}{{ $pesanan->created_at->translatedFormat('d M, H:i') }}
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <div class="font-bold text-xs text-slate-900 tabular-nums">
                            {{ number_format($pesanan->grand_total, 0, ',', '.') }}
                        </div>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-medium border {{ $badge }}">
                            {{ $pesanan->status_label }}
                        </span>
                    </div>
                </div>

                {{-- Body: Pelanggan & Produk --}}
                <div class="bg-slate-50/70 p-2 rounded-lg text-xs space-y-1">
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="font-medium text-slate-900">{{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Langsung') }}</span>
                        @if($pesanan->telepon ?? $pesanan->pelanggan->telepon ?? null)
                            <span class="text-[11px] text-slate-400 font-mono">{{ $pesanan->telepon ?? $pesanan->pelanggan->telepon }}</span>
                        @endif
                    </div>
                    @if($firstDetail)
                        <div class="text-[11px] text-slate-500 truncate">
                            {{ $firstDetail->qty }}x {{ $firstDetail->helm->nama_helm ?? 'Helm' }}
                            <span class="text-slate-400">({{ $firstDetail->warna }}, {{ $firstDetail->ukuran }})</span>
                            @if($otherCount > 0)
                                <span class="text-blue-600 font-medium">+{{ $otherCount }} lainnya</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Footer: Kasir, Metode & Detail Button --}}
                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-0.5">
                    <div class="flex items-center gap-1.5 truncate">
                        <span>Kasir: <strong class="text-slate-800 font-medium">{{ $kasirNama }}</strong></span>
                        <span class="text-slate-300">·</span>
                        <span>{{ $pesanan->metode_bayar === 'tunai' ? 'Tunai' : ucfirst($pesanan->metode_bayar) }}</span>
                    </div>

                    <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                       wire:navigate 
                       class="shrink-0 text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                        <span>Detail</span>
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white py-12 text-center text-slate-400 text-xs rounded-xl border border-slate-200/90 shadow-2xs">
                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                <p class="font-medium">Tidak ada transaksi ditemukan.</p>
            </div>
        @endforelse
    </div>

    {{-- B. DESKTOP VIEW (Clean, Tight Spreadsheet) --}}
    <div class="hidden md:block bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 text-slate-500 text-[11px] font-semibold bg-slate-50/70">
                        <th class="py-2.5 px-3.5">Order</th>
                        <th class="py-2.5 px-3.5">Pelanggan</th>
                        <th class="py-2.5 px-3.5">Produk</th>
                        <th class="py-2.5 px-3.5">Kasir</th>
                        <th class="py-2.5 px-3.5">Metode</th>
                        <th class="py-2.5 px-3.5 text-right">Total</th>
                        <th class="py-2.5 px-3.5 text-center">Status</th>
                        <th class="py-2.5 px-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pesanans as $pesanan)
                        @php
                            $staffLog = $pesanan->logs->first(function($l) {
                                return !in_array(strtolower($l->diubah_oleh ?? ''), ['pelanggan', 'system', '']);
                            });
                            $kasirUser = $staffLog ? ($usersByUsername[$staffLog->diubah_oleh] ?? null) : null;
                            $kasirNama = $kasirUser ? $kasirUser->nama_lengkap : ($staffLog->diubah_oleh ?? 'Kasir Gerai');
                            $firstDetail = $pesanan->details->first();
                            $otherCount = $pesanan->details->count() - 1;

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
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Order --}}
                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                                   wire:navigate
                                   class="font-mono font-bold text-slate-900 hover:text-blue-600 transition-colors block">
                                    {{ $pesanan->no_pesanan }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    {{ $pesanan->kode_pickup ? $pesanan->kode_pickup . ' · ' : '' }}{{ $pesanan->created_at->translatedFormat('d M, H:i') }}
                                </span>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="py-2.5 px-3.5 max-w-[140px]">
                                <p class="font-medium text-slate-900 truncate">
                                    {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Langsung') }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono truncate">
                                    {{ $pesanan->telepon ?? ($pesanan->pelanggan->telepon ?? '-') }}
                                </p>
                            </td>

                            {{-- Produk --}}
                            <td class="py-2.5 px-3.5 max-w-[180px]">
                                @if($firstDetail)
                                    <p class="text-slate-800 truncate">
                                        <span class="font-medium text-slate-900">{{ $firstDetail->qty }}x</span> {{ $firstDetail->helm->nama_helm ?? 'Helm' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 truncate">
                                        {{ $firstDetail->warna }}, {{ $firstDetail->ukuran }}
                                        @if($otherCount > 0)
                                            <span class="text-slate-600 font-medium">+{{ $otherCount }}</span>
                                        @endif
                                    </p>
                                @else
                                    <span class="text-slate-400 italic">Tanpa rincian</span>
                                @endif
                            </td>

                            {{-- Kasir --}}
                            <td class="py-2.5 px-3.5 whitespace-nowrap text-slate-800 font-medium">
                                {{ $kasirNama }}
                            </td>

                            {{-- Metode --}}
                            <td class="py-2.5 px-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-[11px] text-slate-600">
                                    {{ $pesanan->metode_bayar === 'tunai' ? 'Tunai' : ucfirst($pesanan->metode_bayar) }}
                                </span>
                            </td>

                            {{-- Total --}}
                            <td class="py-2.5 px-3.5 text-right font-bold text-slate-900 tabular-nums whitespace-nowrap">
                                {{ number_format($pesanan->grand_total, 0, ',', '.') }}
                            </td>

                            {{-- Status --}}
                            <td class="py-2.5 px-3.5 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $badge }}">
                                    {{ $pesanan->status_label }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-2.5 px-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan) }}" 
                                   wire:navigate
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 rounded-lg transition-colors">
                                    <span>Detail</span>
                                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-medium">Tidak ada transaksi ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($pesanans->hasPages())
        <div class="bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs">
            {{ $pesanans->links('partials.pagination-admin') }}
        </div>
    @endif

</div>
@endsection