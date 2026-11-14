@extends('layouts.admin')
@section('title', 'Validasi Pickup')
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Input Section --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8 text-center sticky top-28">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mx-auto mb-6">
                <i class="fa-solid fa-qrcode text-2xl"></i>
            </div>
            <h2 class="text-lg font-black text-slate-900 mb-2">Validasi Pickup</h2>
            <p class="text-xs font-medium text-slate-500 mb-8">Masukkan kode pickup yang ditunjukkan pelanggan di gerai.</p>

            <form method="POST" action="{{ route('admin.pickup.validasi') }}" class="space-y-4">
                @csrf
                <input type="text"
                       name="kode_pickup"
                       class="w-full h-14 rounded-2xl border border-slate-200 bg-slate-50 text-center text-2xl font-black uppercase tracking-widest text-slate-900 focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                       placeholder="XXXXXX"
                       value="{{ old('kode_pickup') }}"
                       maxlength="10"
                       autofocus
                       required>
                
                @error('kode_pickup')
                    <p class="text-[10px] font-bold text-rose-600 text-left">{{ $message }}</p>
                @enderror

                <button type="submit" class="w-full h-14 rounded-2xl bg-slate-900 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all active:scale-95">
                    Cari Pesanan
                </button>
            </form>
        </div>
    </div>

    {{-- Orders Table Section --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Pesanan Siap Diambil</h3>
                <span class="px-3 py-1 rounded-full bg-blue-50 text-[10px] font-black text-blue-600 uppercase tracking-wider">{{ $pesanan_ready->count() }} Menunggu</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">No. Pesanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4">Batas Ambil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($pesanan_ready as $pesanan)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $pesanan->no_pesanan }}</td>
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $pesanan->pelanggan->nama ?? '-' }}</td>
                            <td class="px-6 py-4 font-black text-blue-600 tracking-widest">{{ $pesanan->kode_pickup }}</td>
                            <td class="px-6 py-4 font-black text-slate-900 text-right">{{ format_rupiah($pesanan->grand_total) }}</td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-500">
                                {{ $pesanan->batas_ambil ? \Carbon\Carbon::parse($pesanan->batas_ambil)->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-xs font-bold text-slate-400">Tidak ada pesanan siap ambil saat ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
