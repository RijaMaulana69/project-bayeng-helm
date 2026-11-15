<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helm;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\StokMasuk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerBarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = StokMasuk::with(['helm.merk', 'helm.kategori', 'user']);

        // 1. Filter Petugas Karyawan
        $selectedUser = $request->input('user_id', 'all');
        if ($selectedUser !== 'all' && !empty($selectedUser)) {
            $query->where('id_user', $selectedUser);
        }

        // 2. Filter Merk
        $selectedMerk = $request->input('id_merk', 'all');
        if ($selectedMerk !== 'all' && !empty($selectedMerk)) {
            $query->whereHas('helm', function ($q) use ($selectedMerk) {
                $q->where('id_merk', $selectedMerk);
            });
        }

        // 3. Filter Kategori
        $selectedKategori = $request->input('id_kategori', 'all');
        if ($selectedKategori !== 'all' && !empty($selectedKategori)) {
            $query->whereHas('helm', function ($q) use ($selectedKategori) {
                $q->where('id_kategori', $selectedKategori);
            });
        }

        // 4. Filter Periode
        $selectedPeriod = $request->input('period', 'this_month');
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $selectedPeriod = 'custom';
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('tgl_masuk', [$startDate, $endDate]);
        } else {
            switch ($selectedPeriod) {
                case 'today':
                    $query->whereDate('tgl_masuk', $now->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('tgl_masuk', $now->copy()->subDay()->toDateString());
                    break;
                case '7days':
                    $query->where('tgl_masuk', '>=', $now->copy()->subDays(7)->startOfDay());
                    break;
                case '30days':
                    $query->where('tgl_masuk', '>=', $now->copy()->subDays(30)->startOfDay());
                    break;
                case 'this_month':
                    $query->whereMonth('tgl_masuk', $now->month)->whereYear('tgl_masuk', $now->year);
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
                $q->where('warna', 'like', "%{$search}%")
                  ->orWhere('ukuran', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('helm', function ($hq) use ($search) {
                      $hq->where('nama_helm', 'like', "%{$search}%");
                  });
            });
        }

        // KPI Calculations based on filtered dataset
        $summaryQuery = clone $query;
        $allFiltered = $summaryQuery->get();

        $totalUnitMasuk = (int) $allFiltered->sum('jumlah');
        $totalModalMasuk = (float) $allFiltered->sum(function ($item) {
            return $item->jumlah * ($item->harga_awal ?? 0);
        });
        $totalPotensiOmzet = (float) $allFiltered->sum(function ($item) {
            return $item->jumlah * ($item->harga_jual ?? 0);
        });
        $potensiMargin = $totalPotensiOmzet - $totalModalMasuk;

        $kpi = [
            'total_unit'      => $totalUnitMasuk,
            'total_modal'     => $totalModalMasuk,
            'total_omzet'     => $totalPotensiOmzet,
            'potensi_margin'  => $potensiMargin,
            'total_frekuensi' => $allFiltered->count(),
        ];

        // Master lists for filters
        $users = User::where(function ($q) {
            $q->whereHas('role', function ($rq) {
                $rq->whereRaw('LOWER(nama_role) = ?', ['karyawan']);
            })->orWhere('id_role', 2);
        })->get();
        if ($users->isEmpty()) {
            $users = User::all();
        }

        $merks = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // Paginated records
        $stokMasuks = $query->orderBy('tgl_masuk', 'desc')
                            ->orderBy('id_stok_masuk', 'desc')
                            ->paginate(15)
                            ->withQueryString();

        return view('admin.owner.barang-masuk.index', compact(
            'stokMasuks',
            'kpi',
            'users',
            'merks',
            'kategoris',
            'selectedUser',
            'selectedMerk',
            'selectedKategori',
            'selectedPeriod'
        ));
    }
}
