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
        return redirect()->route('admin.produk.index', array_merge(['tab' => 'stok'], $request->all()));
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

            // Tambah stok fisik varian
            $varian->increment('stok', $request->jumlah);

            // Sinkronkan stok helm induk dengan penjumlahan seluruh variannya
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

            return redirect()->route('admin.produk.index', ['tab' => 'stok'])
                ->with('success', "Berhasil menambahkan {$request->jumlah} unit stok untuk {$helm->nama_helm} ({$varian->warna}, {$varian->ukuran}).");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.produk.index', ['tab' => 'stok'])
                ->with('error', 'Gagal menambahkan stok: ' . $e->getMessage());
        }
    }
}
