@extends('layouts.admin')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-lg mx-auto space-y-3 sm:space-y-4">

    {{-- 1. Top Navigation Bar (Mobile-First) --}}
    <div>
        <a href="{{ route('admin.dashboard') }}" 
           wire:navigate
           class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 font-medium transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- 2. Single Integrated Profile Card (E-Commerce Standard) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden divide-y divide-slate-100">
        
        {{-- Profile Header (Avatar & Username, tanpa badge peran) --}}
        <div class="p-4 sm:p-5 flex items-center gap-3.5 bg-slate-50/50">
            <div class="w-12 h-12 rounded-full bg-slate-900 text-white font-bold text-base flex items-center justify-center shrink-0 shadow-2xs">
                {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-sm font-bold text-slate-900 truncate">
                    {{ $user->nama_lengkap }}
                </h2>
                <p class="text-xs text-slate-400 font-mono mt-0.5 truncate">
                    {{ '@' . $user->username }}
                </p>
            </div>
        </div>

        {{-- Form Body --}}
        <form method="POST" 
              action="{{ route('admin.profil.update') }}" 
              x-data="{ 
                  changePass: {{ $errors->has('password') ? 'true' : 'false' }},
                  showPass: false,
                  showConfirm: false,
                  newPass: '',
                  confirmPass: ''
              }" 
              class="p-4 sm:p-5 space-y-4">
            @csrf
            @method('PUT')

            {{-- Username (Permanen / Read-only) --}}
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">
                    Username
                </label>
                <div class="relative">
                    <input type="text" 
                           value="{{ $user->username }}" 
                           readonly 
                           tabindex="-1"
                           class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-500 cursor-not-allowed select-none focus:outline-none">
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 text-[11px]" title="Permanen">
                        <i class="fa-solid fa-lock text-[10px]"></i>
                    </span>
                </div>
            </div>

            {{-- Nama Lengkap (Editable) --}}
            <div class="space-y-1">
                <label for="nama_lengkap" class="block text-xs font-semibold text-slate-700">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="nama_lengkap"
                       name="nama_lengkap" 
                       value="{{ old('nama_lengkap', $user->nama_lengkap) }}" 
                       required
                       placeholder="Masukkan nama lengkap"
                       class="w-full h-10 px-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all @error('nama_lengkap') border-rose-300 ring-1 ring-rose-400 @enderror">
                @error('nama_lengkap')
                    <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kata Sandi (E-Commerce Accordion Pattern) --}}
            <div class="pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between py-1">
                    <div>
                        <span class="block text-xs font-semibold text-slate-700">Kata Sandi</span>
                        <span class="block text-xs text-slate-400 font-mono mt-0.5 tracking-wider" x-show="!changePass">
                            ••••••••••••
                        </span>
                    </div>
                    <button type="button" 
                            @click="changePass = !changePass; if(!changePass) { newPass = ''; confirmPass = ''; }"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors cursor-pointer select-none">
                        <span x-text="changePass ? 'Batal' : 'Ubah'">Ubah</span>
                    </button>
                </div>

                {{-- Expandable Password Inputs --}}
                <div x-show="changePass" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-3 pt-3 border-t border-slate-100 space-y-3">
                    
                    {{-- Input: Kata Sandi Baru --}}
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-600">
                            Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'"
                                   name="password"
                                   x-model="newPass"
                                   placeholder="Masukkan kata sandi baru"
                                   autocomplete="new-password"
                                   class="w-full h-10 pl-3.5 pr-10 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all @error('password') border-rose-300 ring-1 ring-rose-400 @enderror">
                            <button type="button" 
                                    @click="showPass = !showPass"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                                    title="Tampilkan / Sembunyikan">
                                <i class="fa-regular text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input: Konfirmasi Kata Sandi Baru --}}
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-600">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'"
                                   name="password_confirmation"
                                   x-model="confirmPass"
                                   placeholder="Ulangi kata sandi baru"
                                   autocomplete="new-password"
                                   class="w-full h-10 pl-3.5 pr-10 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all">
                            <button type="button" 
                                    @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                                    title="Tampilkan / Sembunyikan">
                                <i class="fa-regular text-xs" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Helper Ringkas --}}
                    <p class="text-[11px] text-slate-400 leading-normal">
                        Minimal 8 karakter kombinasi huruf besar, huruf kecil, dan angka.
                    </p>
                </div>
            </div>

            {{-- Action Submit Button (Mobile-First) --}}
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-2">
                <a href="{{ route('admin.dashboard') }}" 
                   wire:navigate
                   class="w-full sm:w-auto h-10 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs inline-flex items-center justify-center transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto h-10 px-5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer active:scale-98">
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
