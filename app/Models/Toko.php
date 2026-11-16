<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = 'toko';
    protected $primaryKey = 'id_toko';
    public $timestamps = false;

    protected $fillable = [
        'nama_toko',
        'alamat',
        'telepon',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return file_exists(public_path('assets/logo.png')) ? asset('assets/logo.png') : null;
        }

        if (file_exists(public_path($this->logo))) {
            return asset($this->logo);
        }

        if (file_exists(public_path('assets/' . $this->logo))) {
            return asset('assets/' . $this->logo);
        }

        if (file_exists(public_path('uploads/toko/' . $this->logo))) {
            return asset('uploads/toko/' . $this->logo);
        }

        if (file_exists(public_path('storage/' . $this->logo))) {
            return asset('storage/' . $this->logo);
        }

        return file_exists(public_path('assets/logo.png')) ? asset('assets/logo.png') : null;
    }
}
