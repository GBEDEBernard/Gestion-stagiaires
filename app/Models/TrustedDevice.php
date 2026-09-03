<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'device_uuid',
        'device_label',
        'platform',
        'browser',
        'app_version',
        'last_ip_address',
        'first_seen_at',
        'last_seen_at',
        'is_trusted',
        'is_primary',
        'revoked_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_trusted' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }
}
