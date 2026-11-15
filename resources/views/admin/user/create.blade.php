@extends('layouts.admin')

@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan')

@section('content')
<div class="max-w-xl mx-auto space-y-3 sm:space-y-4">

    {{-- 1. Top Navigation Bar --}}
    <div>
        <a href="{{ route('admin.user.index') }}" 
           wire:navigate
           class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-900 font-medium transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Daftar Karyawan</span>
        </a>
    </div>

    {{-- 2. Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-user-plus text-sm"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-slate-900">
                    Tambah Akun Karyawan Baru
                </h1>
                <p class="text-xs text-slate-500">
                    Kredensial untuk operasional kasir & restok toko
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.user.store') }}" x-data="{ showPass: false }" class="p-4 sm:p-5 space-y-4 text-xs">
            @csrf

            {{-- Username --}}
            <div class="space-y-1">
                <label for="username" class="block font-semibold text-slate-700">
                    Username Login <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-mono text-xs">@</span>
                    <input type="text" 
                           id="username"
                           name="username" 
                           value="{{ old('username') }}" 
                           required
                           placeholder="contoh: ari_kasir"
                           class="w-full h-10 pl-8 pr-3.5 rounded-xl bg-white border border-slate-200 font-mono text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all @error('username') border-rose-300 ring-1 ring-rose-400 @enderror">
                </div>
                <p class="text-[11px] text-slate-400">Digunakan untuk login ke panel admin kasir.</p>
                @error('username')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Lengkap --}}
            <div class="space-y-1">
                <label for="nama_lengkap" class="block font-semibold text-slate-700">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="nama_lengkap"
                       name="nama_lengkap" 
                       value="{{ old('nama_lengkap') }}" 
                       required
                       placeholder="Masukkan nama lengkap staf"
                       class="w-full h-10 px-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all @error('nama_lengkap') border-rose-300 ring-1 ring-rose-400 @enderror">
                @error('nama_lengkap')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role Karyawan --}}
            <div class="space-y-1">
                <label for="id_role" class="block font-semibold text-slate-700">
                    Peran Akses <span class="text-rose-500">*</span>
                </label>
                <select id="id_role" 
                        name="id_role" 
                        required
                        class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 font-medium text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 cursor-pointer @error('id_role') border-rose-300 ring-1 ring-rose-400 @enderror">
                    <option value="">-- Pilih Peran --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id_role }}" {{ old('id_role') == $role->id_role ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                    @endforeach
                </select>
                @error('id_role')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block font-semibold text-slate-700">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" 
                                @click="showPass = !showPass"
                                class="text-[11px] text-slate-400 hover:text-slate-700 font-medium">
                            <span x-text="showPass ? 'Sembunyikan' : 'Lihat'"></span>
                        </button>
                    </div>
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password"
                           name="password" 
                           required
                           placeholder="Min. 8 karakter"
                           class="w-full h-10 px-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all @error('password') border-rose-300 ring-1 ring-rose-400 @enderror">
                    @error('password')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="password_confirmation" class="block font-semibold text-slate-700">
                        Ulangi Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password_confirmation"
                           name="password_confirmation" 
                           required
                           placeholder="Ketik ulang kata sandi"
                           class="w-full h-10 px-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all">
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.user.index') }}" 
                   wire:navigate
                   class="h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold inline-flex items-center justify-center transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    <span>Simpan Karyawan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
