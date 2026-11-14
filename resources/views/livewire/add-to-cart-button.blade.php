<div>
    {{-- Flash Messages --}}
    @if(session()->has('cart-success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert" style="border-radius: 8px;">
            <i class="fa fa-check-circle me-1"></i> {{ session('cart-success') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('cart-error'))
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert" style="border-radius: 8px;">
            <i class="fa fa-exclamation-circle me-1"></i> {{ session('cart-error') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Variant Selector --}}
    <div class="mb-3">
        <label class="form-label fw-bold" style="color: #0f172a;">Pilih Varian</label>
        <div class="d-flex flex-wrap gap-2">
            @forelse($availableVariants as $varian)
                <button
                    type="button"
                    wire:click="selectVarian({{ $varian['id_varian'] }})"
                    class="btn btn-sm {{ $selectedVarian == $varian['id_varian'] ? 'text-white' : 'btn-outline-secondary' }}"
                    style="{{ $selectedVarian == $varian['id_varian'] ? 'background-color: #2563eb; border-color: #2563eb;' : '' }} border-radius: 8px; padding: 0.4rem 0.8rem;"
                    {{ $varian['stok'] <= 0 ? 'disabled' : '' }}
                >
                    {{ $varian['warna'] }} &bull; {{ $varian['ukuran'] }}
                    @if($varian['stok'] <= 0)
                        <small class="text-muted">(Habis)</small>
                    @endif
                </button>
            @empty
                <p class="text-muted small mb-0">Tidak ada varian tersedia.</p>
            @endforelse
        </div>
    </div>

    {{-- Stock Info for Selected Variant --}}
    @if($selectedVarian)
        @php
            $selectedStock = collect($availableVariants)->firstWhere('id_varian', $selectedVarian);
        @endphp
        @if($selectedStock)
            <div class="mb-3">
                <span class="small {{ $selectedStock['stok'] > 5 ? 'text-success' : 'text-warning' }}">
                    <i class="fa fa-circle fa-xs me-1"></i>
                    Stok tersedia: <strong>{{ $selectedStock['stok'] }}</strong> unit
                </span>
            </div>
        @endif
    @endif

    {{-- Quantity Input --}}
    <div class="mb-3">
        <label class="form-label fw-bold" style="color: #0f172a;">Jumlah</label>
        <div class="d-flex align-items-center" style="max-width: 160px;">
            <button wire:click="decrementQty" class="btn btn-outline-secondary" style="border-radius: 8px 0 0 8px;" {{ $qty <= 1 ? 'disabled' : '' }}>
                <i class="fa fa-minus"></i>
            </button>
            <input type="number" wire:model="qty" class="form-control text-center border-start-0 border-end-0" style="border-radius: 0;" min="1" max="{{ $selectedVarian ? collect($availableVariants)->firstWhere('id_varian', $selectedVarian)['stok'] ?? 1 : 1 }}" readonly>
            <button wire:click="incrementQty" class="btn btn-outline-secondary" style="border-radius: 0 8px 8px 0;" {{ !$selectedVarian || $qty >= (collect($availableVariants)->firstWhere('id_varian', $selectedVarian)['stok'] ?? 0) ? 'disabled' : '' }}>
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>

    {{-- Add to Cart Button --}}
    <button
        wire:click="addToCart"
        wire:loading.attr="disabled"
        class="btn w-100 text-white fw-bold py-2"
        style="background-color: #2563eb; border-radius: 8px; font-size: 1.05rem;"
        {{ !$selectedVarian || (collect($availableVariants)->firstWhere('id_varian', $selectedVarian)['stok'] ?? 0) <= 0 ? 'disabled' : '' }}
    >
        <span wire:loading.remove wire:target="addToCart">
            <i class="fa fa-cart-plus me-2"></i> Tambah ke Keranjang
        </span>
        <span wire:loading wire:target="addToCart">
            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menambahkan...
        </span>
    </button>

    @if(!$selectedVarian && count($availableVariants) > 0)
        <p class="text-muted small mt-2 mb-0 text-center">
            <i class="fa fa-info-circle me-1"></i> Pilih varian terlebih dahulu
        </p>
    @endif
</div>
