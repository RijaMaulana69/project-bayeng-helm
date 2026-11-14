<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Bayeng Putra Helm (E-Commerce Store Pickup)
|--------------------------------------------------------------------------
*/

// ============================================================
// PUBLIK (Landing Page & Katalog)
// ============================================================
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/beranda', fn() => redirect()->route('home'))->name('pelanggan.beranda');

// Katalog (bisa diakses publik, tapi cart butuh login)
Route::get('katalog', [\App\Http\Controllers\Pelanggan\KatalogController::class, 'index'])->name('katalog');
Route::get('katalog-helm', fn() => redirect()->route('katalog'))->name('pelanggan.katalog');
Route::get('katalog/{helm}', [\App\Http\Controllers\Pelanggan\KatalogController::class, 'show'])->name('katalog.detail');
Route::get('katalog-helm/{helm}', fn($helm) => redirect()->route('katalog.detail', $helm))->name('pelanggan.katalog.detail');

// Short aliases for customer pages
Route::get('cart', fn() => redirect()->route('pelanggan.cart'))->name('cart');
Route::get('checkout', fn() => redirect()->route('pelanggan.checkout'))->name('checkout');

// ============================================================
// AUTH PELANGGAN (Guard: pelanggan)
// ============================================================
Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
    // Guest only
    Route::middleware('guest:pelanggan')->group(function () {
        Route::get('login', [\App\Http\Controllers\Pelanggan\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\Pelanggan\AuthController::class, 'login'])->name('login.post');
        Route::get('register', [\App\Http\Controllers\Pelanggan\AuthController::class, 'showRegister'])->name('register');
        Route::post('register', [\App\Http\Controllers\Pelanggan\AuthController::class, 'register'])->name('register.post');
    });

    // Authenticated pelanggan
    Route::middleware('auth:pelanggan')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Pelanggan\AuthController::class, 'logout'])->name('logout');
        Route::get('profil', [\App\Http\Controllers\Pelanggan\ProfilController::class, 'index'])->name('profil');
        Route::put('profil', [\App\Http\Controllers\Pelanggan\ProfilController::class, 'update'])->name('profil.update');

        // Keranjang & Checkout
        Route::get('cart', [\App\Http\Controllers\Pelanggan\CartController::class, 'index'])->name('cart');
        Route::get('checkout', [\App\Http\Controllers\Pelanggan\CheckoutController::class, 'index'])->name('checkout');
        Route::post('checkout', [\App\Http\Controllers\Pelanggan\CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('checkout/sukses/{pesanan}', [\App\Http\Controllers\Pelanggan\CheckoutController::class, 'sukses'])->name('checkout.sukses');

        // Pesanan
        Route::get('pesanan', [\App\Http\Controllers\Pelanggan\PesananController::class, 'index'])->name('pesanan');
        Route::get('pesanan/{pesanan}', [\App\Http\Controllers\Pelanggan\PesananController::class, 'show'])->name('pesanan.show');
        Route::get('pesanan/{pesanan}/detail', [\App\Http\Controllers\Pelanggan\PesananController::class, 'show'])->name('pesanan.detail');
        Route::post('pesanan/{pesanan}/upload-bukti', [\App\Http\Controllers\Pelanggan\PesananController::class, 'uploadBukti'])->name('pesanan.upload-bukti');
    });
});

// ============================================================
// AUTH ADMIN (Guard: web) - Owner & Karyawan
// ============================================================
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest only
    Route::middleware('guest:web')->group(function () {
        Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');
    });

    // Authenticated admin (owner + karyawan)
    Route::middleware('auth:web')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Profil admin
        Route::get('profil', [\App\Http\Controllers\Admin\ProfilController::class, 'index'])->name('profil');
        Route::put('profil', [\App\Http\Controllers\Admin\ProfilController::class, 'update'])->name('profil.update');

        // Manajemen Produk (karyawan + owner)
        Route::resource('produk', \App\Http\Controllers\Admin\ProdukController::class)->except(['show']);
        Route::resource('merk', \App\Http\Controllers\Admin\MerkController::class)->except(['show']);
        Route::resource('kategori', \App\Http\Controllers\Admin\KategoriController::class)->except(['show']);
        Route::resource('warna', \App\Http\Controllers\Admin\WarnaController::class)->except(['show', 'create', 'edit']);
        Route::resource('stok', \App\Http\Controllers\Admin\StokController::class)->only(['index', 'store']);

        // Manajemen Pesanan Online (karyawan + owner)
        Route::get('pesanan', [\App\Http\Controllers\Admin\PesananController::class, 'index'])->name('pesanan.index');
        Route::get('pesanan/{pesanan}', [\App\Http\Controllers\Admin\PesananController::class, 'show'])->name('pesanan.show');
        Route::put('pesanan/{pesanan}/status', [\App\Http\Controllers\Admin\PesananController::class, 'updateStatus'])->name('pesanan.update-status');

        // Validasi Pickup (kasir scan/input kode)
        Route::get('pickup', [\App\Http\Controllers\Admin\PickupController::class, 'index'])->name('pickup.index');
        Route::post('pickup/validasi', [\App\Http\Controllers\Admin\PickupController::class, 'validasi'])->name('pickup.validasi');
        Route::post('pickup/{pesanan}/serahkan', [\App\Http\Controllers\Admin\PickupController::class, 'serahkan'])->name('pickup.serahkan');

        // ============================================================
        // OWNER ONLY
        // ============================================================
        Route::middleware('role:owner')->group(function () {
            // User / Karyawan (supports both admin.user.* and admin.owner.user.*)
            Route::resource('user', \App\Http\Controllers\Admin\UserController::class)->except(['show']);

            // Laporan
            Route::get('laporan-penjualan', [\App\Http\Controllers\Admin\LaporanController::class, 'penjualan'])->name('laporan.penjualan');
            Route::get('laporan-penjualan/export-pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
            Route::get('laporan-penjualan/export-csv', [\App\Http\Controllers\Admin\LaporanController::class, 'exportCsv'])->name('laporan.export-csv');

            // Activity Log
            Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
            Route::get('activity-log-alias', fn() => redirect()->route('admin.activity-log.index'))->name('activity-log');

            // Pengaturan Toko
            Route::get('pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('pengaturan');
            Route::get('pengaturan-index', fn() => redirect()->route('admin.pengaturan'))->name('pengaturan.index');
            Route::put('pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'update'])->name('pengaturan.update');

            // Owner prefix aliases to ensure complete compatibility
            Route::prefix('owner')->name('owner.')->group(function () {
                Route::get('user', fn() => redirect()->route('admin.user.index'))->name('user.index');
                Route::get('laporan-penjualan', fn() => redirect()->route('admin.laporan.penjualan'))->name('laporan.penjualan');
                Route::get('activity-log', fn() => redirect()->route('admin.activity-log.index'))->name('activity-log');
                Route::get('pengaturan', fn() => redirect()->route('admin.pengaturan'))->name('pengaturan');
            });
        });
    });
});
