@extends('layouts.auth')
@section('title', 'Masuk ke Akun - Bayeng Putra Helm')
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
            Masuk ke Akun
        </h1>
    </div>

    {{-- Main Login Form --}}
    <form method="POST" action="{{ route('pelanggan.login') }}" class="space-y-4" x-data="{ showPass: false }">
        @csrf

        {{-- Email / Username Input --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   inputmode="email"
                   maxlength="30"
                   autocomplete="username"
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   placeholder="nama@email.com"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            @error('email')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Input with Eye Toggle --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       maxlength="12"
                       autocomplete="current-password"
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
        </div>

        {{-- Remember Me Option --}}
        <div class="flex items-center justify-between text-xs pt-0.5">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none py-1">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                <span class="text-slate-600 font-medium text-xs">Ingat saya di perangkat ini</span>
            </label>
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit" 
                    class="w-full h-11 sm:h-12 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wide shadow-sm active:scale-[0.99] transition-all flex items-center justify-center">
                Masuk
            </button>
        </div>
    </form>

    {{-- Switch Link to Register (Placed below Masuk) --}}
    <div class="text-center text-xs text-slate-500 pt-2 border-t border-slate-100">
        Belum punya akun? 
        <a href="{{ route('pelanggan.register') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline ml-1">
            Daftar
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
