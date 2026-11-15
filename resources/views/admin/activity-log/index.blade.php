@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('content')
<div class="space-y-4 max-w-6xl mx-auto">

    {{-- 1. Header Minimalist --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Log Aktivitas
        </h1>

        <span class="px-3 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-semibold shadow-2xs shrink-0">
            Total: <span class="font-mono text-slate-900">{{ number_format($logs->total(), 0, ',', '.') }}</span>
        </span>
    </div>

    {{-- 2. Filter Bar --}}
    @php
        $startDateVal = request('start_date', request('date_from', ''));
        $endDateVal   = request('end_date', request('date_to', ''));
        $hasFilter    = request()->filled('action') || !empty($startDateVal) || !empty($endDateVal);
    @endphp
    <div class="bg-white p-3 sm:p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs items-end">
            {{-- Filter Aksi --}}
            <div class="space-y-1">
                <label class="block font-semibold text-slate-700">Jenis Aksi</label>
                <select name="action" class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900 cursor-pointer">
                    <option value="">Semua Aksi</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $act)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dari Tanggal --}}
            <div class="space-y-1">
                <label class="block font-semibold text-slate-700">Dari Tanggal</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ $startDateVal }}" 
                       class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900">
            </div>

            {{-- Sampai Tanggal --}}
            <div class="space-y-1">
                <label class="block font-semibold text-slate-700">Sampai Tanggal</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ $endDateVal }}" 
                       class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-900">
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 h-9 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filter</span>
                </button>

                @if($hasFilter)
                    <a href="{{ route('admin.activity-log.index') }}" 
                       wire:navigate
                       title="Reset Filter"
                       class="h-9 px-3 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. Log Records Container --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Desktop Table (sm and up) --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Aksi</th>
                        <th class="py-3 px-4">Aktor / User</th>
                        <th class="py-3 px-4">Deskripsi Aktivitas</th>
                        <th class="py-3 px-4 text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        @php
                            $actionName = $log->action;
                            $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';
                            if (str_contains($actionName, 'success') || str_contains($actionName, 'completed')) {
                                $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif (str_contains($actionName, 'failed') || str_contains($actionName, 'blocked') || str_contains($actionName, 'delete')) {
                                $badgeColor = 'bg-rose-50 text-rose-700 border-rose-200';
                            } elseif (str_contains($actionName, 'create') || str_contains($actionName, 'add')) {
                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                            } elseif (str_contains($actionName, 'update')) {
                                $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-mono text-slate-900 block font-medium">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeColor }}">
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-medium text-slate-900">
                                    {{ $log->user->nama_lengkap ?? ($log->username ?? 'Sistem / Tamu') }}
                                </div>
                                @if($log->username)
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ '@' . $log->username }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-md">
                                <p class="line-clamp-2 leading-relaxed">
                                    {{ $log->description ?? '-' }}
                                </p>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <span class="font-mono text-[11px] bg-slate-50 border border-slate-200/80 px-2 py-0.5 rounded text-slate-600">
                                    {{ $log->ip_address ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-list-check text-2xl text-slate-300 block mb-1"></i>
                                Tidak ada log aktivitas yang cocok dengan filter
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards (sm hidden) --}}
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse ($logs as $log)
                @php
                    $actionName = $log->action;
                    $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';
                    if (str_contains($actionName, 'success') || str_contains($actionName, 'completed')) {
                        $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    } elseif (str_contains($actionName, 'failed') || str_contains($actionName, 'blocked') || str_contains($actionName, 'delete')) {
                        $badgeColor = 'bg-rose-50 text-rose-700 border-rose-200';
                    } elseif (str_contains($actionName, 'create') || str_contains($actionName, 'add')) {
                        $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                    } elseif (str_contains($actionName, 'update')) {
                        $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                    }
                @endphp
                <div class="p-3.5 space-y-2 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeColor }}">
                            {{ ucwords(str_replace('_', ' ', $log->action)) }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <p class="text-slate-800 leading-relaxed font-medium">
                        {{ $log->description ?? '-' }}
                    </p>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-50">
                        <div class="flex items-center gap-1.5 truncate">
                            <i class="fa-regular fa-user text-slate-400 text-[10px]"></i>
                            <span class="truncate">{{ $log->user->nama_lengkap ?? ($log->username ?? 'Tamu') }}</span>
                        </div>
                        <span class="font-mono text-[10px] bg-slate-50 px-1.5 py-0.2 rounded border border-slate-200 text-slate-500 shrink-0">
                            {{ $log->ip_address ?? '-' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-list-check text-2xl text-slate-300 block mb-1"></i>
                    Tidak ada log aktivitas ditemukan
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
