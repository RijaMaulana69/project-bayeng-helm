<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pesanan;
use App\Models\Toko;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    private function getNamaBulan(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    private function buildReportData(string $jenisLaporan, int|string $tahun): array
    {
        $namaBulan = $this->getNamaBulan();

        if ($jenisLaporan === 'bulanan') {
            $results = Pesanan::where('status', 'completed')
                ->whereYear('created_at', $tahun)
                ->select(
                    DB::raw('MONTH(created_at) as bulan'),
                    DB::raw('COUNT(*) as total_transaksi'),
                    DB::raw('SUM(grand_total) as total_pendapatan')
                )
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get();

            $reportData = [];
            $chartLabels = [];
            $chartData = [];

            for ($m = 1; $m <= 12; $m++) {
                $row = $results->firstWhere('bulan', $m);
                $pesananCount = $row ? (int) $row->total_transaksi : 0;
                $omzet = $row ? (float) $row->total_pendapatan : 0;

                $reportData[] = [
                    'label' => $namaBulan[$m],
                    'total_pesanan' => $pesananCount,
                    'total_omzet' => $omzet,
                ];

                $chartLabels[] = $namaBulan[$m];
                $chartData[] = $omzet;
            }

            return [$reportData, $chartLabels, $chartData];
        }

        // Tahunan
        $results = Pesanan::where('status', 'completed')
            ->select(
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(grand_total) as total_pendapatan')
            )
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'))
            ->get();

        $reportData = [];
        $chartLabels = [];
        $chartData = [];

        foreach ($results as $r) {
            $reportData[] = [
                'label' => 'Tahun ' . $r->tahun,
                'total_pesanan' => (int) $r->total_transaksi,
                'total_omzet' => (float) $r->total_pendapatan,
            ];
            $chartLabels[] = (string) $r->tahun;
            $chartData[] = (float) $r->total_pendapatan;
        }

        return [$reportData, $chartLabels, $chartData];
    }

    public function penjualan(Request $request)
    {
        $jenisLaporan = $request->input('jenis_laporan', 'bulanan');
        $tahun = $request->input('tahun', date('Y'));

        [$reportData, $chartLabels, $chartData] = $this->buildReportData($jenisLaporan, $tahun);

        $availableYears = Pesanan::where('status', 'completed')
            ->selectRaw('DISTINCT YEAR(created_at) as tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        return view('admin.laporan.penjualan', [
            'jenis_laporan' => $jenisLaporan,
            'tahun' => $tahun,
            'chart_labels' => $chartLabels,
            'chart_data' => $chartData,
            'report_data' => $reportData,
            'available_years' => $availableYears,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $jenisLaporan = $request->input('jenis_laporan', 'bulanan');
        $tahun = $request->input('tahun', date('Y'));

        [$reportData] = $this->buildReportData($jenisLaporan, $tahun);
        $toko = Toko::first();
        $periode = $jenisLaporan === 'bulanan' ? "Tahun {$tahun}" : "Rekap Tahunan";

        // Ringkasan metrik eksekutif
        $totalPesanan = (int) array_sum(array_column($reportData, 'total_pesanan'));
        $totalOmzet = (float) array_sum(array_column($reportData, 'total_omzet'));
        $avgOmzet = $totalPesanan > 0 ? (float) ($totalOmzet / $totalPesanan) : 0;

        // Kontribusi persentase per baris
        foreach ($reportData as &$row) {
            $row['persentase'] = $totalOmzet > 0 ? round(($row['total_omzet'] / $totalOmzet) * 100, 1) : 0;
        }
        unset($row);

        // Periode terbaik (omzet tertinggi)
        $bestPeriod = collect($reportData)->sortByDesc('total_omzet')->first();

        // Encode logo dalam base64 untuk keandalan rendering Dompdf
        $logoBase64 = null;
        $logoPath = public_path('assets/logo.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $user = Auth::guard('web')->user();
        $nomorDokumen = 'BPH-LPJ/' . $tahun . '/' . str_pad(now()->month, 2, '0', STR_PAD_LEFT) . '/' . str_pad($user->id_user ?? 1, 3, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'jenis_laporan' => $jenisLaporan,
            'tahun' => $tahun,
            'report_data' => $reportData,
            'pengaturan' => $toko,
            'periode' => $periode,
            'total_pesanan' => $totalPesanan,
            'total_omzet' => $totalOmzet,
            'avg_omzet' => $avgOmzet,
            'best_period' => $bestPeriod,
            'logo_base64' => $logoBase64,
            'user' => $user,
            'nomor_dokumen' => $nomorDokumen,
            'tanggal_cetak' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ]);

        $pdf->setPaper('a4', 'portrait');

        if ($user) {
            ActivityLog::log('export_pdf', "Export PDF laporan penjualan {$jenisLaporan} tahun {$tahun}", 'user', $user->id_user, $user->username);
        }

        return $pdf->download("laporan-penjualan-{$jenisLaporan}-{$tahun}.pdf");
    }

    public function exportCsv(Request $request)
    {
        $jenisLaporan = $request->input('jenis_laporan', 'bulanan');
        $tahun = $request->input('tahun', date('Y'));

        [$reportData] = $this->buildReportData($jenisLaporan, $tahun);
        $toko = Toko::first();
        $user = Auth::guard('web')->user();

        if ($user) {
            ActivityLog::log('export_csv', "Export CSV laporan penjualan {$jenisLaporan} tahun {$tahun}", 'user', $user->id_user, $user->username);
        }

        $filename = "laporan-penjualan-{$jenisLaporan}-{$tahun}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $totalPesanan = (int) array_sum(array_column($reportData, 'total_pesanan'));
        $totalOmzet = (float) array_sum(array_column($reportData, 'total_omzet'));
        $periodeLabel = $jenisLaporan === 'bulanan' ? "Tahun {$tahun}" : "Rekap Tahunan";

        $callback = function () use ($reportData, $jenisLaporan, $tahun, $toko, $user, $periodeLabel, $totalPesanan, $totalOmzet) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM untuk kompatibilitas penuh Microsoft Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Metadata Identitas Usaha & Dokumen
            fputcsv($file, [strtoupper($toko->nama_toko ?? 'Bayeng Putra Helm')], ',', '"', '\\');
            fputcsv($file, ['Alamat: ' . ($toko->alamat ?? 'Jl. RE Martadinata No.49, Cijoho, Kuningan, Jawa Barat')], ',', '"', '\\');
            fputcsv($file, ['Kontak: ' . ($toko->telepon ?? '089664269624')], ',', '"', '\\');
            fwrite($file, "\r\n");
            fputcsv($file, ['LAPORAN PENJUALAN', strtoupper($periodeLabel)], ',', '"', '\\');
            fputcsv($file, ['Tanggal Ekspor', now()->format('d/m/Y H:i:s') . ' WIB'], ',', '"', '\\');
            fputcsv($file, ['Dicetak Oleh', ($user->nama_lengkap ?? $user->username ?? 'Administrator')], ',', '"', '\\');
            fwrite($file, "\r\n");

            // Baris Header Kolom
            fputcsv($file, [
                'No',
                $jenisLaporan === 'tahunan' ? 'Tahun' : 'Bulan',
                'Total Pesanan',
                'Total Omzet (Rp)',
                'Kontribusi Omzet (%)'
            ], ',', '"', '\\');

            // Data Rows
            $no = 1;
            foreach ($reportData as $row) {
                $omzet = (float) $row['total_omzet'];
                $kontribusi = $totalOmzet > 0 ? number_format(($omzet / $totalOmzet) * 100, 1, ',', '') . '%' : '0,0%';

                fputcsv($file, [
                    $no++,
                    $row['label'] ?? '-',
                    (int) $row['total_pesanan'],
                    $omzet,
                    $kontribusi,
                ], ',', '"', '\\');
            }

            // Separator & Summary Rows
            fwrite($file, "\r\n");
            fputcsv($file, [
                '',
                'TOTAL KESELURUHAN',
                $totalPesanan,
                $totalOmzet,
                '100,0%',
            ], ',', '"', '\\');

            $avgOmzet = $totalPesanan > 0 ? round($totalOmzet / $totalPesanan) : 0;
            fputcsv($file, [
                '',
                'Rata-rata Omzet per Pesanan',
                '-',
                $avgOmzet,
                '-',
            ], ',', '"', '\\');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
