@extends('layouts.admin')

@section('title', 'Kelola Karyawan')
@section('page-title', 'Kelola Karyawan')

@section('content')
<div class="card card-bayeng">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="fas fa-users-gear me-2"></i>Daftar Karyawan</h5>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary-admin">
            <i class="fas fa-plus me-1"></i> Tambah Karyawan
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th>Terakhir Aktif</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->username }}</td>
                            <td>{{ $user->nama_lengkap }}</td>
                            <td>
                                @if ($user->role && $user->role->nama_role === 'Owner')
                                    <span class="badge bg-primary">{{ $user->role->nama_role }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $user->role->nama_role ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="small text-muted">
                                {{ $user->last_active ? \Carbon\Carbon::parse($user->last_active)->diffForHumans() : '-' }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.user.edit', $user->id_user) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($user->id_user !== auth()->id())
                                        <form method="POST" action="{{ route('admin.user.destroy', $user->id_user) }}" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-delete" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data karyawan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Karyawan?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
