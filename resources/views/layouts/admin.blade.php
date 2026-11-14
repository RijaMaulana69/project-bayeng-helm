<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Bayeng Putra Helm Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 260px;
            --topbar-h: 64px;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --dark: #0f172a;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --muted: #64748b;
            --body-bg: #f1f5f9;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--body-bg);
            color: var(--dark);
            margin: 0;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1050;
            overflow-y: auto;
            transition: transform .3s ease;
            display: flex;
            flex-direction: column;
        }
        .sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            border-bottom: 1px solid #1e293b;
        }
        .sidebar .sidebar-brand img { height: 32px; }
        .sidebar .sidebar-brand span {
            color: #fff;
            font-weight: 800;
            font-size: 1rem;
        }
        .sidebar .nav-section {
            padding: .75rem 1.5rem .25rem;
            font-size: .68rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            font-size: .875rem;
            font-weight: 500;
            padding: .65rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            border-radius: 0;
            transition: all .2s;
        }
        .sidebar .nav-link:hover { color: #fff; background: var(--sidebar-hover); }
        .sidebar .nav-link.active {
            color: #fff;
            background: var(--primary);
        }
        .sidebar .nav-link i { width: 20px; text-align: center; font-size: .9rem; }

        /* Topbar */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1040;
        }
        .topbar .topbar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 1.5rem;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: all .3s;
        }
        .stat-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(37,99,235,.08);
        }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
        }
        .stat-card .stat-label {
            font-size: .78rem;
            color: var(--muted);
            font-weight: 500;
        }

        .card-bayeng {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .card-bayeng .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 700;
            padding: 1rem 1.25rem;
        }

        /* Responsive */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 1045;
            display: none;
        }

        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
        }

        .btn-primary-admin {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-primary-admin:hover {
            background: var(--primary-dark);
            color: #fff;
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body>
    @php
        $currentUser = Auth::guard('web')->user();
        $isOwner = $currentUser?->isOwner();
        $roleName = $currentUser?->role?->nama_role ?? 'user';
    @endphp

    {{-- Sidebar Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo">
            <span>Bayeng Helm</span>
        </div>

        <nav class="flex-grow-1 py-2">
            <div class="nav-section">Menu Utama</div>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fa fa-chart-pie"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.pickup*') ? 'active' : '' }}" href="{{ route('admin.pickup.index') }}">
                <i class="fa fa-qrcode"></i> Validasi Pickup
            </a>
            <a class="nav-link {{ request()->routeIs('admin.pesanan*') ? 'active' : '' }}" href="{{ route('admin.pesanan.index') }}">
                <i class="fa fa-receipt"></i> Pesanan Online
            </a>

            <div class="nav-section mt-3">Inventaris</div>
            <a class="nav-link {{ request()->routeIs('admin.produk*') ? 'active' : '' }}" href="{{ route('admin.produk.index') }}">
                <i class="fa fa-helmet-safety"></i> Produk Helm
            </a>
            <a class="nav-link {{ request()->routeIs('admin.merk*') ? 'active' : '' }}" href="{{ route('admin.merk.index') }}">
                <i class="fa fa-tags"></i> Merk
            </a>
            <a class="nav-link {{ request()->routeIs('admin.kategori*') ? 'active' : '' }}" href="{{ route('admin.kategori.index') }}">
                <i class="fa fa-layer-group"></i> Tipe / Kategori
            </a>
            <a class="nav-link {{ request()->routeIs('admin.warna*') ? 'active' : '' }}" href="{{ route('admin.warna.index') }}">
                <i class="fa fa-palette"></i> Warna
            </a>
            <a class="nav-link {{ request()->routeIs('admin.stok*') ? 'active' : '' }}" href="{{ route('admin.stok.index') }}">
                <i class="fa fa-boxes-stacked"></i> Stok Masuk
            </a>

            @if($isOwner)
            <div class="nav-section mt-3">Owner</div>
            <a class="nav-link {{ request()->routeIs('admin.owner.user*') ? 'active' : '' }}" href="{{ route('admin.owner.user.index') }}">
                <i class="fa fa-users-gear"></i> Kelola Karyawan
            </a>
            <a class="nav-link {{ request()->routeIs('admin.owner.laporan*') ? 'active' : '' }}" href="{{ route('admin.owner.laporan.penjualan') }}">
                <i class="fa fa-file-invoice-dollar"></i> Laporan Penjualan
            </a>
            <a class="nav-link {{ request()->routeIs('admin.owner.activity-log') ? 'active' : '' }}" href="{{ route('admin.owner.activity-log') }}">
                <i class="fa fa-clock-rotate-left"></i> Activity Log
            </a>
            <a class="nav-link {{ request()->routeIs('admin.owner.pengaturan') ? 'active' : '' }}" href="{{ route('admin.owner.pengaturan') }}">
                <i class="fa fa-gear"></i> Pengaturan Toko
            </a>
            @endif
        </nav>

        <div class="p-3 border-top" style="border-color:#1e293b !important">
            <div class="d-flex align-items-center gap-2 text-white small">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:36px;height:36px">
                    <i class="fa fa-user"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-bold text-truncate" style="font-size:.8rem">{{ $currentUser?->nama_lengkap }}</div>
                    <div style="font-size:.7rem;color:#64748b">{{ ucfirst($roleName) }}</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Topbar --}}
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-lg-none rounded-2" onclick="toggleSidebar()">
                <i class="fa fa-bars"></i>
            </button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-sm btn-light rounded-pill" target="_blank" title="Lihat Toko">
                <i class="fa fa-external-link-alt me-1"></i> Lihat Toko
            </a>
            <div class="dropdown">
                <a class="btn btn-sm btn-light rounded-pill dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fa fa-user-circle me-1"></i> {{ $currentUser?->nama_lengkap }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="{{ route('admin.profil') }}"><i class="fa fa-user me-2"></i> Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="fa fa-sign-out-alt me-2"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="main-content">
        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Auto-dismiss alerts
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(el => {
                bootstrap.Alert.getOrCreateInstance(el)?.close();
            });
        }, 5000);
    </script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
