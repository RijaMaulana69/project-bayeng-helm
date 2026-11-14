<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_helm',
        'id_varian',
        'nama_helm',
        'warna',
        'ukuran',
        'harga',
        'qty',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function getJumlahAttribute()
    {
        return $this->qty;
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function helm()
    {
        return $this->belongsTo(Helm::class, 'id_helm', 'id_helm');
    }

    public function varian()
    {
        return $this->belongsTo(VarianHelm::class, 'id_varian', 'id_varian');
    }
}
