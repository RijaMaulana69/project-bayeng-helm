@php
    $totalPesanan = $total_pesanan ?? (int) array_sum(array_column($report_data, 'total_pesanan'));
    $totalOmzet = $total_omzet ?? (float) array_sum(array_column($report_data, 'total_omzet'));
    $avgOmzet = $avg_omzet ?? ($totalPesanan > 0 ? (float) ($totalOmzet / $totalPesanan) : 0);
    $bestPeriod = $best_period ?? collect($report_data)->sortByDesc('total_omzet')->first();
    $toko = $pengaturan ?? \App\Models\Toko::first();
    $currentUser = $user ?? auth()->user();
    $tanggalCetak = $tanggal_cetak ?? (now()->translatedFormat('d F Y, H:i') . ' WIB');
    $nomorDokumen = $nomor_dokumen ?? ('BPH-LPJ/' . ($tahun ?? date('Y')) . '/' . str_pad(now()->month, 2, '0', STR_PAD_LEFT) . '/' . str_pad($currentUser->id_user ?? 1, 3, '0', STR_PAD_LEFT));

    if (empty($logo_base64)) {
        $logoPath = public_path('assets/logo.png');
        if (file_exists($logoPath)) {
            $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - {{ $toko->nama_toko ?? 'Bayeng Putra Helm' }}</title>
    <style>
        @page {
            margin: 12mm 14mm 14mm 14mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.4;
        }

        /* Kop Surat Toko */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .kop-logo {
            width: 65px;
            vertical-align: middle;
            text-align: center;
        }

        .kop-logo img {
            max-width: 58px;
            max-height: 58px;
        }

        .kop-info {
            vertical-align: middle;
            padding-left: 14px;
        }

        .kop-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-sub {
            font-size: 9pt;
            color: #475569;
            margin-bottom: 2px;
        }

        .kop-address {
            font-size: 8.5pt;
            color: #64748b;
        }

        /* Garis Pemisah Resmi Kop Surat */
        .kop-divider {
            border-top: 2px solid #0f172a;
            border-bottom: 0.75px solid #0f172a;
            height: 2px;
            margin-bottom: 14px;
        }

        /* Header Judul & Metadata Dokumen */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .doc-title-cell {
            vertical-align: top;
        }

        .doc-main-title {
            font-size: 12.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .doc-periode-badge {
            font-size: 9pt;
            color: #334155;
            font-weight: 500;
        }

        .doc-meta-cell {
            vertical-align: top;
            text-align: right;
        }

        .meta-table {
            float: right;
            border-collapse: collapse;
            font-size: 8pt;
            color: #475569;
        }

        .meta-table td {
            padding: 1.5px 4px;
        }

        .meta-label {
            color: #64748b;
            text-align: right;
        }

        .meta-val {
            font-weight: 600;
            color: #0f172a;
            text-align: left;
        }

        /* Ringkasan Eksekutif (KPI Strip) */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 14px;
            margin-left: -6px;
            margin-right: -6px;
        }

        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            vertical-align: top;
            width: 25%;
        }

        .kpi-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .kpi-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }

        .kpi-sub {
            font-size: 7.5pt;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Tabel Rincian Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 8px;
            border: 1px solid #0f172a;
        }

        .data-table td {
            padding: 6px 8px;
            font-size: 8.5pt;
            border: 1px solid #e2e8f0;
            color: #334155;
        }

        .data-table tr.even {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-semibold { font-weight: 600; }

        .total-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 9pt;
        }

        /* Lembar Pengesahan & Catatan */
        .closing-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .closing-notes {
            width: 58%;
            vertical-align: top;
            padding-right: 20px;
        }

        .closing-notes-title {
            font-size: 8pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .closing-notes ol {
            font-size: 7.5pt;
            color: #64748b;
            padding-left: 14px;
            line-height: 1.45;
        }

        .closing-sign {
            width: 42%;
            vertical-align: top;
            text-align: center;
        }

        .sign-city-date {
            font-size: 8.5pt;
            color: #334155;
            margin-bottom: 2px;
        }

        .sign-role {
            font-size: 8.5pt;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 48px;
        }

        .sign-name {
            font-size: 9pt;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }

        .sign-meta {
            font-size: 7.5pt;
            color: #64748b;
        }

        /* Dokumen Footer */
        .doc-footer {
            margin-top: 18px;
            padding-top: 6px;
            border-top: 0.5px solid #cbd5e1;
            font-size: 7.5pt;
            color: #94a3b8;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left { text-align: left; }
        .footer-right { text-align: right; }
    </style>
</head>
<body>
    <!-- Kop Surat Resmi Usaha -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if (!empty($logo_base64))
                    <img src="{{ $logo_base64 }}" alt="Logo Toko">
                @else
                    <div style="font-weight: bold; font-size: 11pt; color: #0f172a;">BPH</div>
                @endif
            </td>
            <td class="kop-info">
                <div class="kop-title">{{ strtoupper($toko->nama_toko ?? 'BAYENG PUTRA HELM') }}</div>
                <div class="kop-sub">Pusat Helm Berkualitas &amp; Aksesoris Terlengkap</div>
                <div class="kop-address">{{ $toko->alamat ?? 'Jl. RE Martadinata No.49, Cijoho, Kuningan, Jawa Barat' }} | Telp / WA: {{ $toko->telepon ?? '0896-6426-9624' }}</div>
            </td>
        </tr>
    </table>

    <div class="kop-divider"></div>

    <!-- Judul & Metadata Dokumen -->
    <table class="doc-header-table">
        <tr>
            <td class="doc-title-cell">
                <div class="doc-main-title">Laporan Penjualan</div>
                <div class="doc-periode-badge">Periode: {{ $periode ?? '-' }}</div>
            </td>
            <td class="doc-meta-cell">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">No. Dokumen</td>
                        <td>:</td>
                        <td class="meta-val">{{ $nomorDokumen }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal Cetak</td>
                        <td>:</td>
                        <td class="meta-val">{{ $tanggalCetak }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Dicetak Oleh</td>
                        <td>:</td>
                        <td class="meta-val">{{ $currentUser->nama_lengkap ?? $currentUser->username ?? 'Administrator' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Ringkasan Eksekutif (KPI Strip) -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <div class="kpi-label">Total Omzet</div>
                <div class="kpi-value">{{ format_rupiah($totalOmzet) }}</div>
                <div class="kpi-sub">Akumulasi pendapatan</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Volume Pesanan</div>
                <div class="kpi-value">{{ number_format($totalPesanan, 0, ',', '.') }} Pesanan</div>
                <div class="kpi-sub">Total transaksi selesai</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Rata-rata Transaksi</div>
                <div class="kpi-value">{{ format_rupiah($avgOmzet) }}</div>
                <div class="kpi-sub">Nilai per pesanan</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Periode Puncak</div>
                <div class="kpi-value">{{ $bestPeriod['label'] ?? '-' }}</div>
                <div class="kpi-sub">
                    @if (!empty($bestPeriod) && ($bestPeriod['total_omzet'] ?? 0) > 0)
                        {{ format_rupiah($bestPeriod['total_omzet']) }}
                    @else
                        Belum ada transaksi
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;" class="text-center">No</th>
                <th style="width: 32%;" class="text-left">{{ $jenis_laporan == 'tahunan' ? 'Tahun' : 'Bulan' }}</th>
                <th style="width: 20%;" class="text-center">Total Pesanan</th>
                <th style="width: 24%;" class="text-right">Total Omzet (Rp)</th>
                <th style="width: 18%;" class="text-right">Kontribusi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report_data as $i => $row)
                @php
                    $omzet = (float) ($row['total_omzet'] ?? 0);
                    $pesanan = (int) ($row['total_pesanan'] ?? 0);
                    $persen = isset($row['persentase']) ? (float) $row['persentase'] : ($totalOmzet > 0 ? ($omzet / $totalOmzet) * 100 : 0);
                @endphp
                <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="font-semibold">{{ $row['label'] ?? $row['periode'] ?? '-' }}</td>
                    <td class="text-center">{{ number_format($pesanan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ format_rupiah($omzet) }}</td>
                    <td class="text-right">{{ number_format($persen, 1, ',', '.') }}%</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-center font-semibold">TOTAL KESELURUHAN</td>
                <td class="text-center font-semibold">{{ number_format($totalPesanan, 0, ',', '.') }}</td>
                <td class="text-right font-semibold">{{ format_rupiah($totalOmzet) }}</td>
                <td class="text-right font-semibold">100,0%</td>
            </tr>
        </tbody>
    </table>

    <!-- Lembar Pengesahan & Catatan -->
    <table class="closing-table">
        <tr>
            <td class="closing-notes">
                <div class="closing-notes-title">Ketentuan &amp; Catatan Dokumen</div>
                <ol>
                    <li>Laporan ini disusun secara otomatis berdasarkan data transaksi dengan status pesanan <strong>selesai (completed)</strong>.</li>
                    <li>Total omzet adalah akumulasi nilai transaksi penjualan yang telah tervalidasi pada sistem kasir dan penjualan online.</li>
                    <li>Dokumen ini sah dan diterbitkan sebagai arsip resmi pertanggungjawaban operasional {{ $toko->nama_toko ?? 'Bayeng Putra Helm' }}.</li>
                </ol>
            </td>
            <td class="closing-sign">
                <div class="sign-city-date">Kuningan, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="sign-role">Penanggung Jawab Operasional,</div>
                <div class="sign-name">{{ $currentUser->nama_lengkap ?? $currentUser->username ?? 'Bayeng Putra Helm' }}</div>
                <div class="sign-meta">Pemilik / Pengelola Toko</div>
            </td>
        </tr>
    </table>

    <!-- Footer Dokumen -->
    <div class="doc-footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">{{ $toko->nama_toko ?? 'Bayeng Putra Helm' }} &bull; Sistem Informasi Manajemen Toko</td>
                <td class="footer-right">Dokumen Internal Resmi</td>
            </tr>
        </table>
    </div>
</body>
</html>