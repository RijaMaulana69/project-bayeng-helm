@extends('layouts.auth')
@section('title', 'Login Admin')
@section('container-width', 'max-w-[380px]')

@section('content')
<div class="space-y-5">
    
    {{-- Brand Header --}}
    <div class="text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition-colors mb-3">
            <i class="fa-solid fa-helmet-safety text-xl"></i>
        </a>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Login Admin</h1>
        <p class="text-xs text-slate-500 mt-1">Bayeng Putra Helm</p>
    </div>

    {{-- Error & Session Alerts --}}
    @if ($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium space-y-1">
            <div class="flex items-center gap-2 font-semibold text-rose-800">
                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                <span>Gagal Masuk</span>
            </div>
            @foreach ($errors->all() as $error)
                <p class="text-[11px] leading-relaxed">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (session('error'))
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-xs shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-xs shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4" x-data="{ showPass: false }">
            @csrf

            {{-- Username --}}
            <div>
                <label for="username" class="block text-xs font-medium text-slate-700 mb-1.5">
                    Username
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       autocomplete="username"
                       autocapitalize="none"
                       spellcheck="false"
                       value="{{ old('username') }}" 
                       required 
                       autofocus
                       placeholder="Masukkan username"
                       class="w-full h-11 px-3.5 rounded-xl border {{ $errors->has('username') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all">
            </div>

            {{-- Password with discrete inline Eye Toggle --}}
            <div>
                <label for="password" class="block text-xs font-medium text-slate-700 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" 
                           id="password" 
                           name="password" 
                           autocomplete="current-password"
                           required
                           placeholder="Masukkan kata sandi"
                           class="w-full h-11 pl-3.5 pr-11 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all">
                    <button type="button" 
                            @click="showPass = !showPass" 
                            tabindex="-1"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                            title="Tampilkan / Sembunyikan password">
                        <i class="fa-solid text-xs" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit" 
                    class="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm shadow-sm transition-all cursor-pointer">
                Masuk
            </button>
        </form>

        {{-- Help footnote --}}
        <div class="pt-2 border-t border-slate-100 text-center">
            <span class="text-[11px] text-slate-400">
                Lupa kata sandi? Hubungi Owner
            </span>
        </div>
    </div>

    {{-- Back to Store Link --}}
    <div class="text-center">
        <a href="{{ route('home') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Katalog Toko</span>
        </a>
    </div>

</div>
@endsection
