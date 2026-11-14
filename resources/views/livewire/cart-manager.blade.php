<div>
    {{-- Toast Notification --}}
    @if(session()->has('success'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-2xs animate-fade-in">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session()->has('warning'))
        <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-center gap-2 shadow-2xs animate-fade-in">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2 shadow-2xs animate-fade-in">
            <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($this->carts->count() > 0)
        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <h1 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                <span>Keranjang</span>
                <span class="text-xs font-semibold text-slate-400">({{ $this->carts->count() }})</span>
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6 items-start">
            
            {{-- Column 1: Items List (Left 8 Cols) --}}
            <div class="lg:col-span-8 space-y-3">
                
                {{-- Header Checkbox Strip --}}
                <div class="bg-white rounded-xl border border-slate-200 px-3.5 py-2.5 flex items-center justify-between shadow-2xs">
                    <label class="flex items-center gap-2.5 cursor-pointer text-xs font-bold text-slate-800 select-none">
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
                                class="text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer">
                            Hapus
                        </button>
                    @endif
                </div>

                {{-- Cart Items Cards --}}
                <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 shadow-2xs overflow-hidden">
                    @foreach($this->carts as $item)
                        @php
                            $isSelected = in_array((int) $item->id_cart, $selectedItems);
                            $maxStock = $item->varian ? $item->varian->stok : 0;
                            $isOutOfStock = ($maxStock <= 0);
                        @endphp
                        <div class="p-3 sm:p-4 flex items-start gap-2.5 sm:gap-3.5 hover:bg-slate-50/50 transition-colors" wire:key="cart-item-{{ $item->id_cart }}">
                            
                            {{-- Checkbox --}}
                            <div class="pt-2 sm:pt-2.5 shrink-0">
                                <input type="checkbox" 
                                       wire:click="toggleSelectItem({{ $item->id_cart }})"
                                       {{ $isSelected ? 'checked' : '' }}
                                       @if($isOutOfStock) disabled @endif
                                       class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 disabled:opacity-40 cursor-pointer">
                            </div>

                            {{-- Product Thumbnail --}}
                            <a href="{{ route('katalog.detail', $item->id_helm) }}" class="w-18 h-18 sm:w-20 sm:h-20 rounded-xl bg-slate-50 border border-slate-200 overflow-hidden shrink-0 block">
                                <img src="{{ $item->helm->gambar_url }}" 
                                     alt="{{ $item->helm->nama_helm }}" 
                                     class="w-full h-full object-cover">
                            </a>

                            {{-- Product Info & Stepper --}}
                            <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch gap-1.5">
                                
                                {{-- Title & Delete --}}
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        @if($item->helm->merk)
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                {{ $item->helm->merk->nama_merk }}
                                            </p>
                                        @endif
                                        <a href="{{ route('katalog.detail', $item->id_helm) }}" class="font-bold text-xs sm:text-sm text-slate-900 hover:text-blue-600 line-clamp-2 leading-snug transition-colors">
                                            {{ $item->helm->nama_helm }}
                                        </a>
                                    </div>
                                    <button type="button" 
                                            wire:click="removeFromCart({{ $item->id_cart }})" 
                                            wire:confirm="Hapus {{ $item->helm->nama_helm }} dari keranjang?"
                                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer shrink-0" 
                                            title="Hapus helm">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </div>

                                {{-- Variant Chip --}}
                                @if($item->varian)
                                    <div>
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-medium border border-slate-200/70">
                                            {{ $item->varian->warna }} &bull; Size {{ $item->varian->ukuran }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Price & Quantity Stepper --}}
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <div>
                                        <p class="text-xs sm:text-sm font-black text-blue-600">
                                            {{ format_rupiah($item->helm->harga_jual) }}
                                        </p>
                                        @if($isOutOfStock)
                                            <span class="text-[10px] font-bold text-rose-500">Stok Habis</span>
                                        @elseif($maxStock <= 3)
                                            <span class="text-[10px] font-medium text-amber-600">Sisa {{ $maxStock }}</span>
                                        @endif
                                    </div>

                                    {{-- Stepper --}}
                                    <div class="flex items-center border border-slate-200 rounded-lg bg-slate-50 p-0.5">
                                        <button type="button" 
                                                wire:click="decrementQty({{ $item->id_cart }})" 
                                                {{ $item->qty <= 1 ? 'disabled' : '' }}
                                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-600 hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer">
                                            <i class="fa-solid fa-minus text-[9px]"></i>
                                        </button>
                                        <span class="w-8 text-center text-xs font-bold text-slate-900">{{ $item->qty }}</span>
                                        <button type="button" 
                                                wire:click="incrementQty({{ $item->id_cart }})" 
                                                {{ $item->qty >= $maxStock ? 'disabled' : '' }}
                                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-600 hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer">
                                            <i class="fa-solid fa-plus text-[9px]"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>

            </div>

            {{-- Column 2: Desktop Sticky Summary Card (Right 4 Cols) --}}
            <div class="hidden lg:block lg:col-span-4 sticky top-24 space-y-3">
                <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3.5 shadow-2xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                        Ringkasan Belanja
                    </h2>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Total Harga ({{ $this->selectedCount }} barang)</span>
                            <span class="font-bold text-slate-900">{{ format_rupiah($this->selectedTotal) }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800">Total Tagihan</span>
                        <span class="text-base font-black text-blue-600">
                            {{ format_rupiah($this->selectedTotal) }}
                        </span>
                    </div>

                    {{-- Desktop Checkout Button --}}
                    @if($this->selectedCount > 0)
                        <a href="{{ route('pelanggan.checkout') }}" 
                           class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 active:scale-98 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span>Beli ({{ $this->selectedCount }})</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @else
                        <button type="button" 
                                disabled
                                class="w-full py-3 rounded-xl bg-slate-200 text-slate-400 font-bold text-xs cursor-not-allowed flex items-center justify-center gap-2">
                            <span>Beli (0)</span>
                        </button>
                    @endif

                    {{-- Store Pickup Subtitle --}}
                    <p class="text-[10px] text-slate-400 text-center flex items-center justify-center gap-1.5 pt-1">
                        <i class="fa-solid fa-store text-slate-400"></i>
                        <span>Ambil di Gerai Jl. RE Martadinata No.49 Kuningan</span>
                    </p>
                </div>
            </div>

        </div>

        {{-- Mobile Sticky Action Bottom Bar (at bottom-0) --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 px-3.5 py-2.5 flex items-center justify-between shadow-lg">
            {{-- Checkbox Select All Mobile --}}
            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-800 select-none">
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
                    <span class="text-sm font-black text-blue-600 leading-tight">
                        {{ format_rupiah($this->selectedTotal) }}
                    </span>
                </div>

                @if($this->selectedCount > 0)
                    <a href="{{ route('pelanggan.checkout') }}" 
                       class="h-10 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 active:scale-98 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Checkout</span>
                        <span class="text-[11px]">({{ $this->selectedCount }})</span>
                    </a>
                @else
                    <button type="button" 
                            disabled 
                            class="h-10 px-5 rounded-xl bg-slate-200 text-slate-400 font-bold text-xs cursor-not-allowed">
                        <span>Checkout (0)</span>
                    </button>
                @endif
            </div>
        </div>

    @else
        {{-- Empty Cart State (Clean & Minimalist) --}}
        <div class="bg-white rounded-xl p-8 sm:p-12 text-center border border-slate-200 shadow-2xs max-w-md mx-auto my-6 space-y-4">
            <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-2xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="space-y-1">
                <h2 class="text-base font-bold text-slate-900">Keranjang belanja masih kosong</h2>
                <p class="text-xs text-slate-500 max-w-xs mx-auto leading-relaxed">
                    Yuk pilih helm impianmu dan amankan stoknya untuk diambil di gerai.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('katalog') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all active:scale-95">
                    <i class="fa-solid fa-compass text-xs"></i>
                    <span>Jelajahi Katalog Helm</span>
                </a>
            </div>
        </div>
    @endif
</div>
