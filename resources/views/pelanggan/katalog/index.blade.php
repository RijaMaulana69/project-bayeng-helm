@extends('layouts.pelanggan')
@section('title', 'Katalog Koleksi Helm Lengkap')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb & Title Section --}}
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-3 font-medium">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span class="text-slate-800 font-semibold">Katalog Helm</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Eksplor Koleksi Helm Resmi
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pilih tipe, cek ketersediaan ukuran dan warna di toko fisik secara real-time.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-600 bg-white px-3.5 py-2 rounded-xl border border-slate-200/80 shadow-sm">
                    <i class="fa-solid fa-store text-blue-600"></i>
                    <span>Tersedia untuk Ambil Langsung di Toko Kuningan</span>
                </div>
            </div>
        </div>

        {{-- Livewire Reactive Katalog Filter --}}
        <livewire:katalog-filter />

    </div>
</div>
@endsection
