<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warna extends Model
{
    protected $table = 'warna';
    protected $primaryKey = 'id_warna';
    public $timestamps = false;

    protected $fillable = [
        'nama_warna',
        'kode_hex',
        'jenis_finishing',
    ];
}
