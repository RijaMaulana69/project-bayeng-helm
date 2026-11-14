<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bayeng Putra Helm') - Toko Helm Click & Collect Kuningan</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Tailwind CSS & Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        bayeng: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Custom dynamic underline for nav */
        .nav-link { position: relative; }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: #2563eb;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link.active::after { width: 60%; }
        
        /* Glassmorphism utility */
        .glass-header {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen antialiased selection:bg-blue-600 selection:text-white pb-20 md:pb-0" 
      x-data="{ 
        mobileMenu: false,
        scrolled: false 
      }"
      @scroll.window="scrolled = (window.pageYOffset > 20)">

    {{-- High-End Floating Header --}}
    <div class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 flex justify-center pointer-events-none"
         :class="scrolled ? 'pt-3 md:pt-4' : 'pt-0'">
        
        <header class="w-full md:w-[95%] max-w-7xl h-16 md:h-20 transition-all duration-500 pointer-events-auto flex items-center justify-between px-4 md:px-8 shadow-sm"
                :class="scrolled ? 'glass-header border border-white/50 md:rounded-full shadow-xl shadow-slate-200/40 h-14 md:h-16' : 'bg-transparent border-transparent'">
            
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/30 transition-transform group-hover:scale-105">
                    <i class="fa-solid fa-helmet-safety text-lg"></i>
                </div>
                <div class="hidden sm:block">
                    <span class="font-extrabold text-sm md:text-base text-slate-900 tracking-tight block leading-none">BAYENG PUTRA</span>
                    <span class="text-[9px] md:text-[10px] font-black text-blue-600 uppercase tracking-widest">HELM STORE</span>
                </div>
            </a>

            {{-- Navigation --}}
            <nav class="hidden md:flex items-center gap-1.5 lg:gap-2">
                <a href="{{ route('home') }}" 
                   class="nav-link px-4 py-2 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 {{ request()->routeIs('home') ? 'active text-blue-700 bg-blue-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                   Beranda
                </a>
                <a href="{{ route('katalog') }}" 
                   class="nav-link px-4 py-2 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 {{ request()->routeIs('katalog*') ? 'active text-blue-700 bg-blue-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                   Katalog
                </a>
                <a href="{{ route('pelanggan.pesanan') }}" 
                   class="nav-link px-4 py-2 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 {{ request()->routeIs('pelanggan.pesanan*') ? 'active text-blue-700 bg-blue-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                   Cek Tiket
                </a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-2.5 md:gap-4">
                @auth('pelanggan')
                    <a href="{{ route('pelanggan.cart') }}" class="relative p-2 text-slate-600 hover:text-blue-600 transition-all hover:scale-110">
                        @livewire('cart-counter')
                    </a>
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-full border border-slate-200 bg-white/80 hover:bg-white transition-all shadow-sm">
                            <div class="w-6 h-6 md:w-7 md:h-7 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-black uppercase">
                                {{ substr(Auth::guard('pelanggan')->user()->nama, 0, 1) }}
                            </div>
                            <span class="text-[11px] md:text-xs font-bold text-slate-700 truncate max-w-[80px] md:max-w-[100px]">{{ explode(' ', Auth::guard('pelanggan')->user()->nama)[0] }}</span>
                        </button>
                        <div x-show="open" x-cloak 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 mt-3 w-52 bg-white/95 backdrop-blur-xl rounded-[1.5rem] shadow-2xl shadow-slate-300/60 border border-slate-100 py-2.5 overflow-hidden z-50">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Akun Pelanggan</p>
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::guard('pelanggan')->user()->nama }}</p>
                            </div>
                            <a href="{{ route('pelanggan.profil') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                                <i class="fa-regular fa-user-circle text-sm text-slate-400"></i> Profil Saya
                            </a>
                            <a href="{{ route('pelanggan.pesanan') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                                <i class="fa-regular fa-file-lines text-sm text-slate-400"></i> Riwayat Pesanan
                            </a>
                            <hr class="my-1.5 border-slate-50">
                            <form action="{{ route('pelanggan.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i> Keluar Akun
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-1.5 md:gap-3">
                        <a href="{{ route('pelanggan.login') }}" class="px-4 py-2 text-xs md:text-sm font-bold text-slate-700 hover:text-blue-600 transition-colors">Masuk</a>
                        <a href="{{ route('pelanggan.register') }}" class="px-4 md:px-6 py-2 md:py-2.5 rounded-full bg-blue-600 text-white text-xs md:text-sm font-bold shadow-lg shadow-blue-600/20 hover:bg-blue-700 hover:-translate-y-0.5 active:scale-95 transition-all">Daftar</a>
                    </div>
                @endauth
            </div>
        </header>
    </div>

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Mobile Bottom Navigation Bar (High-End App Style) --}}
    <nav class="fixed bottom-4 left-4 right-4 z-50 bg-white/90 backdrop-blur-xl border border-white/50 md:hidden rounded-2xl shadow-2xl shadow-slate-900/10 px-2">
        <div class="flex items-center justify-around h-16">
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center w-full gap-1 transition-all duration-300 {{ request()->routeIs('home') ? 'text-blue-600 -translate-y-1' : 'text-slate-400' }}">
                <i class="fa-solid fa-house-chimney text-lg"></i>
                <span class="text-[9px] font-black uppercase">Beranda</span>
            </a>
            <a href="{{ route('katalog') }}" class="flex flex-col items-center justify-center w-full gap-1 transition-all duration-300 {{ request()->routeIs('katalog*') ? 'text-blue-600 -translate-y-1' : 'text-slate-400' }}">
                <i class="fa-solid fa-compass text-lg"></i>
                <span class="text-[9px] font-black uppercase">Katalog</span>
            </a>
            <a href="{{ route('pelanggan.pesanan') }}" class="flex flex-col items-center justify-center w-full gap-1 transition-all duration-300 {{ request()->routeIs('pelanggan.pesanan*') ? 'text-blue-600 -translate-y-1' : 'text-slate-400' }}">
                <i class="fa-solid fa-receipt text-lg"></i>
                <span class="text-[9px] font-black uppercase">Tiket</span>
            </a>
            <a href="{{ auth('pelanggan')->check() ? route('pelanggan.profil') : route('pelanggan.login') }}" class="flex flex-col items-center justify-center w-full gap-1 transition-all duration-300 {{ request()->routeIs('pelanggan.profil*') || request()->routeIs('pelanggan.login') ? 'text-blue-600 -translate-y-1' : 'text-slate-400' }}">
                <i class="fa-solid fa-user text-lg"></i>
                <span class="text-[9px] font-black uppercase">{{ auth('pelanggan')->check() ? 'Profil' : 'Masuk' }}</span>
            </a>
        </div>
    </nav>

    {{-- Modern Minimal Footer --}}
    <footer class="bg-white border-t border-slate-100 pt-20 pb-10 hidden md:block mt-12">
        <div class="max-w-7xl mx-auto px-8 grid grid-cols-12 gap-12">
            <div class="col-span-4 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                        <i class="fa-solid fa-helmet-safety text-base"></i>
                    </div>
                    <span class="font-extrabold text-lg text-slate-900 tracking-tighter uppercase">Bayeng Putra</span>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed font-medium">
                    Store pickup experience terbaik di Kuningan. Kami memastikan setiap helm yang Anda pesan online terasa pas dan nyaman saat digunakan di jalan.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-span-2 space-y-5">
                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em]">Navigasi</h4>
                <ul class="space-y-3 text-xs font-bold text-slate-500">
                    <li><a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors">Katalog Helm</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">Cara Belanja</a></li>
                    <li><a href="{{ route('pelanggan.pesanan') }}" class="hover:text-blue-600 transition-colors">Cek E-Tiket</a></li>
                </ul>
            </div>
            <div class="col-span-2 space-y-5">
                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em]">Legal</h4>
                <ul class="space-y-3 text-xs font-bold text-slate-500">
                    <li><a href="#" class="hover:text-blue-600 transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-blue-600 transition-colors opacity-40">Admin Portal</a></li>
                </ul>
            </div>
            <div class="col-span-4 space-y-5">
                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em]">Lokasi Gerai</h4>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-3">
                    <div class="flex gap-3 text-xs font-bold text-slate-600">
                        <i class="fa-solid fa-map-marker-alt text-blue-600"></i>
                        <p>Jl. RE Martadinata No.49, Kuningan, Jawa Barat</p>
                    </div>
                    <div class="flex gap-3 text-xs font-bold text-slate-600">
                        <i class="fa-solid fa-clock text-blue-600"></i>
                        <p>08:00 - 21:00 WIB (Setiap Hari)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-8 border-t border-slate-100 mt-16 pt-8 flex justify-between items-center">
            <p class="text-[10px] font-black text-slate-400 tracking-[0.3em] uppercase">&copy; {{ date('Y') }} Bayeng Putra Helm</p>
            <div class="flex gap-6">
                <i class="fa-brands fa-cc-visa text-slate-300 text-xl"></i>
                <i class="fa-brands fa-cc-mastercard text-slate-300 text-xl"></i>
                <span class="text-[10px] font-black text-slate-300 tracking-widest">QRIS</span>
            </div>
        </div>
    </footer>

    {{-- SweetAlert2 Flash Notifications --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session('success') }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: '{{ session('error') }}', showConfirmButton: false, timer: 4000, timerProgressBar: true });
            });
        </script>
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>
