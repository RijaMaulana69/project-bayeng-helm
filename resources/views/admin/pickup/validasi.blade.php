@extends('layouts.admin')
@section('title', 'Pickup: ' . $pesanan->no_pesanan)
@section('page-title', 'Validasi Pickup')

@section('content')
<div class="max-w-xl mx-auto space-y-3" x-data="{ showConfirm: false, submitting: false }">

    {{-- Navigasi Kembali --}}
    <a href="{{ route('admin.pickup.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-800 transition-colors">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali</span>
    </a>

    {{-- Kartu Validasi Kasir --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        
        @php
            $isPaid = in_array($pesanan->status, ['ready', 'paid']);
            $customerName = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan');
            $customerPhone = $pesanan->pelanggan->no_hp ?? ($pesanan->telepon_penerima ?? null);
        @endphp

        {{-- Header: Kode Pickup & Data Pembeli --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <span class="font-bold text-lg sm:text-xl text-slate-900 tracking-wide block">
                    {{ $pesanan->kode_pickup }}
                </span>
                <p class="text-xs text-slate-600 mt-0.5 truncate">
                    {{ $customerName }}@if($customerPhone) • <span class="text-slate-400">{{ $customerPhone }}</span>@endif
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    {{ $pesanan->no_pesanan }}
                </p>
            </div>

            <div class="shrink-0 text-right">
                @if($isPaid)
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        Lunas
                    </span>
                @else
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                        Bayar di Toko
                    </span>
                @endif
            </div>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            {{-- Daftar Barang & Tagihan --}}
            <div class="border border-slate-200/80 rounded-xl divide-y divide-slate-100 overflow-hidden bg-white">
                @foreach ($pesanan->details as $item)
                    @php
                        $helmName = $item->helm->nama_helm ?? ($item->nama_helm ?? 'Helm');
                        $variantParts = array_filter([$item->warna, $item->ukuran]);
                        $variantText = implode(', ', $variantParts);
                    @endphp
                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800 truncate">
                                <span class="font-bold text-slate-900">{{ $item->jumlah }}x</span> {{ $helmName }}
                            </p>
                            @if($variantText)
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $variantText }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right font-semibold text-slate-800 text-xs sm:text-sm shrink-0">
                            {{ format_rupiah($item->subtotal) }}
                        </div>
                    </div>
                @endforeach

                {{-- Total Tagihan --}}
                <div class="p-3.5 bg-slate-50/70 flex items-center justify-between text-xs border-t border-slate-200/70">
                    <div>
                        <span class="font-semibold text-slate-700 block">Total Tagihan</span>
                        @if($isPaid)
                            <span class="text-[11px] text-emerald-700 font-medium">Lunas ({{ ucfirst($pesanan->metode_bayar ?? 'Online') }})</span>
                        @else
                            <span class="text-[11px] text-amber-700 font-medium">Bayar di kasir ({{ ucfirst($pesanan->metode_bayar ?? 'Tunai') }})</span>
                        @endif
                    </div>
                    <span class="text-base sm:text-lg font-bold tracking-tight {{ $isPaid ? 'text-slate-900' : 'text-amber-900' }}">
                        {{ format_rupiah($pesanan->grand_total) }}
                    </span>
                </div>
            </div>

            {{-- Tombol Pemicu Konfirmasi --}}
            <div class="pt-1">
                <button type="button" 
                        @click="showConfirm = true"
                        class="w-full h-11 sm:h-12 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs sm:text-sm rounded-xl transition-all cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Serahkan Pesanan</span>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile-Friendly Action Sheet / Dialog Konfirmasi --}}
    <div x-show="showConfirm" 
         x-cloak
         @keydown.escape.window="if(!submitting) showConfirm = false"
         class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4"
         role="dialog"
         aria-modal="true">
        
        {{-- Backdrop --}}
        <div x-show="showConfirm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="if(!submitting) showConfirm = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        {{-- Bottom Sheet di HP, Modal Tengah di Desktop --}}
        <div x-show="showConfirm"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             class="relative w-full sm:max-w-sm bg-white rounded-t-3xl sm:rounded-2xl p-5 pb-8 sm:p-6 pb-[max(2rem,env(safe-area-inset-bottom))] shadow-2xl space-y-4">
            
            {{-- Handle Bar untuk Layar Mobile --}}
            <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto sm:hidden -mt-1 mb-2"></div>

            {{-- Teks Konfirmasi Alami & Ringkas --}}
            <div class="text-center sm:text-left space-y-1">
                <h3 class="text-base font-bold text-slate-900">
                    Selesaikan Penyerahan?
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Helm siap diserahkan ke <span class="font-semibold text-slate-700">{{ $customerName }}</span>.
                </p>
            </div>

            {{-- Form Aksi Ramah Jempol --}}
            <form method="POST" 
                  action="{{ route('admin.pickup.serahkan', $pesanan->id_pesanan) }}"
                  @submit="submitting = true"
                  class="space-y-2 pt-1">
                @csrf
                <button type="submit" 
                        :disabled="submitting"
                        class="w-full h-11 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-75 disabled:cursor-not-allowed text-white font-semibold text-xs sm:text-sm rounded-xl transition-all cursor-pointer shadow-2xs">
                    <template x-if="!submitting">
                        <span>Ya, Selesaikan</span>
                    </template>
                    <template x-if="submitting">
                        <span class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            <span>Memproses...</span>
                        </span>
                    </template>
                </button>

                <button type="button" 
                        @click="showConfirm = false"
                        :disabled="submitting"
                        class="w-full h-10 flex items-center justify-center text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
