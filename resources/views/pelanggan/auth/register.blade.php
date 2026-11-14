@extends('layouts.auth')
@section('title', 'Buat Akun Pelanggan Baru')

@section('content')
<div class="w-full max-w-md space-y-6">
    
    {{-- Card Header --}}
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Buat Akun Pelanggan</h1>
        <p class="text-xs text-slate-500">
            Daftar untuk reservasi helm online dan dapatkan e-Tiket fitting di toko.
        </p>
    </div>

    {{-- Main Auth Card --}}
    <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-xl shadow-slate-200/40">
        <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-4" x-data="{ showPass: false }">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           value="{{ old('nama') }}" 
                           required 
                           autofocus
                           placeholder="Contoh: Budi Pratama"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                @error('nama')
                    <p class="text-rose-500 text-[11px] mt-1.5 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Alamat Email --}}
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

            {{-- No. WhatsApp / Telepon --}}
            <div>
                <label for="telepon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    No. WhatsApp / Telepon <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-phone text-xs"></i>
                    </div>
                    <input type="text" 
                           id="telepon" 
                           name="telepon" 
                           value="{{ old('telepon') }}" 
                           required 
                           placeholder="08xxxxxxxxxx"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                @error('telepon')
                    <p class="text-rose-500 text-[11px] mt-1.5 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            {{-- Kata Sandi --}}
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="Min. 8 karakter (huruf besar, kecil, angka)"
                           class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <button type="button" 
                            @click="showPass = !showPass" 
                            tabindex="-1"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fa-solid text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-rose-500 text-[11px] mt-1.5 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle-info text-[9px] text-blue-500"></i>
                    <span>Kombinasi minimal 8 karakter, huruf besar, huruf kecil, dan angka.</span>
                </p>
            </div>

            {{-- Konfirmasi Kata Sandi --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-shield-check text-xs"></i>
                    </div>
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           placeholder="Ulangi kata sandi"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all">
                    <span>Daftar Akun Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-[11px]"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- Switch to Login --}}
    <p class="text-center text-xs text-slate-500">
        Sudah memiliki akun? 
        <a href="{{ route('pelanggan.login') }}" class="font-bold text-blue-600 hover:text-blue-700 underline decoration-blue-300 underline-offset-4">
            Masuk di sini
        </a>
    </p>

</div>
@endsection
