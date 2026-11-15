<?php

namespace Tests\Feature;

use App\Models\Helm;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_view_modern_merchant_dashboard(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'admin_dash_test'],
            [
                'nama_lengkap' => 'Admin Dashboard Tester',
                'password' => Hash::make('Admin123!'),
                'id_role' => 1,
            ]
        );

        $response = $this->actingAs($user, 'web')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Toko');
        $response->assertSee('Halo, Admin Dashboard Tester');
        $response->assertSee('Penting Hari Ini');
        $response->assertSee('Siap Diambil di Toko');
        $response->assertSee('Pesanan Masuk Hari Ini');
        $response->assertSee('Stok Kritis (< 5)');
        $response->assertSee('Omzet Selesai Hari Ini');
        $response->assertSee('Tren Penjualan Bulanan');
        $response->assertSee('Pesanan Masuk Terbaru');
        $response->assertSee('Peringatan Stok Menipis');

        // Owner executive monitoring tools & period filter presence
        $response->assertSee(route('admin.owner.transaksi-karyawan.index'));
        $response->assertSee(route('admin.owner.barang-masuk.index'));
        $response->assertSee('Filter Periode:');
        $response->assertSee('Bulan Ini');

        // Verify Owner does NOT have cashier pickup tools
        $response->assertDontSee('Scan / Kode Pickup (PKP-...)');

        // Verify elimination of AI slop rainbow gradients & legacy classes
        $response->assertDontSee('linear-gradient(135deg, #2563eb, #1d4ed8)');
        $response->assertDontSee('linear-gradient(135deg, #f59e0b, #d97706)');
        $response->assertDontSee('linear-gradient(135deg, #10b981, #059669)');
        $response->assertDontSee('linear-gradient(135deg, #8b5cf6, #7c3aed)');
        $response->assertDontSee('card-bayeng');
    }

    public function test_admin_dashboard_supports_dynamic_period_switching(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'admin_dash_test'],
            [
                'nama_lengkap' => 'Admin Dashboard Tester',
                'password' => Hash::make('Admin123!'),
                'id_role' => 1,
            ]
        );

        foreach (['today', '7days', '30days', 'this_month', 'all'] as $period) {
            $response = $this->actingAs($user, 'web')->get(route('admin.dashboard', ['period' => $period]));
            $response->assertStatus(200);
            $response->assertSee('Ringkasan Toko');
        }
    }

    public function test_karyawan_sees_operational_cashier_dashboard_without_financial_data(): void
    {
        $karyawan = User::firstOrCreate(
            ['username' => 'karyawan_test'],
            [
                'nama_lengkap' => 'Kasir Operasional Testing',
                'password' => Hash::make('Admin123!'),
                'id_role' => 2, // role karyawan
            ]
        );

        $response = $this->actingAs($karyawan, 'web')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Kasir');
        $response->assertSee('Halo, Kasir Operasional Testing');
        $response->assertSee('Kasir Gerai');
        $response->assertSee('Masukkan kode pickup');
        $response->assertSee('Ambil di Toko');
        $response->assertSee('Perlu Disiapkan');

        // Security check: Karyawan must NOT see financial turnover, profit metrics, or financial charts
        $response->assertDontSee('Omzet Selesai Hari Ini');
        $response->assertDontSee('Rata-rata Order (AOV)');
        $response->assertDontSee('Tren Penjualan Bulanan');
        $response->assertDontSee('salesChart');

        // Navigation check: Karyawan must NOT see Owner management links in sidebar/nav
        $response->assertDontSee('Kelola Karyawan');
        $response->assertDontSee('Laporan Penjualan');
    }

    public function test_mobile_bottom_navigation_bar_is_rendered(): void
    {
        $owner = User::firstOrCreate(
            ['username' => 'admin_dash_test'],
            [
                'nama_lengkap' => 'Admin Dashboard Tester',
                'password' => Hash::make('Admin123!'),
                'id_role' => 1,
            ]
        );

        // Owner mobile navigation: Executive links (Trx Kasir, Brg Masuk, Laporan), no operational cashier buttons
        $response = $this->actingAs($owner, 'web')->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('lg:hidden fixed bottom-0 inset-x-0');
        $response->assertSee('Trx Kasir');
        $response->assertSee('Brg Masuk');
        $response->assertSee('Laporan');

        // Karyawan mobile navigation: Operational cashier buttons (Transaksi, Stok)
        $karyawan = User::where('id_role', 2)->first();
        if ($karyawan) {
            $respKaryawan = $this->actingAs($karyawan, 'web')->get(route('admin.dashboard'));
            $respKaryawan->assertStatus(200);
            $respKaryawan->assertSee('Transaksi');
            $respKaryawan->assertSee('Stok');
        }
    }
}
