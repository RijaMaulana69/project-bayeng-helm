<?php

namespace Tests\Feature;

use App\Models\Helm;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\User;
use App\Models\VarianHelm;
use App\Models\Warna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminProdukTest extends TestCase
{
    protected function getAdmin()
    {
        return User::where('id_role', 1)->first() ?? User::first();
    }

    public function test_admin_can_view_produk_index(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin, 'web')
            ->get(route('admin.produk.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Produk Helm', false);
        $response->assertSee('Tambah Produk Baru');
    }

    public function test_admin_can_search_and_filter_produk(): void
    {
        $admin = $this->getAdmin();
        $helm = Helm::first();

        $response = $this->actingAs($admin, 'web')
            ->get(route('admin.produk.index', ['search' => $helm->nama_helm]));

        $response->assertStatus(200);
        $response->assertSee($helm->nama_helm);
    }

    public function test_admin_can_view_create_page(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin, 'web')
            ->get(route('admin.produk.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Produk Helm Baru');
        $response->assertSee('Simpan Produk');
    }

    public function test_admin_can_view_edit_page(): void
    {
        $admin = $this->getAdmin();
        $helm = Helm::first();

        $response = $this->actingAs($admin, 'web')
            ->get(route('admin.produk.edit', $helm->id_helm));

        $response->assertStatus(200);
        $response->assertSee('Edit Produk Helm');
        $response->assertSee($helm->nama_helm);
        $response->assertSee('Simpan Perubahan');
    }

    public function test_admin_can_create_and_update_produk_with_variants(): void
    {
        $admin = $this->getAdmin();
        $merk = Merk::first();
        $kategori = Kategori::first();

        $namaHelm = 'Helm Uji Coba Modern ' . uniqid();

        // Create
        $response = $this->actingAs($admin, 'web')
            ->post(route('admin.produk.store'), [
                'nama_helm' => $namaHelm,
                'id_merk' => $merk->id_merk,
                'id_kategori' => $kategori->id_kategori,
                'harga_awal' => 200000,
                'harga_jual' => 300000,
                'deskripsi' => 'Deskripsi helm uji coba modern',
                'warna' => ['Black Solid', 'White Glossy'],
                'ukuran' => ['M', 'L'],
                'variant_stok' => [
                    'Black Solid|M' => 5,
                    'Black Solid|L' => 3,
                    'White Glossy|M' => 2,
                ],
            ]);

        $response->assertRedirect(route('admin.produk.index'));

        $helm = Helm::where('nama_helm', $namaHelm)->first();
        $this->assertNotNull($helm);
        $this->assertEquals(10, $helm->stok); // 5 + 3 + 2 = 10
        $this->assertEquals(3, $helm->varians()->count());

        // Update
        $updateResponse = $this->actingAs($admin, 'web')
            ->put(route('admin.produk.update', $helm->id_helm), [
                'nama_helm' => $namaHelm . ' Updated',
                'id_merk' => $merk->id_merk,
                'id_kategori' => $kategori->id_kategori,
                'harga_awal' => 210000,
                'harga_jual' => 320000,
                'deskripsi' => 'Deskripsi terupdate',
                'warna' => ['Black Solid'],
                'ukuran' => ['M'],
                'variant_stok' => [
                    'Black Solid|M' => 15,
                ],
            ]);

        $updateResponse->assertRedirect(route('admin.produk.index'));

        $helm->refresh();
        $this->assertEquals($namaHelm . ' Updated', $helm->nama_helm);
        $this->assertEquals(15, $helm->stok);

        // Delete cleanup
        $deleteResponse = $this->actingAs($admin, 'web')
            ->delete(route('admin.produk.destroy', $helm->id_helm));

        $deleteResponse->assertRedirect(route('admin.produk.index'));
        $this->assertNull(Helm::find($helm->id_helm));
    }

    public function test_karyawan_can_view_catalog_and_create_products_without_cost_prices_or_delete_action(): void
    {
        $karyawan = User::where('id_role', 2)->first();

        if (!$karyawan) {
            $this->markTestSkipped('No user with id_role = 2 (Karyawan) found.');
        }

        $response = $this->actingAs($karyawan, 'web')
            ->get(route('admin.produk.index'));

        $response->assertStatus(200);
        $response->assertSee('Harga Jual');
        $response->assertDontSee('Modal: Rp');
        $response->assertSee('Tambah Produk Baru');

        // Can access create page
        $this->actingAs($karyawan, 'web')
            ->get(route('admin.produk.create'))
            ->assertStatus(200);

        // Cannot delete product (Owner only)
        $this->actingAs($karyawan, 'web')
            ->delete(route('admin.produk.destroy', 1))
            ->assertStatus(403);
    }

    public function test_admin_can_view_riwayat_tab(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin, 'web')
            ->get(route('admin.produk.index', ['tab' => 'riwayat']));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Stok Masuk');
    }

    public function test_admin_can_filter_by_stok_status(): void
    {
        $admin = $this->getAdmin();

        foreach (['kritis_habis', 'habis', 'tersedia', 'all'] as $filter) {
            $response = $this->actingAs($admin, 'web')
                ->get(route('admin.produk.index', ['stok_filter' => $filter]));

            $response->assertStatus(200);
            $response->assertSee('Data Produk Helm');
        }
    }
}
