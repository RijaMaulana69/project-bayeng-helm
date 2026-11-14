@extends('layouts.pelanggan')
@section('title', 'Riwayat Pesanan & e-Tiket')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-slate-800 font-semibold">Pesanan Saya</span>
        </nav>

        {{-- Heading --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fa-solid fa-receipt text-blue-600"></i>
                    <span>Riwayat Pesanan & e-Tiket Pickup</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Pantau status pesanan, lihat e-Tiket pengambilan, dan unggah bukti pembayaran.
                </p>
            </div>
            <a href="{{ route('katalog') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition-colors self-start sm:self-auto">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Pesan Helm Baru</span>
            </a>
        </div>

        {{-- Orders List --}}
        @forelse($pesanans as $pesanan)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-4 hover:border-blue-300 transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-mono text-sm font-black text-slate-900">{{ $pesanan->no_pesanan }}</span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <i class="fa-regular fa-calendar text-[11px]"></i>
                            {{ $pesanan->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>

                    {{-- Status Badge --}}
                    <div>
                        {!! $pesanan->status_badge !!}
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        {{-- Pickup Code Highlight Pill --}}
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-50/80 border border-blue-200 text-xs">
                            <i class="fa-solid fa-ticket text-blue-600"></i>
                            <span class="text-slate-600 font-medium">Kode Pickup:</span>
                            <span class="font-mono font-black text-blue-700 tracking-wider select-all">{{ $pesanan->kode_pickup }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Batas Ambil: <strong class="text-slate-800">{{ $pesanan->batas_ambil ? $pesanan->batas_ambil->format('d M Y') : '-' }}</strong> di Gerai Kuningan
                        </p>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-5">
                        <div class="text-left sm:text-right">
                            <span class="text-[10px] text-slate-400 font-medium block">Total Belanja</span>
                            <span class="text-base font-black text-slate-900">{{ format_rupiah($pesanan->grand_total) }}</span>
                        </div>
                        <a href="{{ route('pelanggan.pesanan.detail', $pesanan->id_pesanan) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs transition-colors shadow-sm">
                            <span>Buka e-Tiket</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 sm:p-16 text-center border border-slate-200/80 shadow-sm space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Belum Ada Transaksi</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Anda belum pernah melakukan pemesanan helm di Bayeng Putra Helm.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('katalog') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                        <i class="fa-solid fa-helmet-safety"></i>
                        <span>Lihat Katalog Helm</span>
                    </a>
                </div>
            </div>
        @endforelse

    </div>
</div>
@endsection
