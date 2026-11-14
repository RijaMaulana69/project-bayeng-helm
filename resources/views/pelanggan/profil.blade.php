@extends('layouts.pelanggan')
@section('title', 'Profil Saya - Bayeng Putra Helm')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-20 sm:pb-12">
    <div class="max-w-lg mx-auto px-3 sm:px-4 space-y-3">
        
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Page Header (Tanpa tombol kembali sesuai preferensi) --}}
        <div class="flex items-center justify-between">
            <h1 class="text-sm sm:text-base font-bold text-slate-900">Profil Saya</h1>
            <a href="{{ route('pelanggan.pesanan') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                <span>Pesanan Saya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        {{-- Single Integrated Flat Sheet Container (Opsi 2: Ultra-Minimalist) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs divide-y divide-slate-100 overflow-hidden">
            
            {{-- Integrated Profile Header --}}
            <div class="p-4 sm:p-5 flex items-center gap-3.5 bg-slate-50/40">
                <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-base uppercase shrink-0 shadow-2xs">
                    {{ substr($pelanggan->nama, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-sm text-slate-900 truncate">{{ $pelanggan->nama }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $pelanggan->email }}</p>
                </div>
            </div>

            {{-- Form Fields --}}
            <form method="POST" action="{{ route('pelanggan.profil.update') }}" 
                  x-data="{ changePass: {{ $errors->has('password') ? 'true' : 'false' }}, showPass: false, showConfirm: false }" 
                  class="p-4 sm:p-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- Field: Nama Lengkap --}}
                <div class="space-y-1">
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
                <div class="space-y-1">
                    <label for="telepon" class="block text-xs font-medium text-slate-700">
                        Nomor WhatsApp
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

                {{-- Field: Email (Read Only & Verified) --}}
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-700">
                        Email
                    </label>
                    <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        <span class="font-medium text-slate-800">{{ $pelanggan->email }}</span>
                        <span class="text-[10px] text-slate-400 flex items-center gap-1">
                            <i class="fa-solid fa-lock text-[9px]"></i>
                            <span>Terdaftar</span>
                        </span>
                    </div>
                </div>

                {{-- Password Toggle Section (Opsi 2: Minimalist Accordion) --}}
                <div class="pt-1">
                    <button type="button" 
                            @click="changePass = !changePass"
                            class="text-xs font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1.5 transition-colors cursor-pointer select-none">
                        <i class="fa-solid text-[10px]" :class="changePass ? 'fa-minus' : 'fa-key'"></i>
                        <span x-text="changePass ? 'Batal ubah kata sandi' : 'Ingin ganti kata sandi?'">Ingin ganti kata sandi?</span>
                    </button>

                    <div x-show="changePass" x-cloak class="mt-3 pt-3 border-t border-slate-100 space-y-3">
                        <div class="space-y-1">
                            <label for="password" class="block text-xs font-medium text-slate-700">
                                Kata Sandi Baru
                            </label>
                            <div class="relative">
                                <input :type="showPass ? 'text' : 'password'"
                                       id="password"
                                       name="password"
                                       placeholder="Masukkan Kata Sandi"
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

                        <div class="space-y-1">
                            <label for="password_confirmation" class="block text-xs font-medium text-slate-700">
                                Ulangi Kata Sandi
                            </label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Ulangi kata sandi baru"
                                       class="w-full px-3 pr-9 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                <button type="button" 
                                        @click="showConfirm = !showConfirm" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                                        title="Tampilkan / Sembunyikan Kata Sandi">
                                    <i class="fa-solid text-[11px]" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-2xs active:scale-98 transition-all cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>

        {{-- Logout Section --}}
        <div class="pt-2 text-center">
            <form method="POST" action="{{ route('pelanggan.logout') }}">
                @csrf
                <button type="submit" class="text-xs font-medium text-rose-500 hover:text-rose-600 transition-colors cursor-pointer">
                    Keluar dari Akun
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
