@extends('layouts.pelanggan')

@section('title', 'Login')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 450px;">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                {{-- Logo --}}
                <div class="text-center mb-4">
                    <a href="{{ route('pelanggan.beranda') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Bayeng Putra Helm" height="48">
                    </a>
                </div>

                <h4 class="fw-bold text-center mb-1" style="color: var(--dark);">Masuk ke Akun Anda</h4>
                <p class="text-center mb-4" style="color: var(--muted); font-size: 0.9rem;">
                    Selamat datang kembali! Silakan masuk untuk melanjutkan.
                </p>

                <form method="POST" action="{{ route('pelanggan.login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold" style="font-size: 0.875rem;">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               required
                               autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold" style="font-size: 0.875rem;">Password</label>
                        <div class="input-group">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control form-control-lg rounded-start-3 @error('password') is-invalid @enderror"
                                   placeholder="••••••••"
                                   required>
                            <button class="btn btn-outline-secondary rounded-end-3 border-start-0" type="button" id="togglePassword" style="border-color: #dee2e6;">
                                <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Lupa Password --}}
                    <div class="d-flex justify-content-end mb-4">
                        <a href="#" class="text-decoration-none fw-semibold" style="color: var(--primary); font-size: 0.875rem;">
                            Lupa Password?
                        </a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-primary-bayeng btn-lg w-100 rounded-3 fw-semibold">
                        Masuk
                    </button>
                </form>

                {{-- Register Link --}}
                <p class="text-center mt-4 mb-0" style="color: var(--muted); font-size: 0.9rem;">
                    Belum punya akun?
                    <a href="{{ route('pelanggan.register') }}" class="text-decoration-none fw-semibold" style="color: var(--primary);">
                        Daftar Sekarang
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
@endpush
