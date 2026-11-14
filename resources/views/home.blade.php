@extends('layouts.pelanggan')
@section('title', 'Toko Helm Click & Collect Terpercaya')

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden bg-slate-950 text-white pt-12 pb-20 lg:pt-16 lg:pb-28">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:16px_16px] opacity-40"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            {{-- Hero Left Content --}}
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/80 text-blue-400 text-xs font-semibold backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>Pusat Helm Original & Bergaransi Resmi di Kuningan</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    Pesan Helm Favorit Online, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-blue-500 to-amber-400">
                        Fitting Pas & Ambil di Toko!
                    </span>
                </h1>

                <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto lg:mx-0 font-normal">
                    Tak perlu khawatir helm kebesaran atau kekecilan. Pilih koleksi dari 
                    <strong class="text-white">{{ $total_produk }}+ helm</strong> ternama, lakukan reservasi online, dan lakukan pengecekan ukuran kepala secara langsung di gerai kami.
                </p>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('katalog') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xl shadow-blue-600/30 active:scale-95 transition-all">
                        <i class="fa-solid fa-helmet-safety text-base"></i>
                        <span>Eksplor Katalog Helm</span>
                        <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                    </a>
                    <a href="#alur-pickup" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-slate-800/90 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold text-sm transition-all">
                        <i class="fa-solid fa-circle-question text-slate-400"></i>
                        <span>Cara Kerja Ambil di Toko</span>
                    </a>
                </div>

                {{-- Social Proof Mini Stats --}}
                <div class="pt-6 border-t border-slate-800/80 grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0 text-left">
                    <div>
                        <div class="text-xl font-extrabold text-white">{{ $total_produk }}+</div>
                        <div class="text-[11px] text-slate-400 font-medium">Model Helm Tersedia</div>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-emerald-400">100%</div>
                        <div class="text-[11px] text-slate-400 font-medium">SNI & Standar DOT</div>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-amber-400">0 Menit</div>
                        <div class="text-[11px] text-slate-400 font-medium">Bebas Ongkir (Pickup)</div>
                    </div>
                </div>
            </div>

            {{-- Hero Right Card --}}
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    {{-- Decorative frame --}}
                    <div class="absolute -inset-1.5 bg-gradient-to-tr from-blue-600 to-amber-500 rounded-3xl blur opacity-30"></div>
                    
                    <div class="relative rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl">
                        <img src="{{ asset('assets/home.jpeg') }}" 
                             alt="Gerai Bayeng Putra Helm" 
                             class="w-full h-80 sm:h-96 object-cover object-center transform hover:scale-105 transition-transform duration-700">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                        
                        {{-- Floating Pickup Highlight Badge --}}
                        <div class="absolute bottom-4 left-4 right-4 bg-slate-900/95 backdrop-blur-md p-4 rounded-2xl border border-slate-700/80 shadow-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center text-blue-400 flex-shrink-0">
                                    <i class="fa-solid fa-qrcode text-lg"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-white flex items-center gap-1.5">
                                        <span>Sistem Verifikasi Kode Tiket</span>
                                        <span class="px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-400 text-[10px]">Cepat</span>
                                    </p>
                                    <p class="text-[11px] text-slate-400 truncate">Tunjukkan kode tiket unik ke kasir, helm langsung diserahkan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Alur Ambil di Toko (Click & Collect 4-Step Process) --}}
