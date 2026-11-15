@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-bayeng">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-gear me-2"></i>Pengaturan Toko</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" name="nama_toko" class="form-control @error('nama_toko') is-invalid @enderror" value="{{ old('nama_toko', $pengaturan->nama_toko ?? '') }}" required>
                            @error('nama_toko')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $pengaturan->alamat ?? '') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telepon</label>
                            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $pengaturan->telepon ?? '') }}">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Logo Toko</label>
                            @if (isset($pengaturan) && $pengaturan->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo" class="rounded" style="max-height: 80px;">
                                    <div class="small text-muted mt-1">Logo saat ini</div>
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*" id="input-logo">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah logo</div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-2" id="preview-wrapper" style="display: none;">
                                <img id="preview-logo" class="rounded" style="max-height: 80px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary-admin">
                            <i class="fas fa-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const inputLogo = document.getElementById('input-logo');
        if (!inputLogo) return;

        inputLogo.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const wrapper = document.getElementById('preview-wrapper');
            const img = document.getElementById('preview-logo');
            if (file) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    img.src = ev.target.result;
                    wrapper.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                wrapper.style.display = 'none';
            }
        });
    })();
</script>
@endpush
