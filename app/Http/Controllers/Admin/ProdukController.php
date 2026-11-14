<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Helm;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\VarianHelm;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index()
    {
        $helms = Helm::with(['merk', 'kategori'])->orderBy('id_helm', 'desc')->get();
        return view('admin.produk.index', compact('helms'));
    }

    public function create()
    {
        $merks = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $warnas = Warna::orderBy('nama_warna')->get();
        return view('admin.produk.create', compact('merks', 'kategoris', 'warnas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_helm' => 'required|string|max:100',
            'id_merk' => 'required|exists:merk,id_merk',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga_awal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $gambar = null;
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $gambar = time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $gambar);
            }

            $warna_summary = implode(', ', $request->warna ?? []);
            $ukuran_summary = implode(', ', $request->ukuran ?? []);

            $helm = Helm::create([
                'id_merk' => $request->id_merk,
                'id_kategori' => $request->id_kategori,
                'nama_helm' => $request->nama_helm,
                'warna' => $warna_summary,
                'ukuran' => $ukuran_summary,
                'harga_awal' => $request->harga_awal,
                'harga_jual' => $request->harga_jual,
                'stok' => 0,
                'deskripsi' => $request->deskripsi,
                'gambar' => $gambar,
            ]);

            // Simpan Varian & Hitung Stok
            $total_stok = 0;
            if ($request->has('variant_stok')) {
                foreach ($request->variant_stok as $key => $stok) {
                    $parts = explode('|', $key);
                    $v_stok = (int) $stok;
                    if ($v_stok > 0 && count($parts) === 2) {
                        VarianHelm::create([
                            'id_helm' => $helm->id_helm,
                            'warna' => $parts[0],
                            'ukuran' => $parts[1],
                            'stok' => $v_stok,
                        ]);
                        $total_stok += $v_stok;
                    }
                }
            }

            $helm->update(['stok' => $total_stok]);

            $user = Auth::guard('web')->user();
            ActivityLog::log('create_produk', "Produk baru ditambahkan: {$helm->nama_helm}", 'user', $user->id_user, $user->username);

            DB::commit();

            return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Helm $produk)
    {
        $produk->load('varians');
        $merks = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $warnas = Warna::orderBy('nama_warna')->get();

        $variant_data = [];
        foreach ($produk->varians as $v) {
            $variant_data[$v->warna . '|' . $v->ukuran] = $v->stok;
        }

        return view('admin.produk.edit', compact('produk', 'merks', 'kategoris', 'warnas', 'variant_data'));
    }

    public function update(Request $request, Helm $produk)
    {
        $request->validate([
            'nama_helm' => 'required|string|max:100',
            'id_merk' => 'required|exists:merk,id_merk',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga_awal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $gambar = $produk->gambar;
            if ($request->hasFile('gambar')) {
                if ($gambar && file_exists(public_path('uploads/' . $gambar))) {
                    unlink(public_path('uploads/' . $gambar));
                }
                $file = $request->file('gambar');
                $gambar = time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $gambar);
            }

            $warna_summary = implode(', ', $request->warna ?? []);
            $ukuran_summary = implode(', ', $request->ukuran ?? []);

            $produk->update([
                'id_merk' => $request->id_merk,
                'id_kategori' => $request->id_kategori,
                'nama_helm' => $request->nama_helm,
                'warna' => $warna_summary,
                'ukuran' => $ukuran_summary,
                'harga_awal' => $request->harga_awal,
                'harga_jual' => $request->harga_jual,
                'deskripsi' => $request->deskripsi,
                'gambar' => $gambar,
            ]);

            // Re-create varians
            VarianHelm::where('id_helm', $produk->id_helm)->delete();
            $total_stok = 0;
            if ($request->has('variant_stok')) {
                foreach ($request->variant_stok as $key => $stok) {
                    $parts = explode('|', $key);
                    $v_stok = (int) $stok;
                    if ($v_stok > 0 && count($parts) === 2) {
                        VarianHelm::create([
                            'id_helm' => $produk->id_helm,
                            'warna' => $parts[0],
                            'ukuran' => $parts[1],
                            'stok' => $v_stok,
                        ]);
                        $total_stok += $v_stok;
                    }
                }
            }

            $produk->update(['stok' => $total_stok]);

            $user = Auth::guard('web')->user();
            ActivityLog::log('update_produk', "Produk diperbarui: {$produk->nama_helm}", 'user', $user->id_user, $user->username);

            DB::commit();

            return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Helm $produk)
    {
        try {
            if ($produk->gambar && file_exists(public_path('uploads/' . $produk->gambar))) {
                unlink(public_path('uploads/' . $produk->gambar));
            }

            $nama = $produk->nama_helm;
            $produk->varians()->delete();
            $produk->delete();

            $user = Auth::guard('web')->user();
            ActivityLog::log('delete_produk', "Produk dihapus: {$nama}", 'user', $user->id_user, $user->username);

            return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk. Mungkin masih terhubung dengan pesanan.');
        }
    }
}
