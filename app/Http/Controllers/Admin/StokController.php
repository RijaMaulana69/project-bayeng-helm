<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Helm;
use App\Models\StokMasuk;
use App\Models\VarianHelm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'stok'); // 'stok' atau 'riwayat'
        $filter = $request->get('filter', 'all'); // 'all', 'kritis_habis', 'habis', 'aman'
        $search = $request->filled('search') ? trim($request->search) : null;

        // Statistik persediaan varian helm
        $totalVariants    = VarianHelm::count();
        $habisCount       = VarianHelm::where('stok', '<=', 0)->count();
        $kritisCount      = VarianHelm::where('stok', '>', 0)->where('stok', '<=', 3)->count();
        $totalKritisHabis = $kritisCount + $habisCount;
        $amanCount        = VarianHelm::where('stok', '>', 3)->count();

        // Query varian helm untuk monitoring fisik
        $varianQuery = VarianHelm::with(['helm.merk', 'helm.kategori']);

        if ($search) {
            $varianQuery->where(function ($q) use ($search) {
                $q->where('warna', 'like', "%{$search}%")
                  ->orWhere('ukuran', 'like', "%{$search}%")
                  ->orWhereHas('helm', function ($qh) use ($search) {
                      $qh->where('nama_helm', 'like', "%{$search}%")
                         ->orWhereHas('merk', function ($qm) use ($search) {
                             $qm->where('nama_merk', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($filter === 'habis') {
            $varianQuery->where('stok', '<=', 0);
        } elseif ($filter === 'kritis') {
            $varianQuery->where('stok', '>', 0)->where('stok', '<=', 3);
        } elseif ($filter === 'kritis_habis') {
            $varianQuery->where('stok', '<=', 3);
        } elseif ($filter === 'aman') {
            $varianQuery->where('stok', '>', 3);
        }

        $varians = $varianQuery->orderBy('stok', 'asc')->paginate(15)->withQueryString();

        // Riwayat Stok Masuk
        $stok_masuks = StokMasuk::with(['helm.merk', 'user'])
            ->orderBy('id_stok_masuk', 'desc')
            ->paginate(15, ['*'], 'history_page')
            ->withQueryString();

        // Data helm untuk modal restok
        $helms = Helm::with('varians')->orderBy('nama_helm', 'asc')->get();

        $helm_variants = [];
        foreach ($helms as $h) {
            $helm_variants[$h->id_helm] = $h->varians->map(function ($v) {
                return [
                    'id_varian' => $v->id_varian,
                    'label'     => $v->warna . ' - ' . $v->ukuran . ' (Stok: ' . $v->stok . ')',
                    'stok'      => $v->stok,
                    'warna'     => $v->warna,
                    'ukuran'    => $v->ukuran,
                ];
            })->values()->toArray();
        }

        return view('admin.stok.index', compact(
            'helms',
            'varians',
            'stok_masuks',
            'helm_variants',
            'totalVariants',
            'kritisCount',
            'habisCount',
            'totalKritisHabis',
            'amanCount',
            'tab',
            'filter',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_varian'  => 'required|exists:varian_helm,id_varian',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $varian = VarianHelm::findOrFail($request->id_varian);
            $helm = Helm::findOrFail($varian->id_helm);

            // Tambah stok varian
            $varian->increment('stok', $request->jumlah);

            // Sinkronkan stok helm induk dengan jumlah total seluruh varian
            $totalStokHelm = VarianHelm::where('id_helm', $helm->id_helm)->sum('stok');
            $helm->update(['stok' => $totalStokHelm]);

            // Catat log riwayat stok masuk
            $user = Auth::guard('web')->user();

            StokMasuk::create([
                'id_helm'    => $helm->id_helm,
                'warna'      => $varian->warna,
                'ukuran'     => $varian->ukuran,
                'id_user'    => $user->id_user,
                'jumlah'     => $request->jumlah,
                'harga_awal' => $helm->harga_awal,
                'harga_jual' => $helm->harga_jual,
                'keterangan' => $request->keterangan ?? 'Penambahan stok fisik gerai',
            ]);

            ActivityLog::log('add_stok', "Restok +{$request->jumlah} unit {$helm->nama_helm} ({$varian->warna}, {$varian->ukuran}) oleh {$user->nama_lengkap}", 'user', $user->id_user, $user->username);

            DB::commit();

            return redirect()->route('admin.stok.index', ['tab' => 'stok'])
                ->with('success', "Berhasil menambahkan {$request->jumlah} unit stok untuk {$helm->nama_helm} ({$varian->warna}, {$varian->ukuran}).");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.stok.index')
                ->with('error', 'Gagal menambahkan stok: ' . $e->getMessage());
        }
    }
}
