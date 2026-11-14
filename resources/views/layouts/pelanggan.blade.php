<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bayeng Putra Helm') | Toko Helm Terpercaya Kuningan</title>

    {{-- Fonts: Inter for professional e-commerce feel --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
            -webkit-font-smoothing: antialiased;
        }
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Professional Nav Underline */
        .nav-link { position: relative; padding-bottom: 4px; }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #2563eb;
            transition: width 0.2s ease;
        }
        .nav-link.active::after { width: 100%; }

        /* Glassmorphism Refined */
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen pb-20 text-slate-800 md:pb-0 selection:bg-blue-600 selection:text-white" 
      x-data="{ scrolled: false }"
      @scroll.window="scrolled = (window.pageYOffset > 20)">

    {{-- Sticky Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 h-16 md:h-20 flex items-center"
            :class="scrolled ? 'glass-nav h-14 md:h-16 shadow-sm' : 'bg-white border-b border-slate-100'">
        
        <div class="flex items-center justify-between w-full px-4 mx-auto max-w-7xl md:px-8">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 rounded-lg shadow-sm md:w-9 md:h-9">
                    <i class="text-sm fa-solid fa-helmet-safety"></i>
                </div>
                <div>
                    <span class="block text-sm font-extrabold leading-none tracking-tight text-slate-900 md:text-base">BAYENG <span class="text-blue-600 uppercase">PUTRA</span></span>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="nav-link text-xs font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('home') ? 'active text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">Beranda</a>
                <a href="{{ route('katalog') }}" class="nav-link text-xs font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('katalog*') ? 'active text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">Katalog</a>
                <a href="{{ route('pelanggan.pesanan') }}" class="nav-link text-xs font-bold uppercase tracking-widest transition-colors {{ request()->routeIs('pelanggan.pesanan*') ? 'active text-blue-600' : 'text-slate-500 hover:text-slate-900' }}">Cek Tiket</a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-3 md:gap-6">
                @auth('pelanggan')
                    <a href="{{ route('pelanggan.cart') }}" class="relative p-2 text-slate-500 hover:text-blue-600 transition-colors">
                        @livewire('cart-counter')
                    </a>
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-50 transition-colors border border-slate-200">
                            <div class="flex items-center justify-center w-6 h-6 text-[10px] font-bold text-white bg-slate-800 rounded-full md:w-7 md:h-7 uppercase">
                                {{ substr(Auth::guard('pelanggan')->user()->nama, 0, 1) }}
                            </div>
                            <i class="fa-solid fa-chevron-down text-[8px] text-slate-400 mr-1.5"></i>
                        </button>
                        <div x-show="open" x-cloak 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 z-50 w-48 mt-2 overflow-hidden bg-white border border-slate-100 rounded-xl shadow-xl">
                            <a href="{{ route('pelanggan.profil') }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 border-b border-slate-50">Profil Saya</a>
                            <a href="{{ route('pelanggan.pesanan') }}" class="block px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 border-b border-slate-50">Riwayat Pesanan</a>
                            <form action="{{ route('pelanggan.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">Keluar Akun</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('pelanggan.login') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors">Masuk</a>
                        <a href="{{ route('pelanggan.register') }}" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all shadow-sm">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-grow pt-16 md:pt-20">
        @yield('content')
    </main>

    {{-- Mobile Nav (App Style) --}}
    <nav class="fixed bottom-0 left-0 right-0 z-50 flex items-center justify-around h-16 px-2 bg-white border-t border-slate-100 md:hidden shadow-lg">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 transition-colors {{ request()->routeIs('home') ? 'text-blue-600' : 'text-slate-400' }}">
            <i class="fa-solid fa-house-chimney text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Beranda</span>
        </a>
        <a href="{{ route('katalog') }}" class="flex flex-col items-center gap-1 transition-colors {{ request()->routeIs('katalog*') ? 'text-blue-600' : 'text-slate-400' }}">
            <i class="fa-solid fa-compass text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Katalog</span>
        </a>
        <a href="{{ route('pelanggan.pesanan') }}" class="flex flex-col items-center gap-1 transition-colors {{ request()->routeIs('pelanggan.pesanan*') ? 'text-blue-600' : 'text-slate-400' }}">
            <i class="fa-solid fa-receipt text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Tiket</span>
        </a>
        <a href="{{ auth('pelanggan')->check() ? route('pelanggan.profil') : route('pelanggan.login') }}" class="flex flex-col items-center gap-1 transition-colors {{ request()->routeIs('pelanggan.profil*') || request()->routeIs('pelanggan.login') ? 'text-blue-600' : 'text-slate-400' }}">
            <i class="fa-solid fa-user text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">{{ auth('pelanggan')->check() ? 'Profil' : 'Masuk' }}</span>
        </a>
    </nav>

    {{-- Footer (Ultra-Minimalist 1 Baris - Uniqlo/Apple Style) --}}
    <footer class="hidden md:block bg-white border-t border-slate-100 py-6 mt-16">
        <div class="max-w-6xl mx-auto px-5 flex items-center justify-between">
            {{-- Brand Left --}}
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-blue-600 text-white flex items-center justify-center text-[10px]">
                    <i class="fa-solid fa-helmet-safety"></i>
                </div>
                <span class="text-xs font-black tracking-tight text-slate-800">BAYENG PUTRA</span>
            </div>

            {{-- Nav Links Center --}}
            <nav class="flex items-center gap-6 text-[11px] font-medium text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                <a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors">Katalog</a>
                <a href="{{ route('pelanggan.pesanan') }}" class="hover:text-blue-600 transition-colors">Cek Tiket</a>
                <a href="#lokasi-gerai" class="hover:text-blue-600 transition-colors">Lokasi Gerai</a>
                <a href="{{ route('admin.login') }}" class="hover:text-slate-700 transition-colors text-slate-300">Staff</a>
            </nav>

            {{-- Action Right --}}
            <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Hubungi Toko</span>
            </a>
        </div>
    </footer>

    {{-- Toast --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session('success') }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            });
        </script>
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>
