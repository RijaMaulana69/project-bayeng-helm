@extends('layouts.admin')

@section('title', 'Produk Helm')
@section('page-title', 'Produk Helm')

@section('content')
<div class="card card-bayeng">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="fas fa-helmet-safety me-2"></i>Daftar Produk</h5>
        <a href="{{ route('admin.produk.create') }}" class="btn btn-primary-admin">
            <i class="fas fa-plus me-1"></i> Tambah Produk
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="datatable" class="table table-hover mb-0 w-100">
                <thead>
                    <tr>
                        <th width="70">Gambar</th>
                        <th>Nama Helm</th>
                        <th>Merk</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produks as $produk)
                        <tr>
                            <td>
                                @if ($produk->gambar)
                                    <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_helm }}" class="rounded" width="50" height="50" style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $produk->nama_helm }}</td>
                            <td>{{ $produk->merk->nama_merk ?? '-' }}</td>
                            <td>{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                            <td class="text-end">{{ format_rupiah($produk->harga_jual) }}</td>
                            <td class="text-center">
                                @if ($produk->stok <= 5)
                                    <span class="badge bg-danger">{{ $produk->stok }}</span>
                                @else
                                    <span class="badge bg-success">{{ $produk->stok }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.produk.edit', $produk->id_helm) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.produk.destroy', $produk->id_helm) }}" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-delete" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.0/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ]
        });

        // SweetAlert delete confirmation
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Produk?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
