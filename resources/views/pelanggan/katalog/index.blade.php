@extends('layouts.pelanggan')
@section('title', 'Katalog Helm Resmi - Bayeng Putra Kuningan')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-5">
        
        {{-- Header Bar (E-commerce Style) --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-5">
            <div>
                <nav class="flex items-center gap-2 text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1.5">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-700">Katalog</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tighter">
                    KATALOG KOLEKSI HELM
                </h1>
            </div>
            <div class="inline-flex items-center gap-2 text-[10px] font-bold text-slate-600 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs">
                <i class="fa-solid fa-store text-blue-600"></i>
                <span class="uppercase tracking-widest">Stok Real-Time di Gerai</span>
            </div>
        </div>

        {{-- Livewire Reactive Katalog Filter --}}
        <livewire:katalog-filter />

    </div>
</div>
@endsection
