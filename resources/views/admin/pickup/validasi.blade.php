@extends('layouts.admin')

@section('title', 'Validasi Pickup')
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-bayeng">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Detail Pesanan</h5>
                {!! $pesanan->status_badge !!}
            </div>
            <div class="card-body">
                {{-- Order Info --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="130">No. Pesanan</td>
                                <td class="fw-bold">{{ $pesanan->no_pesanan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pelanggan</td>
                                <td>{{ $pesanan->pelanggan->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block mb-1">Kode Pickup</small>
                            <h2 class="fw-bold text-primary font-monospace mb-0">{{ $pesanan->kode_pickup }}</h2>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <h6 class="fw-bold mb-3">Item Pesanan</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Varian</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesanan->items as $item)
                                <tr>
                                    <td>{{ $item->helm->nama_helm ?? $item->nama_helm ?? '-' }}</td>
                                    <td class="text-muted">{{ $item->warna ?? '-' }} / {{ $item->ukuran ?? '-' }}</td>
                                    <td class="text-center">{{ $item->jumlah }}</td>
                                    <td class="text-end">{{ format_rupiah($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Grand Total</td>
                                <td class="text-end text-primary fs-5">{{ format_rupiah($pesanan->grand_total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-3">
                    <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg w-100" onclick="return confirm('Konfirmasi serahkan barang ke pelanggan?')">
                            <i class="fas fa-handshake me-2"></i> Serahkan Barang
                        </button>
                    </form>
                    <a href="{{ route('admin.pickup.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
