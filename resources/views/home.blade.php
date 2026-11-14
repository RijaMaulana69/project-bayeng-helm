@extends('layouts.pelanggan')
@section('title', 'Bayeng Putra Helm - Pilih Online, Ambil di Gerai')

@section('content')
<div class="bg-slate-50 min-h-screen">

    {{-- Hero: Photo Background --}}
    <section class="relative overflow-hidden border-b border-slate-200">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('assets/home.jpeg') }}" alt="Gerai Bayeng Putra Helm" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-slate-900/60"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-5 py-16 md:py-28">
            <div class="text-center space-y-4">
                <h1 class="text-3xl md:text-6xl font-black text-white tracking-tighter leading-none">
                    PESAN ONLINE. <br>
                    <span class="text-blue-400">AMBIL DI TOKO.</span>
                </h1>
                <p class="text-xs md:text-base text-slate-300 font-medium max-w-xl mx-auto leading-relaxed">
                    Amankan helm incaranmu hari ini. Coba langsung di gerai kami untuk memastikan kenyamanan & ukuran yang sempurna.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 pt-4">
                    <a href="{{ route('katalog') }}" class="px-6 py-3 bg-blue-600 text-white font-bold text-xs md:text-sm rounded-lg hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20 active:scale-95">
                        MULAI BELANJA
                    </a>
                    <a href="#lokasi-gerai" class="px-6 py-3 bg-white/10 text-white font-bold text-xs md:text-sm rounded-lg border border-white/20 hover:bg-white/20 transition-all">
                        CEK LOKASI
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Value Props: Minisized & Sharp --}}
    <section class="py-10 px-5">
        <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-6">
            <div class="flex items-center gap-4 p-4 bg-white border border-slate-200 rounded-xl shadow-sm group hover:border-blue-500 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-headset text-sm"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-tight">Fitting Garansi</h3>
                    <p class="text-[10px] text-slate-500 font-medium">Ukuran pas atau tukar di tempat.</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 bg-white border border-slate-200 rounded-xl shadow-sm group hover:border-emerald-500 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-shield-check text-sm"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-tight">Original 100%</h3>
                    <p class="text-[10px] text-slate-500 font-medium">Produk resmi berstandar SNI.</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 bg-white border border-slate-200 rounded-xl shadow-sm group hover:border-amber-500 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-tight">Cepat & Aman</h3>
                    <p class="text-[10px] text-slate-500 font-medium">Validasi kode instan di kasir.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Category Filter Bar (E-commerce Style) --}}
    <section class="py-6 px-5 bg-white border-b border-slate-100">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center gap-3 overflow-x-auto hide-scrollbar">
                <a href="{{ route('katalog') }}" 
                   class="flex-shrink-0 px-4 py-2 bg-blue-600 text-white rounded-full text-[11px] font-bold transition-all shadow-sm">
                    Semua
                </a>
                @foreach($kategoris as $kat)
                    <a href="{{ route('katalog', ['kategori_filter' => $kat->id_kategori]) }}" 
                       class="flex-shrink-0 px-4 py-2 bg-white border border-slate-200 rounded-full text-[11px] font-semibold text-slate-700 hover:border-blue-600 hover:text-blue-600 transition-all">
                        {{ $kat->nama_kategori }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Popular Products: Compact 2-Col Mobile / 6-Col Desktop --}}
    <section class="py-12 px-5">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tighter">KOLEKSI PILIHAN</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Stok Tersedia</p>
                </div>
                <a href="{{ route('katalog') }}" class="text-[10px] font-black text-blue-600 hover:underline tracking-widest uppercase">Lihat Katalog</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-4">
                @foreach($produk_unggulan as $helm)
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden group hover:border-blue-500 hover:shadow-xl hover:shadow-slate-200/40 transition-all flex flex-col">
                    {{-- Thumb --}}
                    <div class="aspect-square bg-slate-50 relative overflow-hidden">
                        <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-white/90 backdrop-blur-sm rounded text-[8px] font-black text-slate-900 shadow-sm uppercase">
                            {{ $helm->merk->nama_merk }}
                        </div>
                    </div>
                    {{-- Body --}}
                    <div class="p-3 flex flex-col flex-grow justify-between gap-3">
                        <div>
                            <h3 class="text-[11px] font-bold text-slate-800 line-clamp-2 h-8 leading-tight mb-1 group-hover:text-blue-600 transition-colors">
                                {{ $helm->nama_helm }}
                            </h3>
                            <p class="text-[10px] font-black text-blue-600">{{ format_rupiah($helm->harga_jual) }}</p>
                        </div>
                        <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-center text-[9px] font-black text-slate-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all uppercase tracking-widest">
                            Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Store Location: Non-White Card & Interactivity --}}
    <section class="py-16 px-5" id="lokasi-gerai">
        <div class="max-w-4xl mx-auto">
            <div class="bg-[#0f172a] rounded-2xl p-8 md:p-12 text-white relative overflow-hidden shadow-2xl border border-slate-800">
                {{-- Light Leak Effect --}}
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-600/20 rounded-full blur-[80px] pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 md:gap-12">
                    <div class="flex-1 space-y-4 text-center md:text-left">
                        <h2 class="text-2xl md:text-4xl font-black tracking-tighter leading-none">KUNJUNGI GERAI KAMI</h2>
                        <p class="text-xs md:text-sm text-slate-400 font-medium leading-relaxed max-w-md">
                            Pusat helm resmi berstandar SNI & DOT di Kuningan. Tersedia berbagai merk ternama dan varian ukuran lengkap.
                        </p>
                        <div class="pt-4 flex flex-col sm:flex-row gap-3">
                            <div class="flex items-center gap-3 p-3 bg-white/5 border border-white/10 rounded-xl">
                                <i class="fa-solid fa-location-dot text-blue-500"></i>
                                <span class="text-[10px] font-bold text-slate-300">Jl. RE Martadinata No.49, Kuningan</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white/5 border border-white/10 rounded-xl">
                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                <span class="text-[10px] font-bold text-slate-300">+62 812 3456 7890</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="https://wa.me/6281234567890" target="_blank" class="w-full h-16 px-8 bg-blue-600 text-white font-black text-xs md:text-sm rounded-2xl flex items-center justify-center gap-3 hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20 active:scale-95 uppercase tracking-widest">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>CHAT ADMIN TOKO</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
