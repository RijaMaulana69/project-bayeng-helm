@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form method="POST" action="{{ route('admin.produk.update', $produk->id_helm) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card card-bayeng mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nama Helm <span class="text-danger">*</span></label>
                            <input type="text" name="nama_helm" class="form-control @error('nama_helm') is-invalid @enderror" value="{{ old('nama_helm', $produk->nama_helm) }}" required>
                            @error('nama_helm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Merk <span class="text-danger">*</span></label>
                            <select name="id_merk" class="form-select @error('id_merk') is-invalid @enderror" required>
                                <option value="">-- Pilih Merk --</option>
                                @foreach ($merks as $merk)
                                    <option value="{{ $merk->id_merk }}" {{ old('id_merk', $produk->id_merk) == $merk->id_merk ? 'selected' : '' }}>{{ $merk->nama_merk }}</option>
                                @endforeach
                            </select>
                            @error('id_merk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="id_kategori" class="form-select @error('id_kategori') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori', $produk->id_kategori) == $kategori->id_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('id_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga Awal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_awal" class="form-control @error('harga_awal') is-invalid @enderror" value="{{ old('harga_awal', $produk->harga_awal) }}" min="0" required>
                                @error('harga_awal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror" value="{{ old('harga_jual', $produk->harga_jual) }}" min="0" required>
                                @error('harga_jual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Varian --}}
            <div class="card card-bayeng mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Varian Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warna</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($warnas as $warna)
                                    <div class="form-check">
                                        <input type="checkbox" name="warna[]" value="{{ $warna->id_warna }}" class="form-check-input warna-check" id="warna_{{ $warna->id_warna }}" data-label="{{ $warna->nama_warna }}"
                                            {{ (is_array(old('warna')) && in_array($warna->id_warna, old('warna'))) || (!old('warna') && isset($existing_warnas) && in_array($warna->id_warna, $existing_warnas)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="warna_{{ $warna->id_warna }}">{{ $warna->nama_warna }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('warna')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ukuran</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach (['M', 'L', 'XL', 'XXL'] as $ukuran)
                                    <div class="form-check">
                                        <input type="checkbox" name="ukuran[]" value="{{ $ukuran }}" class="form-check-input ukuran-check" id="ukuran_{{ $ukuran }}" data-label="{{ $ukuran }}"
                                            {{ (is_array(old('ukuran')) && in_array($ukuran, old('ukuran'))) || (!old('ukuran') && isset($existing_ukurans) && in_array($ukuran, $existing_ukurans)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ukuran_{{ $ukuran }}">{{ $ukuran }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('ukuran')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Dynamic Variant Grid --}}
                    <div id="variant-grid-wrapper" style="display: none;">
                        <h6 class="fw-bold mb-3">Stok per Varian</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="variant-grid">
                                <thead class="table-light">
                                    <tr id="variant-header"></tr>
                                </thead>
                                <tbody id="variant-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Media & Deskripsi --}}
            <div class="card card-bayeng mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Gambar & Deskripsi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gambar Produk</label>
                            @if ($produk->gambar)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $produk->gambar) }}" class="rounded" style="max-height: 150px;" id="current-image">
                                    <div class="small text-muted mt-1">Gambar saat ini</div>
                                </div>
                            @endif
                            <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*" id="input-gambar">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah gambar</div>
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-2" id="preview-wrapper" style="display: none;">
                                <img id="preview-image" class="rounded" style="max-height: 200px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="5" placeholder="Deskripsi produk...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary-admin">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Existing variant data from controller
    const variantData = @json($variant_data ?? []);

    // Image preview
    document.getElementById('input-gambar').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const wrapper = document.getElementById('preview-wrapper');
        const img = document.getElementById('preview-image');
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

    // Dynamic variant grid
    function generateVariantGrid() {
        const warnas = [];
        document.querySelectorAll('.warna-check:checked').forEach(function (el) {
            warnas.push({ id: el.value, label: el.dataset.label });
        });

        const ukurans = [];
        document.querySelectorAll('.ukuran-check:checked').forEach(function (el) {
            ukurans.push(el.value);
        });

        const wrapper = document.getElementById('variant-grid-wrapper');
        const header = document.getElementById('variant-header');
        const body = document.getElementById('variant-body');

        if (warnas.length === 0 || ukurans.length === 0) {
            wrapper.style.display = 'none';
            return;
        }

        wrapper.style.display = 'block';

        // Build header
        let headerHtml = '<th>Warna \\ Ukuran</th>';
        ukurans.forEach(function (u) {
            headerHtml += '<th class="text-center">' + u + '</th>';
        });
        header.innerHTML = headerHtml;

        // Build body
        let bodyHtml = '';
        warnas.forEach(function (w) {
            bodyHtml += '<tr><td class="fw-semibold">' + w.label + '</td>';
            ukurans.forEach(function (u) {
                const key = w.id + '|' + u;
                const name = 'variant_stok[' + key + ']';
                const existingStok = variantData[key] !== undefined ? variantData[key] : 0;
                bodyHtml += '<td><input type="number" name="' + name + '" class="form-control form-control-sm text-center" value="' + existingStok + '" min="0"></td>';
            });
            bodyHtml += '</tr>';
        });
        body.innerHTML = bodyHtml;
    }

    document.querySelectorAll('.warna-check, .ukuran-check').forEach(function (el) {
        el.addEventListener('change', generateVariantGrid);
    });

    // Init on page load
    generateVariantGrid();
</script>
@endpush
