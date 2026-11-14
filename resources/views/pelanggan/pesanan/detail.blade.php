@extends('layouts.pelanggan')
@section('title', 'Detail Pesanan #' . $pesanan->no_pesanan)

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <a href="{{ route('pelanggan.pesanan') }}" class="hover:text-blue-600 transition-colors">Pesanan Saya</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-slate-800 font-semibold font-mono">{{ $pesanan->no_pesanan }}</span>
        </nav>

        {{-- Heading with Status --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tiket Pesanan</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-xs text-slate-500">{{ $pesanan->created_at->format('d M Y, H:i') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-mono mt-0.5">
                    {{ $pesanan->no_pesanan }}
                </h1>
            </div>
            <div>
                {!! $pesanan->status_badge !!}
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Column: Details & Items --}}
            <div class="lg:col-span-8 space-y-6">
                
                {{-- e-Tiket Stub Box --}}
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 p-5 rounded-2xl border-2 border-dashed border-blue-200 mb-6">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">
                                Kode Pickup Verifikasi Toko
                            </span>
                            <span class="font-mono text-3xl font-black text-blue-600 tracking-widest select-all">
                                {{ $pesanan->kode_pickup }}
                            </span>
                            <p class="text-[11px] text-slate-500 mt-1">
                                Perlihatkan kode ini kepada kasir saat mengambil helm di gerai.
                            </p>
                        </div>
                        <button onclick="navigator.clipboard.writeText('{{ $pesanan->kode_pickup }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Kode disalin!', showConfirmButton:false, timer:1500})" 
                                type="button" 
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 shadow-sm self-start sm:self-auto transition-colors">
                            <i class="fa-solid fa-copy text-blue-600"></i>
                            <span>Salin Kode</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-1">
                        <div>
                            <span class="text-slate-400 font-medium block">Penerima Barang</span>
                            <span class="font-bold text-slate-800">{{ $pesanan->penerima }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Kontak WhatsApp</span>
                            <span class="font-bold text-slate-800">{{ $pesanan->telepon ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Batas Pengambilan</span>
                            <span class="font-bold text-rose-600">
                                {{ $pesanan->batas_ambil ? $pesanan->batas_ambil->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Helm Items Table --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Rincian Helm Dipesan</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                                    <th class="py-3 px-2">Helm</th>
                                    <th class="py-3 px-2">Varian</th>
                                    <th class="py-3 px-2 text-right">Harga</th>
                                    <th class="py-3 px-2 text-center">Jumlah</th>
                                    <th class="py-3 px-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($pesanan->items as $item)
                                <tr>
                                    <td class="py-3 px-2 font-bold text-slate-900">
                                        {{ $item->nama_helm }}
                                    </td>
                                    <td class="py-3 px-2 text-slate-600">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 font-medium text-[11px]">
                                            {{ $item->warna }} &bull; {{ $item->ukuran }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-right font-medium text-slate-700">
                                        {{ format_rupiah($item->harga) }}
                                    </td>
                                    <td class="py-3 px-2 text-center font-bold text-slate-900">
                                        {{ $item->jumlah }}
                                    </td>
                                    <td class="py-3 px-2 text-right font-black text-slate-900">
                                        {{ format_rupiah($item->subtotal) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Summary --}}
                    <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                        <span class="text-xs font-bold text-slate-700">Grand Total Pembayaran</span>
                        <span class="text-xl font-black text-blue-600">
                            {{ format_rupiah($pesanan->grand_total) }}
                        </span>
                    </div>
                </div>

                {{-- Status Timeline Logs --}}
                @if($pesanan->logs && $pesanan->logs->count() > 0)
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Riwayat Status Pesanan</h3>
                    <div class="space-y-4 text-xs">
                        @foreach($pesanan->logs as $log)
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-800">{{ $log->catatan }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $log->created_at->format('d M Y, H:i:s') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            {{-- Right Column: Payment & Store Instructions --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Store Location Card --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-3 text-xs">
                    <h3 class="font-bold uppercase tracking-wider text-slate-900 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-store text-blue-600"></i>
                        <span>Gerai Pengambilan</span>
                    </h3>
                    <p class="font-extrabold text-slate-800">Bayeng Putra Helm Kuningan</p>
                    <p class="text-slate-500 leading-relaxed text-[11px]">
                        Jl. RE Martadinata No.49, Ancaran, Kuningan, Jawa Barat.
                    </p>
                    <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-[11px] text-emerald-700 font-semibold">
                        <i class="fa-solid fa-clock"></i>
                        <span>Buka Setiap Hari 08.00 - 21.00 WIB</span>
                    </div>
                </div>

                {{-- Upload Bukti Bayar Form (if pending and transfer/qris) --}}
                @if($pesanan->status === 'pending' && in_array($pesanan->metode_bayar, ['transfer', 'qris']))
                <div class="bg-white rounded-3xl p-6 border border-amber-200 bg-amber-50/20 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-amber-800">
                        <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                        <h3 class="font-bold text-xs uppercase tracking-wider">Unggah Bukti Bayar</h3>
                    </div>

                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Silakan transfer sesuai nominal <strong class="text-slate-900">{{ format_rupiah($pesanan->grand_total) }}</strong>, lalu upload tangkapan layar/foto struk:
                    </p>

                    <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs space-y-1">
                        <p class="text-slate-500 text-[10px] uppercase font-bold">Rekening Toko</p>
                        <p class="font-mono font-extrabold text-slate-900 text-sm">BCA: 123-456-7890</p>
                        <p class="text-[11px] text-slate-600">a.n. Bayeng Putra Helm</p>
                    </div>

                    <form method="POST" action="{{ route('pelanggan.pesanan.upload-bukti', $pesanan->id_pesanan) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <input type="file" 
                                   name="bukti_bayar" 
                                   required 
                                   accept="image/*"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                            @error('bukti_bayar')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[10px] text-slate-400 mt-1">Format gambar: JPG, PNG (Maks 2MB).</p>
                        </div>

                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>Kirim Bukti Pembayaran</span>
                        </button>
                    </form>
                </div>
                @endif

                {{-- Display Uploaded Bukti if exists --}}
                @if($pesanan->bukti_bayar)
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2 text-xs">
                    <h3 class="font-bold uppercase tracking-wider text-slate-800 text-xs">Bukti Bayar Terlampir</h3>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/60">
                        <img src="{{ asset('storage/' . $pesanan->bukti_bayar) }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                    </div>
                    <p class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1 pt-1">
                        <i class="fa-solid fa-check"></i> Bukti bayar berhasil terunggah dan tersimpan aman.
                    </p>
                </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
