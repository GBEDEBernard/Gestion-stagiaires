<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteGeofence extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'name',
        'center_latitude',
        'center_longitude',
        'radius_meters',
        'allowed_accuracy_meters',
        'is_primary',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'center_latitude' => 'decimal:7',
        'center_longitude' => 'decimal:7',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }
}
