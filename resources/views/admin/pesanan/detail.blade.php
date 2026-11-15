@extends('layouts.admin')

@section('title', 'Detail Pesanan: #' . $pesanan->no_pesanan)
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="space-y-4 max-w-5xl mx-auto">

    @php
        $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum');
        $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
        $customerEmail = $pesanan->pelanggan->email ?? null;

        $statusBadge = match($pesanan->status) {
            'pending'       => ['label' => 'Perlu Verifikasi', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/80', 'icon' => 'fa-regular fa-clock'],
            'unpaid_pickup' => ['label' => 'Bayar di Toko',   'class' => 'bg-sky-50 text-sky-700 border-sky-200/80',       'icon' => 'fa-solid fa-store'],
            'paid'          => ['label' => 'Sudah Bayar',     'class' => 'bg-blue-50 text-blue-700 border-blue-200/80',     'icon' => 'fa-regular fa-circle-check'],
            'processing'    => ['label' => 'Sedang Disiapkan', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80', 'icon' => 'fa-solid fa-box'],
            'ready'         => ['label' => 'Siap Ambil',      'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80', 'icon' => 'fa-solid fa-bell'],
            'completed'     => ['label' => 'Selesai',         'class' => 'bg-slate-100 text-slate-700 border-slate-200',   'icon' => 'fa-solid fa-check-double'],
            'cancelled'     => ['label' => 'Dibatalkan',       'class' => 'bg-rose-50 text-rose-700 border-rose-200/80',     'icon' => 'fa-solid fa-ban'],
            default         => ['label' => $pesanan->status_label, 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'icon' => 'fa-solid fa-circle-info'],
        };

        $metodeLabel = match($pesanan->metode_bayar) {
            'tunai'    => 'Bayar di Toko (Kasir)',
            'transfer' => 'Transfer Bank Manual',
            'qris'     => 'QRIS Dinamis',
            default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
        };
    @endphp

    {{-- Top Action Bar / Back Navigation --}}
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('admin.pesanan.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Kembali ke Daftar Pesanan</span>
        </a>

        @if(in_array($pesanan->status, ['ready', 'unpaid_pickup', 'paid', 'processing']) && $pesanan->kode_pickup)
            <a href="{{ route('admin.pickup.validasi', ['kode' => $pesanan->kode_pickup]) }}" 
               class="h-8 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold inline-flex items-center gap-1.5 border border-emerald-200/80 transition-colors">
                <i class="fa-solid fa-qrcode text-xs"></i>
                <span>Buka di Kasir Pickup</span>
            </a>
        @endif
    </div>

    {{-- Main 2-Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 items-start">

        {{-- Left: Informasi Utama, Item, Bukti Bayar & Riwayat (2 Kolom) --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- 1. Kartu Ringkasan Pesanan & Pelanggan --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900">
                                #{{ $pesanan->no_pesanan }}
                            </h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Dibuat pada {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border shrink-0 {{ $statusBadge['class'] }}">
                        <i class="{{ $statusBadge['icon'] }} text-[11px]"></i>
                        <span>{{ $statusBadge['label'] }}</span>
                    </span>
                </div>

                <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    {{-- Data Pembeli --}}
                    <div class="space-y-2">
                        <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">
                            Informasi Pemesan
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 text-sm">{{ $customerName }}</p>
                            @if($customerPhone)
                                <p class="text-slate-500 mt-0.5">
                                    <i class="fa-solid fa-phone text-[10px] text-slate-400 w-3.5"></i>
                                    {{ $customerPhone }}
                                </p>
                            @endif
                            @if($customerEmail)
                                <p class="text-slate-500 mt-0.5">
                                    <i class="fa-regular fa-envelope text-[10px] text-slate-400 w-3.5"></i>
                                    {{ $customerEmail }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Data Pickup / Pengambilan --}}
                    <div class="space-y-2">
                        <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">
                            Metode & Pengambilan
                        </span>
                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Metode Bayar:</span>
                                <span class="font-semibold text-slate-800">{{ $metodeLabel }}</span>
                            </div>
                            @if($pesanan->kode_pickup)
                                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                    <span class="text-slate-500">Kode Pickup:</span>
                                    <span class="font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200 text-xs">
                                        {{ $pesanan->kode_pickup }}
                                    </span>
                                </div>
                            @endif
                            @if($pesanan->batas_ambil)
                                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-0.5">
                                    <span>Batas Ambil:</span>
                                    <span class="font-medium text-slate-700">
                                        {{ \Carbon\Carbon::parse($pesanan->batas_ambil)->format('d M Y') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Daftar Produk / Item Pesanan --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Rincian Item ({{ $pesanan->details->count() }})
                    </h4>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach ($pesanan->details as $item)
                        @php
                            $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                            $variantParts = array_filter([$item->warna, $item->ukuran]);
                            $variantText = implode(', ', $variantParts);
                        @endphp
                        <div class="p-4 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-slate-800 text-xs sm:text-sm">
                                    {{ $helmName }}
                                </p>
                                @if($variantText)
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Varian: {{ $variantText }}
                                    </p>
                                @endif
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $item->qty ?? 1 }} x {{ format_rupiah($item->harga) }}
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="font-bold text-slate-900 text-xs sm:text-sm">
                                    {{ format_rupiah($item->subtotal) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Total Tagihan --}}
                <div class="p-4 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-bold text-slate-700 block">Grand Total</span>
                        <span class="text-[11px] text-slate-400">Total belanja pesanan</span>
                    </div>
                    <span class="text-base sm:text-lg font-bold text-slate-900">
                        {{ format_rupiah($pesanan->grand_total) }}
                    </span>
                </div>
            </div>

            {{-- 3. Bukti Pembayaran (Jika ada) --}}
            @if ($pesanan->bukti_bayar)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Bukti Transfer Pelanggan
                        </h4>
                        <a href="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                           target="_blank" 
                           class="text-xs text-blue-600 hover:underline font-semibold inline-flex items-center gap-1">
                            <span>Buka Gambar Penuh</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>

                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50 text-center max-w-sm mx-auto">
                        <img src="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                             alt="Bukti Transfer" 
                             class="max-h-72 w-full object-contain mx-auto cursor-pointer"
                             onclick="window.open(this.src, '_blank')">
                    </div>
                </div>
            @endif

            {{-- 4. Riwayat Status Log --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-3">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Riwayat Perjalanan Pesanan
                </h4>

                @if ($pesanan->logs && $pesanan->logs->count() > 0)
                    <div class="space-y-3 pt-1">
                        @foreach ($pesanan->logs->sortByDesc('created_at') as $log)
                            <div class="flex items-start gap-3 text-xs">
                                <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 text-[10px] mt-0.5 border border-blue-200">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-semibold text-slate-800">
                                            {{ ucfirst($log->status_ke ?? $log->status ?? 'Update') }}
                                        </p>
                                        <span class="text-[10px] text-slate-400 shrink-0">
                                            {{ $log->created_at->format('d M, H:i') }}
                                        </span>
                                    </div>
                                    @if ($log->catatan)
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            {{ $log->catatan }}
                                        </p>
                                    @endif
                                    <span class="text-[10px] text-slate-400 block mt-0.5">
                                        Oleh: {{ $log->diubah_oleh ?? 'Sistem' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Belum ada catatan riwayat status.</p>
                @endif
            </div>

        </div>

        {{-- Right: Status Control Panel (1 Kolom) --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 space-y-4">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Update Status Pesanan
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Ubah tahapan pesanan sesuai alur operasional gerai.
                    </p>
                </div>

                @if(!empty($allowed_statuses))
                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Status Baru
                            </label>
                            <select name="status" required class="w-full h-10 px-3 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                                <option value="">-- Pilih Status Berikutnya --</option>
                                @foreach ($allowed_statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Catatan Opsional
                            </label>
                            <textarea name="catatan" 
                                      rows="3" 
                                      placeholder="Tambahkan catatan perubahan status..." 
                                      class="w-full p-3 bg-slate-50 border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"></textarea>
                            @error('catatan')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" 
                                class="w-full h-10 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold rounded-xl transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Simpan Status</span>
                        </button>
                    </form>
                @else
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100 text-center">
                        <i class="fa-solid fa-lock text-slate-400 text-sm mb-1"></i>
                        <p class="text-xs font-semibold text-slate-700">Status Final</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Pesanan ini telah berada di status akhir dan tidak dapat diubah lagi.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Quick BOPIS Helper --}}
            <div class="bg-blue-50/60 rounded-2xl border border-blue-200/80 p-4 space-y-2 text-xs">
                <div class="flex items-center gap-2 text-blue-900 font-bold">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <span>Alur Pesanan Toko</span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Untuk pesanan dengan metode <b>Bayar di Toko</b> atau <b>Siap Ambil</b>, penyerahan helm dapat divalidasi langsung melalui menu <b>Kasir Pickup</b> menggunakan kode pickup.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
