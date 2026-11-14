<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Bayeng Admin</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- CSS & JS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; 
            -webkit-font-smoothing: antialiased;
        }
        [x-cloak] { display: none !important; }
        
        /* Modern Slim Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Navigation active state */
        .sidebar-active {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }
        .sidebar-active i {
            color: #2563eb;
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="h-full text-slate-700 antialiased" x-data="{ mobileDrawer: false }">

    @php
        $authUser = Auth::guard('web')->user();
        $isOwner = $authUser ? $authUser->isOwner() : false;
        $isKaryawan = $authUser ? $authUser->isKaryawan() : false;
        $roleLabel = $isOwner ? 'Pemilik Toko' : 'Kasir Gerai';
        $roleBadgeBg = $isOwner ? 'bg-indigo-50 text-indigo-700 border-indigo-200/80' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
    @endphp

    <div class="flex h-screen overflow-hidden bg-[#f8fafc]">
        
        {{-- ======================================================== --}}
        {{-- 1. DESKTOP SIDEBAR (Visible on lg: >= 1024px)             --}}
        {{-- ======================================================== --}}
        <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-200/90 shrink-0">
            
            {{-- Brand Section --}}
            <div class="h-[60px] px-5 flex items-center justify-between border-b border-slate-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-2xs">
                        <i class="fa-solid fa-helmet-safety text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-extrabold tracking-tight text-slate-900 leading-none">
                            BAYENG<span class="text-blue-600">PRO</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium tracking-tight">Sistem Kasir & Toko</span>
                    </div>
                </a>
            </div>

            {{-- Navigation Links --}}
            <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">
                
                {{-- Group 1: Operasional Gerai --}}
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                        Operasional Gerai
                    </p>

                    {{-- Ringkasan / Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-chart-pie text-slate-400 group-hover:text-slate-600"></i>
                            <span>{{ $isOwner ? 'Ringkasan Bisnis' : 'Ringkasan Kasir' }}</span>
                        </div>
                    </a>

                    {{-- Kasir Pickup (BOPIS) --}}
                    <a href="{{ route('admin.pickup.index') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.pickup*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-qrcode text-slate-400 group-hover:text-slate-600"></i>
                            <span>Kasir Pickup</span>
                        </div>
                        @if(($nav_pickup_count ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                {{ $nav_pickup_count }}
                            </span>
                        @endif
                    </a>

                    {{-- Pesanan Online --}}
                    <a href="{{ route('admin.pesanan.index') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.pesanan*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-receipt text-slate-400 group-hover:text-slate-600"></i>
                            <span>Pesanan Masuk</span>
                        </div>
                        @if(($nav_pending_count ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                {{ $nav_pending_count }}
                            </span>
                        @endif
                    </a>

                    {{-- Update Stok Helm --}}
                    <a href="{{ route('admin.stok.index') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.stok*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-boxes-stacked text-slate-400 group-hover:text-slate-600"></i>
                            <span>Stok Fisik & Restok</span>
                        </div>
                        @if(($nav_critical_stock_count ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700" title="{{ $nav_critical_stock_count }} varian menipis/habis">
                                {{ $nav_critical_stock_count }}
                            </span>
                        @endif
                    </a>

                    {{-- Katalog Helm --}}
                    <a href="{{ route('admin.produk.index') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.produk*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-helmet-safety text-slate-400 group-hover:text-slate-600"></i>
                            <span>Katalog Helm</span>
                        </div>
                    </a>

                    {{-- Master Data Atribut (Merk, Kategori, Warna) --}}
                    <div class="pt-1.5 pl-6 space-y-0.5">
                        <a href="{{ route('admin.merk.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] rounded-lg transition-colors {{ request()->routeIs('admin.merk*') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.merk*') ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                            <span>Merk</span>
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] rounded-lg transition-colors {{ request()->routeIs('admin.kategori*') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.kategori*') ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                            <span>Kategori</span>
                        </a>
                        <a href="{{ route('admin.warna.index') }}" 
                           class="flex items-center gap-2 px-2.5 py-1.5 text-[11px] rounded-lg transition-colors {{ request()->routeIs('admin.warna*') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.warna*') ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                            <span>Warna</span>
                        </a>
                    </div>
                </div>

                {{-- Group 2: Manajemen Bisnis (HANYA MUNCUL JIKA OWNER) --}}
                @if($isOwner)
                <div class="space-y-1 pt-3 border-t border-slate-100">
                    <p class="px-3 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                        Manajemen Bisnis (Owner)
                    </p>

                    {{-- Manajemen Karyawan --}}
                    <a href="{{ route('admin.owner.user.index') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.owner.user*') || request()->routeIs('admin.user*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-4 text-center fa-solid fa-users-gear text-slate-400 group-hover:text-slate-600"></i>
                        <span>Kelola Karyawan</span>
                    </a>

                    {{-- Laporan Penjualan --}}
                    <a href="{{ route('admin.owner.laporan.penjualan') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.owner.laporan*') || request()->routeIs('admin.laporan*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-4 text-center fa-solid fa-file-invoice-dollar text-slate-400 group-hover:text-slate-600"></i>
                        <span>Laporan Penjualan</span>
                    </a>

                    {{-- Audit Activity Log --}}
                    <a href="{{ route('admin.owner.activity-log') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.owner.activity-log*') || request()->routeIs('admin.activity-log*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-4 text-center fa-solid fa-clock-rotate-left text-slate-400 group-hover:text-slate-600"></i>
                        <span>Audit Log Aktivitas</span>
                    </a>

                    {{-- Pengaturan Toko --}}
                    <a href="{{ route('admin.owner.pengaturan') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.owner.pengaturan*') || request()->routeIs('admin.pengaturan*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-4 text-center fa-solid fa-sliders text-slate-400 group-hover:text-slate-600"></i>
                        <span>Pengaturan Toko</span>
                    </a>
                </div>
                @endif

            </nav>

            {{-- User Profile Footer --}}
            @if($authUser)
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($authUser->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate leading-tight">{{ $authUser->nama_lengkap }}</p>
                            <span class="inline-block text-[10px] font-semibold px-1.5 py-0.2 rounded border {{ $roleBadgeBg }}">
                                {{ $roleLabel }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors" title="Logout">
                            <i class="fa-solid fa-power-off text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </aside>

        {{-- ======================================================== --}}
        {{-- 2. MAIN COLUMN (Topbar + Content + Mobile Bottom Nav)    --}}
        {{-- ======================================================== --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            
            {{-- Topbar Header --}}
            <header class="z-20 h-[60px] bg-white border-b border-slate-200/90 px-4 lg:px-8 flex items-center justify-between shrink-0">
                
                {{-- Left: Mobile Brand & Page Title --}}
                <div class="flex items-center gap-3">
                    {{-- Mobile Drawer Trigger --}}
                    <button type="button" 
                            @click="mobileDrawer = true" 
                            class="lg:hidden w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition-colors">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-sm font-bold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Toko Aktif</span>
                            </span>
                        </div>
                        <p class="hidden md:block text-[11px] text-slate-400">Bayeng Putra Helm &bull; {{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                {{-- Right: Quick Pickup Search + Actions --}}
                <div class="flex items-center gap-2.5">
                    {{-- Inline Quick Pickup Input (Desktop & Tablet) --}}
                    <form action="{{ route('admin.pickup.validasi') }}" method="POST" class="hidden md:flex items-center gap-1.5">
                        @csrf
                        <div class="relative">
                            <i class="fa-solid fa-qrcode absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" 
                                   name="kode_pickup" 
                                   placeholder="Scan / Kode PKP-..." 
                                   required 
                                   autocomplete="off"
                                   class="w-44 lg:w-48 h-8 pl-7 pr-2.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-blue-500 focus:outline-none uppercase font-mono tracking-wider transition-all placeholder:normal-case placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400">
                        </div>
                        <button type="submit" 
                                class="h-8 px-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold inline-flex items-center gap-1 shadow-2xs transition-colors cursor-pointer">
                            <span>Validasi</span>
                        </button>
                    </form>

                    {{-- Quick Kasir Pickup Button --}}
                    <a href="{{ route('admin.pickup.index') }}" 
                       class="h-8 px-2.5 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-600 border border-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors relative"
                       title="Buka Kasir Pickup">
                        <i class="fa-solid fa-qrcode text-xs text-blue-600"></i>
                        <span class="hidden sm:inline">Kasir Pickup</span>
                        @if(($nav_pickup_count ?? 0) > 0)
                            <span class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        @endif
                    </a>

                    {{-- Link to Store Frontend --}}
                    <a href="{{ route('home') }}" target="_blank" 
                       class="h-8 px-2.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-medium inline-flex items-center gap-1.5 transition-colors"
                       title="Buka Toko Online">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400"></i>
                        <span class="hidden sm:inline">Toko</span>
                    </a>

                    {{-- Profile Menu (Desktop) --}}
                    @if($authUser)
                    <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-slate-200">
                        <a href="{{ route('admin.profil') }}" class="text-right">
                            <span class="text-xs font-semibold text-slate-800 block leading-tight">{{ $authUser->nama_lengkap }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $roleLabel }}</span>
                        </a>
                    </div>
                    @endif
                </div>

            </header>

            {{-- Main Scrollable Content --}}
            <main class="flex-1 overflow-y-auto bg-[#f8fafc] p-4 lg:p-8 pb-24 lg:pb-8">
                <div class="max-w-6xl mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

    {{-- ======================================================== --}}
    {{-- 3. MOBILE BOTTOM NAVIGATION BAR (Fixed on lg:hidden)      --}}
    {{-- ======================================================== --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-lg px-2 py-1 flex items-center justify-around">
        
        {{-- 1. Kasir Pickup (Action-focused) --}}
        <a href="{{ route('admin.pickup.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-colors relative {{ request()->routeIs('admin.pickup*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <div class="relative">
                <i class="fa-solid fa-qrcode text-base"></i>
                @if(($nav_pickup_count ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-3 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-500 text-white ring-2 ring-white">
                        {{ $nav_pickup_count }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] mt-0.5">Pickup</span>
        </a>

        {{-- 2. Pesanan Online --}}
        <a href="{{ route('admin.pesanan.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-colors relative {{ request()->routeIs('admin.pesanan*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <div class="relative">
                <i class="fa-solid fa-receipt text-base"></i>
                @if(($nav_pending_count ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-3 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-500 text-white ring-2 ring-white">
                        {{ $nav_pending_count }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] mt-0.5">Pesanan</span>
        </a>

        {{-- 3. Ringkasan / Dashboard (Center) --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-chart-pie text-base"></i>
            <span class="text-[10px] mt-0.5">{{ $isOwner ? 'Ringkasan' : 'Kasir' }}</span>
        </a>

        {{-- 4. Stok Helm --}}
        <a href="{{ route('admin.stok.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-colors relative {{ request()->routeIs('admin.stok*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <div class="relative">
                <i class="fa-solid fa-boxes-stacked text-base"></i>
                @if(($nav_critical_stock_count ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-3 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-rose-500 text-white ring-2 ring-white">
                        {{ $nav_critical_stock_count }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] mt-0.5">Stok</span>
        </a>

        {{-- 5. Menu Drawer Trigger --}}
        <button type="button" 
                @click="mobileDrawer = true" 
                class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fa-solid fa-bars text-base"></i>
            <span class="text-[10px] mt-0.5">Menu</span>
        </button>

    </nav>

    {{-- ======================================================== --}}
    {{-- 4. MOBILE SLIDE-OVER DRAWER (Offcanvas for extra items)  --}}
    {{-- ======================================================== --}}
    <div x-show="mobileDrawer" 
         x-cloak 
         class="relative z-50 lg:hidden" 
         role="dialog" 
         aria-modal="true">
        
        {{-- Backdrop --}}
        <div x-show="mobileDrawer" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" 
             @click="mobileDrawer = false"></div>

        <div class="fixed inset-0 flex justify-end">
            <div x-show="mobileDrawer" 
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="relative ml-auto w-full max-w-xs bg-white h-full shadow-2xl flex flex-col justify-between overflow-y-auto">
                
                {{-- Drawer Header --}}
                <div>
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                                <i class="fa-solid fa-helmet-safety"></i>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold text-slate-900 leading-tight">BAYENG PRO</p>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $roleLabel }}</span>
                            </div>
                        </div>
                        <button type="button" @click="mobileDrawer = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    {{-- User Profile Card --}}
                    @if($authUser)
                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-slate-800 text-white font-bold text-sm flex items-center justify-center">
                                {{ strtoupper(substr($authUser->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $authUser->nama_lengkap }}</p>
                                <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded border {{ $roleBadgeBg }}">
                                    {{ $roleLabel }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('admin.profil') }}" class="text-xs text-blue-600 font-medium hover:underline">
                            Profil
                        </a>
                    </div>
                    @endif

                    {{-- Drawer Navigation --}}
                    <div class="p-4 space-y-4 text-xs">
                        {{-- Katalog & Atribut --}}
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Katalog & Atribut</p>
                            <a href="{{ route('admin.produk.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-box-archive text-slate-400 w-4"></i>
                                <span>Katalog Helm</span>
                            </a>
                            <a href="{{ route('admin.merk.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-tags text-slate-400 w-4"></i>
                                <span>Merk Helm</span>
                            </a>
                            <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-layer-group text-slate-400 w-4"></i>
                                <span>Kategori</span>
                            </a>
                            <a href="{{ route('admin.warna.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-palette text-slate-400 w-4"></i>
                                <span>Warna</span>
                            </a>
                        </div>

                        {{-- Menu Khusus Owner --}}
                        @if($isOwner)
                        <div class="space-y-1 pt-3 border-t border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Manajemen Bisnis (Owner)</p>
                            <a href="{{ route('admin.owner.user.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-users-gear text-slate-400 w-4"></i>
                                <span>Kelola Karyawan</span>
                            </a>
                            <a href="{{ route('admin.owner.laporan.penjualan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-file-invoice-dollar text-slate-400 w-4"></i>
                                <span>Laporan Penjualan</span>
                            </a>
                            <a href="{{ route('admin.owner.activity-log') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-clock-rotate-left text-slate-400 w-4"></i>
                                <span>Audit Log Aktivitas</span>
                            </a>
                            <a href="{{ route('admin.owner.pengaturan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-sliders text-slate-400 w-4"></i>
                                <span>Pengaturan Toko</span>
                            </a>
                        </div>
                        @endif

                        {{-- Eksternal --}}
                        <div class="pt-3 border-t border-slate-100 space-y-1">
                            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-arrow-up-right-from-square text-slate-400 w-4"></i>
                                <span>Lihat Toko Publik</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Drawer Logout Button --}}
                <div class="p-4 border-t border-slate-100">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full h-10 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold inline-flex items-center justify-center gap-2 transition-colors">
                            <i class="fa-solid fa-power-off text-xs"></i>
                            <span>Keluar dari Sistem</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

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
                    timer: 3000, 
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
