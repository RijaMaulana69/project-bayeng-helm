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

    {{-- Footer --}}
    <footer class="hidden pt-16 pb-10 bg-white border-t border-slate-100 md:block mt-12">
        <div class="px-8 mx-auto max-w-7xl grid grid-cols-12 gap-12">
            <div class="col-span-4 space-y-6">
                <div class="flex items-center gap-2.5">
                    <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 rounded-lg shadow-sm">
                        <i class="text-sm fa-solid fa-helmet-safety"></i>
                    </div>
                    <span class="text-lg font-extrabold tracking-tighter text-slate-900 uppercase">Bayeng Putra</span>
                </div>
                <p class="text-sm font-medium leading-relaxed text-slate-500">
                    Retailer helm terkemuka di Kuningan. Kami memastikan setiap helm yang Anda pesan pas dan nyaman saat digunakan.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="flex items-center justify-center w-8 h-8 text-slate-400 transition-colors bg-slate-50 rounded-full hover:bg-blue-600 hover:text-white"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="flex items-center justify-center w-8 h-8 text-slate-400 transition-colors bg-slate-50 rounded-full hover:bg-emerald-600 hover:text-white"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-span-2 space-y-5">
                <h4 class="text-[10px] font-bold tracking-[0.2em] text-slate-900 uppercase">Navigasi</h4>
                <ul class="space-y-3 text-xs font-bold text-slate-500">
                    <li><a href="{{ route('katalog') }}" class="transition-colors hover:text-blue-600">Katalog Helm</a></li>
                    <li><a href="#" class="transition-colors hover:text-blue-600">Panduan Ukuran</a></li>
                    <li><a href="{{ route('pelanggan.pesanan') }}" class="transition-colors hover:text-blue-600">Status Pesanan</a></li>
                </ul>
            </div>
            <div class="col-span-2 space-y-5">
                <h4 class="text-[10px] font-bold tracking-[0.2em] text-slate-900 uppercase">Legal</h4>
                <ul class="space-y-3 text-xs font-bold text-slate-500">
                    <li><a href="#" class="transition-colors hover:text-blue-600">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="transition-colors hover:text-blue-600">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('admin.login') }}" class="transition-colors hover:text-blue-600 opacity-40">Admin</a></li>
                </ul>
            </div>
            <div class="col-span-4 space-y-5">
                <h4 class="text-[10px] font-bold tracking-[0.2em] text-slate-900 uppercase">Hubungi Kami</h4>
                <div class="p-5 space-y-3 bg-slate-50 border border-slate-100 rounded-xl">
                    <div class="flex gap-3 text-xs font-bold text-slate-600">
                        <i class="fa-solid fa-location-dot text-blue-600"></i>
                        <p>Jl. RE Martadinata No.49, Kuningan</p>
                    </div>
                    <div class="flex gap-3 text-xs font-bold text-slate-600">
                        <i class="fa-solid fa-clock text-blue-600"></i>
                        <p>Buka 08:00 - 21:00 WIB</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-8 pt-8 mx-auto mt-16 border-t border-slate-100 max-w-7xl flex justify-between items-center">
            <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">&copy; {{ date('Y') }} Bayeng Putra Helm Store</p>
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
