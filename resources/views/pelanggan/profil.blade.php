@extends('layouts.pelanggan')
@section('title', 'Profil Saya - Bayeng Putra Helm')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-20 sm:pb-12">
    <div class="max-w-xl mx-auto px-3 sm:px-4 space-y-3">
        
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-sm sm:text-base font-bold text-slate-900">Profil Saya</h1>
        </div>

        {{-- User Info Header Card --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-base uppercase shrink-0">
                    {{ substr($pelanggan->nama, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-sm text-slate-900 truncate">{{ $pelanggan->nama }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $pelanggan->email }}</p>
                </div>
            </div>
            <a href="{{ route('pelanggan.pesanan') }}" class="px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-medium text-xs transition-colors shrink-0">
                Pesanan
            </a>
        </div>

        {{-- Profile Form (Single Unified Container, List Format) --}}
        <form method="POST" action="{{ route('pelanggan.profil.update') }}" x-data="{ showPass: false }">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 shadow-2xs overflow-hidden">
                
                {{-- Field: Nama Lengkap --}}
                <div class="p-3.5 sm:p-4 space-y-1">
                    <label for="nama" class="block text-xs font-medium text-slate-700">
                        Nama Lengkap
                    </label>
                    <input type="text"
                           id="nama"
                           name="nama"
                           value="{{ old('nama', $pelanggan->nama) }}"
                           required
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('nama')
                        <p class="text-rose-500 text-[11px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Field: No. WhatsApp / HP --}}
                <div class="p-3.5 sm:p-4 space-y-1">
                    <label for="telepon" class="block text-xs font-medium text-slate-700">
                        Nomor WhatsApp / HP
                    </label>
                    <input type="text"
                           id="telepon"
                           name="telepon"
                           value="{{ old('telepon', $pelanggan->telepon) }}"
                           required
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('telepon')
                        <p class="text-rose-500 text-[11px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Field: Email (Read Only) --}}
                <div class="p-3.5 sm:p-4 space-y-1">
                    <label class="block text-xs font-medium text-slate-700">
                        Email
                    </label>
                    <input type="email"
                           value="{{ $pelanggan->email }}"
                           readonly
                           disabled
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs text-slate-500 cursor-not-allowed">
                </div>

                {{-- Field: Password Baru (Optional) --}}
                <div class="p-3.5 sm:p-4 space-y-1.5">
                    <label for="password" class="block text-xs font-medium text-slate-700">
                        Kata Sandi Baru <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'"
                               id="password"
                               name="password"
                               placeholder="Minimal 8 karakter"
                               class="w-full px-3 pr-9 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-solid text-[11px]" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-500 text-[11px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Field: Konfirmasi Password Baru --}}
                <div class="p-3.5 sm:p-4 space-y-1.5" x-show="showPass || document.getElementById('password')?.value?.length > 0" x-cloak>
                    <label for="password_confirmation" class="block text-xs font-medium text-slate-700">
                        Ulangi Kata Sandi Baru
                    </label>
                    <input :type="showPass ? 'text' : 'password'"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Ulangi kata sandi baru"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>

                {{-- Submit Button Row --}}
                <div class="p-3.5 sm:p-4 bg-slate-50/40 flex items-center justify-end gap-2">
                    <button type="submit" 
                            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-2xs active:scale-98 transition-all cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>

        {{-- Logout Section --}}
        <div class="pt-2 text-center">
            <form method="POST" action="{{ route('pelanggan.logout') }}">
                @csrf
                <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-700 transition-colors cursor-pointer">
                    Keluar dari Akun
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
