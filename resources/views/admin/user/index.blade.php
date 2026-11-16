@extends('layouts.admin')

@section('title', 'Kelola Karyawan')
@section('page-title', 'Kelola Karyawan')

@section('content')
<div class="space-y-4 max-w-5xl mx-auto">

    {{-- 1. Header Minimalist --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Kelola Karyawan
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Manajemen akun staf kasir dan hak akses gerai helm
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.user.create') }}" 
               wire:navigate
               class="h-8.5 px-3.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs inline-flex items-center justify-center gap-1.5 shadow-2xs transition-colors shrink-0">
                <i class="fa-solid fa-user-plus text-[11px]"></i>
                <span>Tambah Karyawan</span>
            </a>
        </div>
    </div>

    {{-- 2. Daftar Karyawan (Staff Text Ledger - 100% Tailwind CSS Bersih & Responsif) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Ledger Header --}}
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs bg-slate-50/50">
            <span class="font-semibold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-users text-slate-400 text-[11px]"></i>
                <span>Daftar Staf Gerai</span>
            </span>
            <span class="text-slate-400 text-[11px]">
                {{ count($users) }} staf terdaftar
            </span>
        </div>

        {{-- Ledger Items (Anti-Patah & Tipografi Murni Tanpa Background Badge Lebay) --}}
        <div class="divide-y divide-slate-100">
            @forelse ($users as $u)
                @php
                    $roleLabel = match(strtolower($u->role->nama_role ?? '')) {
                        'karyawan' => 'Kasir Gerai',
                        'owner'    => 'Owner Gerai',
                        default    => ucfirst($u->role->nama_role ?? 'Staf'),
                    };
                    $isActiveRecently = $u->last_active && $u->last_active->diffInMinutes(now()) <= 30;
                @endphp
                <div class="p-3.5 sm:p-4 hover:bg-slate-50/60 transition-colors flex items-center justify-between gap-3">
                    {{-- Kolom Kiri: Avatar Inisial & Info Akun Staf --}}
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm flex items-center justify-center shrink-0 border border-slate-200/70">
                            {{ strtoupper(substr($u->nama_lengkap, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1 space-y-0.5">
                            {{-- Baris 1: Nama Lengkap & Peran Akses Teks Murni --}}
                            <div class="flex items-center gap-2 truncate">
                                <span class="font-semibold text-xs sm:text-sm text-slate-900 truncate">
                                    {{ $u->nama_lengkap }}
                                </span>
                                <span class="text-slate-300 text-xs">·</span>
                                <span class="text-[11px] font-semibold text-blue-600 shrink-0">
                                    {{ $roleLabel }}
                                </span>
                            </div>

                            {{-- Baris 2: Username & Tanggal Bergabung --}}
                            <div class="text-[11px] text-slate-500 truncate">
                                <span class="text-slate-600 font-medium">&#64;{{ $u->username }}</span>
                                <span class="text-slate-300 mx-1">·</span>
                                <span class="text-slate-400">Bergabung {{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}</span>
                            </div>

                            {{-- Baris 3: Status Aktivitas Terakhir --}}
                            <div class="text-[10px] sm:text-[11px] text-slate-400 flex items-center gap-1.5 pt-0.5 truncate">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isActiveRecently ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <span>
                                    @if ($u->last_active)
                                        Terakhir aktif {{ $u->last_active->diffForHumans() }}
                                    @else
                                        Belum pernah login
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Tombol Aksi Ramping --}}
                    <div class="flex items-center gap-1.5 shrink-0 pl-2">
                        <a href="{{ route('admin.user.edit', $u->id_user) }}" 
                           wire:navigate
                           title="Ubah Data Karyawan"
                           class="h-8 px-2.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-medium inline-flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                            <span class="hidden sm:inline">Ubah</span>
                        </a>

                        @if ($u->id_user !== auth()->id())
                            <form method="POST" action="{{ route('admin.user.destroy', $u->id_user) }}" class="inline form-delete-user">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        onclick="confirmDeleteUser(this, '{{ addslashes($u->nama_lengkap) }}')"
                                        title="Hapus Karyawan"
                                        class="btn-delete h-8 px-2.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 text-xs font-medium inline-flex items-center gap-1.5 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-14 text-center text-slate-400 space-y-2">
                    <div class="w-11 h-11 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                    <p class="text-xs font-medium text-slate-600">Belum ada akun karyawan terdaftar</p>
                    <p class="text-[11px] text-slate-400">Klik tombol Tambah Karyawan untuk mendaftarkan staf baru</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function confirmDeleteUser(btn, name) {
        const form = btn.closest('form');
        Swal.fire({
            title: 'Hapus Karyawan?',
            text: 'Akun staf "' + name + '" akan dinonaktifkan secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endpush
