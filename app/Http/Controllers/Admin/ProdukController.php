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
    public function index(Request $request)
    {
        $search = $request->filled('search') ? trim($request->search) : null;
        $idMerk = $request->filled('merk') && $request->merk !== 'all' ? $request->merk : null;
        $idKategori = $request->filled('kategori') && $request->kategori !== 'all' ? $request->kategori : null;
        $stokFilter = $request->get('stok', 'all'); // 'all', 'tersedia', 'habis'

        // Statistik Keseluruhan
        $totalProduk = Helm::count();
        $totalStokFisik = VarianHelm::sum('stok');
        $produkHabis = Helm::where('stok', '<=', 0)->count();
        $totalMerk = Merk::count();

        // Query Master Produk
        $query = Helm::with(['merk', 'kategori'])->withCount('varians');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_helm', 'like', "%{$search}%")
                  ->orWhereHas('merk', function ($qm) use ($search) {
                      $qm->where('nama_merk', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategori', function ($qk) use ($search) {
                      $qk->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        if ($idMerk) {
            $query->where('id_merk', $idMerk);
        }

        if ($idKategori) {
            $query->where('id_kategori', $idKategori);
        }

        if ($stokFilter === 'tersedia') {
            $query->where('stok', '>', 0);
        } elseif ($stokFilter === 'habis') {
            $query->where('stok', '<=', 0);
        }

        $produks = $query->orderBy('id_helm', 'desc')->paginate(15)->withQueryString();

        $merks = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('admin.produk.index', compact(
            'produks',
            'merks',
            'kategoris',
            'totalProduk',
            'totalStokFisik',
            'produkHabis',
            'totalMerk',
            'search',
            'idMerk',
            'idKategori',
            'stokFilter'
        ));
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
            'nama_helm'   => 'required|string|max:100',
            'id_merk'     => 'required|exists:merk,id_merk',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga_awal'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deskripsi'   => 'nullable|string',
            'warna'       => 'nullable|array',
            'ukuran'      => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $gambar = null;
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $gambar = time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $gambar);
            }

            $warna_summary = implode(', ', $request->warna ?? []);
            $ukuran_summary = implode(', ', $request->ukuran ?? []);

            $helm = Helm::create([
                'id_merk'     => $request->id_merk,
                'id_kategori' => $request->id_kategori,
                'nama_helm'   => $request->nama_helm,
                'warna'       => $warna_summary,
                'ukuran'      => $ukuran_summary,
                'harga_awal'  => $request->harga_awal,
                'harga_jual'  => $request->harga_jual,
                'stok'        => 0,
                'deskripsi'   => $request->deskripsi,
                'gambar'      => $gambar,
            ]);

            // Simpan Varian & Hitung Total Stok
            $total_stok = 0;
            if ($request->has('variant_stok') && is_array($request->variant_stok)) {
                foreach ($request->variant_stok as $key => $stok) {
                    $parts = explode('|', $key);
                    $v_stok = max(0, (int) $stok);
                    if (count($parts) === 2) {
                        $warnaName = trim($parts[0]);
                        $ukuranName = trim($parts[1]);

                        VarianHelm::create([
                            'id_helm' => $helm->id_helm,
                            'warna'   => $warnaName,
                            'ukuran'  => $ukuranName,
                            'stok'    => $v_stok,
                        ]);
                        $total_stok += $v_stok;
                    }
                }
            }

            $helm->update(['stok' => $total_stok]);

            $user = Auth::guard('web')->user();
            ActivityLog::log('create_produk', "Produk baru ditambahkan: {$helm->nama_helm}", 'user', $user->id_user, $user->username);

            DB::commit();

            return redirect()->route('admin.produk.index')->with('success', "Produk {$helm->nama_helm} berhasil ditambahkan!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Helm $produk)
    {
        $produk->load(['varians', 'merk', 'kategori']);
        $merks = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $warnas = Warna::orderBy('nama_warna')->get();

        // Dapatkan nama warna & ukuran yang aktif pada varian
        $existing_warnas = $produk->varians->pluck('warna')->unique()->values()->toArray();
        $existing_ukurans = $produk->varians->pluck('ukuran')->unique()->values()->toArray();

        if (empty($existing_warnas) && $produk->warna) {
            $existing_warnas = array_filter(array_map('trim', explode(',', $produk->warna)));
        }
        if (empty($existing_ukurans) && $produk->ukuran) {
            $existing_ukurans = array_filter(array_map('trim', explode(',', $produk->ukuran)));
        }

        $variant_data = [];
        foreach ($produk->varians as $v) {
            $variant_data[$v->warna . '|' . $v->ukuran] = $v->stok;
        }

        return view('admin.produk.edit', compact(
            'produk',
            'merks',
            'kategoris',
            'warnas',
            'existing_warnas',
            'existing_ukurans',
            'variant_data'
        ));
    }

    public function update(Request $request, Helm $produk)
    {
        $request->validate([
            'nama_helm'   => 'required|string|max:100',
            'id_merk'     => 'required|exists:merk,id_merk',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga_awal'  => 'required|numeric|min:0',
            'harga_jual'  => 'required|numeric|min:0',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deskripsi'   => 'nullable|string',
            'warna'       => 'nullable|array',
            'ukuran'      => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $gambar = $produk->gambar;
            if ($request->hasFile('gambar')) {
                if ($gambar && file_exists(public_path('uploads/' . $gambar))) {
                    @unlink(public_path('uploads/' . $gambar));
                }
                $file = $request->file('gambar');
                $gambar = time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $gambar);
            }

            $warna_summary = implode(', ', $request->warna ?? []);
            $ukuran_summary = implode(', ', $request->ukuran ?? []);

            $produk->update([
                'id_merk'     => $request->id_merk,
                'id_kategori' => $request->id_kategori,
                'nama_helm'   => $request->nama_helm,
                'warna'       => $warna_summary,
                'ukuran'      => $ukuran_summary,
                'harga_awal'  => $request->harga_awal,
                'harga_jual'  => $request->harga_jual,
                'deskripsi'   => $request->deskripsi,
                'gambar'      => $gambar,
            ]);

            // Sinkronisasi varian tanpa memecah relasi cart/pesanan yang ada
            $keptVariantIds = [];
            $total_stok = 0;

            if ($request->has('variant_stok') && is_array($request->variant_stok)) {
                foreach ($request->variant_stok as $key => $stok) {
                    $parts = explode('|', $key);
                    if (count($parts) === 2) {
                        $warnaName = trim($parts[0]);
                        $ukuranName = trim($parts[1]);
                        $v_stok = max(0, (int) $stok);

                        $varian = VarianHelm::updateOrCreate(
                            [
                                'id_helm' => $produk->id_helm,
                                'warna'   => $warnaName,
                                'ukuran'  => $ukuranName,
                            ],
                            [
                                'stok'    => $v_stok,
                            ]
                        );

                        $keptVariantIds[] = $varian->id_varian;
                        $total_stok += $v_stok;
                    }
                }
            }

            // Hapus varian usang yang tidak lagi dipilih (jika tidak terikat order/cart)
            $obsoleteVariants = VarianHelm::where('id_helm', $produk->id_helm)
                ->whereNotIn('id_varian', $keptVariantIds)
                ->get();

            foreach ($obsoleteVariants as $obs) {
                $inCart = DB::table('cart')->where('id_varian', $obs->id_varian)->exists();
                $inPesanan = DB::table('detail_pesanan')->where('id_varian', $obs->id_varian)->exists();

                if (!$inCart && !$inPesanan) {
                    $obs->delete();
                } else {
                    $obs->update(['stok' => 0]);
                }
            }

            $produk->update(['stok' => $total_stok]);

            $user = Auth::guard('web')->user();
            ActivityLog::log('update_produk', "Produk diperbarui: {$produk->nama_helm}", 'user', $user->id_user, $user->username);

            DB::commit();

            return redirect()->route('admin.produk.index')->with('success', "Produk {$produk->nama_helm} berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Helm $produk)
    {
        try {
            $hasOrders = DB::table('detail_pesanan')->where('id_helm', $produk->id_helm)->exists();
            if ($hasOrders) {
                return back()->with('error', "Produk {$produk->nama_helm} tidak dapat dihapus karena sudah memiliki data transaksi pesanan.");
            }

            DB::table('cart')->where('id_helm', $produk->id_helm)->delete();

            if ($produk->gambar && file_exists(public_path('uploads/' . $produk->gambar))) {
                @unlink(public_path('uploads/' . $produk->gambar));
            }

            $nama = $produk->nama_helm;
            $produk->varians()->delete();
            $produk->delete();

            $user = Auth::guard('web')->user();
            ActivityLog::log('delete_produk', "Produk dihapus: {$nama}", 'user', $user->id_user, $user->username);

            return redirect()->route('admin.produk.index')->with('success', "Produk {$nama} berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
