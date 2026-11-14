@extends('layouts.pelanggan')
@section('title', 'Detail Pesanan #' . $pesanan->no_pesanan)

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-4 sm:py-6 pb-20 sm:pb-12">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 space-y-4">
        
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @php
            $badgeClass = match($pesanan->status) {
                'pending'            => 'bg-amber-50 text-amber-700 border-amber-200/60',
                'unpaid_pickup'      => 'bg-blue-50 text-blue-700 border-blue-200/60',
                'paid', 'processing' => 'bg-sky-50 text-sky-700 border-sky-200/60',
                'ready'              => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                'completed'          => 'bg-slate-100 text-slate-600 border-slate-200',
                'cancelled'          => 'bg-rose-50 text-rose-700 border-rose-200/60',
                default              => 'bg-slate-50 text-slate-600 border-slate-200',
            };
        @endphp

        {{-- Page Header --}}
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('pelanggan.pesanan') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors shadow-2xs" title="Kembali">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 font-mono leading-tight">
                        {{ $pesanan->no_pesanan }}
                    </h1>
                    <p class="text-[11px] text-slate-500">{{ $pesanan->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
            </div>

            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border {{ $badgeClass }}">
                {{ $pesanan->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
            
            {{-- Left Column: Order Main Info & Products (8 Cols) --}}
            <div class="lg:col-span-8 space-y-4">
                
                {{-- Unified Card: Kode Pickup & Penerima --}}
                <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 shadow-2xs overflow-hidden">
                    
                    {{-- Kode Pickup --}}
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

                    {{-- Data Pengambilan --}}
                    <div class="p-3.5 sm:p-4 text-xs grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Penerima</span>
                            <span class="font-medium text-slate-900">{{ $pesanan->penerima }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">No. WhatsApp</span>
                            <span class="font-medium text-slate-900">{{ $pesanan->telepon ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Batas Pengambilan</span>
                            <span class="font-medium text-slate-900">
                                {{ $pesanan->batas_ambil ? $pesanan->batas_ambil->format('d M Y') : '3 Hari' }}
                            </span>
                        </div>
                    </div>

                    {{-- Lokasi Gerai --}}
                    <div class="p-3.5 sm:p-4 text-xs space-y-0.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Lokasi Gerai</span>
                        <p class="font-medium text-slate-900">Gerai Bayeng Putra Helm Kuningan</p>
                        <p class="text-[11px] text-slate-500">Jl. RE Martadinata No.49, Ancaran, Kuningan &bull; Jam Buka: 08.00 - 21.00 WIB</p>
                    </div>

                </div>

                {{-- Helm Items List --}}
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                    <div class="px-3.5 sm:px-4 py-2.5 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Helm yang Dipesan</span>
                        <span class="text-xs font-medium text-slate-600">{{ $pesanan->details->sum('qty') }} barang</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($pesanan->details as $item)
                            <div class="p-3.5 sm:p-4 flex items-center gap-3 text-xs">
                                @if($item->helm && $item->helm->gambar_url)
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                                        <img src="{{ $item->helm->gambar_url }}" alt="{{ $item->nama_helm }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-xs sm:text-sm text-slate-900 truncate">{{ $item->nama_helm }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Varian: {{ $item->warna ?? '-' }}, Size {{ $item->ukuran ?? '-' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $item->qty }} &times; {{ format_rupiah($item->harga) }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-900 text-xs sm:text-sm">
                                        {{ format_rupiah($item->subtotal) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-3.5 sm:p-4 bg-slate-50/30 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-600">Total Tagihan</span>
                        <span class="text-sm sm:text-base font-bold text-blue-600">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </span>
                    </div>
                </div>

                {{-- Status Timeline Logs --}}
                @if($pesanan->logs && $pesanan->logs->count() > 0)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Riwayat Pesanan</span>
                        <div class="space-y-3 text-xs">
                            @foreach($pesanan->logs as $log)
                                <div class="flex items-start gap-2.5">
                                    <div class="w-2 h-2 rounded-full bg-blue-600 mt-1.5 shrink-0"></div>
                                    <div class="flex-1">
                                        <p class="text-slate-800 font-medium leading-snug">{{ $log->catatan }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $log->created_at->format('d M Y, H:i') }} WIB</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Right Column: Payment & Action (4 Cols) --}}
            <div class="lg:col-span-4 space-y-4">
                
                {{-- Payment Method Summary --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-2 text-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Metode Pembayaran</span>
                    <p class="font-medium text-slate-900 text-xs sm:text-sm">
                        @if(in_array($pesanan->metode_bayar, ['bayar_di_toko', 'tunai']))
                            Bayar di Toko (Kasir)
                        @elseif($pesanan->metode_bayar === 'qris')
                            QRIS
                        @else
                            Transfer Bank
                        @endif
                    </p>
                    <p class="text-[11px] text-slate-500">
                        @if(in_array($pesanan->metode_bayar, ['bayar_di_toko', 'tunai']))
                            Lakukan pembayaran saat mengambil helm di kasir gerai.
                        @elseif($pesanan->status === 'pending')
                            Menunggu verifikasi bukti transfer.
                        @else
                            Pembayaran sudah tercatat di sistem.
                        @endif
                    </p>
                </div>

                {{-- Form Upload Bukti (if pending and transfer/qris) --}}
                @if($pesanan->status === 'pending' && in_array($pesanan->metode_bayar, ['transfer', 'qris']))
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3 text-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Upload Bukti Transfer</span>

                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-0.5 text-xs">
                            <span class="text-[10px] text-slate-400 font-medium block">Rekening Gerai</span>
                            <p class="font-mono font-bold text-slate-900">BCA: 123-456-7890</p>
                            <p class="text-[11px] text-slate-500">a.n. Bayeng Putra Helm</p>
                        </div>

                        <form method="POST" action="{{ route('pelanggan.pesanan.upload-bukti', $pesanan->id_pesanan) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <input type="file" 
                                       name="bukti_bayar" 
                                       required 
                                       accept="image/*"
                                       class="block w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                                @error('bukti_bayar')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG (Maks 2MB)</p>
                            </div>

                            <button type="submit" 
                                    class="w-full py-2 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-2xs active:scale-98 transition-all cursor-pointer">
                                Kirim Bukti Transfer
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Display Bukti Bayar if Uploaded --}}
                @if($pesanan->bukti_bayar)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-2 text-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Bukti Transfer</span>
                        <div class="rounded-lg overflow-hidden border border-slate-200 bg-slate-50 max-h-48">
                            <img src="{{ $pesanan->bukti_bayar_url }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
