@extends('layouts.pelanggan')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-slate-800 font-semibold">Keranjang Belanja</span>
        </nav>

        {{-- Heading --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-bag-shopping text-blue-600"></i>
                <span>Keranjang Belanja Anda</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Periksa daftar helm yang ingin Anda pesan untuk pengambilan langsung di toko.
            </p>
        </div>

        {{-- Livewire Cart Manager Component --}}
        <livewire:cart-manager />

    </div>
</div>
@endsection
