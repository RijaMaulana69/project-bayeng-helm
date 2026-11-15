@extends('layouts.admin')

@section('title', 'Detail Transaksi ' . $pesanan->no_pesanan)
@section('page-title', 'Detail Transaksi Gerai')

@section('content')
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

    {{-- 2. Header Status Card --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    {{ $pesanan->no_pesanan }}
                </h2>
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
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold border {{ $badge }}">
                    {{ $pesanan->status_label }}
                </span>
            </div>
            <p class="text-xs text-slate-400 font-mono mt-0.5">
                Kode Pickup: <span class="font-bold text-slate-700">{{ $pesanan->kode_pickup }}</span> · Dibuat {{ $pesanan->created_at->translatedFormat('l, d F Y, H:i') }} WIB
            </p>
        </div>

        <div class="text-left sm:text-right shrink-0">
            <span class="text-xs text-slate-400 block">Total Transaksi</span>
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tabular-nums">
                {{ number_format($pesanan->grand_total, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- 3. Detail Pelanggan & Kasir --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        {{-- Card A: Pelanggan --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-user text-xs"></i>
                <span>Data Pelanggan</span>
            </h3>
            <div class="text-xs space-y-1">
                <p class="text-sm font-bold text-slate-900">
                    {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Langsung') }}
                </p>
                <p class="text-slate-500 font-mono">
                    <i class="fa-solid fa-phone text-[10px] text-slate-400 mr-1"></i>
                    {{ $pesanan->telepon ?? ($pesanan->pelanggan->telepon ?? '-') }}
                </p>
                @if($pesanan->catatan)
                    <div class="mt-2 p-2 rounded-lg bg-slate-50 text-[11px] text-slate-600">
                        <span class="font-semibold text-slate-700 block">Catatan Pesanan:</span>
                        {{ $pesanan->catatan }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Card B: Kasir & Pembayaran --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-cash-register text-xs"></i>
                <span>Kasir & Pembayaran</span>
            </h3>
            <div class="text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-bold text-slate-900">
                        {{ $pesanan->metode_bayar === 'tunai' ? 'Tunai di Gerai (Laci Kasir)' : ucfirst($pesanan->metode_bayar) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Status Bayar:</span>
                    <span class="font-semibold {{ in_array($pesanan->status, ['paid', 'processing', 'ready', 'completed']) ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ in_array($pesanan->status, ['paid', 'processing', 'ready', 'completed']) ? 'Sudah Dibayar' : 'Menunggu Pelunasan' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Batas Pengambilan:</span>
                    <span class="font-mono text-slate-700">
                        {{ $pesanan->batas_ambil ? $pesanan->batas_ambil->translatedFormat('d M Y') : 'Hari H Transaksi' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Item Produk yang Dibeli (Dengan Rincian Modal & Margin untuk Owner) --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                Rincian Helm & Analisis Laba
            </h3>
            <span class="text-xs text-slate-400">{{ $pesanan->details->count() }} item</span>
        </div>

        <div class="overflow-x-auto">
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
                    @php
                        $totalModalOrder = 0;
                    @endphp
                    @foreach ($pesanan->details as $item)
                        @php
                            $hargaAwal = $item->helm->harga_awal ?? 0;
                            $hargaJual = $item->harga;
                            $margin = ($hargaJual - $hargaAwal) * $item->qty;
                            $totalModalOrder += ($hargaAwal * $item->qty);
                        @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                {{ $item->helm->nama_helm ?? 'Helm' }}
                                <span class="text-[11px] text-slate-400 block font-normal">{{ $item->helm->merk->nama_merk ?? '' }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-[11px]">
                                    {{ $item->warna }} / {{ $item->ukuran }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold font-mono">
                                {{ $item->qty }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-500">
                                {{ format_rupiah($hargaAwal) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-800">
                                {{ format_rupiah($hargaJual) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-emerald-600 font-semibold">
                                +{{ format_rupiah($margin) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                {{ format_rupiah($item->subtotal) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/70 border-t border-slate-100 text-xs font-semibold">
                    <tr>
                        <td colspan="5" class="py-3 px-4 text-right text-slate-600">
                            Total Estimasi Margin Transaksi:
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-emerald-700 font-bold">
                            +{{ format_rupiah($pesanan->grand_total - $totalModalOrder) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-slate-900 font-bold">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 5. Audit Log Alur Status (Timeline Pelayanan Karyawan) --}}
    <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-4">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-timeline text-blue-600 text-xs"></i>
                <span>Jejak Audit Pelayanan Kasir (Timeline)</span>
            </h3>
            <p class="text-[11px] text-slate-400 mt-0.5">
                Riwayat perubahan status, petugas yang bertugas, dan catatan operasional
            </p>
        </div>

        <div class="relative pl-6 space-y-4 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            @forelse ($pesanan->logs as $log)
                @php
                    $logUser = $usersByUsername[$log->diubah_oleh] ?? null;
                    $logNama = $logUser ? $logUser->nama_lengkap : ($log->diubah_oleh ?? 'Sistem');
                @endphp
                <div class="relative group">
                    {{-- Dot --}}
                    <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-blue-600 ring-4 ring-white"></div>
                    
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-bold text-xs text-slate-900">
                                {{ ucfirst($log->status_ke) }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $log->created_at->translatedFormat('d M Y, H:i:s') }} WIB
                            </span>
                        </div>
                        <p class="text-xs text-slate-600">
                            {{ $log->catatan }}
                        </p>
                        <div class="pt-1 text-[11px] text-slate-400 flex items-center gap-2">
                            <span>Petugas: <strong class="text-slate-700">{{ $logNama }}</strong> ({{ $log->diubah_oleh }})</span>
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
