<div>
    {{-- Toast Alerts --}}
    @if(session()->has('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($this->carts->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Cart Items Column --}}
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                        <span>Daftar Helm</span>
                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-extrabold">
                            {{ $this->cartCount }} item
                        </span>
                    </h2>
                    <span class="text-xs text-slate-400">Harga Satuan</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($this->carts as $item)
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors" wire:key="cart-{{ $item->id_cart }}">
                            
                            {{-- Product Details with Image --}}
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-20 h-20 rounded-2xl bg-slate-100 border border-slate-200/60 overflow-hidden flex-shrink-0 flex items-center justify-center p-1">
                                    @if($item->helm)
                                        <img src="{{ $item->helm->gambar_url }}" 
                                             alt="{{ $item->helm->nama_helm }}" 
                                             class="w-full h-full object-cover rounded-xl">
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-bold text-sm text-slate-900 line-clamp-1">
                                        {{ $item->helm->nama_helm ?? 'Helm' }}
                                    </h3>
                                    @if($item->varian)
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold">
                                            <span>Warna: {{ $item->varian->warna }}</span>
                                            <span>&bull;</span>
                                            <span>Ukuran: {{ $item->varian->ukuran }}</span>
                                        </div>
                                    @endif
                                    <p class="text-xs font-bold text-blue-600 sm:hidden">
                                        {{ format_rupiah($item->helm->harga_jual ?? 0) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Price & Quantity & Actions --}}
                            <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                
                                {{-- Quantity Control --}}
                                <div class="flex items-center border border-slate-200 rounded-xl bg-white p-0.5 shadow-sm">
                                    <button wire:click="updateQty({{ $item->id_cart }}, {{ $item->qty - 1 }})" 
                                            {{ $item->qty <= 1 ? 'disabled' : '' }}
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-xs">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <span class="w-9 text-center text-xs font-bold text-slate-800">
                                        {{ $item->qty }}
                                    </span>
                                    <button wire:click="updateQty({{ $item->id_cart }}, {{ $item->qty + 1 }})" 
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors text-xs">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>

                                {{-- Subtotal --}}
                                <div class="text-right min-w-[100px]">
                                    <p class="text-[10px] text-slate-400 font-medium hidden sm:block">Subtotal</p>
                                    <p class="text-sm font-extrabold text-slate-900">
                                        {{ format_rupiah(($item->helm->harga_jual ?? 0) * $item->qty) }}
                                    </p>
                                </div>

                                {{-- Remove Item --}}
                                <button wire:click="removeFromCart({{ $item->id_cart }})" 
                                        wire:confirm="Yakin ingin menghapus helm ini dari keranjang?"
                                        class="w-8 h-8 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors text-xs"
                                        title="Hapus dari Keranjang">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Checkout Summary (Right Column) --}}
            <div class="lg:col-span-4 sticky top-28 space-y-4">
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">Ringkasan Pesanan</h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Total Jumlah Barang</span>
                            <span class="font-bold text-slate-800">{{ $this->cartCount }} unit helm</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Metode Pemenuhan</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Ambil di Toko Fisik</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Biaya Pengiriman</span>
                            <span class="font-bold text-emerald-600">GRATIS (Rp 0)</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                        <span class="text-xs font-bold text-slate-800">Total Pembayaran</span>
                        <span class="text-xl font-black text-blue-600">
                            {{ format_rupiah($this->cartTotal) }}
                        </span>
                    </div>

                    {{-- Store Pickup Notice --}}
                    <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/60 text-[11px] text-blue-900 space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-store text-blue-600"></i>
                            <span>Gerai Bayeng Putra Helm Kuningan</span>
                        </p>
                        <p class="text-blue-700 leading-relaxed">
                            Setelah checkout, Anda akan langsung mendapatkan e-Tiket dengan kode unik untuk serah terima barang.
                        </p>
                    </div>

                    {{-- CTA Buttons --}}
                    <div class="space-y-2 pt-1">
                        <a href="{{ route('pelanggan.checkout') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-600/20 active:scale-98 transition-all">
                            <span>Lanjut ke Checkout Ambil di Toko</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                        <a href="{{ route('katalog') }}" 
                           class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Helm Lain</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    @else
        {{-- Empty Cart State --}}
        <div class="bg-white rounded-3xl p-12 sm:p-16 text-center border border-slate-200/80 shadow-sm max-w-lg mx-auto space-y-4">
            <div class="w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-3xl">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Keranjang Masih Kosong</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                    Anda belum memilih helm apapun. Temukan helm idaman Anda dari berbagai brand ternama di katalog kami.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('katalog') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-600/20 transition-all active:scale-95">
                    <i class="fa-solid fa-helmet-safety"></i>
                    <span>Mulai Belanja Helm</span>
                </a>
            </div>
        </div>
    @endif
</div>
