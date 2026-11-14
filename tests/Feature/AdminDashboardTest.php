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

        // Verify elimination of AI slop rainbow gradients & legacy classes
        $response->assertDontSee('linear-gradient(135deg, #2563eb, #1d4ed8)');
        $response->assertDontSee('linear-gradient(135deg, #f59e0b, #d97706)');
        $response->assertDontSee('linear-gradient(135deg, #10b981, #059669)');
        $response->assertDontSee('linear-gradient(135deg, #8b5cf6, #7c3aed)');
        $response->assertDontSee('card-bayeng');
    }
}
