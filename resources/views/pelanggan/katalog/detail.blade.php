@extends('layouts.pelanggan')
@section('title', $helm->nama_helm . ' - Detail & Spesifikasi')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors">Katalog Helm</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $helm->nama_helm }}</span>
        </nav>

        {{-- Main Product Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200/80 shadow-sm mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                {{-- Product Image Gallery (Left Column) --}}
                <div class="lg:col-span-6 space-y-4">
                    <div class="relative aspect-square rounded-3xl overflow-hidden bg-slate-100/70 border border-slate-200/60 shadow-inner group">
                        <img src="{{ $helm->gambar_url }}" 
                             alt="{{ $helm->nama_helm }}" 
                             class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                        
                        @if($helm->merk)
                            <div class="absolute top-4 left-4 px-3 py-1 rounded-xl bg-white/95 backdrop-blur-md text-xs font-black text-slate-800 shadow-sm border border-slate-200/60">
                                {{ $helm->merk->nama_merk }}
                            </div>
                        @endif

                        <div class="absolute bottom-4 right-4 bg-slate-900/90 text-white text-[11px] font-semibold px-3 py-1.5 rounded-xl backdrop-blur-md shadow-md flex items-center gap-1.5">
                            <i class="fa-solid fa-store text-blue-400"></i>
                            <span>Tersedia untuk Ambil di Toko</span>
                        </div>
                    </div>
                </div>

                {{-- Product Info & Variant Selection (Right Column) --}}
                <div class="lg:col-span-6 space-y-6">
                    
                    {{-- Category & Title --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider">
                                {{ $helm->kategori->nama_kategori ?? 'Helm Motor' }}
                            </span>
                            @php $totalStok = $helm->varians->sum('stok'); @endphp
                            @if($totalStok > 0)
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Stok Fisik Tersedia ({{ $totalStok }})</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold">
                                    Stok Habis
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            {{ $helm->nama_helm }}
                        </h1>
                    </div>

                    {{-- Price Display --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Harga Penjualan</p>
                            <p class="text-2xl sm:text-3xl font-black text-blue-600 tracking-tight">
                                {{ format_rupiah($helm->harga_jual) }}
                            </p>
                        </div>
                        <div class="text-right text-xs text-slate-500">
                            <p class="font-bold text-slate-700">Bebas Ongkir</p>
                            <p class="text-[11px]">Ambil Langsung di Toko</p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="space-y-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Deskripsi & Spesifikasi</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {{ $helm->deskripsi ?? 'Helm original berkualitas tinggi dengan sertifikasi standar keamanan resmi. Nyaman digunakan untuk harian maupun touring jarak jauh.' }}
                        </p>
                    </div>

                    {{-- Add to Cart Livewire Component --}}
                    <div class="pt-2">
                        <livewire:add-to-cart-button :helm="$helm" />
                    </div>

                </div>

            </div>
        </div>

        {{-- Varian Matrix Card --}}
        @if($helm->varians->count())
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Ketersediaan Stok Fisik Gerai</h3>
                    <p class="text-xs text-slate-500">Kombinasi warna dan ukuran helm yang siap diambil langsung di toko hari ini.</p>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-xl">
                    Live Stock Sync
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                            <th class="py-3 px-4">Warna Helm</th>
                            <th class="py-3 px-4">Ukuran</th>
                            <th class="py-3 px-4 text-center">Status Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($helm->varians as $v)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full border border-slate-300 bg-slate-400"></span>
                                <span>{{ $v->warna }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-800">
                                    {{ $v->ukuran }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($v->stok > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia ({{ $v->stok }} unit)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-[11px]">
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

        {{-- Related Helmets --}}
        @if(isset($related) && $related->count())
        <div>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Helm Serupa Lainnya</h3>
                    <p class="text-xs text-slate-500">Pilihan alternatif merk atau kategori yang sama</p>
                </div>
                <a href="{{ route('katalog') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    Lihat Katalog Lengkap &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($related->take(4) as $item)
                <div class="group bg-white rounded-3xl overflow-hidden border border-slate-200/80 hover:border-blue-400 hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div class="relative aspect-square bg-slate-100/70 overflow-hidden">
                        <a href="{{ route('katalog.detail', $item->id_helm) }}">
                            <img src="{{ $item->gambar_url }}" 
                                 alt="{{ $item->nama_helm }}" 
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                        </a>
                    </div>
                    <div class="p-4 flex flex-col justify-between flex-grow space-y-2">
                        <div>
                            <p class="text-[10px] font-bold text-blue-600 uppercase">{{ $item->merk->nama_merk ?? 'Helm' }}</p>
                            <h4 class="font-bold text-xs text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ $item->nama_helm }}
                            </h4>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900">{{ format_rupiah($item->harga_jual) }}</span>
                            <a href="{{ route('katalog.detail', $item->id_helm) }}" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white flex items-center justify-center text-[10px] transition-colors">
                                <i class="fa-solid fa-arrow-right"></i>
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
