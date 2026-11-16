@extends('layouts.pelanggan')
@section('title', $helm->nama_helm . ' - Bayeng Putra Helm Kuningan')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-5 pb-20 sm:pb-16">
    
    <div class="max-w-6xl mx-auto px-3 sm:px-5">
        
        {{-- Main PDP Canvas (Clean Minimalist E-Commerce) --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-0 md:gap-6 items-start">
                
                {{-- Column 1: Image Showcase (Compact Retail Size) --}}
                <div class="md:col-span-5 lg:col-span-4 p-4 sm:p-5 md:p-6 md:sticky md:top-24">
                    <div class="max-w-[260px] sm:max-w-[300px] md:max-w-none mx-auto">
                        <div class="relative aspect-square rounded-xl bg-slate-50 border border-slate-200 overflow-hidden">
                            <img src="{{ $helm->gambar_url }}" 
                                 alt="{{ $helm->nama_helm }}" 
                                 class="w-full h-full object-cover">
                            
                            @if($helm->merk)
                                <div class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded bg-slate-900/90 text-white text-[9px] font-black uppercase tracking-wider">
                                    {{ $helm->merk->nama_merk }}
                                </div>
                            @endif

                            {{-- Stock Status Tag --}}
                            <div class="absolute top-2.5 right-2.5">
                                @if($helm->stok > 0)
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-200">
                                        Stok {{ $helm->stok }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-[9px] font-bold border border-rose-200">
                                        Habis
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 2: Product Data & Purchase Engine --}}
                <div class="md:col-span-7 lg:col-span-8 p-4 sm:p-5 md:p-6 md:pl-0 space-y-4">
                    
                    {{-- Category & Title --}}
                    <div>
                        <div class="mb-1">
                            <a href="{{ route('katalog', ['kategori_filter' => $helm->id_kategori]) }}" 
                               class="text-blue-600 text-xs font-bold uppercase tracking-wider hover:underline">
                                {{ $helm->kategori->nama_kategori ?? 'Helm Motor' }}
                            </a>
                        </div>
                        <h1 class="text-base sm:text-xl md:text-2xl font-bold text-slate-900 tracking-tight leading-snug">
                            {{ $helm->nama_helm }}
                        </h1>
                    </div>

                    {{-- Clean Minimalist Price Box --}}
                    <div class="py-2.5 px-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Harga</span>
                            <span class="text-2xl sm:text-3xl font-black text-blue-600 tracking-tight">
                                {{ format_rupiah($helm->harga_jual) }}
                            </span>
                        </div>
                    </div>

                    {{-- Add to Cart Component (Desktop Buttons & Mobile Bottom Bar & Bottom Sheet) --}}
                    <livewire:add-to-cart-button :helm="$helm" />

                    {{-- Section: Deskripsi Produk & Ukuran --}}
                    <div class="border-t border-slate-100 pt-4 space-y-3.5">
                        
                        {{-- Header --}}
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-600 text-xs"></i>
                                <span>Deskripsi Produk</span>
                            </h2>
                        </div>

                        {{-- Text Description --}}
                        <div class="text-xs text-slate-600 leading-relaxed space-y-2">
                            <p>{{ $helm->deskripsi ?? 'Helm berkendara standar SNI & DOT dengan bahan shell berkualitas tinggi, busa dalam empuk dapat dilepas dan dicuci, serta sistem penguncian aman.' }}</p>
                        </div>

                        {{-- Informasi Ukuran Kepala --}}
                        <div class="pt-2 text-xs text-slate-600 leading-relaxed space-y-0.5">
                            <p class="font-semibold text-slate-800 mb-1">Ukuran kepala:</p>
                            <p>- M: 57 - 58 cm</p>
                            <p>- L: 59 - 60 cm</p>
                            <p>- XL: 61 - 62 cm</p>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- Helm Terkait Lainnya (Compact Card & Selaras dengan Katalog) --}}
        @if(isset($related) && $related->count() > 0)
        <div class="mt-8 sm:mt-10 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Helm Terkait Lainnya
                </h3>
                <a href="{{ route('katalog', ['kategori_filter' => $helm->id_kategori]) }}" 
                   class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </a>
            </div>

            {{-- Grid Selaras dengan Katalog --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-4">
                @foreach($related as $rel)
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden group hover:border-blue-500 hover:shadow-xl hover:shadow-slate-200/40 transition-all flex flex-col">
                    {{-- Thumb --}}
                    <div class="aspect-square bg-slate-50 relative overflow-hidden">
                        <a href="{{ route('katalog.detail', $rel->id_helm) }}">
                            <img src="{{ $rel->gambar_url }}" 
                                 alt="{{ $rel->nama_helm }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </a>
                        
                        {{-- Brand Tag --}}
                        @if($rel->merk)
                        <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-white/90 backdrop-blur-sm rounded text-[8px] font-black text-slate-900 shadow-sm uppercase">
                            {{ $rel->merk->nama_merk }}
                        </div>
                        @endif

                        {{-- Stock Status Tag --}}
                        <div class="absolute top-2 right-2">
                            @if($rel->stok > 0)
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[8px] font-black border border-emerald-200 shadow-2xs">
                                    Stok {{ $rel->stok }}
                                </span>
                            @else
                                <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 text-[8px] font-black border border-rose-200 shadow-2xs">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-3 flex flex-col flex-grow justify-between gap-3">
                        <div>
                            <a href="{{ route('katalog.detail', $rel->id_helm) }}">
                                <h3 class="text-[11px] font-bold text-slate-800 line-clamp-2 h-8 leading-tight mb-1 group-hover:text-blue-600 transition-colors">
                                    {{ $rel->nama_helm }}
                                </h3>
                            </a>
                            <p class="text-[10px] font-black text-blue-600">{{ format_rupiah($rel->harga_jual) }}</p>
                        </div>
                        <a href="{{ route('katalog.detail', $rel->id_helm) }}" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-center text-[9px] font-black text-slate-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all uppercase tracking-widest">
                            Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
