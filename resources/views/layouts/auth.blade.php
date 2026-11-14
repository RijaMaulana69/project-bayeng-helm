<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Autentikasi') - Bayeng Putra Helm</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

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
            -webkit-tap-highlight-color: transparent;
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="min-h-full flex flex-col justify-between bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    {{-- ========================================================
         SHOPEE-STYLE AUTH HEADER
         - Desktop: Brand Logo + | + "Masuk" / "Daftar" + "Butuh bantuan?"
         - Mobile: Native Mobile App Bar with Back Arrow + Centered Title
         ======================================================== --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        {{-- Desktop Header (Hidden on Mobile) --}}
        <div class="hidden sm:flex max-w-6xl mx-auto px-4 sm:px-6 h-20 items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-600/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-helmet-safety text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1">
                            <span class="font-black text-lg tracking-tight text-slate-900">BAYENG PUTRA</span>
                            <span class="px-1 py-0.2 rounded bg-blue-100 text-blue-700 text-[9px] font-black uppercase">HELM</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium -mt-1">Click & Collect Store</span>
                    </div>
                </a>

                {{-- Shopee-style Vertical Separator & Action Title --}}
                <div class="h-7 w-[1.5px] bg-slate-200 mx-1"></div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                    @yield('auth-heading', 'Masuk')
                </h1>
            </div>

            <div class="flex items-center gap-4 text-xs font-semibold">
                <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Bayeng%20Putra%20Helm,%20saya%20butuh%20bantuan" 
                   target="_blank" 
                   class="text-blue-600 hover:text-blue-700 flex items-center gap-1.5 transition-colors">
                    <i class="fa-regular fa-circle-question text-sm"></i>
                    <span>Butuh bantuan?</span>
                </a>
            </div>
        </div>

        {{-- Mobile App Top Bar (Hidden on Desktop) --}}
        <div class="sm:hidden flex items-center justify-between px-4 h-14 bg-white">
            <a href="{{ route('home') }}" 
               class="w-10 h-10 -ml-2 rounded-full flex items-center justify-center text-slate-700 active:bg-slate-100 transition-colors"
               title="Kembali ke Beranda">
                <i class="fa-solid fa-arrow-left text-base"></i>
            </a>

            <div class="font-bold text-base text-slate-900 tracking-tight">
                @yield('auth-heading', 'Masuk')
            </div>

            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Bayeng%20Putra%20Helm,%20saya%20butuh%20bantuan" 
               target="_blank" 
               class="w-10 h-10 -mr-2 rounded-full flex items-center justify-center text-blue-600 active:bg-slate-100 transition-colors"
               title="Bantuan WhatsApp">
                <i class="fa-regular fa-circle-question text-lg"></i>
            </a>
        </div>
    </header>

    {{-- ========================================================
         MAIN BODY CONTENT
         - Desktop: Branded Blue Banner background with Left Promo + Right Elevated Card
         - Mobile: Clean White Full-Fluid Screen
         ======================================================== --}}
    <main class="flex-grow bg-slate-50 sm:bg-gradient-to-r sm:from-blue-700 sm:via-blue-600 sm:to-indigo-700 py-0 sm:py-12 lg:py-16 relative overflow-hidden">
        
        {{-- Ambient Backdrop Glow on Desktop --}}
        <div class="hidden sm:block absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-400 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-6xl mx-auto px-0 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-12">
                
                {{-- Left Promotional Banner (Visible on Desktop / Tablets, Hidden on Mobile) --}}
                <div class="hidden lg:flex flex-col justify-center text-white flex-1 max-w-lg space-y-6 select-none pl-4">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-blue-100">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Click & Collect #1 di Kuningan</span>
                    </div>

                    <h2 class="text-3xl xl:text-4xl font-black leading-tight tracking-tight">
                        Pesan Helm Favorit Online, <br>
                        <span class="text-amber-300">Fitting Langsung di Toko!</span>
                    </h2>

                    <p class="text-sm text-blue-100 leading-relaxed font-normal">
                        Bebas cemas salah ukuran lingkar kepala. Pesan model helm impian Anda, dapatkan e-Tiket, dan coba langsung di gerai kami sebelum dibawa pulang.
                    </p>

                    {{-- Shopee-style Feature Highlights --}}
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-1">
                            <div class="w-8 h-8 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center text-sm font-bold">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h3 class="text-xs font-bold text-white">100% Original</h3>
                            <p class="text-[11px] text-blue-200">SNI & Standar DOT Resmi</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-1">
                            <div class="w-8 h-8 rounded-xl bg-emerald-400/20 text-emerald-300 flex items-center justify-center text-sm font-bold">
                                <i class="fa-solid fa-store"></i>
                            </div>
                            <h3 class="text-xs font-bold text-white">Bebas Ongkir</h3>
                            <p class="text-[11px] text-blue-200">Ambil di Gerai Kuningan</p>
                        </div>
                    </div>
                </div>

                {{-- Right Form Side (Full fluid on Mobile, Elevated Card on Desktop) --}}
                <div class="w-full lg:w-[430px] xl:w-[450px] flex-shrink-0">
                    <div class="bg-white sm:rounded-3xl sm:shadow-2xl sm:border sm:border-slate-100 px-5 py-7 sm:p-9">
                        @yield('content')
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- ========================================================
         SHOPEE-STYLE E-COMMERCE FOOTER
         ======================================================== --}}
    <footer class="bg-white border-t border-slate-200 text-slate-500 text-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
            
            {{-- Top Footer Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 pb-8 border-b border-slate-100 text-xs">
                
                {{-- Col 1: Layanan Pelanggan --}}
                <div class="space-y-2.5">
                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Layanan Pelanggan</h4>
                    <ul class="space-y-2 text-slate-600">
                        <li><a href="https://wa.me/6281234567890" target="_blank" class="hover:text-blue-600 transition-colors">Bantuan WhatsApp</a></li>
                        <li><a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors">Cara Belanja & Ambil</a></li>
                        <li><a href="{{ route('pelanggan.pesanan') }}" class="hover:text-blue-600 transition-colors">Lacak Status Pesanan</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Garansi Ukuran Helm</a></li>
                    </ul>
                </div>

                {{-- Col 2: Jelajahi Toko --}}
                <div class="space-y-2.5">
                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Jelajahi Toko</h4>
                    <ul class="space-y-2 text-slate-600">
                        <li><a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda Utama</a></li>
                        <li><a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors">Katalog Helm Motor</a></li>
                        <li><a href="{{ route('pelanggan.cart') }}" class="hover:text-blue-600 transition-colors">Keranjang Belanja</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-blue-600 transition-colors text-[11px] text-slate-400">Portal Staf / Kasir</a></li>
                    </ul>
                </div>

                {{-- Col 3: Pembayaran Resmi --}}
                <div class="col-span-2 space-y-3">
                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Metode Pembayaran & Keamanan</h4>
                    <p class="text-[11px] text-slate-500">Mendukung pembayaran langsung di toko kasir, QRIS instan, dan transfer bank resmi:</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700">Bayar di Toko (COD)</span>
                        <span class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700">QRIS Instan</span>
                        <span class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700">BCA</span>
                        <span class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700">Mandiri</span>
                        <span class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-700">BRI</span>
                    </div>
                </div>

            </div>

            {{-- Bottom Copyright Strip --}}
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-400 text-[11px]">
                <p>&copy; {{ date('Y') }} Bayeng Putra Helm. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1 text-slate-500"><i class="fa-solid fa-location-dot text-blue-600 text-xs"></i> Gerai Fisik Kuningan, Jawa Barat</span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1 text-emerald-600 font-semibold"><i class="fa-solid fa-shield-halved text-xs"></i> 256-Bit SSL Enkripsi</span>
                </div>
            </div>

        </div>
    </footer>

    {{-- SweetAlert2 Flash Notifications --}}
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

    @livewireScripts
    @stack('scripts')
</body>
</html>
