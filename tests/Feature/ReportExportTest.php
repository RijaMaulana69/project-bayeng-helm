<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    public function test_owner_can_export_sales_report_to_pdf(): void
    {
        $owner = User::where('id_role', 1)->first();

        if (!$owner) {
            $this->markTestSkipped('No user with id_role = 1 (Owner) found.');
        }

        $response = $this->actingAs($owner, 'web')
            ->get('/admin/laporan-penjualan/export-pdf?jenis_laporan=bulanan&tahun=' . date('Y'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_owner_can_export_sales_report_to_csv(): void
    {
        $owner = User::where('id_role', 1)->first();

        if (!$owner) {
            $this->markTestSkipped('No user with id_role = 1 (Owner) found.');
        }

        $response = $this->actingAs($owner, 'web')
            ->get('/admin/laporan-penjualan/export-csv?jenis_laporan=bulanan&tahun=' . date('Y'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
