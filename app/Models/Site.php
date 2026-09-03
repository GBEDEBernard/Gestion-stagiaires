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
