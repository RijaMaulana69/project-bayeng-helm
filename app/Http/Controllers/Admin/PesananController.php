<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with('pelanggan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhere('kode_pickup', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        $pesanans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pesanan.index', compact('pesanans'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['pelanggan', 'details', 'logs']);
        return view('admin.pesanan.detail', compact('pesanan'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => 'required|in:paid,processing,ready,completed,cancelled',
        ]);

        $statusLama = $pesanan->status;
        $statusBaru = $request->status;

        // Validasi alur status
        $allowed = [
            'pending' => ['paid', 'cancelled'],
            'unpaid_pickup' => ['processing', 'ready', 'cancelled'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready' => ['completed', 'cancelled'],
        ];

        if (!isset($allowed[$statusLama]) || !in_array($statusBaru, $allowed[$statusLama])) {
            return back()->with('error', "Tidak bisa mengubah status dari '{$statusLama}' ke '{$statusBaru}'.");
        }

        // Jika dibatalkan, kembalikan stok
        if ($statusBaru === 'cancelled') {
            foreach ($pesanan->details as $detail) {
                if ($detail->id_varian) {
                    \App\Models\VarianHelm::where('id_varian', $detail->id_varian)->increment('stok', $detail->qty);
                }
                if ($detail->id_helm) {
                    \App\Models\Helm::where('id_helm', $detail->id_helm)->increment('stok', $detail->qty);
                }
            }
        }

        $pesanan->update(['status' => $statusBaru]);

        $user = Auth::guard('web')->user();
        $pesanan->logs()->create([
            'status_dari' => $statusLama,
            'status_ke' => $statusBaru,
            'catatan' => $request->catatan ?? "Status diubah oleh {$user->nama_lengkap}.",
            'diubah_oleh' => $user->username,
        ]);

        ActivityLog::log('update_status_pesanan', "Status pesanan {$pesanan->no_pesanan} diubah: {$statusLama} → {$statusBaru}", 'user', $user->id_user, $user->username);

        return back()->with('success', "Status pesanan berhasil diubah ke '{$statusBaru}'.");
    }
}
