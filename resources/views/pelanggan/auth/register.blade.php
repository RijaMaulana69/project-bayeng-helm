@extends('layouts.auth')
@section('title', 'Daftar Akun Baru - Bayeng Putra Helm')
@section('container-width', 'max-w-[430px]')

@section('content')
<div class="bg-white sm:rounded-2xl sm:border sm:border-slate-200/90 sm:shadow-sm p-5 sm:p-8 space-y-6">
    
    {{-- Header --}}
    <div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 group mb-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-helmet-safety text-sm"></i>
            </div>
            <span class="font-extrabold text-sm tracking-tight text-slate-900">BAYENG PUTRA HELM</span>
        </a>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            Daftar Akun Baru
        </h1>
    </div>

    {{-- Main Register Form --}}
    <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-4" 
          x-data="{ 
              showPass: false, 
              showConfirmPass: false,
              password: '',
              get hasMinLen() { return this.password.length >= 8 },
              get hasUpper() { return /[A-Z]/.test(this.password) },
              get hasLower() { return /[a-z]/.test(this.password) },
              get hasNumber() { return /\d/.test(this.password) },
              get score() {
                  let s = 0;
                  if (this.hasMinLen) s++;
                  if (this.hasUpper) s++;
                  if (this.hasLower) s++;
                  if (this.hasNumber) s++;
                  return s;
              }
          }">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   id="nama" 
                   name="nama" 
                   autocomplete="name"
                   value="{{ old('nama') }}" 
                   maxlength="20"
                   required 
                   autofocus
                   placeholder="Masukkan Nama Lengkap"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            @error('nama')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- No. WhatsApp / Telepon --}}
        <div>
            <label for="telepon" class="block text-xs font-semibold text-slate-700 mb-1.5">
                No. Handphone<span class="text-rose-500">*</span>
            </label>
            <input type="tel" 
                   id="telepon" 
                   name="telepon" 
                   inputmode="numeric"
                   maxlength="15"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   autocomplete="tel"
                   value="{{ old('telepon') }}" 
                   required 
                   placeholder="Masukkan Nomor Handphone"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            @error('telepon')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Alamat Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   inputmode="email"
                   maxlength="30"
                   autocomplete="email"
                   value="{{ old('email') }}" 
                   required 
                   placeholder="nama@email.com"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            @error('email')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       x-model="password"
                       minlength="8"
                       maxlength="12"
                       autocomplete="new-password"
                       required
                       placeholder="Masukkan password"
                       class="w-full h-11 sm:h-12 pl-3.5 pr-11 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <button type="button" 
                        @click="showPass = !showPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                        title="Tampilkan / Sembunyikan Password">
                    <i class="fa-solid text-sm" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror

            {{-- Dynamic Password Strength Meter (Clean Bars) --}}
            <div class="mt-2" x-show="password.length > 0">
                <div class="grid grid-cols-4 gap-1.5">
                    <div class="h-1 rounded-full transition-colors duration-200" :class="score >= 1 ? (score <= 2 ? 'bg-amber-400' : 'bg-emerald-500') : 'bg-slate-200'"></div>
                    <div class="h-1 rounded-full transition-colors duration-200" :class="score >= 2 ? (score <= 2 ? 'bg-amber-400' : 'bg-emerald-500') : 'bg-slate-200'"></div>
                    <div class="h-1 rounded-full transition-colors duration-200" :class="score >= 3 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                    <div class="h-1 rounded-full transition-colors duration-200" :class="score >= 4 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                </div>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showConfirmPass ? 'text' : 'password'" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       minlength="8"
                       maxlength="12"
                       autocomplete="new-password"
                       required
                       placeholder="Ulangi password"
                       class="w-full h-11 sm:h-12 pl-3.5 pr-11 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <button type="button" 
                        @click="showConfirmPass = !showConfirmPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                        title="Tampilkan / Sembunyikan Konfirmasi Password">
                    <i class="fa-solid text-sm" :class="showConfirmPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        {{-- Legal Disclaimer --}}
        <p class="text-[11px] text-slate-500 leading-relaxed pt-1">
            Dengan mendaftar, saya menyetujui 
            <span class="text-blue-600 font-semibold cursor-pointer">Syarat & Ketentuan</span> 
            serta 
            <span class="text-blue-600 font-semibold cursor-pointer">Kebijakan Privasi</span> 
            Bayeng Putra Helm.
        </p>

        {{-- Submit Button --}}
        <div class="pt-1">
            <button type="submit" 
                    class="w-full h-11 sm:h-12 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wide shadow-sm active:scale-[0.99] transition-all flex items-center justify-center">
                Daftar
            </button>
        </div>
    </form>

    {{-- Switch Link to Login (Placed below Daftar) --}}
    <div class="text-center text-xs text-slate-500 pt-2 border-t border-slate-100">
        Sudah punya akun? 
        <a href="{{ route('pelanggan.login') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline ml-1">
            Masuk
        </a>
    </div>

    {{-- Back to Home Link --}}
    <div class="text-center">
        <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-slate-600 inline-flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

</div>
@endsection
