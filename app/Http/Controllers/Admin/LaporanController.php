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

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'jenis_laporan' => $jenisLaporan,
            'tahun' => $tahun,
            'report_data' => $reportData,
            'pengaturan' => $toko,
            'periode' => $periode,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('export_pdf', "Export PDF laporan penjualan {$jenisLaporan} tahun {$tahun}", 'user', $user->id_user, $user->username);

        return $pdf->download("laporan-penjualan-{$jenisLaporan}-{$tahun}.pdf");
    }

    public function exportCsv(Request $request)
    {
        $jenisLaporan = $request->input('jenis_laporan', 'bulanan');
        $tahun = $request->input('tahun', date('Y'));

        [$reportData] = $this->buildReportData($jenisLaporan, $tahun);

        $user = Auth::guard('web')->user();
        ActivityLog::log('export_csv', "Export CSV laporan penjualan {$jenisLaporan} tahun {$tahun}", 'user', $user->id_user, $user->username);

        $filename = "laporan-penjualan-{$jenisLaporan}-{$tahun}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($reportData, $jenisLaporan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [$jenisLaporan === 'tahunan' ? 'Tahun' : 'Bulan', 'Total Pesanan', 'Total Omzet (Rp)']);

            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row['label'],
                    $row['total_pesanan'],
                    $row['total_omzet'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
