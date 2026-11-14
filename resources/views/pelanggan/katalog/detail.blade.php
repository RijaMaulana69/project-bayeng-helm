@extends('layouts.pelanggan')
@section('title', $helm->nama_helm . ' - Bayeng Putra Helm Kuningan')

@section('content')
<div class="bg-[#f6f7f9] min-h-screen py-3 sm:py-5 pb-20 sm:pb-16" 
     x-data="{ 
         chatOpen: false,
         chatInput: '',
         messages: [
             { 
                 sender: 'bot', 
                 text: 'Halo kak! Selamat datang di Bayeng Putra Helm Kuningan. Ada yang bisa kami bantu mengenai helm {{ addslashes($helm->nama_helm) }}?', 
                 time: 'Online' 
             }
         ],
         sendMessage(customText = null) {
             let msg = customText || this.chatInput;
             if (!msg || msg.trim() === '') return;
             this.messages.push({ sender: 'user', text: msg, time: 'Baru saja' });
             if (!customText) this.chatInput = '';
             this.$nextTick(() => {
                 let box = document.getElementById('chat-scroll-container');
                 if (box) box.scrollTop = box.scrollHeight;
             });
             setTimeout(() => {
                 let reply = 'Pesan kakak mengenai helm {{ addslashes($helm->nama_helm) }} sudah diteruskan ke staf gerai Kuningan. Kami akan membalas secepatnya kak.';
                 if (msg.includes('fitting') || msg.includes('coba')) {
                     reply = 'Bisa banget kak! Helm ini bisa dicoba fitting langsung di gerai kami (Jl. RE Martadinata No.49 Kuningan).';
                 } else if (msg.includes('ready') || msg.includes('stok')) {
                     reply = 'Untuk helm {{ addslashes($helm->nama_helm) }}, stok fisik di gerai saat ini ready {{ $helm->stok }} unit kak.';
                 } else if (msg.includes('jam') || msg.includes('alamat')) {
                     reply = 'Gerai kami buka setiap hari jam 08:00 - 21:00 WIB di Jl. RE Martadinata No.49 Kuningan. Ditunggu kedatangannya kak!';
                 }
                 this.messages.push({ sender: 'bot', text: reply, time: 'Baru saja' });
                 this.$nextTick(() => {
                     let box = document.getElementById('chat-scroll-container');
                     if (box) box.scrollTop = box.scrollHeight;
                 });
             }, 500);
         }
     }"
     @open-store-chat.window="chatOpen = true">
    
    <div class="max-w-6xl mx-auto px-3 sm:px-5">
        
        {{-- Main PDP Canvas (Clean Minimalist E-Commerce) --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-0 md:gap-6 items-start">
                
                {{-- Column 1: Image Showcase (Compact Retail Size) --}}
                <div class="md:col-span-5 lg:col-span-4 p-4 sm:p-5 md:p-6 md:sticky md:top-24">
                    <div class="max-w-[260px] sm:max-w-[300px] md:max-w-none mx-auto">
                        <div class="relative aspect-square rounded-xl bg-slate-50 border border-slate-200 overflow-hidden">
                            <img src="{{ $helm->gambar_url }}" 
                                 alt="{{ $helm->nama_helm }}" 
                                 class="w-full h-full object-cover">
                            
                            @if($helm->merk)
                                <div class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded bg-slate-900/90 text-white text-[9px] font-black uppercase tracking-wider">
                                    {{ $helm->merk->nama_merk }}
                                </div>
                            @endif

                            {{-- Stock Status Tag --}}
                            <div class="absolute top-2.5 right-2.5">
                                @if($helm->stok > 0)
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-200">
                                        Stok {{ $helm->stok }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-[9px] font-bold border border-rose-200">
                                        Habis
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Column 2: Product Data & Purchase Engine --}}
                <div class="md:col-span-7 lg:col-span-8 p-4 sm:p-5 md:p-6 md:pl-0 space-y-4">
                    
                    {{-- Category & Title --}}
                    <div>
                        <div class="mb-1">
                            <a href="{{ route('katalog', ['kategori_filter' => $helm->id_kategori]) }}" 
                               class="text-blue-600 text-xs font-bold uppercase tracking-wider hover:underline">
                                {{ $helm->kategori->nama_kategori ?? 'Helm Motor' }}
                            </a>
                        </div>
                        <h1 class="text-base sm:text-xl md:text-2xl font-bold text-slate-900 tracking-tight leading-snug">
                            {{ $helm->nama_helm }}
                        </h1>
                    </div>

                    {{-- Clean Minimalist Price Box --}}
                    <div class="py-2.5 px-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Harga</span>
                            <span class="text-2xl sm:text-3xl font-black text-blue-600 tracking-tight">
                                {{ format_rupiah($helm->harga_jual) }}
                            </span>
                        </div>
                    </div>

                    {{-- Add to Cart Component (Desktop Buttons & Mobile Bottom Bar & Bottom Sheet) --}}
                    <livewire:add-to-cart-button :helm="$helm" />

                    {{-- Section: Deskripsi Produk & Ukuran --}}
                    <div class="border-t border-slate-100 pt-4 space-y-3.5">
                        
                        {{-- Header --}}
                        <div class="flex items-center justify-between">
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-600 text-xs"></i>
                                <span>Deskripsi Produk</span>
                            </h2>
                            <span class="text-[10px] text-slate-400 font-medium">SNI & DOT</span>
                        </div>

                        {{-- Text Description --}}
                        <div class="text-xs text-slate-600 leading-relaxed space-y-2">
                            <p>{{ $helm->deskripsi ?? 'Helm berkendara standar SNI & DOT dengan bahan shell berkualitas tinggi, busa dalam empuk dapat dilepas dan dicuci, serta sistem penguncian aman.' }}</p>
                        </div>

                        {{-- Informasi Ukuran Terintegrasi --}}
                        <div class="pt-2 space-y-2">
                            <p class="text-xs font-bold text-slate-800">Panduan Ukuran (Lingkar Kepala):</p>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200">
                                    <p class="font-bold text-slate-900">Ukuran M</p>
                                    <p class="text-[11px] font-bold text-blue-600 mt-0.5">57 - 58 cm</p>
                                </div>
                                <div class="p-2 rounded-lg bg-blue-50 border border-blue-200">
                                    <p class="font-bold text-blue-900">Ukuran L</p>
                                    <p class="text-[11px] font-bold text-blue-600 mt-0.5">59 - 60 cm</p>
                                </div>
                                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200">
                                    <p class="font-bold text-slate-900">Ukuran XL</p>
                                    <p class="text-[11px] font-bold text-blue-600 mt-0.5">61 - 62 cm</p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- Helm Terkait Lainnya (Compact Card & Selaras dengan Katalog) --}}
        @if(isset($related) && $related->count() > 0)
        <div class="mt-8 sm:mt-10 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Helm Terkait Lainnya
                </h3>
                <a href="{{ route('katalog', ['kategori_filter' => $helm->id_kategori]) }}" 
                   class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </a>
            </div>

            {{-- Grid Compact Selaras dengan Katalog --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 sm:gap-3 md:gap-4">
                @foreach($related as $rel)
                <a href="{{ route('katalog.detail', $rel->id_helm) }}" 
                   class="bg-white border border-slate-200 rounded-xl overflow-hidden group hover:border-blue-500 hover:shadow-md transition-all flex flex-col justify-between">
                    {{-- Thumb --}}
                    <div class="aspect-square bg-slate-50 relative overflow-hidden">
                        <img src="{{ $rel->gambar_url }}" 
                             alt="{{ $rel->nama_helm }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        
                        {{-- Brand Tag --}}
                        @if($rel->merk)
                        <div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-white/90 backdrop-blur-sm rounded text-[8px] font-black text-slate-900 shadow-2xs uppercase">
                            {{ $rel->merk->nama_merk }}
                        </div>
                        @endif

                        {{-- Stock Status Tag --}}
                        <div class="absolute top-1.5 right-1.5">
                            @if($rel->stok > 0)
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[8px] font-black border border-emerald-200">
                                    Stok {{ $rel->stok }}
                                </span>
                            @else
                                <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 text-[8px] font-black border border-rose-200">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Body (Compact Shopee Style) --}}
                    <div class="p-2 sm:p-2.5 flex flex-col flex-grow justify-between gap-1 sm:gap-1.5">
                        <h4 class="text-[11px] font-bold text-slate-800 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors">
                            {{ $rel->nama_helm }}
                        </h4>
                        <div class="flex items-center justify-between pt-0.5">
                            <span class="text-xs sm:text-sm font-black text-blue-600">{{ format_rupiah($rel->harga_jual) }}</span>
                            <span class="text-[9px] font-semibold text-slate-400 group-hover:text-blue-600 transition-colors">Lihat &rarr;</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Slide-Over Chat Internal Toko --}}
    <div x-show="chatOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden" 
         role="dialog" aria-modal="true">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-2xs transition-opacity" 
             @click="chatOpen = false"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
            <div class="w-screen max-w-md bg-white border-l border-slate-200 flex flex-col shadow-2xl animate-slide-left">
                
                {{-- Clean E-Commerce Header --}}
                <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-helmet-safety"></i>
                            </div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-slate-900 leading-tight">Bayeng Putra Helm</p>
                                <span class="px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 text-[9px] font-bold">Official</span>
                            </div>
                            <p class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Online</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="chatOpen = false" 
                            class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                {{-- Product Context Banner --}}
                <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
                    <img src="{{ $helm->gambar_url }}" class="w-11 h-11 object-cover rounded-lg border border-slate-200 shrink-0">
                    <div class="overflow-hidden flex-1">
                        <p class="font-bold text-slate-900 truncate text-xs">{{ $helm->nama_helm }}</p>
                        <p class="text-[11px] font-black text-blue-600 mt-0.5">{{ format_rupiah($helm->harga_jual) }}</p>
                    </div>
                    <button type="button" 
                            @click="sendMessage('Halo, apakah stok helm {{ addslashes($helm->nama_helm) }} masih ready?')"
                            class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[10px] rounded-lg border border-blue-200 transition-colors shrink-0 cursor-pointer">
                        Tanya Stok
                    </button>
                </div>

                {{-- Messages Stream --}}
                <div id="chat-scroll-container" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#f8fafc]">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex flex-col" :class="msg.sender === 'user' ? 'items-end' : 'items-start'">
                            <div class="max-w-[85%] rounded-2xl px-3.5 py-2 text-xs leading-relaxed"
                                 :class="msg.sender === 'user' ? 'bg-blue-600 text-white rounded-br-xs' : 'bg-white text-slate-800 border border-slate-200 shadow-2xs rounded-bl-xs'">
                                <p x-text="msg.text"></p>
                            </div>
                            <span class="text-[9px] text-slate-400 mt-1 px-1" x-text="msg.time"></span>
                        </div>
                    </template>
                </div>

                {{-- Quick Suggestions --}}
                <div class="px-3 py-2 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto hide-scrollbar">
                    <button type="button" 
                            @click="sendMessage('Apakah helm {{ addslashes($helm->nama_helm) }} masih ready di toko?')"
                            class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 text-[10px] font-semibold border border-slate-200 shrink-0 transition-colors cursor-pointer">
                        Ready stok?
                    </button>
                    <button type="button" 
                            @click="sendMessage('Bisa coba fitting ukuran helm ini langsung di gerai?')"
                            class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 text-[10px] font-semibold border border-slate-200 shrink-0 transition-colors cursor-pointer">
                        Bisa fitting di gerai?
                    </button>
                    <button type="button" 
                            @click="sendMessage('Jam buka gerai hari ini sampai jam berapa?')"
                            class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 text-[10px] font-semibold border border-slate-200 shrink-0 transition-colors cursor-pointer">
                        Jam buka gerai?
                    </button>
                </div>

                {{-- Chat Input Bar --}}
                <div class="p-3 bg-white border-t border-slate-200">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                        <input type="text" 
                               x-model="chatInput" 
                               placeholder="Ketik pesan..." 
                               class="flex-1 px-3.5 py-2 bg-slate-100 rounded-xl text-xs text-slate-900 border border-transparent focus:border-blue-600 focus:bg-white outline-none transition-all placeholder:text-slate-400">
                        <button type="submit" 
                                class="w-9 h-9 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shrink-0 transition-all shadow-sm active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
