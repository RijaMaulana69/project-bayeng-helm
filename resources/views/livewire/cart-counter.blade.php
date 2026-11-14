<div class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-slate-700 hover:text-blue-600 hover:bg-slate-100/80 transition-all">
    <i class="fa-solid fa-cart-shopping text-base sm:text-lg"></i>
    @if($count > 0)
        <span class="absolute -top-1 -right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-2xs ring-2 ring-white leading-none">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</div>
