<div>
    {{-- Floating Chat Button Launcher (Desktop & Mobile) --}}
    @unless(request()->routeIs('admin.*'))
    <div class="fixed bottom-20 md:bottom-6 right-4 sm:right-6 z-40">
        <button type="button"
                wire:click="openChat"
                class="group relative flex items-center gap-2 h-11 px-4 rounded-full bg-slate-900 hover:bg-slate-800 text-white shadow-lg shadow-slate-900/20 active:scale-95 transition-all cursor-pointer"
                title="Chat Toko">
            <div class="relative flex items-center justify-center">
                <i class="fa-solid fa-comments text-sm"></i>
                @if($unreadCount > 0)
                    <span class="absolute -top-2 -right-2 min-w-[17px] h-[17px] px-1 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center ring-2 ring-white animate-pulse">
                        {{ $unreadCount }}
                    </span>
                @else
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-emerald-500 rounded-full ring-2 ring-slate-900"></span>
                @endif
            </div>
            <span class="text-xs font-semibold tracking-tight hidden sm:inline">
                Chat Toko
            </span>
        </button>
    </div>
    @endunless

    {{-- Slide-Over Drawer Chat --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-hidden" 
         role="dialog" 
         aria-modal="true"
         @chat-scrolled-to-bottom.window="
             $nextTick(() => {
                 let box = document.getElementById('customer-chat-scroll');
                 if (box) box.scrollTop = box.scrollHeight;
             });
         "
         @if($isOpen) wire:poll.3s @endif>
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-2xs transition-opacity" 
             wire:click="closeChat"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
            <div class="w-screen max-w-md bg-white border-l border-slate-200 flex flex-col shadow-2xl">
                
                {{-- Clean Minimalist Header --}}
                <div class="px-4 py-3 bg-white border-b border-slate-200/80 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                                <i class="fa-solid fa-helmet-safety"></i>
                            </div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900 leading-tight">Bayeng Helm</p>
                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Gerai Kuningan • Buka 08.00–21.00</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            wire:click="closeChat" 
                            class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer"
                            title="Tutup Chat">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                {{-- Compact Product Context Card --}}
                @if($helmContext)
                <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-200/70 flex items-center gap-3 shrink-0">
                    <img src="{{ $helmContext->gambar_url }}" 
                         alt="{{ $helmContext->nama_helm }}" 
                         class="w-10 h-10 object-cover rounded-lg border border-slate-200/80 bg-white shrink-0">
                    <div class="overflow-hidden flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 truncate text-xs">{{ $helmContext->nama_helm }}</p>
                        <p class="text-[11px] font-bold text-slate-700 mt-0.5">{{ format_rupiah($helmContext->harga_jual) }}</p>
                    </div>
                    <button type="button" 
                            wire:click="sendMessage('Halo kak, helm {{ addslashes($helmContext->nama_helm) }} masih ready stok?')"
                            class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white font-medium text-[11px] rounded-full transition-colors shrink-0 cursor-pointer shadow-2xs">
                        Tanya Stok
                    </button>
                </div>
                @endif

                {{-- Messages Stream --}}
                <div id="customer-chat-scroll" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#f8fafc]">
                    @forelse($messages as $msg)
                        @if($msg->pengirim_tipe === 'pelanggan')
                            {{-- Bubble Pelanggan (Kanan) --}}
                            <div class="flex flex-col items-end max-w-[85%] ml-auto">
                                <div class="rounded-2xl rounded-br-xs px-3.5 py-2 text-[13px] leading-relaxed bg-slate-900 text-white shadow-2xs">
                                    <p class="whitespace-pre-line">{{ $msg->pesan }}</p>
                                </div>
                                <span class="text-[9px] text-slate-400 mt-1 px-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        @elseif($msg->pengirim_tipe === 'karyawan')
                            {{-- Bubble Kru Toko (Kiri) --}}
                            <div class="flex flex-col items-start max-w-[85%]">
                                <div class="rounded-2xl rounded-bl-xs px-3.5 py-2 text-[13px] leading-relaxed bg-white text-slate-800 border border-slate-200/90 shadow-2xs">
                                    <p class="whitespace-pre-line">{{ $msg->pesan }}</p>
                                </div>
                                <span class="text-[9px] text-slate-400 mt-1 px-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        @else
                            {{-- Pesan Sambutan Toko (Tengah) --}}
                            <div class="my-3 text-center">
                                <span class="inline-block max-w-[90%] px-3.5 py-1.5 rounded-full bg-slate-200/70 text-slate-600 text-[11px] leading-relaxed">
                                    {{ $msg->pesan }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <div class="py-14 text-center text-xs text-slate-400 space-y-1">
                            <i class="fa-regular fa-comment-dots text-2xl text-slate-300 block mb-1"></i>
                            <p class="font-medium text-slate-600">Belum ada pesan</p>
                            <p class="text-[11px] text-slate-400">Tanyakan ketersediaan stok atau ukuran helm di sini.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Natural Quick Suggestion Chips (Anti-AI-Slop) --}}
                <div class="px-3 py-2 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto hide-scrollbar shrink-0">
                    <button type="button" 
                            wire:click="sendMessage('Halo kak, helm ini masih ready stok?')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Helm ini ready?
                    </button>
                    <button type="button" 
                            wire:click="sendMessage('Bisa coba fitting ukuran langsung di toko kak?')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Bisa fitting di toko?
                    </button>
                    <button type="button" 
                            wire:click="sendMessage('Untuk helm ini tersedia ukuran apa aja kak?')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Pilihan ukuran?
                    </button>
                    <button type="button" 
                            wire:click="sendMessage('Toko buka hari ini sampai jam berapa kak?')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Jam buka toko
                    </button>
                </div>

                {{-- Pill-Style Clean Input Bar --}}
                <div class="p-3 bg-white border-t border-slate-200 shrink-0">
                    <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                        <input type="text" 
                               wire:model="chatInput" 
                               placeholder="Tulis pesan..." 
                               autocomplete="off"
                               class="flex-1 px-4 py-2 bg-slate-100 rounded-full text-xs text-slate-900 border border-transparent focus:border-slate-300 focus:bg-white outline-none transition-all placeholder:text-slate-400">
                        <button type="submit" 
                                class="w-8 h-8 rounded-full bg-slate-900 hover:bg-slate-800 text-white flex items-center justify-center shrink-0 transition-all shadow-2xs active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-[11px]"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>
