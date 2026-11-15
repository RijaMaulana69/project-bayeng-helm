<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'no_pesanan',
        'id_pelanggan',
        'penerima',
        'telepon',
        'alamat_lengkap',
        'batas_ambil',
        'kode_pickup',
        'total_belanja',
        'biaya_admin',
        'grand_total',
        'metode_bayar',
        'metode_bayar_detail',
        'status',
        'bukti_bayar',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'batas_ambil' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'total_belanja' => 'decimal:2',
            'biaya_admin' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function items()
    {
        return $this->details();
    }

    public function logs()
    {
        return $this->hasMany(LogStatusPesanan::class, 'id_pesanan', 'id_pesanan')->orderBy('created_at', 'desc');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'unpaid_pickup' => 'Menunggu Diambil',
            'paid' => 'Sudah Dibayar',
            'processing' => 'Sedang Disiapkan',
            'ready' => 'Siap Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getMetodeBayarLabelAttribute(): string
    {
        return match(strtolower($this->metode_bayar ?? '')) {
            'tunai' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
            'ewallet' => 'E-Wallet',
            default => ucfirst($this->metode_bayar ?? '-'),
        };
    }

    public function getBuktiBayarUrlAttribute(): ?string
    {
        if (! $this->bukti_bayar) {
            return null;
        }

        if (file_exists(public_path('uploads/bukti/' . $this->bukti_bayar))) {
            return asset('uploads/bukti/' . $this->bukti_bayar);
        }

        if (file_exists(public_path('storage/' . $this->bukti_bayar))) {
            return asset('storage/' . $this->bukti_bayar);
        }

        return asset('uploads/bukti/' . $this->bukti_bayar);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Menunggu Bayar</span>',
            'unpaid_pickup' => '<span class="badge bg-info text-dark"><i class="fa fa-clock me-1"></i> Menunggu Diambil</span>',
            'paid' => '<span class="badge bg-primary"><i class="fa fa-check-circle me-1"></i> Terbayar</span>',
            'processing' => '<span class="badge bg-info text-dark"><i class="fa fa-box me-1"></i> Disiapkan</span>',
            'ready' => '<span class="badge bg-success"><i class="fa fa-bell me-1"></i> Siap Diambil</span>',
            'completed' => '<span class="badge bg-secondary"><i class="fa fa-check-double me-1"></i> Selesai Diambil</span>',
            'cancelled' => '<span class="badge bg-danger"><i class="fa fa-times-circle me-1"></i> Dibatalkan</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getTrackingTimelineAttribute(): array
    {
        $events = [];
        $initialLog = $this->logs ? $this->logs->where('status_dari', '-')->first() : null;
        $createdTime = ($initialLog && $initialLog->created_at) ? $initialLog->created_at : $this->created_at;

        // 1. Initial Milestone: Pesanan Dibuat
        $createdDesc = match ($this->metode_bayar) {
            'tunai'    => 'Pesanan berhasil dibuat dengan opsi Bayar di Gerai.',
            'transfer' => 'Pesanan berhasil dibuat. Menunggu konfirmasi pembayaran transfer.',
            'qris'     => 'Pesanan berhasil dibuat. Menunggu scan pembayaran QRIS.',
            default    => 'Pesanan berhasil dibuat oleh pembeli.',
        };

        $events[] = [
            'status'     => 'created',
            'title'      => 'Pesanan Dibuat',
            'desc'       => $createdDesc,
            'time'       => $createdTime ? $createdTime->format('d M Y, H:i') : '-',
            'created_at' => $createdTime,
        ];

        // 2. Process logs from oldest to newest
        $logs = $this->logs ? $this->logs->sortBy('created_at') : collect();

        foreach ($logs as $log) {
            $isCustomNote = $log->catatan 
                && !str_contains($log->catatan, 'Pesanan baru dibuat')
                && !str_starts_with($log->catatan, 'Status diubah oleh')
                && !str_starts_with($log->catatan, 'Barang diserahkan ke pelanggan')
                && !str_contains($log->catatan, 'Bukti pembayaran diunggah');

            $eventData = match ($log->status_ke) {
                'unpaid_pickup' => [
                    'status' => 'unpaid_pickup',
                    'title'  => 'Menunggu Pengambilan di Gerai',
                    'desc'   => $isCustomNote ? $log->catatan : "Pesanan siap diambil. Tunjukkan Kode Pickup {$this->kode_pickup} ke kasir saat pengambilan barang.",
                ],
                'pending' => [
                    'status' => 'pending',
                    'title'  => 'Menunggu Pembayaran',
                    'desc'   => $isCustomNote ? $log->catatan : 'Selesaikan pembayaran ' . format_rupiah($this->grand_total) . ' dan unggah bukti transfer.',
                ],
                'paid' => [
                    'status' => 'paid',
                    'title'  => 'Pembayaran Diverifikasi',
                    'desc'   => $isCustomNote ? $log->catatan : 'Bukti pembayaran telah diterima dan diverifikasi oleh kasir gerai.',
                ],
                'processing' => [
                    'status' => 'processing',
                    'title'  => 'Pesanan Sedang Disiapkan',
                    'desc'   => $isCustomNote ? $log->catatan : 'Staf toko sedang menyiapkan dan memeriksa kondisi helm pesanan.',
                ],
                'ready' => [
                    'status' => 'ready',
                    'title'  => 'Siap Diambil di Gerai',
                    'desc'   => $isCustomNote ? $log->catatan : "Helm sudah siap di Gerai Bayeng Putra Helm Kuningan. Tunjukkan Kode Pickup {$this->kode_pickup} ke kasir.",
                ],
                'completed' => [
                    'status' => 'completed',
                    'title'  => 'Pesanan Selesai',
                    'desc'   => $isCustomNote ? $log->catatan : 'Helm telah berhasil diambil di gerai. Terima kasih telah berbelanja!',
                ],
                'cancelled' => [
                    'status' => 'cancelled',
                    'title'  => 'Pesanan Dibatalkan',
                    'desc'   => $isCustomNote ? $log->catatan : 'Pesanan telah dibatalkan.',
                ],
                default => null,
            };

            if ($eventData) {
                // If it's a pending status right at checkout creation, enrich the created event instead of duplicating
                if ($eventData['status'] === 'pending' && $log->status_dari === '-') {
                    $events[0]['desc'] = 'Pesanan dibuat. Menunggu penyelesaian pembayaran sebesar ' . format_rupiah($this->grand_total);
                    continue;
                }

                $eventData['time'] = $log->created_at ? $log->created_at->format('d M Y, H:i') : '-';
                $eventData['created_at'] = $log->created_at;
                $events[] = $eventData;
            }
        }

        // 3. Fallback: if current status is not represented yet (e.g. status was updated directly)
        $hasCurrentStatus = collect($events)->contains('status', $this->status);
        if (! $hasCurrentStatus) {
            $fallback = match ($this->status) {
                'unpaid_pickup' => [
                    'status' => 'unpaid_pickup',
                    'title'  => 'Menunggu Pengambilan di Gerai',
                    'desc'   => "Pesanan siap diambil. Tunjukkan Kode Pickup {$this->kode_pickup} ke kasir saat pengambilan barang.",
                ],
                'ready' => [
                    'status' => 'ready',
                    'title'  => 'Siap Diambil di Gerai',
                    'desc'   => "Helm sudah siap di Gerai Bayeng Putra Helm. Tunjukkan Kode Pickup {$this->kode_pickup} ke kasir.",
                ],
                'processing' => [
                    'status' => 'processing',
                    'title'  => 'Pesanan Sedang Disiapkan',
                    'desc'   => 'Staf toko sedang menyiapkan helm pesanan.',
                ],
                'paid' => [
                    'status' => 'paid',
                    'title'  => 'Pembayaran Diverifikasi',
                    'desc'   => 'Pembayaran telah diverifikasi oleh kasir gerai.',
                ],
                'completed' => [
                    'status' => 'completed',
                    'title'  => 'Pesanan Selesai',
                    'desc'   => 'Helm telah berhasil diambil di gerai.',
                ],
                'cancelled' => [
                    'status' => 'cancelled',
                    'title'  => 'Pesanan Dibatalkan',
                    'desc'   => 'Pesanan telah dibatalkan.',
                ],
                default => null,
            };

            if ($fallback) {
                $fallbackTime = $this->updated_at ?: $this->created_at;
                $fallback['time'] = $fallbackTime ? $fallbackTime->format('d M Y, H:i') : '-';
                $fallback['created_at'] = $fallbackTime;
                $events[] = $fallback;
            }
        }

        // Return newest at the top (Tokopedia timeline style: index 0 = latest milestone)
        return array_reverse($events);
    }
}
