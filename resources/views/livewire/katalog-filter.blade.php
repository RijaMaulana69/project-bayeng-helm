<div x-data="{ filterModal: false }">
    
    {{-- Sticky App Toolbar (Menempel di bawah header saat di-scroll) --}}
    <div class="sticky top-16 md:top-20 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-2xs">
        <div class="max-w-6xl mx-auto px-4 py-3 space-y-2.5">
            
            {{-- Baris 1: Search Input & Quick Action --}}
            <div class="flex items-center gap-2">
                {{-- Live Search Input --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari merk atau tipe helm..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-100 border border-transparent focus:border-blue-600 focus:bg-white text-xs font-semibold text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                        </button>
                    @endif
                </div>

                {{-- Sort Dropdown (Compact) --}}
                <div class="relative shrink-0">
                    <select wire:model.live="sort" 
                            class="appearance-none pl-3 pr-7 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/70 text-slate-700 text-xs font-bold border border-transparent focus:border-blue-600 focus:bg-white focus:outline-none cursor-pointer transition-all">
                        <option value="terbaru">Terbaru</option>
                        <option value="termurah">Termurah</option>
                        <option value="termahal">Termahal</option>
                        <option value="nama">A - Z</option>
                    </select>
                    <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>

                {{-- Filter Modal Trigger (Harga & Detail) --}}
                <button @click="filterModal = true" 
                        class="shrink-0 px-3 py-2 rounded-xl border text-xs font-bold flex items-center gap-1.5 transition-all {{ ($price_min || $price_max) ? 'bg-blue-50 border-blue-600 text-blue-600' : 'bg-slate-100 border-transparent text-slate-700 hover:bg-slate-200/70' }}">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    <span class="hidden sm:inline">Filter</span>
                    @if($price_min || $price_max)
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    @endif
                </button>
            </div>

            {{-- Baris 2: Infinite Horizontal Chips (Kategori + Merk) --}}
            <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar -mx-4 px-4 py-0.5">
                
                {{-- Chip Semua --}}
                <button wire:click="resetFilters" 
                        class="shrink-0 px-3 py-1.5 rounded-full text-xs font-bold transition-all {{ (!$merk_filter && !$kategori_filter && !$search && !$price_min && !$price_max) ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                    Semua
                </button>

                <div class="h-4 w-[1px] bg-slate-200 shrink-0"></div>

                {{-- Chips Kategori --}}
                @foreach($kategoris as $kat)
                    <button wire:click="$set('kategori_filter', '{{ $kategori_filter == $kat->id_kategori ? '' : $kat->id_kategori }}')" 
                            class="shrink-0 px-3 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 {{ $kategori_filter == $kat->id_kategori ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span>{{ $kat->nama_kategori }}</span>
                        @if($kategori_filter == $kat->id_kategori)
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        @endif
                    </button>
                @endforeach

                <div class="h-4 w-[1px] bg-slate-200 shrink-0"></div>

                {{-- Chips Merk --}}
                @foreach($merks as $m)
                    <button wire:click="$set('merk_filter', '{{ $merk_filter == $m->id_merk ? '' : $m->id_merk }}')" 
                            class="shrink-0 px-3 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 {{ $merk_filter == $m->id_merk ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 hover:border-slate-400 text-slate-700' }}">
                        <span>{{ $m->nama_merk }}</span>
                        @if($merk_filter == $m->id_merk)
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        @endif
                    </button>
                @endforeach

            </div>

        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-5 py-6">
        
        {{-- Counter & Active Filter Tags Bar --}}
        @if($search || $merk_filter || $kategori_filter || $price_min || $price_max)
            <div class="flex items-center justify-between mb-5 px-3 py-2 bg-blue-50/60 border border-blue-100 rounded-xl text-xs">
                <div class="flex items-center gap-2 text-slate-700 font-medium">
                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-600">Filter Aktif:</span>
                    <span class="text-slate-600">
                        {{ $helms->total() }} helm ditemukan
                    </span>
                </div>
                <button wire:click="resetFilters" 
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white text-[11px] font-bold transition-all shadow-2xs active:scale-95">
                    <i class="fa-solid fa-xmark text-[10px]"></i>
                    <span>Hapus Semua Filter</span>
                </button>
            </div>
        @endif

        {{-- Loading Indicator --}}
        <div wire:loading class="w-full text-center py-20">
            <div class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-white border border-slate-200 shadow-sm text-xs font-bold text-slate-700">
                <i class="fa-solid fa-circle-notch fa-spin text-blue-600 text-base"></i>
                <span>Menyaring katalog helm...</span>
            </div>
        </div>

        {{-- Product Grid: 100% Selaras dengan Beranda (2 Col HP, 6 Col Desktop) --}}
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
                <div class="mt-10 border-t border-slate-200/80 pt-4">
                    {{ $helms->links('livewire.partials.pagination') }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm max-w-sm mx-auto my-8">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4 text-xl">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 mb-1">Helm Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                        Tidak ada helm yang sesuai dengan filter pencarian ini.
                    </p>
                    <button wire:click="resetFilters" 
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-md shadow-blue-600/20 active:scale-95">
                        Tampilkan Semua Helm
                    </button>
                </div>
            @endif
        </div>

    </div>

    {{-- Filter Modal / Bottom Sheet (Rentang Harga) --}}
    <div x-show="filterModal" 
         x-cloak
         class="fixed inset-0 z-[60] overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" 
             @click="filterModal = false"></div>

        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-center">
            <div class="relative transform overflow-hidden rounded-t-3xl sm:rounded-2xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-md p-6 pb-24 sm:pb-6 space-y-5">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-blue-600 text-sm"></i>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Filter Tambahan</h3>
                    </div>
                    <button @click="filterModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                {{-- Rentang Harga --}}
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700">Rentang Harga (Rp)</label>
                    <div class="grid grid-cols-2 gap-3 items-center">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-1">Harga Minimum</span>
                            <input type="number" 
                                   wire:model.live.debounce.400ms="price_min" 
                                   placeholder="Rp 0" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 focus:bg-white focus:border-blue-600 outline-none">
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-1">Harga Maksimum</span>
                            <input type="number" 
                                   wire:model.live.debounce.400ms="price_max" 
                                   placeholder="Tanpa Batas" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 focus:bg-white focus:border-blue-600 outline-none">
                        </div>
                    </div>
                </div>

                {{-- Modal Action --}}
                <div class="pt-2 flex gap-2">
                    <button wire:click="resetFilters" 
                            @click="filterModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-colors">
                        Reset Filter
                    </button>
                    <button @click="filterModal = false" 
                            class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-500 transition-colors shadow-md shadow-blue-600/20">
                        Terapkan
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
