@extends('layouts.auth')
@section('title', 'Daftar Akun Pelanggan')
@section('auth-heading', 'Daftar')

@section('content')
<div class="space-y-4">
    
    {{-- Card Header --}}
    <div class="flex items-center justify-between pb-1">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Daftar</h2>
    </div>

    {{-- Main Register Form --}}
    <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-3.5" x-data="{ showPass: false }">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <input type="text" 
                   id="nama" 
                   name="nama" 
                   autocomplete="name"
                   value="{{ old('nama') }}" 
                   required 
                   autofocus
                   placeholder="Nama Lengkap"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-lg border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            @error('nama')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Alamat Email --}}
        <div>
            <input type="email" 
                   id="email" 
                   name="email" 
                   inputmode="email"
                   autocomplete="email"
                   value="{{ old('email') }}" 
                   required 
                   placeholder="Alamat Email"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-lg border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            @error('email')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Nomor WhatsApp / Telepon --}}
        <div>
            <input type="tel" 
                   id="telepon" 
                   name="telepon" 
                   inputmode="tel"
                   autocomplete="tel"
                   value="{{ old('telepon') }}" 
                   required 
                   placeholder="No. Handphone / WhatsApp (contoh: 08123456789)"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-lg border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            @error('telepon')
                <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="new-password"
                       required
                       placeholder="Password (min. 8 karakter, huruf besar & angka)"
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

        {{-- Konfirmasi Password --}}
        <div>
            <input :type="showPass ? 'text' : 'password'" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   autocomplete="new-password"
                   required
                   placeholder="Konfirmasi Password"
                   class="w-full h-11 sm:h-12 px-3.5 rounded-lg border border-slate-300 bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
        </div>

        {{-- Shopee-Style Terms Notice --}}
        <p class="text-[11px] text-slate-500 leading-relaxed text-center px-2">
            Dengan mendaftar, Anda menyetujui 
            <span class="text-blue-600">Syarat & Ketentuan</span> 
            serta 
            <span class="text-blue-600">Kebijakan Privasi</span> 
            Bayeng Putra Helm.
        </p>

        {{-- Shopee-Style Prominent CTA Button --}}
        <div class="pt-1">
            <button type="submit" 
                    class="w-full h-11 sm:h-12 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wider uppercase shadow-md shadow-blue-600/20 flex items-center justify-center transition-all">
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

    {{-- Bottom Switch Link (Shopee exact format) --}}
    <div class="text-center text-xs text-slate-500 pt-1">
        Punya akun? 
        <a href="{{ route('pelanggan.login') }}" class="font-bold text-blue-600 hover:underline ml-1">
            Log in
        </a>
    </div>

</div>
@endsection
