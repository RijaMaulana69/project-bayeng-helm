@extends('layouts.pelanggan')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.beranda') }}" class="text-decoration-none" style="color: var(--primary);">Beranda</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.cart') }}" class="text-decoration-none" style="color: var(--primary);">Keranjang</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" style="color: var(--muted);">Checkout</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4" style="color: var(--dark);">Checkout</h2>

    <form method="POST" action="{{ route('pelanggan.checkout.store') }}">
        @csrf

        <div class="row g-4">
            {{-- Left: Form --}}
            <div class="col-lg-7">
                {{-- Info Pengambilan --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: var(--dark);">
                            <i class="fa-solid fa-user me-2" style="color: var(--primary);"></i>
                            Info Pengambilan
                        </h5>

                        <div class="mb-3">
                            <label for="penerima" class="form-label fw-semibold" style="font-size: 0.875rem;">Nama Penerima</label>
                            <input type="text"
                                   id="penerima"
                                   name="penerima"
                                   class="form-control form-control-lg rounded-3 @error('penerima') is-invalid @enderror"
                                   value="{{ old('penerima', $pelanggan->nama) }}"
                                   required>
                            @error('penerima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label fw-semibold" style="font-size: 0.875rem;">No. Telepon</label>
                            <input type="text"
                                   id="telepon"
                                   name="telepon"
                                   class="form-control form-control-lg rounded-3 @error('telepon') is-invalid @enderror"
                                   value="{{ old('telepon', $pelanggan->telepon) }}"
                                   required>
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert border-0 rounded-3 d-flex align-items-start gap-2" style="background-color: #f0f7ff; color: var(--primary);">
                            <i class="fa-solid fa-store mt-1"></i>
                            <div>
                                <strong>Alamat Toko (Pengambilan):</strong><br>
                                <span style="font-size: 0.9rem;">Jl. RE Martadinata No.49, Kuningan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: var(--dark);">
                            <i class="fa-solid fa-credit-card me-2" style="color: var(--primary);"></i>
                            Metode Pembayaran
                        </h5>

                        @error('metode_bayar')
                            <div class="alert alert-danger rounded-3 py-2" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center gap-3 p-3 rounded-3 border cursor-pointer @if(old('metode_bayar') === 'transfer') border-primary @endif" style="cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="metode_bayar" value="transfer" class="form-check-input" {{ old('metode_bayar') === 'transfer' ? 'checked' : '' }}>
                                <div>
                                    <span class="fw-semibold" style="color: var(--dark);">Transfer Bank</span>
                                    <small class="d-block" style="color: var(--muted);">Transfer ke rekening toko</small>
                                </div>
                            </label>

                            <label class="d-flex align-items-center gap-3 p-3 rounded-3 border cursor-pointer @if(old('metode_bayar') === 'qris') border-primary @endif" style="cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="metode_bayar" value="qris" class="form-check-input" {{ old('metode_bayar') === 'qris' ? 'checked' : '' }}>
                                <div>
                                    <span class="fw-semibold" style="color: var(--dark);">QRIS</span>
                                    <small class="d-block" style="color: var(--muted);">Bayar via QRIS</small>
                                </div>
                            </label>

                            <label class="d-flex align-items-center gap-3 p-3 rounded-3 border cursor-pointer @if(old('metode_bayar') === 'bayar_di_toko') border-primary @endif" style="cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="metode_bayar" value="bayar_di_toko" class="form-check-input" {{ old('metode_bayar') === 'bayar_di_toko' ? 'checked' : '' }}>
                                <div>
                                    <span class="fw-semibold" style="color: var(--dark);">Bayar di Toko Saat Ambil</span>
                                    <small class="d-block" style="color: var(--muted);">Bayar langsung saat pengambilan</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--dark);">
                            <i class="fa-solid fa-message me-2" style="color: var(--primary);"></i>
                            Catatan <span class="fw-normal" style="color: var(--muted); font-size: 0.85rem;">(opsional)</span>
                        </h5>
                        <textarea name="catatan"
                                  rows="3"
                                  class="form-control rounded-3 @error('catatan') is-invalid @enderror"
                                  placeholder="Tambahkan catatan untuk pesanan Anda...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Right: Order Summary --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: var(--dark);">
                            <i class="fa-solid fa-receipt me-2" style="color: var(--primary);"></i>
                            Ringkasan Pesanan
                        </h5>

                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach($cartItems as $item)
                                <div class="d-flex justify-content-between align-items-start pb-3 border-bottom">
                                    <div class="flex-grow-1">
                                        <p class="fw-semibold mb-1" style="color: var(--dark); font-size: 0.9rem;">
                                            {{ $item->varian->helm->nama_helm ?? '-' }}
                                        </p>
                                        <small style="color: var(--muted);">
                                            {{ $item->varian->warna ?? '-' }} / {{ $item->varian->ukuran ?? '-' }}
                                        </small>
                                        <br>
                                        <small style="color: var(--muted);">
                                            {{ $item->jumlah }} x Rp{{ number_format($item->varian->helm->harga ?? 0, 0, ',', '.') }}
                                        </small>
                                    </div>
                                    <span class="fw-semibold text-nowrap" style="color: var(--dark); font-size: 0.9rem;">
                                        Rp{{ number_format(($item->varian->helm->harga ?? 0) * $item->jumlah, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total --}}
                        <div class="d-flex justify-content-between align-items-center pt-2 mb-4">
                            <span class="fw-bold" style="color: var(--dark); font-size: 1.1rem;">Total</span>
                            <span class="fw-bold" style="color: var(--primary); font-size: 1.25rem;">
                                @php
                                    $total = $cartItems->sum(function ($item) {
                                        return ($item->varian->helm->harga ?? 0) * $item->jumlah;
                                    });
                                @endphp
                                Rp{{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-primary-bayeng btn-lg w-100 rounded-3 fw-semibold">
                            <i class="fa-solid fa-lock me-2"></i>
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
