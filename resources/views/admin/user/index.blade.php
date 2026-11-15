@extends('layouts.admin')

@section('title', 'Kelola Karyawan')
@section('page-title', 'Kelola Karyawan')

@section('content')
<div class="space-y-4 max-w-5xl mx-auto">

    {{-- 1. Header Minimalist --}}
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Kelola Karyawan
            </h1>
            <p class="text-xs text-slate-500">
                Manajemen akun operasional kasir & staf gerai
            </p>
        </div>

        <a href="{{ route('admin.user.create') }}" 
           wire:navigate
           class="h-8 sm:h-9 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs transition-colors shrink-0">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span class="hidden sm:inline">Tambah Karyawan</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>

    {{-- 2. Compact Info Strip --}}
    <div class="bg-white p-3 sm:p-3.5 rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <p class="text-xs font-medium text-slate-700">
                Total Karyawan Aktif: <span class="font-bold text-slate-900 font-mono">{{ count($users) }}</span> staf
            </p>
        </div>
        <p class="text-[11px] text-slate-400 hidden sm:block">
            Hak akses: Kasir gerai, validasi pickup, dan input stok
        </p>
    </div>

    {{-- 3. Responsive List / Table --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        {{-- Desktop Table (sm and up) --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Karyawan</th>
                        <th class="py-3 px-4">Peran</th>
                        <th class="py-3 px-4">Bergabung</th>
                        <th class="py-3 px-4">Terakhir Aktif</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($u->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900 truncate">
                                            {{ $u->nama_lengkap }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 font-mono">
                                            {{ '@' . $u->username }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border bg-blue-50 text-blue-700 border-blue-200">
                                    {{ $u->role->nama_role ?? 'Karyawan' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-[11px]">
                                @if($u->last_active)
                                    <span class="text-slate-600 font-medium">
                                        {{ \Carbon\Carbon::parse($u->last_active)->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.user.edit', $u->id_user) }}" 
                                       wire:navigate
                                       title="Edit Karyawan"
                                       class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 flex items-center justify-center text-xs transition-colors">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </a>

                                    @if ($u->id_user !== auth()->id())
                                        <form method="POST" action="{{ route('admin.user.destroy', $u->id_user) }}" class="inline form-delete-user">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="confirmDeleteUser(this, '{{ addslashes($u->nama_lengkap) }}')"
                                                    title="Hapus Karyawan"
                                                    class="btn-delete w-7 h-7 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 flex items-center justify-center text-xs transition-colors cursor-pointer">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-users text-2xl text-slate-300 block mb-1"></i>
                                Belum ada data karyawan terdaftar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards (sm hidden) --}}
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse ($users as $u)
                <div class="p-3.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                            {{ strtoupper(substr($u->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-xs text-slate-900 truncate">
                                {{ $u->nama_lengkap }}
                            </p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] text-slate-400 font-mono">{{ '@' . $u->username }}</span>
                                <span class="text-slate-300 text-[10px]">•</span>
                                <span class="text-[10px] font-semibold text-blue-600">{{ $u->role->nama_role ?? 'Karyawan' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ route('admin.user.edit', $u->id_user) }}" 
                           wire:navigate
                           class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 flex items-center justify-center text-xs transition-colors">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </a>

                        @if ($u->id_user !== auth()->id())
                            <form method="POST" action="{{ route('admin.user.destroy', $u->id_user) }}" class="inline form-delete-user">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        onclick="confirmDeleteUser(this, '{{ addslashes($u->nama_lengkap) }}')"
                                        class="btn-delete w-7 h-7 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 flex items-center justify-center text-xs transition-colors cursor-pointer">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-users text-2xl text-slate-300 block mb-1"></i>
                    Belum ada data karyawan terdaftar
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
