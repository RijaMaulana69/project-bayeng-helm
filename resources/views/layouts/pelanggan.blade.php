<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bayeng Putra Helm') - Toko Helm Terpercaya</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #f59e0b;
            --dark: #0f172a;
            --muted: #64748b;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 12px rgba(0,0,0,.04);
            --card-hover-shadow: 0 8px 30px rgba(37,99,235,.12);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--dark);
            background: var(--light-bg);
            margin: 0;
        }

        /* Navbar */
        .navbar-bayeng {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            padding: .75rem 0;
            position: sticky;
            top: 0;
            z-index: 1040;
        }
        .navbar-bayeng .navbar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--primary);
        }
        .navbar-bayeng .navbar-brand img {
            height: 36px;
            margin-right: 8px;
        }
        .navbar-bayeng .nav-link {
            font-weight: 500;
            color: var(--dark);
            padding: .5rem 1rem !important;
            border-radius: 8px;
            transition: all .2s;
        }
        .navbar-bayeng .nav-link:hover,
        .navbar-bayeng .nav-link.active {
            color: var(--primary);
            background: rgba(37,99,235,.06);
        }

        /* Buttons */
        .btn-primary-bayeng {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: .6rem 1.5rem;
            transition: all .2s;
        }
        .btn-primary-bayeng:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,99,235,.3);
        }
        .btn-outline-bayeng {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 10px;
            font-weight: 600;
            padding: .55rem 1.4rem;
            background: transparent;
            transition: all .2s;
        }
        .btn-outline-bayeng:hover {
            background: var(--primary);
            color: #fff;
        }

        /* Product Cards */
        .product-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: all .3s ease;
            height: 100%;
        }
        .product-card:hover {
            border-color: var(--primary);
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-4px);
        }
        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
            background: #f1f5f9;
        }
        .product-card .card-body { padding: 1.25rem; }
        .product-card .product-merk {
            font-size: .72rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .product-card .product-name {
            font-size: .95rem;
            font-weight: 700;
            color: var(--dark);
            margin: .25rem 0 .5rem;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-card .product-price {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
        }

        /* Footer */
        .footer-bayeng {
            background: var(--dark);
            color: #94a3b8;
            padding: 3rem 0 1.5rem;
        }
        .footer-bayeng h6 { color: #fff; font-weight: 700; }
        .footer-bayeng a { color: #94a3b8; text-decoration: none; transition: color .2s; }
        .footer-bayeng a:hover { color: var(--primary); }

        /* Badge */
        .badge-stock {
            font-size: .7rem;
            font-weight: 600;
            padding: .3rem .6rem;
            border-radius: 6px;
        }

        /* Section */
        .section-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark);
        }

        /* Hero */
        .hero-section {
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(37,99,235,.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-section h1 {
            font-size: 2.75rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
        }
        .hero-section .lead {
            color: #94a3b8;
            font-size: 1.1rem;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-bayeng">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo">
                <span>Bayeng Putra Helm</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fa fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('katalog*') ? 'active' : '' }}" href="{{ route('katalog') }}">
                            <i class="fa fa-helmet-safety me-1"></i> Katalog
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    @auth('pelanggan')
                        <a href="{{ route('pelanggan.cart') }}" class="text-decoration-none text-dark position-relative" title="Keranjang">
                            @livewire('cart-counter')
                        </a>
                        <div class="dropdown">
                            <a class="btn btn-sm btn-light rounded-pill px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fa fa-user-circle me-1"></i> {{ Auth::guard('pelanggan')->user()->nama }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('pelanggan.pesanan') }}"><i class="fa fa-receipt me-2"></i> Pesanan Saya</a></li>
                                <li><a class="dropdown-item" href="{{ route('pelanggan.profil') }}"><i class="fa fa-user me-2"></i> Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('pelanggan.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fa fa-sign-out-alt me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('pelanggan.login') }}" class="btn btn-outline-bayeng btn-sm">
                            <i class="fa fa-sign-in-alt me-1"></i> Login
                        </a>
                        <a href="{{ route('pelanggan.register') }}" class="btn btn-primary-bayeng btn-sm">
                            <i class="fa fa-user-plus me-1"></i> Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer-bayeng mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h6 class="mb-3"><i class="fa fa-helmet-safety me-2"></i> Bayeng Putra Helm</h6>
                    <p class="small mb-0">Toko helm terpercaya dengan koleksi lengkap dari berbagai merk ternama. Pesan online, ambil langsung di toko!</p>
                </div>
                <div class="col-lg-4">
                    <h6 class="mb-3">Menu</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}"><i class="fa fa-chevron-right me-1" style="font-size:.6rem"></i> Beranda</a></li>
                        <li class="mb-2"><a href="{{ route('katalog') }}"><i class="fa fa-chevron-right me-1" style="font-size:.6rem"></i> Katalog Helm</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="mb-3">Hubungi Kami</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fa fa-map-marker-alt me-2 text-primary"></i> Jl. RE Martadinata No.49, Kuningan</li>
                        <li class="mb-2"><i class="fa fa-phone me-2 text-primary"></i> 089664269624</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color:#334155">
            <p class="text-center small mb-0">&copy; {{ date('Y') }} Bayeng Putra Helm. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>AOS.init({ duration: 600, once: true });</script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
