@extends('layouts.pelanggan')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    {{-- Success Icon --}}
                    <div class="mb-4">
                        <i class="fa-solid fa-circle-check fa-4x" style="color: #22c55e;"></i>
                    </div>

                    <h3 class="fw-bold mb-2" style="color: var(--dark);">Pesanan Berhasil Dibuat!</h3>
                    <p class="mb-4" style="color: var(--muted);">
                        Terima kasih telah berbelanja di Bayeng Putra Helm.
                    </p>

                    {{-- Order Number --}}
                    <div class="mb-3">
                        <small class="text-uppercase fw-semibold" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                            Nomor Pesanan
                        </small>
                        <p class="fw-bold mb-0" style="color: var(--dark); font-size: 1.1rem;">
                            {{ $pesanan->no_pesanan }}
                        </p>
                    </div>

                    {{-- Pickup Code --}}
                    <div class="mb-3">
                        <small class="text-uppercase fw-semibold" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                            Kode Pengambilan
                        </small>
                        <div class="bg-light rounded-3 py-3 mt-1 mx-auto" style="max-width: 280px;">
                            <span class="fw-bold" style="color: var(--primary); font-size: 1.75rem; letter-spacing: 0.15em;">
                                {{ $pesanan->kode_pickup }}
                            </span>
                        </div>
                    </div>

                    {{-- Batas Pengambilan --}}
                    <div class="mb-4">
                        <small class="text-uppercase fw-semibold" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                            Batas Pengambilan
                        </small>
                        <p class="fw-semibold mb-0" style="color: var(--dark);">
                            {{ $pesanan->batas_ambil->format('d M Y') }}
                        </p>
                    </div>

                    {{-- Payment Instructions (for transfer / qris) --}}
                    @if(in_array($pesanan->metode_bayar, ['transfer', 'qris']))
                        <div class="alert border-0 rounded-3 text-start mb-4" style="background-color: #fef9c3; color: #854d0e;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                                <div>
                                    <strong>Instruksi Pembayaran</strong>
                                    @if($pesanan->metode_bayar === 'transfer')
                                        <p class="mb-1 mt-2" style="font-size: 0.9rem;">
                                            Silakan transfer ke rekening berikut:
                                        </p>
                                        <p class="mb-1 fw-semibold" style="font-size: 0.9rem;">
                                            Bank BCA: 123-456-7890<br>
                                            a/n Bayeng Putra Helm
                                        </p>
                                    @else
                                        <p class="mb-1 mt-2" style="font-size: 0.9rem;">
                                            Silakan lakukan pembayaran melalui QRIS. Scan QR code yang tersedia di halaman detail pesanan.
                                        </p>
                                    @endif
                                    <p class="mb-0" style="font-size: 0.85rem;">
                                        Setelah melakukan pembayaran, unggah bukti bayar di halaman detail pesanan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Status Badge --}}
                    <div class="mb-4">
                        <small class="text-uppercase fw-semibold d-block mb-1" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                            Status
                        </small>
                        {!! $pesanan->status_badge !!}
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('pelanggan.pesanan.detail', $pesanan->id_pesanan) }}" class="btn btn-primary-bayeng rounded-3 px-4 fw-semibold">
                            <i class="fa-solid fa-eye me-2"></i>
                            Lihat Detail Pesanan
                        </a>
                        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-bayeng rounded-3 px-4 fw-semibold">
                            <i class="fa-solid fa-bag-shopping me-2"></i>
                            Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
