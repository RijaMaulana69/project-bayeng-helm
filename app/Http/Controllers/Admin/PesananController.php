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

        $search = $request->filled('search') ? trim($request->search) : null;
        $isSpecificCode = $search && (
            str_starts_with(strtoupper($search), 'PKP-') ||
            str_starts_with(strtoupper($search), 'ORD-')
        );

        // Filter status pesanan (diabaikan jika mencari kode unik spesifik seperti PKP- atau ORD- agar kasir cepat menemukan pesanan)
        if ($request->filled('status') && $request->status !== 'all' && !$isSpecificCode) {
            if ($request->status === 'ready') {
                $query->whereIn('status', ['ready', 'unpaid_pickup']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter metode pembayaran
        if ($request->filled('metode_bayar') && $request->metode_bayar !== 'all') {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        // Pencarian multi-kolom
        if ($search) {
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
            'ready' => ($rawCounts['ready'] ?? 0) + ($rawCounts['unpaid_pickup'] ?? 0),
            'completed' => $rawCounts['completed'] ?? 0,
            'cancelled' => $rawCounts['cancelled'] ?? 0,
        ];

        // Rekap Pendapatan untuk Kasir / Karyawan (Hari ini & Bulan ini)
        $todayStats = Pesanan::where('status', 'completed')
            ->whereDate('created_at', today())
            ->selectRaw("
                COUNT(*) as total_count,
                COALESCE(SUM(grand_total), 0) as total_omzet,
                COALESCE(SUM(CASE WHEN metode_bayar = 'tunai' THEN grand_total ELSE 0 END), 0) as tunai_omzet,
                COUNT(CASE WHEN metode_bayar = 'tunai' THEN 1 ELSE NULL END) as tunai_count,
                COALESCE(SUM(CASE WHEN metode_bayar IN ('transfer', 'qris') THEN grand_total ELSE 0 END), 0) as online_omzet,
                COUNT(CASE WHEN metode_bayar IN ('transfer', 'qris') THEN 1 ELSE NULL END) as online_count
            ")->first();

        $monthStats = Pesanan::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw("
                COUNT(*) as total_count,
                COALESCE(SUM(grand_total), 0) as total_omzet,
                COALESCE(SUM(CASE WHEN metode_bayar = 'tunai' THEN grand_total ELSE 0 END), 0) as tunai_omzet,
                COUNT(CASE WHEN metode_bayar = 'tunai' THEN 1 ELSE NULL END) as tunai_count,
                COALESCE(SUM(CASE WHEN metode_bayar IN ('transfer', 'qris') THEN grand_total ELSE 0 END), 0) as online_omzet,
                COUNT(CASE WHEN metode_bayar IN ('transfer', 'qris') THEN 1 ELSE NULL END) as online_count
            ")->first();

        $rekapPendapatan = [
            'hari_ini' => [
                'total' => (float) ($todayStats->total_omzet ?? 0),
                'count' => (int) ($todayStats->total_count ?? 0),
                'tunai' => (float) ($todayStats->tunai_omzet ?? 0),
                'tunai_count' => (int) ($todayStats->tunai_count ?? 0),
                'online' => (float) ($todayStats->online_omzet ?? 0),
                'online_count' => (int) ($todayStats->online_count ?? 0),
            ],
            'bulan_ini' => [
                'total' => (float) ($monthStats->total_omzet ?? 0),
                'count' => (int) ($monthStats->total_count ?? 0),
                'tunai' => (float) ($monthStats->tunai_omzet ?? 0),
                'tunai_count' => (int) ($monthStats->tunai_count ?? 0),
                'online' => (float) ($monthStats->online_omzet ?? 0),
                'online_count' => (int) ($monthStats->online_count ?? 0),
            ],
        ];

        return view('admin.pesanan.index', compact('pesanans', 'statusCounts', 'rekapPendapatan'));
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
