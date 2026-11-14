<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    protected $table = 'stok_masuk';
    protected $primaryKey = 'id_stok_masuk';
    public $timestamps = false;

    protected $fillable = [
        'id_helm',
        'warna',
        'ukuran',
        'id_user',
        'jumlah',
        'harga_awal',
        'harga_jual',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_masuk' => 'datetime',
        ];
    }

    public function helm()
    {
        return $this->belongsTo(Helm::class, 'id_helm', 'id_helm');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
