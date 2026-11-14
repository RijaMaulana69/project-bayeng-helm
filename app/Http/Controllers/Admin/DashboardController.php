<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helm;
use App\Models\Pesanan;
use App\Models\VarianHelm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            $user->update(['last_active' => now()]);
        }

        $isOwner = $user ? $user->isOwner() : false;

        // 1. Operational Real-time Counts (Shared by both roles)
        $pickup_ready_count = Pesanan::where('status', 'ready')->count();
        $pickup_unpaid_count = Pesanan::where('status', 'unpaid_pickup')->count();
        $pesanan_siap_ambil = $pickup_ready_count + $pickup_unpaid_count; // Total awaiting customer pickup
        $pesanan_perlu_disiapkan = Pesanan::whereIn('status', ['paid', 'processing'])->count();
        $pesanan_pending_count = Pesanan::where('status', 'pending')->count();
        $pesanan_hari_ini = Pesanan::whereDate('created_at', today())->count();
        $total_pesanan = Pesanan::count();
        $total_produk = Helm::count();
        $total_varian = VarianHelm::count();

        // 2. Realistic Inventory Tracking (Variant Level)
        $varian_habis_count = VarianHelm::where('stok', 0)->count();
        $varian_menipis_count = VarianHelm::where('stok', '>', 0)->where('stok', '<=', 3)->count();
        $total_stok_kritis = $varian_habis_count + $varian_menipis_count;

        // Critical variants needing restock
        $stok_kritis_varian = VarianHelm::with(['helm.merk'])
            ->where('stok', '<=', 3)
            ->orderBy('stok', 'asc')
            ->orderBy('id_varian', 'asc')
            ->take(6)
            ->get();

        // Helmet-level low stock for test assertions
        $stok_menipis = Helm::where('stok', '<', 5)->orderBy('stok')->take(5)->get();

        // 3. Recent Orders
        $pesanan_terbaru = Pesanan::with(['pelanggan', 'details.helm.merk'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // 4. Financial & Analytics Data (Exclusively computed for Owner)
        $omzet_hari_ini = Pesanan::where('status', 'completed')->whereDate('created_at', today())->sum('grand_total');
        $omzet_bulan = Pesanan::where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('grand_total');

        $selected_period = 'this_month';
        $periode_label = 'Bulan Ini (' . now()->translatedFormat('M Y') . ')';
        $period_total_pesanan = 0;
        $period_completed_pesanan = 0;
        $period_omzet = 0;
        $period_aov = 0;
        $chart_labels = [];
        $chart_values = [];
        $chart_orders = [];
        $daily_labels = [];
        $daily_values = [];
        $daily_orders = [];

        if ($isOwner) {
            // Dynamic Period Filter for Owner
            $validPeriods = ['today', 'yesterday', '7days', '30days', 'this_month', 'all'];
            $selected_period = $request->query('period');

            if (!$selected_period || !in_array($selected_period, $validPeriods)) {
                $hasToday = Pesanan::whereDate('created_at', today())->exists();
                $selected_period = $hasToday ? 'today' : 'this_month';
            }

            $periodLabels = [
                'today'      => 'Hari Ini (' . now()->translatedFormat('d M') . ')',
                'yesterday'  => 'Kemarin (' . now()->subDay()->translatedFormat('d M') . ')',
                '7days'      => '7 Hari Terakhir',
                '30days'     => '30 Hari Terakhir',
                'this_month' => 'Bulan Ini (' . now()->translatedFormat('M Y') . ')',
                'all'        => 'Semua Waktu',
            ];
            $periode_label = $periodLabels[$selected_period] ?? 'Bulan Ini';

            // Filter query for dynamic period
            $periodQuery = Pesanan::query();
            switch ($selected_period) {
                case 'today':
                    $periodQuery->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $periodQuery->whereDate('created_at', today()->subDay());
                    break;
                case '7days':
                    $periodQuery->where('created_at', '>=', now()->subDays(7)->startOfDay());
                    break;
                case '30days':
                    $periodQuery->where('created_at', '>=', now()->subDays(30)->startOfDay());
                    break;
                case 'this_month':
                    $periodQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'all':
                default:
                    break;
            }

            $period_total_pesanan = (clone $periodQuery)->count();
            $period_completed_pesanan = (clone $periodQuery)->where('status', 'completed')->count();
            $period_omzet = (clone $periodQuery)->where('status', 'completed')->sum('grand_total');
            $period_aov = $period_completed_pesanan > 0 ? (float) ($period_omzet / $period_completed_pesanan) : 0;

            // Monthly Chart Data (Year-to-date)
            $monthlyData = Pesanan::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total_pesanan, SUM(grand_total) as total_omzet')
                ->groupByRaw('MONTH(created_at)')
                ->get();

            $bulan_names = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($i = 1; $i <= 12; $i++) {
                $chart_labels[] = $bulan_names[$i];
                $row = $monthlyData->firstWhere('bulan', $i);
                $chart_values[] = $row ? (float) $row->total_omzet : 0;
                $chart_orders[] = $row ? (int) $row->total_pesanan : 0;
            }

            // Daily Chart Data (Past 14 Days)
            $dailyData = Pesanan::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->selectRaw('DATE(created_at) as tgl, COUNT(*) as total_pesanan, SUM(grand_total) as total_omzet')
                ->groupByRaw('DATE(created_at)')
                ->get();

            for ($i = 13; $i >= 0; $i--) {
                $dateStr = now()->subDays($i)->format('Y-m-d');
                $daily_labels[] = now()->subDays($i)->translatedFormat('d M');
                $row = $dailyData->firstWhere('tgl', $dateStr);
                $daily_values[] = $row ? (float) $row->total_omzet : 0;
                $daily_orders[] = $row ? (int) $row->total_pesanan : 0;
            }
        }

        return view('admin.dashboard', compact(
            'user',
            'isOwner',
            'selected_period',
            'periode_label',
            'period_total_pesanan',
            'period_completed_pesanan',
            'period_omzet',
            'period_aov',
            'pickup_ready_count',
            'pickup_unpaid_count',
            'pesanan_siap_ambil',
            'pesanan_perlu_disiapkan',
            'pesanan_pending_count',
            'pesanan_hari_ini',
            'omzet_hari_ini',
            'omzet_bulan',
            'total_pesanan',
            'total_produk',
            'total_varian',
            'varian_habis_count',
            'varian_menipis_count',
            'total_stok_kritis',
            'stok_kritis_varian',
            'stok_menipis',
            'chart_labels',
            'chart_values',
            'chart_orders',
            'daily_labels',
            'daily_values',
            'daily_orders',
            'pesanan_terbaru'
        ));
    }
}
