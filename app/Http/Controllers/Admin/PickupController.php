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
        $pesanan_ready = Pesanan::with(['pelanggan', 'details.helm'])
            ->whereIn('status', ['ready', 'unpaid_pickup', 'paid', 'processing'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pickup.index', compact('pesanan_ready'));
    }

    public function validasi(Request $request)
    {
        $request->validate([
            'kode_pickup' => 'required|string|max:20',
        ]);

        $pesanan = Pesanan::with(['pelanggan', 'details.helm'])
            ->where('kode_pickup', strtoupper($request->kode_pickup))
            ->whereIn('status', ['ready', 'unpaid_pickup', 'paid', 'processing'])
            ->first();

        if (!$pesanan) {
            return back()->with('error', 'Kode pickup tidak ditemukan atau pesanan tidak dalam status siap ambil.');
        }

        return view('admin.pickup.validasi', compact('pesanan'));
    }

    public function serahkan(Request $request, Pesanan $pesanan)
    {
        if (!in_array($pesanan->status, ['ready', 'unpaid_pickup', 'paid', 'processing'])) {
            return redirect()->route('admin.pickup.index')->with('error', 'Pesanan tidak valid untuk diserahkan.');
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

        return redirect()->route('admin.pickup.index')->with('success', "Pesanan {$pesanan->no_pesanan} berhasil diserahkan ke pelanggan!");
    }
}
