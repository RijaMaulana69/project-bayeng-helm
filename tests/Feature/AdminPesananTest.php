<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPesananTest extends TestCase
{
    public function test_admin_can_view_modern_pesanan_index(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'pesanan_tester'],
            [
                'nama_lengkap' => 'Pesanan Admin Tester',
                'password' => Hash::make('Password123!'),
                'id_role' => 1,
            ]
        );

        $customer = Pelanggan::create([
            'nama' => 'Customer Pesanan Test ' . uniqid(),
            'email' => 'pesanan_test_' . uniqid() . '@test.com',
            'telepon' => '0812' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        $pesanan = Pesanan::create([
            'no_pesanan' => 'ORD-TEST-' . uniqid(),
            'id_pelanggan' => $customer->id_pelanggan,
            'kode_pickup' => 'PKP-' . strtoupper(substr(uniqid(), 0, 6)),
            'total_belanja' => 350000,
            'grand_total' => 350000,
            'metode_bayar' => 'transfer',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'web')->get(route('admin.pesanan.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pesanan');
        $response->assertSee($pesanan->no_pesanan);
        $response->assertSee($pesanan->kode_pickup);
        $response->assertSee('Perlu Verifikasi');

        // Verify elimination of legacy bootstrap AI slop classes
        $response->assertDontSee('card-bayeng');
        $response->assertDontSee('form-select');
        $response->assertDontSee('table-responsive');
    }

    public function test_admin_can_filter_and_search_pesanan(): void
    {
        $admin = User::where('id_role', 1)->first() ?? User::first();

        $customer = Pelanggan::create([
            'nama' => 'Joko Anwar ' . uniqid(),
            'email' => 'joko_' . uniqid() . '@test.com',
            'telepon' => '0813' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        $orderNum = 'ORD-SEARCH-' . uniqid();
        $pesanan = Pesanan::create([
            'no_pesanan' => $orderNum,
            'id_pelanggan' => $customer->id_pelanggan,
            'kode_pickup' => 'PKP-SRCH',
            'total_belanja' => 500000,
            'grand_total' => 500000,
            'metode_bayar' => 'qris',
            'status' => 'paid',
        ]);

        // Search by order number
        $response = $this->actingAs($admin, 'web')
            ->get(route('admin.pesanan.index', ['search' => $orderNum]));

        $response->assertStatus(200);
        $response->assertSee($orderNum);

        // Filter by status
        $statusResponse = $this->actingAs($admin, 'web')
            ->get(route('admin.pesanan.index', ['status' => 'paid']));

        $statusResponse->assertStatus(200);
        $statusResponse->assertSee($orderNum);
    }

    public function test_admin_can_view_pesanan_detail_and_update_status(): void
    {
        $admin = User::where('id_role', 1)->first() ?? User::first();

        $customer = Pelanggan::create([
            'nama' => 'Detail Customer ' . uniqid(),
            'email' => 'detail_' . uniqid() . '@test.com',
            'telepon' => '0815' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        $pesanan = Pesanan::create([
            'no_pesanan' => 'ORD-DETAIL-' . uniqid(),
            'id_pelanggan' => $customer->id_pelanggan,
            'kode_pickup' => 'PKP-DTL',
            'total_belanja' => 400000,
            'grand_total' => 400000,
            'metode_bayar' => 'transfer',
            'status' => 'pending',
        ]);

        // View detail
        $viewResponse = $this->actingAs($admin, 'web')
            ->get(route('admin.pesanan.show', $pesanan->id_pesanan));

        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($pesanan->no_pesanan);
        $viewResponse->assertSee('Perlu Verifikasi');
        $viewResponse->assertSee('Konfirmasi Pembayaran (Sudah Bayar)');

        // Update status from pending to paid
        $updateResponse = $this->actingAs($admin, 'web')
            ->put(route('admin.pesanan.update-status', $pesanan->id_pesanan), [
                'status' => 'paid',
                'catatan' => 'Pembayaran transfer BCA telah diverifikasi.',
            ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status' => 'paid',
        ]);
    }

    public function test_ready_order_cannot_be_marked_completed_directly_in_pesanan_controller(): void
    {
        $admin = User::where('id_role', 1)->first() ?? User::first();

        $customer = Pelanggan::create([
            'nama' => 'Ready Customer ' . uniqid(),
            'email' => 'ready_' . uniqid() . '@test.com',
            'telepon' => '0816' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        $pesanan = Pesanan::create([
            'no_pesanan' => 'ORD-READY-' . uniqid(),
            'id_pelanggan' => $customer->id_pelanggan,
            'kode_pickup' => 'PKP-RDY',
            'total_belanja' => 400000,
            'grand_total' => 400000,
            'metode_bayar' => 'transfer',
            'status' => 'ready',
        ]);

        // Attempting to complete a ready order directly in PesananController should fail
        $response = $this->actingAs($admin, 'web')
            ->put(route('admin.pesanan.update-status', $pesanan->id_pesanan), [
                'status' => 'completed',
            ]);

        $response->assertSessionHasErrors('status');

        // Status should remain ready
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status' => 'ready',
        ]);
    }
}
