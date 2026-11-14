@extends('layouts.pelanggan')
@section('title', 'Checkout - Bayeng Putra Helm')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-28 sm:pb-12">
    <div class="max-w-5xl mx-auto px-3 sm:px-5">
        
        {{-- Flash Alerts --}}
        @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex items-center gap-2.5 mb-4">
            <a href="{{ route('pelanggan.cart') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors shadow-2xs" title="Kembali ke Keranjang">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Checkout</h1>
                <p class="text-[11px] text-slate-500">Ambil di Gerai Bayeng Putra Helm</p>
            </div>
        </div>

        <form id="checkout-form" method="POST" action="{{ route('pelanggan.checkout.store') }}" x-data="{ metode: '{{ old('metode_bayar', 'bayar_di_toko') }}' }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6 items-start">
                
                {{-- Left Column: Sections (8 Cols) --}}
                <div class="lg:col-span-8 space-y-3.5">
                    
                    {{-- 1. Lokasi Pengambilan di Toko --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Metode Pengambilan</span>
                            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60">Gratis Ambil di Toko</span>
                        </div>
                        <div class="flex items-start gap-3 pt-1">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-store text-xs"></i>
                            </div>
                            <div class="text-xs space-y-0.5">
                                <p class="font-bold text-slate-900">Gerai Bayeng Putra Helm Kuningan</p>
                                <p class="text-slate-600">Jl. RE Martadinata No.49, Ancaran, Kec. Kuningan, Jawa Barat</p>
                                <p class="text-[11px] text-slate-400">Buka setiap hari: 08.00 - 21.00 WIB</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Data Pengambil --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3">
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
                                       placeholder="Nama pengambil helm"
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
                                       placeholder="Contoh: 081234567890"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                @error('telepon')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 3. Daftar Produk --}}
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                        <div class="px-4 py-3 flex items-center justify-between border-b border-slate-100">
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
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Pilih Pembayaran</span>

                        <div class="space-y-2">
                            {{-- Option 1: Bayar di Toko --}}
                            <label @click="metode = 'bayar_di_toko'" 
                                   class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors"
                                   :class="metode === 'bayar_di_toko' ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="bayar_di_toko" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                    <div>
                                        <p class="font-medium text-xs text-slate-900">Bayar di Toko Saat Pengambilan</p>
                                        <p class="text-[11px] text-slate-500">Bayar tunai, debit, atau QRIS kasir saat mencoba helm di toko.</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-medium text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60 shrink-0 hidden sm:inline-block">Direkomendasikan</span>
                            </label>

                            {{-- Option 2: QRIS --}}
                            <label @click="metode = 'qris'" 
                                   class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors"
                                   :class="metode === 'qris' ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200 hover:border-slate-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="qris" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                    <div>
                                        <p class="font-medium text-xs text-slate-900">QRIS Instan</p>
                                        <p class="text-[11px] text-slate-500">Scan QRIS melalui BCA, GoPay, OVO, Dana, atau ShopeePay.</p>
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
                                        <p class="text-[11px] text-slate-500">Transfer rekening resmi toko & konfirmasi bukti transfer.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('metode_bayar')
                            <p class="text-rose-500 text-[11px]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. Catatan Tambahan (Opsional) --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-2">
                        <label for="catatan" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Catatan Pesanan <span class="normal-case font-normal text-slate-400">(Opsional)</span>
                        </label>
                        <input type="text" 
                               id="catatan" 
                               name="catatan" 
                               value="{{ old('catatan') }}"
                               placeholder="Contoh: Estimasi ambil jam 4 sore, siapkan kardus asli..."
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>

                </div>

                {{-- Right Column: Sticky Summary Desktop (4 Cols) --}}
                <div class="hidden lg:block lg:col-span-4 sticky top-24">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3.5 shadow-2xs">
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Ringkasan Pesanan
                        </h2>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Total Harga ({{ $carts->sum('qty') }} helm)</span>
                                <span class="font-medium text-slate-900">{{ format_rupiah($total) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Biaya Ambil di Toko</span>
                                <span class="font-semibold text-emerald-600">GRATIS</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-800">Total Pembayaran</span>
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

                        <p class="text-[11px] text-slate-400 text-center leading-relaxed">
                            Amankan stok helm Anda sekarang dan coba langsung ukurannya di toko.
                        </p>
                    </div>
                </div>

            </div>
        </form>

        {{-- Mobile Sticky Action Bottom Bar (at bottom-0) --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 px-4 py-2.5 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] text-slate-400 block leading-tight">Total Pembayaran</span>
                <span class="text-sm font-bold text-blue-600 leading-tight">
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
