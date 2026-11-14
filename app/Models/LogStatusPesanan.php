<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStatusPesanan extends Model
{
    protected $table = 'log_status_pesanan';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'status_dari',
        'status_ke',
        'catatan',
        'diubah_oleh',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
