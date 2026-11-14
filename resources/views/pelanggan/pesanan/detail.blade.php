@extends('layouts.pelanggan')
@section('title', 'Detail Pesanan #' . $pesanan->no_pesanan)

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-20 sm:pb-12">
    <div class="max-w-xl mx-auto px-3 sm:px-4 space-y-3">
        
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Top Navigation Bar --}}
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('pelanggan.pesanan') }}" 
                   class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors shadow-2xs active:scale-95" 
                   title="Kembali">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Detail Pesanan</h1>
            </div>

            <button type="button"
                    @click="$dispatch('open-store-chat')"
                    class="h-8 px-3 inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-blue-600 text-xs font-medium transition-colors shadow-2xs cursor-pointer active:scale-95">
                <i class="fa-regular fa-comment-dots text-blue-600 text-xs"></i>
                <span>Chat Toko</span>
            </button>
        </div>

        @php
            $statusLabel = match($pesanan->status) {
                'unpaid_pickup' => 'Menunggu Diambil',
                'ready'         => 'Siap Diambil',
                'pending'       => 'Menunggu Pembayaran',
                'paid'          => 'Sudah Dibayar',
                'processing'    => 'Sedang Disiapkan',
                'completed'     => 'Selesai',
                'cancelled'     => 'Dibatalkan',
                default         => $pesanan->status_label,
            };

            $statusBadgeClass = match($pesanan->status) {
                'unpaid_pickup' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                'ready'         => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                'pending'       => 'bg-amber-50 text-amber-700 border-amber-200/80',
                'paid', 'processing' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                'completed'     => 'bg-slate-100 text-slate-700 border-slate-200',
                'cancelled'     => 'bg-rose-50 text-rose-700 border-rose-200/80',
                default         => 'bg-slate-50 text-slate-600 border-slate-200',
            };

            $statusInstruction = match($pesanan->status) {
                'unpaid_pickup' => 'Ambil pesanan sebelum ' . ($pesanan->batas_ambil ? $pesanan->batas_ambil->format('d M Y') : '17 Nov 2026') . ', 21:00 WIB',
                'ready'         => 'Helm sudah siap. Silakan ambil di gerai sekarang.',
                'pending'       => 'Selesaikan pembayaran sebelum batas waktu berakhir.',
                'paid'          => 'Pembayaran diterima. Pesanan sedang disiapkan.',
                'processing'    => 'Staf toko sedang menyiapkan helm pesanan.',
                'completed'     => 'Helm telah berhasil diambil di gerai.',
                'cancelled'     => 'Pesanan ini telah dibatalkan.',
                default         => 'Status: ' . $pesanan->status_label,
            };
        @endphp

        {{-- Single Flat Canvas (List Format Mobile-First) --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 divide-y divide-slate-100 shadow-2xs overflow-hidden">
            
            {{-- 1. Status & Kode Pengambilan --}}
            <div class="p-4 sm:p-5 space-y-3.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border uppercase tracking-wider {{ $statusBadgeClass }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="text-[11px] font-mono text-slate-400">
                        {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                    </span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    {{ $statusInstruction }}
                </p>

                {{-- Kode Pengambilan (Clean & Faktual) --}}
                @if(in_array($pesanan->status, ['ready', 'unpaid_pickup', 'paid']))
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 flex items-center justify-between gap-3" x-data="{ copied: false }">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">
                                Kode Pengambilan
                            </span>
                            <span class="font-mono text-xl sm:text-2xl font-bold text-slate-900 tracking-wider select-all">
                                {{ $pesanan->kode_pickup }}
                            </span>
                        </div>
                        <button type="button" 
                                @click="navigator.clipboard.writeText('{{ $pesanan->kode_pickup }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="h-8 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-xs font-semibold text-slate-700 transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs active:scale-95">
                            <i class="fa-regular" :class="copied ? 'fa-check text-emerald-600' : 'fa-copy text-slate-400'"></i>
                            <span x-text="copied ? 'Tersalin' : 'Salin Kode'">Salin Kode</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Tunjukkan kode di atas ke kasir toko saat mengambil helm.
                    </p>
                @endif
            </div>

            {{-- 2. Lacak Pesanan (Timeline Stepper) --}}
            @if(count($pesanan->tracking_timeline) > 0)
                <div class="p-4 sm:p-5 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Lacak Pesanan
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">#{{ $pesanan->no_pesanan }}</span>
                    </div>

                    {{-- Vertical Stepper --}}
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
                                        <h4 class="text-xs font-bold {{ $isLatest ? 'text-slate-900' : 'text-slate-700' }}">
                                            {{ $step['title'] }}
                                        </h4>
                                        <span class="text-[10px] text-slate-400 shrink-0 font-normal">
                                            {{ $step['time'] }} WIB
                                        </span>
                                    </div>
                                    <p class="text-[11px] {{ $isLatest ? 'text-slate-600' : 'text-slate-500' }} leading-relaxed">
                                        {{ $step['desc'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 3. Produk Dipesan (List Format) --}}
            <div class="divide-y divide-slate-100">
                <div class="px-4 py-2.5 bg-slate-50/50 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    <span>Produk Dipesan ({{ $pesanan->details->sum('qty') }})</span>
                </div>

                @foreach($pesanan->details as $item)
                    <div class="p-4 flex items-center gap-3 text-xs">
                        {{-- Thumbnail --}}
                        <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                            @if($item->helm && $item->helm->gambar_url)
                                <img src="{{ $item->helm->gambar_url }}" alt="{{ $item->nama_helm }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-helmet-safety text-sm"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('katalog.detail', $item->id_helm) }}" class="font-semibold text-slate-900 hover:text-blue-600 line-clamp-1 transition-colors">
                                {{ $item->nama_helm }}
                            </a>
                            @if($item->warna || $item->ukuran)
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                    {{ $item->warna ?? '' }}{{ $item->warna && $item->ukuran ? ', ' : '' }}{{ $item->ukuran ?? '' }}
                                </p>
                            @endif
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                {{ $item->qty }} &times; {{ format_rupiah($item->harga) }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="font-bold text-slate-900">
                                {{ format_rupiah($item->subtotal) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 4. Lokasi Pengambilan di Toko --}}
            <div class="p-4 sm:p-5 space-y-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">
                    Lokasi Pengambilan di Toko
                </span>

                <div class="space-y-1 text-xs">
                    <p class="font-bold text-slate-900">Gerai Bayeng Putra Helm Kuningan</p>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Jl. RE Martadinata No.49, Ancaran, Kuningan
                    </p>
                    <p class="text-[11px] text-slate-400">
                        Buka setiap hari, 08.00 - 21.00 WIB
                    </p>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold block">Penerima</span>
                        <p class="font-semibold text-slate-900">
                            {{ $pesanan->penerima }}
                            @if($pesanan->telepon)
                                <span class="font-normal text-slate-500 text-[11px] font-mono">({{ $pesanan->telepon }})</span>
                            @endif
                        </p>
                    </div>

                    <a href="https://maps.google.com/?q=Bayeng+Putra+Helm+Kuningan" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="h-8 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-location-arrow text-slate-400 text-[10px]"></i>
                        <span>Buka Peta</span>
                    </a>
                </div>
            </div>

            {{-- 5. Rincian Pembayaran --}}
            <div class="p-4 sm:p-5 space-y-2.5 text-xs">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">
                    Rincian Pembayaran
                </span>

                <div class="space-y-1.5 pt-0.5">
                    <div class="flex justify-between text-slate-600">
                        <span>Metode Pembayaran</span>
                        <span class="font-medium text-slate-900">
                            @if(in_array($pesanan->metode_bayar, ['bayar_di_toko', 'tunai']))
                                Bayar di Tempat (Kasir Toko)
                            @elseif($pesanan->metode_bayar === 'qris')
                                QRIS
                            @else
                                Transfer Bank BCA
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Total Harga ({{ $pesanan->details->sum('qty') }} helm)</span>
                        <span class="font-medium text-slate-900">{{ format_rupiah($pesanan->total_belanja) }}</span>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Ambil di Toko</span>
                        <span class="font-medium text-emerald-600">Gratis</span>
                    </div>

                    <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-sm font-bold text-slate-900">
                        <span>Total Pembayaran</span>
                        <span class="text-blue-600">{{ format_rupiah($pesanan->grand_total) }}</span>
                    </div>
                </div>
            </div>

            {{-- 6. Form Upload Bukti Transfer (Khusus Pending Transfer/QRIS) --}}
            @if($pesanan->status === 'pending' && in_array($pesanan->metode_bayar, ['transfer', 'qris']))
                <div class="p-4 sm:p-5 space-y-3 bg-slate-50/50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">
                        Upload Bukti Transfer
                    </span>

                    <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs space-y-1">
                        <p class="text-slate-500">Transfer ke rekening BCA:</p>
                        <p class="font-mono font-bold text-sm text-slate-900">123-456-7890</p>
                        <p class="text-[11px] text-slate-500">a.n. Bayeng Putra Helm</p>
                    </div>

                    <form method="POST" action="{{ route('pelanggan.pesanan.upload-bukti', $pesanan->id_pesanan) }}" enctype="multipart/form-data" class="space-y-2.5">
                        @csrf
                        <input type="file" 
                               name="bukti_bayar" 
                               required 
                               accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors border border-slate-200 rounded-xl bg-white p-1">
                        @error('bukti_bayar')
                            <p class="text-rose-500 text-[11px]">{{ $message }}</p>
                        @enderror

                        <button type="submit" 
                                class="w-full h-9 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-2xs active:scale-98 transition-all cursor-pointer">
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
            @endif

            {{-- 7. Bukti Transfer Terlampir (jika sudah diunggah) --}}
            @if($pesanan->bukti_bayar)
                <div class="p-4 sm:p-5 space-y-2 text-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">
                        Bukti Pembayaran
                    </span>
                    <div class="w-32 h-32 rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                        <img src="{{ $pesanan->bukti_bayar_url }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

            {{-- 8. Footer Metadata Transaksi --}}
            <div class="px-4 py-3 bg-slate-50/60 text-center text-[11px] text-slate-400">
                <span>No. Pesanan: <span class="font-mono text-slate-600 font-medium">{{ $pesanan->no_pesanan }}</span></span>
            </div>

        </div>

        {{-- Bottom Back Link --}}
        <div class="pt-2 text-center">
            <a href="{{ route('pelanggan.pesanan') }}" 
               class="text-xs font-semibold text-slate-500 hover:text-blue-600 inline-flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                <span>Kembali ke Riwayat Pesanan</span>
            </a>
        </div>

    </div>
</div>
@endsection
