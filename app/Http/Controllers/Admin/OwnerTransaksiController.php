<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['pelanggan', 'details.helm.merk', 'logs']);

        // 1. Filter Karyawan (Staf Kasir)
        $selectedKaryawan = $request->input('karyawan', 'all');
        if ($selectedKaryawan !== 'all' && !empty($selectedKaryawan)) {
            $userObj = is_numeric($selectedKaryawan) ? User::find($selectedKaryawan) : User::where('username', $selectedKaryawan)->first();
            $targetUsername = $userObj ? $userObj->username : $selectedKaryawan;
            
            $query->whereHas('logs', function ($q) use ($targetUsername) {
                $q->where('diubah_oleh', $targetUsername);
            });
        }

        // 2. Filter Status
        $selectedStatus = $request->input('status', 'all');
        if ($selectedStatus !== 'all' && !empty($selectedStatus)) {
            $query->where('status', $selectedStatus);
        }

        // 3. Filter Metode Bayar
        $selectedMetode = $request->input('metode_bayar', 'all');
        if ($selectedMetode !== 'all' && !empty($selectedMetode)) {
            $query->where('metode_bayar', $selectedMetode);
        }

        // 4. Filter Periode
        $selectedPeriod = $request->input('period', 'this_month');
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $selectedPeriod = 'custom';
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            switch ($selectedPeriod) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', $now->copy()->subDay()->toDateString());
                    break;
                case '7days':
                    $query->where('created_at', '>=', $now->copy()->subDays(7)->startOfDay());
                    break;
                case '30days':
                    $query->where('created_at', '>=', $now->copy()->subDays(30)->startOfDay());
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                    break;
                case 'all':
                    // no date filter
                    break;
            }
        }

        // 5. Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhere('kode_pickup', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%")
                         ->orWhere('telepon', 'like', "%{$search}%");
                  });
            });
        }

        // KPI calculations based on current filter
        $summaryQuery = clone $query;
        $allFiltered = $summaryQuery->get();

        $completedOrders = $allFiltered->where('status', 'completed');
        $kpi = [
            'total_transaksi' => $allFiltered->count(),
            'total_omzet'     => (float) $completedOrders->sum('grand_total'),
            'total_potensi'   => (float) $allFiltered->sum('grand_total'),
            'tunai_count'     => $allFiltered->where('metode_bayar', 'tunai')->count(),
            'tunai_total'     => (float) $completedOrders->where('metode_bayar', 'tunai')->sum('grand_total'),
            'online_count'    => $allFiltered->where('metode_bayar', '!=', 'tunai')->count(),
            'online_total'    => (float) $completedOrders->where('metode_bayar', '!=', 'tunai')->sum('grand_total'),
            'completed_count' => $completedOrders->count(),
            'pending_count'   => $allFiltered->whereIn('status', ['pending', 'unpaid_pickup', 'paid', 'processing', 'ready'])->count(),
        ];

        // List of all employees (karyawan)
        $karyawanUsers = User::where(function ($q) {
            $q->whereHas('role', function ($rq) {
                $rq->whereRaw('LOWER(nama_role) = ?', ['karyawan']);
            })->orWhere('id_role', 2);
        })->get();

        // Cashier Performance Breakdown
        $cashierStats = [];
        $usersByUsername = User::all()->keyBy('username');

        foreach ($karyawanUsers as $kUser) {
            $userOrders = $allFiltered->filter(function ($order) use ($kUser) {
                return $order->logs->contains(function ($log) use ($kUser) {
                    return $log->diubah_oleh === $kUser->username;
                });
            });

            $cashierStats[] = [
                'user'            => $kUser,
                'transaksi_count' => $userOrders->count(),
                'selesai_count'   => $userOrders->where('status', 'completed')->count(),
                'total_omzet'     => (float) $userOrders->where('status', 'completed')->sum('grand_total'),
            ];
        }

        // Paginated Orders
        $pesanans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.owner.transaksi-karyawan.index', compact(
            'pesanans',
            'kpi',
            'karyawanUsers',
            'cashierStats',
            'selectedKaryawan',
            'selectedStatus',
            'selectedMetode',
            'selectedPeriod',
            'usersByUsername'
        ));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['pelanggan', 'details.helm.merk', 'logs']);
        $usersByUsername = User::all()->keyBy('username');

        return view('admin.owner.transaksi-karyawan.show', compact('pesanan', 'usersByUsername'));
    }
}
