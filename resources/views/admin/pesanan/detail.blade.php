@extends('layouts.admin')

@section('title', 'Detail Pesanan: ' . $pesanan->no_pesanan)
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="max-w-xl mx-auto space-y-3">

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

        $isPickupReady  = in_array($pesanan->status, ['ready', 'unpaid_pickup']);
        $isCashOnPickup = ($pesanan->metode_bayar === 'tunai');
    @endphp

    {{-- Navigasi Kembali --}}
    <a href="{{ route('admin.pesanan.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali ke Transaksi</span>
    </a>

    {{-- Single Unified Card (Satu Grid Mobile-First) --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs divide-y divide-slate-100 overflow-hidden">
        
        {{-- 1. Header: No. Transaksi, Waktu, dan Status --}}
        <div class="p-4 sm:p-5 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-bold text-slate-900 truncate">
                    {{ $pesanan->no_pesanan }}
                </h2>
                <span class="text-xs text-slate-400 block mt-0.5">
                    Dibuat {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                </span>
            </div>

            <div class="shrink-0 text-right">
                @if($isPickupReady)
                    <span class="text-xs font-semibold text-blue-600">Siap Diambil</span>
                @elseif($pesanan->status === 'pending')
                    <span class="text-xs font-semibold text-amber-600">Perlu Verifikasi</span>
                @elseif($pesanan->status === 'paid')
                    <span class="text-xs font-semibold text-slate-700">Sudah Bayar</span>
                @elseif($pesanan->status === 'processing')
                    <span class="text-xs font-semibold text-indigo-600">Sedang Disiapkan</span>
                @elseif($pesanan->status === 'completed')
                    <span class="text-xs font-medium text-emerald-600">Selesai</span>
                @elseif($pesanan->status === 'cancelled')
                    <span class="text-xs font-medium text-rose-500">Dibatalkan</span>
                @else
                    <span class="text-xs font-medium text-slate-600">{{ ucfirst($pesanan->status) }}</span>
                @endif
            </div>
        </div>

        {{-- 2. Aksi Kasir: Serah Terima Langsung di Tempat --}}
        @if($isPickupReady)
            <div class="p-4 sm:p-5 bg-slate-50/60 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Serah Terima Kasir</span>
                        <span class="text-[11px] text-slate-500">
                            @if($isCashOnPickup)
                                Terima uang tunai dari pembeli sebelum menyerahkan helm.
                            @else
                                Pesanan sudah lunas via {{ ucfirst($pesanan->metode_bayar) }}.
                            @endif
                        </span>
                    </div>

                    @if($isCashOnPickup)
                        <div class="text-right shrink-0">
                            <span class="text-[10px] text-slate-400 block">Tagihan Kasir</span>
                            <span class="text-sm sm:text-base font-bold text-slate-900">{{ format_rupiah($pesanan->grand_total) }}</span>
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}">
                    @csrf
                    <button type="submit" 
                            class="w-full h-10 sm:h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>{{ $isCashOnPickup ? 'Terima Tunai & Selesaikan Penyerahan' : 'Konfirmasi & Serahkan Helm' }}</span>
                    </button>
                </form>
            </div>
        @endif

        {{-- 3. Kode Pickup & Data Pembeli --}}
        <div class="p-4 sm:p-5 space-y-3">
            @if($pesanan->kode_pickup)
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider block">
                            Kode Pickup
                        </span>
                        <span class="font-mono text-lg font-bold text-slate-900 tracking-wider">
                            {{ $pesanan->kode_pickup }}
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium">
                        {{ $metodeLabel }}
                    </span>
                </div>
            @endif

            <div class="space-y-1 text-xs">
                <span class="text-[11px] font-medium text-slate-400 block">Data Pemesan</span>
                <p class="font-semibold text-slate-900">{{ $customerName }}</p>
                @if($customerPhone)
                    <a href="tel:{{ $customerPhone }}" class="text-slate-500 hover:text-blue-600 font-mono block">
                        {{ $customerPhone }}
                    </a>
                @endif
                @if($customerEmail)
                    <p class="text-slate-400 text-[11px]">{{ $customerEmail }}</p>
                @endif
            </div>
        </div>

        {{-- 4. Lacak Pesanan (Timeline Stepper Selaras dengan Pembeli) --}}
        @if(count($pesanan->tracking_timeline) > 0)
            <div class="p-4 sm:p-5 space-y-3">
                <span class="text-[11px] font-medium text-slate-400 block uppercase tracking-wider">
                    Lacak Pesanan
                </span>

                <div class="relative pl-6 space-y-3.5 before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @foreach($pesanan->tracking_timeline as $index => $step)
                        @php
                            $isLatest = ($index === 0);
                        @endphp
                        <div class="relative">
                            @if($isLatest)
                                <div class="absolute -left-6 top-0.5 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] ring-4 ring-blue-50 shadow-2xs">
                                    <i class="fa-solid fa-check text-[9px]"></i>
                                </div>
                            @else
                                <div class="absolute -left-6 top-1 w-6 h-6 rounded-full bg-white border-2 border-slate-300 text-slate-400 flex items-center justify-center text-[8px]">
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                </div>
                            @endif

                            <div class="space-y-0.5">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-semibold {{ $isLatest ? 'text-slate-900' : 'text-slate-700' }}">
                                        {{ $step['title'] }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400 shrink-0 font-normal">
                                        {{ $step['time'] }} WIB
                                    </span>
                                </div>
                                <p class="text-[11px] {{ $isLatest ? 'text-slate-600' : 'text-slate-500' }} leading-relaxed">
                                    {{ $step['desc'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 5. Produk Dipesan --}}
        <div class="p-4 sm:p-5 space-y-3">
            <span class="text-[11px] font-medium text-slate-400 block uppercase tracking-wider">
                Produk Dipesan ({{ $pesanan->details->sum('qty') ?: $pesanan->details->count() }})
            </span>

            <div class="divide-y divide-slate-100">
                @foreach ($pesanan->details as $item)
                    @php
                        $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                        $variantParts = array_filter([$item->warna, $item->ukuran]);
                        $variantText = implode(', ', $variantParts);
                        $qty = $item->qty ?? ($item->jumlah ?? 1);
                    @endphp
                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <p class="text-slate-800 truncate">
                                <span class="font-medium text-slate-900">{{ $qty }}x</span> {{ $helmName }}
                            </p>
                            @if($variantText)
                                <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                    {{ $variantText }}
                                </p>
                            @endif
                        </div>

                        <div class="text-right shrink-0">
                            <span class="font-semibold text-slate-900 text-xs">
                                {{ format_rupiah($item->subtotal) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="font-medium text-slate-700">Total Tagihan</span>
                <span class="text-sm font-bold text-slate-900">
                    {{ format_rupiah($pesanan->grand_total) }}
                </span>
            </div>
        </div>

        {{-- 6. Bukti Pembayaran (Bila Ada) --}}
        @if ($pesanan->bukti_bayar)
            <div class="p-4 sm:p-5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-slate-400 block uppercase tracking-wider">
                        Bukti Transfer
                    </span>
                    <a href="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                       target="_blank" 
                       class="text-[11px] text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
                        <span>Buka Gambar</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>
                </div>

                <div class="rounded-lg overflow-hidden border border-slate-100 bg-slate-50 max-w-xs p-2">
                    <img src="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                         alt="Bukti Transfer" 
                         class="max-h-48 w-full object-contain mx-auto cursor-pointer rounded"
                         onclick="window.open(this.src, '_blank')">
                </div>
            </div>
        @endif

        {{-- 7. Panel Update Status Operasional --}}
        @if(!empty($allowed_statuses))
            <div class="p-4 sm:p-5 space-y-3 bg-slate-50/40">
                <span class="text-[11px] font-medium text-slate-400 block uppercase tracking-wider">
                    Ubah Status Operasional
                </span>

                <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <select name="status" required class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all cursor-pointer">
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
                        <textarea name="catatan" 
                                  rows="2" 
                                  placeholder="Catatan opsional..." 
                                  class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
                        @error('catatan')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="h-9 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        @endif

    </div>

</div>
@endsection
