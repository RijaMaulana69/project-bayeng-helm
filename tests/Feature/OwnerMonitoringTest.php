<?php

namespace Tests\Feature;

use App\Models\Helm;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\StokMasuk;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OwnerMonitoringTest extends TestCase
{
    protected function getOwnerUser(): User
    {
        return User::where('id_role', 1)->first() ?? User::factory()->create([
            'id_role'       => 1,
            'nama_lengkap'  => 'Owner Testing',
            'username'      => 'owner_tester',
            'email'         => 'owner_tester@example.com',
            'password'      => Hash::make('password123'),
        ]);
    }

    protected function getKaryawanUser(): User
    {
        return User::where('id_role', 2)->first() ?? User::factory()->create([
            'id_role'       => 2,
            'nama_lengkap'  => 'Karyawan Testing',
            'username'      => 'karyawan_tester',
            'email'         => 'karyawan_tester@example.com',
            'password'      => Hash::make('password123'),
        ]);
    }

    public function test_owner_can_access_transaksi_karyawan_index(): void
    {
        $owner = $this->getOwnerUser();

        $response = $this->actingAs($owner, 'web')
            ->get(route('admin.owner.transaksi-karyawan.index'));

        $response->assertStatus(200);
        $response->assertSee('Transaksi Karyawan');
        $response->assertSee('Omzet Kasir Selesai');
        $response->assertSee('Uang Tunai Laci');
        $response->assertSee('Aktivitas Staf Kasir');
    }

    public function test_owner_can_access_transaksi_karyawan_show(): void
    {
        $owner = $this->getOwnerUser();
        $pesanan = Pesanan::first();

        if ($pesanan) {
            $response = $this->actingAs($owner, 'web')
                ->get(route('admin.owner.transaksi-karyawan.show', $pesanan->id_pesanan));

            $response->assertStatus(200);
            $response->assertSee($pesanan->no_pesanan);
            $response->assertSee('Audit Transaksi Karyawan');
            $response->assertSee('Jejak Audit Pelayanan Kasir');
        }
    }

    public function test_owner_can_access_barang_masuk_index(): void
    {
        $owner = $this->getOwnerUser();

        $response = $this->actingAs($owner, 'web')
            ->get(route('admin.owner.barang-masuk.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Barang Masuk');
        $response->assertSee('Total Fisik Masuk');
        $response->assertSee('Total Modal Masuk');
        $response->assertSee('Log Pencatatan Barang Masuk');
    }

    public function test_karyawan_cannot_access_owner_transaksi_karyawan(): void
    {
        $karyawan = $this->getKaryawanUser();

        $response = $this->actingAs($karyawan, 'web')
            ->get(route('admin.owner.transaksi-karyawan.index'));

        $response->assertStatus(403);
    }

    public function test_karyawan_cannot_access_owner_barang_masuk(): void
    {
        $karyawan = $this->getKaryawanUser();

        $response = $this->actingAs($karyawan, 'web')
            ->get(route('admin.owner.barang-masuk.index'));

        $response->assertStatus(403);
    }

    public function test_owner_navigation_excludes_cashier_and_stock_menus(): void
    {
        $owner = $this->getOwnerUser();

        $response = $this->actingAs($owner, 'web')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Owner must NOT see cashier operational menus
        $response->assertDontSee('Transaksi Kasir');
        $response->assertDontSee('Katalog & Cek Stok');

        // Owner MUST see the monitoring menus
        $response->assertSee('Transaksi Karyawan');
        $response->assertSee('Riwayat Barang Masuk');

        // Verify Karyawan DOES see cashier operational menus
        $karyawan = $this->getKaryawanUser();
        $respKaryawan = $this->actingAs($karyawan, 'web')->get(route('admin.dashboard'));
        $respKaryawan->assertStatus(200);
        $respKaryawan->assertSee('Transaksi Kasir');
        $respKaryawan->assertSee('Katalog & Cek Stok');
    }

    public function test_guest_cannot_access_owner_monitoring(): void
    {
        $this->get(route('admin.owner.transaksi-karyawan.index'))
            ->assertRedirect('/admin/login');

        $this->get(route('admin.owner.barang-masuk.index'))
            ->assertRedirect('/admin/login');
    }
}
