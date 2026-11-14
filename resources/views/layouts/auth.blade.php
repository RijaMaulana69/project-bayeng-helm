<!DOCTYPE html>
<html lang="id" class="h-full">
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
<body class="min-h-full bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        {{-- Left Hero Visual Showcase (Hidden on Mobile, Displayed on Desktop & Tablet Landscape) --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-5/12 bg-slate-950 text-white relative overflow-hidden flex-col justify-between p-10 xl:p-14 select-none">
            {{-- Background Store Image with Dark Atmospheric Gradient --}}
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('assets/home.jpeg') }}" 
                     alt="Gerai Bayeng Putra Helm" 
                     class="w-full h-full object-cover object-center scale-105 filter brightness-75">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/50 backdrop-blur-[2px]"></div>
            </div>

            {{-- Top Branding --}}
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/30 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-helmet-safety text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-lg text-white tracking-tight">BAYENG PUTRA</span>
                            <span class="px-1.5 py-0.5 rounded bg-blue-500/30 text-blue-300 text-[10px] font-bold uppercase tracking-wider border border-blue-400/30">HELM</span>
                        </div>
                        <p class="text-xs text-slate-300 -mt-0.5">Toko Helm Resmi Kuningan</p>
                    </div>
                </a>
            </div>

            {{-- Center Selling Value Proposition --}}
            <div class="relative z-10 my-auto py-10 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-xs font-semibold backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    <span>Sistem Click & Collect Resmi</span>
                </div>

                <h2 class="text-3xl xl:text-4xl font-black text-white leading-tight tracking-tight">
                    Pesan Helm Favorit, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-amber-300">
                        Fitting Ukuran Langsung di Toko!
                    </span>
                </h2>

                <p class="text-slate-300 text-sm leading-relaxed max-w-md font-normal">
                    Tak perlu khawatir ukuran lingkar kepala salah. Pilih varian helm impian Anda, dapatkan e-Tiket pickup, dan coba langsung di gerai kami sebelum dibawa pulang.
                </p>

                {{-- Feature Bullet Points --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3 text-xs text-slate-200">
                        <div class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 border border-emerald-500/30">
                            <i class="fa-solid fa-shield-check"></i>
                        </div>
                        <span>100% Helm Original Berstandar SNI & DOT Resmi</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-200">
                        <div class="w-7 h-7 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center flex-shrink-0 border border-blue-500/30">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <span>e-Tiket Digital Cepat dengan Kode Verifikasi Kasir</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-200">
                        <div class="w-7 h-7 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0 border border-amber-500/30">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <span>Bebas Biaya Pengiriman (Pickup di Toko Kuningan)</span>
                    </div>
                </div>
            </div>

            {{-- Bottom Store Info & Trust --}}
            <div class="relative z-10 pt-6 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-blue-400"></i>
                    <span>Jl. RE Martadinata No.49, Kuningan</span>
                </div>
                <div class="flex items-center gap-1 text-amber-400 font-semibold">
                    <i class="fa-solid fa-star text-xs"></i>
                    <span>4.9 / 5.0 Rider Rating</span>
                </div>
            </div>
        </div>

        {{-- Right Side: Mobile-First Responsive Form Container --}}
        <div class="w-full lg:w-1/2 xl:w-7/12 min-h-screen flex flex-col justify-between bg-white px-5 sm:px-10 md:px-16 py-6 sm:py-8 lg:py-12">
            
            {{-- Mobile & Tablet Top Bar --}}
            <div class="flex items-center justify-between pb-6">
                {{-- Clean Back-to-Store Button --}}
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100/80 hover:bg-slate-200 transition-colors active:scale-95"
                   title="Kembali ke Beranda">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span class="hidden sm:inline">Kembali ke Beranda</span>
                    <span class="sm:hidden">Kembali</span>
                </a>

                {{-- Mobile Brand Badge (Visible on mobile/tablet, hidden on desktop left side) --}}
                <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs shadow-sm">
                        <i class="fa-solid fa-helmet-safety"></i>
                    </div>
                    <span class="font-extrabold text-xs tracking-tight text-slate-900">BAYENG HELM</span>
                </a>

                {{-- Quick Catalog Link --}}
                <a href="{{ route('katalog') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>Lihat Helm</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            {{-- Main Form Yield Content --}}
            <div class="w-full max-w-md mx-auto my-auto py-4">
                @yield('content')
            </div>

            {{-- Minimalist Mobile-Friendly Bottom Footer --}}
            <div class="pt-6 border-t border-slate-100 text-center text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} Bayeng Putra Helm. Hak cipta dilindungi.</p>
                <div class="flex items-center gap-3 text-[11px] text-slate-500">
                    <span class="flex items-center gap-1"><i class="fa-solid fa-lock text-emerald-600 text-[10px]"></i> SSL 256-Bit</span>
                    <span>&bull;</span>
                    <span>Toko Fisik Kuningan</span>
                </div>
            </div>

        </div>

    </div>

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
