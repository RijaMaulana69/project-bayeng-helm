@extends('layouts.pelanggan')

@section('title', $helm->nama_helm)

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.beranda') }}" class="text-decoration-none" style="color: var(--primary);">Beranda</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.katalog') }}" class="text-decoration-none" style="color: var(--primary);">Katalog</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" style="color: var(--muted);">{{ $helm->nama_helm }}</li>
        </ol>
    </nav>

    {{-- Product Detail --}}
    <div class="row g-4 g-lg-5 mb-5">
        {{-- Left: Product Image --}}
        <div class="col-lg-6">
            <div class="position-relative overflow-hidden rounded-4 bg-light" style="max-height: 450px;">
                <img src="{{ asset('storage/' . $helm->gambar) }}"
                     alt="{{ $helm->nama_helm }}"
                     class="w-100 h-100"
                     style="object-fit: cover; max-height: 450px;">
            </div>
        </div>

        {{-- Right: Product Info --}}
        <div class="col-lg-6">
            {{-- Merk Badge --}}
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background-color: rgba(37, 99, 235, 0.1); color: var(--primary); font-size: 0.8rem;">
                {{ $helm->merk->nama_merk ?? '-' }}
            </span>

            {{-- Product Name --}}
            <h2 class="fw-bold mb-3" style="color: var(--dark);">{{ $helm->nama_helm }}</h2>

            {{-- Price --}}
            <p class="fw-bold mb-3" style="color: var(--primary); font-size: 1.75rem;">
                Rp{{ number_format($helm->harga, 0, ',', '.') }}
            </p>

            {{-- Category Badge --}}
            <div class="mb-3">
                <span class="badge rounded-pill px-3 py-2" style="background-color: #f1f5f9; color: var(--dark); font-size: 0.8rem;">
                    <i class="fa-solid fa-tag me-1"></i>
                    {{ $helm->kategori->nama_kategori ?? '-' }}
                </span>
            </div>

            {{-- Stock Info --}}
            <div class="d-flex align-items-center gap-2 mb-4">
                @php
                    $totalStok = $helm->varians->sum('stok');
                @endphp
                @if($totalStok > 0)
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">
                        <i class="fa-solid fa-check-circle me-1"></i> Stok Tersedia ({{ $totalStok }})
                    </span>
                @else
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">
                        <i class="fa-solid fa-times-circle me-1"></i> Stok Habis
                    </span>
                @endif
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <h6 class="fw-bold mb-2" style="color: var(--dark);">Deskripsi</h6>
                <p class="mb-0" style="color: var(--muted); line-height: 1.7; font-size: 0.95rem;">
                    {{ $helm->deskripsi ?? 'Tidak ada deskripsi.' }}
                </p>
            </div>

            {{-- Add to Cart --}}
            <livewire:add-to-cart-button :helm="$helm" />
        </div>
    </div>

    {{-- Variant Table --}}
    @if($helm->varians->count())
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: var(--dark);">Varian Tersedia</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr style="color: var(--muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <th class="border-0 fw-semibold">Warna</th>
                                <th class="border-0 fw-semibold">Ukuran</th>
                                <th class="border-0 fw-semibold text-center">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($helm->varians as $varian)
                                <tr>
                                    <td class="fw-medium" style="color: var(--dark);">{{ $varian->warna }}</td>
                                    <td style="color: var(--dark);">{{ $varian->ukuran }}</td>
                                    <td class="text-center">
                                        @if($varian->stok > 0)
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3">{{ $varian->stok }}</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3">Habis</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if(isset($related) && $related->count())
        <div class="mb-5">
            <h4 class="fw-bold mb-4" style="color: var(--dark);">Produk Terkait</h4>
            <div class="row g-4">
                @foreach($related->take(4) as $item)
                    <div class="col-6 col-md-3">
                        <div class="product-card card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                            <a href="{{ route('pelanggan.katalog.detail', $item->id_helm) }}" class="text-decoration-none">
                                <div class="overflow-hidden" style="height: 200px;">
                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                         alt="{{ $item->nama_helm }}"
                                         class="w-100 h-100"
                                         style="object-fit: cover; transition: transform 0.3s;">
                                </div>
                                <div class="card-body p-3">
                                    <p class="mb-1" style="color: var(--muted); font-size: 0.8rem;">{{ $item->merk->nama_merk ?? '-' }}</p>
                                    <h6 class="fw-bold mb-2 text-truncate" style="color: var(--dark);">{{ $item->nama_helm }}</h6>
                                    <p class="fw-bold mb-0" style="color: var(--primary);">
                                        Rp{{ number_format($item->harga, 0, ',', '.') }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
