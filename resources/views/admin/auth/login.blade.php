@extends('layouts.auth')
@section('title', 'Login Portal Staf & Admin - Bayeng Putra Helm')
@section('container-width', 'max-w-[420px]')

@section('content')
<div class="bg-white sm:rounded-2xl sm:border sm:border-slate-200/90 sm:shadow-xs p-5 sm:p-8 space-y-6">
    
    {{-- Header & Branding --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-xs">
                    <i class="fa-solid fa-helmet-safety text-base"></i>
                </div>
                <div>
                    <span class="text-xs font-extrabold tracking-tight text-slate-900 block leading-tight">BAYENG <span class="text-blue-600">PRO</span></span>
                    <span class="text-[10px] text-slate-400 font-medium">Sistem Operasional Toko</span>
                </div>
            </div>
            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                Portal Internal
            </span>
        </div>

        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Masuk ke Portal
            </h1>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Gunakan akun staf kasir atau pemilik untuk mengelola inventaris, pesanan, dan validasi pickup.
            </p>
        </div>
    </div>

    {{-- Error & Session Flash Messages --}}
    @if ($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
            <div class="flex items-center gap-2 font-bold text-rose-900">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0"></i>
                <span>Gagal Masuk</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 pl-1 text-[11px] text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Login Form --}}
    <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4" x-data="{ showPass: false, submitting: false }" @submit="submitting = true">
        @csrf

        {{-- Username Input --}}
        <div>
            <label for="username" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Username <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input type="text" 
                       id="username" 
                       name="username" 
                       autocomplete="username"
                       value="{{ old('username') }}" 
                       required 
                       autofocus
                       placeholder="Masukkan username staf"
                       class="w-full h-11 sm:h-12 pl-10 pr-3.5 rounded-xl border {{ $errors->has('username') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-user text-xs"></i>
                </div>
            </div>
            @error('username')
                <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Input with Eye Toggle --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700">
                    Kata Sandi <span class="text-rose-500">*</span>
                </label>
            </div>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="current-password"
                       required
                       placeholder="Masukkan kata sandi"
                       class="w-full h-11 sm:h-12 pl-10 pr-11 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-lock text-xs"></i>
                </div>
                <button type="button" 
                        @click="showPass = !showPass" 
                        tabindex="-1"
                        class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                        title="Tampilkan / Sembunyikan Kata Sandi">
                    <i class="fa-solid text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit" 
                    :disabled="submitting"
                    class="w-full h-11 sm:h-12 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-60 text-white font-semibold text-sm tracking-wide shadow-xs active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                <template x-if="!submitting">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                        <span>Masuk ke Dashboard</span>
                    </span>
                </template>
                <template x-if="submitting">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        <span>Memverifikasi...</span>
                    </span>
                </template>
            </button>
        </div>
    </form>

    {{-- Footer Links & Storefront Navigation --}}
    <div class="pt-2 border-t border-slate-100 flex flex-col items-center gap-2.5 text-center">
        <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 inline-flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Katalog Toko</span>
        </a>

        <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
            <i class="fa-solid fa-shield-halved text-[10px]"></i>
            <span>Akses terbatas untuk staf kasir & manajemen toko</span>
        </div>
    </div>

</div>
@endsection
