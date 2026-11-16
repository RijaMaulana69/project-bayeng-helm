@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 text-xs text-slate-500">
        {{-- Counter Text --}}
        <p class="text-center sm:text-left text-[11px]">
            Menampilkan <span class="font-semibold text-slate-800">{{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> data
        </p>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-1 flex-wrap justify-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="h-8 px-2.5 sm:px-3 rounded-lg border border-slate-200/80 text-slate-300 text-xs inline-flex items-center gap-1 cursor-not-allowed select-none bg-slate-50/50">
                    <span>&larr;</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" wire:navigate class="h-8 px-2.5 sm:px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-blue-600 hover:border-blue-500 font-medium text-xs transition-colors inline-flex items-center gap-1 shadow-2xs">
                    <span>&larr;</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </a>
            @endif

            {{-- Numbered Page Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 text-xs font-semibold select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="w-8 h-8 rounded-lg flex items-center justify-center text-white bg-blue-600 text-xs font-bold shadow-2xs select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" wire:navigate class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-700 bg-white border border-slate-200 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50/50 transition-colors text-xs font-semibold shadow-2xs">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" wire:navigate class="h-8 px-2.5 sm:px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-blue-600 hover:border-blue-500 font-medium text-xs transition-colors inline-flex items-center gap-1 shadow-2xs">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <span>&rarr;</span>
                </a>
            @else
                <span class="h-8 px-2.5 sm:px-3 rounded-lg border border-slate-200/80 text-slate-300 text-xs inline-flex items-center gap-1 cursor-not-allowed select-none bg-slate-50/50">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <span>&rarr;</span>
                </span>
            @endif
        </div>
    </div>
@elseif($paginator->total() > 0)
    <div class="flex items-center justify-between pt-3 text-xs text-slate-500">
        <p class="text-[11px]">
            Menampilkan <span class="font-semibold text-slate-800">1-{{ $paginator->total() }}</span> dari <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> data
        </p>
    </div>
@endif
