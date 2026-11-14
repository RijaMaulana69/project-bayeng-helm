<div>
    {{-- Search & Filter Command Bar --}}
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/80 mb-8 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
            
            {{-- Search Bar --}}
            <div class="lg:col-span-4">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Cari Nama / Model
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Ketik KYT, INK, NHK, dll..." 
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
            </div>

            {{-- Merk Dropdown --}}
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Merk
                </label>
                <select wire:model.live="merk_filter" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                    <option value="">Semua Merk</option>
                    @foreach($merks as $merk)
                        <option value="{{ $merk->id_merk }}">{{ $merk->nama_merk }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Kategori Dropdown --}}
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Kategori
                </label>
                <select wire:model.live="kategori_filter" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort By --}}
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Urutan
                </label>
                <select wire:model.live="sort" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                    <option value="terbaru">Terbaru</option>
                    <option value="termurah">Harga: Rendah ke Tinggi</option>
                    <option value="termahal">Harga: Tinggi ke Rendah</option>
                    <option value="nama">Nama A-Z</option>
                </select>
            </div>

            {{-- Reset Button --}}
            <div class="lg:col-span-2">
                <button wire:click="resetFilters" 
                        class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100/70 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all active:scale-95">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Reset Filter</span>
                </button>
            </div>

        </div>

        {{-- Price Range Filter --}}
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3 text-xs">
            <span class="font-bold text-slate-500 uppercase text-[10px] tracking-wider">Rentang Harga (Rp):</span>
            <div class="flex items-center gap-2">
                <input type="number" 
                       wire:model.live.debounce.500ms="price_min" 
                       placeholder="Min: Rp 0" 
                       class="w-32 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-medium focus:bg-white focus:ring-1 focus:ring-blue-500 outline-none">
                <span class="text-slate-400">-</span>
                <input type="number" 
                       wire:model.live.debounce.500ms="price_max" 
                       placeholder="Max: Rp Tak Batas" 
                       class="w-32 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-medium focus:bg-white focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
            @if($search || $merk_filter || $kategori_filter || $price_min || $price_max)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    Filter Aktif
                </span>
            @endif
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="w-full text-center py-16">
        <div class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-white border border-slate-200 shadow-md">
            <i class="fa-solid fa-circle-notch fa-spin text-blue-600 text-base"></i>
            <span class="text-xs font-bold text-slate-700">Menyaring helm secara real-time...</span>
        </div>
    </div>

    {{-- Product Grid --}}
    <div wire:loading.remove>
        @if($helms->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($helms as $helm)
                    <div class="group bg-white rounded-3xl overflow-hidden border border-slate-200/80 hover:border-blue-400 hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        
                        {{-- Image Wrapper --}}
                        <div class="relative aspect-square bg-slate-100/70 overflow-hidden">
                            <a href="{{ route('katalog.detail', $helm->id_helm) }}">
                                <img src="{{ $helm->gambar_url }}" 
                                     alt="{{ $helm->nama_helm }}" 
                                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                            </a>

                            {{-- Brand Badge --}}
                            @if($helm->merk)
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-white/95 backdrop-blur-md text-[10px] font-extrabold text-slate-800 shadow-sm border border-slate-100">
                                {{ $helm->merk->nama_merk }}
                            </div>
                            @endif

                            {{-- Stock Indicator Pill --}}
                            <div class="absolute top-3 right-3">
                                @if($helm->stok > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 text-[10px] font-bold border border-emerald-500/20 backdrop-blur-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Stok {{ $helm->stok }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-700 text-[10px] font-bold border border-rose-500/20">
                                        Habis
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-4 sm:p-5 flex flex-col flex-grow justify-between space-y-3">
                            <div>
                                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">
                                    {{ $helm->kategori->nama_kategori ?? 'Helm Motor' }}
                                </p>
                                <a href="{{ route('katalog.detail', $helm->id_helm) }}" class="block mt-0.5">
                                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug">
                                        {{ $helm->nama_helm }}
                                    </h3>
                                </a>
                            </div>

                            <div class="pt-3 border-t border-slate-100">
                                <div class="flex items-baseline justify-between mb-3">
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-medium">Harga</p>
                                        <p class="text-sm sm:text-base font-extrabold text-slate-900">
                                            {{ format_rupiah($helm->harga_jual) }}
                                        </p>
                                    </div>
                                    <span class="text-[11px] font-medium text-slate-500">
                                        <i class="fa-solid fa-store text-emerald-500 text-[10px] mr-1"></i>Pickup
                                    </span>
                                </div>

                                <a href="{{ route('katalog.detail', $helm->id_helm) }}" 
                                   class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-slate-900 group-hover:bg-blue-600 text-white text-xs font-bold transition-all shadow-sm active:scale-98">
                                    <span>Pilih Varian & Stok</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
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
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-sm max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-helmet-safety"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Helm Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                    Tidak ada helm yang cocok dengan kata kunci atau filter yang Anda pilih. Silakan reset filter untuk melihat seluruh koleksi kami.
                </p>
                <button wire:click="resetFilters" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Tampilkan Semua Helm</span>
                </button>
            </div>
        @endif
    </div>
</div>
