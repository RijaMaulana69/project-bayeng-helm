<div>
    {{-- Filter & Search Section --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                {{-- Search --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-muted small text-uppercase">Cari Helm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Nama helm...">
                    </div>
                </div>
                {{-- Merk Filter --}}
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-muted small text-uppercase">Merk</label>
                    <select wire:model.live="merk_filter" class="form-select">
                        <option value="">Semua Merk</option>
                        @foreach($merks as $merk)
                            <option value="{{ $merk->id_merk }}">{{ $merk->nama_merk }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Kategori Filter --}}
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-muted small text-uppercase">Kategori</label>
                    <select wire:model.live="kategori_filter" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id_kategori }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Sort --}}
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold text-muted small text-uppercase">Urutkan</label>
                    <select wire:model.live="sort" class="form-select">
                        <option value="terbaru">Terbaru</option>
                        <option value="termurah">Termurah</option>
                        <option value="termahal">Termahal</option>
                        <option value="nama">Nama A-Z</option>
                    </select>
                </div>
                {{-- Reset --}}
                <div class="col-6 col-md-2">
                    <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                        <i class="fa fa-rotate-left me-1"></i> Reset Filter
                    </button>
                </div>
            </div>

            {{-- Price Range (collapsible) --}}
            <div class="row g-3 mt-1">
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold text-muted small text-uppercase">Harga Min</label>
                    <input type="number" wire:model.live.debounce.500ms="price_min" class="form-control" placeholder="0" min="0">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold text-muted small text-uppercase">Harga Max</label>
                    <input type="number" wire:model.live.debounce.500ms="price_max" class="form-control" placeholder="∞" min="0">
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="text-center py-4">
        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <p class="text-muted mt-2 mb-0">Memuat produk...</p>
    </div>

    {{-- Product Grid --}}
    <div wire:loading.remove>
        @if($helms->count() > 0)
            <div class="row g-4">
                @foreach($helms as $helm)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm product-card" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;">
                            {{-- Product Image --}}
                            <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="text-decoration-none">
                                <div class="position-relative" style="height: 220px; background: #f8f9fa;">
                                    <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-100 h-100" style="object-fit: contain; padding: 1rem;">
                                    @if($helm->merk)
                                        <span class="position-absolute top-0 start-0 m-2 badge text-white px-2 py-1" style="background-color: #2563eb; font-size: 0.7rem; border-radius: 6px;">
                                            {{ $helm->merk->nama_merk }}
                                        </span>
                                    @endif
                                    @if($helm->stok <= 3)
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark px-2 py-1" style="font-size: 0.7rem; border-radius: 6px;">
                                            Stok Terbatas
                                        </span>
                                    @endif
                                </div>
                            </a>
                            {{-- Product Info --}}
                            <div class="card-body d-flex flex-column p-3">
                                @if($helm->kategori)
                                    <span class="text-muted small mb-1">{{ $helm->kategori->nama_kategori }}</span>
                                @endif
                                <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="text-decoration-none">
                                    <h6 class="card-title fw-bold mb-2" style="color: #0f172a; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $helm->nama_helm }}
                                    </h6>
                                </a>
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold fs-6" style="color: #2563eb;">{{ format_rupiah($helm->harga_jual) }}</span>
                                        <span class="small {{ $helm->stok > 5 ? 'text-success' : 'text-warning' }}">
                                            <i class="fa fa-circle fa-xs me-1"></i>Stok: {{ $helm->stok }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            {{-- Quick Action --}}
                            <div class="card-footer bg-white border-0 p-3 pt-0">
                                <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="btn w-100 text-white fw-semibold" style="background-color: #2563eb; border-radius: 8px;">
                                    <i class="fa fa-eye me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $helms->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fa fa-helmet-safety fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">Tidak ada helm ditemukan</h5>
                <p class="text-muted">Coba ubah kata kunci atau filter pencarian Anda.</p>
                <button wire:click="resetFilters" class="btn text-white px-4" style="background-color: #2563eb; border-radius: 8px;">
                    <i class="fa fa-rotate-left me-1"></i> Reset Semua Filter
                </button>
            </div>
        @endif
    </div>

    <style>
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
        }
    </style>
</div>
