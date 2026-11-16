<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatPercakapan extends Model
{
    use HasFactory;

    protected $table = 'chat_percakapan';
    protected $primaryKey = 'id_percakapan';

    protected $fillable = [
        'id_pelanggan',
        'session_id',
        'nama_pengirim',
        'email_pengirim',
        'telepon_pengirim',
        'id_helm_terkait',
        'pesan_terakhir',
        'pesan_terakhir_at',
        'unread_karyawan',
        'unread_pelanggan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pesan_terakhir_at' => 'datetime',
            'unread_karyawan' => 'integer',
            'unread_pelanggan' => 'integer',
        ];
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function helmTerkait()
    {
        return $this->belongsTo(Helm::class, 'id_helm_terkait', 'id_helm');
    }

    public function pesans()
    {
        return $this->hasMany(ChatPesan::class, 'id_percakapan', 'id_percakapan')->orderBy('created_at', 'asc');
    }

    public function markAsReadByKaryawan(): void
    {
        $this->update(['unread_karyawan' => 0]);
        $this->pesans()
            ->where('pengirim_tipe', 'pelanggan')
            ->where('dibaca', false)
            ->update(['dibaca' => true]);
    }

    public function markAsReadByPelanggan(): void
    {
        $this->update(['unread_pelanggan' => 0]);
        $this->pesans()
            ->where('pengirim_tipe', 'karyawan')
            ->where('dibaca', false)
            ->update(['dibaca' => true]);
    }
}
