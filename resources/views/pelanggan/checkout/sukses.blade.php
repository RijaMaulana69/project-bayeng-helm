@extends('layouts.pelanggan')
@section('title', 'Pesanan Berhasil - ' . $pesanan->no_pesanan)

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-5 sm:py-8">
    <div class="max-w-lg mx-auto px-3 sm:px-4 space-y-4">
        
        {{-- Success Heading --}}
        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 pt-1">Pesanan Berhasil Dibuat</h1>
            <p class="text-xs text-slate-500 font-mono">No. Pesanan: {{ $pesanan->no_pesanan }}</p>
        </div>

        {{-- Single Unified Flat Canvas --}}
        <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 shadow-2xs overflow-hidden">
            
            {{-- 1. Kode Pickup --}}
            <div class="p-4 text-center space-y-1.5" x-data="{ copied: false }">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Kode Pickup</span>
                <div class="flex items-center justify-center gap-2">
                    <span class="font-mono text-2xl sm:text-3xl font-extrabold text-blue-600 tracking-wider">
                        {{ $pesanan->kode_pickup }}
                    </span>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('{{ $pesanan->kode_pickup }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="px-2.5 py-1 rounded-md border border-slate-200 bg-slate-50 hover:bg-slate-100 text-[11px] font-medium text-slate-700 transition-colors cursor-pointer flex items-center gap-1">
                        <i class="fa-regular" :class="copied ? 'fa-check text-emerald-600' : 'fa-copy text-slate-400'"></i>
                        <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                    </button>
                </div>
                <p class="text-[11px] text-slate-500">Tunjukkan kode ini ke kasir saat mengambil helm.</p>
            </div>

            {{-- 2. Rincian Pesanan --}}
            <div class="p-3.5 sm:p-4 space-y-2 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Rincian Pesanan</span>
                
                <div class="grid grid-cols-2 gap-2.5 pt-0.5">
                    <div>
                        <span class="text-slate-500 block text-[11px]">Penerima</span>
                        <span class="font-medium text-slate-900">{{ $pesanan->penerima }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">Metode Pembayaran</span>
                        <span class="font-medium text-slate-900">
                            @if(in_array($pesanan->metode_bayar, ['bayar_di_toko', 'tunai']))
                                Bayar di Toko (Kasir)
                            @elseif($pesanan->metode_bayar === 'qris')
                                QRIS
                            @else
                                Transfer Bank
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">Batas Pengambilan</span>
                        <span class="font-medium text-slate-900">
                            {{ $pesanan->batas_ambil ? $pesanan->batas_ambil->format('d M Y') : '3 Hari ke Depan' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">Total Tagihan</span>
                        <span class="font-bold text-blue-600 text-xs sm:text-sm">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. Helm yang Dipesan --}}
            <div>
                <div class="px-3.5 sm:px-4 py-2 bg-slate-50/50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Helm yang Dipesan ({{ $pesanan->details->sum('qty') }})</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($pesanan->details as $detail)
                        <div class="p-3 sm:p-3.5 flex items-center gap-3 text-xs">
                            @if($detail->helm && $detail->helm->gambar_url)
                                <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                                    <img src="{{ $detail->helm->gambar_url }}" alt="{{ $detail->nama_helm }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-slate-900 truncate">{{ $detail->nama_helm }}</p>
                                @if($detail->warna || $detail->ukuran)
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $detail->warna ?? '' }}{{ ($detail->warna && $detail->ukuran) ? ', ' : '' }}{{ $detail->ukuran ?? '' }}
                                    </p>
                                @endif
                                <p class="text-[11px] text-slate-500">
                                    {{ $detail->qty }} &times; {{ format_rupiah($detail->harga) }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-slate-900 text-xs">
                                    {{ format_rupiah($detail->subtotal) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 4. Lokasi Pengambilan --}}
            <div class="p-3.5 sm:p-4 space-y-0.5 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Lokasi Pengambilan</span>
                <p class="font-medium text-slate-900">Gerai Bayeng Putra Helm Kuningan</p>
                <p class="text-[11px] text-slate-500">Jl. RE Martadinata No.49, Ancaran, Kuningan &bull; Jam Buka: 08.00 - 21.00 WIB</p>
            </div>

        </div>

        {{-- Action Buttons --}}
        <div class="space-y-2 pt-1">
            <a href="{{ route('pelanggan.pesanan.detail', $pesanan->id_pesanan) }}" 
               class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm active:scale-98 transition-all flex items-center justify-center cursor-pointer">
                Lihat Detail Pesanan
            </a>
            <a href="{{ route('katalog') }}" 
               class="w-full py-2.5 px-4 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors flex items-center justify-center shadow-2xs cursor-pointer">
                Kembali ke Katalog
            </a>
        </div>

    </div>
</div>
@endsection
