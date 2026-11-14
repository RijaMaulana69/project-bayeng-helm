@extends('layouts.pelanggan')
@section('title', $helm->nama_helm . ' - Detail & Ketersediaan Gerai')

@section('content')
<div class="bg-slate-50 min-h-screen py-6 pb-32 md:pb-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-5">
        
        {{-- Breadcrumb Nav --}}
        <nav class="flex items-center gap-1.5 text-[11px] text-slate-500 font-medium mb-4 overflow-hidden">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors shrink-0">Beranda</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors shrink-0">Katalog</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-bold truncate">{{ $helm->nama_helm }}</span>
        </nav>

        {{-- Main PDP Card (Marketplace Pro Layout) --}}
        <div class="bg-white rounded-2xl p-4 sm:p-6 md:p-8 border border-slate-200 shadow-sm mb-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 items-start">
                
                {{-- Left: Product Image Gallery --}}
                <div class="md:col-span-5 space-y-3">
                    <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-50 border border-slate-200 group">
                        <img src="{{ $helm->gambar_url }}" 
                             alt="{{ $helm->nama_helm }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        
                        @if($helm->merk)
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-slate-900/80 text-white text-[9px] font-bold uppercase tracking-wider">
                                {{ $helm->merk->nama_merk }}
                            </div>
                        @endif

                        <div class="absolute bottom-3 left-3 right-3 bg-white/95 backdrop-blur-xs text-slate-800 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-slate-200 shadow-2xs flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-blue-600">
                                <i class="fa-solid fa-store"></i>
                                <span>Gerai Kuningan</span>
                            </span>
                            <span class="text-emerald-600 font-black">Bisa Fitting di Toko</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Product Details & Buying Actions --}}
                <div class="md:col-span-7 space-y-5">
                    
                    {{-- Title & Meta --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider">
                                {{ $helm->kategori->nama_kategori ?? 'Helm Motor' }}
                            </span>
                            <span class="text-[11px] text-slate-400">&bull;</span>
                            <span class="text-[11px] font-semibold text-slate-600">SNI & DOT Certified</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
                            {{ $helm->nama_helm }}
                        </h1>
                    </div>

                    {{-- Price Strip --}}
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block leading-none mb-1">Harga Toko</span>
                            <span class="text-2xl font-black text-blue-600 tracking-tight">
                                {{ format_rupiah($helm->harga_jual) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200">
                                <i class="fa-solid fa-handshake"></i>
                                <span>Bayar Pas Ambil</span>
                            </span>
                        </div>
                    </div>

                    {{-- Livewire Add To Cart & Variant Selection --}}
                    <livewire:add-to-cart-button :helm="$helm" />

                    {{-- Store Consultation Strip (Tokopedia Style Chat Info) --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 leading-none">Konsultasi Ukuran & Fitting</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Ragu dengan ukuran lingkar kepala? Chat staf toko.</p>
                                </div>
                            </div>
                            @php
                                $waTextCons = urlencode("Halo Staf Toko Bayeng Helm, saya mau konsultasi ukuran untuk helm *{$helm->nama_helm}*. Lingkar kepala saya sekitar ... cm.");
                            @endphp
                            <a href="https://wa.me/6281234567890?text={{ $waTextCons }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg transition-colors shrink-0">
                                Tanya Staf
                            </a>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Deskripsi Produk</h3>
                        <div class="text-xs text-slate-600 leading-relaxed space-y-2">
                            <p>{{ $helm->deskripsi ?? 'Helm berkendara standar SNI & DOT dengan bahan shell berkualitas tinggi, busa dalam empuk dapat dilepas dan dicuci, serta tali pengunci yang aman.' }}</p>
                        </div>
                    </div>

                    {{-- Store Trust Badges --}}
                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-[11px] text-slate-600 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-blue-600"></i>
                            <span>100% Produk Original</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-rotate text-blue-600"></i>
                            <span>Bisa Tukar Ukuran di Toko</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- Ketersediaan Stok Fisik Gerai (Table Clean) --}}
        @if($helm->varians->count())
        <div class="bg-white rounded-2xl p-5 md:p-6 border border-slate-200 shadow-sm mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Ketersediaan Stok Fisik Gerai</h3>
                    <p class="text-[11px] text-slate-500">Data ketersediaan langsung di gerai kami hari ini.</p>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100">
                    Gerai Kuningan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                            <th class="py-2.5 px-3 rounded-l-lg">Varian Warna</th>
                            <th class="py-2.5 px-3">Ukuran</th>
                            <th class="py-2.5 px-3 text-right rounded-r-lg">Status Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($helm->varians as $v)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $v->warna }}</td>
                            <td class="py-2.5 px-3 font-bold text-slate-600">{{ $v->ukuran }}</td>
                            <td class="py-2.5 px-3 text-right">
                                @if($v->stok > 0)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        Tersedia ({{ $v->stok }})
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                        Habis
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Produk Terkait (Grid Serasi) --}}
        @if(isset($related) && $related->count() > 0)
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Produk Terkait</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
                @foreach($related as $rel)
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden group hover:border-blue-500 hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="aspect-square bg-slate-50 relative overflow-hidden">
                        <a href="{{ route('katalog.detail', $rel->id_helm) }}">
                            <img src="{{ $rel->gambar_url }}" alt="{{ $rel->nama_helm }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </a>
                        @if($rel->merk)
                            <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-slate-900/80 text-white text-[8px] font-bold uppercase rounded">
                                {{ $rel->merk->nama_merk }}
                            </div>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-grow justify-between gap-2">
                        <a href="{{ route('katalog.detail', $rel->id_helm) }}">
                            <h4 class="text-[11px] font-bold text-slate-800 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors">
                                {{ $rel->nama_helm }}
                            </h4>
                        </a>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-xs font-black text-blue-600">{{ format_rupiah($rel->harga_jual) }}</span>
                            <a href="{{ route('katalog.detail', $rel->id_helm) }}" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-[9px] font-bold text-slate-700 hover:bg-blue-600 hover:text-white transition-colors">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
