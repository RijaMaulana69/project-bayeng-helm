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

    <div class="flex h-[100dvh] overflow-hidden bg-[#f8fafc]">
        
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

                    {{-- Transaksi (Pusat Kasir & Pesanan) --}}
                    <a href="{{ route('admin.pesanan.index') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.pesanan*') || request()->routeIs('admin.pickup*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-receipt text-slate-400 group-hover:text-slate-600"></i>
                            <span>Transaksi</span>
                        </div>
                        @if(($nav_transaksi_count ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                {{ $nav_transaksi_count }}
                            </span>
                        @endif
                    </a>

                    {{-- Produk & Inventori (Katalog, Stok Fisik & Riwayat Masuk) --}}
                    <a href="{{ route('admin.produk.index') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.produk*') || request()->routeIs('admin.stok*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-boxes-stacked text-slate-400 group-hover:text-slate-600"></i>
                            <span>Produk & Inventori</span>
                        </div>
                        @if(($nav_critical_stock_count ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700" title="{{ $nav_critical_stock_count }} varian menipis/habis">
                                {{ $nav_critical_stock_count }}
                            </span>
                        @endif
                    </a>

                    {{-- Atribut & Taksonomi Produk (Merk, Kategori, Warna Swatch) --}}
                    <a href="{{ route('admin.atribut.index') }}" 
                       class="flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-colors group {{ request()->routeIs('admin.atribut*') || request()->routeIs('admin.merk*') || request()->routeIs('admin.kategori*') || request()->routeIs('admin.warna*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <div class="flex items-center gap-2.5">
                            <i class="w-4 text-center fa-solid fa-tags text-slate-400 group-hover:text-slate-600"></i>
                            <span>Atribut & Taksonomi</span>
                        </div>
                    </a>
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
                        <button type="submit" class="w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
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
            <header class="z-20 h-14 bg-white border-b border-slate-200/80 px-4 lg:px-8 flex items-center justify-between shrink-0">
                
                {{-- Left: Page Title + Real-time Clock --}}
                <div class="flex items-center gap-3">
                    <h1 class="text-sm font-bold text-slate-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                    
                    {{-- Real-time Digital Clock --}}
                    <div x-data="{ 
                            time: '',
                            updateClock() {
                                const now = new Date();
                                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                const day = days[now.getDay()];
                                const date = now.getDate();
                                const month = months[now.getMonth()];
                                const year = now.getFullYear();
                                const h = String(now.getHours()).padStart(2, '0');
                                const m = String(now.getMinutes()).padStart(2, '0');
                                this.time = `${day}, ${date} ${month} ${year}, ${h}:${m} WIB`;
                            }
                         }" 
                         x-init="updateClock(); setInterval(() => updateClock(), 1000)"
                         class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                        <i class="fa-regular fa-clock text-xs text-slate-400"></i>
                        <span x-text="time"></span>
                    </div>
                </div>

                {{-- Right: Notification + Profile + Logout --}}
                <div class="flex items-center gap-2">
                    {{-- Operational Notification Bell Dropdown --}}
                    @php
                        $notifPickup = $nav_pickup_count ?? 0;
                        $notifPending = $nav_pending_count ?? 0;
                        $notifStock = $nav_critical_stock_count ?? 0;
                        $notifTotal = $notifPickup + $notifPending + ($notifStock > 0 ? 1 : 0);
                    @endphp
                    <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
                        <button type="button" 
                                @click="notifOpen = !notifOpen" 
                                class="relative w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer"
                                aria-label="Notifikasi Toko">
                            <i class="fa-regular fa-bell text-base"></i>
                            @if($notifTotal > 0)
                                <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                            @endif
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="notifOpen" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-2xl border border-slate-200/90 shadow-xl overflow-hidden z-50">
                            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900">Notifikasi</span>
                                    @if($notifTotal > 0)
                                        <span class="px-1.5 py-0.2 rounded-md bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200/80 font-mono">
                                            {{ $notifTotal }}
                                        </span>
                                    @endif
                                </div>
                                <button type="button" @click="notifOpen = false" class="text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                                @if($notifPickup > 0)
                                    <a href="{{ route('admin.pesanan.index', ['status' => 'ready']) }}" class="p-3 flex items-start gap-3 hover:bg-slate-50 transition-colors group">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xs mt-0.5">
                                            <i class="fa-solid fa-box-open"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-slate-800 group-hover:text-blue-600 transition-colors truncate">
                                                {{ $notifPickup }} Pesanan Siap Ambil
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                Pelanggan siap mengambil paket di gerai
                                            </p>
                                        </div>
                                    </a>
                                @endif

                                @if($notifPending > 0)
                                    <a href="{{ route('admin.pesanan.index', ['status' => 'pending']) }}" class="p-3 flex items-start gap-3 hover:bg-slate-50 transition-colors group">
                                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 text-xs mt-0.5">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-slate-800 group-hover:text-blue-600 transition-colors truncate">
                                                {{ $notifPending }} Pembayaran Menunggu
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                Bukti transfer perlu diverifikasi
                                            </p>
                                        </div>
                                    </a>
                                @endif

                                @if($notifStock > 0)
                                    <a href="{{ route('admin.produk.index', ['tab' => 'stok', 'filter' => 'kritis_habis']) }}" class="p-3 flex items-start gap-3 hover:bg-slate-50 transition-colors group">
                                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 text-xs mt-0.5">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-slate-800 group-hover:text-blue-600 transition-colors truncate">
                                                {{ $notifStock }} Varian Stok Kritis
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                Stok varian habis atau &le; 3 unit
                                            </p>
                                        </div>
                                    </a>
                                @endif

                                @if($notifTotal === 0)
                                    <div class="p-6 text-center">
                                        <i class="fa-regular fa-circle-check text-2xl text-emerald-500 mb-1"></i>
                                        <p class="text-xs font-semibold text-slate-700">Operasional Aman</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Semua antrean gerai terkendali.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Subtle Vertical Divider --}}
                    <div class="h-5 w-px bg-slate-200 mx-1"></div>

                    {{-- User Profile Dropdown Menu --}}
                    @if($authUser)
                    <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                        <button type="button" 
                                @click="userMenu = !userMenu"
                                class="flex items-center gap-2.5 py-1 px-1.5 sm:px-2 rounded-xl hover:bg-slate-100 transition-all cursor-pointer group"
                                aria-label="Menu Pengguna">
                            <div class="w-8 h-8 rounded-full bg-slate-100/90 border border-slate-200/90 text-slate-600 flex items-center justify-center shrink-0 group-hover:bg-blue-50 group-hover:border-blue-200 group-hover:text-blue-600 transition-colors">
                                <i class="fa-regular fa-user text-xs"></i>
                            </div>
                            <div class="hidden sm:block text-left">
                                <span class="text-xs font-semibold text-slate-800 group-hover:text-blue-600 transition-colors block leading-tight">
                                    {{ $authUser->nama_lengkap }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium">
                                    {{ $roleLabel }}
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 group-hover:text-slate-600 transition-colors hidden sm:block"></i>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="userMenu" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl border border-slate-200/90 shadow-xl overflow-hidden z-50 divide-y divide-slate-100">
                            
                            {{-- User Summary --}}
                            <div class="p-3.5 bg-slate-50/70">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-700 flex items-center justify-center shrink-0 shadow-2xs">
                                        <i class="fa-regular fa-user text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $authUser->nama_lengkap }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="inline-block text-[10px] font-semibold px-1.5 py-0.2 rounded border {{ $roleBadgeBg }}">
                                                {{ $roleLabel }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono truncate">{{ '@' . $authUser->username }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="p-1.5 space-y-0.5">
                                <a href="{{ route('admin.profil') }}" 
                                   class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:text-blue-600 hover:bg-slate-50 rounded-xl transition-colors">
                                    <i class="fa-regular fa-user text-slate-400 text-xs w-4 text-center"></i>
                                    <span>Ubah Profil</span>
                                </a>
                            </div>

                            {{-- Logout inside dropdown --}}
                            <div class="p-1.5">
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 rounded-xl transition-colors text-left cursor-pointer">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-xs w-4 text-center"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
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
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-lg px-2 py-1 pb-[max(0.35rem,env(safe-area-inset-bottom))] flex items-center justify-around">
        
        {{-- 1. Ringkasan / Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-chart-pie text-base"></i>
            <span class="text-[10px] mt-0.5">{{ $isOwner ? 'Ringkasan' : 'Kasir' }}</span>
        </a>

        {{-- 2. Transaksi (Pusat Kasir & Pesanan) --}}
        <a href="{{ route('admin.pesanan.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-colors relative {{ request()->routeIs('admin.pesanan*') || request()->routeIs('admin.pickup*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <div class="relative">
                <i class="fa-solid fa-receipt text-base"></i>
                @if(($nav_transaksi_count ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-3 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-500 text-white ring-2 ring-white">
                        {{ $nav_transaksi_count }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] mt-0.5">Transaksi</span>
        </a>

        {{-- 3. Produk & Inventori --}}
        <a href="{{ route('admin.produk.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-2.5 rounded-xl transition-colors relative {{ request()->routeIs('admin.produk*') || request()->routeIs('admin.stok*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <div class="relative">
                <i class="fa-solid fa-boxes-stacked text-base"></i>
                @if(($nav_critical_stock_count ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-3 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-rose-500 text-white ring-2 ring-white">
                        {{ $nav_critical_stock_count }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] mt-0.5">Produk</span>
        </a>

        {{-- 4. Menu Drawer Trigger --}}
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
                        {{-- Operasional Gerai --}}
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Operasional Kasir</p>
                            <a href="{{ route('admin.pesanan.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-receipt text-slate-400 w-4"></i>
                                    <span>Transaksi</span>
                                </div>
                                @if(($nav_transaksi_count ?? 0) > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                        {{ $nav_transaksi_count }}
                                    </span>
                                @endif
                            </a>
                        </div>

                        {{-- Katalog & Atribut --}}
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2">Katalog & Atribut</p>
                            <a href="{{ route('admin.produk.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 {{ request()->routeIs('admin.produk*') || request()->routeIs('admin.stok*') ? 'bg-slate-100 font-semibold text-slate-900' : '' }}">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-boxes-stacked text-slate-400 w-4"></i>
                                    <span>Produk & Inventori</span>
                                </div>
                                @if(($nav_critical_stock_count ?? 0) > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                        {{ $nav_critical_stock_count }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.atribut.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 {{ request()->routeIs('admin.atribut*') ? 'bg-slate-100 font-semibold text-slate-900' : '' }}">
                                <i class="fa-solid fa-tags text-slate-400 w-4"></i>
                                <span>Atribut & Taksonomi</span>
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
                        <button type="submit" class="w-full h-10 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold inline-flex items-center justify-center gap-2 transition-colors cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            <span>Keluar dari Sistem</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Global Floating Notification ("Ngambang di Atas") --}}
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             class="fixed top-16 left-1/2 -translate-x-1/2 z-[100] w-[92%] sm:w-auto sm:min-w-[340px] max-w-md px-4 py-3 bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700/80 flex items-center justify-between gap-3 backdrop-blur-md">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <i class="fa-solid fa-check text-xs"></i>
                </div>
                <p class="text-xs sm:text-sm font-medium text-slate-100 truncate">
                    {{ session('success') }}
                </p>
            </div>
            <button type="button" @click="show = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer shrink-0" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4500)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             class="fixed top-16 left-1/2 -translate-x-1/2 z-[100] w-[92%] sm:w-auto sm:min-w-[340px] max-w-md px-4 py-3 bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700/80 flex items-center justify-between gap-3 backdrop-blur-md">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/30">
                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                </div>
                <p class="text-xs sm:text-sm font-medium text-slate-100 truncate">
                    {{ session('error') }}
                </p>
            </div>
            <button type="button" @click="show = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer shrink-0" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>
