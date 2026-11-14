<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Bayeng Putra Helm</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .report-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-title h2 {
            font-size: 16px;
            color: #2563eb;
            margin-bottom: 4px;
        }

        .report-title .period {
            font-size: 12px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #2563eb;
            color: #fff;
            padding: 8px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        table .text-center {
            text-align: center;
        }

        table .text-right {
            text-align: right;
        }

        .totals-row {
            background-color: #f1f5f9 !important;
            font-weight: bold;
        }

        .totals-row td {
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bayeng Putra Helm</h1>
        <p>{{ $pengaturan->alamat ?? 'Alamat Toko' }} | Telp: {{ $pengaturan->telepon ?? '-' }}</p>
    </div>

    <div class="report-title">
        <h2>Laporan Penjualan</h2>
        <div class="period">Periode: {{ $periode ?? '-' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>{{ $jenis_laporan == 'tahunan' ? 'Tahun' : 'Bulan' }}</th>
                <th class="text-center">Total Pesanan</th>
                <th class="text-right">Total Omzet</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $grandPesanan = 0; @endphp
            @foreach ($report_data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['label'] ?? $row['periode'] ?? '-' }}</td>
                    <td class="text-center">{{ $row['total_pesanan'] }}</td>
                    <td class="text-right">{{ format_rupiah($row['total_omzet']) }}</td>
                </tr>
                @php $grandTotal += $row['total_omzet']; $grandPesanan += $row['total_pesanan']; @endphp
            @endforeach
            <tr class="totals-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ $grandPesanan }}</strong></td>
                <td class="text-right"><strong>{{ format_rupiah($grandTotal) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y, H:i:s') }} | Bayeng Putra Helm &mdash; Sistem Manajemen
    </div>
</body>
</html>