<section class="py-16 bg-white border-b border-slate-200" id="alur-pickup">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold tracking-wide uppercase mb-2">
                Simpel & Tanpa Risiko
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Alur Ambil di Toko (Click & Collect)
            </h2>
            <p class="text-slate-500 text-sm mt-2">
                Empat langkah mudah bertransaksi aman tanpa cemas salah ukuran lingkar kepala.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $steps = [
                    [
                        'step' => '01',
                        'icon' => 'fa-magnifying-glass',
                        'color' => 'blue',
                        'title' => 'Pilih Helm & Varian',
                        'desc'  => 'Cari model favorit Anda, tentukan kombinasi warna dan perkiraan ukuran (M, L, XL).'
                    ],
                    [
                        'step' => '02',
                        'icon' => 'fa-bag-shopping',
                        'color' => 'amber',
                        'title' => 'Checkout Online',
                        'desc'  => 'Masukkan keranjang, pilih metode bayar (Transfer, QRIS, atau bayar tunai saat ambil).'
                    ],
                    [
                        'step' => '03',
                        'icon' => 'fa-ticket',
                        'color' => 'emerald',
                        'title' => 'Terima e-Tiket Unik',
                        'desc'  => 'Sistem menerbitkan e-Tiket dengan kode pickup unik (contoh: PKP-A1B2C3) ke akun Anda.'
                    ],
                    [
                        'step' => '04',
                        'icon' => 'fa-store',
                        'color' => 'indigo',
                        'title' => 'Fitting & Serah Terima',
                        'desc'  => 'Datang ke toko fisik, fitting ukuran helm langsung, lalu kasir memvalidasi tiket Anda.'
                    ]
                ];
            @endphp

            @foreach($steps as $s)
            <div class="relative bg-slate-50 hover:bg-white rounded-3xl p-6 border border-slate-200/80 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-2xl font-black text-slate-300 group-hover:text-blue-600 transition-colors">
                        {{ $s['step'] }}
                    </span>
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-200/60 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fa-solid {{ $s['icon'] }} text-lg"></i>
                    </div>
                </div>
                <h3 class="font-bold text-base text-slate-900 mb-1.5">{{ $s['title'] }}</h3>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- Produk Unggulan Section --}}
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-10">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Koleksi Terpopuler</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                    Helm Pilihan Terlaris
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar helm paling diminati rider dengan ketersediaan stok fisik riil.</p>
            </div>
            <a href="{{ route('katalog') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2.5 rounded-xl transition-colors">
                <span>Lihat Seluruh Koleksi</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        {{-- Product Grid Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($produk_unggulan as $helm)
            <div class="group bg-white rounded-3xl overflow-hidden border border-slate-200/80 hover:border-blue-300 hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                
                {{-- Product Thumbnail with Badge --}}
                <div class="relative bg-slate-100/60 aspect-square overflow-hidden">
                    <img src="{{ $helm->gambar_url }}" 
                         alt="{{ $helm->nama_helm }}" 
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                    
                    {{-- Merk Chip --}}
                    @if($helm->merk)
                    <div class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-md text-[10px] font-bold text-slate-800 shadow-sm border border-slate-100">
                        {{ $helm->merk->nama_merk }}
                    </div>
                    @endif

                    {{-- Stock Indicator Pill --}}
                    <div class="absolute top-3 right-3">
                        @if($helm->stok > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 text-[10px] font-bold border border-emerald-500/20 backdrop-blur-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Stok {{ $helm->stok }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-700 text-[10px] font-bold border border-rose-500/20">
                                Habis
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Product Details --}}
                <div class="p-4 sm:p-5 flex flex-col flex-grow justify-between space-y-3">
                    <div>
                        <p class="text-[11px] font-semibold text-blue-600 uppercase tracking-wide">
                            {{ $helm->kategori->nama_kategori ?? 'Helm' }}
                        </p>
                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2 mt-0.5">
                            {{ $helm->nama_helm }}
                        </h3>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-slate-400 font-medium">Harga Toko</p>
                            <p class="text-base font-extrabold text-slate-900">
                                {{ format_rupiah($helm->harga_jual) }}
                            </p>
                        </div>
                        <a href="{{ route('katalog.detail', $helm->id_helm) }}" 
                           class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white flex items-center justify-center transition-colors shadow-sm"
                           title="Lihat Detail & Varian">
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- Brand Partners Carousel/Pill Showcase --}}
<section class="py-14 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">
            Merk Resmi Pilihan di Bayeng Putra Helm
        </h2>
        <div class="flex flex-wrap justify-center items-center gap-3">
            @foreach($merks as $merk)
            <a href="{{ route('katalog', ['merk_filter' => $merk->id_merk]) }}" 
               class="px-5 py-2.5 rounded-2xl bg-slate-50 hover:bg-blue-50 border border-slate-200/80 hover:border-blue-300 text-slate-700 hover:text-blue-700 font-bold text-xs tracking-tight transition-all shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-tag text-[10px] text-slate-400"></i>
                <span>{{ $merk->nama_merk }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
