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
        'logo',
        'deskripsi',
    ];

    public function helms()
    {
        return $this->hasMany(Helm::class, 'id_merk', 'id_merk');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo && file_exists(public_path('uploads/merk/' . $this->logo))) {
            return asset('uploads/merk/' . $this->logo);
        }
        if ($this->logo && file_exists(public_path('uploads/' . $this->logo))) {
            return asset('uploads/' . $this->logo);
        }
        return null;
    }
}
