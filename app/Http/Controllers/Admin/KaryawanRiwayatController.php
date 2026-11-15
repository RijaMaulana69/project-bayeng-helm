<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KaryawanRiwayatController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        if (!in_array($period, ['today', 'this_month'])) {
            $period = 'today';
        }

        $now = Carbon::now();
        if ($period === 'today') {
            $periodDateLabel = $now->translatedFormat('l, d F Y');
        } else {
            $periodDateLabel = $now->translatedFormat('F Y');
        }

        // Riwayat Kasir HANYA memuat transaksi yang SUDAH SELESAI (status = completed)
        $periodQuery = Pesanan::where('status', 'completed');
        if ($period === 'today') {
            $periodQuery->whereDate('created_at', today());
        } else {
            $periodQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
        }

        // 1. Total Penjualan (Omzet kasir selesai)
        $totalPenjualan = (clone $periodQuery)->sum('grand_total');
        $totalTransaksi = (clone $periodQuery)->count();

        // 2. Kas Tunai
        $uangTunai = (clone $periodQuery)
            ->where('metode_bayar', 'tunai')
            ->sum('grand_total');
        $uangTunaiCount = (clone $periodQuery)
            ->where('metode_bayar', 'tunai')
            ->count();

        // 3. Non-Tunai (QRIS / Transfer Bank)
        $nonTunai = (clone $periodQuery)
            ->where('metode_bayar', '!=', 'tunai')
            ->sum('grand_total');
        $nonTunaiCount = (clone $periodQuery)
            ->where('metode_bayar', '!=', 'tunai')
            ->count();

        // 4. Helm Fisik Terjual
        $completedOrderIds = (clone $periodQuery)->pluck('id_pesanan');
        $helmTerjual = DetailPesanan::whereIn('id_pesanan', $completedOrderIds)->sum('qty');

        // Tab counts: hanya pesanan yang sudah selesai
        $countHariIni = Pesanan::where('status', 'completed')->whereDate('created_at', today())->count();
        $countBulanIni = Pesanan::where('status', 'completed')->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();

        // Query daftar transaksi (hanya transaksi selesai)
        $listQuery = (clone $periodQuery)->with(['pelanggan', 'details.helm', 'details.varian']);

        // Filter metode pembayaran
        $metodeBayar = $request->get('metode_bayar', 'all');
        if ($metodeBayar === 'tunai') {
            $listQuery->where('metode_bayar', 'tunai');
        } elseif ($metodeBayar === 'non_tunai') {
            $listQuery->where('metode_bayar', '!=', 'tunai');
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $listQuery->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhere('kode_pickup', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('telepon', 'like', "%{$search}%");
                  });
            });
        }

        $pesanans = $listQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $kpi = [
            'total_penjualan'   => $totalPenjualan,
            'total_transaksi'   => $totalTransaksi,
            'uang_tunai'        => $uangTunai,
            'uang_tunai_count'  => $uangTunaiCount,
            'non_tunai'         => $nonTunai,
            'non_tunai_count'   => $nonTunaiCount,
            'helm_terjual'      => $helmTerjual,
            'count_today'       => $countHariIni,
            'count_month'       => $countBulanIni,
        ];

        return view('admin.karyawan.riwayat.index', compact(
            'pesanans',
            'period',
            'periodDateLabel',
            'metodeBayar',
            'kpi'
        ));
    }
}
