@extends('layouts.pelanggan')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.beranda') }}" class="text-decoration-none" style="color: var(--primary);">Beranda</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" style="color: var(--muted);">Pesanan Saya</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4" style="color: var(--dark);">
        <i class="fa-solid fa-box me-2" style="color: var(--primary);"></i>
        Pesanan Saya
    </h2>

    @forelse($pesanans as $pesanan)
        <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                    {{-- Left: Order Info --}}
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fw-bold" style="color: var(--dark);">{{ $pesanan->no_pesanan }}</span>
                            {!! $pesanan->status_badge !!}
                        </div>

                        <p class="mb-2" style="color: var(--muted); font-size: 0.875rem;">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{ $pesanan->created_at->format('d M Y, H:i') }}
                        </p>

                        <div class="d-flex align-items-center gap-3">
                            <span class="fw-bold" style="color: var(--primary); font-size: 1.15rem;">
                                Rp{{ number_format($pesanan->grand_total, 0, ',', '.') }}
                            </span>

                            @if($pesanan->kode_pickup)
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #f1f5f9; color: var(--dark); font-size: 0.8rem;">
                                    <i class="fa-solid fa-key me-1"></i>
                                    Kode: <strong>{{ $pesanan->kode_pickup }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Right: Action --}}
                    <div class="flex-shrink-0">
                        <a href="{{ route('pelanggan.pesanan.detail', $pesanan->id_pesanan) }}"
                           class="btn btn-outline-bayeng rounded-3 fw-semibold">
                            <i class="fa-solid fa-arrow-right me-1"></i>
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        {{-- Empty State --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5 text-center">
                <i class="fa-solid fa-box-open fa-3x mb-3" style="color: #cbd5e1;"></i>
                <h5 class="fw-bold mb-2" style="color: var(--dark);">Belum Ada Pesanan</h5>
                <p class="mb-4" style="color: var(--muted);">Anda belum memiliki pesanan. Mulai belanja sekarang!</p>
                <a href="{{ route('pelanggan.katalog') }}" class="btn btn-primary-bayeng rounded-3 px-4 fw-semibold">
                    <i class="fa-solid fa-bag-shopping me-2"></i>
                    Mulai Belanja
                </a>
            </div>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($pesanans->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $pesanans->links() }}
        </div>
    @endif
</div>
@endsection
