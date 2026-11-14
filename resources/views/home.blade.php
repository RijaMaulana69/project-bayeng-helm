@extends('layouts.pelanggan')
@section('title', 'Beranda')

@section('content')
{{-- Hero Section --}}
<section class="hero-section">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <div class="mb-3">
                    <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2 rounded-pill" style="font-size:.8rem">
                        <i class="fa fa-shield-halved me-1"></i> Toko Helm Terpercaya
                    </span>
                </div>
                <h1>Temukan Helm <span style="color:var(--secondary)">Impian</span> Anda</h1>
                <p class="lead mt-3 mb-4">Pesan online, coba langsung di toko. Koleksi lengkap {{ $total_produk }}+ helm dari brand ternama dengan harga terbaik.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('katalog') }}" class="btn btn-primary-bayeng btn-lg px-4">
                        <i class="fa fa-helmet-safety me-2"></i> Lihat Katalog
                    </a>
                    <a href="#cara-pesan" class="btn btn-outline-light btn-lg px-4 rounded-3">
                        <i class="fa fa-info-circle me-2"></i> Cara Pesan
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0" data-aos="fade-left">
                <img src="{{ asset('assets/home.jpeg') }}" alt="Hero" class="img-fluid rounded-4 shadow" style="max-height:360px;object-fit:cover">
            </div>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-5" id="cara-pesan">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Cara Pesan & Ambil di Toko</h2>
            <p class="text-muted">Proses mudah dalam 4 langkah</p>
        </div>
        <div class="row g-4">
            @php
                $steps = [
                    ['icon' => 'fa-search', 'color' => '#2563eb', 'title' => 'Pilih Helm', 'desc' => 'Telusuri katalog dan pilih helm favorit Anda berdasarkan merk, tipe, dan ukuran.'],
                    ['icon' => 'fa-cart-shopping', 'color' => '#f59e0b', 'title' => 'Checkout', 'desc' => 'Masukkan ke keranjang, lalu checkout. Pilih bayar online atau bayar di toko.'],
                    ['icon' => 'fa-ticket', 'color' => '#10b981', 'title' => 'Dapat Kode Pickup', 'desc' => 'Setelah checkout, Anda akan mendapat kode pickup unik untuk pengambilan.'],
                    ['icon' => 'fa-store', 'color' => '#8b5cf6', 'title' => 'Ambil di Toko', 'desc' => 'Datang ke toko, tunjukkan kode pickup, coba helmnya, dan bawa pulang!'],
                ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="text-center p-4 h-100 bg-white rounded-4 border" style="border-color:#e2e8f0 !important">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:{{ $step['color'] }}15">
                        <i class="fa {{ $step['icon'] }} fa-xl" style="color:{{ $step['color'] }}"></i>
                    </div>
                    <div class="badge bg-light text-muted mb-2">Langkah {{ $i + 1 }}</div>
                    <h6 class="fw-bold">{{ $step['title'] }}</h6>
                    <p class="text-muted small mb-0">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Produk Unggulan --}}
<section class="py-5" style="background:#fff">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
            <div>
                <h2 class="section-title mb-1">Produk Unggulan</h2>
                <p class="text-muted mb-0">Helm pilihan terbaik dari koleksi kami</p>
            </div>
            <a href="{{ route('katalog') }}" class="btn btn-outline-bayeng">
                Lihat Semua <i class="fa fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach($produk_unggulan as $helm)
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="text-decoration-none">
                    <div class="product-card">
                        <img src="{{ $helm->gambar_url }}" class="card-img-top" alt="{{ $helm->nama_helm }}">
                        <div class="card-body">
                            <div class="product-merk">{{ $helm->merk->nama_merk ?? '' }}</div>
                            <div class="product-name">{{ $helm->nama_helm }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">{{ format_rupiah($helm->harga_jual) }}</span>
                                @if($helm->stok > 0)
                                    <span class="badge-stock bg-success bg-opacity-10 text-success">Stok {{ $helm->stok }}</span>
                                @else
                                    <span class="badge-stock bg-danger bg-opacity-10 text-danger">Habis</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Brand Partners --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <h2 class="section-title">Brand Tersedia</h2>
            <p class="text-muted">Merk helm ternama dengan kualitas terjamin</p>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="100">
            @foreach($merks as $merk)
            <a href="{{ route('katalog', ['merk_filter' => $merk->id_merk]) }}" class="btn btn-light fw-semibold rounded-pill px-4 py-2 border">
                {{ $merk->nama_merk }}
            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
