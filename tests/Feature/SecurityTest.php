<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    public function test_guest_cannot_access_customer_orders(): void
    {
        $response = $this->get('/pelanggan/pesanan');

        $response->assertRedirect('/pelanggan/login');
    }

    public function test_idor_protection_prevents_viewing_other_customers_orders(): void
    {
        // Customer 1 and Customer 2
        $customer1 = Pelanggan::firstOrCreate(
            ['email' => 'customer1@test.com'],
            ['nama' => 'Customer 1', 'no_hp' => '081234567891', 'password' => Hash::make('Password123!')]
        );

        $customer2 = Pelanggan::firstOrCreate(
            ['email' => 'customer2@test.com'],
            ['nama' => 'Customer 2', 'no_hp' => '081234567892', 'password' => Hash::make('Password123!')]
        );

        // Order belonging to Customer 1
        $pesanan = Pesanan::firstOrCreate(
            ['no_pesanan' => 'ORD-TEST-IDOR-01'],
            [
                'id_pelanggan'  => $customer1->id_pelanggan,
                'kode_pickup'   => 'PKP-TEST01',
                'total_belanja' => 350000,
                'grand_total'   => 350000,
                'metode_bayar'  => 'tunai',
                'status'        => 'pending',
            ]
        );

        // Customer 2 attempts to view Customer 1's order
        $response = $this->actingAs($customer2, 'pelanggan')
            ->get('/pelanggan/pesanan/' . $pesanan->id_pesanan);

        $response->assertStatus(403);
    }

    public function test_karyawan_cannot_access_owner_only_routes(): void
    {
        // Role 2 is Karyawan
        $karyawan = User::where('id_role', 2)->first();

        if ($karyawan) {
            $response = $this->actingAs($karyawan, 'web')
                ->get('/admin/user');

            $response->assertStatus(403);
        } else {
            $this->markTestSkipped('No user with id_role = 2 (Karyawan) found.');
        }
    }

    public function test_owner_can_access_user_management(): void
    {
        // Role 1 is Owner
        $owner = User::where('id_role', 1)->first();

        if ($owner) {
            $response = $this->actingAs($owner, 'web')
                ->get('/admin/user');

            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('No user with id_role = 1 (Owner) found.');
        }
    }
}
