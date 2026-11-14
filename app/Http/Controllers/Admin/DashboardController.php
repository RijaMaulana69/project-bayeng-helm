<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helm;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        $user->update(['last_active' => now()]);

        // Stats
        $total_produk = Helm::count();
        $total_pesanan = Pesanan::count();
        $pesanan_hari_ini = Pesanan::whereDate('created_at', today())->count();
        $pesanan_siap_ambil = Pesanan::where('status', 'ready')->count();
        $omzet_hari_ini = Pesanan::where('status', 'completed')->whereDate('created_at', today())->sum('grand_total');
        $omzet_bulan = Pesanan::where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('grand_total');

        // Stok menipis (< 5)
        $stok_menipis = Helm::where('stok', '>', 0)->where('stok', '<', 5)->orderBy('stok')->take(5)->get();

        // Chart: Penjualan bulanan tahun ini
        $chart_data = Pesanan::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total_pesanan, SUM(grand_total) as total_omzet')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $chart_labels = [];
        $chart_values = [];
        $bulan_names = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 1; $i <= 12; $i++) {
            $chart_labels[] = $bulan_names[$i];
            $row = $chart_data->firstWhere('bulan', $i);
            $chart_values[] = $row ? (float) $row->total_omzet : 0;
        }

        // Pesanan terbaru
        $pesanan_terbaru = Pesanan::with('pelanggan')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'user', 'total_produk', 'total_pesanan', 'pesanan_hari_ini',
            'pesanan_siap_ambil', 'omzet_hari_ini', 'omzet_bulan',
            'stok_menipis', 'chart_labels', 'chart_values', 'pesanan_terbaru'
        ));
    }
}
