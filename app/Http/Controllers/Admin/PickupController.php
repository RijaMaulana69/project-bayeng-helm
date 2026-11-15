<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PickupController extends Controller
{
    public function index()
    {
        // Alihkan antrean kasir pickup ke menu terpadu Transaksi (Tab Siap Ambil)
        return redirect()->route('admin.pesanan.index', ['status' => 'ready']);
    }

    public function validasi(Request $request)
    {
        $kodePickup = $request->input('kode_pickup') ?? $request->query('kode');

        if (!$kodePickup) {
            return redirect()->route('admin.pickup.index');
        }

        $kodeClean = strtoupper(trim($kodePickup));

        $pesanan = Pesanan::with(['pelanggan', 'details.helm'])
            ->where('kode_pickup', $kodeClean)
            ->first();

        if (!$pesanan) {
            return redirect()->route('admin.pickup.index')->with('error', 'Kode pickup tidak ditemukan.');
        }

        if (!in_array($pesanan->status, ['ready', 'unpaid_pickup', 'completed'])) {
            if ($pesanan->status === 'cancelled') {
                return redirect()->route('admin.pickup.index')->with('error', "Pesanan {$pesanan->no_pesanan} telah dibatalkan.");
            }

            if ($pesanan->status === 'pending') {
                return redirect()->route('admin.pickup.index')->with('error', "Pesanan {$pesanan->no_pesanan} masih menunggu verifikasi pembayaran di menu Pesanan.");
            }

            if (in_array($pesanan->status, ['paid', 'processing'])) {
                return redirect()->route('admin.pickup.index')->with('error', "Pesanan {$pesanan->no_pesanan} masih disiapkan di gudang. Silakan tandai siap ambil terlebih dahulu di menu Pesanan.");
            }

            return redirect()->route('admin.pickup.index')->with('error', 'Pesanan tidak dalam status siap ambil.');
        }

        return view('admin.pickup.validasi', compact('pesanan'));
    }

    public function serahkan(Request $request, Pesanan $pesanan)
    {
        if (!in_array($pesanan->status, ['ready', 'unpaid_pickup'])) {
            return redirect()->route('admin.pickup.index')->with('error', 'Pesanan belum dalam status siap diserahkan.');
        }

        $statusLama = $pesanan->status;

        $pesanan->update(['status' => 'completed']);

        $pesanan->logs()->create([
            'status_dari' => $statusLama,
            'status_ke' => 'completed',
            'catatan' => 'Barang diserahkan ke pelanggan di toko. Kasir: ' . Auth::guard('web')->user()->nama_lengkap,
            'diubah_oleh' => Auth::guard('web')->user()->username,
        ]);

        $user = Auth::guard('web')->user();
        ActivityLog::log('pickup_completed', "Serah terima pesanan {$pesanan->no_pesanan} oleh kasir {$user->nama_lengkap}", 'user', $user->id_user, $user->username);

        $namaPelanggan = $pesanan->pelanggan->nama ?? ($pesanan->penerima ?? 'Pelanggan');
        return redirect()->route('admin.pickup.index')->with('success', "Pesanan berhasil diserahkan ke {$namaPelanggan}.");
    }
}
