@extends('layouts.auth')
@section('title', 'Masuk - Bayeng Putra Helm')
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
                Masuk ke Akun
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Bayeng Putra Helm &bull; Click & Collect
            </p>
        </div>
    </div>

    {{-- Main Login Form --}}
    <form method="POST" action="{{ route('pelanggan.login') }}" class="space-y-4" x-data="{ showPass: false }">
        @csrf

        {{-- Email / Username Input --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                Email / Username <span class="text-rose-500">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   inputmode="email"
                   autocomplete="username"
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   placeholder="nama@email.com"
                   class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 text-sm font-normal focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 transition-all">
            @error('email')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Input with Eye Toggle --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="current-password"
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
        </div>

        {{-- Remember Me Option --}}
        <div class="flex items-center justify-between text-xs pt-0.5">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none py-1">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                <span class="text-slate-600 font-medium text-xs">Ingat saya di perangkat ini</span>
            </label>
        </div>

        {{-- Submit Button --}}
        <div class="pt-1">
            <button type="submit" 
                    class="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm shadow-md shadow-blue-600/20 active:scale-[0.99] transition-all">
                Masuk ke Akun
            </button>
        </div>
    </form>

    {{-- Switch Link & Back Link --}}
    <div class="pt-2 border-t border-slate-100 text-center space-y-2">
        <p class="text-xs text-slate-500">
            Belum punya akun? 
            <a href="{{ route('pelanggan.register') }}" class="font-semibold text-blue-600 hover:underline ml-0.5">
                Daftar sekarang
            </a>
        </p>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

</div>
@endsection
