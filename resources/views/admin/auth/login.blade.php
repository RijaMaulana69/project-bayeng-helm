@extends('layouts.auth')
@section('title', 'Login Admin - Bayeng Putra Helm')
@section('container-width', 'max-w-[360px]')

@section('content')
<div class="space-y-6">
    
    {{-- Brand Header --}}
    <div class="text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition-colors mb-3">
            <i class="fa-solid fa-helmet-safety text-lg"></i>
        </a>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Login Admin</h1>
        <p class="text-xs text-slate-500 mt-0.5">Bayeng Putra Helm</p>
    </div>

    {{-- Error & Session Alerts --}}
    @if ($errors->any())
        <div class="p-3 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-700 text-xs font-medium space-y-0.5">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (session('error'))
        <div class="p-3 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-700 text-xs font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white p-6 sm:p-7 rounded-xl border border-slate-200 shadow-2xs">
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
                       value="{{ old('username') }}" 
                       required 
                       autofocus
                       class="w-full h-10 px-3 rounded-lg border {{ $errors->has('username') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300' }} text-slate-900 text-sm focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-colors">
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-medium text-slate-700">
                        Password
                    </label>
                    <button type="button" 
                            @click="showPass = !showPass" 
                            tabindex="-1"
                            class="text-[11px] text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                            x-text="showPass ? 'Sembunyikan' : 'Tampilkan'">
                    </button>
                </div>
                <input :type="showPass ? 'text' : 'password'" 
                       id="password" 
                       name="password" 
                       autocomplete="current-password"
                       required
                       class="w-full h-10 px-3 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-colors">
            </div>

            {{-- Submit --}}
            <button type="submit" 
                    class="w-full h-10 rounded-lg bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-medium text-sm transition-colors cursor-pointer">
                Masuk
            </button>
        </form>
    </div>

    {{-- Footer --}}
    <div class="text-center">
        <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-slate-700 inline-flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke toko</span>
        </a>
    </div>

</div>
@endsection
