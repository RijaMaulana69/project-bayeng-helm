@extends('layouts.admin')
@section('title', 'Kasir Pickup')
@section('page-title', 'Kasir Pickup')

@section('content')
<div class="space-y-4 max-w-5xl mx-auto" x-data="{ pickupFilter: 'all' }">

    {{-- 1. Search Bar / Pickup Input Bar --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="POST" action="{{ route('admin.pickup.validasi') }}" class="flex items-center gap-2">
            @csrf
            <div class="relative flex-1">
                <i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text"
                       name="kode_pickup"
                       class="w-full h-11 pl-10 pr-4 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider transition-all placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400"
                       placeholder="Masukkan kode pickup"
                       value="{{ old('kode_pickup') }}"
                       autocomplete="off"
                       autofocus
                       required>
            </div>
            <button type="submit" 
                    class="h-11 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs sm:text-sm font-semibold inline-flex items-center shadow-2xs transition-colors cursor-pointer shrink-0">
                <span>Validasi</span>
            </button>
        </form>
        @error('kode_pickup')
            <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- 2. Antrean Ambil Pesanan (Ultra-Compact List) --}}
    @php
        $totalOrders = $pesanan_ready->count();
        $unpaidOrders = $pesanan_ready->where('status', 'unpaid_pickup')->count();
        $paidOrders = $pesanan_ready->whereIn('status', ['ready', 'paid'])->count();
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Header & Status Tabs --}}
        <div class="p-3.5 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Antrean Siap Ambil</h2>
            </div>

            {{-- Segmented Filter Tabs --}}
            <div class="flex p-0.5 bg-slate-100 rounded-xl text-xs shrink-0">
                <button type="button" 
                        @click="pickupFilter = 'all'"
                        :class="pickupFilter === 'all' ? 'bg-white text-slate-900 font-semibold shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                    Semua ({{ $totalOrders }})
                </button>
                <button type="button" 
                        @click="pickupFilter = 'unpaid'"
                        :class="pickupFilter === 'unpaid' ? 'bg-white text-amber-700 font-semibold shadow-2xs' : 'text-slate-500 hover:text-amber-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                    Bayar di Toko ({{ $unpaidOrders }})
                </button>
                <button type="button" 
                        @click="pickupFilter = 'ready'"
                        :class="pickupFilter === 'ready' ? 'bg-white text-emerald-700 font-semibold shadow-2xs' : 'text-slate-500 hover:text-emerald-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                    Sudah Lunas ({{ $paidOrders }})
                </button>
            </div>
        </div>

        {{-- Ultra-Compact Interactive List --}}
        <div class="divide-y divide-slate-100">
            @forelse ($pesanan_ready as $pesanan)
                @php
                    $firstDetail = $pesanan->details->first();
                    $firstHelm = $firstDetail ? ($firstDetail->helm->nama_helm ?? ($firstDetail->nama_helm ?? 'Helm')) : 'Item Helm';
                    $firstSize = $firstDetail && $firstDetail->ukuran ? " ({$firstDetail->ukuran})" : '';
                    $extraCount = $pesanan->details->count() - 1;
                    $itemSummary = $firstHelm . $firstSize . ($extraCount > 0 ? " +{$extraCount} item" : '');
                    $isPaid = in_array($pesanan->status, ['ready', 'paid']);
                @endphp
                <form method="POST" action="{{ route('admin.pickup.validasi') }}"
                      x-show="pickupFilter === 'all' || (pickupFilter === 'unpaid' && '{{ $pesanan->status }}' === 'unpaid_pickup') || (pickupFilter === 'ready' && ('{{ $pesanan->status }}' === 'ready' || '{{ $pesanan->status }}' === 'paid'))">
                    @csrf
                    <input type="hidden" name="kode_pickup" value="{{ $pesanan->kode_pickup }}">
                    <button type="submit" class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors group cursor-pointer">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 truncate">
                                <span class="font-mono font-bold text-xs sm:text-sm text-slate-900 group-hover:text-blue-600 transition-colors tracking-wide shrink-0">
                                    {{ $pesanan->kode_pickup }}
                                </span>
                                <span class="text-slate-300 text-xs shrink-0">•</span>
                                <span class="text-xs font-semibold text-slate-700 truncate">
                                    {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan Umum') }}
                                </span>
                                <span class="text-slate-300 text-xs shrink-0 hidden sm:inline">•</span>
                                <span class="text-xs text-slate-500 truncate hidden sm:inline">
                                    {{ $itemSummary }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate mt-0.5 sm:hidden">
                                {{ $itemSummary }}
                            </p>
                        </div>

                        <div class="text-right shrink-0 flex items-center gap-2">
                            @if($isPaid)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                    Lunas
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                    Bayar di Toko
                                </span>
                            @endif
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-blue-600 transition-colors"></i>
                        </div>
                    </button>
                </form>
            @empty
                <div class="p-12 text-center">
                    <i class="fa-regular fa-circle-check text-3xl text-emerald-500 mb-2"></i>
                    <p class="text-xs font-semibold text-slate-700">Tidak ada antrean</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Semua pesanan siap ambil sudah selesai.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
