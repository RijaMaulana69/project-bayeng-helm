@extends('layouts.auth')
@section('title', 'Daftar Akun Pelanggan Baru')

@section('content')
<div class="space-y-6">
    
    {{-- Modern Segmented Tab Switcher (Masuk vs Daftar) --}}
    <div class="p-1 rounded-2xl bg-slate-100 border border-slate-200/80 flex items-center">
        <a href="{{ route('pelanggan.login') }}" 
           class="flex-1 py-2.5 rounded-xl text-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
            Masuk ke Akun
        </a>
        <a href="{{ route('pelanggan.register') }}" 
           class="flex-1 py-2.5 rounded-xl text-center text-xs font-extrabold bg-white text-blue-600 shadow-sm border border-slate-200/60 transition-all">
            Daftar Baru
        </a>
    </div>

    {{-- Welcome Title --}}
    <div class="space-y-1.5">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Buat Akun Baru 🚀
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
            Daftar untuk reservasi helm online dan dapatkan kemudahan fitting ukuran di toko fisik.
        </p>
    </div>

    {{-- Main Register Form --}}
    <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-4" x-data="{ showPass: false }">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="nama" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-user text-sm"></i>
                </div>
                <input type="text" 
                       id="nama" 
                       name="nama" 
                       autocomplete="name"
                       value="{{ old('nama') }}" 
                       required 
                       autofocus
                       placeholder="Nama lengkap Anda"
                       class="w-full h-12 pl-10 pr-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-base sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all shadow-sm">
            </div>
            @error('nama')
                <p class="text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Alamat Email --}}
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
                       autocomplete="email"
                       value="{{ old('email') }}" 
                       required 
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

        {{-- Nomor WhatsApp / Telepon --}}
        <div>
            <label for="telepon" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                No. WhatsApp / Telepon <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                </div>
                <input type="tel" 
                       id="telepon" 
                       name="telepon" 
                       inputmode="tel"
                       autocomplete="tel"
                       value="{{ old('telepon') }}" 
                       required 
                       placeholder="08xxxxxxxxxx"
                       class="w-full h-12 pl-10 pr-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-base sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all shadow-sm">
            </div>
            @error('telepon')
                <p class="text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
            <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk konfirmasi ketersediaan helm & notifikasi e-Tiket.</p>
        </div>

        {{-- Kata Sandi --}}
        <div>
            <label for="password" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
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
                       placeholder="Min. 8 karakter (huruf besar, kecil, angka)"
                       class="w-full h-12 pl-10 pr-12 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-base sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all shadow-sm">
                <button type="button" 
                        @click="showPass = !showPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-12 h-12 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors"
                        title="Tampilkan / Sembunyikan Kata Sandi">
                    <i class="fa-solid text-sm" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span>{{ $message }}</span>
                </p>
            @enderror
            <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-[10px] text-blue-500"></i>
                <span>Minimal 8 karakter, wajib kombinasi huruf besar, kecil, dan angka.</span>
            </p>
        </div>

        {{-- Konfirmasi Kata Sandi --}}
        <div>
            <label for="password_confirmation" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-shield-check text-sm"></i>
                </div>
                <input :type="showPass ? 'text' : 'password'" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       autocomplete="new-password"
                       required
                       placeholder="Ulangi kata sandi Anda"
                       class="w-full h-12 pl-10 pr-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-base sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all shadow-sm">
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit" 
                    class="w-full h-12 sm:h-13 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-sm shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2 transition-all">
                <span>Daftar Akun Sekarang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </form>

    {{-- Bottom Switch Link --}}
    <p class="text-center text-xs text-slate-500 pt-1">
        Sudah memiliki akun? 
        <a href="{{ route('pelanggan.login') }}" class="font-bold text-blue-600 hover:text-blue-700 underline decoration-blue-300 underline-offset-4">
            Masuk di sini
        </a>
    </p>

</div>
@endsection
