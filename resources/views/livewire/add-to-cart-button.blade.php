<div>
    {{-- Toast Notification Feedback --}}
    @if(session()->has('cart-success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 mb-4 animate-fade-in">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('cart-success') }}</span>
        </div>
    @endif
    @if(session()->has('cart-error'))
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2 mb-4 animate-fade-in">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
            <span>{{ session('cart-error') }}</span>
        </div>
    @endif

    {{-- Variant Selector: Pill Chips E-Commerce --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-800">
                Pilih Varian <span class="text-rose-500">*</span>
            </label>
            @if($selectedVarian)
                @php
                    $selectedObj = collect($availableVariants)->firstWhere('id_varian', $selectedVarian);
                @endphp
                @if($selectedObj)
                    <span class="text-[11px] font-semibold text-slate-500">
                        Sisa stok: <strong class="{{ $selectedObj['stok'] > 3 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $selectedObj['stok'] }}</strong>
                    </span>
                @endif
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            @forelse($availableVariants as $varian)
                @php
                    $isSelected = ($selectedVarian == $varian['id_varian']);
                    $isOutOfStock = ($varian['stok'] <= 0);
                @endphp
                <button type="button"
                        wire:click="selectVarian({{ $varian['id_varian'] }})"
                        @if($isOutOfStock) disabled @endif
                        class="px-3 py-2 rounded-xl text-xs font-bold transition-all border flex items-center gap-2 
                        {{ $isSelected 
                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-600/30 ring-2 ring-blue-600/20' 
                            : ($isOutOfStock 
                                ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-60' 
                                : 'bg-white text-slate-700 border-slate-200 hover:border-blue-500 hover:text-blue-600') }}">
                    <span class="w-2 h-2 rounded-full {{ $isSelected ? 'bg-white' : ($isOutOfStock ? 'bg-slate-300' : 'bg-blue-600') }}"></span>
                    <span>{{ $varian['warna'] }} &bull; {{ $varian['ukuran'] }}</span>
                    @if($isOutOfStock)
                        <span class="text-[10px] text-rose-500 font-semibold">(Habis)</span>
                    @endif
                </button>
            @empty
                <p class="text-xs text-slate-400">Tidak ada varian fisik tersedia.</p>
            @endforelse
        </div>
    </div>

    {{-- Quantity & Action Buttons (Desktop Panel) --}}
    <div class="mt-6 pt-5 border-t border-slate-100 hidden sm:flex items-center gap-3">
        
        {{-- Quantity Counter --}}
        <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 p-1">
            <button type="button" 
                    wire:click="decrementQty" 
                    {{ $qty <= 1 ? 'disabled' : '' }}
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                <i class="fa-solid fa-minus text-[10px]"></i>
            </button>
            <span class="w-10 text-center text-xs font-black text-slate-900">{{ $qty }}</span>
            <button type="button" 
                    wire:click="incrementQty" 
                    {{ !$selectedVarian || $qty >= (collect($availableVariants)->firstWhere('id_varian', $selectedVarian)['stok'] ?? 0) ? 'disabled' : '' }}
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                <i class="fa-solid fa-plus text-[10px]"></i>
            </button>
        </div>

        {{-- Add to Cart Button --}}
        <button type="button"
                wire:click="addToCart"
                wire:loading.attr="disabled"
                {{ !$selectedVarian ? 'disabled' : '' }}
                class="flex-1 h-11 inline-flex items-center justify-center gap-2 px-6 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-600/20 disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed active:scale-98 transition-all">
            <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2">
                <i class="fa-solid fa-bag-shopping text-sm"></i>
                <span>+ Keranjang</span>
            </span>
            <span wire:loading wire:target="addToCart" class="flex items-center gap-2">
                <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                <span>Menambahkan...</span>
            </span>
        </button>

        {{-- Chat Admin Desktop Button --}}
        @php
            $waText = urlencode("Halo Admin Bayeng Putra Helm, saya mau tanya ketersediaan helm *{$helm->nama_helm}* (ID: {$helm->id_helm}). Apakah stoknya ready di toko Kuningan?");
        @endphp
        <a href="https://wa.me/6281234567890?text={{ $waText }}" 
           target="_blank" 
           title="Chat Admin Toko"
           class="h-11 px-4 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-2xs">
            <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
            <span>Chat</span>
        </a>
    </div>

    {{-- Sticky Mobile Action Bottom Bar (Tokopedia Style) --}}
    <div class="sm:hidden fixed bottom-16 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-2.5 shadow-lg flex items-center gap-3">
        
        {{-- Chat Button Mobile --}}
        @php
            $waTextMob = urlencode("Halo Admin Bayeng Putra Helm, saya tertarik helm *{$helm->nama_helm}*. Apakah ready di toko?");
        @endphp
        <a href="https://wa.me/6281234567890?text={{ $waTextMob }}" 
           target="_blank" 
           class="w-10 h-10 rounded-xl border border-slate-200 flex flex-col items-center justify-center text-slate-600 bg-white shadow-2xs active:scale-95 shrink-0">
            <i class="fa-brands fa-whatsapp text-emerald-600 text-lg"></i>
        </a>

        {{-- Price preview --}}
        <div class="flex-1 overflow-hidden">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Harga Gerai</p>
            <p class="text-sm font-black text-blue-600 truncate mt-0.5">{{ format_rupiah($helm->harga_jual) }}</p>
        </div>

        {{-- Mobile Add to Cart --}}
        <button type="button"
                wire:click="addToCart"
                wire:loading.attr="disabled"
                {{ !$selectedVarian ? 'disabled' : '' }}
                class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold flex items-center gap-2 disabled:bg-slate-200 disabled:text-slate-400 shadow-md shadow-blue-600/20 active:scale-95 transition-all shrink-0">
            <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-1.5">
                <i class="fa-solid fa-bag-shopping text-xs"></i>
                <span>{{ $selectedVarian ? '+ Keranjang' : 'Pilih Varian' }}</span>
            </span>
            <span wire:loading wire:target="addToCart">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
            </span>
        </button>
    </div>

</div>
