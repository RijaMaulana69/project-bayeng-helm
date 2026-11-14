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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

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
<body class="min-h-full bg-white sm:bg-[#f8f9fa] text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex flex-col justify-between">

    {{-- Minimal Top Navigation (Tokopedia Style) --}}
    <header class="w-full border-b border-slate-100 sm:border-slate-200 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-helmet-safety text-base"></i>
                </div>
                <span class="font-extrabold text-base tracking-tight text-slate-900">BAYENG PUTRA HELM</span>
            </a>

            <div class="flex items-center gap-4 text-xs">
                <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Bayeng%20Putra%20Helm,%20saya%20butuh%20bantuan" 
                   target="_blank" 
                   class="text-slate-500 hover:text-blue-600 transition-colors flex items-center gap-1.5 font-medium">
                    <i class="fa-regular fa-circle-question text-sm text-slate-400"></i>
                    <span class="hidden sm:inline">Butuh bantuan?</span>
                </a>
                <a href="{{ route('home') }}" class="text-slate-500 hover:text-slate-800 transition-colors font-medium">
                    <i class="fa-solid fa-arrow-left text-[11px] mr-1"></i> Beranda
                </a>
            </div>
        </div>
    </header>

    {{-- Main Form Container --}}
    <main class="flex-grow flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full @yield('container-width', 'max-w-[430px]') mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- Minimal Clean Footer --}}
    <footer class="w-full border-t border-slate-100 sm:border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
        <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} Bayeng Putra Helm &bull; Sistem Click & Collect</p>
            <div class="flex items-center gap-4 text-slate-400 text-[11px]">
                <span class="flex items-center gap-1"><i class="fa-solid fa-shield-halved text-emerald-500"></i> 100% SNI & DOT Original</span>
                <span>&bull;</span>
                <span>Jl. RE Martadinata No.49, Kuningan</span>
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
