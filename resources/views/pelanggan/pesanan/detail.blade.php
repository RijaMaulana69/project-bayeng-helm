@extends('layouts.pelanggan')
@section('title', 'Detail Pesanan #' . $pesanan->no_pesanan)

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-4 sm:py-7 pb-24 sm:pb-16">
    <div class="max-w-2xl mx-auto px-3.5 sm:px-4 space-y-4">
        
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5 shadow-2xs animate-fade-in">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5 shadow-2xs animate-fade-in">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Top Navigation Bar --}}
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('pelanggan.pesanan') }}" 
                   class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center transition-all shadow-2xs active:scale-95" 
                   title="Kembali ke Daftar Pesanan">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Detail Pesanan</h1>
                    <span class="text-[11px] font-mono text-slate-400 leading-none">#{{ $pesanan->no_pesanan }}</span>
                </div>
            </div>

            <button type="button"
                    @click="$dispatch('open-store-chat')"
                    class="h-8 px-3 inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-blue-600 hover:border-blue-200 text-xs font-medium transition-all shadow-2xs cursor-pointer active:scale-95">
                <i class="fa-regular fa-comment-dots text-blue-600 text-xs"></i>
                <span>Chat Toko</span>
            </button>
        </div>

        @php
            $statusMeta = match($pesanan->status) {
                'unpaid_pickup' => [
                    'label' => 'Menunggu Diambil',
                    'badge' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                    'icon' => 'fa-store',
                    'icon_bg' => 'bg-blue-600 text-white',
                    'desc' => 'Pesanan siap diambil di gerai. Tunjukkan kode pickup ke kasir untuk verifikasi & pembayaran.',
                ],
                'ready' => [
                    'label' => 'Siap Diambil',
                    'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                    'icon' => 'fa-box-check',
                    'icon_bg' => 'bg-emerald-600 text-white',
                    'desc' => 'Helm sudah disiapkan oleh staf gerai dan siap diambil kapan saja saat jam operasional.',
                ],
                'pending' => [
                    'label' => 'Menunggu Pembayaran',
                    'badge' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                    'icon' => 'fa-wallet',
                    'icon_bg' => 'bg-amber-500 text-white',
                    'desc' => 'Selesaikan pembayaran sebelum batas waktu berakhir dan unggah bukti transfer.',
                ],
                'paid' => [
                    'label' => 'Pembayaran Diverifikasi',
                    'badge' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                    'icon' => 'fa-check',
                    'icon_bg' => 'bg-sky-600 text-white',
                    'desc' => 'Pembayaran telah diterima sistem. Pesanan sedang diteruskan ke staf gerai.',
                ],
                'processing' => [
                    'label' => 'Sedang Disiapkan',
                    'badge' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                    'icon' => 'fa-box',
                    'icon_bg' => 'bg-sky-600 text-white',
                    'desc' => 'Staf toko sedang mengambil helm dan memeriksa kondisi fisik serta kelengkapannya.',
                ],
                'completed' => [
                    'label' => 'Selesai Diambil',
                    'badge' => 'bg-slate-100 text-slate-700 border-slate-200',
                    'icon' => 'fa-circle-check',
                    'icon_bg' => 'bg-slate-700 text-white',
                    'desc' => 'Helm telah diserahkan kepada pelanggan di gerai. Terima kasih telah berbelanja!',
                ],
                'cancelled' => [
                    'label' => 'Pesanan Dibatalkan',
                    'badge' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                    'icon' => 'fa-circle-xmark',
                    'icon_bg' => 'bg-rose-600 text-white',
                    'desc' => 'Pesanan telah dibatalkan.',
                ],
                default => [
                    'label' => $pesanan->status_label,
                    'badge' => 'bg-slate-50 text-slate-600 border-slate-200',
                    'icon' => 'fa-box',
                    'icon_bg' => 'bg-slate-600 text-white',
                    'desc' => 'Status pesanan saat ini: ' . $pesanan->status_label,
                ],
            };
        @endphp

        {{-- 1. Status Banner Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl {{ $statusMeta['icon_bg'] }} flex items-center justify-center text-sm shrink-0 shadow-sm">
                    <i class="fa-solid {{ $statusMeta['icon'] }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                            {{ $statusMeta['label'] }}
                        </h2>
                        <span class="text-[11px] text-slate-400 font-normal">
                            {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        {{ $statusMeta['desc'] }}
                    </p>
                </div>
            </div>
        </div>

        {{-- 2. E-Ticket / Kode Pickup Card (Tokopedia BOPIS Voucher Style) --}}
        @if(in_array($pesanan->status, ['ready', 'unpaid_pickup', 'paid']))
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-4 sm:p-5 text-white shadow-md relative overflow-hidden" x-data="{ copied: false }">
                {{-- Decorative background glow --}}
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5 text-blue-100 text-[11px] font-semibold uppercase tracking-wider">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Tiket Pengambilan di Gerai</span>
                        </div>
                        <div class="flex items-center gap-3 pt-0.5">
                            <span class="font-mono text-2xl sm:text-3xl font-extrabold tracking-wider text-white select-all">
                                {{ $pesanan->kode_pickup }}
                            </span>
                            <button type="button" 
                                    @click="navigator.clipboard.writeText('{{ $pesanan->kode_pickup }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="h-7 px-2.5 rounded-lg bg-white/20 hover:bg-white/30 backdrop-blur-sm text-[11px] font-semibold text-white transition-all cursor-pointer flex items-center gap-1 active:scale-95">
                                <i class="fa-regular" :class="copied ? 'fa-check text-emerald-300' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-blue-100/90 pt-0.5">
                            Tunjukkan kode ini ke staf kasir gerai saat mengambil helm.
                        </p>
                    </div>

                    @if($pesanan->batas_ambil)
                        <div class="sm:text-right bg-white/10 sm:bg-transparent p-2.5 sm:p-0 rounded-xl sm:rounded-none shrink-0 border border-white/15 sm:border-0">
                            <span class="text-[10px] text-blue-200 block uppercase tracking-wider font-semibold">Batas Pengambilan</span>
                            <span class="text-xs sm:text-sm font-bold text-white">
                                {{ $pesanan->batas_ambil->format('d M Y') }} • 21:00 WIB
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 3. Lacak Pesanan Card (Tokopedia Tracking Stepper) --}}
        @if(count($pesanan->tracking_timeline) > 0)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-600 text-xs"></i>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900">Lacak Pesanan</h3>
                    </div>
                    <span class="text-[11px] font-mono text-slate-400">#{{ $pesanan->no_pesanan }}</span>
                </div>

                {{-- Vertical Stepper ala Tokopedia --}}
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

        {{-- 4. Detail Produk Dipesan Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-box-open text-blue-600 text-xs"></i>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Daftar Helm</h3>
                </div>
                <span class="text-xs text-slate-500 font-medium">
                    {{ $pesanan->details->sum('qty') }} helm
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($pesanan->details as $item)
                    <div class="p-4 sm:p-5 flex items-start gap-3.5 hover:bg-slate-50/50 transition-colors">
                        {{-- Thumbnail --}}
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                            @if($item->helm && $item->helm->gambar_url)
                                <img src="{{ $item->helm->gambar_url }}" alt="{{ $item->nama_helm }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-helmet-safety text-lg"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs sm:text-sm font-semibold text-slate-900 line-clamp-2 leading-snug">
                                {{ $item->nama_helm }}
                            </h4>
                            @if($item->warna || $item->ukuran)
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Varian: <span class="font-medium text-slate-700">{{ $item->warna ?? '' }}{{ $item->warna && $item->ukuran ? ', ' : '' }}{{ $item->ukuran ?? '' }}</span>
                                </p>
                            @endif
                            <div class="flex items-center justify-between gap-2 mt-2 pt-1 border-t border-slate-100/80">
                                <span class="text-[11px] text-slate-500">
                                    {{ $item->qty }} &times; {{ format_rupiah($item->harga) }}
                                </span>
                                <span class="text-xs sm:text-sm font-bold text-slate-900">
                                    {{ format_rupiah($item->subtotal) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 5. Informasi Lokasi Pengambilan di Toko Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-store text-blue-600 text-xs"></i>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Pengambilan di Gerai</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    BOPIS (Ambil di Toko)
                </span>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 text-xs mt-0.5">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-900">Gerai Bayeng Putra Helm Kuningan</h4>
                        <p class="text-slate-600 text-[11px] leading-relaxed mt-0.5">
                            Jl. RE Martadinata No.49, Ancaran, Kec. Kuningan, Kabupaten Kuningan, Jawa Barat 45514
                        </p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Jam Operasional: <span class="font-medium text-slate-700">08.00 - 21.00 WIB</span> (Setiap Hari)
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 pt-2 border-t border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 text-xs mt-0.5">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] text-slate-400 font-semibold block uppercase">Nama Penerima</span>
                        <p class="font-semibold text-slate-900">
                            {{ $pesanan->penerima }}
                            @if($pesanan->telepon)
                                <span class="font-normal text-slate-500 font-mono text-[11px]">({{ $pesanan->telepon }})</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <a href="https://maps.google.com/?q=Bayeng+Putra+Helm+Kuningan" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="flex-1 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                    <i class="fa-solid fa-map-location-dot text-slate-500"></i>
                    <span>Buka Google Maps</span>
                </a>
                <button type="button"
                        @click="$dispatch('open-store-chat')"
                        class="flex-1 h-9 rounded-xl border border-blue-200 bg-blue-50/60 hover:bg-blue-100/60 text-blue-700 text-xs font-semibold inline-flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                    <i class="fa-regular fa-comment-dots"></i>
                    <span>Tanya Kasir</span>
                </button>
            </div>
        </div>

        {{-- 6. Rincian Pembayaran Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-blue-600 text-xs"></i>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Rincian Pembayaran</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    @if(in_array($pesanan->metode_bayar, ['bayar_di_toko', 'tunai']))
                        Bayar di Kasir Gerai
                    @elseif($pesanan->metode_bayar === 'qris')
                        QRIS
                    @else
                        Transfer Bank
                    @endif
                </span>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Metode Pembayaran</span>
                    <span class="font-medium text-slate-900">
                        @if(in_array($pesanan->metode_bayar, ['bayar_di_toko', 'tunai']))
                            Bayar di Kasir Gerai (Tunai)
                        @elseif($pesanan->metode_bayar === 'qris')
                            QRIS (Scan Barcode)
                        @else
                            Transfer Bank BCA
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Total Belanja ({{ $pesanan->details->sum('qty') }} helm)</span>
                    <span class="font-medium text-slate-900">{{ format_rupiah($pesanan->total_belanja) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Biaya Ambil di Gerai</span>
                    <span class="font-medium text-emerald-600">Gratis (Rp 0)</span>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Total Tagihan</span>
                        <span class="text-[10px] text-slate-400">Termasuk pajak & biaya layanan gerai</span>
                    </div>
                    <span class="text-base sm:text-lg font-extrabold text-blue-600">
                        {{ format_rupiah($pesanan->grand_total) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 7. Form Upload Bukti Transfer (Khusus Pending Transfer/QRIS) --}}
        @if($pesanan->status === 'pending' && in_array($pesanan->metode_bayar, ['transfer', 'qris']))
            <div class="bg-white rounded-2xl border border-amber-200 shadow-2xs p-4 sm:p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-arrow-up text-amber-600 text-xs"></i>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Upload Bukti Transfer</h3>
                </div>

                <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/60 text-xs text-amber-900 space-y-1">
                    <p class="font-medium">Rekening Tujuan Pembayaran:</p>
                    <p class="font-mono font-bold text-sm text-slate-900">BCA 123-456-7890</p>
                    <p class="text-[11px] text-amber-800">a.n. Bayeng Putra Helm • Total: <span class="font-bold">{{ format_rupiah($pesanan->grand_total) }}</span></p>
                </div>

                <form method="POST" action="{{ route('pelanggan.pesanan.upload-bukti', $pesanan->id_pesanan) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pilih File Bukti Transfer (JPG/PNG, Maks. 2MB)</label>
                        <input type="file" 
                               name="bukti_bayar" 
                               required 
                               accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors border border-slate-200 rounded-xl p-1">
                        @error('bukti_bayar')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm shadow-blue-600/20 active:scale-98 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Kirim Bukti Pembayaran</span>
                    </button>
                </form>
            </div>
        @endif

        {{-- 8. Bukti Transfer yang Sudah Diunggah --}}
        @if($pesanan->bukti_bayar)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-image text-blue-600 text-xs"></i>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900">Bukti Pembayaran Terlampir</h3>
                    </div>
                    <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-check text-[10px]"></i>
                        <span>Terunggah</span>
                    </span>
                </div>
                <div class="w-36 h-36 rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                    <img src="{{ $pesanan->bukti_bayar_url }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                </div>
            </div>
        @endif

        {{-- Bottom Navigation Links --}}
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
