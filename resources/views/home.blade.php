@extends('layouts.pelanggan')
@section('title', 'Pesan Helm Online, Ambil di Toko')

@section('content')
{{-- Hero Section (Simplified for performance) --}}
<section class="relative h-[60vh] md:h-[80vh] flex items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/home.jpeg') }}" 
             alt="Gerai Bayeng Putra Helm" 
             class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-slate-900/20"></div>
    </div>

    <div class="relative z-10 w-full max-w-7xl px-4 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 mb-6 bg-blue-600/20 border border-blue-500/30 rounded-lg backdrop-blur-sm">
            <i class="fa-solid fa-store text-blue-400 text-[10px]"></i>
            <span class="text-blue-400 text-[10px] font-bold tracking-[0.2em] uppercase">Gerai Resmi Kuningan</span>
        </div>
        <h1 class="text-3xl md:text-6xl font-extrabold text-white tracking-tight leading-tight mb-6">
            Pilih Online. <span class="text-blue-500">Coba & Ambil</span> di Toko.
        </h1>
        <p class="text-sm md:text-lg text-slate-300 font-medium max-w-2xl mx-auto mb-10 leading-relaxed px-4">
            Amankan stok helm impian dari rumah, pastikan ukuran lingkar kepala pas saat serah terima di gerai kami.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('katalog') }}" class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 text-white font-bold text-sm rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/25">
                JELAJAHI KATALOG
            </a>
            <a href="#lokasi-gerai" class="w-full sm:w-auto px-8 py-3.5 bg-white/10 text-white border border-white/20 backdrop-blur-md font-bold text-sm rounded-lg hover:bg-white/20 transition-all">
                LOKASI GERAI
            </a>
        </div>
    </div>
</section>

{{-- Advantages Section (Bento Style) --}}
<section class="py-12 md:py-20 px-4 bg-[#f8fafc]">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-8 bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-check-double text-xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight mb-3">Garansi Fitting Pas</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Coba produk langsung di gerai kami untuk memastikan kenyamanan sebelum transaksi selesai.</p>
            </div>
            <div class="p-8 bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-shield-heart text-xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight mb-3">100% Produk Original</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Seluruh koleksi helm resmi berstandar SNI & DOT dari distributor resmi pilihan.</p>
            </div>
            <div class="p-8 bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight mb-3">Sistem Bayar Fleksibel</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Pesan online tanpa was-was. Selesaikan pembayaran saat Anda sudah yakin dengan barangnya.</p>
            </div>
        </div>
    </div>
</section>

{{-- Popular Products (Compact Mobile Grid) --}}
<section class="py-12 md:py-20 px-4 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-[0.2em] block mb-2">Koleksi Rider</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Koleksi Terpopuler</h2>
            </div>
            <a href="{{ route('katalog') }}" class="text-xs font-bold text-slate-400 hover:text-blue-600 transition-colors uppercase tracking-widest flex items-center gap-2">
                Lihat Semua <i class="fa-solid fa-chevron-right text-[8px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @foreach($produk_unggulan as $helm)
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden hover:border-blue-500 hover:shadow-md transition-all group">
                <div class="aspect-square overflow-hidden bg-slate-50">
                    <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                <div class="p-3">
                    <div class="text-[10px] text-slate-500 truncate mb-1">{{ $helm->merk->nama_merk }}</div>
                    <h3 class="text-xs font-medium text-slate-900 line-clamp-2 h-8 mb-2 leading-tight">{{ $helm->nama_helm }}</h3>
                    <div class="font-bold text-sm text-slate-900 mb-2">{{ format_rupiah($helm->harga_jual) }}</div>
                    <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="block w-full py-1.5 text-center text-[11px] font-bold text-blue-600 border border-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Category Strip --}}
<section class="py-12 bg-[#f8fafc]">
    <div class="max-w-7xl mx-auto px-4">
        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center mb-8">Kategori Favorit</h3>
        <div class="flex flex-wrap justify-center gap-2 md:gap-4">
            @foreach($kategoris as $kat)
                <a href="{{ route('katalog', ['kategori_filter' => $kat->id_kategori]) }}" 
                   class="px-5 py-2.5 bg-white border border-slate-200 rounded-lg text-[11px] font-bold text-slate-600 hover:border-blue-600 hover:text-blue-600 transition-all shadow-sm uppercase tracking-widest">
                    {{ $kat->nama_kategori }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Store Location - Rich E-commerce Layout --}}
<section class="py-20 bg-slate-100" id="lokasi-gerai">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            
            {{-- Content Section --}}
            <div class="md:col-span-5 space-y-6">
                <div class="inline-block px-3 py-1 bg-blue-100 text-blue-700 font-bold text-[10px] uppercase tracking-widest rounded-full">Lokasi Offline</div>
                <h2 class="text-4xl font-extrabold tracking-tighter text-slate-900">Kunjungi Gerai Kami</h2>
                <p class="text-slate-600 leading-relaxed font-medium">Datang langsung ke lokasi kami untuk fitting dan konsultasi helm yang paling sesuai dengan gaya berkendara Anda. Staf kami siap membantu.</p>
                
                <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-3 px-8 py-4 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 active:scale-95">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    <span>CHAT ADMIN TOKO</span>
                </a>
            </div>

            {{-- Cards Section --}}
            <div class="md:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Address Card --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Alamat Lengkap</p>
                    <p class="text-sm font-bold text-slate-900 leading-tight">Jl. RE Martadinata No.49, Kuningan</p>
                </div>

                {{-- Hours Card --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center mb-4">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Jam Operasional</p>
                    <p class="text-sm font-bold text-slate-900 leading-tight">08:00 - 21:00 WIB (Setiap Hari)</p>
                </div>

                {{-- Phone Card --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 sm:col-span-2">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Hubungi Kami</p>
                            <p class="text-sm font-bold text-slate-900">+62 812 3456 7890</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
