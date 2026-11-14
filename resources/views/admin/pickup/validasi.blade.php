@extends('layouts.admin')
@section('title', 'Validasi: ' . $pesanan->no_pesanan)
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Order Header --}}
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Detail Penyerahan Barang</h2>
                <p class="text-[11px] text-slate-500 font-medium">No. Pesanan: {{ $pesanan->no_pesanan }}</p>
            </div>
            {!! $pesanan->status_badge !!}
        </div>

        <div class="p-8">
            {{-- Summary Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="p-5 rounded-xl border border-slate-200 bg-white space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Data Pelanggan</span>
                        <i class="fa-solid fa-user text-slate-300"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $pesanan->pelanggan->nama ?? '-' }}</p>
                        <p class="text-xs text-slate-500">{{ $pesanan->pelanggan->no_hp ?? '-' }}</p>
                    </div>
                </div>

                <div class="p-5 rounded-xl border border-blue-200 bg-blue-50/50 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-400 uppercase tracking-widest">Kode Verifikasi</span>
                        <i class="fa-solid fa-key text-blue-300"></i>
                    </div>
                    <div class="text-2xl font-mono font-black text-blue-700 tracking-[0.2em]">
                        {{ $pesanan->kode_pickup }}
                    </div>
                </div>
            </div>

            {{-- Table Items --}}
            <div class="mb-8">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Rincian Barang</h3>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Nama Helm</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Varian</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Qty</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pesanan->details as $item)
                            <tr>
                                <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                    {{ $item->helm->nama_helm ?? $item->nama_helm ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-500">
                                    {{ $item->warna }} / {{ $item->ukuran }}
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-900 text-center">
                                    {{ $item->jumlah }}
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-900 text-right">
                                    {{ format_rupiah($item->subtotal) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest">Total Tagihan</td>
                                <td class="px-6 py-4 text-right text-base font-black text-blue-600">
                                    {{ format_rupiah($pesanan->grand_total) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Payment Info (Security Check) --}}
            <div class="p-4 mb-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-credit-card text-slate-400"></i>
                    <span class="text-xs font-bold text-slate-600">Metode Pembayaran: <span class="text-slate-900 uppercase">{{ $pesanan->metode_bayar }}</span></span>
                </div>
                @if($pesanan->status == 'paid')
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase tracking-wider">Lunas Online</span>
                @else
                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded uppercase tracking-wider">Bayar di Kasir</span>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-4">
                <form method="POST" action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full h-12 flex items-center justify-center gap-3 bg-emerald-600 text-white font-bold text-sm rounded-lg hover:bg-emerald-700 transition-all active:scale-[0.98] shadow-sm shadow-emerald-600/20"
                            onclick="return confirm('Selesaikan penyerahan barang ini?')">
                        <i class="fa-solid fa-check-double text-xs"></i>
                        <span>KONFIRMASI PENYERAHAN</span>
                    </button>
                </form>
                <a href="{{ route('admin.pickup.index') }}" 
                   class="h-12 px-6 flex items-center justify-center bg-slate-100 text-slate-600 font-bold text-xs rounded-lg hover:bg-slate-200 transition-colors">
                    BATAL
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
