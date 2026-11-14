@extends('layouts.pelanggan')
@section('title', 'Pilih Online, Ambil di Toko - Bayeng Putra Helm')

@section('content')
{{-- Hero Section (Interactive Immersive Background) --}}
<section class="relative min-h-[85vh] md:min-h-[90vh] flex items-center justify-center overflow-hidden" 
         x-data="{ y: 0 }" @scroll.window="y = window.pageYOffset">
    
    {{-- Parallax Background Image --}}
    <div class="absolute inset-0 z-0 transition-transform duration-75 ease-out"
         :style="`transform: translateY(${y * 0.4}px)`">
        <img src="{{ asset('assets/home.jpeg') }}" 
             alt="Gerai Bayeng Putra Helm" 
             class="w-full h-full object-cover scale-110">
        {{-- High-End Light Overlay --}}
        <div class="absolute inset-0 bg-white/40 backdrop-blur-[2px]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-white/10 via-transparent to-white"></div>
    </div>

    {{-- Hero Content --}}
    <div class="relative z-10 max-w-7xl mx-auto px-4 text-center space-y-8"
         :style="`opacity: ${1 - (y / 600)}; transform: translateY(${y * 0.15}px)`">
        
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-600 text-white text-[11px] font-black tracking-widest shadow-lg shadow-blue-600/30 animate-bounce-slow">
            <i class="fa-solid fa-location-dot"></i>
            <span>GERAI RESMI KUNINGAN</span>
        </div>

        <h1 class="text-4xl sm:text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.95]">
            PESAN HELM ONLINE.<br>
            <span class="text-blue-600 drop-shadow-sm">FITTING PAS DI TOKO.</span>
        </h1>

        <p class="text-base md:text-xl text-slate-700 font-medium leading-relaxed max-w-2xl mx-auto drop-shadow-sm">
            Amankan stok helm favorit dari rumah, pastikan ukuran lingkar kepala pas saat serah terima di gerai kami. Bebas risiko salah ukuran.
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <a href="{{ route('katalog') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-10 py-5 rounded-2xl bg-blue-600 text-white font-black text-sm shadow-2xl shadow-blue-600/40 hover:bg-blue-700 hover:-translate-y-1 active:scale-95 transition-all">
                <span>JELAJAHI KATALOG</span>
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
            <a href="#lokasi-gerai" 
               class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-5 rounded-2xl bg-white/80 backdrop-blur-md border border-slate-200 text-slate-900 font-bold text-sm shadow-xl hover:bg-white transition-all">
                <span>LOKASI GERAI</span>
            </a>
        </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 animate-bounce text-slate-400"
         x-show="y < 100" x-transition>
        <i class="fa-solid fa-chevron-down text-2xl"></i>
    </div>
</section>

{{-- Jaminan Toko Section (Layered Component) --}}
<section class="bg-slate-50 py-16 md:py-20 relative z-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col items-center text-center space-y-4 hover:shadow-xl hover:shadow-slate-200/50 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-person-circle-check text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Garansi Fitting Pas</h3>
                    <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">Coba & rasakan ukuran helm langsung di gerai kami sebelum dibawa pulang.</p>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col items-center text-center space-y-4 hover:shadow-xl hover:shadow-slate-200/50 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">100% Produk Original</h3>
                    <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">Seluruh koleksi helm resmi berstandar SNI & DOT dari distributor resmi.</p>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col items-center text-center space-y-4 hover:shadow-xl hover:shadow-slate-200/50 transition-all">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Bayar Aman di Kasir</h3>
                    <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">Bisa bayar online atau bayar tunai/QRIS saat ambil barang di toko.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Kategori Cepat Section --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div class="space-y-1">
                <h2 class="text-2xl font-black text-slate-900 tracking-tighter">KATEGORI HELM</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Temukan gaya berkendara Anda</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4 overflow-x-auto hide-scrollbar pb-2">
            @foreach($kategoris as $kat)
                <a href="{{ route('katalog', ['kategori_filter' => $kat->id_kategori]) }}" 
                   class="flex-shrink-0 px-6 py-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-blue-500 hover:bg-white hover:text-blue-600 font-bold text-xs transition-all">
                    {{ $kat->nama_kategori }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Produk Unggulan Section --}}
<section class="py-16 md:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center md:text-left mb-12">
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">KOLEKSI TERPOPULER</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($produk_unggulan as $helm)
            <div class="bg-white rounded-[1.5rem] border border-slate-100 overflow-hidden hover:shadow-xl hover:shadow-slate-200/60 transition-all flex flex-col group">
                <div class="aspect-square bg-slate-50 relative overflow-hidden">
                    <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>

                <div class="p-5 flex flex-col flex-grow justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest">{{ $helm->merk->nama_merk }}</p>
                        <h3 class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug">{{ $helm->nama_helm }}</h3>
                    </div>

                    <div class="flex items-end justify-between pt-3 border-t border-slate-50">
                        <p class="text-base font-black text-slate-900">{{ format_rupiah($helm->harga_jual) }}</p>
                        <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors">
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Info Gerai & WhatsApp --}}
<section class="py-20 bg-white" id="lokasi-gerai">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-slate-900 rounded-[2rem] p-10 md:p-16 text-center space-y-8 relative overflow-hidden">
            <div class="relative z-10 space-y-4">
                <h2 class="text-3xl font-black text-white tracking-tighter">KUNJUNGI GERAI KAMI</h2>
                <p class="text-slate-400 text-sm max-w-lg mx-auto">Datang ke lokasi fisik kami untuk mencoba helm secara langsung sebelum dibawa pulang.</p>
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-center gap-4 pt-4">
                <div class="bg-white/5 px-6 py-4 rounded-xl border border-white/10 flex items-center gap-3 text-left w-full md:w-auto">
                    <i class="fa-solid fa-location-dot text-blue-500"></i>
                    <p class="text-xs font-bold text-slate-200">Jl. RE Martadinata No.49, Kuningan</p>
                </div>
                <div class="bg-white/5 px-6 py-4 rounded-xl border border-white/10 flex items-center gap-3 text-left w-full md:w-auto">
                    <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                    <p class="text-xs font-bold text-slate-200">+62 812 3456 7890</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 2s infinite ease-in-out;
    }
</style>
@endsection
