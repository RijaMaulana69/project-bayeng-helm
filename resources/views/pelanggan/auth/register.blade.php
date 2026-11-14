@extends('layouts.pelanggan')

@section('title', 'Daftar')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 500px;">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                {{-- Heading --}}
                <h4 class="fw-bold text-center mb-1" style="color: var(--dark);">Buat Akun Baru</h4>
                <p class="text-center mb-4" style="color: var(--muted); font-size: 0.9rem;">
                    Daftar untuk mulai berbelanja helm berkualitas.
                </p>

                <form method="POST" action="{{ route('pelanggan.register') }}">
                    @csrf

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-semibold" style="font-size: 0.875rem;">Nama Lengkap</label>
                        <input type="text"
                               id="nama"
                               name="nama"
                               class="form-control form-control-lg rounded-3 @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}"
                               placeholder="Masukkan nama lengkap"
                               required
                               autofocus>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold" style="font-size: 0.875rem;">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Telepon --}}
                    <div class="mb-3">
                        <label for="telepon" class="form-label fw-semibold" style="font-size: 0.875rem;">No. Telepon</label>
                        <input type="text"
                               id="telepon"
                               name="telepon"
                               class="form-control form-control-lg rounded-3 @error('telepon') is-invalid @enderror"
                               value="{{ old('telepon') }}"
                               placeholder="08xxxxxxxxxx"
                               required>
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold" style="font-size: 0.875rem;">Password</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror"
                               placeholder="••••••••"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" style="font-size: 0.8rem; color: var(--muted);">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            Min 8 karakter, huruf besar + kecil + angka
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold" style="font-size: 0.875rem;">Konfirmasi Password</label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="form-control form-control-lg rounded-3"
                               placeholder="Ulangi password"
                               required>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-primary-bayeng btn-lg w-100 rounded-3 fw-semibold">
                        Daftar
                    </button>
                </form>

                {{-- Login Link --}}
                <p class="text-center mt-4 mb-0" style="color: var(--muted); font-size: 0.9rem;">
                    Sudah punya akun?
                    <a href="{{ route('pelanggan.login') }}" class="text-decoration-none fw-semibold" style="color: var(--primary);">
                        Masuk
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
