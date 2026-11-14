@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4 w-full">
        
        {{-- Counter Text ala E-commerce --}}
        <div class="text-xs text-slate-500 font-medium order-2 sm:order-1">
            Menampilkan <span class="font-bold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-900">{{ $paginator->total() }}</span> helm
        </div>

        {{-- Page Buttons --}}
        <div class="inline-flex items-center gap-1 order-1 sm:order-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 bg-slate-100 cursor-not-allowed text-xs">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 bg-white border border-slate-200 hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50/50 transition-all text-xs font-bold active:scale-95 shadow-2xs">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 text-xs font-bold">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white bg-blue-600 text-xs font-black shadow-sm shadow-blue-600/30">
                                {{ $page }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-700 bg-white border border-slate-200 hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50/50 transition-all text-xs font-bold active:scale-95 shadow-2xs">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 bg-white border border-slate-200 hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50/50 transition-all text-xs font-bold active:scale-95 shadow-2xs">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            @else
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 bg-slate-100 cursor-not-allowed text-xs">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>

    </nav>
@else
    @if($paginator->total() > 0)
        <div class="text-center py-4 text-xs text-slate-400 font-medium">
            Menampilkan seluruh <span class="font-bold text-slate-700">{{ $paginator->total() }}</span> helm
        </div>
    @endif
@endif
