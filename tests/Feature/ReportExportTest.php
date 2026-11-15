<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    private function getOwnerUser(): ?User
    {
        return User::where('id_role', 1)
            ->orWhereHas('role', fn($q) => $q->whereRaw('LOWER(nama_role) = ?', ['owner']))
            ->first();
    }

    public function test_owner_can_view_sales_report_page(): void
    {
        $owner = $this->getOwnerUser();
        if (!$owner) {
            $this->markTestSkipped('No owner found.');
        }

        // Test Bulanan
        $response = $this->actingAs($owner, 'web')
            ->get(route('admin.laporan.penjualan', ['jenis_laporan' => 'bulanan', 'tahun' => date('Y')]));
        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Total Omzet');
        $response->assertSee('Beban Modal (HPP)');

        // Test Harian
        $responseHarian = $this->actingAs($owner, 'web')
            ->get(route('admin.laporan.penjualan', ['jenis_laporan' => 'harian', 'tahun' => date('Y'), 'bulan' => date('n')]));
        $responseHarian->assertStatus(200);
        $responseHarian->assertSee('Harian (Per Tanggal)');

        // Test Tahunan
        $responseTahunan = $this->actingAs($owner, 'web')
            ->get(route('admin.laporan.penjualan', ['jenis_laporan' => 'tahunan']));
        $responseTahunan->assertStatus(200);
        $responseTahunan->assertSee('Tahunan');
    }

    public function test_owner_can_export_sales_report_to_pdf(): void
    {
        $owner = $this->getOwnerUser();
        if (!$owner) {
            $this->markTestSkipped('No owner found.');
        }

        // Bulanan
        $response = $this->actingAs($owner, 'web')
            ->get('/admin/laporan-penjualan/export-pdf?jenis_laporan=bulanan&tahun=' . date('Y'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // Harian
        $responseHarian = $this->actingAs($owner, 'web')
            ->get('/admin/laporan-penjualan/export-pdf?jenis_laporan=harian&tahun=' . date('Y') . '&bulan=' . date('n'));

        $responseHarian->assertStatus(200);
        $responseHarian->assertHeader('content-type', 'application/pdf');
    }

    public function test_owner_can_export_sales_report_to_csv(): void
    {
        $owner = $this->getOwnerUser();
        if (!$owner) {
            $this->markTestSkipped('No owner found.');
        }

        // Bulanan
        $response = $this->actingAs($owner, 'web')
            ->get('/admin/laporan-penjualan/export-csv?jenis_laporan=bulanan&tahun=' . date('Y'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));

        // Harian
        $responseHarian = $this->actingAs($owner, 'web')
            ->get('/admin/laporan-penjualan/export-csv?jenis_laporan=harian&tahun=' . date('Y') . '&bulan=' . date('n'));

        $responseHarian->assertStatus(200);
        $this->assertStringContainsString('text/csv', $responseHarian->headers->get('content-type'));
    }
}
