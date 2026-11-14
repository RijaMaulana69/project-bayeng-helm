<div class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-700 hover:text-blue-600 hover:bg-blue-50/80 transition-all">
    <i class="fa-solid fa-bag-shopping text-lg"></i>
    @if($count > 0)
        <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-extrabold text-white shadow-sm ring-2 ring-white">
            {{ $count }}
        </span>
    @endif
</div>
