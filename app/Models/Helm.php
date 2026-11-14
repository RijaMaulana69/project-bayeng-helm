<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helm extends Model
{
    protected $table = 'helm';
    protected $primaryKey = 'id_helm';
    public $timestamps = false;

    protected $fillable = [
        'id_merk',
        'id_kategori',
        'nama_helm',
        'warna',
        'ukuran',
        'harga_awal',
        'harga_jual',
        'stok',
        'deskripsi',
        'gambar',
    ];

    public function merk()
    {
        return $this->belongsTo(Merk::class, 'id_merk', 'id_merk');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function varians()
    {
        return $this->hasMany(VarianHelm::class, 'id_helm', 'id_helm');
    }

    public function getGambarUrlAttribute(): string
    {
        if ($this->gambar && file_exists(public_path('uploads/' . $this->gambar))) {
            return asset('uploads/' . $this->gambar);
        }
        return asset('assets/logo.png');
    }
}
