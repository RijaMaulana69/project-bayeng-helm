<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StorePickupTest extends TestCase
{
    public function test_cashier_can_validate_and_handover_pickup_order(): void
    {
        $karyawan = User::where('id_role', 2)->first() ?? User::first();

        $customer = Pelanggan::firstOrCreate(
            ['email' => 'pickup_customer@test.com'],
            ['nama' => 'Pickup Customer', 'no_hp' => '081234567899', 'password' => Hash::make('Password123!')]
        );

        $orderNumber = 'ORD-TEST-PKP-' . uniqid();
        $pickupCode  = strtoupper(substr(uniqid('PKP'), 0, 16));

        $pesanan = Pesanan::create([
            'no_pesanan'    => $orderNumber,
            'id_pelanggan'  => $customer->id_pelanggan,
            'kode_pickup'   => $pickupCode,
            'total_belanja' => 450000,
            'grand_total'   => 450000,
            'metode_bayar'  => 'tunai',
            'status'        => 'ready',
        ]);

        // 1. Cashier validates pickup code
        $response = $this->actingAs($karyawan, 'web')
            ->post('/admin/pickup/validasi', [
                'kode_pickup' => $pickupCode,
            ]);

        $response->assertStatus(200);
        $response->assertSee($orderNumber);
        $response->assertSee($pickupCode);

        // 2. Cashier hands over items
        $handoverResponse = $this->actingAs($karyawan, 'web')
            ->post('/admin/pickup/' . $pesanan->id_pesanan . '/serahkan');

        $handoverResponse->assertRedirect('/admin/pickup');
        $handoverResponse->assertSessionHas('success');

        // 3. Verify status changed to completed in database
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status'     => 'completed',
        ]);
    }

    public function test_invalid_pickup_code_returns_error(): void
    {
        $karyawan = User::where('id_role', 2)->first() ?? User::first();

        $response = $this->actingAs($karyawan, 'web')
            ->from('/admin/pickup')
            ->post('/admin/pickup/validasi', [
                'kode_pickup' => 'PKP-NOTEXIST',
            ]);

        $response->assertRedirect('/admin/pickup');
        $response->assertSessionHas('error');
    }

    public function test_unprepared_order_in_processing_cannot_be_validated_at_pickup(): void
    {
        $karyawan = User::where('id_role', 2)->first() ?? User::first();

        $customer = Pelanggan::create([
            'nama' => 'Preparation Customer ' . uniqid(),
            'email' => 'prep_' . uniqid() . '@test.com',
            'telepon' => '0812' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        $pickupCode = 'PKP-PREP-' . strtoupper(substr(uniqid(), 0, 4));

        $pesanan = Pesanan::create([
            'no_pesanan'    => 'ORD-PREP-' . uniqid(),
            'id_pelanggan'  => $customer->id_pelanggan,
            'kode_pickup'   => $pickupCode,
            'total_belanja' => 200000,
            'grand_total'   => 200000,
            'metode_bayar'  => 'transfer',
            'status'        => 'processing', // still being prepared in warehouse
        ]);

        $response = $this->actingAs($karyawan, 'web')
            ->from('/admin/pickup')
            ->post('/admin/pickup/validasi', [
                'kode_pickup' => $pickupCode,
            ]);

        $response->assertRedirect('/admin/pickup');
        $response->assertSessionHas('error');
    }
}
