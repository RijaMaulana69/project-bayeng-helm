@extends('layouts.admin')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
{{-- Filter Bar --}}
<div class="card card-bayeng mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua Aksi</option>
                    <option value="login_success" {{ request('action') == 'login_success' ? 'selected' : '' }}>Login Berhasil</option>
                    <option value="login_failed" {{ request('action') == 'login_failed' ? 'selected' : '' }}>Login Gagal</option>
                    <option value="login_blocked" {{ request('action') == 'login_blocked' ? 'selected' : '' }}>Login Diblokir</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="checkout" {{ request('action') == 'checkout' ? 'selected' : '' }}>Checkout</option>
                    <option value="update_status" {{ request('action') == 'update_status' ? 'selected' : '' }}>Update Status</option>
                    <option value="pickup" {{ request('action') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-admin flex-fill">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['action', 'date_from', 'date_to']))
                    <a href="{{ route('admin.activity-log.index') }}" wire:navigate class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card card-bayeng">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Log Aktivitas</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Aksi</th>
                        <th>Username</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="small text-nowrap">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                            <td>
                                @php
                                    $badgeClass = match($log->action) {
                                        'login_success' => 'bg-success',
                                        'login_failed'  => 'bg-warning text-dark',
                                        'login_blocked' => 'bg-danger',
                                        'logout'        => 'bg-secondary',
                                        'checkout'      => 'bg-info',
                                        'create'        => 'bg-primary',
                                        'update', 'update_status' => 'bg-warning text-dark',
                                        'delete'        => 'bg-danger',
                                        'pickup'        => 'bg-success',
                                        default         => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $log->action }}</span>
                            </td>
                            <td class="fw-semibold">{{ $log->username ?? ($log->user->username ?? '-') }}</td>
                            <td>{{ $log->description ?? '-' }}</td>
                            <td class="small text-muted font-monospace">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada log aktivitas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($logs->hasPages())
        <div class="card-footer">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
