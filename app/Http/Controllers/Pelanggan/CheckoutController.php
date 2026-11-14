<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Cart;
use App\Models\DetailPesanan;
use App\Models\LogStatusPesanan;
use App\Models\Pesanan;
use App\Models\VarianHelm;
use App\Models\Helm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $carts = Cart::with(['helm', 'varian'])
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('selected', 1)
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('pelanggan.cart')->with('error', 'Pilih minimal 1 helm untuk checkout.');
        }

        $total = $carts->sum(function ($item) {
            return ($item->helm->harga_jual ?? 0) * $item->qty;
        });

        return view('pelanggan.checkout.index', compact('carts', 'total', 'pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penerima' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'metode_bayar' => 'required|in:transfer,qris,bayar_di_toko',
        ]);

        $pelanggan = Auth::guard('pelanggan')->user();
        $carts = Cart::with(['helm', 'varian'])
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('selected', 1)
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('pelanggan.cart')->with('error', 'Pilih minimal 1 helm untuk checkout.');
        }

        try {
            DB::beginTransaction();

            $total_belanja = 0;
            foreach ($carts as $item) {
                $total_belanja += ($item->helm->harga_jual ?? 0) * $item->qty;
            }

            $isBayarDiToko = $request->metode_bayar === 'bayar_di_toko';
            $status = $isBayarDiToko ? 'unpaid_pickup' : 'pending';
            $metode_bayar_db = $isBayarDiToko ? 'tunai' : ($request->metode_bayar === 'qris' ? 'qris' : 'transfer');

            $pesanan = Pesanan::create([
                'no_pesanan' => generate_order_number(),
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'penerima' => $request->penerima,
                'telepon' => $request->telepon,
                'batas_ambil' => now()->addDays(3)->toDateString(),
                'kode_pickup' => generate_pickup_code(),
                'total_belanja' => $total_belanja,
                'biaya_admin' => 0,
                'grand_total' => $total_belanja,
                'metode_bayar' => $metode_bayar_db,
                'metode_bayar_detail' => $request->metode_bayar,
                'status' => $status,
                'catatan' => $request->catatan,
            ]);

            // Insert detail pesanan & kurangi stok
            foreach ($carts as $item) {
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_helm' => $item->id_helm,
                    'id_varian' => $item->id_varian,
                    'nama_helm' => $item->helm->nama_helm,
                    'warna' => $item->varian->warna ?? '-',
                    'ukuran' => $item->varian->ukuran ?? '-',
                    'harga' => $item->helm->harga_jual,
                    'qty' => $item->qty,
                    'subtotal' => $item->helm->harga_jual * $item->qty,
                ]);

                // Kurangi stok varian
                if ($item->id_varian) {
                    VarianHelm::where('id_varian', $item->id_varian)->decrement('stok', $item->qty);
                }
                // Kurangi stok helm
                Helm::where('id_helm', $item->id_helm)->decrement('stok', $item->qty);
            }

            // Log status pesanan
            LogStatusPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_dari' => '-',
                'status_ke' => $status,
                'catatan' => 'Pesanan baru dibuat oleh pelanggan.',
                'diubah_oleh' => 'pelanggan',
            ]);

            // Kosongkan barang yang sudah dicheckout dari keranjang
            Cart::where('id_pelanggan', $pelanggan->id_pelanggan)->where('selected', 1)->delete();

            ActivityLog::log('checkout', "Pesanan baru {$pesanan->no_pesanan} oleh {$pelanggan->nama} (total: " . format_rupiah($pesanan->grand_total) . ")", 'pelanggan', $pelanggan->id_pelanggan, $pelanggan->nama);

            DB::commit();

            return redirect()->route('pelanggan.checkout.sukses', $pesanan->id_pesanan)
                ->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function sukses(Pesanan $pesanan)
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        if ($pesanan->id_pelanggan !== $pelanggan->id_pelanggan) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $pesanan->load(['details.helm']);

        return view('pelanggan.checkout.sukses', compact('pesanan'));
    }
}
