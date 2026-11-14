@extends('layouts.admin')

@section('title', 'Pesanan Online')
@section('page-title', 'Pesanan Online')

@section('content')
{{-- Filter Bar --}}
<div class="card card-bayeng mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pesanan.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu_pembayaran" {{ request('status') == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="pembayaran_dikonfirmasi" {{ request('status') == 'pembayaran_dikonfirmasi' ? 'selected' : '' }}>Pembayaran Dikonfirmasi</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="siap_diambil" {{ request('status') == 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="No. pesanan, nama pelanggan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-admin w-100">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
            </div>
            @if(request()->hasAny(['status', 'search']))
                <div class="col-md-2">
                    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card card-bayeng">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Penerima</th>
                        <th>Kode Pickup</th>
                        <th class="text-end">Grand Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanans as $pesanan)
                        <tr>
                            <td class="fw-semibold">{{ $pesanan->no_pesanan }}</td>
                            <td>{{ $pesanan->pelanggan->nama ?? '-' }}</td>
                            <td>{{ $pesanan->penerima ?? '-' }}</td>
                            <td>
                                @if ($pesanan->kode_pickup)
                                    <code class="fs-6">{{ $pesanan->kode_pickup }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">{{ format_rupiah($pesanan->grand_total) }}</td>
                            <td>{!! $pesanan->status_badge !!}</td>
                            <td class="text-muted small">{{ $pesanan->created_at->format('d M Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.pesanan.show', $pesanan->id_pesanan) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada data pesanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($pesanans->hasPages())
        <div class="card-footer">
            {{ $pesanans->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
