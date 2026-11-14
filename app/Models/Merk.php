<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merk extends Model
{
    protected $table = 'merk';
    protected $primaryKey = 'id_merk';
    public $timestamps = false;

    protected $fillable = [
        'nama_merk',
    ];

    public function helms()
    {
        return $this->hasMany(Helm::class, 'id_merk', 'id_merk');
    }
}
