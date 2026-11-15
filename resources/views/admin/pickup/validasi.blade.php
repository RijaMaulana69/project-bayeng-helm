@extends('layouts.admin')
@section('title', 'Validasi: ' . $pesanan->no_pesanan)
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="max-w-xl mx-auto space-y-3">

    {{-- Back Link --}}
    <a href="{{ route('admin.pickup.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali ke Antrean</span>
    </a>

    {{-- Ultra-Compact Handover Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        
        @php
            $isPaid = in_array($pesanan->status, ['ready', 'paid']);
            $customerName = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan');
            $customerPhone = $pesanan->pelanggan->no_hp ?? ($pesanan->telepon_penerima ?? null);
        @endphp

        {{-- Card Header: Pickup Code & Customer Summary --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kode Pickup</span>
                <span class="font-mono font-black text-lg sm:text-xl text-slate-900 tracking-wider">
                    {{ $pesanan->kode_pickup }}
                </span>
                <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500 truncate">
                    <span class="font-medium text-slate-700 truncate">{{ $customerName }}</span>
                    @if($customerPhone)
                        <span class="text-slate-300">•</span>
                        <span class="font-mono text-slate-400">{{ $customerPhone }}</span>
                    @endif
                </div>
                <p class="text-[11px] font-mono text-slate-400 mt-0.5 truncate">
                    {{ $pesanan->no_pesanan }}
                </p>
            </div>

            <div class="shrink-0 text-right">
                @if($isPaid)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        <i class="fa-solid fa-check text-[10px]"></i>
                        <span>Lunas</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                        <i class="fa-solid fa-wallet text-[10px]"></i>
                        <span>Bayar di Toko</span>
                    </span>
                @endif
            </div>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            {{-- Payment Status Banner --}}
            @if($isPaid)
                <div class="p-3 bg-emerald-50/70 border border-emerald-200/70 rounded-xl flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                        <div>
                            <p class="text-xs font-bold text-emerald-900">Sudah Lunas Online</p>
                            <p class="text-[11px] text-emerald-700">Barang siap langsung diserahkan tanpa tagihan kasir.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm shrink-0"></i>
                        <div>
                            <p class="text-xs font-bold text-amber-900">Perlu Pembayaran di Kasir</p>
                            <p class="text-[11px] text-amber-700">Tagihkan uang tunai atau QRIS sebelum serahkan helm.</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-mono font-black text-xs sm:text-sm text-amber-900">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </span>
                    </div>
                </div>
            @endif

            {{-- Items List (Compact 1-Line per Item) --}}
            <div>
                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mb-1.5 px-0.5">
                    <span>Barang yang Diambil ({{ $pesanan->details->count() }})</span>
                    <span>Subtotal</span>
                </div>
                <div class="border border-slate-200/80 rounded-xl divide-y divide-slate-100 overflow-hidden bg-white">
                    @foreach ($pesanan->details as $item)
                        @php
                            $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                            $variantParts = array_filter([$item->warna, $item->ukuran ? 'Ukuran ' . $item->ukuran : null]);
                            $variantText = implode(' • ', $variantParts);
                        @endphp
                        <div class="p-3 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-900 truncate">
                                    <span class="font-bold font-mono text-slate-600">{{ $item->jumlah }}x</span> {{ $helmName }}
                                </div>
                                @if($variantText)
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                        {{ $variantText }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right font-mono font-semibold text-slate-800 shrink-0">
                                {{ format_rupiah($item->subtotal) }}
                            </div>
                        </div>
                    @endforeach

                    {{-- Total Tagihan Row --}}
                    <div class="p-3 bg-slate-50/70 flex items-center justify-between text-xs font-bold border-t border-slate-200/80">
                        <span class="text-slate-600">Total Tagihan</span>
                        <span class="font-mono text-sm font-black {{ $isPaid ? 'text-slate-900' : 'text-amber-700' }}">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Action Handover & Back Buttons --}}
            <div class="pt-2 space-y-2">
                <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}">
                    @csrf
                    <button type="submit" 
                            class="w-full h-11 sm:h-12 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs sm:text-sm rounded-xl transition-colors cursor-pointer shadow-2xs"
                            onclick="return confirm('Konfirmasi bahwa barang pesanan ini telah diserahkan kepada pelanggan?')">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Konfirmasi Penyerahan</span>
                    </button>
                </form>
                
                <a href="{{ route('admin.pickup.index') }}" 
                   class="w-full h-10 flex items-center justify-center text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                    <span>Batal</span>
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
