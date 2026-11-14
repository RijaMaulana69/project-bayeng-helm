<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bayeng Putra Helm') - E-Commerce Helm Click & Collect</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Tailwind CSS & Assets via Vite & CDN Fallback --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        bayeng: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ticket-cutout-left {
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background-color: #f1f5f9;
            border-radius: 9999px;
        }
        .ticket-cutout-right {
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background-color: #f1f5f9;
            border-radius: 9999px;
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileMenu: false }">

    {{-- Top Announcement Bar --}}
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2 text-center sm:text-left">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 font-semibold text-[10px] uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-ping"></span>
                    Click & Collect
                </span>
                <span>Pesan Online, Ambil & Fitting Ukuran Langsung di Toko Fisik!</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-slate-400">
                <span class="flex items-center gap-1"><i class="fa-solid fa-location-dot text-blue-400"></i> Jl. RE Martadinata No.49, Kuningan</span>
                <span class="hidden md:inline text-slate-600">•</span>
                <span class="hidden md:flex items-center gap-1"><i class="fa-solid fa-shield-halved text-emerald-400"></i> 100% Original & Bergaransi</span>
            </div>
        </div>
    </div>

    {{-- Sticky Glassmorphism Header --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                {{-- Brand Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-helmet-safety text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-lg text-slate-900 tracking-tight">BAYENG PUTRA</span>
                            <span class="px-1.5 py-0.5 rounded-md bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-wider">HELM</span>
                        </div>
                        <p class="text-[11px] text-slate-500 -mt-0.5 font-medium">Store Pick-up Experience</p>
                    </div>
                </a>

                {{-- Desktop Navigation --}}
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa-solid fa-house-chimney text-xs mr-1.5 opacity-70"></i> Beranda
                    </a>
                    <a href="{{ route('katalog') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('katalog*') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa-solid fa-compass text-xs mr-1.5 opacity-70"></i> Katalog Helm
                    </a>
                </nav>

                {{-- Action Buttons & Auth State --}}
                <div class="flex items-center gap-3">
                    @auth('pelanggan')
                        {{-- Shopping Cart Button --}}
                        <a href="{{ route('pelanggan.cart') }}" class="relative group" title="Keranjang Belanja">
                            @livewire('cart-counter')
                        </a>

                        {{-- User Dropdown with Alpine.js --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" 
                                    class="flex items-center gap-2.5 p-1.5 pr-3 rounded-full border border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm transition-all text-left">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-slate-800 to-slate-700 text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                    {{ substr(Auth::guard('pelanggan')->user()->nama, 0, 1) }}
                                </div>
                                <span class="hidden sm:inline font-semibold text-xs text-slate-800 max-w-[120px] truncate">
                                    {{ Auth::guard('pelanggan')->user()->nama }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" :class="{'rotate-180': open}"></i>
                            </button>

                            {{-- Dropdown Panel --}}
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 py-2 z-50">
                                
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-[11px] font-medium text-slate-400">Masuk sebagai</p>
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::guard('pelanggan')->user()->nama }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ Auth::guard('pelanggan')->user()->email }}</p>
                                </div>

                                <a href="{{ route('pelanggan.pesanan') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                    <i class="fa-solid fa-receipt text-slate-400 text-sm w-4"></i> Pesanan & E-Tiket
                                </a>
                                <a href="{{ route('pelanggan.profil') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                    <i class="fa-solid fa-user-gear text-slate-400 text-sm w-4"></i> Pengaturan Akun
                                </a>

                                <div class="border-t border-slate-100 my-1"></div>

                                <form action="{{ route('pelanggan.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-rose-400 text-sm w-4"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Guest Buttons --}}
                        <a href="{{ route('pelanggan.login') }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-blue-600 hover:bg-slate-100/80 transition-all">
                            Masuk
                        </a>
                        <a href="{{ route('pelanggan.register') }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/20 active:scale-95 transition-all">
                            <span>Daftar</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @endauth

                    {{-- Mobile Hamburger --}}
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100">
                        <i class="fa-solid text-lg" :class="mobileMenu ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile Drawer --}}
        <div x-show="mobileMenu" class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-house-chimney text-xs mr-2 text-blue-600"></i> Beranda
            </a>
            <a href="{{ route('katalog') }}" class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-compass text-xs mr-2 text-blue-600"></i> Katalog Helm
            </a>
            @guest('pelanggan')
                <div class="pt-3 border-t border-slate-100 flex gap-2">
                    <a href="{{ route('pelanggan.login') }}" class="flex-1 text-center py-2.5 rounded-xl text-xs font-bold border border-slate-200 text-slate-700">Masuk</a>
                    <a href="{{ route('pelanggan.register') }}" class="flex-1 text-center py-2.5 rounded-xl text-xs font-bold bg-blue-600 text-white">Daftar</a>
                </div>
            @endguest
        </div>
    </header>

    {{-- SweetAlert Flash Message Handler --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    {{-- Main Page Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Modern Footer --}}
    <footer class="bg-slate-950 text-slate-400 border-t border-slate-800 pt-16 pb-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                
                {{-- Col 1: Brand & USP --}}
                <div class="md:col-span-1 space-y-4">
                    <div class="flex items-center gap-2.5 text-white">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-helmet-safety text-lg"></i>
                        </div>
                        <span class="font-extrabold text-base tracking-tight">BAYENG PUTRA HELM</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pusat perlengkapan helm original berkualitas tinggi di Kuningan. Nikmati kemudahan pesan online dengan garansi pas fitting ukuran langsung di gerai fisik kami.
                    </p>
                    <div class="flex items-center gap-3 text-slate-400 text-sm pt-2">
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-colors"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-colors"><i class="fa-brands fa-facebook"></i></a>
                    </div>
                </div>

                {{-- Col 2: Alur Click & Collect --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Alur Ambil di Toko</h4>
                    <ul class="text-xs space-y-2.5">
                        <li class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-4 h-4 rounded-full bg-blue-500/20 text-blue-400 font-bold text-[10px] flex items-center justify-center">1</span>
                            <span>Pilih varian warna & ukuran helm</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-4 h-4 rounded-full bg-blue-500/20 text-blue-400 font-bold text-[10px] flex items-center justify-center">2</span>
                            <span>Checkout & dapatkan e-Tiket Pickup</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-4 h-4 rounded-full bg-blue-500/20 text-blue-400 font-bold text-[10px] flex items-center justify-center">3</span>
                            <span>Kunjungi gerai toko & fitting helm</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-4 h-4 rounded-full bg-blue-500/20 text-blue-400 font-bold text-[10px] flex items-center justify-center">4</span>
                            <span>Kasir memvalidasi tiket & bawa pulang</span>
                        </li>
                    </ul>
                </div>

                {{-- Col 3: Link Cepat --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Navigasi</h4>
                    <ul class="text-xs space-y-2">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('katalog') }}" class="hover:text-white transition-colors">Katalog Koleksi Helm</a></li>
                        <li><a href="{{ route('pelanggan.cart') }}" class="hover:text-white transition-colors">Keranjang Belanja</a></li>
                        <li><a href="{{ route('pelanggan.pesanan') }}" class="hover:text-white transition-colors">Lacak Pesanan Saya</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-blue-400 transition-colors text-[11px] text-slate-500">Portal Kasir / Owner</a></li>
                    </ul>
                </div>

                {{-- Col 4: Lokasi & Kontak Toko --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Lokasi Toko Fisik</h4>
                    <div class="bg-slate-900/80 p-3.5 rounded-2xl border border-slate-800 space-y-2">
                        <div class="flex items-start gap-2 text-xs">
                            <i class="fa-solid fa-location-dot text-blue-500 mt-0.5"></i>
                            <div>
                                <p class="font-semibold text-slate-200">Gerai Bayeng Putra Helm</p>
                                <p class="text-[11px] text-slate-400">Jl. RE Martadinata No.49, Ancaran, Kuningan, Jawa Barat</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-xs pt-1 border-t border-slate-800">
                            <i class="fa-solid fa-clock text-amber-400"></i>
                            <span class="text-[11px] text-slate-300">Buka Setiap Hari: 08.00 - 21.00 WIB</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Bottom copyright --}}
            <div class="border-t border-slate-800/80 mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Bayeng Putra Helm. Seluruh hak cipta dilindungi.</p>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-lock text-emerald-500 text-xs"></i> 256-Bit SSL Enkripsi Transaksi</span>
                    <span>•</span>
                    <span class="text-[11px]">Click & Collect System v2.0</span>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
