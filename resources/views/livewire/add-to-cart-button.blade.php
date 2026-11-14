<div x-data="{ variantSheetOpen: false }"
     @open-variant-sheet.window="variantSheetOpen = true"
     @cart-added-success.window="variantSheetOpen = false">
    
    {{-- Toast Notification Feedback --}}
    @if(session()->has('cart-success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2 mb-4 animate-fade-in shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('cart-success') }}</span>
            </div>
            <a href="{{ route('pelanggan.cart') }}" class="text-[11px] text-blue-600 hover:underline font-bold shrink-0">
                Lihat Keranjang &rarr;
            </a>
        </div>
    @endif
    @if(session()->has('cart-error'))
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2 mb-4 animate-fade-in shadow-2xs">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
            <span>{{ session('cart-error') }}</span>
        </div>
    @endif

    {{-- Desktop Action Buttons Panel (Hidden on Mobile) --}}
    <div class="hidden sm:flex items-center gap-3">
        {{-- Chat Button Desktop --}}
        <button type="button"
                @click="$dispatch('open-store-chat')"
                title="Tanya Staf Toko via Chat"
                class="h-11 px-5 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-2xs cursor-pointer">
            <i class="fa-regular fa-comment-dots text-blue-600 text-base"></i>
            <span>Chat</span>
        </button>

        {{-- Primary Add To Cart Button Desktop (No + sign) --}}
        <button type="button"
                @click="variantSheetOpen = true"
                class="flex-1 h-11 inline-flex items-center justify-center gap-2 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 active:scale-98 transition-all cursor-pointer">
            <i class="fa-solid fa-bag-shopping text-sm"></i>
            <span>Masukkan Keranjang</span>
        </button>
    </div>

    {{-- Mobile Sticky Action Bottom Bar (Only Chat & Masukkan Keranjang) --}}
    <div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 px-4 py-2.5 flex items-center gap-3 shadow-lg">
        {{-- Chat Mobile --}}
        <button type="button"
                @click="$dispatch('open-store-chat')"
                class="w-14 h-11 flex flex-col items-center justify-center rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-blue-600 active:scale-95 transition-all cursor-pointer shrink-0">
            <i class="fa-regular fa-comment-dots text-base leading-none"></i>
            <span class="text-[10px] font-bold mt-1 leading-none">Chat</span>
        </button>

        {{-- CTA Button Mobile (No + sign) --}}
        <button type="button"
                @click="variantSheetOpen = true"
                class="flex-1 h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-md shadow-blue-600/20 active:scale-98 transition-all cursor-pointer">
            <i class="fa-solid fa-bag-shopping text-xs"></i>
            <span>Masukkan Keranjang</span>
        </button>
    </div>

    {{-- Variant Selection Bottom Sheet & Quantity Modal (Shopee/Blibli Style) --}}
    <div x-show="variantSheetOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center"
         role="dialog" aria-modal="true">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"
             @click="variantSheetOpen = false"></div>

        {{-- Panel Container --}}
        <div class="relative w-full sm:max-w-md bg-white rounded-t-2xl sm:rounded-2xl z-10 shadow-2xl overflow-hidden flex flex-col max-h-[88vh] animate-slide-up sm:animate-scale-in">
            
            {{-- Sheet Header: Product Thumbnail & Price Preview --}}
            <div class="p-4 border-b border-slate-100 flex items-start gap-3 bg-white relative">
                <div class="w-16 h-16 rounded-xl border border-slate-200 overflow-hidden bg-slate-50 shrink-0">
                    <img src="{{ $helm->gambar_url }}" alt="{{ $helm->nama_helm }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 overflow-hidden pr-6">
                    <p class="text-lg font-black text-blue-600 leading-tight">
                        {{ format_rupiah($helm->harga_jual) }}
                    </p>
                    <div class="mt-1 text-xs text-slate-500">
                        @if($selectedVariantObj)
                            <span>Stok: {{ $selectedVariantObj['stok'] }}</span>
                        @else
                            <span>Pilih varian</span>
                        @endif
                    </div>
                    @if($selectedVariantObj)
                        <p class="text-xs text-slate-700 font-medium truncate mt-0.5">
                            {{ $selectedVariantObj['warna'] }}, {{ $selectedVariantObj['ukuran'] }}
                        </p>
                    @endif
                </div>
                <button type="button" 
                        @click="variantSheetOpen = false" 
                        class="absolute top-3.5 right-3.5 w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            {{-- Sheet Body (Scrollable) --}}
            <div class="p-4 overflow-y-auto space-y-5 flex-1">
                
                {{-- Variant Chips --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-800">
                        Pilih Varian
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @forelse($availableVariants as $v)
                            @php
                                $isSelected = ($selectedVarian == $v['id_varian']);
                                $isOut = ($v['stok'] <= 0);
                            @endphp
                            <button type="button"
                                    wire:click="selectVarian({{ $v['id_varian'] }})"
                                    @if($isOut) disabled @endif
                                    class="px-3 py-2 rounded-xl text-xs font-medium border transition-all flex items-center gap-1.5 cursor-pointer
                                    {{ $isSelected 
                                        ? 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-600/30' 
                                        : ($isOut 
                                            ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-60' 
                                            : 'bg-white text-slate-700 border-slate-200 hover:border-blue-500 hover:text-blue-600') }}">
                                <span>{{ $v['warna'] }} - {{ $v['ukuran'] }}</span>
                                @if($isOut)
                                    <span class="text-[9px] text-rose-500 font-semibold">(Habis)</span>
                                @endif
                            </button>
                        @empty
                            <p class="text-xs text-slate-400">Varian belum tersedia.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Quantity Stepper (- / +) --}}
                <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800">Jumlah</span>

                    <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 p-1">
                        <button type="button" 
                                wire:click="decrementQty" 
                                {{ $qty <= 1 ? 'disabled' : '' }}
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer">
                            <i class="fa-solid fa-minus text-[10px]"></i>
                        </button>
                        <span class="w-10 text-center text-xs font-bold text-slate-900">{{ $qty }}</span>
                        <button type="button" 
                                wire:click="incrementQty" 
                                {{ !$selectedVarian || $qty >= ($selectedVariantObj['stok'] ?? 0) ? 'disabled' : '' }}
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Sheet Footer: Confirmation Button (No + sign) --}}
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                <button type="button"
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        {{ !$selectedVarian ? 'disabled' : '' }}
                        class="w-full h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed active:scale-98 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping text-sm"></i>
                        <span>Masukkan Keranjang</span>
                    </span>
                    <span wire:loading wire:target="addToCart" class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                        <span>Memproses...</span>
                    </span>
                </button>
            </div>

        </div>
    </div>

</div>
