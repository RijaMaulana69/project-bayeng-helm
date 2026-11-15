@extends('layouts.admin')
@section('title', 'Pickup: ' . $pesanan->no_pesanan)
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="max-w-xl mx-auto space-y-3">

    {{-- Navigasi Kembali --}}
    <a href="{{ route('admin.pickup.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-800 transition-colors">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali</span>
    </a>

    {{-- Kartu Validasi Kasir --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        
        @php
            $isPaid = in_array($pesanan->status, ['ready', 'paid']);
            $customerName = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan');
            $customerPhone = $pesanan->pelanggan->no_hp ?? ($pesanan->telepon_penerima ?? null);
            $hasMultipleItems = $pesanan->details->count() > 1;
        @endphp

        {{-- Header: Kode Pickup & Data Pembeli --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <span class="font-mono font-bold text-lg sm:text-xl text-slate-900 tracking-wide block">
                    {{ $pesanan->kode_pickup }}
                </span>
                <p class="text-xs text-slate-600 mt-0.5 truncate">
                    {{ $customerName }}@if($customerPhone) • <span class="font-mono text-slate-400">{{ $customerPhone }}</span>@endif
                </p>
                <p class="text-[11px] font-mono text-slate-400 mt-0.5">
                    {{ $pesanan->no_pesanan }}
                </p>
            </div>

            <div class="shrink-0 text-right">
                @if($isPaid)
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        Lunas
                    </span>
                @else
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                        Bayar di Toko
                    </span>
                @endif
            </div>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            {{-- Daftar Barang & Tagihan --}}
            <div class="border border-slate-200/80 rounded-xl divide-y divide-slate-100 overflow-hidden bg-white">
                @foreach ($pesanan->details as $item)
                    @php
                        $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                        $variantParts = array_filter([$item->warna, $item->ukuran]);
                        $variantText = implode(', ', $variantParts);
                    @endphp
                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800 truncate">
                                <span class="font-mono text-slate-500 font-bold">{{ $item->jumlah }}x</span> {{ $helmName }}
                            </p>
                            @if($variantText)
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $variantText }}
                                </p>
                            @endif
                        </div>
                        @if($hasMultipleItems)
                            <div class="text-right font-mono font-medium text-slate-600 shrink-0">
                                {{ format_rupiah($item->subtotal) }}
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Total Tagihan --}}
                <div class="p-3.5 bg-slate-50/70 flex items-center justify-between text-xs border-t border-slate-200/70">
                    <div>
                        <span class="font-semibold text-slate-700 block">Total Tagihan</span>
                        @if($isPaid)
                            <span class="text-[11px] text-emerald-700 font-medium">Lunas ({{ ucfirst($pesanan->metode_bayar ?? 'Online') }})</span>
                        @else
                            <span class="text-[11px] text-amber-700 font-medium">Bayar di kasir ({{ ucfirst($pesanan->metode_bayar ?? 'Tunai') }})</span>
                        @endif
                    </div>
                    <span class="font-mono text-base font-bold {{ $isPaid ? 'text-slate-800' : 'text-amber-900' }}">
                        {{ format_rupiah($pesanan->grand_total) }}
                    </span>
                </div>
            </div>

            {{-- Tombol Serahkan --}}
            <div class="pt-1">
                <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}">
                    @csrf
                    <button type="submit" 
                            class="w-full h-11 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors cursor-pointer shadow-2xs"
                            onclick="return confirm('Serahkan pesanan ini ke pelanggan?')">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Serahkan Pesanan</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
