<div>
    {{-- Search & Filter Toolbar (Compact E-Commerce Style) --}}
    <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs mb-6 space-y-3">
        <div class="grid grid-cols-2 md:grid-cols-12 gap-2.5 items-center">
            
            {{-- Search Bar --}}
            <div class="col-span-2 md:col-span-5">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari merk atau tipe helm (KYT, INK, NHK)..." 
                           class="w-full pl-8 pr-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 transition-all">
                </div>
            </div>

            {{-- Merk Dropdown --}}
            <div class="col-span-1 md:col-span-2">
                <select wire:model.live="merk_filter" 
                        class="w-full px-2.5 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600 transition-all cursor-pointer">
                    <option value="">Semua Merk</option>
                    @foreach($merks as $merk)
                        <option value="{{ $merk->id_merk }}">{{ $merk->nama_merk }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Kategori Dropdown --}}
            <div class="col-span-1 md:col-span-2">
                <select wire:model.live="kategori_filter" 
                        class="w-full px-2.5 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600 transition-all cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort By --}}
            <div class="col-span-1 md:col-span-2">
                <select wire:model.live="sort" 
                        class="w-full px-2.5 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600 transition-all cursor-pointer">
                    <option value="terbaru">Terbaru</option>
                    <option value="termurah">Termurah</option>
                    <option value="termahal">Termahal</option>
                    <option value="nama">Nama (A-Z)</option>
                </select>
            </div>

            {{-- Reset Button --}}
            <div class="col-span-1 md:col-span-1">
                <button wire:click="resetFilters" 
                        title="Reset Filter"
                        class="w-full h-9 inline-flex items-center justify-center gap-1.5 px-2 py-2 rounded-lg border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all active:scale-95">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span class="md:hidden">Reset</span>
                </button>
            </div>

        </div>

        {{-- Filter Tags & Price Range (Thin Row) --}}
        <div class="pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-[11px]">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider">Harga (Rp):</span>
                <input type="number" 
                       wire:model.live.debounce.500ms="price_min" 
                       placeholder="Min" 
                       class="w-20 px-2 py-1 rounded border border-slate-200 bg-slate-50 text-xs font-medium focus:bg-white outline-none">
                <span class="text-slate-300">-</span>
                <input type="number" 
                       wire:model.live.debounce.500ms="price_max" 
                       placeholder="Max" 
                       class="w-20 px-2 py-1 rounded border border-slate-200 bg-slate-50 text-xs font-medium focus:bg-white outline-none">
            </div>

            <div class="flex items-center gap-2 text-slate-500 font-medium">
                <span>Ditemukan: <strong class="text-slate-900 font-bold">{{ $helms->total() }}</strong> produk</span>
                @if($search || $merk_filter || $kategori_filter || $price_min || $price_max)
                    <button wire:click="resetFilters" class="text-blue-600 hover:underline font-bold text-[10px]">
                        Hapus Filter
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="w-full text-center py-12">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 shadow-sm text-xs font-bold text-slate-600">
            <i class="fa-solid fa-circle-notch fa-spin text-blue-600"></i>
            <span>Menyaring produk...</span>
        </div>
    </div>

    {{-- Product Grid: Consistent Compact 2-Col Mobile / 5-Col Desktop --}}
    <div wire:loading.remove>
        @if($helms->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2.5 sm:gap-3.5">
                @foreach($helms as $helm)
                    <div class="bg-white border border-slate-200/90 rounded-xl overflow-hidden group hover:border-blue-500 hover:shadow-md transition-all flex flex-col justify-between">
                        
                        {{-- Image Wrapper --}}
                        <div class="relative aspect-square bg-slate-50 overflow-hidden">
                            <a href="{{ route('katalog.detail', $helm->id_helm) }}">
                                <img src="{{ $helm->gambar_url }}" 
                                     alt="{{ $helm->nama_helm }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </a>

                            {{-- Brand Badge --}}
                            @if($helm->merk)
                            <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded bg-slate-900/80 text-white text-[8px] font-bold uppercase tracking-wider">
                                {{ $helm->merk->nama_merk }}
                            </div>
                            @endif

                            {{-- Stock Indicator Pill --}}
                            <div class="absolute top-2 right-2">
                                @if($helm->stok > 0)
                                    <span class="inline-block px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[8px] font-bold border border-emerald-200">
                                        Stok: {{ $helm->stok }}
                                    </span>
                                @else
                                    <span class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 text-[8px] font-bold border border-rose-200">
                                        Habis
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-2.5 sm:p-3 flex flex-col flex-grow justify-between gap-2">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                    {{ $helm->kategori->nama_kategori ?? 'Helm' }}
                                </p>
                                <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="block mt-0.5">
                                    <h3 class="font-bold text-xs text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug">
                                        {{ $helm->nama_helm }}
                                    </h3>
                                </a>
                            </div>

                            <div class="pt-2 border-t border-slate-100">
                                <div class="mb-2">
                                    <span class="text-xs sm:text-sm font-black text-blue-600">
                                        {{ format_rupiah($helm->harga_jual) }}
                                    </span>
                                </div>

                                <a href="{{ route('katalog.detail', $helm->id_helm) }}" 
                                   class="w-full py-1.5 text-center text-[10px] font-bold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-colors block uppercase tracking-wider">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-8 flex justify-center">
                {{ $helms->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl p-8 text-center border border-slate-200/90 shadow-2xs max-w-sm mx-auto">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Helm Tidak Ditemukan</h3>
                <p class="text-[11px] text-slate-500 mb-4 leading-relaxed">
                    Tidak ada helm yang sesuai dengan filter. Coba kurangi filter pencarian Anda.
                </p>
                <button wire:click="resetFilters" 
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-sm">
                    Tampilkan Semua Helm
                </button>
            </div>
        @endif
    </div>
</div>
