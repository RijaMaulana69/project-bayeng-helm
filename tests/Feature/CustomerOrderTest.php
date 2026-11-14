<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\Helm;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    public function test_customer_can_see_order_history_with_status_filter(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Order History Tester',
            'email' => 'history_' . uniqid() . '@test.com',
            'telepon' => '0812' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        $helm = Helm::first();

        $pesanan = Pesanan::create([
            'no_pesanan' => 'ORD-TEST-' . rand(1000, 9999),
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'penerima' => $pelanggan->nama,
            'telepon' => $pelanggan->telepon,
            'kode_pickup' => 'PKP-' . rand(1000, 9999),
            'total_belanja' => 450000,
            'grand_total' => 450000,
            'metode_bayar' => 'tunai',
            'status' => 'ready',
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_helm' => $helm->id_helm,
            'nama_helm' => $helm->nama_helm,
            'warna' => 'Black',
            'ukuran' => 'L',
            'harga' => 450000,
            'qty' => 1,
            'subtotal' => 450000,
        ]);

        // Test index 'semua'
        $response = $this->actingAs($pelanggan, 'pelanggan')
            ->get(route('pelanggan.pesanan'));

        $response->assertStatus(200);
        $response->assertSee('Pesanan Saya');
        $response->assertSee($pesanan->no_pesanan);
        $response->assertSee($helm->nama_helm);
        $response->assertSee('Siap Diambil');

        // Test tab filter 'siap_diambil'
        $tabResponse = $this->actingAs($pelanggan, 'pelanggan')
            ->get(route('pelanggan.pesanan', ['status' => 'siap_diambil']));

        $tabResponse->assertStatus(200);
        $tabResponse->assertSee($pesanan->no_pesanan);

        // Test tab filter 'selesai' (should not see this ready order)
        $completedTabResponse = $this->actingAs($pelanggan, 'pelanggan')
            ->get(route('pelanggan.pesanan', ['status' => 'selesai']));

        $completedTabResponse->assertStatus(200);
        $completedTabResponse->assertDontSee($pesanan->no_pesanan);
    }

    public function test_customer_can_view_order_detail(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Order Detail Tester',
            'email' => 'detail_' . uniqid() . '@test.com',
            'telepon' => '0812' . rand(10000000, 99999999),
            'password' => Hash::make('Password123!'),
        ]);

        $helm = Helm::first();

        $pesanan = Pesanan::create([
            'no_pesanan' => 'ORD-DETAIL-' . rand(1000, 9999),
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'penerima' => $pelanggan->nama,
            'telepon' => $pelanggan->telepon,
            'kode_pickup' => 'PKP-' . rand(1000, 9999),
            'total_belanja' => 450000,
            'grand_total' => 450000,
            'metode_bayar' => 'tunai',
            'status' => 'unpaid_pickup',
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_helm' => $helm->id_helm,
            'nama_helm' => $helm->nama_helm,
            'warna' => 'Matte Grey',
            'ukuran' => 'XL',
            'harga' => 450000,
            'qty' => 1,
            'subtotal' => 450000,
        ]);

        $response = $this->actingAs($pelanggan, 'pelanggan')
            ->get(route('pelanggan.pesanan.detail', $pesanan->id_pesanan));

        $response->assertStatus(200);
        $response->assertSee('Lacak Pesanan');
        $response->assertSee('Menunggu Pengambilan di Gerai');
        $response->assertSee('Pesanan Dibuat');
        $response->assertSee($pesanan->no_pesanan);
        $response->assertSee($pesanan->kode_pickup);
        $response->assertSee($helm->nama_helm);
        $response->assertSee('Gerai Bayeng Putra Helm Kuningan');
    }
}
