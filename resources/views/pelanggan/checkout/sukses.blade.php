@extends('layouts.pelanggan')
@section('title', 'e-Tiket Pengambilan Helm - ' . $pesanan->no_pesanan)

@section('content')
<div class="bg-slate-100/70 min-h-screen py-12">
    <div class="max-w-xl mx-auto px-4 sm:px-6">
        
        {{-- Success Confirmation Badge --}}
        <div class="text-center mb-8 space-y-2">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-sm">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pesanan Berhasil Dicatat!</h1>
            <p class="text-xs text-slate-500">
                e-Tiket pengambilan helm Anda telah aktif. Tunjukkan tiket ini kepada kasir saat berkunjung ke toko.
            </p>
        </div>

        {{-- Digital Boarding Pass / Pickup e-Ticket --}}
        <div class="relative bg-white rounded-3xl shadow-xl shadow-slate-200/80 border border-slate-200/90 overflow-hidden">
            
            {{-- Ticket Header --}}
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-900 p-6 text-white">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-500 flex items-center justify-center text-white text-base">
                            <i class="fa-solid fa-helmet-safety"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-sm tracking-tight">BAYENG PUTRA HELM</span>
                            <p class="text-[10px] text-blue-200 font-medium">Official Store Pickup Ticket</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-extrabold uppercase tracking-wider">
                        @if($pesanan->status === 'unpaid_pickup')
                            Bayar di Toko
                        @elseif($pesanan->status === 'paid')
                            Lunas (Siap Ambil)
                        @else
                            Menunggu Verifikasi
                        @endif
                    </span>
                </div>
            </div>

            {{-- Main Ticket Body --}}
            <div class="p-6 sm:p-8 space-y-6">
                
                {{-- Prominent Pickup Code Box --}}
                <div class="text-center bg-slate-50 border-2 border-dashed border-blue-300 rounded-2xl p-6 relative group">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Kode Pickup Unik Pengambilan
                    </p>
                    <div class="font-mono text-3xl sm:text-4xl font-black text-blue-600 tracking-widest my-2 select-all">
                        {{ $pesanan->kode_pickup }}
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Sebutkan atau perlihatkan kode ini kepada kasir kami.
                    </p>

                    <button onclick="navigator.clipboard.writeText('{{ $pesanan->kode_pickup }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Kode disalin ke clipboard!', showConfirmButton:false, timer:2000})" 
                            type="button" 
                            class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[11px] font-bold text-slate-700 hover:bg-slate-100 transition-colors shadow-sm">
                        <i class="fa-solid fa-copy text-blue-600"></i>
                        <span>Salin Kode</span>
                    </button>
                </div>

                {{-- Ticket Details Grid --}}
                <div class="grid grid-cols-2 gap-4 text-xs pt-2">
                    <div>
                        <span class="text-slate-400 font-medium block">Nomor Pesanan</span>
                        <span class="font-bold text-slate-800 font-mono text-xs">{{ $pesanan->no_pesanan }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Penerima</span>
                        <span class="font-bold text-slate-800">{{ $pesanan->penerima }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Batas Pengambilan</span>
                        <span class="font-bold text-rose-600">
                            {{ $pesanan->batas_ambil ? $pesanan->batas_ambil->format('d M Y') : '3 Hari ke Depan' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Total Pembayaran</span>
                        <span class="font-extrabold text-slate-900 text-sm">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </span>
                    </div>
                </div>

            </div>

            {{-- Perforated Divider with Circular Cutouts --}}
            <div class="relative py-1 bg-white">
                <div class="ticket-cutout-left"></div>
                <div class="ticket-cutout-right"></div>
                <div class="border-t-2 border-dashed border-slate-200 mx-6"></div>
            </div>

            {{-- Ticket Bottom Stub (Location & Actions) --}}
            <div class="p-6 sm:p-8 bg-slate-50/70 space-y-4 text-xs">
                
                {{-- Store Location Mini Card --}}
                <div class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 text-sm mt-0.5">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Alamat Pengambilan Gerai:</p>
                        <p class="text-slate-600 text-[11px] mt-0.5">Jl. RE Martadinata No.49, Ancaran, Kuningan, Jawa Barat</p>
                        <p class="text-emerald-700 font-semibold text-[10px] mt-0.5">Buka Setiap Hari: 08.00 - 21.00 WIB</p>
                    </div>
                </div>

                {{-- Payment Warning (if bank transfer / QRIS) --}}
                @if(in_array($pesanan->metode_bayar, ['transfer', 'qris']) && $pesanan->status === 'pending')
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 space-y-1">
                    <p class="font-bold flex items-center gap-1.5 text-xs text-amber-800">
                        <i class="fa-solid fa-clock"></i>
                        <span>Menunggu Pembayaran</span>
                    </p>
                    <p class="text-[11px] text-amber-700 leading-relaxed">
                        Silakan selesaikan pembayaran dan upload bukti transfer di halaman detail pesanan agar pesanan segera disiapkan oleh kasir.
                    </p>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="pt-2 space-y-2">
                    <a href="{{ route('pelanggan.pesanan.detail', $pesanan->id_pesanan) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 active:scale-98 transition-all">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Lihat Rincian Pesanan & Upload Bukti</span>
                    </a>
                    <a href="{{ route('katalog') }}" 
                       class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Kembali ke Katalog Helm</span>
                    </a>
                </div>

            </div>

        </div>

        {{-- Floating Hint --}}
        <p class="text-center text-[11px] text-slate-400 mt-6">
            Anda dapat melihat kembali e-Tiket ini kapan saja melalui menu <strong class="text-slate-600">Pesanan Saya</strong>.
        </p>

    </div>
</div>
@endsection
