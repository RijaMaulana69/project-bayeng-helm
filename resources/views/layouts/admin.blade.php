<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Bayeng Putra Helm Admin</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Tailwind CSS --}}
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
                            900: '#1e3a8a',
                            950: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .sidebar-item-active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0) 100%);
            border-right: 3px solid #2563eb;
            color: #2563eb !important;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white" 
      x-data="{ 
        sidebarOpen: window.innerWidth > 1024,
        mobileSidebar: false,
        userDropdown: false
      }"
      @resize.window="sidebarOpen = window.innerWidth > 1024">

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="mobileSidebar" 
         x-cloak
         @click="mobileSidebar = false"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[60] lg:hidden transition-opacity duration-300"
         x-transition:enter="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 bottom-0 z-[70] w-72 bg-white border-r border-slate-100 transition-transform duration-300 lg:translate-x-0 flex flex-col"
           :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        {{-- Sidebar Logo --}}
        <div class="h-20 flex items-center px-8 border-b border-slate-50">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-helmet-safety text-base"></i>
                </div>
                <div>
                    <span class="font-extrabold text-sm text-slate-900 tracking-tight block leading-none">BAYENG PUTRA</span>
                    <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">ADMIN PORTAL</span>
                </div>
            </a>
        </div>

        {{-- Sidebar Menu --}}
        <nav class="flex-grow overflow-y-auto hide-scrollbar py-6 px-4 space-y-8">
            {{-- Main Menu --}}
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Main Navigation</p>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-grid-2 text-base {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.pickup.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.pickup*') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-qrcode text-base {{ request()->routeIs('admin.pickup*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Validasi Pickup</span>
                </a>

                <a href="{{ route('admin.pesanan.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.pesanan*') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-receipt text-base {{ request()->routeIs('admin.pesanan*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Pesanan Online</span>
                </a>
            </div>

            {{-- Inventory --}}
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Inventory</p>
                
                <a href="{{ route('admin.produk.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.produk*') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-helmet-safety text-base {{ request()->routeIs('admin.produk*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Produk Helm</span>
                </a>

                <a href="{{ route('admin.stok.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.stok*') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-boxes-stacked text-base {{ request()->routeIs('admin.stok*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Stok Masuk</span>
                </a>

                <div x-data="{ open: {{ request()->routeIs('admin.merk*') || request()->routeIs('admin.kategori*') || request()->routeIs('admin.warna*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-all group">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-layer-group text-base text-slate-400 group-hover:text-slate-600"></i>
                            <span>Master Data</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-11 pr-4 py-1 space-y-1">
                        <a href="{{ route('admin.merk.index') }}" class="block py-2 text-xs font-bold transition-colors {{ request()->routeIs('admin.merk*') ? 'text-blue-600' : 'text-slate-500 hover:text-blue-600' }}">Merk Helm</a>
                        <a href="{{ route('admin.kategori.index') }}" class="block py-2 text-xs font-bold transition-colors {{ request()->routeIs('admin.kategori*') ? 'text-blue-600' : 'text-slate-500 hover:text-blue-600' }}">Kategori</a>
                        <a href="{{ route('admin.warna.index') }}" class="block py-2 text-xs font-bold transition-colors {{ request()->routeIs('admin.warna*') ? 'text-blue-600' : 'text-slate-500 hover:text-blue-600' }}">Warna</a>
                    </div>
                </div>
            </div>

            {{-- Owner Settings --}}
            @if(Auth::guard('web')->user()->isOwner())
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Owner Control</p>
                
                <a href="{{ route('admin.owner.user.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.owner.user*') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-users-gear text-base {{ request()->routeIs('admin.owner.user*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Kelola Karyawan</span>
                </a>

                <a href="{{ route('admin.owner.laporan.penjualan') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.owner.laporan*') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-file-invoice-dollar text-base {{ request()->routeIs('admin.owner.laporan*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Laporan Penjualan</span>
                </a>

                <a href="{{ route('admin.owner.activity-log') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group {{ request()->routeIs('admin.owner.activity-log') ? 'sidebar-item-active' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-clock-rotate-left text-base {{ request()->routeIs('admin.owner.activity-log') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>Activity Log</span>
                </a>
            </div>
            @endif
        </nav>

        {{-- Sidebar Footer --}}
        <div class="p-6 border-t border-slate-50">
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs font-black uppercase">
                    {{ substr(Auth::guard('web')->user()->nama_lengkap, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::guard('web')->user()->nama_lengkap }}</p>
                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">{{ Auth::guard('web')->user()->role->nama_role }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="transition-all duration-300 lg:ml-72 flex flex-col min-h-screen">
        
        {{-- Topbar --}}
        <header class="sticky top-0 z-50 h-20 glass-panel border-b border-slate-100 px-6 lg:px-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="mobileSidebar = true" class="lg:hidden p-2 text-slate-500 hover:text-blue-600">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
                <div class="hidden sm:block">
                    <h1 class="text-lg font-black text-slate-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Admin</a>
                        <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        <span class="text-slate-500">@yield('page-title', 'Dashboard')</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 md:gap-5">
                <a href="{{ route('home') }}" target="_blank" class="hidden md:flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-blue-600"></i>
                    <span>Lihat Toko</span>
                </a>

                <div class="relative" @click.outside="userDropdown = false">
                    <button @click="userDropdown = !userDropdown" class="flex items-center gap-2 p-1 rounded-full hover:bg-white transition-colors">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-black border border-blue-100">
                            {{ substr(Auth::guard('web')->user()->nama_lengkap, 0, 1) }}
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 pr-1"></i>
                    </button>

                    <div x-show="userDropdown" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="absolute right-0 mt-3 w-56 bg-white rounded-[1.5rem] shadow-2xl shadow-slate-300/60 border border-slate-100 py-2.5 overflow-hidden z-[80]">
                        <div class="px-4 py-2 border-b border-slate-50 mb-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Portal Admin</p>
                            <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::guard('web')->user()->nama_lengkap }}</p>
                        </div>
                        <a href="{{ route('admin.profil') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fa-regular fa-user-circle text-sm text-slate-400"></i> Edit Profil
                        </a>
                        <hr class="my-1.5 border-slate-50">
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content Area --}}
        <main class="p-6 lg:p-10 flex-grow">
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mb-6 flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 shadow-sm shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <p class="text-xs font-bold">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 flex items-center justify-between p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 shadow-sm shadow-rose-500/10">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <p class="text-xs font-bold">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="px-6 lg:px-10 py-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase">&copy; {{ date('Y') }} Bayeng Putra Admin Portal</p>
            <div class="flex items-center gap-6 text-[10px] font-bold text-slate-400">
                <a href="#" class="hover:text-blue-600 transition-colors uppercase tracking-widest">Docs</a>
                <a href="#" class="hover:text-blue-600 transition-colors uppercase tracking-widest">Support</a>
                <span class="text-slate-200">|</span>
                <span class="text-slate-400 uppercase tracking-widest">v2.0.0-gold</span>
            </div>
        </footer>
    </div>

    {{-- SweetAlert2 Flash Notifications (Toast) --}}
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
