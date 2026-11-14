@extends('layouts.pelanggan')
@section('title', 'Detail Pesanan #' . $pesanan->no_pesanan)

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-20 sm:pb-12">
    <div class="max-w-2xl mx-auto px-3 sm:px-4 space-y-3">
        
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

        {{-- Top Bar Header --}}
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('pelanggan.pesanan') }}" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors shadow-2xs" title="Kembali">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                </a>
                <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Detail Pesanan</h1>
            </div>

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
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $badgeClass }}">
                {{ $pesanan->status_label }}
            </span>
        </div>

        {{-- Single Flat Unified Container in List Format --}}
        <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 shadow-2xs overflow-hidden">
            
            {{-- 1. Info Pesanan & Kode Pickup --}}
            <div class="p-3.5 sm:p-4 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-500 text-[11px]">
                    <span>No. Pesanan: <span class="font-mono font-medium text-slate-800">{{ $pesanan->no_pesanan }}</span></span>
                    <span>{{ $pesanan->created_at->format('d M Y, H:i') }} WIB</span>
                </div>

                @if(in_array($pesanan->status, ['ready', 'unpaid_pickup', 'paid']))
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3" x-data="{ copied: false }">
                        <div>
                            <span class="text-[10px] text-slate-400 font-medium block">Kode Pickup</span>
                            <span class="font-mono text-lg sm:text-xl font-bold text-blue-600 tracking-wider select-all">
                                {{ $pesanan->kode_pickup }}
                            </span>
                        </div>
                        <button type="button" 
                                @click="navigator.clipboard.writeText('{{ $pesanan->kode_pickup }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="px-2.5 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-100 text-[11px] font-medium text-slate-700 transition-colors cursor-pointer flex items-center gap-1">
                            <i class="fa-regular" :class="copied ? 'fa-check text-emerald-600' : 'fa-copy text-slate-400'"></i>
                            <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- 2. Lacak Pesanan (Gaya E-Commerce Tokopedia) --}}
            @if(count($pesanan->tracking_timeline) > 0)
                <div class="p-3.5 sm:p-4 space-y-3.5 text-xs bg-white">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-truck-fast text-blue-600 text-xs"></i>
                            <span>Lacak Pesanan</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $pesanan->no_pesanan }}</span>
                    </div>

                    {{-- Vertical Stepper Timeline ala Tokopedia --}}
                    <div class="relative pl-6 sm:pl-7 space-y-4 before:absolute before:left-[11px] sm:before:left-[13px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        @foreach($pesanan->tracking_timeline as $index => $step)
                            @php
                                $isLatest = ($index === 0);
                            @endphp
                            <div class="relative">
                                {{-- Node Indicator --}}
                                @if($isLatest)
                                    <div class="absolute -left-6 sm:-left-7 top-0.5 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] ring-4 ring-blue-50 shadow-2xs">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                    </div>
                                @else
                                    <div class="absolute -left-6 sm:-left-7 top-1 w-6 h-6 rounded-full bg-white border-2 border-slate-300 text-slate-400 flex items-center justify-center text-[8px]">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                    </div>
                                @endif

                                {{-- Content --}}
                                <div class="space-y-0.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold {{ $isLatest ? 'text-blue-600' : 'text-slate-800' }}">
                                            {{ $step['title'] }}
                                        </h4>
                                        <span class="text-[10px] text-slate-400 shrink-0 font-normal">
                                            {{ $step['time'] }} WIB
                                        </span>
                                    </div>
                                    <p class="text-[11px] {{ $isLatest ? 'text-slate-700 font-medium' : 'text-slate-500' }} leading-relaxed">
                                        {{ $step['desc'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 3. Daftar Helm (List Format) --}}
            <div class="divide-y divide-slate-100">
                <div class="px-3.5 sm:px-4 py-2 bg-slate-50/40 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Produk Dipesan ({{ $pesanan->details->sum('qty') }})
                </div>

                @foreach($pesanan->details as $item)
                    <div class="p-3.5 sm:p-4 flex items-center gap-3 text-xs">
                        @if($item->helm && $item->helm->gambar_url)
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                                <img src="{{ $item->helm->gambar_url }}" alt="{{ $item->nama_helm }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-900 truncate">{{ $item->nama_helm }}</p>
                            @if($item->warna || $item->ukuran)
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $item->warna ?? '' }}{{ $item->warna && $item->ukuran ? ', ' : '' }}{{ $item->ukuran ?? '' }}
                                </p>
                            @endif
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                {{ $item->qty }} &times; {{ format_rupiah($item->harga) }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-semibold text-slate-900">
                                {{ format_rupiah($item->subtotal) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 3. Rincian Pembayaran --}}
            <div class="p-3.5 sm:p-4 space-y-2 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Rincian Pembayaran</span>
                
                <div class="space-y-1.5 pt-0.5">
                    <div class="flex justify-between text-slate-600">
                        <span>Metode Pembayaran</span>
                        <span class="font-medium text-slate-900">
                            @if(in_array($pesanan->metode_bayar, ['bayar_di_toko', 'tunai']))
                                Bayar di Kasir Gerai
                            @elseif($pesanan->metode_bayar === 'qris')
                                QRIS
                            @else
                                Transfer Bank
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Total Belanja</span>
                        <span class="font-medium text-slate-900">{{ format_rupiah($pesanan->total_belanja) }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-100 font-bold text-slate-900">
                        <span>Total Tagihan</span>
                        <span class="text-blue-600">{{ format_rupiah($pesanan->grand_total) }}</span>
                    </div>
                </div>
            </div>

            {{-- 4. Pengambilan di Gerai --}}
            <div class="p-3.5 sm:p-4 space-y-1.5 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Pengambilan di Toko</span>
                <div class="space-y-1">
                    <p class="text-slate-700">Penerima: <span class="font-medium text-slate-900">{{ $pesanan->penerima }}</span> ({{ $pesanan->telepon ?? '-' }})</p>
                    <p class="text-slate-700">Lokasi: <span class="font-medium text-slate-900">Gerai Bayeng Putra Helm Kuningan</span></p>
                    <p class="text-[11px] text-slate-500">Jl. RE Martadinata No.49, Ancaran, Kuningan (08.00 - 21.00 WIB)</p>
                    <p class="text-[11px] text-slate-500">Batas Ambil: <span class="font-medium text-slate-800">{{ $pesanan->batas_ambil ? $pesanan->batas_ambil->format('d M Y') : '-' }}</span></p>
                </div>
            </div>

            {{-- 5. Form Upload Bukti (hanya jika pending & transfer/qris) --}}
            @if($pesanan->status === 'pending' && in_array($pesanan->metode_bayar, ['transfer', 'qris']))
                <div class="p-3.5 sm:p-4 space-y-2.5 text-xs bg-slate-50/50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Upload Bukti Transfer</span>
                    
                    <div class="text-[11px] text-slate-600">
                        Transfer ke rekening BCA: <span class="font-mono font-bold text-slate-900">123-456-7890</span> a.n. Bayeng Putra Helm
                    </div>

                    <form method="POST" action="{{ route('pelanggan.pesanan.upload-bukti', $pesanan->id_pesanan) }}" enctype="multipart/form-data" class="space-y-2">
                        @csrf
                        <input type="file" 
                               name="bukti_bayar" 
                               required 
                               accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                        @error('bukti_bayar')
                            <p class="text-rose-500 text-[11px]">{{ $message }}</p>
                        @enderror

                        <button type="submit" 
                                class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-2xs active:scale-98 transition-all cursor-pointer">
                            Kirim Bukti Transfer
                        </button>
                    </form>
                </div>
            @endif

            {{-- 6. Bukti Transfer Terlampir (jika sudah diunggah) --}}
            @if($pesanan->bukti_bayar)
                <div class="p-3.5 sm:p-4 space-y-2 text-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Bukti Transfer</span>
                    <div class="w-32 h-32 rounded-lg overflow-hidden border border-slate-200 bg-slate-50">
                        <img src="{{ $pesanan->bukti_bayar_url }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
