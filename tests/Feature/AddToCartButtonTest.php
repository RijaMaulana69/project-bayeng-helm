<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\AddToCartButton;
use App\Models\Helm;
use App\Models\Pelanggan;

class AddToCartButtonTest extends TestCase
{
    public function test_component_renders_with_variant_and_size_options(): void
    {
        $helm = Helm::with('varians')->whereHas('varians')->first();
        $this->assertNotNull($helm);

        Livewire::test(AddToCartButton::class, ['helm' => $helm])
            ->assertSee('Pilih Varian')
            ->assertSee('Pilih Size')
            ->assertSee('Masukkan Keranjang')
            ->assertDontSee('Wajib pilih 1');
    }

    public function test_selecting_color_and_size_resolves_variant(): void
    {
        $helm = Helm::with('varians')->whereHas('varians')->first();
        $variant = $helm->varians->first();

        Livewire::test(AddToCartButton::class, ['helm' => $helm])
            ->call('selectWarna', $variant->warna)
            ->assertSet('selectedWarna', $variant->warna)
            ->call('selectUkuran', $variant->ukuran)
            ->assertSet('selectedUkuran', $variant->ukuran)
            ->assertSet('selectedVarian', $variant->id_varian);
    }

    public function test_authenticated_customer_can_add_to_cart(): void
    {
        $pelanggan = Pelanggan::first() ?? Pelanggan::create([
            'nama' => 'Tester PDP',
            'email' => 'pdp_test_' . rand(100, 999) . '@test.com',
            'telepon' => '081299998888',
            'password' => bcrypt('password'),
        ]);

        $helm = Helm::with('varians')->whereHas('varians', function ($q) {
            $q->where('stok', '>', 0);
        })->first();

        $variant = $helm->varians->where('stok', '>', 0)->first();

        Livewire::actingAs($pelanggan, 'pelanggan')
            ->test(AddToCartButton::class, ['helm' => $helm])
            ->call('selectWarna', $variant->warna)
            ->call('selectUkuran', $variant->ukuran)
            ->call('addToCart')
            ->assertDispatched('cart-updated')
            ->assertDispatched('cart-added-success');

        $this->assertDatabaseHas('cart', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_helm' => $helm->id_helm,
            'id_varian' => $variant->id_varian,
        ]);
    }
}
