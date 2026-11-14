<div>
    {{-- Toast Notification --}}
    @if(session()->has('success'))
        <div class="mb-3 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-2xs animate-fade-in">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session()->has('warning'))
        <div class="mb-3 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-center gap-2 shadow-2xs animate-fade-in">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-3 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-2xs animate-fade-in">
            <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($this->carts->count() > 0)
        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('katalog') }}" 
                   onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }"
                   class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors shadow-2xs" 
                   title="Kembali">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                </a>
                <h1 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-1.5 leading-tight">
                    <span>Keranjang</span>
                    <span class="text-xs font-medium text-slate-400">({{ $this->carts->count() }})</span>
                </h1>
            </div>
            <a href="{{ route('katalog') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                <span>Tambah Helm</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-5 items-start">
            
            {{-- Column 1: Items List (Left 8 Cols) --}}
            <div class="lg:col-span-8 space-y-3">
                
                {{-- Single Flat Clean Canvas --}}
                <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 overflow-hidden shadow-2xs">
                    
                    {{-- Header Checkbox Row (Pilih Semua & Hapus) --}}
                    <div class="px-4 py-3 flex items-center justify-between text-xs bg-white">
                        <label class="flex items-center gap-3 cursor-pointer text-slate-800 font-medium select-none">
                            <input type="checkbox" 
                                   wire:click="toggleSelectAll" 
                                   {{ $this->isAllSelected ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer">
                            <span>Pilih Semua ({{ $this->carts->count() }})</span>
                        </label>

                        @if(count($selectedItems) > 0)
                            <button type="button" 
                                    wire:click="removeSelected" 
                                    wire:confirm="Hapus {{ count($selectedItems) }} helm yang dipilih?"
                                    class="text-slate-500 hover:text-rose-600 font-medium transition-colors cursor-pointer">
                                Hapus
                            </button>
                        @endif
                    </div>

                    {{-- Cart Items --}}
                    @foreach($this->carts as $item)
                        @php
                            $isSelected = in_array((int) $item->id_cart, $selectedItems);
                            $maxStock = $item->varian ? $item->varian->stok : 0;
                            $isOutOfStock = ($maxStock <= 0);
                        @endphp
                        <div class="p-3.5 sm:p-4 flex items-start gap-3 sm:gap-3.5 hover:bg-slate-50/50 transition-colors" wire:key="cart-item-{{ $item->id_cart }}">
                            
                            {{-- Checkbox --}}
                            <div class="pt-1 shrink-0">
                                <input type="checkbox" 
                                       wire:click="toggleSelectItem({{ $item->id_cart }})"
                                       {{ $isSelected ? 'checked' : '' }}
                                       @if($isOutOfStock) disabled @endif
                                       class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 disabled:opacity-30 cursor-pointer">
                            </div>

                            {{-- Product Thumbnail --}}
                            <a href="{{ route('katalog.detail', $item->id_helm) }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg bg-slate-50 border border-slate-200/80 overflow-hidden shrink-0 block">
                                <img src="{{ $item->helm->gambar_url }}" 
                                     alt="{{ $item->helm->nama_helm }}" 
                                     class="w-full h-full object-cover">
                            </a>

                            {{-- Product Info & Stepper --}}
                            <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch gap-1">
                                
                                <div>
                                    <a href="{{ route('katalog.detail', $item->id_helm) }}" class="text-xs sm:text-sm font-medium text-slate-900 hover:text-blue-600 line-clamp-2 leading-snug transition-colors">
                                        {{ $item->helm->nama_helm }}
                                    </a>
                                    @if($item->varian)
                                        <p class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $item->varian->warna ?? '' }}{{ ($item->varian->warna && $item->varian->ukuran) ? ', ' : '' }}{{ $item->varian->ukuran ?? '' }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Price & Quantity Stepper --}}
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900">
                                            {{ format_rupiah($item->helm->harga_jual) }}
                                        </span>
                                        @if($isOutOfStock)
                                            <span class="text-[10px] text-rose-500 font-medium ml-1.5">Stok habis</span>
                                        @elseif($maxStock <= 3)
                                            <span class="text-[10px] text-amber-600 font-medium ml-1.5">Sisa {{ $maxStock }}</span>
                                        @endif
                                    </div>

                                    {{-- Trash & Stepper --}}
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                wire:click="removeFromCart({{ $item->id_cart }})" 
                                                wire:confirm="Hapus {{ $item->helm->nama_helm }} dari keranjang?"
                                                class="text-slate-400 hover:text-rose-600 p-1 transition-colors cursor-pointer" 
                                                title="Hapus">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>

                                        <div class="flex items-center border border-slate-200 rounded-md bg-white">
                                            <button type="button" 
                                                    wire:click="decrementQty({{ $item->id_cart }})" 
                                                    {{ $item->qty <= 1 ? 'disabled' : '' }}
                                                    class="w-6 h-6 flex items-center justify-center text-slate-600 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer">
                                                <i class="fa-solid fa-minus text-[8px]"></i>
                                            </button>
                                            <span class="w-7 text-center text-xs font-semibold text-slate-900">{{ $item->qty }}</span>
                                            <button type="button" 
                                                    wire:click="incrementQty({{ $item->id_cart }})" 
                                                    {{ $item->qty >= $maxStock ? 'disabled' : '' }}
                                                    class="w-6 h-6 flex items-center justify-center text-slate-600 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer">
                                                <i class="fa-solid fa-plus text-[8px]"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>

            </div>

            {{-- Column 2: Desktop Sticky Summary Card (Right 4 Cols) --}}
            <div class="hidden lg:block lg:col-span-4 sticky top-24">
                <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3.5 shadow-2xs">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Ringkasan Belanja
                    </h2>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Total Harga ({{ $this->selectedCount }} helm)</span>
                            <span class="font-medium text-slate-900">{{ format_rupiah($this->selectedTotal) }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-800">Total Tagihan</span>
                        <span class="text-base font-bold text-blue-600">
                            {{ format_rupiah($this->selectedTotal) }}
                        </span>
                    </div>

                    {{-- Desktop Checkout Button --}}
                    @if($this->selectedCount > 0)
                        <a href="{{ route('pelanggan.checkout') }}" 
                           class="w-full py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm active:scale-98 transition-all flex items-center justify-center cursor-pointer">
                            Checkout ({{ $this->selectedCount }})
                        </a>
                    @else
                        <button type="button" 
                                disabled
                                class="w-full py-2.5 rounded-lg bg-slate-100 text-slate-400 font-semibold text-xs cursor-not-allowed flex items-center justify-center">
                            Checkout (0)
                        </button>
                    @endif
                </div>
            </div>

        </div>

        {{-- Mobile Sticky Action Bottom Bar (at bottom-0) --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 px-4 py-2.5 flex items-center justify-between shadow-lg">
            {{-- Checkbox Select All Mobile --}}
            <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-800 select-none">
                <input type="checkbox" 
                       wire:click="toggleSelectAll" 
                       {{ $this->isAllSelected ? 'checked' : '' }}
                       class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer">
                <span>Semua</span>
            </label>

            {{-- Total & Checkout CTA Button --}}
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 block leading-tight">Total</span>
                    <span class="text-xs sm:text-sm font-bold text-blue-600 leading-tight">
                        {{ format_rupiah($this->selectedTotal) }}
                    </span>
                </div>

                @if($this->selectedCount > 0)
                    <a href="{{ route('pelanggan.checkout') }}" 
                       class="h-9 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm active:scale-98 transition-all flex items-center justify-center cursor-pointer">
                        Checkout ({{ $this->selectedCount }})
                    </a>
                @else
                    <button type="button" 
                            disabled 
                            class="h-9 px-4 rounded-lg bg-slate-100 text-slate-400 font-semibold text-xs cursor-not-allowed">
                        Checkout (0)
                    </button>
                @endif
            </div>
        </div>

    @else
        {{-- Empty State Header --}}
        <div class="flex items-center gap-2 mb-3">
            <a href="{{ route('katalog') }}" 
               onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }"
               class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors shadow-2xs" 
               title="Kembali">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
            </a>
            <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Keranjang</h1>
        </div>

        {{-- Empty Cart State (Clean & Quiet) --}}
        <div class="bg-white rounded-xl p-8 sm:p-12 text-center border border-slate-200 shadow-2xs max-w-sm mx-auto my-8 space-y-3">
            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="space-y-1">
                <h2 class="text-sm font-semibold text-slate-900">Keranjang belanja masih kosong</h2>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pilih helm favoritmu dan pesan sekarang.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('katalog') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs shadow-sm transition-all active:scale-95">
                    <span>Mulai Belanja</span>
                </a>
            </div>
        </div>
    @endif
</div>
