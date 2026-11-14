<div>
    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($this->carts->count() > 0)
        <div class="row g-4">
            {{-- Cart Items --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                        <h5 class="mb-0 fw-bold" style="color: #0f172a;">
                            <i class="fa fa-shopping-cart me-2" style="color: #2563eb;"></i>Keranjang Belanja
                            <span class="badge bg-primary ms-2">{{ $this->cartCount }} item</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($this->carts as $item)
                            <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}" wire:key="cart-{{ $item->id_cart }}">
                                {{-- Product Image --}}
                                <div class="flex-shrink-0 me-3" style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; overflow: hidden;">
                                    @if($item->helm)
                                        <img src="{{ $item->helm->gambar_url }}" alt="{{ $item->helm->nama_helm }}" class="w-100 h-100" style="object-fit: contain; padding: 0.5rem;">
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="flex-grow-1 me-3">
                                    <h6 class="fw-bold mb-1" style="color: #0f172a;">
                                        {{ $item->helm->nama_helm ?? 'Produk tidak ditemukan' }}
                                    </h6>
                                    @if($item->varian)
                                        <span class="badge bg-light text-dark border mb-1">
                                            {{ $item->varian->warna }} &bull; {{ $item->varian->ukuran }}
                                        </span>
                                    @endif
                                    <div class="fw-semibold" style="color: #2563eb;">
                                        {{ format_rupiah($item->helm->harga_jual ?? 0) }}
                                    </div>
                                </div>

                                {{-- Quantity Control --}}
                                <div class="d-flex align-items-center me-3">
                                    <button wire:click="updateQty({{ $item->id_cart }}, {{ $item->qty - 1 }})" class="btn btn-outline-secondary btn-sm" {{ $item->qty <= 1 ? 'disabled' : '' }} style="border-radius: 8px 0 0 8px;">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <input type="number" value="{{ $item->qty }}" wire:change="updateQty({{ $item->id_cart }}, $event.target.value)" class="form-control form-control-sm text-center border-start-0 border-end-0" style="width: 50px; border-radius: 0;" min="1">
                                    <button wire:click="updateQty({{ $item->id_cart }}, {{ $item->qty + 1 }})" class="btn btn-outline-secondary btn-sm" style="border-radius: 0 8px 8px 0;">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>

                                {{-- Subtotal --}}
                                <div class="text-end me-3 d-none d-md-block" style="min-width: 110px;">
                                    <small class="text-muted d-block">Subtotal</small>
                                    <span class="fw-bold" style="color: #0f172a;">
                                        {{ format_rupiah(($item->helm->harga_jual ?? 0) * $item->qty) }}
                                    </span>
                                </div>

                                {{-- Remove Button --}}
                                <button wire:click="removeFromCart({{ $item->id_cart }})" wire:confirm="Hapus produk ini dari keranjang?" class="btn btn-outline-danger btn-sm" style="border-radius: 8px;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Cart Summary --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; position: sticky; top: 1rem;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: #0f172a;">Ringkasan Belanja</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item</span>
                            <span class="fw-semibold">{{ $this->cartCount }} produk</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold fs-6" style="color: #0f172a;">Total Harga</span>
                            <span class="fw-bold fs-5" style="color: #2563eb;">{{ format_rupiah($this->cartTotal) }}</span>
                        </div>

                        <div class="alert alert-info border-0 small mb-3" style="border-radius: 8px; background-color: #eff6ff; color: #1e40af;">
                            <i class="fa fa-store me-1"></i>
                            Pengambilan di toko — gratis ongkir!
                        </div>

                        <a href="{{ route('checkout') }}" class="btn w-100 text-white fw-bold py-2" style="background-color: #2563eb; border-radius: 8px; font-size: 1rem;">
                            <i class="fa fa-arrow-right me-1"></i> Lanjut Checkout
                        </a>
                        <a href="{{ route('katalog') }}" class="btn btn-outline-secondary w-100 mt-2" style="border-radius: 8px;">
                            <i class="fa fa-arrow-left me-1"></i> Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart State --}}
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fa fa-cart-shopping fa-4x text-muted" style="opacity: 0.3;"></i>
            </div>
            <h4 class="fw-bold" style="color: #0f172a;">Keranjang Belanja Kosong</h4>
            <p class="text-muted mb-4">Belum ada produk di keranjang Anda. Yuk mulai belanja!</p>
            <a href="{{ route('katalog') }}" class="btn text-white fw-semibold px-4 py-2" style="background-color: #2563eb; border-radius: 8px;">
                <i class="fa fa-helmet-safety me-1"></i> Mulai Belanja
            </a>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="removeFromCart, updateQty" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.7); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Memproses...</span>
        </div>
    </div>
</div>
