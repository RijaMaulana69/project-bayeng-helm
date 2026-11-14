<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\CartManager;
use App\Models\Cart;
use App\Models\Helm;
use App\Models\Pelanggan;

class CartManagerTest extends TestCase
{
    public function test_customer_can_see_cart_items(): void
    {
        $pelanggan = Pelanggan::first() ?? Pelanggan::create([
            'nama' => 'Test Cart',
            'email' => 'cart_test_' . rand(100, 999) . '@test.com',
            'telepon' => '081299991111',
            'password' => bcrypt('password'),
        ]);

        $helm = Helm::with('varians')->whereHas('varians', function ($q) {
            $q->where('stok', '>', 0);
        })->first();

        $variant = $helm->varians->where('stok', '>', 0)->first();

        Cart::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_helm' => $helm->id_helm,
            'id_varian' => $variant->id_varian,
            'qty' => 1,
            'selected' => 1,
        ]);

        Livewire::actingAs($pelanggan, 'pelanggan')
            ->test(CartManager::class)
            ->assertSee($helm->nama_helm)
            ->assertSee('Ringkasan Belanja')
            ->assertSee('Pilih Semua');
    }

    public function test_toggle_select_item_and_select_all(): void
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Toggle Tester',
            'email' => 'toggle_' . rand(1000, 9999) . '@test.com',
            'telepon' => '081299992222',
            'password' => bcrypt('password'),
        ]);
        $helm = Helm::with('varians')->whereHas('varians')->first();
        $variant = $helm->varians->first();

        $cart = Cart::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_helm' => $helm->id_helm,
            'id_varian' => $variant->id_varian,
            'qty' => 1,
            'selected' => 1,
        ]);

        Livewire::actingAs($pelanggan, 'pelanggan')
            ->test(CartManager::class)
            ->call('toggleSelectItem', $cart->id_cart)
            ->assertSet('selectedItems', [])
            ->call('toggleSelectAll')
            ->assertCount('selectedItems', 1);
    }

    public function test_remove_from_cart(): void
    {
        $pelanggan = Pelanggan::first();
        $helm = Helm::with('varians')->whereHas('varians')->first();
        $variant = $helm->varians->first();

        $cart = Cart::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_helm' => $helm->id_helm,
            'id_varian' => $variant->id_varian,
            'qty' => 1,
            'selected' => 1,
        ]);

        Livewire::actingAs($pelanggan, 'pelanggan')
            ->test(CartManager::class)
            ->call('removeFromCart', $cart->id_cart)
            ->assertDispatched('cart-updated');

        $this->assertDatabaseMissing('cart', [
            'id_cart' => $cart->id_cart,
        ]);
    }
}
