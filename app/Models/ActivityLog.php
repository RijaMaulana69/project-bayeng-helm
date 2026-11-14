<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'user_type',
        'user_id',
        'username',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Log an activity
     */
    public static function log(string $action, string $description = '', ?string $userType = 'guest', ?int $userId = null, ?string $username = null): self
    {
        return static::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'username' => $username,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
        ]);
    }
}
