<?php

namespace Tests\Feature;

use App\Models\Helm;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\User;
use App\Models\VarianHelm;
use App\Models\Warna;
use Tests\TestCase;

class AdminAtributTest extends TestCase
{
    protected function getAdmin()
    {
        return User::where('id_role', 1)->first() ?? User::first();
    }

    public function test_admin_can_view_atribut_hub(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin, 'web')
            ->get(route('admin.atribut.index'));

        $response->assertStatus(200);
        $response->assertSee('Atribut');
        $response->assertSee('Taksonomi');
        $response->assertSee('Palet Warna Swatch');
    }

    public function test_legacy_attribute_routes_redirect_to_hub(): void
    {
        $admin = $this->getAdmin();

        $this->actingAs($admin, 'web')
            ->get(route('admin.merk.index'))
            ->assertRedirect(route('admin.atribut.index', ['tab' => 'merk']));

        $this->actingAs($admin, 'web')
            ->get(route('admin.kategori.index'))
            ->assertRedirect(route('admin.atribut.index', ['tab' => 'kategori']));

        $this->actingAs($admin, 'web')
            ->get(route('admin.warna.index'))
            ->assertRedirect(route('admin.atribut.index', ['tab' => 'warna']));
    }

    public function test_admin_can_crud_merk(): void
    {
        $admin = $this->getAdmin();
        $brandName = 'Brand Uji ' . uniqid();

        // Create
        $response = $this->actingAs($admin, 'web')
            ->post(route('admin.merk.store'), [
                'nama_merk' => $brandName,
                'deskripsi' => 'Deskripsi merk uji coba',
            ]);

        $response->assertRedirect(route('admin.atribut.index', ['tab' => 'merk']));

        $merk = Merk::where('nama_merk', $brandName)->first();
        $this->assertNotNull($merk);

        // Update
        $updateResp = $this->actingAs($admin, 'web')
            ->put(route('admin.merk.update', $merk->id_merk), [
                'nama_merk' => $brandName . ' Edit',
                'deskripsi' => 'Deskripsi merk terupdate',
            ]);

        $updateResp->assertRedirect(route('admin.atribut.index', ['tab' => 'merk']));
        $this->assertDatabaseHas('merk', ['nama_merk' => $brandName . ' Edit']);

        // Delete
        $delResp = $this->actingAs($admin, 'web')
            ->delete(route('admin.merk.destroy', $merk->id_merk));

        $delResp->assertRedirect(route('admin.atribut.index', ['tab' => 'merk']));
        $this->assertNull(Merk::find($merk->id_merk));
    }

    public function test_admin_can_crud_kategori(): void
    {
        $admin = $this->getAdmin();
        $catName = 'Kategori Uji ' . uniqid();

        // Create
        $response = $this->actingAs($admin, 'web')
            ->post(route('admin.kategori.store'), [
                'nama_kategori' => $catName,
                'deskripsi' => 'Deskripsi kategori uji',
                'icon' => 'fa-solid fa-gem',
            ]);

        $response->assertRedirect(route('admin.atribut.index', ['tab' => 'kategori']));

        $kategori = Kategori::where('nama_kategori', $catName)->first();
        $this->assertNotNull($kategori);

        // Update
        $updateResp = $this->actingAs($admin, 'web')
            ->put(route('admin.kategori.update', $kategori->id_kategori), [
                'nama_kategori' => $catName . ' Edit',
                'deskripsi' => 'Deskripsi terupdate',
                'icon' => 'fa-solid fa-crown',
            ]);

        $updateResp->assertRedirect(route('admin.atribut.index', ['tab' => 'kategori']));
        $this->assertDatabaseHas('kategori', ['nama_kategori' => $catName . ' Edit']);

        // Delete
        $delResp = $this->actingAs($admin, 'web')
            ->delete(route('admin.kategori.destroy', $kategori->id_kategori));

        $delResp->assertRedirect(route('admin.atribut.index', ['tab' => 'kategori']));
        $this->assertNull(Kategori::find($kategori->id_kategori));
    }

    public function test_admin_can_crud_warna(): void
    {
        $admin = $this->getAdmin();
        $warnaName = 'Electric Blue ' . uniqid();

        // Create
        $response = $this->actingAs($admin, 'web')
            ->post(route('admin.warna.store'), [
                'nama_warna' => $warnaName,
                'kode_hex' => '#06B6D4',
                'jenis_finishing' => 'metallic',
            ]);

        $response->assertRedirect(route('admin.atribut.index', ['tab' => 'warna']));

        $warna = Warna::where('nama_warna', $warnaName)->first();
        $this->assertNotNull($warna);
        $this->assertEquals('#06B6D4', $warna->kode_hex);
        $this->assertEquals('metallic', $warna->jenis_finishing);

        // Update
        $updateResp = $this->actingAs($admin, 'web')
            ->put(route('admin.warna.update', $warna->id_warna), [
                'nama_warna' => $warnaName . ' Pearl',
                'kode_hex' => '#0891B2',
                'jenis_finishing' => 'glossy',
            ]);

        $updateResp->assertRedirect(route('admin.atribut.index', ['tab' => 'warna']));
        $this->assertDatabaseHas('warna', ['nama_warna' => $warnaName . ' Pearl', 'kode_hex' => '#0891B2']);

        // Delete
        $delResp = $this->actingAs($admin, 'web')
            ->delete(route('admin.warna.destroy', $warna->id_warna));

        $delResp->assertRedirect(route('admin.atribut.index', ['tab' => 'warna']));
        $this->assertNull(Warna::find($warna->id_warna));
    }
}
