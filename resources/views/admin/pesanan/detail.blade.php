@extends('layouts.admin')

@section('title', 'Detail Pesanan: ' . $pesanan->no_pesanan)
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="space-y-4 max-w-4xl mx-auto">

    @php
        $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum');
        $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
        $customerEmail = $pesanan->pelanggan->email ?? null;

        $metodeLabel = match($pesanan->metode_bayar) {
            'tunai'    => 'Bayar di Toko (Tunai)',
            'transfer' => 'Transfer Bank Manual',
            'qris'     => 'QRIS Dinamis',
            default    => ucfirst($pesanan->metode_bayar ?? 'Online'),
        };

        $isPickupReady = in_array($pesanan->status, ['ready', 'unpaid_pickup']);
        $isUnpaid      = $pesanan->status === 'unpaid_pickup';
    @endphp

    {{-- Navigasi Kembali --}}
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('admin.pesanan.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Kembali ke Transaksi</span>
        </a>
    </div>

    {{-- 1. Kartu Header Utama Pesanan --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">
                    {{ $pesanan->no_pesanan }}
                </h2>
                <span class="text-xs text-slate-400 block mt-0.5">
                    Dibuat pada {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                </span>
            </div>

            <div class="shrink-0">
                @if($isPickupReady)
                    <span class="text-xs sm:text-sm font-semibold text-blue-600">Siap Diambil</span>
                @elseif($pesanan->status === 'pending')
                    <span class="text-xs sm:text-sm font-semibold text-amber-600">Perlu Verifikasi</span>
                @elseif($pesanan->status === 'paid')
                    <span class="text-xs sm:text-sm font-semibold text-slate-700">Sudah Bayar</span>
                @elseif($pesanan->status === 'processing')
                    <span class="text-xs sm:text-sm font-semibold text-indigo-600">Sedang Disiapkan</span>
                @elseif($pesanan->status === 'completed')
                    <span class="text-xs sm:text-sm font-medium text-emerald-600">Selesai</span>
                @elseif($pesanan->status === 'cancelled')
                    <span class="text-xs sm:text-sm font-medium text-rose-500">Dibatalkan</span>
                @else
                    <span class="text-xs sm:text-sm font-medium text-slate-600">{{ ucfirst($pesanan->status) }}</span>
                @endif
            </div>
        </div>

        {{-- Ringkasan Pemesan & Pengambilan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 text-xs">
            <div class="space-y-1">
                <span class="text-[11px] font-medium text-slate-400 block">Pemesan</span>
                <p class="font-semibold text-slate-900 text-sm">{{ $customerName }}</p>
                @if($customerPhone)
                    <a href="tel:{{ $customerPhone }}" class="text-slate-500 hover:text-blue-600 font-mono block mt-0.5">
                        {{ $customerPhone }}
                    </a>
                @endif
                @if($customerEmail)
                    <p class="text-slate-400 text-[11px] block">{{ $customerEmail }}</p>
                @endif
            </div>

            <div class="space-y-1.5 sm:border-l sm:border-slate-100 sm:pl-4">
                <span class="text-[11px] font-medium text-slate-400 block">Pengambilan & Pembayaran</span>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-medium text-slate-800">{{ $metodeLabel }}</span>
                </div>
                @if($pesanan->kode_pickup)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kode Pickup:</span>
                        <span class="font-mono font-semibold text-slate-900">{{ $pesanan->kode_pickup }}</span>
                    </div>
                @endif
                @if($pesanan->batas_ambil)
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Batas Ambil:</span>
                        <span class="text-slate-600">{{ \Carbon\Carbon::parse($pesanan->batas_ambil)->format('d M Y') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. Aksi Cepat Kasir: Serah Terima Langsung di Tempat --}}
    @if($isPickupReady)
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-slate-900 text-xs sm:text-sm">Serah Terima Pesanan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if($isUnpaid)
                            Pelanggan membayar tunai di kasir gerai sebelum barang diserahkan.
                        @else
                            Pesanan telah lunas. Pastikan barang sudah diperiksa bersama pelanggan.
                        @endif
                    </p>
                </div>

                @if($isUnpaid)
                    <div class="sm:text-right">
                        <span class="text-[10px] text-slate-400 block leading-tight">Total Tagihan Tunai</span>
                        <span class="text-sm sm:text-base font-bold text-slate-900">{{ format_rupiah($pesanan->grand_total) }}</span>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}" class="pt-1">
                @csrf
                <button type="submit" 
                        class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs sm:text-sm rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>{{ $isUnpaid ? 'Terima Tunai & Selesaikan Penyerahan' : 'Konfirmasi & Serahkan Helm' }}</span>
                </button>
            </form>
        </div>
    @endif

    {{-- 3. Daftar Produk yang Dipesan --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">
                Produk Pesanan ({{ $pesanan->details->count() }})
            </h3>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach ($pesanan->details as $item)
                @php
                    $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                    $variantParts = array_filter([$item->warna, $item->ukuran]);
                    $variantText = implode(', ', $variantParts);
                    $qty = $item->qty ?? ($item->jumlah ?? 1);
                @endphp
                <div class="p-4 flex items-center justify-between gap-3 text-xs">
                    <div class="min-w-0 flex-1">
                        <p class="text-slate-800 truncate">
                            <span class="font-medium text-slate-900">{{ $qty }}x</span> {{ $helmName }}
                        </p>
                        @if($variantText)
                            <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                {{ $variantText }}
                            </p>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $qty }} x {{ format_rupiah($item->harga) }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="font-semibold text-slate-900 text-xs sm:text-sm">
                            {{ format_rupiah($item->subtotal) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Total Tagihan --}}
        <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="font-medium text-slate-700">Total Belanja</span>
            <span class="text-sm sm:text-base font-bold text-slate-900">
                {{ format_rupiah($pesanan->grand_total) }}
            </span>
        </div>
    </div>

    {{-- 4. Bukti Pembayaran (Bila Ada) --}}
    @if ($pesanan->bukti_bayar)
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">
                    Bukti Transfer Pembeli
                </h3>
                <a href="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                   target="_blank" 
                   class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
                    <span>Buka Ukuran Penuh</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </div>

            <div class="rounded-lg overflow-hidden border border-slate-100 bg-slate-50 max-w-xs mx-auto text-center p-2">
                <img src="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                     alt="Bukti Transfer" 
                     class="max-h-64 w-full object-contain mx-auto cursor-pointer rounded"
                     onclick="window.open(this.src, '_blank')">
            </div>
        </div>
    @endif

    {{-- 5. Panel Update Status Operasional (Jika Ada Transisi yang Diizinkan) --}}
    @if(!empty($allowed_statuses))
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs space-y-3">
            <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">
                Ubah Status Pesanan
            </h3>

            <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Pilih Status
                    </label>
                    <select name="status" required class="w-full h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all cursor-pointer">
                        <option value="">-- Pilih Status --</option>
                        @foreach ($allowed_statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Catatan Opsional
                    </label>
                    <textarea name="catatan" 
                              rows="2" 
                              placeholder="Tuliskan catatan perubahan status..." 
                              class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
                    @error('catatan')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="h-9 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <span>Simpan Perubahan</span>
                </button>
            </form>
        </div>
    @endif

    {{-- 6. Riwayat Perjalanan Pesanan --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-2xs space-y-3">
        <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">
            Riwayat Status
        </h3>

        @if ($pesanan->logs && $pesanan->logs->count() > 0)
            <div class="divide-y divide-slate-100 text-xs">
                @foreach ($pesanan->logs->sortByDesc('created_at') as $log)
                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="font-medium text-slate-800 block">
                                {{ ucfirst($log->status_ke ?? $log->status ?? 'Update') }}
                            </span>
                            @if ($log->catatan)
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $log->catatan }}
                                </p>
                            @endif
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                Oleh: {{ $log->diubah_oleh ?? 'Sistem' }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 shrink-0">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 italic">Belum ada catatan riwayat status.</p>
        @endif
    </div>

</div>
@endsection
