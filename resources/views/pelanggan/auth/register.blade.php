@extends('layouts.auth')
@section('title', 'Daftar - Bayeng Putra Helm')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-8 space-y-4">
    
    {{-- Brand Header inside Card (Clean logo without blue badge or subtitle) --}}
    <div class="text-center space-y-2 pb-1">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
            <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-600/30 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-helmet-safety text-lg"></i>
            </div>
            <span class="font-black text-lg tracking-tight text-slate-900">BAYENG PUTRA HELM</span>
        </a>

        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight pt-2">
            Daftar
        </h1>
        <p class="text-xs text-slate-500">
            Buat akun untuk pesan helm online & fitting di toko
        </p>
    </div>

    {{-- Main Register Form --}}
    <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-3.5" x-data="{ showPass: false, showConfirmPass: false }">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="nama" class="block text-xs font-bold text-slate-700 mb-1">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   id="nama" 
                   name="nama" 
                   autocomplete="name"
                   value="{{ old('nama') }}" 
                   required 
                   autofocus
                   placeholder="Masukkan nama lengkap"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-xl border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
            @error('nama')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Alamat Email --}}
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">
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
                   class="w-full h-11 sm:h-12 px-3.5 rounded-xl border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
            @error('email')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Nomor WhatsApp / Telepon --}}
        <div>
            <label for="telepon" class="block text-xs font-bold text-slate-700 mb-1">
                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
            </label>
            <input type="tel" 
                   id="telepon" 
                   name="telepon" 
                   inputmode="tel"
                   autocomplete="tel"
                   value="{{ old('telepon') }}" 
                   required 
                   placeholder="08xxxxxxxxxx"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-xl border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
            @error('telepon')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1">
                Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="new-password"
                       required
                       placeholder="Min. 8 karakter (huruf besar, kecil, angka)"
                       class="w-full h-11 sm:h-12 pl-3.5 pr-11 rounded-xl border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
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

        {{-- Konfirmasi Password (dengan Hide/Show) --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">
                Konfirmasi Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input :type="showConfirmPass ? 'text' : 'password'" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       autocomplete="new-password"
                       required
                       placeholder="Ulangi password Anda"
                       class="w-full h-11 sm:h-12 pl-3.5 pr-11 rounded-xl border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
                <button type="button" 
                        @click="showConfirmPass = !showConfirmPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-11 h-full flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                        title="Tampilkan / Sembunyikan Konfirmasi Password">
                    <i class="fa-solid text-sm" :class="showConfirmPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        {{-- Terms Notice --}}
        <p class="text-[11px] text-slate-500 leading-relaxed text-center px-1 pt-1">
            Dengan mendaftar, Anda menyetujui 
            <span class="text-blue-600 font-semibold">Syarat & Ketentuan</span> 
            serta 
            <span class="text-blue-600 font-semibold">Kebijakan Privasi</span>.
        </p>

        {{-- Shopee-Style Prominent CTA Button --}}
        <div class="pt-1">
            <button type="submit" 
                    class="w-full h-11 sm:h-12 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wider uppercase shadow-md shadow-blue-600/20 flex items-center justify-center transition-all">
                DAFTAR
            </button>
        </div>
    </form>

    {{-- Shopee-Style "ATAU" Divider --}}
    <div class="relative flex items-center justify-center py-1">
        <div class="border-t border-slate-200 w-full"></div>
        <span class="bg-white px-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">ATAU</span>
        <div class="border-t border-slate-200 w-full"></div>
    </div>

    {{-- Bottom Switch Link --}}
    <div class="text-center text-xs text-slate-500">
        Punya akun? 
        <a href="{{ route('pelanggan.login') }}" class="font-bold text-blue-600 hover:underline ml-1">
            Log in
        </a>
    </div>

    {{-- Back to Home --}}
    <div class="pt-2 border-t border-slate-100 text-center">
        <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-slate-600 inline-flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Beranda Toko</span>
        </a>
    </div>

</div>
@endsection
