<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        
        $query = Pesanan::with(['details.helm'])
            ->where('id_pelanggan', $pelanggan->id_pelanggan);

        // Status filter tabs
        $activeTab = $request->query('status', 'semua');

        match ($activeTab) {
            'menunggu_bayar' => $query->where('status', 'pending'),
            'diproses'       => $query->whereIn('status', ['unpaid_pickup', 'paid', 'processing']),
            'siap_diambil'   => $query->where('status', 'ready'),
            'selesai'        => $query->where('status', 'completed'),
            'dibatalkan'     => $query->where('status', 'cancelled'),
            default          => null,
        };

        $pesanans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Calculate count for each status tab
        $counts = [
            'semua'          => Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)->count(),
            'menunggu_bayar' => Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)->where('status', 'pending')->count(),
            'diproses'       => Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)->whereIn('status', ['unpaid_pickup', 'paid', 'processing'])->count(),
            'siap_diambil'   => Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)->where('status', 'ready')->count(),
            'selesai'        => Pesanan::where('id_pelanggan', $pelanggan->id_pelanggan)->where('status', 'completed')->count(),
        ];

        return view('pelanggan.pesanan.index', compact('pesanans', 'activeTab', 'counts'));
    }

    public function show(Pesanan $pesanan)
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        // IDOR Protection: pastikan pesanan milik pelanggan yang login
        if ($pesanan->id_pelanggan !== $pelanggan->id_pelanggan) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $pesanan->load(['details.helm', 'logs']);

        return view('pelanggan.pesanan.detail', compact('pesanan'));
    }

    public function uploadBukti(Request $request, Pesanan $pesanan)
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        if ($pesanan->id_pelanggan !== $pelanggan->id_pelanggan) {
            abort(403);
        }

        if ($pesanan->status !== 'pending') {
            return back()->with('error', 'Upload bukti hanya untuk pesanan yang menunggu pembayaran.');
        }

        $request->validate([
            'bukti_bayar' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ], [
            'bukti_bayar.mimes' => 'Format file harus JPG, JPEG, atau PNG.',
            'bukti_bayar.max'   => 'Ukuran file maksimal 2MB.',
        ]);

        // Verify MIME type via finfo (security: anti-bypass extension)
        $file = $request->file('bukti_bayar');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getPathname());
        $allowedMimes = ['image/jpeg', 'image/png'];

        if (!in_array($realMime, $allowedMimes)) {
            return back()->with('error', 'Jenis file tidak diizinkan! Hanya gambar JPG/PNG yang valid.');
        }

        $uploadDir = public_path('uploads/bukti');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old file
        if ($pesanan->bukti_bayar && file_exists($uploadDir . '/' . $pesanan->bukti_bayar)) {
            unlink($uploadDir . '/' . $pesanan->bukti_bayar);
        }

        $filename = 'BUKTI_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);

        $pesanan->update([
            'bukti_bayar' => $filename,
            'status'      => 'paid',
        ]);

        $pesanan->logs()->create([
            'status_dari' => 'pending',
            'status_ke'   => 'paid',
            'catatan'     => 'Bukti pembayaran diunggah oleh pelanggan.',
            'diubah_oleh' => 'pelanggan',
        ]);

        ActivityLog::log('upload_bukti', "Bukti bayar diunggah untuk pesanan {$pesanan->no_pesanan}", 'pelanggan', $pelanggan->id_pelanggan, $pelanggan->nama);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Pesanan akan segera disiapkan oleh kasir.');
    }
}
