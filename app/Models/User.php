<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false; // database has created_at without updated_at

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'id_role',
        'last_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'created_at' => 'datetime',
            'last_active' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function isOwner(): bool
    {
        return strtolower($this->role?->nama_role ?? '') === 'owner';
    }

    public function isKaryawan(): bool
    {
        return strtolower($this->role?->nama_role ?? '') === 'karyawan';
    }
}
