<div wire:poll.4s
     x-data="{
         isMobile: window.innerWidth < 768,
         mobileView: '{{ request()->has('id') ? 'chat' : 'list' }}',
         init() {
             this.isMobile = window.innerWidth < 768;
             const urlParams = new URLSearchParams(window.location.search);
             if (urlParams.has('id')) {
                 this.mobileView = 'chat';
             }
             window.addEventListener('resize', () => {
                 this.isMobile = window.innerWidth < 768;
             });
             this.scrollToBottom();
         },
         openChat(id) {
             $wire.selectPercakapan(id);
             this.mobileView = 'chat';
             this.scrollToBottom();
         },
         goBack() {
             this.mobileView = 'list';
             $wire.backToList();
         },
         scrollToBottom() {
             this.$nextTick(() => {
                 let el = document.getElementById('admin-chat-scroll');
                 if (el) el.scrollTop = el.scrollHeight;
             });
         }
     }"
     @chat-scrolled-to-bottom.window="scrollToBottom()">

    {{-- Main Container: Mobile Full-View / Desktop 2-Column Split --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden h-[calc(100dvh-9.5rem)] md:h-[calc(100vh-6.5rem)] min-h-[520px] flex">

        {{-- ========================================================= --}}
        {{-- PANE 1: DAFTAR CHAT (INBOX)                               --}}
        {{-- Mobile: Layar Penuh saat mobileView === 'list'            --}}
        {{-- Desktop: Kolom Kiri Selalu Tampil                         --}}
        {{-- ========================================================= --}}
        <div class="w-full md:w-80 lg:w-96 flex flex-col shrink-0 bg-white border-r border-slate-200/80"
             x-show="!isMobile || mobileView === 'list'"
             x-cloak>
            
            {{-- Search & Filter Bar --}}
            <div class="p-3 border-b border-slate-100 space-y-2.5 shrink-0 bg-white">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari nama, pesan, nomor..."
                           class="w-full h-9 pl-9 pr-8 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-slate-400 transition-colors">
                    @if($search)
                        <button type="button" 
                                wire:click="$set('search', '')" 
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs cursor-pointer"
                                title="Hapus pencarian">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>

                {{-- Status Filter Chips --}}
                <div class="flex items-center gap-1.5 overflow-x-auto hide-scrollbar text-[11px] font-medium">
                    <button type="button"
                            wire:click="$set('filterStatus', 'all')"
                            class="px-3 py-1 rounded-full transition-colors cursor-pointer {{ $filterStatus === 'all' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua
                    </button>
                    <button type="button"
                            wire:click="$set('filterStatus', 'unread')"
                            class="px-3 py-1 rounded-full transition-colors cursor-pointer flex items-center gap-1 {{ $filterStatus === 'unread' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <span>Belum Dibaca</span>
                        @if($totalUnread > 0)
                            <span class="px-1.5 py-0.2 rounded-full bg-white text-rose-600 text-[9px] font-bold">
                                {{ $totalUnread }}
                            </span>
                        @endif
                    </button>
                    <button type="button"
                            wire:click="$set('filterStatus', 'active')"
                            class="px-3 py-1 rounded-full transition-colors cursor-pointer {{ $filterStatus === 'active' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Aktif
                    </button>
                    <button type="button"
                            wire:click="$set('filterStatus', 'closed')"
                            class="px-3 py-1 rounded-full transition-colors cursor-pointer {{ $filterStatus === 'closed' ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Selesai
                    </button>
                </div>
            </div>

            {{-- Conversation Items Scrollable List --}}
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($conversations as $conv)
                    @php
                        $isSelected = $selectedPercakapanId === $conv->id_percakapan;
                        $hasUnread = $conv->unread_karyawan > 0;
                    @endphp
                    <div wire:key="conv-item-{{ $conv->id_percakapan }}"
                         data-conv-id="{{ $conv->id_percakapan }}"
                         @click="openChat({{ $conv->id_percakapan }})"
                         class="p-3 sm:p-3.5 flex items-center gap-3 cursor-pointer transition-colors relative {{ $isSelected ? 'bg-slate-100/90 border-l-4 border-slate-900' : 'hover:bg-slate-50 active:bg-slate-100' }}">
                        
                        {{-- Avatar Monogram with Status Dot --}}
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-2xs {{ $isSelected ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ strtoupper(substr($conv->nama_pengirim, 0, 1)) }}
                            </div>
                            @if($conv->status === 'aktif')
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white" title="Aktif"></span>
                            @else
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-slate-400 ring-2 ring-white" title="Selesai"></span>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <p class="text-xs font-bold text-slate-900 truncate {{ $hasUnread ? 'text-blue-900 font-extrabold' : '' }}">
                                    {{ $conv->nama_pengirim }}
                                </p>
                                <span class="text-[10px] text-slate-400 font-medium shrink-0 ml-1">
                                    {{ $conv->pesan_terakhir_at ? $conv->pesan_terakhir_at->diffForHumans(null, true, true) : '' }}
                                </span>
                            </div>

                            <p class="text-[11px] truncate leading-snug {{ $hasUnread ? 'font-semibold text-slate-800' : 'text-slate-500' }}">
                                {{ $conv->pesan_terakhir ?: 'Belum ada pesan' }}
                            </p>

                            {{-- Product Context Pill --}}
                            @if($conv->helmTerkait)
                                <div class="mt-1 flex items-center gap-1 text-[10px] text-slate-500 truncate">
                                    <i class="fa-solid fa-helmet-safety text-[9px] text-slate-400"></i>
                                    <span class="truncate">{{ $conv->helmTerkait->nama_helm }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Unread Badge --}}
                        @if($hasUnread)
                            <div class="shrink-0">
                                <span class="min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white">
                                    {{ $conv->unread_karyawan }}
                                </span>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="py-16 px-4 text-center text-slate-400">
                        <i class="fa-regular fa-comment-dots text-3xl text-slate-300 mb-2 block"></i>
                        <p class="text-xs font-semibold text-slate-600">Tidak ada obrolan</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            @if($search)
                                Tidak ada hasil untuk "{{ $search }}".
                            @else
                                Belum ada pesan dari pelanggan.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- PANE 2: RUANG CHAT AKTIF (CHAT ROOM)                      --}}
        {{-- Mobile: Layar Penuh saat mobileView === 'chat'            --}}
        {{-- Desktop: Kolom Kanan Selalu Tampil                        --}}
        {{-- ========================================================= --}}
        <div class="flex-1 flex flex-col bg-[#f8fafc] overflow-hidden"
             x-show="!isMobile || mobileView === 'chat'"
             x-cloak>
            
            @if($selectedPercakapan)
                {{-- Room Header --}}
                <div class="h-14 px-3 sm:px-4 bg-white border-b border-slate-200/80 flex items-center justify-between shrink-0 shadow-2xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        {{-- Tombol Kembali (Mobile Only) --}}
                        <button type="button"
                                @click="goBack()"
                                class="md:hidden w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-700 active:scale-95 transition-colors cursor-pointer shrink-0"
                                title="Kembali ke Daftar Chat">
                            <i class="fa-solid fa-arrow-left text-sm"></i>
                        </button>

                        {{-- Monogram Avatar --}}
                        <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            {{ strtoupper(substr($selectedPercakapan->nama_pengirim, 0, 1)) }}
                        </div>

                        {{-- Customer Info --}}
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 truncate leading-tight">
                                    {{ $selectedPercakapan->nama_pengirim }}
                                </h3>
                                @if($selectedPercakapan->id_pelanggan)
                                    <span class="px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 text-[9px] font-semibold border border-blue-200/60 hidden sm:inline">
                                        Member
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5 truncate">
                                @if($selectedPercakapan->status === 'aktif')
                                    <span class="flex items-center gap-1 text-emerald-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-slate-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Selesai</span>
                                    </span>
                                @endif

                                @if($selectedPercakapan->telepon_pengirim)
                                    <span class="text-slate-300">•</span>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedPercakapan->telepon_pengirim) }}" 
                                       target="_blank" 
                                       class="text-emerald-600 hover:underline flex items-center gap-1 font-medium"
                                       title="Hubungi via WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>{{ $selectedPercakapan->telepon_pengirim }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Header Actions --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        {{-- Status Toggle --}}
                        <button type="button"
                                wire:click="toggleStatus({{ $selectedPercakapan->id_percakapan }})"
                                class="px-2.5 py-1 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50 text-[11px] font-semibold transition-colors cursor-pointer"
                                title="Ubah status">
                            @if($selectedPercakapan->status === 'aktif')
                                <i class="fa-solid fa-check text-emerald-600 mr-1"></i>
                                <span>Selesai</span>
                            @else
                                <i class="fa-solid fa-rotate-left text-slate-500 mr-1"></i>
                                <span>Buka</span>
                            @endif
                        </button>

                        {{-- Delete Button --}}
                        <button type="button"
                                wire:click="deletePercakapan({{ $selectedPercakapan->id_percakapan }})"
                                wire:confirm="Hapus sesi percakapan ini?"
                                class="w-8 h-8 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer"
                                title="Hapus">
                            <i class="fa-regular fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- Compact Product Context Banner (if helm inquiry) --}}
                @if($selectedPercakapan->helmTerkait)
                    <div class="px-3 sm:px-4 py-2 bg-amber-50/80 border-b border-amber-200/60 flex items-center justify-between gap-2.5 shrink-0">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <img src="{{ $selectedPercakapan->helmTerkait->gambar_url }}" 
                                 alt="{{ $selectedPercakapan->helmTerkait->nama_helm }}" 
                                 class="w-8 h-8 object-cover rounded-lg bg-white border border-amber-200 shrink-0">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-900 truncate">
                                    {{ $selectedPercakapan->helmTerkait->nama_helm }}
                                </p>
                                <p class="text-[10px] text-slate-600">
                                    <strong class="text-blue-700">{{ format_rupiah($selectedPercakapan->helmTerkait->harga_jual) }}</strong>
                                    <span class="mx-1">•</span>
                                    <span>Stok: <strong class="{{ $selectedPercakapan->helmTerkait->stok > 0 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $selectedPercakapan->helmTerkait->stok }} unit</strong></span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" 
                                    wire:click="sendQuickReply('Ready kak! Stok helm {{ addslashes($selectedPercakapan->helmTerkait->nama_helm) }} di toko ada {{ $selectedPercakapan->helmTerkait->stok }} unit. Mau cari ukuran apa kak?')"
                                    class="px-2.5 py-1 rounded-full bg-white hover:bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-semibold transition-colors cursor-pointer shadow-2xs">
                                Balas Stok
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Clean Messages Stream (WhatsApp / Telegram Minimalist Style) --}}
                <div id="admin-chat-scroll" class="flex-1 p-3 sm:p-4 overflow-y-auto space-y-2.5">
                    @forelse($selectedPercakapan->pesans as $msg)
                        @if($msg->pengirim_tipe === 'pelanggan')
                            {{-- Bubble Pelanggan (Kiri - Bersih Tanpa Header Berulang) --}}
                            <div class="flex flex-col items-start max-w-[85%] sm:max-w-[75%]">
                                <div class="rounded-2xl rounded-tl-xs px-3.5 py-2 text-xs sm:text-[13px] leading-relaxed bg-white text-slate-800 border border-slate-200/90 shadow-2xs">
                                    <p class="whitespace-pre-line">{{ $msg->pesan }}</p>
                                </div>
                                <span class="text-[9px] text-slate-400 mt-0.5 px-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        @elseif($msg->pengirim_tipe === 'karyawan')
                            {{-- Bubble Kasir (Kanan - Gelap Elegan Tanpa AI Slop) --}}
                            <div class="flex flex-col items-end max-w-[85%] sm:max-w-[75%] ml-auto">
                                <div class="rounded-2xl rounded-tr-xs px-3.5 py-2 text-xs sm:text-[13px] leading-relaxed bg-slate-900 text-white shadow-2xs">
                                    <p class="whitespace-pre-line">{{ $msg->pesan }}</p>
                                </div>
                                <div class="flex items-center gap-1 text-[9px] text-slate-400 mt-0.5 px-1">
                                    <span>{{ $msg->created_at->format('H:i') }}</span>
                                    @if($msg->dibaca)
                                        <i class="fa-solid fa-check-double text-blue-500 text-[8px]" title="Dibaca"></i>
                                    @else
                                        <i class="fa-solid fa-check text-slate-400 text-[8px]" title="Terkirim"></i>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Pesan Sambutan / Sistem (Tengah) --}}
                            <div class="my-2 text-center">
                                <span class="inline-block max-w-[90%] px-3 py-1 rounded-full bg-slate-200/70 text-slate-600 text-[10px] sm:text-[11px] leading-relaxed">
                                    {{ $msg->pesan }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <div class="py-14 text-center text-xs text-slate-400">
                            Belum ada pesan dalam percakapan ini.
                        </div>
                    @endforelse
                </div>

                {{-- Fast Quick Reply Chips Bar --}}
                <div class="px-3 py-1.5 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto hide-scrollbar shrink-0">
                    <button type="button"
                            wire:click="sendQuickReply('Ready kak! Mau cari helm ini ukuran apa ya (M/L/XL)?')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Ready (Tanya Ukuran)
                    </button>
                    <button type="button"
                            wire:click="sendQuickReply('Bisa banget kak, mampir aja langsung ke toko buat coba ukuran fitting. Buka sampai jam 9 malam.')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Bisa Fitting di Toko
                    </button>
                    <button type="button"
                            wire:click="sendQuickReply('Toko kami buka setiap hari jam 08.00–21.00 WIB di Jl. RE Martadinata No.49 Kuningan.')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Jam Buka & Lokasi
                    </button>
                    <button type="button"
                            wire:click="sendQuickReply('Bisa pesan lewat web kak, nanti pas checkout pilih ambil di kasir toko ya.')"
                            class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                        Cara Ambil di Kasir
                    </button>
                </div>

                {{-- Input Bar Ramping --}}
                <div class="p-2.5 sm:p-3 bg-white border-t border-slate-200/90 shrink-0">
                    <form wire:submit.prevent="sendReply" class="flex items-center gap-2">
                        <input type="text"
                               wire:model="replyMessage"
                               placeholder="Tulis balasan untuk pelanggan..."
                               autocomplete="off"
                               class="flex-1 px-4 py-2 bg-slate-100 rounded-full text-xs text-slate-900 border border-transparent focus:border-slate-300 focus:bg-white outline-none transition-all placeholder:text-slate-400">
                        <button type="submit"
                                class="w-8 h-8 rounded-full bg-slate-900 hover:bg-slate-800 text-white flex items-center justify-center shrink-0 transition-all shadow-2xs active:scale-95 cursor-pointer"
                                title="Kirim Balasan">
                            <i class="fa-solid fa-paper-plane text-[11px]"></i>
                        </button>
                    </form>
                </div>

            @else
                {{-- State Kosong (Ketika belum ada percakapan yang dipilih di desktop) --}}
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-2xs mb-3 text-slate-300">
                        <i class="fa-regular fa-comments text-2xl"></i>
                    </div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Pilih Percakapan Pelanggan</h3>
                    <p class="text-[11px] text-slate-400 max-w-xs mt-1">
                        Pilih salah satu sesi obrolan dari daftar sebelah kiri untuk membalas pesan.
                    </p>
                </div>
            @endif

        </div>

    </div>

</div>
