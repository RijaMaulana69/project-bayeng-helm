@extends('layouts.auth')
@section('title', 'Masuk ke Akun')

@section('content')
<div class="w-full max-w-md space-y-6">
    
    {{-- Card Header --}}
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Masuk ke Akun</h1>
        <p class="text-xs text-slate-500">
            Akses keranjang belanja, status pesanan, dan e-Tiket pengambilan Anda.
        </p>
    </div>

    {{-- Main Auth Card --}}
    <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-xl shadow-slate-200/40">
        <form method="POST" action="{{ route('pelanggan.login') }}" class="space-y-4" x-data="{ showPassword: false }">
            @csrf

            {{-- Email Input --}}
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </div>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           placeholder="nama@email.com"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                @error('email')
                    <p class="text-rose-500 text-[11px] mt-1.5 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Password Input --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input :type="showPassword ? 'text' : 'password'" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="••••••••"
                           class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            tabindex="-1"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid text-xs" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-rose-500 text-[11px] mt-1.5 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                    <span>Ingat Saya</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <button type="submit" 
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all">
                <span>Masuk Sekarang</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </button>
        </form>

        {{-- Rate Limiter Security Notice --}}
        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-center gap-1.5 text-[11px] text-slate-400">
            <i class="fa-solid fa-shield-halved text-emerald-500 text-xs"></i>
            <span>Dilindungi Sistem Keamanan Rate-Limiter</span>
        </div>
    </div>

    {{-- Switch to Register --}}
    <p class="text-center text-xs text-slate-500">
        Belum memiliki akun? 
        <a href="{{ route('pelanggan.register') }}" class="font-bold text-blue-600 hover:text-blue-700 underline decoration-blue-300 underline-offset-4">
            Daftar di sini
        </a>
    </p>

</div>
@endsection
