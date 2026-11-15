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
        $query = Pesanan::with(['pelanggan', 'details.helm']);

        // Filter status pesanan
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter metode pembayaran
        if ($request->filled('metode_bayar') && $request->metode_bayar !== 'all') {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        // Pencarian multi-kolom
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhere('kode_pickup', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('telepon', 'like', "%{$search}%");
                  });
            });
        }

        $pesanans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Hitung total status untuk tab filter cepat
        $rawCounts = Pesanan::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $statusCounts = [
            'all' => array_sum($rawCounts),
            'pending' => $rawCounts['pending'] ?? 0,
            'paid' => $rawCounts['paid'] ?? 0,
            'processing' => $rawCounts['processing'] ?? 0,
            'ready' => $rawCounts['ready'] ?? 0,
            'completed' => $rawCounts['completed'] ?? 0,
            'cancelled' => $rawCounts['cancelled'] ?? 0,
        ];

        return view('admin.pesanan.index', compact('pesanans', 'statusCounts'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['pelanggan', 'details.helm', 'logs']);

        $statusLabels = [
            'paid' => 'Konfirmasi Pembayaran (Sudah Bayar)',
            'processing' => 'Proses & Siapkan Barang',
            'ready' => 'Tandai Siap Diambil di Gerai (Ready)',
            'cancelled' => 'Batalkan Pesanan',
        ];

        $allowedTransitions = [
            'pending' => ['paid', 'cancelled'],
            'unpaid_pickup' => ['processing', 'ready', 'cancelled'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready' => ['cancelled'], // Penyelesaian dilakukan di Kasir Pickup!
        ];

        $allowed_statuses = [];
        foreach ($allowedTransitions[$pesanan->status] ?? [] as $nextStatus) {
            $allowed_statuses[$nextStatus] = $statusLabels[$nextStatus] ?? ucfirst($nextStatus);
        }

        return view('admin.pesanan.detail', compact('pesanan', 'allowed_statuses'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => 'required|in:paid,processing,ready,cancelled',
        ]);

        $statusLama = $pesanan->status;
        $statusBaru = $request->status;

        // Validasi alur status meja persiapan
        $allowed = [
            'pending' => ['paid', 'cancelled'],
            'unpaid_pickup' => ['processing', 'ready', 'cancelled'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready' => ['cancelled'], // Selesai wajib divalidasi lewat Kasir Pickup!
        ];

        if ($statusLama === 'ready' && $statusBaru === 'completed') {
            return back()->with('error', 'Penyerahan barang dan penyelesaian pesanan wajib dilakukan melalui menu Kasir Pickup.');
        }

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

        $statusLabelBaru = match($statusBaru) {
            'paid' => 'Sudah Dibayar',
            'processing' => 'Sedang Disiapkan',
            'ready' => 'Siap Diambil di Gerai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($statusBaru),
        };

        return back()->with('success', "Status pesanan berhasil diperbarui ke '{$statusLabelBaru}'.");
    }
}
