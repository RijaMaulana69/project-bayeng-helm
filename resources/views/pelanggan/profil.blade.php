@extends('layouts.pelanggan')

@section('title', 'Profil')

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('pelanggan.beranda') }}" class="text-decoration-none" style="color: var(--primary);">Beranda</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" style="color: var(--muted);">Profil</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h2 class="fw-bold mb-4" style="color: var(--dark);">
                <i class="fa-solid fa-user-circle me-2" style="color: var(--primary);"></i>
                Profil Saya
            </h2>

            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('pelanggan.profil.update') }}">
                        @csrf
                        @method('PUT')

                        {{-- Email (readonly) --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold" style="font-size: 0.875rem;">Email</label>
                            <input type="email"
                                   id="email"
                                   class="form-control form-control-lg rounded-3"
                                   value="{{ $pelanggan->email }}"
                                   readonly
                                   disabled
                                   style="background-color: #f8fafc;">
                            <div class="form-text" style="font-size: 0.8rem; color: var(--muted);">
                                Email tidak dapat diubah.
                            </div>
                        </div>

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-semibold" style="font-size: 0.875rem;">Nama Lengkap</label>
                            <input type="text"
                                   id="nama"
                                   name="nama"
                                   class="form-control form-control-lg rounded-3 @error('nama') is-invalid @enderror"
                                   value="{{ old('nama', $pelanggan->nama) }}"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Telepon --}}
                        <div class="mb-4">
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

                        {{-- Password Section --}}
                        <hr class="my-4">

                        <h6 class="fw-bold mb-1" style="color: var(--dark);">Ubah Password</h6>
                        <p class="mb-3" style="color: var(--muted); font-size: 0.85rem;">
                            Kosongkan jika tidak ingin mengubah password.
                        </p>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold" style="font-size: 0.875rem;">Password Baru</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror"
                                   placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold" style="font-size: 0.875rem;">Konfirmasi Password Baru</label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control form-control-lg rounded-3"
                                   placeholder="Ulangi password baru">
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-primary-bayeng btn-lg rounded-3 fw-semibold px-5">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
