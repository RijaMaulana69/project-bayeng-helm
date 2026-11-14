@extends('layouts.admin')
@section('title', 'Validasi Pickup - ' . $pesanan->no_pesanan)
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-xl font-black text-slate-900 tracking-tighter">Detail Pesanan</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $pesanan->no_pesanan }}</p>
            </div>
            {!! $pesanan->status_badge !!}
        </div>

        <div class="p-8 space-y-8">
            {{-- Order Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-slate-400">Pelanggan</span>
                        <span class="text-slate-900">{{ $pesanan->pelanggan->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-slate-400">Metode Bayar</span>
                        <span class="text-slate-900 uppercase">{{ $pesanan->metode_bayar ?? '-' }}</span>
                    </div>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 text-center flex flex-col justify-center items-center">
                    <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Kode Pickup</span>
                    <span class="text-3xl font-black text-blue-600 tracking-[0.2em]">{{ $pesanan->kode_pickup }}</span>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Item Pesanan</h3>
                <div class="rounded-2xl border border-slate-100 overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 text-slate-400">
                            <tr>
                                <th class="px-6 py-4 font-bold uppercase">Produk</th>
                                <th class="px-6 py-4 font-bold uppercase">Varian</th>
                                <th class="px-6 py-4 font-bold uppercase text-center">Qty</th>
                                <th class="px-6 py-4 font-bold uppercase text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($pesanan->items as $item)
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $item->helm->nama_helm ?? $item->nama_helm ?? '-' }}</td>
                                <td class="px-6 py-4 font-medium text-slate-500">{{ $item->warna }} / {{ $item->ukuran }}</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-900">{{ $item->jumlah }}</td>
                                <td class="px-6 py-4 text-right font-black text-slate-900">{{ format_rupiah($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-black text-slate-900">Grand Total</td>
                                <td class="px-6 py-4 text-right font-black text-blue-600 text-base">{{ format_rupiah($pesanan->grand_total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-4 pt-4 border-t border-slate-50">
                <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}" class="flex-grow">
                    @csrf
                    <button type="submit" 
                            class="w-full h-14 rounded-2xl bg-emerald-600 text-white font-black text-sm hover:bg-emerald-700 transition-all active:scale-95 flex items-center justify-center gap-3"
                            onclick="return confirm('Konfirmasi serahkan barang ke pelanggan?')">
                        <i class="fa-solid fa-handshake"></i>
                        <span>SERAHKAN BARANG</span>
                    </button>
                </form>
                <a href="{{ route('admin.pickup.index') }}" 
                   class="h-14 px-8 rounded-2xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-all flex items-center justify-center">
                    KEMBALI
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
