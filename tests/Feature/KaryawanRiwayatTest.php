<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\Helm;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KaryawanRiwayatTest extends TestCase
{
    protected function getKaryawanUser(): User
    {
        return User::where('id_role', 2)->first() ?? User::factory()->create([
            'id_role'       => 2,
            'nama_lengkap'  => 'Karyawan Kasir Testing',
            'username'      => 'karyawan_kasir_tester',
            'email'         => 'karyawan_kasir@example.com',
            'password'      => Hash::make('password123'),
        ]);
    }

    public function test_karyawan_can_access_riwayat_kasir_today(): void
    {
        $karyawan = $this->getKaryawanUser();

        $response = $this->actingAs($karyawan, 'web')
            ->get(route('admin.karyawan.riwayat', ['period' => 'today']));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Kasir');
        $response->assertSee('Total Penjualan');
        $response->assertSee('Kas Tunai');
        $response->assertSee('Non-Tunai');
        $response->assertSee('Helm Terjual');
        $response->assertSee('Hari Ini');
        $response->assertSee('Bulan Ini');

        // Security assertion: Karyawan must NOT see cost price (modal) or profit margin
        $response->assertDontSee('harga_awal');
        $response->assertDontSee('Total Modal');
        $response->assertDontSee('Laba Bersih');
    }

    public function test_only_completed_transactions_appear_in_riwayat_kasir(): void
    {
        $karyawan = $this->getKaryawanUser();

        $customer = \App\Models\Pelanggan::first() ?? \App\Models\Pelanggan::create([
            'nama'     => 'Customer Riwayat Test ' . uniqid(),
            'email'    => 'riwayat_test_' . uniqid() . '@test.com',
            'telepon'  => '0812' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        // Buat 1 pesanan belum selesai (unpaid_pickup) dan 1 pesanan selesai (completed)
        $unpaidOrder = Pesanan::create([
            'no_pesanan'    => 'TEST-UNPAID-' . uniqid(),
            'id_pelanggan'  => $customer->id_pelanggan,
            'penerima'      => 'Pelanggan Belum Bayar',
            'telepon'       => '081234567801',
            'total_belanja' => 200000,
            'grand_total'   => 200000,
            'metode_bayar'  => 'tunai',
            'status'        => 'unpaid_pickup',
            'created_at'    => now(),
        ]);

        $completedOrder = Pesanan::create([
            'no_pesanan'    => 'TEST-COMPLETED-' . uniqid(),
            'id_pelanggan'  => $customer->id_pelanggan,
            'penerima'      => 'Pelanggan Selesai',
            'telepon'       => '081234567802',
            'total_belanja' => 350000,
            'grand_total'   => 350000,
            'metode_bayar'  => 'tunai',
            'status'        => 'completed',
            'created_at'    => now(),
        ]);

        $response = $this->actingAs($karyawan, 'web')
            ->get(route('admin.karyawan.riwayat', ['period' => 'today']));

        $response->assertStatus(200);
        // Pesanan selesai harus terlihat
        $response->assertSee($completedOrder->no_pesanan);
        // Pesanan belum selesai TIDAK BOLEH terlihat di riwayat kasir
        $response->assertDontSee($unpaidOrder->no_pesanan);

        // Cleanup
        $unpaidOrder->delete();
        $completedOrder->delete();
    }

    public function test_karyawan_can_switch_to_this_month_period(): void
    {
        $karyawan = $this->getKaryawanUser();

        $response = $this->actingAs($karyawan, 'web')
            ->get(route('admin.karyawan.riwayat', ['period' => 'this_month']));

        $response->assertStatus(200);
        $response->assertSee('Bulan Ini');
        $response->assertSee('Riwayat Kasir');
    }

    public function test_karyawan_can_filter_by_metode_bayar(): void
    {
        $karyawan = $this->getKaryawanUser();

        // Filter Tunai
        $respTunai = $this->actingAs($karyawan, 'web')
            ->get(route('admin.karyawan.riwayat', ['metode_bayar' => 'tunai']));
        $respTunai->assertStatus(200);

        // Filter Non Tunai
        $respNonTunai = $this->actingAs($karyawan, 'web')
            ->get(route('admin.karyawan.riwayat', ['metode_bayar' => 'non_tunai']));
        $respNonTunai->assertStatus(200);
    }

    public function test_guest_cannot_access_riwayat_kasir(): void
    {
        $response = $this->get(route('admin.karyawan.riwayat'));
        $response->assertRedirect('/admin/login');
    }
}
