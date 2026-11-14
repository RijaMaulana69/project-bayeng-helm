<div>
    {{-- Search & Filter Toolbar (Tokopedia/Shopee Header Style) --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm mb-8 space-y-4">
        
        {{-- Row 1: Search, Dropdowns & Sort --}}
        <div class="grid grid-cols-2 md:grid-cols-12 gap-3 items-center">
            
            {{-- Search Bar (Main) --}}
            <div class="col-span-2 md:col-span-5">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari merk atau model helm..." 
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                </div>
            </div>

            {{-- Merk Dropdown --}}
            <div class="col-span-1 md:col-span-2">
                <select wire:model.live="merk_filter" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all cursor-pointer">
                    <option value="">Semua Merk</option>
                    @foreach($merks as $merk)
                        <option value="{{ $merk->id_merk }}">{{ $merk->nama_merk }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Kategori Dropdown --}}
            <div class="col-span-1 md:col-span-2">
                <select wire:model.live="kategori_filter" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort By --}}
            <div class="col-span-1 md:col-span-2">
                <select wire:model.live="sort" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all cursor-pointer">
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
                        class="w-full h-10 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all active:scale-95">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span class="md:hidden">Reset</span>
                </button>
            </div>

        </div>

        {{-- Row 2: Price Filters & Total Counter --}}
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-400 uppercase text-[10px] tracking-wider">Rentang Harga:</span>
                <input type="number" 
                       wire:model.live.debounce.500ms="price_min" 
                       placeholder="Min (Rp)" 
                       class="w-24 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold focus:bg-white focus:border-blue-600 outline-none">
                <span class="text-slate-300">-</span>
                <input type="number" 
                       wire:model.live.debounce.500ms="price_max" 
                       placeholder="Max (Rp)" 
                       class="w-24 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-semibold focus:bg-white focus:border-blue-600 outline-none">
            </div>

            <div class="flex items-center gap-2 text-slate-500 font-semibold text-[11px]">
                <span>Menampilkan <strong class="text-slate-900 font-black">{{ $helms->total() }}</strong> Helm</span>
                @if($search || $merk_filter || $kategori_filter || $price_min || $price_max)
                    <button wire:click="resetFilters" class="text-blue-600 hover:underline font-bold text-[10px] uppercase tracking-wider ml-1">
                        Hapus Filter
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="w-full text-center py-16">
        <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-xl bg-white border border-slate-200 shadow-sm text-xs font-bold text-slate-700">
            <i class="fa-solid fa-circle-notch fa-spin text-blue-600"></i>
            <span>Menyaring produk...</span>
        </div>
    </div>

    {{-- Product Grid: 100% Identik dengan Grid di Beranda --}}
    <div wire:loading.remove>
        @if($helms->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-4">
                @foreach($helms as $helm)
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden group hover:border-blue-500 hover:shadow-xl hover:shadow-slate-200/40 transition-all flex flex-col">
                    {{-- Thumb --}}
                    <div class="aspect-square bg-slate-50 relative overflow-hidden">
                        <a href="{{ route('katalog.detail', $helm->id_helm) }}">
                            <img src="{{ $helm->gambar_url }}" 
                                 alt="{{ $helm->nama_helm }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </a>
                        
                        {{-- Brand Tag --}}
                        @if($helm->merk)
                        <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-white/90 backdrop-blur-sm rounded text-[8px] font-black text-slate-900 shadow-sm uppercase">
                            {{ $helm->merk->nama_merk }}
                        </div>
                        @endif

                        {{-- Stock Status Tag --}}
                        <div class="absolute top-2 right-2">
                            @if($helm->stok > 0)
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[8px] font-black border border-emerald-200 shadow-2xs">
                                    Stok {{ $helm->stok }}
                                </span>
                            @else
                                <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 text-[8px] font-black border border-rose-200 shadow-2xs">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-3 flex flex-col flex-grow justify-between gap-3">
                        <div>
                            <a href="{{ route('katalog.detail', $helm->id_helm) }}">
                                <h3 class="text-[11px] font-bold text-slate-800 line-clamp-2 h-8 leading-tight mb-1 group-hover:text-blue-600 transition-colors">
                                    {{ $helm->nama_helm }}
                                </h3>
                            </a>
                            <p class="text-[10px] font-black text-blue-600">{{ format_rupiah($helm->harga_jual) }}</p>
                        </div>
                        <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-center text-[9px] font-black text-slate-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all uppercase tracking-widest">
                            Detail
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-10 flex justify-center">
                {{ $helms->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl p-10 text-center border border-slate-200 shadow-sm max-w-sm mx-auto">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Helm Tidak Ditemukan</h3>
                <p class="text-[11px] text-slate-500 mb-4 leading-relaxed">
                    Tidak ada helm yang sesuai dengan filter. Coba atur ulang filter pencarian Anda.
                </p>
                <button wire:click="resetFilters" 
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-sm active:scale-95">
                    Tampilkan Semua Helm
                </button>
            </div>
        @endif
    </div>
</div>
