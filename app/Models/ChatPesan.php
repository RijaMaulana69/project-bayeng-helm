<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatPesan extends Model
{
    use HasFactory;

    protected $table = 'chat_pesan';
    protected $primaryKey = 'id_pesan';

    protected $fillable = [
        'id_percakapan',
        'pengirim_tipe',
        'id_user',
        'pesan',
        'id_helm',
        'dibaca',
    ];

    protected function casts(): array
    {
        return [
            'dibaca' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function percakapan()
    {
        return $this->belongsTo(ChatPercakapan::class, 'id_percakapan', 'id_percakapan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function helm()
    {
        return $this->belongsTo(Helm::class, 'id_helm', 'id_helm');
    }
}
