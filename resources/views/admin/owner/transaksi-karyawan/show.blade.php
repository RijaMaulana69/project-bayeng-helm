@extends('layouts.admin')

@section('title', 'Detail Transaksi ' . $pesanan->no_pesanan)
@section('page-title', 'Detail Transaksi Gerai')

@section('content')
@php
    $staffLog = $pesanan->logs->first(function($l) {
        return !in_array(strtolower($l->diubah_oleh ?? ''), ['pelanggan', 'system', '']);
    });
    $kasirUser = $staffLog ? ($usersByUsername[$staffLog->diubah_oleh] ?? null) : null;
    $kasirNama = $kasirUser ? $kasirUser->nama_lengkap : ($staffLog->diubah_oleh ?? 'Ari Akbar');

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

<div class="max-w-4xl mx-auto space-y-4">

    {{-- 1. Navigation Breadcrumb --}}
    <div>
        <a href="{{ route('admin.owner.transaksi-karyawan.index') }}" 
           wire:navigate
           class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 font-medium transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Transaksi Gerai</span>
        </a>
    </div>

    {{-- 2. Header Status Card (Tanpa Background Badge yang Mengganggu) --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    {{ $pesanan->no_pesanan }}
                </h1>
                <span class="text-slate-300">·</span>
                <span class="text-xs sm:text-sm font-semibold {{ $statusColor }}">
                    {{ $pesanan->status_label }}
                </span>
            </div>
            <p class="text-xs text-slate-400">
                Kode Pickup: <span class="font-semibold text-slate-700">{{ $pesanan->kode_pickup ?? '-' }}</span> · Dibuat {{ $pesanan->created_at->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>

        <div class="text-left sm:text-right shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
            <span class="text-xs text-slate-400 block">Total Transaksi</span>
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tabular-nums">
                {{ number_format($pesanan->grand_total, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- 3. Detail Pelanggan & Kasir (Clean Typography) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        {{-- Card A: Pelanggan --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-user text-[11px]"></i>
                <span>Data Pelanggan</span>
            </div>
            <div class="space-y-1">
                <p class="text-sm font-semibold text-slate-900">
                    {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Langsung') }}
                </p>
                <p class="text-xs text-slate-500">
                    <i class="fa-solid fa-phone text-[10px] text-slate-400 mr-1"></i>
                    {{ $pesanan->telepon ?? ($pesanan->pelanggan->telepon ?? '-') }}
                </p>
                @if($pesanan->catatan)
                    <div class="mt-2 pt-2 border-t border-slate-100 text-xs text-slate-600">
                        <span class="text-slate-400 text-[11px] block">Catatan:</span>
                        {{ $pesanan->catatan }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Card B: Kasir & Pembayaran --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-cash-register text-[11px]"></i>
                <span>Kasir & Pembayaran</span>
            </div>
            <div class="text-xs space-y-1.5 pt-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Petugas Kasir:</span>
                    <span class="font-semibold text-slate-900">
                        {{ $kasirNama }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-semibold text-slate-900">
                        {{ $pesanan->metode_bayar === 'tunai' ? 'Tunai di Gerai' : $pesanan->metode_bayar_label }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Status Bayar:</span>
                    <span class="font-semibold {{ in_array($pesanan->status, ['paid', 'processing', 'ready', 'completed']) ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ in_array($pesanan->status, ['paid', 'processing', 'ready', 'completed']) ? 'Sudah Dibayar' : 'Menunggu Pelunasan' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Batas Pengambilan:</span>
                    <span class="text-slate-700">
                        {{ $pesanan->batas_ambil ? $pesanan->batas_ambil->translatedFormat('d M Y') : 'Hari H Transaksi' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Item Produk yang Dibeli & Analisis Laba (Mobile-First Responsive) --}}
    @php
        $totalModalOrder = 0;
    @endphp
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between text-xs">
            <h3 class="font-bold uppercase tracking-wider text-slate-800">
                Rincian Helm & Analisis Laba
            </h3>
            <span class="text-slate-400">{{ $pesanan->details->count() }} item</span>
        </div>

        {{-- A. Tampilan Mobile (< sm): Card List Tanpa Horizontal Scroll --}}
        <div class="sm:hidden divide-y divide-slate-100">
            @foreach ($pesanan->details as $item)
                @php
                    $hargaAwal = $item->helm->harga_awal ?? 0;
                    $hargaJual = $item->harga;
                    $margin = ($hargaJual - $hargaAwal) * $item->qty;
                    $totalModalOrder += ($hargaAwal * $item->qty);
                @endphp
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-semibold text-slate-900 truncate">
                                {{ $item->qty }}x {{ $item->helm->nama_helm ?? 'Helm' }}
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $item->warna }} · {{ $item->ukuran }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-slate-900 tabular-nums">
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-emerald-600 font-semibold">
                                +{{ number_format($margin, 0, ',', '.') }} margin
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-50">
                        <span>Modal: {{ number_format($hargaAwal, 0, ',', '.') }}</span>
                        <span>Jual: {{ number_format($hargaJual, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- B. Tampilan Desktop (>= sm): Flat Minimalist Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-[11px] font-medium bg-slate-50/50">
                        <th class="py-2.5 px-4 font-medium">Produk Helm</th>
                        <th class="py-2.5 px-4 font-medium">Varian</th>
                        <th class="py-2.5 px-4 font-medium text-center">Qty</th>
                        <th class="py-2.5 px-4 font-medium text-right">Harga Modal</th>
                        <th class="py-2.5 px-4 font-medium text-right">Harga Jual</th>
                        <th class="py-2.5 px-4 font-medium text-right">Margin Bersih</th>
                        <th class="py-2.5 px-4 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($pesanan->details as $item)
                        @php
                            $hargaAwal = $item->helm->harga_awal ?? 0;
                            $hargaJual = $item->harga;
                            $margin = ($hargaJual - $hargaAwal) * $item->qty;
                        @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                {{ $item->helm->nama_helm ?? 'Helm' }}
                                <span class="text-[11px] text-slate-400 block font-normal">{{ $item->helm->merk->nama_merk ?? '' }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $item->warna }} / {{ $item->ukuran }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold">
                                {{ $item->qty }}
                            </td>
                            <td class="py-3 px-4 text-right tabular-nums text-slate-500">
                                {{ number_format($hargaAwal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right tabular-nums text-slate-800">
                                {{ number_format($hargaJual, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right tabular-nums text-emerald-600 font-semibold">
                                +{{ number_format($margin, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right tabular-nums font-bold text-slate-900">
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Ringkasan Total Margin & Transaksi --}}
        <div class="bg-slate-50/70 p-3.5 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
            <div class="text-slate-500">
                Total Estimasi Laba Bersih Penjualan:
                <strong class="text-emerald-700 font-bold ml-1 tabular-nums">+{{ number_format($pesanan->grand_total - $totalModalOrder, 0, ',', '.') }}</strong>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-slate-500 mr-2">Grand Total:</span>
                <span class="font-bold text-sm sm:text-base text-slate-900 tabular-nums">{{ number_format($pesanan->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- 5. Audit Log Alur Status (Timeline Pelayanan Karyawan) --}}
    <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-4">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-timeline text-blue-600 text-xs"></i>
                <span>Jejak Audit Pelayanan Kasir</span>
            </h3>
            <p class="text-[11px] text-slate-400 mt-0.5">
                Riwayat status pelayanan dan petugas gerai yang memproses
            </p>
        </div>

        <div class="relative pl-5 space-y-3.5 before:content-[''] before:absolute before:left-1.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            @forelse ($pesanan->logs as $log)
                @php
                    $logUser = $usersByUsername[$log->diubah_oleh] ?? null;
                    $logNama = $logUser ? $logUser->nama_lengkap : ($log->diubah_oleh ?? 'Sistem');
                @endphp
                <div class="relative">
                    <div class="absolute -left-5 top-1.5 w-2 h-2 rounded-full bg-blue-600 ring-4 ring-white"></div>
                    
                    <div class="space-y-0.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-semibold text-xs text-slate-900">
                                {{ ucfirst($log->status_ke) }}
                            </span>
                            <span class="text-[11px] text-slate-400">
                                {{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        <p class="text-xs text-slate-600">
                            {{ $log->catatan }}
                        </p>
                        <div class="text-[11px] text-slate-400">
                            Petugas: <strong class="text-slate-700 font-medium">{{ $logNama }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic">Belum ada catatan log aktivitas untuk pesanan ini.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
