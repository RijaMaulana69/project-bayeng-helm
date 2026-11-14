@extends('layouts.pelanggan')
@section('title', 'Katalog Helm Resmi - Bayeng Putra Kuningan')

@section('content')
<div class="bg-slate-50 min-h-screen py-6 md:py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-5">
        
        {{-- Breadcrumb & Title Section (Clean E-commerce Bar) --}}
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-4">
            <div>
                <nav class="flex items-center gap-1.5 text-[11px] text-slate-500 font-medium mb-1">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-bold">Katalog Helm</span>
                </nav>
                <h1 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">
                    Katalog Koleksi Helm
                </h1>
            </div>
            <div class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-2xs self-start sm:self-auto">
                <i class="fa-solid fa-store text-blue-600 text-xs"></i>
                <span>Stok Fisik Gerai Kuningan</span>
            </div>
        </div>

        {{-- Livewire Reactive Katalog Filter --}}
        <livewire:katalog-filter />

    </div>
</div>
@endsection
