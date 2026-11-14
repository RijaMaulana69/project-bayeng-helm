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
}
