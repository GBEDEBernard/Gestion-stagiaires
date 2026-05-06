<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'etudiant_id',
        'site_id',
        'site_geofence_id',
        'user_id',
        'trusted_device_id',
        'event_type',
        'status',
        'occurred_at',
        'latitude',
        'longitude',
        'accuracy_meters',
        'distance_to_site_meters',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'reason_code',
        'rejection_reason',
        'meta',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'meta' => 'array',
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function geofence()
    {
        return $this->belongsTo(SiteGeofence::class, 'site_geofence_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trustedDevice()
    {
        return $this->belongsTo(TrustedDevice::class);
    }

    public function anomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }

    public function checkInDay()
    {
        return $this->hasOne(AttendanceDay::class, 'check_in_event_id');
    }

    public function checkOutDay()
    {
        return $this->hasOne(AttendanceDay::class, 'check_out_event_id');
    }
}
