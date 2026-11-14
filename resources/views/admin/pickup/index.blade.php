@extends('layouts.admin')
@section('title', 'Kasir Pickup')
@section('page-title', 'Kasir Pickup')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
    
    {{-- Search/Input Section --}}
    <div class="lg:col-span-4">
        <div class="p-6 bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-50 text-blue-600 rounded-lg">
                    <i class="fa-solid fa-qrcode text-lg"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Validasi Kode</h2>
                    <p class="text-[11px] text-slate-500 font-medium">Input kode tiket pelanggan</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.pickup.validasi') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kode Pickup</label>
                    <input type="text"
                           name="kode_pickup"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-lg font-mono font-bold uppercase tracking-widest text-slate-900 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all outline-none"
                           placeholder="CONTOH: 8XJ2K"
                           value="{{ old('kode_pickup') }}"
                           maxlength="10"
                           autofocus
                           required>
                    @error('kode_pickup')
                        <p class="mt-1 text-[11px] font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white font-bold text-sm rounded-lg hover:bg-blue-700 transition-all active:scale-[0.98] shadow-sm shadow-blue-600/20">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>CARI PESANAN</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="p-4 rounded-lg bg-amber-50 border border-amber-100">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
                        <p class="text-[11px] leading-relaxed text-amber-700 font-medium">
                            Pastikan pelanggan menunjukkan E-Tiket dari HP mereka. Periksa kesesuaian barang sebelum menekan tombol "Serahkan".
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders List Section --}}
    <div class="lg:col-span-8">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Antrean Siap Ambil</h3>
                <span class="px-2 py-1 bg-white border border-slate-200 rounded-md text-[10px] font-bold text-slate-500">{{ $pesanan_ready->count() }} Data</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">ID Pesanan</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Pelanggan</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Kode</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Total Tagihan</th>
                            <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pesanan_ready as $pesanan)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 tracking-tight">{{ $pesanan->no_pesanan }}</td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-slate-700">{{ $pesanan->pelanggan->nama ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">{{ $pesanan->created_at->format('d/m/y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded font-mono text-xs font-bold tracking-widest border border-blue-100">{{ $pesanan->kode_pickup }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 text-right">{{ format_rupiah($pesanan->grand_total) }}</td>
                            <td class="px-6 py-4">
                                {!! $pesanan->status_badge !!}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center opacity-30 grayscale">
                                    <i class="fa-solid fa-box-open text-4xl mb-3"></i>
                                    <p class="text-xs font-bold uppercase tracking-widest">Belum ada pesanan masuk</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
