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
    public function index()
    {
        $helms = Helm::with('varians')->orderBy('nama_helm', 'asc')->get();

        $stok_masuks = StokMasuk::with(['helm', 'user'])
            ->orderBy('id_stok_masuk', 'desc')
            ->paginate(15);

        $helm_variants = [];
        foreach ($helms as $h) {
            $helm_variants[$h->id_helm] = $h->varians->map(function ($v) {
                return [
                    'id_varian' => $v->id_varian,
                    'label' => $v->warna . ' - ' . $v->ukuran . ' (Stok: ' . $v->stok . ')',
                ];
            })->toArray();
        }

        return view('admin.stok.index', compact('helms', 'stok_masuks', 'helm_variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_varian' => 'required|exists:varian_helm,id_varian',
            'jumlah'    => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $varian = VarianHelm::findOrFail($request->id_varian);
            $helm = Helm::findOrFail($varian->id_helm);

            // Increment stock on varian
            $varian->increment('stok', $request->jumlah);

            // Increment stock on helm
            $helm->increment('stok', $request->jumlah);

            // Insert into stok_masuk
            $user = Auth::guard('web')->user();

            StokMasuk::create([
                'id_helm'    => $helm->id_helm,
                'warna'      => $varian->warna,
                'ukuran'     => $varian->ukuran,
                'id_user'    => $user->id_user,
                'jumlah'     => $request->jumlah,
                'harga_awal' => $helm->harga_awal,
                'harga_jual' => $helm->harga_jual,
                'keterangan' => $request->keterangan ?? 'Penambahan stok manual',
            ]);

            ActivityLog::log('add_stok', 'Menambahkan stok ' . $request->jumlah . ' untuk ' . $helm->nama_helm . ' (' . $varian->warna . ', ' . $varian->ukuran . ')', 'user', $user->id_user, $user->username);

            DB::commit();

            return redirect()->route('admin.stok.index')
                ->with('success', 'Stok berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.stok.index')
                ->with('error', 'Gagal menambahkan stok: ' . $e->getMessage());
        }
    }
}
