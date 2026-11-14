@extends('layouts.admin')

@section('title', 'Validasi Pickup')
@section('page-title', 'Validasi Pickup')

@section('content')
{{-- Pickup Code Input --}}
<div class="row justify-content-center mb-4">
    <div class="col-lg-6">
        <div class="card card-bayeng">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-qrcode fa-3x text-primary"></i>
                </div>
                <h4 class="fw-bold mb-1">Masukkan Kode Pickup</h4>
                <p class="text-muted mb-4">Masukkan kode yang ditunjukkan pelanggan</p>

                <form method="POST" action="{{ route('admin.pickup.validasi') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="text"
                               name="kode_pickup"
                               class="form-control form-control-lg text-center text-uppercase font-monospace @error('kode_pickup') is-invalid @enderror"
                               placeholder="XXXXXX"
                               value="{{ old('kode_pickup') }}"
                               maxlength="10"
                               autofocus
                               required>
                        @error('kode_pickup')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary-admin btn-lg px-5">
                        <i class="fas fa-search me-2"></i> Cari Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Ready Orders --}}
<div class="card card-bayeng">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>Pesanan Siap Diambil</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Kode Pickup</th>
                        <th class="text-end">Grand Total</th>
                        <th>Batas Ambil</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanan_ready as $pesanan)
                        <tr>
                            <td class="fw-semibold">{{ $pesanan->no_pesanan }}</td>
                            <td>{{ $pesanan->pelanggan->nama ?? '-' }}</td>
                            <td><code class="fs-6">{{ $pesanan->kode_pickup }}</code></td>
                            <td class="text-end">{{ format_rupiah($pesanan->grand_total) }}</td>
                            <td class="small">
                                @if ($pesanan->batas_ambil)
                                    {{ \Carbon\Carbon::parse($pesanan->batas_ambil)->format('d M Y, H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{!! $pesanan->status_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada pesanan yang siap diambil</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
