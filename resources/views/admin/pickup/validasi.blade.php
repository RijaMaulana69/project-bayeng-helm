@extends('layouts.admin')
@section('title', 'Validasi Pickup: ' . $pesanan->kode_pickup)
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="max-w-md mx-auto space-y-3">

    {{-- Navigasi Kembali --}}
    <a href="{{ route('admin.pesanan.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-800 transition-colors">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali ke Transaksi</span>
    </a>

    @php
        $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum');
        $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
        $isCompleted   = $pesanan->status === 'completed';
        $isUnpaid      = $pesanan->status === 'unpaid_pickup';
        $isReady       = $pesanan->status === 'ready';
    @endphp

    {{-- Kartu Validasi Serah Terima --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        {{-- Header: Kode Pickup, No Transaksi, Pelanggan --}}
        <div class="p-4 border-b border-slate-100 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <span class="font-mono text-base sm:text-lg font-bold text-slate-900 tracking-wide block">
                    {{ $pesanan->kode_pickup }}
                </span>
                <span class="text-xs text-slate-400 block mt-0.5">
                    {{ $pesanan->no_pesanan }}
                </span>
                <p class="text-xs text-slate-700 font-medium mt-1 truncate">
                    {{ $customerName }}
                    @if($customerPhone)
                        <span class="text-slate-400 font-mono font-normal">({{ $customerPhone }})</span>
                    @endif
                </p>
            </div>

            <div class="shrink-0 text-right">
                @if($isCompleted)
                    <span class="text-xs font-medium text-emerald-600">Selesai</span>
                @elseif($isReady || $isUnpaid)
                    <span class="text-xs font-semibold text-blue-600">Siap Diambil</span>
                @else
                    <span class="text-xs font-medium text-slate-600">{{ ucfirst($pesanan->status) }}</span>
                @endif
            </div>
        </div>

        <div class="p-4 space-y-4">
            {{-- Status Pembayaran & Tagihan --}}
            @if($isCompleted)
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-medium text-slate-800 block">Pesanan Telah Diserahkan</span>
                        <span class="text-[11px] text-slate-400">Total belanja {{ format_rupiah($pesanan->grand_total) }}</span>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500">
                        {{ $pesanan->updated_at->format('d M, H:i') }}
                    </span>
                </div>
            @elseif($isUnpaid)
                <div class="p-3 bg-amber-50/70 rounded-lg border border-amber-200/70 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-amber-800 font-medium">Tagihan Kasir (Tunai):</span>
                        <span class="text-sm font-bold text-amber-900">{{ format_rupiah($pesanan->grand_total) }}</span>
                    </div>
                    <p class="text-[11px] text-amber-700 mt-1">
                        Terima pembayaran tunai dari pembeli sebelum menyerahkan helm.
                    </p>
                </div>
            @else
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-600 block">Status Pembayaran:</span>
                        <span class="text-[11px] text-slate-400">Metode {{ ucfirst($pesanan->metode_bayar ?? 'Online') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold text-blue-600 block">Sudah Lunas</span>
                        <span class="text-xs font-bold text-slate-900">{{ format_rupiah($pesanan->grand_total) }}</span>
                    </div>
                </div>
            @endif

            {{-- Daftar Helm yang Diserahkan --}}
            <div class="space-y-1.5">
                <span class="text-[11px] font-medium text-slate-400 block">Rincian Barang</span>
                <div class="divide-y divide-slate-100 border border-slate-100 rounded-lg overflow-hidden bg-slate-50/30">
                    @foreach ($pesanan->details as $item)
                        @php
                            $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                            $variantParts = array_filter([$item->warna, $item->ukuran]);
                            $variantText = implode(', ', $variantParts);
                            $qty = $item->qty ?? ($item->jumlah ?? 1);
                        @endphp
                        <div class="p-3 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <p class="text-slate-800 truncate">
                                    <span class="font-medium text-slate-900">{{ $qty }}x</span> {{ $helmName }}
                                </p>
                                @if($variantText)
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                        {{ $variantText }}
                                    </p>
                                @endif
                            </div>
                            <span class="font-medium text-slate-700 text-xs shrink-0">
                                {{ format_rupiah($item->subtotal) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tombol Aksi --}}
            @if($isReady || $isUnpaid)
                <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}" class="pt-1">
                    @csrf
                    <button type="submit" 
                            class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs sm:text-sm rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>{{ $isUnpaid ? 'Terima Tunai & Selesaikan Penyerahan' : 'Konfirmasi & Serahkan Helm' }}</span>
                    </button>
                </form>
            @else
                <div class="pt-1 flex items-center gap-2">
                    <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" 
                       class="flex-1 h-10 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-lg inline-flex items-center justify-center transition-colors">
                        Detail Pesanan
                    </a>
                    <a href="{{ route('admin.pesanan.index') }}" 
                       class="flex-1 h-10 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs rounded-lg inline-flex items-center justify-center transition-colors">
                        Kembali ke Transaksi
                    </a>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
