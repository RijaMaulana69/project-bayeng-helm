<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VarianHelm extends Model
{
    protected $table = 'varian_helm';
    protected $primaryKey = 'id_varian';
    public $timestamps = false;

    protected $fillable = [
        'id_helm',
        'warna',
        'ukuran',
        'stok',
    ];

    public function helm()
    {
        return $this->belongsTo(Helm::class, 'id_helm', 'id_helm');
    }
}
