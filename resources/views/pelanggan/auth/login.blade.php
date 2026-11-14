@extends('layouts.auth')
@section('title', 'Masuk ke Akun Pelanggan')

@section('content')
<div class="space-y-6">
    
    {{-- Modern Segmented Tab Switcher (Masuk vs Daftar) --}}
    <div class="p-1 rounded-2xl bg-slate-100 border border-slate-200/80 flex items-center">
        <a href="{{ route('pelanggan.login') }}" 
           class="flex-1 py-2.5 rounded-xl text-center text-xs font-extrabold bg-white text-blue-600 shadow-sm border border-slate-200/60 transition-all">
            Masuk ke Akun
        </a>
        <a href="{{ route('pelanggan.register') }}" 
           class="flex-1 py-2.5 rounded-xl text-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
            Daftar Baru
        </a>
    </div>

    {{-- Welcome Title --}}
    <div class="space-y-1.5">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Selamat Datang! 👋
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
            Masuk untuk mengecek keranjang helm, status pesanan, dan kode tiket pickup Anda.
        </p>
    </div>

    {{-- Main Login Form --}}
    <form method="POST" action="{{ route('pelanggan.login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        {{-- Email Field (Mobile-Optimized Input) --}}
        <div>
            <label for="email" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-envelope text-sm"></i>
                </div>
                <input type="email" 
                       id="email" 
                       name="email" 
                       inputmode="email"
                       autocomplete="username"
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       placeholder="nama@email.com"
                       class="w-full h-12 pl-10 pr-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-base sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all shadow-sm">
            </div>
            @error('email')
                <p class="text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Password Field (Touch-Friendly Toggle) --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                    Kata Sandi <span class="text-rose-500">*</span>
                </label>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <input :type="showPassword ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="current-password"
                       required
                       placeholder="••••••••"
                       class="w-full h-12 pl-10 pr-12 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-base sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all shadow-sm">
                <button type="button" 
                        @click="showPassword = !showPassword" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-12 h-12 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors"
                        title="Tampilkan / Sembunyikan Kata Sandi">
                    <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Remember Me Option --}}
        <div class="pt-1 flex items-center justify-between text-xs">
            <label class="flex items-center gap-2.5 cursor-pointer text-slate-600 select-none py-1">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded-md text-blue-600 border-slate-300 focus:ring-blue-500">
                <span class="font-medium text-slate-700">Ingat Saya di Perangkat Ini</span>
            </label>
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit" 
                    class="w-full h-12 sm:h-13 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-sm shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2 transition-all">
                <span>Masuk Sekarang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </form>

    {{-- Security & Trust Strip --}}
    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center gap-3 text-xs text-slate-500">
        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center flex-shrink-0 text-sm">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <p class="text-[11px] leading-relaxed">
            Dilengkapi proteksi keamanan akun otomatis <span class="font-bold text-slate-700">Rate Limiter Anti-Brute Force</span> & enkripsi sandi standar industri.
        </p>
    </div>

    {{-- Bottom Switch Link --}}
    <p class="text-center text-xs text-slate-500 pt-1">
        Belum memiliki akun pembeli? 
        <a href="{{ route('pelanggan.register') }}" class="font-bold text-blue-600 hover:text-blue-700 underline decoration-blue-300 underline-offset-4">
            Daftar Akun Baru
        </a>
    </p>

</div>
@endsection
