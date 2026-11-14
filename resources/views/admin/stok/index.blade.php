@extends('layouts.admin')

@section('title', 'Stok Masuk')
@section('page-title', 'Stok Masuk')

@section('content')
{{-- Add Stock Form --}}
<div class="card card-bayeng mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-boxes-stacked me-2"></i>Tambah Stok Masuk</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.stok.store') }}">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Produk Helm <span class="text-danger">*</span></label>
                    <select name="id_helm" id="select-helm" class="form-select @error('id_helm') is-invalid @enderror" required>
                        <option value="">-- Pilih Helm --</option>
                        @foreach ($helms as $helm)
                            <option value="{{ $helm->id_helm }}" {{ old('id_helm') == $helm->id_helm ? 'selected' : '' }}>{{ $helm->nama_helm }}</option>
                        @endforeach
                    </select>
                    @error('id_helm')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Varian</label>
                    <select name="id_varian" id="select-varian" class="form-select @error('id_varian') is-invalid @enderror">
                        <option value="">-- Pilih Varian --</option>
                    </select>
                    @error('id_varian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" min="1" required>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" value="{{ old('keterangan') }}" placeholder="Opsional">
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-admin w-100">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Recent Stock Entries --}}
<div class="card card-bayeng">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-clock-rotate-left me-2"></i>Riwayat Stok Masuk</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Varian</th>
                        <th class="text-center">Jumlah</th>
                        <th>Keterangan</th>
                        <th>Ditambahkan Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stok_masuks as $stok)
                        <tr>
                            <td class="small">{{ $stok->tgl_masuk ? $stok->tgl_masuk->format('d M Y, H:i') : '-' }}</td>
                            <td class="fw-semibold">{{ $stok->helm->nama_helm ?? '-' }}</td>
                            <td class="text-muted">{{ $stok->warna ?? '' }} {{ $stok->ukuran ?? '' }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">+{{ $stok->jumlah }}</span>
                            </td>
                            <td>{{ $stok->keterangan ?? '-' }}</td>
                            <td class="small text-muted">{{ $stok->user->nama_lengkap ?? $stok->user->username ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat stok masuk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if (isset($stok_masuks) && $stok_masuks instanceof \Illuminate\Pagination\LengthAwarePaginator && $stok_masuks->hasPages())
        <div class="card-footer">
            {{ $stok_masuks->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Populate varian dropdown based on selected helm
    const helmVariants = @json($helm_variants ?? []);

    document.getElementById('select-helm').addEventListener('change', function () {
        const helmId = this.value;
        const varianSelect = document.getElementById('select-varian');
        varianSelect.innerHTML = '<option value="">-- Pilih Varian --</option>';

        if (helmId && helmVariants[helmId]) {
            helmVariants[helmId].forEach(function (v) {
                const option = document.createElement('option');
                option.value = v.id_varian || v.id;
                option.textContent = v.label;
                varianSelect.appendChild(option);
            });
        }
    });
</script>
@endpush
