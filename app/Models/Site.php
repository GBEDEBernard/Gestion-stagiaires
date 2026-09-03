<?php

namespace App\Models;

use App\Models\Domaine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'qr_token',
        'name',
        'contact_person',
        'contact_phone',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'is_active',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($site) {
            if (empty($site->qr_token)) {
                $prefix = !empty($site->code) ? \Illuminate\Support\Str::slug($site->code) : 'site';
                $site->qr_token = \Illuminate\Support\Str::lower($prefix) . '-' . \Illuminate\Support\Str::random(24);
            }
        });
    }

    public function getPointageUrl(): string
    {
        return route('presence.qr.scan', ['site_token' => $this->qr_token]);
    }

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function geofences()
    {
        return $this->hasMany(SiteGeofence::class);
    }

    public function stages()
    {
        return $this->hasMany(Stage::class);
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function attendanceDays()
    {
        return $this->hasMany(AttendanceDay::class);
    }

    public function domaines()
    {
        return $this->belongsToMany(Domaine::class, 'domaine_site');
    }
}
