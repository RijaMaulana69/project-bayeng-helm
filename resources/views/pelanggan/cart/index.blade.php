@extends('layouts.pelanggan')

@section('title', 'Keranjang')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.beranda') }}" class="text-decoration-none" style="color: var(--primary);">Beranda</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" style="color: var(--muted);">Keranjang</li>
        </ol>
    </nav>

    {{-- Heading --}}
    <h2 class="fw-bold mb-4" style="color: var(--dark);">
        <i class="fa-solid fa-cart-shopping me-2" style="color: var(--primary);"></i>
        Keranjang Belanja
    </h2>

    {{-- Livewire Cart Manager --}}
    <livewire:cart-manager />
</div>
@endsection
