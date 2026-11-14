@extends('layouts.pelanggan')
@section('title', 'Pesanan Saya - Bayeng Putra Helm')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-6 pb-20 sm:pb-12">
    <div class="max-w-3xl mx-auto px-3 sm:px-4 space-y-3">
        
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-2xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-sm sm:text-base font-bold text-slate-900">Pesanan Saya</h1>
            <a href="{{ route('katalog') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                <span>Belanja Helm</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        {{-- Status Filter Tabs (Scrollable on Mobile) --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs">
            @php
                $tabs = [
                    'semua'          => ['label' => 'Semua', 'count' => $counts['semua'] ?? 0],
                    'menunggu_bayar' => ['label' => 'Menunggu Bayar', 'count' => $counts['menunggu_bayar'] ?? 0],
                    'diproses'       => ['label' => 'Diproses', 'count' => $counts['diproses'] ?? 0],
                    'siap_diambil'   => ['label' => 'Siap Diambil', 'count' => $counts['siap_diambil'] ?? 0],
                    'selesai'        => ['label' => 'Selesai', 'count' => $counts['selesai'] ?? 0],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                @php
                    $isActive = ($activeTab === $key);
                @endphp
                <a href="{{ route('pelanggan.pesanan', $key === 'semua' ? [] : ['status' => $key]) }}"
                   class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors flex items-center gap-1.5 {{ $isActive ? 'bg-blue-600 text-white font-medium shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    <span>{{ $tab['label'] }}</span>
                    @if($tab['count'] > 0 && $key !== 'semua')
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $isActive ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                            {{ $tab['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Orders List --}}
        @forelse($pesanans as $pesanan)
            @php
                $firstItem = $pesanan->details->first();
                $otherCount = $pesanan->details->count() - 1;

                $badgeClass = match($pesanan->status) {
                    'pending'            => 'bg-amber-50 text-amber-700 border-amber-200/60',
                    'unpaid_pickup'      => 'bg-blue-50 text-blue-700 border-blue-200/60',
                    'paid', 'processing' => 'bg-sky-50 text-sky-700 border-sky-200/60',
                    'ready'              => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                    'completed'          => 'bg-slate-100 text-slate-600 border-slate-200',
                    'cancelled'          => 'bg-rose-50 text-rose-700 border-rose-200/60',
                    default              => 'bg-slate-50 text-slate-600 border-slate-200',
                };
            @endphp

            <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 shadow-2xs overflow-hidden">
                
                {{-- Header Row: Date, Order No & Status Badge --}}
                <div class="px-3.5 sm:px-4 py-2.5 flex items-center justify-between text-xs bg-slate-50/40">
                    <div class="flex items-center gap-2 text-slate-500 text-[11px]">
                        <span>{{ $pesanan->created_at->format('d M Y') }}</span>
                        <span>&bull;</span>
                        <span class="font-mono text-slate-700">{{ $pesanan->no_pesanan }}</span>
                    </div>

                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border {{ $badgeClass }}">
                        {{ $pesanan->status_label }}
                    </span>
                </div>

                {{-- Product Preview Row --}}
                <div class="p-3.5 sm:p-4">
                    @if($firstItem)
                        <div class="flex items-start gap-3">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0">
                                @if($firstItem->helm && $firstItem->helm->gambar_url)
                                    <img src="{{ $firstItem->helm->gambar_url }}" alt="{{ $firstItem->nama_helm }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-helmet-safety"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <h2 class="text-xs sm:text-sm font-medium text-slate-900 truncate">
                                    {{ $firstItem->nama_helm }}
                                </h2>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Varian: {{ $firstItem->warna ?? '-' }}, Size {{ $firstItem->ukuran ?? '-' }} &bull; {{ $firstItem->qty }} barang
                                </p>
                                @if($otherCount > 0)
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        +{{ $otherCount }} helm lainnya
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer Row: Pickup Code & Actions --}}
                <div class="px-3.5 sm:px-4 py-2.5 flex items-center justify-between gap-3 bg-white">
                    <div>
                        @if(in_array($pesanan->status, ['ready', 'unpaid_pickup', 'paid']))
                            <div class="text-[11px] text-slate-500">
                                Kode Pickup: <span class="font-mono font-bold text-blue-600">{{ $pesanan->kode_pickup }}</span>
                            </div>
                        @else
                            <span class="text-[11px] text-slate-400">
                                {{ $pesanan->created_at->format('H:i') }} WIB
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block leading-tight">Total</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 leading-tight">
                                {{ format_rupiah($pesanan->grand_total) }}
                            </span>
                        </div>

                        @if($pesanan->status === 'pending' && in_array($pesanan->metode_bayar, ['transfer', 'qris']))
                            <a href="{{ route('pelanggan.pesanan.detail', $pesanan->id_pesanan) }}" 
                               class="h-8 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs flex items-center justify-center shadow-2xs active:scale-98 transition-all">
                                Bayar
                            </a>
                        @else
                            <a href="{{ route('pelanggan.pesanan.detail', $pesanan->id_pesanan) }}" 
                               class="h-8 px-3 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs flex items-center justify-center transition-colors shadow-2xs">
                                Detail
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            {{-- Empty State --}}
            <div class="bg-white rounded-xl p-8 sm:p-12 text-center border border-slate-200 shadow-2xs max-w-sm mx-auto my-6 space-y-3">
                <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="space-y-1">
                    <h2 class="text-sm font-semibold text-slate-900">Belum ada pesanan</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pilih helm favoritmu dan pesan sekarang.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('katalog') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-sm transition-all active:scale-95">
                        <span>Mulai Belanja</span>
                    </a>
                </div>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($pesanans->hasPages())
            <div class="pt-2">
                {{ $pesanans->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
