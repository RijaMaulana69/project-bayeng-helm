@extends('layouts.pelanggan')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.beranda') }}" class="text-decoration-none" style="color: var(--primary);">Beranda</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.pesanan') }}" class="text-decoration-none" style="color: var(--primary);">Pesanan Saya</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" style="color: var(--muted);">Detail</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4" style="color: var(--dark);">Detail Pesanan</h2>

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- Order Info --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <small class="text-uppercase fw-semibold" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                                Nomor Pesanan
                            </small>
                            <p class="fw-bold mb-0" style="color: var(--dark); font-size: 1.1rem;">{{ $pesanan->no_pesanan }}</p>
                        </div>
                        <div>
                            <small class="text-uppercase fw-semibold" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                                Tanggal
                            </small>
                            <p class="fw-medium mb-0" style="color: var(--dark);">{{ $pesanan->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <small class="text-uppercase fw-semibold" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                                Status
                            </small>
                            <p class="mb-0">{!! $pesanan->status_badge !!}</p>
                        </div>
                    </div>

                    {{-- Kode Pickup --}}
                    @if($pesanan->kode_pickup)
                        <div class="bg-light rounded-3 py-3 px-4 text-center mb-3">
                            <small class="text-uppercase fw-semibold d-block mb-1" style="color: var(--muted); letter-spacing: 0.05em; font-size: 0.75rem;">
                                Kode Pengambilan
                            </small>
                            <span class="fw-bold" style="color: var(--primary); font-size: 1.75rem; letter-spacing: 0.15em;">
                                {{ $pesanan->kode_pickup }}
                            </span>
                        </div>
                    @endif

                    {{-- Batas Pengambilan --}}
                    @if($pesanan->batas_ambil)
                        <div class="text-center">
                            <small style="color: var(--muted); font-size: 0.85rem;">
                                <i class="fa-regular fa-clock me-1"></i>
                                Batas Pengambilan: <strong style="color: var(--dark);">{{ $pesanan->batas_ambil->format('d M Y') }}</strong>
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: var(--dark);">Item Pesanan</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr style="color: var(--muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <th class="border-0 fw-semibold">Produk</th>
                                    <th class="border-0 fw-semibold">Warna</th>
                                    <th class="border-0 fw-semibold">Ukuran</th>
                                    <th class="border-0 fw-semibold text-end">Harga</th>
                                    <th class="border-0 fw-semibold text-center">Qty</th>
                                    <th class="border-0 fw-semibold text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->items as $item)
                                    <tr>
                                        <td class="fw-medium" style="color: var(--dark);">{{ $item->nama_helm }}</td>
                                        <td style="color: var(--muted);">{{ $item->warna }}</td>
                                        <td style="color: var(--muted);">{{ $item->ukuran }}</td>
                                        <td class="text-end" style="color: var(--dark);">Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-center" style="color: var(--dark);">{{ $item->jumlah }}</td>
                                        <td class="text-end fw-semibold" style="color: var(--dark);">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Upload Bukti Bayar --}}
            @if($pesanan->status === 'pending' && in_array($pesanan->metode_bayar, ['transfer', 'qris']))
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--dark);">
                            <i class="fa-solid fa-upload me-2" style="color: var(--primary);"></i>
                            Upload Bukti Pembayaran
                        </h5>

                        <form method="POST"
                              action="{{ route('pelanggan.pesanan.upload-bukti', $pesanan->id_pesanan) }}"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <input type="file"
                                       name="bukti_bayar"
                                       class="form-control rounded-3 @error('bukti_bayar') is-invalid @enderror"
                                       accept="image/*"
                                       required>
                                @error('bukti_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" style="font-size: 0.8rem; color: var(--muted);">
                                    Format: JPG, PNG, JPEG. Maks: 2MB
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-bayeng rounded-3 fw-semibold">
                                <i class="fa-solid fa-paper-plane me-2"></i>
                                Kirim Bukti Bayar
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Bukti Bayar Thumbnail --}}
            @if($pesanan->bukti_bayar)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--dark);">
                            <i class="fa-solid fa-image me-2" style="color: var(--primary);"></i>
                            Bukti Pembayaran
                        </h5>
                        <a href="{{ asset('storage/' . $pesanan->bukti_bayar) }}" target="_blank">
                            <img src="{{ asset('storage/' . $pesanan->bukti_bayar) }}"
                                 alt="Bukti Bayar"
                                 class="rounded-3 shadow-sm"
                                 style="max-width: 300px; max-height: 400px; object-fit: cover;">
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Payment Info --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: var(--dark);">Informasi Pembayaran</h5>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span style="color: var(--muted); font-size: 0.9rem;">Metode Pembayaran</span>
                        <span class="fw-semibold" style="color: var(--dark);">
                            @switch($pesanan->metode_bayar)
                                @case('transfer')
                                    Transfer Bank
                                    @break
                                @case('qris')
                                    QRIS
                                    @break
                                @case('bayar_di_toko')
                                    Bayar di Toko
                                    @break
                                @default
                                    {{ $pesanan->metode_bayar }}
                            @endswitch
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color: var(--dark); font-size: 1.05rem;">Grand Total</span>
                        <span class="fw-bold" style="color: var(--primary); font-size: 1.25rem;">
                            Rp{{ number_format($pesanan->grand_total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Status Timeline --}}
            @if($pesanan->logs && $pesanan->logs->count())
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--dark);">
                            <i class="fa-solid fa-timeline me-2" style="color: var(--primary);"></i>
                            Riwayat Status
                        </h5>

                        <div class="list-group list-group-flush">
                            @foreach($pesanan->logs as $log)
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 10px; height: 10px; background-color: var(--primary);"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="fw-semibold mb-0" style="color: var(--dark); font-size: 0.9rem;">
                                                {{ $log->status }}
                                            </p>
                                            <small style="color: var(--muted); font-size: 0.8rem;">
                                                {{ $log->created_at->format('d M Y, H:i') }}
                                            </small>
                                            @if($log->catatan)
                                                <p class="mb-0 mt-1" style="color: var(--muted); font-size: 0.85rem;">
                                                    {{ $log->catatan }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
