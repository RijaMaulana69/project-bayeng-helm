<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'user_type',
        'user_id',
        'username',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke User (Admin / Karyawan)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Relasi ke Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'user_id', 'id_pelanggan');
    }

    /**
     * Nama aktor yang akurat sesuai user_type
     */
    public function getAktorNamaAttribute(): string
    {
        if ($this->user_type === 'pelanggan') {
            return $this->username ?: ($this->pelanggan?->nama ?: 'Pelanggan');
        }

        if ($this->user_type === 'user') {
            return $this->user?->nama_lengkap ?: ($this->username ?: 'Staf Toko');
        }

        return $this->username ?: 'Sistem / Tamu';
    }

    /**
     * Label peran aktor
     */
    public function getAktorPeranAttribute(): string
    {
        if ($this->user_type === 'pelanggan') {
            return 'Pelanggan';
        }

        if ($this->user_type === 'user') {
            $roleName = strtolower($this->user?->role?->nama_role ?? '');
            if ($roleName === 'owner') {
                return 'Pemilik Toko';
            }
            if ($roleName === 'karyawan') {
                return 'Kasir Gerai';
            }
            return 'Staf Toko';
        }

        return 'Sistem / Tamu';
    }

    /**
     * Humanized Indonesian Action Label
     */
    public static function labelForAction(string $action): string
    {
        return match($action) {
            'login_success' => 'Berhasil Masuk',
            'login_failed' => 'Gagal Masuk',
            'login_blocked' => 'Diblokir Sistem',
            'logout' => 'Keluar Sesi',
            'checkout' => 'Pesanan Baru',
            'upload_bukti' => 'Unggah Bukti',
            'update_status_pesanan' => 'Update Status',
            'pickup_completed' => 'Serah Terima Helm',
            'create_produk' => 'Tambah Produk',
            'update_produk' => 'Ubah Produk',
            'delete_produk' => 'Hapus Produk',
            'add_stok' => 'Barang Masuk',
            'create_user' => 'Tambah Karyawan',
            'update_user' => 'Ubah Karyawan',
            'delete_user' => 'Hapus Karyawan',
            'export_pdf' => 'Ekspor PDF',
            'export_csv' => 'Ekspor CSV',
            'register' => 'Registrasi Akun',
            'create_kategori', 'update_kategori', 'delete_kategori' => 'Kelola Kategori',
            'create_merk', 'update_merk', 'delete_merk' => 'Kelola Merk',
            'create_warna', 'update_warna', 'delete_warna' => 'Kelola Warna',
            'update_profil' => 'Ubah Profil',
            'update_pengaturan' => 'Pengaturan Toko',
            default => ucwords(str_replace('_', ' ', $action)),
        };
    }

    public function getAksiLabelAttribute(): string
    {
        return static::labelForAction($this->action ?? '');
    }

    /**
     * Log an activity
     */
    public static function log(string $action, string $description = '', ?string $userType = 'guest', ?int $userId = null, ?string $username = null): self
    {
        return static::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'username' => $username,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
        ]);
    }
}
