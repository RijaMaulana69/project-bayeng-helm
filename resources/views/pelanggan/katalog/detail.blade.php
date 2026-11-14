@extends('layouts.pelanggan')
@section('title', $helm->nama_helm . ' - Detail & Ketersediaan Gerai')

@section('content')
<div class="bg-[#f4f5f7] min-h-screen py-5 pb-32 sm:pb-16" 
     x-data="{ 
         activeTab: 'deskripsi',
         chatOpen: false,
         chatInput: 'Halo Admin, apakah helm {{ addslashes($helm->nama_helm) }} masih ready di toko Kuningan?',
         messages: [
             { sender: 'bot', text: 'Halo! Selamat datang di Layanan Pelanggan Bayeng Putra Helm Kuningan. Ada yang bisa kami bantu terkait ketersediaan atau ukuran helm {{ addslashes($helm->nama_helm) }}?', time: 'Baru saja' }
         ],
         sendMessage(text = null) {
             let msgToSend = text || this.chatInput;
             if (!msgToSend || msgToSend.trim() === '') return;
             this.messages.push({ sender: 'user', text: msgToSend, time: 'Baru saja' });
             if (!text) this.chatInput = '';
             this.$nextTick(() => {
                 let box = document.getElementById('chat-scroll-container');
                 if (box) box.scrollTop = box.scrollHeight;
             });
             setTimeout(() => {
                 this.messages.push({ 
                     sender: 'bot', 
                     text: 'Terima kasih telah menghubungi kami. Pesan Anda terkait helm {{ addslashes($helm->nama_helm) }} sudah diteruskan ke staf kasir gerai RE Martadinata. Kami akan merespons secepatnya!', 
                     time: 'Baru saja' 
                 });
                 this.$nextTick(() => {
                     let box = document.getElementById('chat-scroll-container');
                     if (box) box.scrollTop = box.scrollHeight;
                 });
             }, 800);
         }
     }"
     @open-store-chat.window="chatOpen = true">
    
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        {{-- Breadcrumb Clean --}}
        <nav class="flex items-center gap-1.5 text-xs text-slate-500 font-medium mb-4 overflow-hidden">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors">Katalog Helm</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold truncate">{{ $helm->nama_helm }}</span>
        </nav>

        {{-- Main E-Commerce Product Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Column 1: Image Showcase (Left 5 Cols) --}}
            <div class="lg:col-span-5 space-y-3">
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                    <div class="relative aspect-square bg-slate-50">
                        <img src="{{ $helm->gambar_url }}" 
                             alt="{{ $helm->nama_helm }}" 
                             class="w-full h-full object-cover">
                        
                        @if($helm->merk)
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                                {{ $helm->merk->nama_merk }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Guarantee Bar below photo --}}
                <div class="bg-white p-3.5 rounded-xl border border-slate-200 grid grid-cols-2 gap-2 text-[11px] text-slate-600 font-medium">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-store text-blue-600"></i>
                        <span>Gerai Resmi Kuningan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-check text-emerald-600"></i>
                        <span>100% Standar SNI/DOT</span>
                    </div>
                </div>
            </div>

            {{-- Column 2: Details & Buying Engine (Right 7 Cols) --}}
            <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-5 sm:p-7 shadow-2xs space-y-5">
                
                {{-- Category & Product Title --}}
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <a href="{{ route('katalog', ['kategori_filter' => $helm->id_kategori]) }}" 
                           class="text-blue-600 text-xs font-bold uppercase tracking-wider hover:underline">
                            {{ $helm->kategori->nama_kategori ?? 'Helm Motor' }}
                        </a>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-xs text-slate-500 font-medium">Katalog ID: #{{ $helm->id_helm }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight leading-snug">
                        {{ $helm->nama_helm }}
                    </h1>
                </div>

                {{-- Price Display --}}
                <div class="py-3 px-4 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Harga Gerai</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-blue-600 tracking-tight">
                            {{ format_rupiah($helm->harga_jual) }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                            Ambil di Toko Bebas Biaya
                        </span>
                    </div>
                </div>

                {{-- Variant Selector & Add to Cart Component --}}
                <livewire:add-to-cart-button :helm="$helm" />

                {{-- Interactive Store Chat Banner (Natural Retail Style) --}}
                <div class="p-3.5 bg-blue-50/50 border border-blue-100 rounded-xl flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Punya pertanyaan seputar ukuran atau stok?</p>
                            <p class="text-[11px] text-slate-500">Staf toko kami siap menjawab via Chat Toko online.</p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="chatOpen = true"
                            class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shrink-0 active:scale-95 shadow-2xs flex items-center gap-1.5">
                        <i class="fa-solid fa-comment-dots"></i>
                        <span>Chat Toko</span>
                    </button>
                </div>

                {{-- Product Information Tabs --}}
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <div class="flex items-center gap-4 border-b border-slate-200 text-xs font-bold">
                        <button type="button" 
                                @click="activeTab = 'deskripsi'"
                                class="pb-2.5 border-b-2 transition-colors cursor-pointer"
                                :class="activeTab === 'deskripsi' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'">
                            Deskripsi Produk
                        </button>
                        <button type="button" 
                                @click="activeTab = 'stok'"
                                class="pb-2.5 border-b-2 transition-colors cursor-pointer"
                                :class="activeTab === 'stok' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'">
                            Stok Fisik Gerai ({{ $helm->varians->count() }} Varian)
                        </button>
                        <button type="button" 
                                @click="activeTab = 'fitting'"
                                class="pb-2.5 border-b-2 transition-colors cursor-pointer"
                                :class="activeTab === 'fitting' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'">
                            Panduan Ukuran (Fitting)
                        </button>
                    </div>

                    {{-- Tab 1: Deskripsi --}}
                    <div x-show="activeTab === 'deskripsi'" class="text-xs text-slate-600 leading-relaxed space-y-3">
                        <p>{{ $helm->deskripsi ?? 'Helm berkendara standar SNI & DOT dengan bahan shell berkualitas tinggi, busa dalam empuk dapat dilepas dan dicuci, serta tali pengunci yang aman.' }}</p>
                        
                        <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200/80 space-y-1.5 text-[11px]">
                            <p class="font-bold text-slate-900">Alur Ambil di Gerai (BOPIS):</p>
                            <ul class="list-disc list-inside space-y-1 text-slate-600">
                                <li>Pemesanan via website langsung mengunci stok produk Anda selama 1x24 jam.</li>
                                <li>Dapat dicoba (fitting pas) di gerai sebelum menyelesaikan pembayaran / serah terima.</li>
                                <li>Metode bayar fleksibel: transfer atau bayar tunai / QRIS langsung di kasir gerai.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Tab 2: Matriks Stok --}}
                    <div x-show="activeTab === 'stok'" x-cloak class="space-y-3">
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="py-2.5 px-3">Warna</th>
                                        <th class="py-2.5 px-3">Ukuran</th>
                                        <th class="py-2.5 px-3 text-right">Ketersediaan Gerai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($helm->varians as $v)
                                    <tr>
                                        <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $v->warna }}</td>
                                        <td class="py-2.5 px-3 font-bold text-slate-600">{{ $v->ukuran }}</td>
                                        <td class="py-2.5 px-3 text-right">
                                            @if($v->stok > 0)
                                                <span class="inline-flex items-center gap-1 text-emerald-700 font-bold text-[11px] bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                    <i class="fa-solid fa-circle-check text-[9px]"></i>
                                                    <span>Ready ({{ $v->stok }})</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-slate-400 font-medium text-[11px] bg-slate-100 px-2 py-0.5 rounded">
                                                    Habis
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 3: Panduan Ukuran & Fitting --}}
                    <div x-show="activeTab === 'fitting'" x-cloak class="space-y-3 text-xs text-slate-600 leading-relaxed">
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="py-2.5 px-3">Ukuran Helm</th>
                                        <th class="py-2.5 px-3">Lingkar Kepala (cm)</th>
                                        <th class="py-2.5 px-3">Rekomendasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-[11px]">
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-slate-900">M</td>
                                        <td class="py-2.5 px-3 font-semibold text-slate-700">57 - 58 cm</td>
                                        <td class="py-2.5 px-3 text-slate-500">Kompak, busa pas pipi</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-slate-900">L</td>
                                        <td class="py-2.5 px-3 font-semibold text-slate-700">59 - 60 cm</td>
                                        <td class="py-2.5 px-3 text-slate-500">Ukuran paling umum standar dewasa</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3 font-bold text-slate-900">XL</td>
                                        <td class="py-2.5 px-3 font-semibold text-slate-700">61 - 62 cm</td>
                                        <td class="py-2.5 px-3 text-slate-500">Cocok untuk kepala lebar / hijab tebal</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-[11px] text-slate-500 italic">
                            *Tips: Anda dapat mengukur lingkar kepala menggunakan meteran kain di atas alis. Jika ragu, pesan dahulu di website dan coba langsung di gerai kami sebelum checkout final.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- Related Products --}}
        @if(isset($related) && $related->count() > 0)
        <div class="mt-12 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Helm Terkait Lainnya</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
                @foreach($related as $rel)
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden group hover:border-blue-500 hover:shadow-sm transition-all flex flex-col justify-between">
                    <div class="aspect-square bg-slate-50 relative overflow-hidden">
                        <a href="{{ route('katalog.detail', $rel->id_helm) }}">
                            <img src="{{ $rel->gambar_url }}" alt="{{ $rel->nama_helm }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </a>
                        @if($rel->merk)
                            <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-slate-900 text-white text-[8px] font-bold uppercase rounded">
                                {{ $rel->merk->nama_merk }}
                            </div>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-grow justify-between gap-2">
                        <a href="{{ route('katalog.detail', $rel->id_helm) }}">
                            <h4 class="text-[11px] font-bold text-slate-800 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors">
                                {{ $rel->nama_helm }}
                            </h4>
                        </a>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-xs font-black text-blue-600">{{ format_rupiah($rel->harga_jual) }}</span>
                            <a href="{{ route('katalog.detail', $rel->id_helm) }}" class="px-2 py-1 bg-slate-50 border border-slate-200 rounded text-[9px] font-bold text-slate-700 hover:bg-blue-600 hover:text-white transition-colors">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Internal E-Commerce Chat Modal / Drawer (Ala Tokopedia) --}}
    <div x-show="chatOpen" 
         x-cloak
         class="fixed inset-0 z-[70] overflow-hidden" 
         aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-2xs transition-opacity" 
             @click="chatOpen = false"></div>

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div class="w-screen max-w-md bg-white border-l border-slate-200 flex flex-col shadow-2xl">
                
                {{-- Chat Header --}}
                <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold text-white">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-900"></span>
                        </div>
                        <div>
                            <p class="text-xs font-bold leading-tight">Customer Service Gerai</p>
                            <p class="text-[10px] text-emerald-400 font-medium">Online &bull; Gerai RE Martadinata</p>
                        </div>
                    </div>
                    <button type="button" @click="chatOpen = false" class="text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                {{-- Product Preview Pin in Chat --}}
                <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center gap-3 text-xs">
                    <img src="{{ $helm->gambar_url }}" class="w-11 h-11 object-cover rounded-lg border border-slate-200 shrink-0">
                    <div class="overflow-hidden flex-1">
                        <p class="font-bold text-slate-900 truncate text-[11px]">{{ $helm->nama_helm }}</p>
                        <p class="text-[10px] font-black text-blue-600">{{ format_rupiah($helm->harga_jual) }}</p>
                    </div>
                    <span class="text-[10px] text-slate-400 font-semibold bg-white px-2 py-0.5 rounded border border-slate-200 shrink-0">Produk Ditanyakan</span>
                </div>

                {{-- Messages Area --}}
                <div id="chat-scroll-container" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#f8fafc]">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex flex-col" :class="msg.sender === 'user' ? 'items-end' : 'items-start'">
                            <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-xs font-medium leading-relaxed"
                                 :class="msg.sender === 'user' ? 'bg-blue-600 text-white rounded-br-xs' : 'bg-white text-slate-800 border border-slate-200 shadow-2xs rounded-bl-xs'">
                                <p x-text="msg.text"></p>
                            </div>
                            <span class="text-[9px] text-slate-400 mt-1 px-1" x-text="msg.time"></span>
                        </div>
                    </template>
                </div>

                {{-- Quick Suggestions --}}
                <div class="px-3 pt-2 pb-1 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                    <button type="button" 
                            @click="sendMessage('Apakah stok helm {{ addslashes($helm->nama_helm) }} masih ready di toko?')"
                            class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 text-[10px] font-semibold border border-slate-200 shrink-0 transition-colors cursor-pointer">
                        Apakah ready stok?
                    </button>
                    <button type="button" 
                            @click="sendMessage('Apakah bisa coba fitting ukuran langsung di gerai?')"
                            class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 text-[10px] font-semibold border border-slate-200 shrink-0 transition-colors cursor-pointer">
                        Bisa fitting di gerai?
                    </button>
                    <button type="button" 
                            @click="sendMessage('Berapa lama batas waktu pengambilan barang setelah pesan online?')"
                            class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 text-[10px] font-semibold border border-slate-200 shrink-0 transition-colors cursor-pointer">
                        Batas waktu ambil?
                    </button>
                </div>

                {{-- Chat Input Bar --}}
                <div class="p-3 bg-white border-t border-slate-200">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                        <input type="text" 
                               x-model="chatInput" 
                               placeholder="Ketik pesan untuk staf toko..." 
                               class="flex-1 px-3 py-2 bg-slate-100 rounded-xl text-xs text-slate-900 border border-transparent focus:border-blue-600 focus:bg-white outline-none">
                        <button type="submit" 
                                class="w-9 h-9 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shrink-0 transition-colors shadow-2xs">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
