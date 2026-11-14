@extends('layouts.auth')
@section('title', 'Daftar - Bayeng Putra Helm')
@section('container-width', 'max-w-[440px]')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl shadow-blue-950/20 border border-white/60 p-6 sm:p-8 space-y-5">
    
    {{-- Header --}}
    <div class="text-center space-y-2">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-600 text-white shadow-md shadow-blue-600/25 hover:scale-105 transition-transform">
            <i class="fa-solid fa-helmet-safety text-xl"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Buat Akun Baru
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Bayeng Putra Helm &bull; Click & Collect
            </p>
        </div>
    </div>

    {{-- Main Register Form --}}
    <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-3.5" x-data="{ showPass: false, showConfirmPass: false }">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   id="nama" 
                   name="nama" 
                   autocomplete="name"
                   value="{{ old('nama') }}" 
                   required 
                   autofocus
                   placeholder="Nama lengkap Anda"
                   class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 text-sm font-normal focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all">
            @error('nama')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Alamat Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   inputmode="email"
                   autocomplete="email"
                   value="{{ old('email') }}" 
                   required 
                   placeholder="nama@email.com"
                   class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 text-sm font-normal focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all">
            @error('email')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor WhatsApp / Telepon --}}
        <div>
            <label for="telepon" class="block text-xs font-semibold text-slate-700 mb-1">
                No. WhatsApp / HP <span class="text-rose-500">*</span>
            </label>
            <input type="tel" 
                   id="telepon" 
                   name="telepon" 
                   inputmode="tel"
                   autocomplete="tel"
                   value="{{ old('telepon') }}" 
                   required 
                   placeholder="08xxxxxxxxxx"
                   class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 text-sm font-normal focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all">
            @error('telepon')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="new-password"
                       required
                       placeholder="Masukkan password"
                       class="w-full h-11 pl-3.5 pr-10 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 text-sm font-normal focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all">
                <button type="button" 
                        @click="showPass = !showPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-10 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                        title="Tampilkan / Sembunyikan Password">
                    <i class="fa-solid text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
            <p class="text-[11px] text-slate-400 mt-1">
                Min. 8 karakter (kombinasi huruf besar, kecil & angka)
            </p>
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                Konfirmasi Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showConfirmPass ? 'text' : 'password'" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       autocomplete="new-password"
                       required
                       placeholder="Ulangi password Anda"
                       class="w-full h-11 pl-3.5 pr-10 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 text-sm font-normal focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all">
                <button type="button" 
                        @click="showConfirmPass = !showConfirmPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-10 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                        title="Tampilkan / Sembunyikan Konfirmasi Password">
                    <i class="fa-solid text-xs" :class="showConfirmPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit" 
                    class="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm shadow-md shadow-blue-600/20 active:scale-[0.99] transition-all">
                Daftar Sekarang
            </button>
        </div>
    </form>

    {{-- Switch Link & Back Link --}}
    <div class="pt-2 border-t border-slate-100 text-center space-y-2">
        <p class="text-xs text-slate-500">
            Sudah punya akun? 
            <a href="{{ route('pelanggan.login') }}" class="font-semibold text-blue-600 hover:underline ml-0.5">
                Masuk di sini
            </a>
        </p>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

</div>
@endsection
