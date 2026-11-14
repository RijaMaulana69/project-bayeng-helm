<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    {{-- CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Base styles to prevent flash of unstyled content */
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f4f7fe; 
            -webkit-font-smoothing: antialiased;
        }
        [x-cloak] { display: none !important; }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Custom UI Elements */
        .sidebar-active {
            background-color: #f0f7ff;
            color: #2563eb;
            border-right: 3px solid #2563eb;
        }
        .card-pro {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="h-full text-slate-700 overflow-hidden" x-data="{ sidebarOpen: true, mobileMenu: false }">

    <div class="flex h-screen overflow-hidden bg-[#f8fafc]">
        
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transition-transform duration-300 transform lg:relative lg:translate-x-0"
               :class="{'translate-x-0': mobileMenu, '-translate-x-full': !mobileMenu}">
            
            <div class="flex flex-col h-full">
                {{-- Logo Section --}}
                <div class="flex items-center h-16 px-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 rounded-lg shadow-sm">
                            <i class="text-sm fa-solid fa-helmet-safety"></i>
                        </div>
                        <span class="text-sm font-extrabold tracking-tight text-slate-900">BAYENG <span class="text-blue-600">PRO</span></span>
                    </div>
                </div>

                {{-- Nav Items --}}
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <p class="px-3 mb-2 text-[10px] font-semibold tracking-widest text-slate-400 uppercase">Dashboard</p>
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-chart-line"></i>
                        <span>Ringkasan</span>
                    </a>

                    <a href="{{ route('admin.pickup.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.pickup*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-qrcode"></i>
                        <span>Kasir Pickup</span>
                    </a>

                    <a href="{{ route('admin.pesanan.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.pesanan*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-receipt"></i>
                        <span>Pesanan</span>
                    </a>

                    <p class="px-3 mt-6 mb-2 text-[10px] font-semibold tracking-widest text-slate-400 uppercase">Gudang & Produk</p>
                    
                    <a href="{{ route('admin.produk.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.produk*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-box-archive"></i>
                        <span>Katalog Helm</span>
                    </a>

                    <a href="{{ route('admin.stok.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.stok*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-boxes-stacked"></i>
                        <span>Update Stok</span>
                    </a>

                    <a href="{{ route('admin.merk.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.merk*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-tags"></i>
                        <span>Merk</span>
                    </a>

                    <a href="{{ route('admin.kategori.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.kategori*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-layer-group"></i>
                        <span>Kategori</span>
                    </a>

                    <a href="{{ route('admin.warna.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg group {{ request()->routeIs('admin.warna*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-palette"></i>
                        <span>Warna</span>
                    </a>

                    @if(Auth::guard('web')->user()->isOwner())
                    <p class="px-3 mt-6 mb-2 text-[10px] font-semibold tracking-widest text-slate-400 uppercase">Management</p>
                    <a href="{{ route('admin.owner.user.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg {{ request()->routeIs('admin.owner.user*') || request()->routeIs('admin.user*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-user-gear"></i>
                        <span>Karyawan</span>
                    </a>
                    <a href="{{ route('admin.owner.laporan.penjualan') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg {{ request()->routeIs('admin.owner.laporan*') || request()->routeIs('admin.laporan*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-file-invoice-dollar"></i>
                        <span>Laporan</span>
                    </a>
                    <a href="{{ route('admin.owner.activity-log') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg {{ request()->routeIs('admin.owner.activity-log*') || request()->routeIs('admin.activity-log*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-clock-rotate-left"></i>
                        <span>Activity Log</span>
                    </a>
                    <a href="{{ route('admin.owner.pengaturan') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors rounded-lg {{ request()->routeIs('admin.owner.pengaturan*') || request()->routeIs('admin.pengaturan*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="w-5 text-center fa-solid fa-gear"></i>
                        <span>Pengaturan</span>
                    </a>
                    @endif
                </nav>

                {{-- User Info --}}
                <div class="p-4 border-t border-slate-100">
                    <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-50">
                        <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-slate-800 rounded-lg">
                            {{ substr(Auth::guard('web')->user()->nama_lengkap, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::guard('web')->user()->nama_lengkap }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">{{ Auth::guard('web')->user()->role->nama_role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            
            {{-- Topbar --}}
            <header class="z-10 flex flex-shrink-0 h-16 bg-white border-b border-slate-200">
                <div class="flex items-center justify-between flex-1 px-4 lg:px-8">
                    <div class="flex items-center gap-4">
                        <button @click="mobileMenu = true" class="text-slate-500 lg:hidden focus:outline-none">
                            <i class="text-xl fa-solid fa-bars"></i>
                        </button>
                        <h1 class="text-sm font-bold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                            <i class="mr-1 fa-solid fa-eye"></i> Toko
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto bg-[#f8fafc] p-4 lg:p-8">
                <div class="max-w-6xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Flash Notifications --}}
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
