@extends('layouts.auth')
@section('title', 'Log in Pelanggan')
@section('auth-heading', 'Log in')

@section('content')
<div class="space-y-5">
    
    {{-- Card Header --}}
    <div class="flex items-center justify-between pb-1">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Log in</h2>
    </div>

    {{-- Main Login Form --}}
    <form method="POST" action="{{ route('pelanggan.login') }}" class="space-y-4" x-data="{ showPass: false }">
        @csrf

        {{-- Email / Username Input --}}
        <div>
            <div class="relative">
                <input type="email" 
                       id="email" 
                       name="email" 
                       inputmode="email"
                       autocomplete="username"
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       placeholder="No. Handphone/Username/Email"
                       class="w-full h-11 sm:h-12 px-3.5 rounded-lg border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            </div>
            @error('email')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Password Input with Eye Toggle --}}
        <div>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="current-password"
                       required
                       placeholder="Password"
                       class="w-full h-11 sm:h-12 pl-3.5 pr-11 rounded-lg border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <button type="button" 
                        @click="showPass = !showPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-11 h-full flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                        title="Tampilkan / Sembunyikan Password">
                    <i class="fa-solid text-sm" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Remember & Forgot Options --}}
        <div class="flex items-center justify-between text-xs pt-0.5">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none py-1">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                <span class="text-slate-600 font-medium text-xs">Ingat saya</span>
            </label>
        </div>

        {{-- Shopee-Style Prominent CTA Button --}}
        <div class="pt-1">
            <button type="submit" 
                    class="w-full h-11 sm:h-12 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wider uppercase shadow-md shadow-blue-600/20 flex items-center justify-center transition-all">
                LOG IN
            </button>
        </div>
    </form>

    {{-- Shopee-Style "ATAU" Divider --}}
    <div class="relative flex items-center justify-center py-2">
        <div class="border-t border-slate-200 w-full"></div>
        <span class="bg-white px-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">ATAU</span>
        <div class="border-t border-slate-200 w-full"></div>
    </div>

    {{-- Secondary Action / Explore Store --}}
    <div>
        <a href="{{ route('katalog') }}" 
           class="w-full h-11 sm:h-12 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center justify-center gap-2 transition-colors">
            <i class="fa-solid fa-helmet-safety text-blue-600"></i>
            <span>Lihat Koleksi Helm Tanpa Log in</span>
        </a>
    </div>

    {{-- Bottom Switch Link (Shopee exact format) --}}
    <div class="text-center text-xs text-slate-500 pt-3">
        Baru di Bayeng Helm? 
        <a href="{{ route('pelanggan.register') }}" class="font-bold text-blue-600 hover:underline ml-1">
            Daftar
        </a>
    </div>

</div>
@endsection
