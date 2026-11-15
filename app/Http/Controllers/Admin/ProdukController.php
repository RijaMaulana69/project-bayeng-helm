<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Helm;
use App\Models\Kategori;
use App\Models\Merk;
use App\Models\StokMasuk;
use App\Models\VarianHelm;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $rawTab = $request->get('tab', 'produk');
        // Hanya ada 2 tab operasional: 'produk' (default gabungan katalog & stok) dan 'riwayat'
        $tab = ($rawTab === 'riwayat') ? 'riwayat' : 'produk';
        $search = $request->filled('search') ? trim($request->search) : null;

        // Metrik Utama Persediaan & Katalog
        $totalProduk      = Helm::count();
        $totalVarian      = VarianHelm::count();
        $totalStokFisik   = VarianHelm::sum('stok');
        $habisCount       = VarianHelm::where('stok', '<=', 0)->count();
        $kritisCount      = VarianHelm::where('stok', '>', 0)->where('stok', '<=', 3)->count();
        $totalKritisHabis = $kritisCount + $habisCount;
        $amanCount        = VarianHelm::where('stok', '>', 3)->count();
        $produkHabis      = Helm::where('stok', '<=', 0)->count();
        $totalMerk        = Merk::count();

        // Data Merk & Kategori untuk filter & form
        $merks     = Merk::orderBy('nama_merk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $helms     = Helm::with('varians')->orderBy('nama_helm', 'asc')->get();

        // Format data varian untuk modal restok instan
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

        // ==========================================
        // TAB 1: PRODUK & INVENTORI (KATALOG + RAK FISIK)
        // ==========================================
        $idMerk = $request->filled('merk') && $request->merk !== 'all' ? $request->merk : null;
        $idKategori = $request->filled('kategori') && $request->kategori !== 'all' ? $request->kategori : null;

        // Dukung backward compatibility bila ada redirect dari link stok lama
        $stokFilter = $request->get('stok_filter', $request->get('filter', 'all'));
        if ($rawTab === 'stok' && $stokFilter === 'all') {
            $stokFilter = 'kritis_habis';
        }

        $query = Helm::with(['merk', 'kategori', 'varians'])->withCount('varians');

        if ($search && $tab === 'produk') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_helm', 'like', "%{$search}%")
                  ->orWhereHas('merk', function ($qm) use ($search) {
                      $qm->where('nama_merk', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategori', function ($qk) use ($search) {
                      $qk->where('nama_kategori', 'like', "%{$search}%");
                  })
                  ->orWhereHas('varians', function ($qv) use ($search) {
                      $qv->where('warna', 'like', "%{$search}%")
                         ->orWhere('ukuran', 'like', "%{$search}%");
                  });
            });
        }

        if ($idMerk) $query->where('id_merk', $idMerk);
        if ($idKategori) $query->where('id_kategori', $idKategori);

        if ($stokFilter === 'tersedia') {
            $query->where('stok', '>', 0);
        } elseif ($stokFilter === 'habis') {
            $query->where(function ($q) {
                $q->where('stok', '<=', 0)
                  ->orWhereHas('varians', fn($qv) => $qv->where('stok', '<=', 0));
            });
        } elseif ($stokFilter === 'kritis_habis') {
            $query->where(function ($q) {
                $q->where('stok', '<=', 3)
                  ->orWhereHas('varians', fn($qv) => $qv->where('stok', '<=', 3));
            });
        } elseif ($stokFilter === 'aman') {
            $query->where('stok', '>', 3)
                  ->whereDoesntHave('varians', fn($qv) => $qv->where('stok', '<=', 3));
        }

        $produks = $query->orderBy('id_helm', 'desc')->paginate(15)->withQueryString();

        // ==========================================
        // TAB 2: RIWAYAT BARANG MASUK
        // ==========================================
        $stok_masuks = StokMasuk::with(['helm.merk', 'user'])
            ->orderBy('id_stok_masuk', 'desc')
            ->paginate(15, ['*'], 'riwayat_page')
            ->withQueryString();

        return view('admin.produk.index', compact(
            'tab',
            'search',
            'totalProduk',
            'totalVarian',
            'totalStokFisik',
            'totalKritisHabis',
            'kritisCount',
            'habisCount',
            'amanCount',
            'produkHabis',
            'totalMerk',
            'merks',
            'kategoris',
            'helms',
            'helm_variants',
            'produks',
            'idMerk',
            'idKategori',
            'stokFilter',
            'stok_masuks'
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

            return redirect()->route('admin.produk.index')
                ->with('success', "Produk {$helm->nama_helm} berhasil ditambahkan!");
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

            return redirect()->route('admin.produk.index')
                ->with('success', "Produk {$produk->nama_helm} berhasil diperbarui!");
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

            return redirect()->route('admin.produk.index')
                ->with('success', "Produk {$nama} berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
