@if ($paginator->hasPages())
    {{-- Mobile View: Compact Previous / Current / Next pager (prevents horizontal overflow & wrapping) --}}
    <div class="flex items-center justify-between w-full sm:hidden">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="h-8 px-3 rounded-lg border border-slate-200/60 text-slate-300 text-xs inline-flex items-center gap-1.5 cursor-not-allowed select-none bg-slate-50/50">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" wire:navigate class="h-8 px-3 rounded-lg border border-slate-200/90 bg-white text-slate-600 active:bg-slate-50 font-medium text-xs inline-flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                <span>Sebelumnya</span>
            </a>
        @endif

        <span class="text-xs font-medium text-slate-500">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" wire:navigate class="h-8 px-3 rounded-lg border border-slate-200/90 bg-white text-slate-600 active:bg-slate-50 font-medium text-xs inline-flex items-center gap-1.5 transition-colors">
                <span>Selanjutnya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        @else
            <span class="h-8 px-3 rounded-lg border border-slate-200/60 text-slate-300 text-xs inline-flex items-center gap-1.5 cursor-not-allowed select-none bg-slate-50/50">
                <span>Selanjutnya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </span>
        @endif
    </div>

    {{-- Desktop / Tablet View: Left info + Right numbered pagination --}}
    <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
        {{-- Counter Text --}}
        <p class="text-xs text-slate-500 whitespace-nowrap">
            Menampilkan <span class="font-semibold text-slate-700">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
        </p>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-1 shrink-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="h-8 px-2.5 rounded-lg border border-slate-200/60 text-slate-300 text-xs inline-flex items-center gap-1.5 cursor-not-allowed select-none bg-slate-50/50">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" wire:navigate class="h-8 px-2.5 rounded-lg border border-slate-200/90 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-xs transition-colors inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span>Sebelumnya</span>
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
                            <span aria-current="page" class="w-8 h-8 rounded-lg flex items-center justify-center text-white bg-slate-900 text-xs font-semibold shadow-2xs select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" wire:navigate class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 bg-white border border-slate-200/90 hover:bg-slate-50 hover:text-slate-900 transition-colors text-xs font-medium shadow-2xs">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" wire:navigate class="h-8 px-2.5 rounded-lg border border-slate-200/90 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-xs transition-colors inline-flex items-center gap-1.5 shadow-2xs">
                    <span>Selanjutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="h-8 px-2.5 rounded-lg border border-slate-200/60 text-slate-300 text-xs inline-flex items-center gap-1.5 cursor-not-allowed select-none bg-slate-50/50">
                    <span>Selanjutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>
    </div>
@elseif($paginator->total() > 0)
    <div class="flex items-center justify-between text-xs text-slate-500">
        <p class="text-xs text-slate-500">
            Menampilkan <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span>
        </p>
    </div>
@endif
