@extends('layouts.auth')
@section('title', 'Daftar Akun Pelanggan - Bayeng Putra Helm')
@section('container-width', 'max-w-xl')

@section('content')
<div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-9 space-y-6">
    
    {{-- Brand Header inside Card --}}
    <div class="text-center space-y-2 pb-1">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-500 flex items-center justify-center text-white shadow-md shadow-blue-600/25 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-helmet-safety text-xl"></i>
            </div>
            <span class="font-black text-xl tracking-tight text-slate-900">BAYENG PUTRA HELM</span>
        </a>

        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight pt-2">
            Buat Akun Pelanggan
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto">
            Daftar untuk reservasi helm online & nikmati garansi fitting pas di toko fisik kami
        </p>
    </div>

    {{-- Main Register Form --}}
    <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-4" x-data="{ showPass: false, showConfirmPass: false }">
        @csrf

        {{-- 2-Column Responsive Form Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            {{-- Field 1: Nama Lengkap --}}
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           autocomplete="name"
                           value="{{ old('nama') }}" 
                           required 
                           autofocus
                           placeholder="Nama lengkap Anda"
                           class="w-full h-11 sm:h-12 pl-10 pr-3.5 rounded-xl border border-slate-300 bg-slate-50/40 hover:bg-white focus:bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
                </div>
                @error('nama')
                    <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Field 2: No. WhatsApp / Telepon --}}
            <div>
                <label for="telepon" class="block text-xs font-bold text-slate-700 mb-1.5">
                    No. WhatsApp / HP <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                    </div>
                    <input type="tel" 
                           id="telepon" 
                           name="telepon" 
                           inputmode="tel"
                           autocomplete="tel"
                           value="{{ old('telepon') }}" 
                           required 
                           placeholder="08xxxxxxxxxx"
                           class="w-full h-11 sm:h-12 pl-10 pr-3.5 rounded-xl border border-slate-300 bg-slate-50/40 hover:bg-white focus:bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
                </div>
                @error('telepon')
                    <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Field 3: Alamat Email (Full Width on 2-col) --}}
            <div class="sm:col-span-2">
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-envelope text-sm"></i>
                    </div>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           inputmode="email"
                           autocomplete="email"
                           value="{{ old('email') }}" 
                           required 
                           placeholder="nama@email.com"
                           class="w-full h-11 sm:h-12 pl-10 pr-3.5 rounded-xl border border-slate-300 bg-slate-50/40 hover:bg-white focus:bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
                </div>
                @error('email')
                    <p class="text-rose-500 text-xs mt-1 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Field 4: Password --}}
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password" 
                           name="password" 
                           autocomplete="new-password"
                           required
                           placeholder="Masukkan password"
                           class="w-full h-11 sm:h-12 pl-10 pr-11 rounded-xl border border-slate-300 bg-slate-50/40 hover:bg-white focus:bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
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

            {{-- Field 5: Konfirmasi Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>
                    <input :type="showConfirmPass ? 'text' : 'password'" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           autocomplete="new-password"
                           required
                           placeholder="Ulangi password Anda"
                           class="w-full h-11 sm:h-12 pl-10 pr-11 rounded-xl border border-slate-300 bg-slate-50/40 hover:bg-white focus:bg-white text-base sm:text-sm font-normal text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition-all shadow-sm">
                    <button type="button" 
                            @click="showConfirmPass = !showConfirmPass" 
                            tabindex="-1"
                            class="absolute inset-y-0 right-0 w-11 h-full flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                            title="Tampilkan / Sembunyikan Konfirmasi Password">
                        <i class="fa-solid text-sm" :class="showConfirmPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            {{-- Password Requirement Hint Box --}}
            <div class="sm:col-span-2 flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600">
                <i class="fa-solid fa-circle-info text-blue-600 text-xs flex-shrink-0"></i>
                <span>Password minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, dan angka.</span>
            </div>

        </div>

        {{-- Terms & Conditions Notice --}}
        <p class="text-[11px] text-slate-500 leading-relaxed text-center px-2 pt-1">
            Dengan mendaftar, Anda menyetujui 
            <span class="text-blue-600 font-semibold cursor-default">Syarat & Ketentuan</span> 
            serta 
            <span class="text-blue-600 font-semibold cursor-default">Kebijakan Privasi</span> 
            Bayeng Putra Helm.
        </p>

        {{-- High-Impact Professional CTA Button --}}
        <div class="pt-1">
            <button type="submit" 
                    class="w-full h-12 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-extrabold text-sm tracking-wider uppercase shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2 active:scale-[0.99] transition-all">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>DAFTAR AKUN SEKARANG</span>
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
        Sudah memiliki akun? 
        <a href="{{ route('pelanggan.login') }}" class="font-bold text-blue-600 hover:underline ml-1">
            Log in di sini
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
