@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('content')
<div class="space-y-4 max-w-5xl mx-auto">

    {{-- 1. Header Minimalist --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Log Aktivitas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Audit jejak peristiwa sistem, login kasir, dan riwayat aktivitas gerai
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="px-3 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 text-xs font-medium shadow-2xs">
                <span class="font-bold text-slate-900 tabular-nums">{{ number_format($logs->total(), 0, ',', '.') }}</span> peristiwa tercatat
            </span>
        </div>
    </div>

    {{-- 2. Filter Bar Ramping --}}
    @php
        $startDateVal = request('start_date', request('date_from', ''));
        $endDateVal   = request('end_date', request('date_to', ''));
        $hasFilter    = request()->filled('action') || !empty($startDateVal) || !empty($endDateVal);
    @endphp
    <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 text-xs items-end">
            {{-- Filter Aksi --}}
            <div class="sm:col-span-4 space-y-1">
                <label class="block text-[11px] font-semibold text-slate-600">Jenis Peristiwa</label>
                <select name="action" class="w-full h-8.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400 cursor-pointer">
                    <option value="">Semua Peristiwa</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ \App\Models\ActivityLog::labelForAction($act) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dari Tanggal --}}
            <div class="sm:col-span-3 space-y-1">
                <label class="block text-[11px] font-semibold text-slate-600">Dari Tanggal</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ $startDateVal }}" 
                       class="w-full h-8.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
            </div>

            {{-- Sampai Tanggal --}}
            <div class="sm:col-span-3 space-y-1">
                <label class="block text-[11px] font-semibold text-slate-600">Sampai Tanggal</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ $endDateVal }}" 
                       class="w-full h-8.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-slate-400">
            </div>

            {{-- Tombol Aksi --}}
            <div class="sm:col-span-2 flex items-center gap-1.5">
                <button type="submit" 
                        class="flex-1 h-8.5 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filter</span>
                </button>

                @if($hasFilter)
                    <a href="{{ route('admin.activity-log.index') }}" 
                       wire:navigate
                       title="Reset Filter"
                       class="h-8.5 w-8.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors shrink-0">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. Activity Ledger (Minimalist Text Ledger - 100% Tailwind CSS Bersih) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Ledger Header --}}
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
            <span class="font-semibold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-slate-400 text-[11px]"></i>
                <span>Linimasa Peristiwa</span>
            </span>
            <span class="text-slate-400 text-[11px]">
                Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}
            </span>
        </div>

        {{-- Ledger Items --}}
        <div class="divide-y divide-slate-100">
            @forelse ($logs as $log)
                @php
                    $roleColor = match(strtolower($log->aktor_peran)) {
                        'pemilik toko' => 'text-blue-600',
                        'kasir gerai'  => 'text-indigo-600',
                        'pelanggan'    => 'text-slate-600',
                        default        => 'text-slate-400',
                    };
                @endphp
                <div class="p-3.5 sm:p-4 hover:bg-slate-50/60 transition-colors flex flex-col sm:flex-row sm:items-start justify-between gap-2.5 sm:gap-4">
                    {{-- Kolom 1: Timestamp & Waktu Relatif --}}
                    <div class="sm:w-36 shrink-0 space-y-0.5">
                        <span class="font-semibold text-xs text-slate-900 block">
                            {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                        <span class="text-[11px] text-slate-400 block">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Kolom 2: Narasi Peristiwa, Aktor & Deskripsi --}}
                    <div class="flex-1 min-w-0 space-y-1">
                        {{-- Baris Info Aksi & Aktor --}}
                        <div class="flex items-center gap-2 text-xs truncate">
                            <span class="font-bold text-slate-900">
                                {{ $log->aksi_label }}
                            </span>
                            <span class="text-slate-300">·</span>
                            <span class="font-medium text-slate-800 truncate">
                                {{ $log->aktor_nama }}
                            </span>
                            <span class="text-slate-300">·</span>
                            <span class="text-[11px] font-medium {{ $roleColor }} shrink-0">
                                {{ $log->aktor_peran }}
                            </span>
                        </div>

                        {{-- Baris Deskripsi Detail --}}
                        <p class="text-xs text-slate-600 leading-relaxed break-words font-normal">
                            {{ $log->description ?: '-' }}
                        </p>
                    </div>

                    {{-- Kolom 3: Alamat IP & Metadata Teknis --}}
                    <div class="sm:w-28 shrink-0 sm:text-right pt-0.5 sm:pt-0">
                        <span class="text-[11px] text-slate-400 font-mono">
                            {{ $log->ip_address ?: '-' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-14 text-center text-slate-400 space-y-2">
                    <div class="w-11 h-11 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <i class="fa-solid fa-list-check text-lg"></i>
                    </div>
                    <p class="text-xs font-medium text-slate-600">Tidak ada peristiwa yang cocok dengan filter</p>
                    <p class="text-[11px] text-slate-400">Coba ubah tanggal atau pilih semua jenis peristiwa</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Footer --}}
        @if ($logs->hasPages())
            <div class="p-3 sm:p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links('partials.pagination-admin') }}
            </div>
        @endif
    </div>

</div>
@endsection
