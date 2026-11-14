@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="row g-4">
    {{-- Order Info --}}
    <div class="col-lg-8">
        <div class="card card-bayeng mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Informasi Pesanan</h5>
                {!! $pesanan->status_badge !!}
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="140">No. Pesanan</td>
                                <td class="fw-bold">{{ $pesanan->no_pesanan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal</td>
                                <td>{{ $pesanan->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pelanggan</td>
                                <td>{{ $pesanan->pelanggan->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $pesanan->pelanggan->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telepon</td>
                                <td>{{ $pesanan->pelanggan->no_hp ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 text-center mb-3">
                            <small class="text-muted d-block mb-1">Kode Pickup</small>
                            <h2 class="fw-bold text-primary font-monospace mb-0">{{ $pesanan->kode_pickup ?? '-' }}</h2>
                        </div>
                        @if ($pesanan->batas_ambil)
                            <div class="text-center">
                                <small class="text-muted">Batas Pengambilan:</small>
                                <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($pesanan->batas_ambil)->format('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card card-bayeng mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Item Pesanan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Varian</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesanan->items as $item)
                                <tr>
                                    <td>{{ $item->helm->nama_helm ?? $item->nama_helm ?? '-' }}</td>
                                    <td>
                                        <span class="text-muted">{{ $item->warna ?? '-' }} / {{ $item->ukuran ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">{{ $item->jumlah }}</td>
                                    <td class="text-end">{{ format_rupiah($item->harga) }}</td>
                                    <td class="text-end fw-semibold">{{ format_rupiah($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Grand Total</td>
                                <td class="text-end text-primary fs-5">{{ format_rupiah($pesanan->grand_total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Bukti Bayar --}}
        @if ($pesanan->bukti_bayar)
            <div class="card card-bayeng mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Bukti Pembayaran</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $pesanan->bukti_bayar) }}" alt="Bukti Bayar" class="img-fluid rounded" style="max-height: 400px; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                </div>
            </div>
        @endif

        {{-- Status Log Timeline --}}
        <div class="card card-bayeng">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-timeline me-2"></i>Riwayat Status</h5>
            </div>
            <div class="card-body">
                @if ($pesanan->logs && $pesanan->logs->count())
                    <div class="timeline">
                        @foreach ($pesanan->logs->sortByDesc('created_at') as $log)
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="fas fa-circle-dot text-white small"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $log->status ?? $log->aksi ?? '-' }}</p>
                                    @if ($log->catatan)
                                        <p class="mb-0 text-muted small">{{ $log->catatan }}</p>
                                    @endif
                                    <small class="text-muted">
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                        @if ($log->user)
                                            &middot; oleh {{ $log->user->nama_lengkap ?? $log->user->username }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Belum ada riwayat</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar: Status Update --}}
    <div class="col-lg-4">
        <div class="card card-bayeng mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-arrows-rotate me-2"></i>Update Status</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Baru</label>
                        <select name="status" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            @if (isset($allowed_statuses))
                                @foreach ($allowed_statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan opsional...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary-admin w-100">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-secondary w-100">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
