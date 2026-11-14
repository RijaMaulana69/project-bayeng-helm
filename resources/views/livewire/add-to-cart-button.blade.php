<div class="space-y-5">
    {{-- Toast Notification Feedback --}}
    @if(session()->has('cart-success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('cart-success') }}</span>
        </div>
    @endif
    @if(session()->has('cart-error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
            <span>{{ session('cart-error') }}</span>
        </div>
    @endif

    {{-- Variant Selector --}}
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Pilih Varian (Warna & Ukuran) <span class="text-rose-500">*</span>
        </label>
        <div class="flex flex-wrap gap-2.5">
            @forelse($availableVariants as $varian)
                @php
                    $isSelected = ($selectedVarian == $varian['id_varian']);
                    $isOutOfStock = ($varian['stok'] <= 0);
                @endphp
                <button type="button"
                        wire:click="selectVarian({{ $varian['id_varian'] }})"
                        @if($isOutOfStock) disabled @endif
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all border text-left flex items-center gap-2 
                        {{ $isSelected 
                            ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20' 
                            : ($isOutOfStock 
                                ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-60' 
                                : 'bg-white text-slate-800 border-slate-200 hover:border-blue-400 hover:bg-blue-50/50') }}">
                    <span class="w-2 h-2 rounded-full {{ $isSelected ? 'bg-white' : ($isOutOfStock ? 'bg-slate-300' : 'bg-blue-500') }}"></span>
                    <span>{{ $varian['warna'] }} &bull; Ukuran {{ $varian['ukuran'] }}</span>
                    @if($isOutOfStock)
                        <span class="text-[10px] text-rose-500 font-semibold">(Habis)</span>
                    @endif
                </button>
            @empty
                <p class="text-xs text-slate-400">Tidak ada varian fisik tersedia.</p>
            @endforelse
        </div>
    </div>

    {{-- Stock Status for Selected Variant --}}
    @if($selectedVarian)
        @php
            $selectedStock = collect($availableVariants)->firstWhere('id_varian', $selectedVarian);
        @endphp
        @if($selectedStock)
            <div class="flex items-center gap-2 text-xs font-medium">
                <span class="w-2 h-2 rounded-full {{ $selectedStock['stok'] > 3 ? 'bg-emerald-500' : 'bg-amber-500' }} animate-pulse"></span>
                <span class="text-slate-600">Sisa stok fisik di toko:</span>
                <span class="font-bold {{ $selectedStock['stok'] > 3 ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $selectedStock['stok'] }} unit
                </span>
            </div>
        @endif
    @endif

    {{-- Quantity & Add to Cart Action --}}
    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
        
        {{-- Quantity Counter --}}
        <div class="flex items-center border border-slate-200 rounded-2xl bg-white p-1 self-start sm:self-auto shadow-sm">
            <button type="button" 
                    wire:click="decrementQty" 
                    {{ $qty <= 1 ? 'disabled' : '' }}
                    class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                <i class="fa-solid fa-minus text-xs"></i>
            </button>
            <input type="number" 
                   wire:model="qty" 
                   readonly 
                   class="w-12 text-center text-sm font-extrabold text-slate-900 border-0 focus:ring-0 bg-transparent">
            <button type="button" 
                    wire:click="incrementQty" 
                    {{ !$selectedVarian || $qty >= (collect($availableVariants)->firstWhere('id_varian', $selectedVarian)['stok'] ?? 0) ? 'disabled' : '' }}
                    class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                <i class="fa-solid fa-plus text-xs"></i>
            </button>
        </div>

        {{-- Add to Cart Button --}}
        <button type="button"
                wire:click="addToCart"
                wire:loading.attr="disabled"
                {{ !$selectedVarian ? 'disabled' : '' }}
                class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-lg shadow-blue-600/25 disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed active:scale-98 transition-all">
            <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2">
                <i class="fa-solid fa-bag-shopping"></i>
                <span>Masukkan ke Keranjang</span>
            </span>
            <span wire:loading wire:target="addToCart" class="flex items-center gap-2">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
                <span>Menambahkan...</span>
            </span>
        </button>
    </div>

    {{-- Value Props / Guarantee --}}
    <div class="pt-4 border-t border-slate-200/80 grid grid-cols-2 gap-3 text-[11px] text-slate-500">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-blue-600"></i>
            <span>Bisa Dicoba (Fitting) di Toko</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
            <span>Garansi 100% Original & SNI</span>
        </div>
    </div>
</div>
