@extends('layouts.admin')
@section('title', 'Kasir Pickup')
@section('page-title', 'Kasir Pickup')

@section('content')
<div class="space-y-4 max-w-5xl mx-auto" x-data="{ pickupFilter: 'all' }">

    {{-- Notifikasi Mengambang di Atas (Floating Top Toast) --}}
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4500)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-[92%] sm:w-auto sm:min-w-[360px] max-w-md px-4 py-3 bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700/80 flex items-center justify-between gap-3 backdrop-blur-md">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <i class="fa-solid fa-check text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">Penyerahan Berhasil</p>
                    <p class="text-[11px] text-slate-300 truncate mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" @click="show = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer shrink-0" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-[92%] sm:w-auto sm:min-w-[360px] max-w-md px-4 py-3 bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700/80 flex items-center justify-between gap-3 backdrop-blur-md">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/30">
                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">Gagal</p>
                    <p class="text-[11px] text-slate-300 truncate mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" @click="show = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer shrink-0" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

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
            <div class="w-full sm:w-auto flex p-0.5 bg-slate-100 rounded-xl text-[11px] sm:text-xs">
                <button type="button" 
                        @click="pickupFilter = 'all'"
                        :class="pickupFilter === 'all' ? 'bg-white text-slate-900 font-semibold shadow-2xs' : 'text-slate-500 hover:text-slate-800 font-medium'"
                        class="flex-1 sm:flex-initial px-2.5 sm:px-3 py-1.5 rounded-lg text-center transition-all cursor-pointer">
                    Semua ({{ $totalOrders }})
                </button>
                <button type="button" 
                        @click="pickupFilter = 'unpaid'"
                        :class="pickupFilter === 'unpaid' ? 'bg-white text-amber-700 font-semibold shadow-2xs' : 'text-slate-500 hover:text-amber-700 font-medium'"
                        class="flex-1 sm:flex-initial px-2.5 sm:px-3 py-1.5 rounded-lg text-center transition-all cursor-pointer">
                    <span class="sm:hidden">Bayar</span><span class="hidden sm:inline">Bayar di Toko</span> ({{ $unpaidOrders }})
                </button>
                <button type="button" 
                        @click="pickupFilter = 'ready'"
                        :class="pickupFilter === 'ready' ? 'bg-white text-emerald-700 font-semibold shadow-2xs' : 'text-slate-500 hover:text-emerald-700 font-medium'"
                        class="flex-1 sm:flex-initial px-2.5 sm:px-3 py-1.5 rounded-lg text-center transition-all cursor-pointer">
                    <span class="sm:hidden">Lunas</span><span class="hidden sm:inline">Sudah Lunas</span> ({{ $paidOrders }})
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
                            <div class="flex items-baseline gap-2">
                                <span class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-blue-600 transition-colors shrink-0">
                                    {{ $pesanan->kode_pickup }}
                                </span>
                                <span class="text-xs text-slate-500 truncate">
                                    {{ $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan') }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-700 truncate mt-1">
                                {{ $itemSummary }}
                            </p>
                        </div>

                        <div class="text-right shrink-0 flex items-center gap-2">
                            @if($isPaid)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                    Lunas
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                    Bayar di Toko
                                </span>
                            @endif
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-blue-600 transition-colors"></i>
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
