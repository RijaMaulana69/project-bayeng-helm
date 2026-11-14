@extends('layouts.pelanggan')
@section('title', 'Daftar Akun Pelanggan Baru')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        {{-- Header Card --}}
        <div class="text-center space-y-2">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/25">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Buat Akun Pelanggan</h1>
            <p class="text-xs text-slate-500">
                Daftar akun untuk menyimpan keranjang belanja dan menerima e-Tiket pickup.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xl shadow-slate-200/40">
            <form method="POST" action="{{ route('pelanggan.register') }}" class="space-y-4" x-data="{ showPass: false }">
                @csrf

                {{-- Nama Lengkap --}}
                <div>
                    <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           value="{{ old('nama') }}" 
                           required 
                           autofocus
                           placeholder="Contoh: Budi Pratama"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('nama')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           placeholder="nama@email.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('email')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No. Telepon --}}
                <div>
                    <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="no_hp" 
                           name="no_hp" 
                           value="{{ old('no_hp', old('telepon')) }}" 
                           required 
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    @error('no_hp')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                    @error('telepon')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Min. 8 karakter (huruf besar, kecil, angka)"
                               class="w-full px-4 pr-10 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fa-solid text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-lock text-[9px]"></i>
                        <span>Syarat Keamanan: Minimal 8 karakter, kombinasi huruf besar, kecil, & angka.</span>
                    </p>
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           placeholder="Ulangi kata sandi"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-600/20 active:scale-98 transition-all">
                        <span>Daftar Akun Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Switch to Login --}}
        <p class="text-center text-xs text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('pelanggan.login') }}" class="font-bold text-blue-600 hover:text-blue-700 underline">
                Masuk di Sini
            </a>
        </p>

    </div>
</div>
@endsection
