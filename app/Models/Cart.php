<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'id_cart';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'id_helm',
        'id_varian',
        'qty',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
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
