@extends('layouts.pelanggan')
@section('title', 'Checkout - Bayeng Putra Helm')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-24 sm:pb-12">
    <div class="max-w-5xl mx-auto px-3 sm:px-5">
        
        {{-- Flash Alerts --}}
        @if(session('error'))
            <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex items-center gap-2.5 mb-3">
            <a href="{{ route('pelanggan.cart') }}" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors shadow-2xs" title="Kembali ke Keranjang">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
            </a>
            <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Checkout</h1>
        </div>

        <form id="checkout-form" method="POST" action="{{ route('pelanggan.checkout.store') }}" x-data="{ metode: '{{ old('metode_bayar', 'bayar_di_toko') }}' }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-5 items-start">
                
                {{-- Left Column: Single Unified Flat Canvas (8 Cols) --}}
                <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 shadow-2xs overflow-hidden">
                    
                    {{-- 1. Lokasi Pengambilan --}}
                    <div class="p-3.5 sm:p-4 space-y-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Lokasi Pengambilan</span>
                        <p class="font-medium text-xs sm:text-sm text-slate-900">Gerai Bayeng Putra Helm Kuningan</p>
                        <p class="text-[11px] text-slate-500">Jl. RE Martadinata No.49, Ancaran, Kuningan &bull; Jam Buka: 08.00 - 21.00 WIB</p>
                    </div>

                    {{-- 2. Data Pengambil --}}
                    <div class="p-3.5 sm:p-4 space-y-2.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Data Pengambil</span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="penerima" class="block text-xs font-medium text-slate-700 mb-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="penerima" 
                                       name="penerima" 
                                       value="{{ old('penerima', $pelanggan->nama) }}" 
                                       required
                                       placeholder="Nama penerima"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                @error('penerima')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="telepon" class="block text-xs font-medium text-slate-700 mb-1">
                                    No. WhatsApp / HP <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="telepon" 
                                       name="telepon" 
                                       value="{{ old('telepon', $pelanggan->telepon ?? $pelanggan->no_hp ?? '') }}" 
                                       required
                                       placeholder="Nomor telepon aktif"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                @error('telepon')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 3. Daftar Produk yang Dipesan --}}
                    <div>
                        <div class="px-3.5 sm:px-4 py-2.5 flex items-center justify-between bg-slate-50/50">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Helm yang Dipesan ({{ $carts->sum('qty') }})</span>
                            <a href="{{ route('pelanggan.cart') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Ubah</a>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @foreach($carts as $item)
                                <div class="p-3.5 sm:p-4 flex items-center gap-3">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                                        <img src="{{ $item->helm->gambar_url }}" alt="{{ $item->helm->nama_helm }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-medium text-slate-900 truncate">{{ $item->helm->nama_helm }}</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            Varian: {{ $item->varian->warna ?? '-' }}, Size {{ $item->varian->ukuran ?? '-' }}
                                        </p>
                                        <p class="text-xs text-slate-600 mt-0.5">
                                            {{ $item->qty }} &times; {{ format_rupiah($item->helm->harga_jual ?? 0) }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs sm:text-sm font-bold text-slate-900">
                                            {{ format_rupiah(($item->helm->harga_jual ?? 0) * $item->qty) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. Metode Pembayaran --}}
                    <div class="p-3.5 sm:p-4 space-y-2.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Metode Pembayaran</span>

                        <div class="space-y-2">
                            {{-- Option 1: Bayar di Toko --}}
                            <label @click="metode = 'bayar_di_toko'" 
                                   class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors"
                                   :class="metode === 'bayar_di_toko' ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="bayar_di_toko" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                    <div>
                                        <p class="font-medium text-xs text-slate-900">Bayar di Toko (Kasir)</p>
                                    </div>
                                </div>
                            </label>

                            {{-- Option 2: QRIS --}}
                            <label @click="metode = 'qris'" 
                                   class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors"
                                   :class="metode === 'qris' ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="qris" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                    <div>
                                        <p class="font-medium text-xs text-slate-900">QRIS</p>
                                    </div>
                                </div>
                            </label>

                            {{-- Option 3: Transfer Bank --}}
                            <label @click="metode = 'transfer'" 
                                   class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors"
                                   :class="metode === 'transfer' ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="transfer" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                    <div>
                                        <p class="font-medium text-xs text-slate-900">Transfer Bank</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('metode_bayar')
                            <p class="text-rose-500 text-[11px]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. Catatan Pesanan --}}
                    <div class="p-3.5 sm:p-4 space-y-1.5">
                        <label for="catatan" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Catatan
                        </label>
                        <input type="text" 
                               id="catatan" 
                               name="catatan" 
                               value="{{ old('catatan') }}"
                               placeholder="Tulis catatan untuk pesanan ini..."
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>

                </div>

                {{-- Right Column: Sticky Summary Desktop (4 Cols) --}}
                <div class="hidden lg:block lg:col-span-4 sticky top-24">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3.5 shadow-2xs">
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Ringkasan Belanja
                        </h2>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Total Belanja ({{ $carts->sum('qty') }} helm)</span>
                                <span class="font-medium text-slate-900">{{ format_rupiah($total) }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-800">Total Tagihan</span>
                            <span class="text-base font-bold text-blue-600">
                                {{ format_rupiah($total) }}
                            </span>
                        </div>

                        {{-- Desktop Submit Button --}}
                        <button type="submit" 
                                form="checkout-form"
                                class="w-full py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm active:scale-98 transition-all flex items-center justify-center cursor-pointer">
                            Buat Pesanan
                        </button>
                    </div>
                </div>

            </div>
        </form>

        {{-- Mobile Sticky Action Bottom Bar (at bottom-0) --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 px-4 py-2.5 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] text-slate-400 block leading-tight">Total Tagihan</span>
                <span class="text-xs sm:text-sm font-bold text-blue-600 leading-tight">
                    {{ format_rupiah($total) }}
                </span>
            </div>

            <button type="submit" 
                    form="checkout-form"
                    class="h-9 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm active:scale-98 transition-all flex items-center justify-center cursor-pointer">
                Buat Pesanan
            </button>
        </div>

    </div>
</div>
@endsection
