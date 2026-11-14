<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="min-h-full bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex flex-col justify-between relative overflow-x-hidden">

    {{-- Ambient Decorative Background Gradients --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-blue-500/10 to-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 right-10 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Minimal Top Navigation Bar (Just clean back-to-store link & minimal branding) --}}
    <header class="w-full px-6 py-5 max-w-6xl mx-auto flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-helmet-safety text-base"></i>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="font-extrabold text-sm tracking-tight text-slate-900">BAYENG PUTRA</span>
                <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 text-[9px] font-bold uppercase">HELM</span>
            </div>
        </a>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white/80 hover:bg-white text-xs font-semibold text-slate-600 hover:text-slate-900 shadow-sm transition-all">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </header>

    {{-- Main Centered Auth Container --}}
    <main class="flex-grow flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    {{-- Ultra Minimalist Single-Line Footer --}}
    <footer class="w-full py-5 text-center text-xs text-slate-400">
        <div class="flex items-center justify-center gap-3">
            <span class="flex items-center gap-1"><i class="fa-solid fa-shield-halved text-emerald-500 text-[10px]"></i> Transaksi 100% Aman</span>
            <span>&bull;</span>
            <span>&copy; {{ date('Y') }} Bayeng Putra Helm</span>
            <span>&bull;</span>
            <a href="{{ route('katalog') }}" class="hover:text-blue-600 transition-colors">Katalog Helm</a>
        </div>
    </footer>

    {{-- Flash Notifications via SweetAlert2 --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3500
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
                    timer: 4000
                });
            });
        </script>
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>
