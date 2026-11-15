@extends('layouts.admin')

@section('title', 'Pesanan ' . $pesanan->no_pesanan)
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="max-w-md mx-auto space-y-3 pb-8">

    @php
        $customerName  = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan');
        $customerPhone = $pesanan->pelanggan->telepon ?? ($pesanan->pelanggan->no_hp ?? ($pesanan->telepon ?? null));
        $customerEmail = $pesanan->pelanggan->email ?? null;

        $metodeBayar = match($pesanan->metode_bayar) {
            'tunai'    => 'Bayar di Toko (Tunai)',
            'transfer' => 'Transfer Bank',
            'qris'     => 'QRIS',
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

    {{-- Kartu Tunggal Mobile-First (Single Grid) --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs divide-y divide-slate-100 overflow-hidden text-xs">

        {{-- 1. Identitas Transaksi & Status --}}
        <div class="p-4 flex items-start justify-between gap-3">
            <div>
                <span class="font-bold text-sm text-slate-900 block">
                    {{ $pesanan->no_pesanan }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-0.5">
                    {{ $pesanan->created_at->format('d M Y, H:i') }} WIB
                </span>
            </div>

            <div class="text-right shrink-0">
                @if($isPickupReady)
                    <span class="font-semibold text-blue-600">Siap Diambil</span>
                @elseif($pesanan->status === 'pending')
                    <span class="font-semibold text-amber-600">Perlu Verifikasi</span>
                @elseif($pesanan->status === 'paid')
                    <span class="font-semibold text-slate-700">Sudah Bayar</span>
                @elseif($pesanan->status === 'processing')
                    <span class="font-semibold text-indigo-600">Sedang Disiapkan</span>
                @elseif($pesanan->status === 'completed')
                    <span class="font-medium text-emerald-600">Selesai</span>
                @elseif($pesanan->status === 'cancelled')
                    <span class="font-medium text-rose-500">Dibatalkan</span>
                @else
                    <span class="font-medium text-slate-600">{{ ucfirst($pesanan->status) }}</span>
                @endif
            </div>
        </div>

        {{-- 2. Kode Pickup & Pelanggan (Mirrored dari Sisi Pembeli) --}}
        <div class="p-4 space-y-3">
            @if($pesanan->kode_pickup)
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">
                            Kode Pengambilan
                        </span>
                        <span class="font-mono text-xl sm:text-2xl font-bold text-slate-900 tracking-wider select-all">
                            {{ $pesanan->kode_pickup }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">
                            Pembayaran
                        </span>
                        <span class="text-xs font-semibold text-slate-800">
                            {{ $metodeBayar }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between gap-2">
                <div>
                    <span class="font-semibold text-slate-900 block">{{ $customerName }}</span>
                    @if($customerPhone)
                        <a href="tel:{{ $customerPhone }}" class="text-blue-600 hover:underline font-mono text-[11px] block mt-0.5">
                            {{ $customerPhone }}
                        </a>
                    @endif
                </div>

                @if($pesanan->batas_ambil)
                    <div class="text-right text-[11px] text-slate-400">
                        Batas ambil: <span class="text-slate-600 font-medium">{{ \Carbon\Carbon::parse($pesanan->batas_ambil)->format('d M Y') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. Rincian Barang & Total (Thumbnail Visual + Rincian) --}}
        <div class="p-4 space-y-2.5">
            <div class="divide-y divide-slate-100">
                @foreach ($pesanan->details as $item)
                    @php
                        $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                        $variantParts = array_filter([$item->warna, $item->ukuran]);
                        $variantText = implode(', ', $variantParts);
                        $qty = $item->qty ?? ($item->jumlah ?? 1);
                        $gambarUrl = $item->helm->gambar_url ?? null;
                    @endphp
                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-center gap-3">
                        {{-- Thumbnail Foto Helm --}}
                        <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                            @if($gambarUrl)
                                <img src="{{ $gambarUrl }}" alt="{{ $helmName }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-helmet-safety text-slate-400 text-sm"></i>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-slate-900 truncate font-semibold">
                                <span class="font-bold">{{ $qty }}x</span> {{ $helmName }}
                            </p>
                            @if($variantText)
                                <span class="text-[11px] text-slate-400 block mt-0.5">{{ $variantText }}</span>
                            @endif
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $qty }} &times; {{ format_rupiah($item->harga) }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="font-bold text-slate-900 text-xs">
                                {{ format_rupiah($item->subtotal) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                <span class="font-medium text-slate-500">Total Tagihan</span>
                <span class="text-sm font-bold text-slate-900">
                    {{ format_rupiah($pesanan->grand_total) }}
                </span>
            </div>
        </div>

        {{-- 4. Bukti Transfer (Bila Ada) --}}
        @if ($pesanan->bukti_bayar)
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Bukti Transfer
                    </span>
                    <a href="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                       target="_blank" 
                       class="text-[11px] text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
                        <span>Lihat Penuh</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>
                </div>

                <div class="rounded-lg overflow-hidden border border-slate-100 bg-slate-50 p-2 text-center">
                    <img src="{{ $pesanan->bukti_bayar_url ?? asset('storage/' . $pesanan->bukti_bayar) }}" 
                         alt="Bukti Transfer" 
                         class="max-h-48 w-full object-contain mx-auto rounded cursor-pointer"
                         onclick="window.open(this.src, '_blank')">
                </div>
            </div>
        @endif

        {{-- 5. Riwayat Alur Pesanan (Selaras dengan Stepper Pembeli) --}}
        @if(count($pesanan->tracking_timeline) > 0)
            <div class="p-4 space-y-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">
                    Riwayat Pesanan
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

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold {{ $isLatest ? 'text-slate-900' : 'text-slate-600' }}">
                                    {{ $step['title'] }}
                                </span>
                                <span class="text-[11px] text-slate-400 shrink-0 font-normal">
                                    {{ $step['time'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 6. Aksi Kasir / Operasional (Satu Form Pasti, Tanpa Ambiguitas) --}}
        <div class="p-4 bg-slate-50/50">
            @if($isPickupReady)
                {{-- Siap Diambil: Serah Terima Langsung --}}
                <div class="space-y-2">
                    <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}">
                        @csrf
                        <input type="hidden" name="redirect_to" value="detail">
                        <button type="submit" 
                                class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs sm:text-sm rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-2 shadow-2xs">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>{{ $isCashOnPickup ? 'Serahkan Helm & Terima Tunai (' . format_rupiah($pesanan->grand_total) . ')' : 'Konfirmasi & Serahkan Helm' }}</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')" class="text-center pt-1">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 font-medium transition-colors cursor-pointer">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>

            @elseif($pesanan->status === 'pending')
                {{-- Pending: Verifikasi Pembayaran --}}
                <div class="space-y-2">
                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="paid">
                        <button type="submit" 
                                class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs sm:text-sm rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-2 shadow-2xs">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>Konfirmasi Pembayaran (Sudah Bayar)</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')" class="text-center pt-1">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 font-medium transition-colors cursor-pointer">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>

            @elseif($pesanan->status === 'paid')
                {{-- Paid: Siapkan Barang --}}
                <div class="space-y-2">
                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="processing">
                        <button type="submit" 
                                class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs sm:text-sm rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-2 shadow-2xs">
                            <i class="fa-solid fa-box text-xs"></i>
                            <span>Proses & Siapkan Barang</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')" class="text-center pt-1">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 font-medium transition-colors cursor-pointer">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>

            @elseif($pesanan->status === 'processing')
                {{-- Processing: Tandai Siap Ambil --}}
                <div class="space-y-2">
                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="ready">
                        <button type="submit" 
                                class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs sm:text-sm rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-2 shadow-2xs">
                            <i class="fa-solid fa-bell text-xs"></i>
                            <span>Tandai Siap Diambil di Gerai</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.pesanan.update-status', $pesanan->id_pesanan) }}" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')" class="text-center pt-1">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 font-medium transition-colors cursor-pointer">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>

            @elseif($pesanan->status === 'completed')
                <div class="py-1 text-center text-xs text-emerald-600 font-semibold flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>Pesanan telah diserahkan</span>
                </div>

            @elseif($pesanan->status === 'cancelled')
                <div class="py-1 text-center text-xs text-rose-500 font-semibold flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-circle-xmark text-rose-400"></i>
                    <span>Pesanan telah dibatalkan</span>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
