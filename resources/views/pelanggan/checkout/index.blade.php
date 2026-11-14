@extends('layouts.pelanggan')
@section('title', 'Checkout Pengambilan di Toko')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <a href="{{ route('pelanggan.cart') }}" class="hover:text-blue-600 transition-colors">Keranjang</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-slate-800 font-semibold">Checkout Ambil di Toko</span>
        </nav>

        {{-- Heading --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-clipboard-check text-blue-600"></i>
                <span>Checkout Reservasi Helm</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Lengkapi data penerima dan pilih metode pembayaran untuk penerbitan e-Tiket pengambilan.
            </p>
        </div>

        <form method="POST" action="{{ route('pelanggan.checkout.store') }}" x-data="{ metode: '{{ old('metode_bayar', 'bayar_di_toko') }}' }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Left Column: Forms --}}
                <div class="lg:col-span-7 space-y-6">
                    
                    {{-- 1. Info Pengambil Barang --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">1</div>
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Identitas Pengambil Helm</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="penerima" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Lengkap Penerima <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="penerima" 
                                       name="penerima" 
                                       value="{{ old('penerima', $pelanggan->nama) }}" 
                                       required
                                       placeholder="Sesuai KTP / Identitas"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                @error('penerima')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="telepon" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    No. WhatsApp / Telepon <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="telepon" 
                                       name="telepon" 
                                       value="{{ old('telepon', $pelanggan->telepon ?? $pelanggan->no_hp ?? '') }}" 
                                       required
                                       placeholder="Contoh: 08123456789"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                @error('telepon')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Store Pickup Location Box --}}
                        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200/60 flex items-start gap-3 text-xs">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <p class="font-extrabold text-blue-900">Lokasi Penjemputan Fisik:</p>
                                <p class="text-blue-800 font-semibold mt-0.5">Gerai Bayeng Putra Helm Kuningan</p>
                                <p class="text-blue-700 text-[11px]">Jl. RE Martadinata No.49, Ancaran, Kab. Kuningan • Jam Buka: 08.00 - 21.00 WIB</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Metode Pembayaran --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">2</div>
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pilih Metode Pembayaran</h2>
                        </div>

                        <div class="space-y-3">
                            {{-- Option 1: Bayar di Toko (COD Store) --}}
                            <label @click="metode = 'bayar_di_toko'" 
                                   class="flex items-center justify-between p-4 rounded-2xl border-2 transition-all cursor-pointer"
                                   :class="metode === 'bayar_di_toko' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="bayar_di_toko" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <p class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                            <span>Bayar di Toko Saat Pengambilan</span>
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Paling Praktis</span>
                                        </p>
                                        <p class="text-[11px] text-slate-500">Bayar tunai atau debit kasir setelah Anda puas mencoba helm di gerai.</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-money-bill-wave text-emerald-600 text-lg"></i>
                            </label>

                            {{-- Option 2: QRIS Instan --}}
                            <label @click="metode = 'qris'" 
                                   class="flex items-center justify-between p-4 rounded-2xl border-2 transition-all cursor-pointer"
                                   :class="metode === 'qris' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="qris" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <p class="font-bold text-xs text-slate-900">QRIS Instan</p>
                                        <p class="text-[11px] text-slate-500">Scan via GoPay, OVO, Dana, ShopeePay, atau Mobile Banking.</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-qrcode text-blue-600 text-lg"></i>
                            </label>

                            {{-- Option 3: Transfer Bank Manual --}}
                            <label @click="metode = 'transfer'" 
                                   class="flex items-center justify-between p-4 rounded-2xl border-2 transition-all cursor-pointer"
                                   :class="metode === 'transfer' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="metode_bayar" value="transfer" x-model="metode" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <p class="font-bold text-xs text-slate-900">Transfer Bank Resmi</p>
                                        <p class="text-[11px] text-slate-500">Transfer ke rekening toko (BCA / Mandiri / BRI) & upload bukti bayar.</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-building-columns text-slate-600 text-lg"></i>
                            </label>
                        </div>
                        @error('metode_bayar')
                            <p class="text-rose-500 text-[11px]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 3. Catatan Tambahan --}}
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
                        <label for="catatan" class="block text-xs font-bold text-slate-700">
                            Catatan untuk Toko <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="catatan" 
                                  name="catatan" 
                                  rows="2" 
                                  placeholder="Contoh: Estimasi ambil jam 4 sore, tolong siapkan kardus bawaan asli helm..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">{{ old('catatan') }}</textarea>
                    </div>

                </div>

                {{-- Right Column: Order Items & Submit --}}
                <div class="lg:col-span-5 sticky top-28 space-y-4">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">Ringkasan Helm</h3>
                            <a href="{{ route('pelanggan.cart') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">Ubah Keranjang</a>
                        </div>

                        {{-- Item List --}}
                        <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                            @foreach($carts as $item)
                                <div class="flex items-center gap-3 text-xs">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200/60 p-0.5">
                                        <img src="{{ $item->helm->gambar_url }}" alt="{{ $item->helm->nama_helm }}" class="w-full h-full object-cover rounded-lg">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-900 truncate">{{ $item->helm->nama_helm }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $item->varian->warna ?? '-' }} &bull; Size {{ $item->varian->ukuran ?? '-' }} &bull; {{ $item->qty }}x</p>
                                    </div>
                                    <p class="font-extrabold text-slate-800 flex-shrink-0">
                                        {{ format_rupiah(($item->helm->harga_jual ?? 0) * $item->qty) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Calculation Breakdown --}}
                        <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                            <div class="flex justify-between text-slate-500">
                                <span>Subtotal Helm</span>
                                <span class="font-bold text-slate-800">{{ format_rupiah($total) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Biaya Layanan & Ambil di Toko</span>
                                <span class="font-bold text-emerald-600">GRATIS</span>
                            </div>
                        </div>

                        {{-- Grand Total --}}
                        <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                            <span class="text-xs font-bold text-slate-800">Grand Total</span>
                            <span class="text-2xl font-black text-blue-600">
                                {{ format_rupiah($total) }}
                            </span>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-xl shadow-blue-600/30 active:scale-98 transition-all">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Konfirmasi & Terbitkan e-Tiket Pickup</span>
                        </button>

                        <p class="text-[11px] text-center text-slate-400">
                            Dengan menekan tombol, Anda menyetujui jadwal pengambilan helm maksimal 3 hari kerja di toko.
                        </p>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection
