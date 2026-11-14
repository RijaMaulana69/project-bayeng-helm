<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Helm;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    public function test_customer_can_view_checkout_page_with_selected_items(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Checkout Tester',
            'email' => 'checkout_' . rand(1000, 9999) . '@test.com',
            'telepon' => '081288887777',
            'password' => Hash::make('Password123!'),
        ]);

        $helm = Helm::with('varians')->whereHas('varians', function ($q) {
            $q->where('stok', '>', 0);
        })->first();

        $varian = $helm->varians->where('stok', '>', 0)->first();

        Cart::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_helm' => $helm->id_helm,
            'id_varian' => $varian->id_varian,
            'qty' => 1,
            'selected' => 1,
        ]);

        $response = $this->actingAs($pelanggan, 'pelanggan')
            ->get(route('pelanggan.checkout'));

        $response->assertStatus(200);
        $response->assertSee('Checkout');
        $response->assertSee('Gerai Bayeng Putra Helm Kuningan');
        $response->assertSee($helm->nama_helm);
        $response->assertSee('Buat Pesanan');
    }

    public function test_customer_can_place_order_with_bayar_di_toko(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Buyer Tester',
            'email' => 'buyer_' . rand(1000, 9999) . '@test.com',
            'telepon' => '081266665555',
            'password' => Hash::make('Password123!'),
        ]);

        $helm = Helm::with('varians')->whereHas('varians', function ($q) {
            $q->where('stok', '>', 5);
        })->first();

        $varian = $helm->varians->where('stok', '>', 5)->first();

        Cart::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_helm' => $helm->id_helm,
            'id_varian' => $varian->id_varian,
            'qty' => 1,
            'selected' => 1,
        ]);

        $response = $this->actingAs($pelanggan, 'pelanggan')
            ->post(route('pelanggan.checkout.store'), [
                'penerima' => 'Buyer Tester',
                'telepon' => '081266665555',
                'metode_bayar' => 'bayar_di_toko',
                'catatan' => 'Tolong siapkan helmnya',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pesanan', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'metode_bayar' => 'tunai',
            'status' => 'unpaid_pickup',
        ]);
    }
}
