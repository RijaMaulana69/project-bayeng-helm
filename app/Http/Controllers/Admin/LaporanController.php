<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Toko;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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

    private function getNamaBulanPendek(): array
    {
        return [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
    }

    private function buildReportData(string $jenisLaporan, int|string $tahun, ?int $bulan = null): array
    {
        $namaBulan = $this->getNamaBulan();
        $namaBulanPendek = $this->getNamaBulanPendek();

        // 1. Mode Harian (Rincian per Tanggal dalam 1 Bulan)
        if ($jenisLaporan === 'harian') {
            $bulan = $bulan ? (int) $bulan : (int) date('n');
            $tahun = $tahun ? (int) $tahun : (int) date('Y');
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

            $orders = Pesanan::with('details.helm')
                ->where('status', 'completed')
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->get();

            $reportData = [];
            $chartLabels = [];
            $chartData = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dayOrders = $orders->filter(function ($order) use ($d) {
                    return (int) $order->created_at->format('j') === $d;
                });

                $pesananCount = $dayOrders->count();
                $omzet = (float) $dayOrders->sum('grand_total');
                $modal = (float) $dayOrders->sum(function ($order) {
                    return $order->details->sum(function ($detail) {
                        return ($detail->helm->harga_awal ?? 0) * $detail->qty;
                    });
                });
                $laba = $omzet - $modal;
                $tunai = (float) $dayOrders->where('metode_bayar', 'tunai')->sum('grand_total');
                $nonTunai = (float) $dayOrders->where('metode_bayar', '!=', 'tunai')->sum('grand_total');

                $reportData[] = [
                    'label' => sprintf('%02d %s', $d, $namaBulanPendek[$bulan] ?? ''),
                    'hari' => $d,
                    'total_pesanan' => $pesananCount,
                    'total_omzet' => $omzet,
                    'total_modal' => $modal,
                    'total_laba' => $laba,
                    'tunai_total' => $tunai,
                    'non_tunai_total' => $nonTunai,
                ];

                $chartLabels[] = (string) $d;
                $chartData[] = $omzet;
            }

            return [$reportData, $chartLabels, $chartData, $orders];
        }

        // 2. Mode Bulanan (Rincian 12 Bulan dalam 1 Tahun)
        if ($jenisLaporan === 'bulanan') {
            $tahun = $tahun ? (int) $tahun : (int) date('Y');
            $orders = Pesanan::with('details.helm')
                ->where('status', 'completed')
                ->whereYear('created_at', $tahun)
                ->get();

            $reportData = [];
            $chartLabels = [];
            $chartData = [];

            for ($m = 1; $m <= 12; $m++) {
                $monthOrders = $orders->filter(function ($order) use ($m) {
                    return (int) $order->created_at->format('n') === $m;
                });

                $pesananCount = $monthOrders->count();
                $omzet = (float) $monthOrders->sum('grand_total');
                $modal = (float) $monthOrders->sum(function ($order) {
                    return $order->details->sum(function ($detail) {
                        return ($detail->helm->harga_awal ?? 0) * $detail->qty;
                    });
                });
                $laba = $omzet - $modal;
                $tunai = (float) $monthOrders->where('metode_bayar', 'tunai')->sum('grand_total');
                $nonTunai = (float) $monthOrders->where('metode_bayar', '!=', 'tunai')->sum('grand_total');

                $reportData[] = [
                    'label' => $namaBulan[$m],
                    'bulan' => $m,
                    'total_pesanan' => $pesananCount,
                    'total_omzet' => $omzet,
                    'total_modal' => $modal,
                    'total_laba' => $laba,
                    'tunai_total' => $tunai,
                    'non_tunai_total' => $nonTunai,
                ];

                $chartLabels[] = $namaBulanPendek[$m];
                $chartData[] = $omzet;
            }

            return [$reportData, $chartLabels, $chartData, $orders];
        }

        // 3. Mode Tahunan (Rekap Historis Tahun ke Tahun)
        $orders = Pesanan::with('details.helm')
            ->where('status', 'completed')
            ->orderBy('created_at')
            ->get();

        $groupedYears = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y');
        });

        if ($groupedYears->isEmpty()) {
            $groupedYears = collect([date('Y') => collect()]);
        }

        $reportData = [];
        $chartLabels = [];
        $chartData = [];

        foreach ($groupedYears as $yearKey => $yearOrders) {
            $pesananCount = $yearOrders->count();
            $omzet = (float) $yearOrders->sum('grand_total');
            $modal = (float) $yearOrders->sum(function ($order) {
                return $order->details->sum(function ($detail) {
                    return ($detail->helm->harga_awal ?? 0) * $detail->qty;
                });
            });
            $laba = $omzet - $modal;
            $tunai = (float) $yearOrders->where('metode_bayar', 'tunai')->sum('grand_total');
            $nonTunai = (float) $yearOrders->where('metode_bayar', '!=', 'tunai')->sum('grand_total');

            $reportData[] = [
                'label' => 'Tahun ' . $yearKey,
                'tahun' => $yearKey,
                'total_pesanan' => $pesananCount,
                'total_omzet' => $omzet,
                'total_modal' => $modal,
                'total_laba' => $laba,
                'tunai_total' => $tunai,
                'non_tunai_total' => $nonTunai,
            ];

            $chartLabels[] = (string) $yearKey;
            $chartData[] = $omzet;
        }

        return [$reportData, $chartLabels, $chartData, $orders];
    }

    public function penjualan(Request $request)
    {
        $jenisLaporan = $request->input('jenis_laporan', 'bulanan');
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('n'));

        [$reportData, $chartLabels, $chartData, $periodOrders] = $this->buildReportData($jenisLaporan, $tahun, $bulan);

        $availableYears = Pesanan::where('status', 'completed')
            ->selectRaw('DISTINCT YEAR(created_at) as tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        // Top 5 Produk Helm Terlaris pada periode yang dipilih
        $completedOrderIds = $periodOrders->pluck('id_pesanan');
        $topProducts = DetailPesanan::whereIn('id_pesanan', $completedOrderIds)
            ->select('nama_helm', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_subtotal'))
            ->groupBy('nama_helm')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.laporan.penjualan', [
            'jenis_laporan'   => $jenisLaporan,
            'tahun'           => $tahun,
            'bulan'           => $bulan,
            'nama_bulan'      => $this->getNamaBulan(),
            'chart_labels'    => $chartLabels,
            'chart_data'      => $chartData,
            'report_data'     => $reportData,
            'available_years' => $availableYears,
            'top_products'    => $topProducts,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $jenisLaporan = $request->input('jenis_laporan', 'bulanan');
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('n'));

        [$reportData] = $this->buildReportData($jenisLaporan, $tahun, $bulan);
        $toko = Toko::first();

        $namaBulan = $this->getNamaBulan();
        if ($jenisLaporan === 'harian') {
            $periode = "Bulan {$namaBulan[$bulan]} {$tahun}";
        } elseif ($jenisLaporan === 'bulanan') {
            $periode = "Tahun {$tahun}";
        } else {
            $periode = "Rekap Tahunan";
        }

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
            'bulan' => $bulan,
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
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('n'));

        [$reportData] = $this->buildReportData($jenisLaporan, $tahun, $bulan);
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

        $namaBulan = $this->getNamaBulan();
        if ($jenisLaporan === 'harian') {
            $periodeLabel = "Bulan {$namaBulan[$bulan]} {$tahun}";
            $kolomPeriode = 'Tanggal';
        } elseif ($jenisLaporan === 'bulanan') {
            $periodeLabel = "Tahun {$tahun}";
            $kolomPeriode = 'Bulan';
        } else {
            $periodeLabel = "Rekap Tahunan";
            $kolomPeriode = 'Tahun';
        }

        $totalPesanan = (int) array_sum(array_column($reportData, 'total_pesanan'));
        $totalOmzet = (float) array_sum(array_column($reportData, 'total_omzet'));

        $callback = function () use ($reportData, $kolomPeriode, $toko, $user, $periodeLabel, $totalPesanan, $totalOmzet) {
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
                $kolomPeriode,
                'Total Pesanan',
                'Total Omzet (Rp)',
                'Modal HPP (Rp)',
                'Laba Bersih (Rp)',
                'Tunai (Rp)',
                'Non-Tunai (Rp)',
                'Kontribusi Omzet (%)'
            ], ',', '"', '\\');

            // Data Rows
            $no = 1;
            foreach ($reportData as $row) {
                $omzet = (float) $row['total_omzet'];
                $modal = (float) ($row['total_modal'] ?? 0);
                $laba = (float) ($row['total_laba'] ?? 0);
                $tunai = (float) ($row['tunai_total'] ?? 0);
                $nonTunai = (float) ($row['non_tunai_total'] ?? 0);
                $kontribusi = $totalOmzet > 0 ? number_format(($omzet / $totalOmzet) * 100, 1, ',', '') . '%' : '0,0%';

                fputcsv($file, [
                    $no++,
                    $row['label'],
                    $row['total_pesanan'],
                    number_format($omzet, 0, ',', '.'),
                    number_format($modal, 0, ',', '.'),
                    number_format($laba, 0, ',', '.'),
                    number_format($tunai, 0, ',', '.'),
                    number_format($nonTunai, 0, ',', '.'),
                    $kontribusi,
                ], ',', '"', '\\');
            }

            // Total Keseluruhan
            fwrite($file, "\r\n");
            fputcsv($file, [
                'TOTAL',
                'KESELURUHAN',
                $totalPesanan,
                number_format($totalOmzet, 0, ',', '.'),
                number_format(array_sum(array_column($reportData, 'total_modal')), 0, ',', '.'),
                number_format(array_sum(array_column($reportData, 'total_laba')), 0, ',', '.'),
                number_format(array_sum(array_column($reportData, 'tunai_total')), 0, ',', '.'),
                number_format(array_sum(array_column($reportData, 'non_tunai_total')), 0, ',', '.'),
                '100,0%'
            ], ',', '"', '\\');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
