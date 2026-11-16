<?php

namespace Tests\Feature;

use App\Models\Toko;
use App\Models\User;
use Tests\TestCase;

class PengaturanTest extends TestCase
{
    private function getOwner(): ?User
    {
        return User::where('id_role', 1)
            ->orWhereHas('role', fn($q) => $q->whereRaw('LOWER(nama_role) = ?', ['owner']))
            ->first();
    }

    public function test_owner_can_view_pengaturan_page(): void
    {
        $owner = $this->getOwner();
        if (!$owner) {
            $this->markTestSkipped('No owner found.');
        }

        $response = $this->actingAs($owner, 'web')->get('/admin/pengaturan');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Toko');
        $response->assertSee('Nama Toko');
        $response->assertSee('Nomor Telepon');
        $response->assertSee('Alamat Gerai');
    }

    public function test_owner_can_update_pengaturan(): void
    {
        $owner = $this->getOwner();
        if (!$owner) {
            $this->markTestSkipped('No owner found.');
        }

        $toko = Toko::first();

        $response = $this->actingAs($owner, 'web')->put('/admin/pengaturan', [
            'nama_toko' => 'Bayeng Putra Helm Testing',
            'telepon'   => '081234567890',
            'alamat'    => 'Jl. RE Martadinata No.49 Kuningan',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('toko', [
            'nama_toko' => 'Bayeng Putra Helm Testing',
            'telepon'   => '081234567890',
        ]);

        // Revert back
        if ($toko) {
            $toko->update([
                'nama_toko' => 'Bayeng Putra Helm',
                'telepon'   => '089664269624',
                'alamat'    => 'Jl. RE Martadinata No.49, Cijoho, Kuningan, Jawa Barat',
            ]);
        }
    }
}
