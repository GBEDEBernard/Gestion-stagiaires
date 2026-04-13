<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'etudiant_id',
        'site_id',
        'check_in_event_id',
        'check_out_event_id',
        'attendance_date',
        'first_check_in_at',
        'last_check_out_at',
        'worked_minutes',
        'late_minutes',
        'early_departure_minutes',
        'anomaly_count',
        'day_status',
        'validation_status',
        'validated_by',
        'validated_at',
        'summary_notes',
        'arrival_status',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'first_check_in_at' => 'datetime',
        'last_check_out_at' => 'datetime',
        'validated_at' => 'datetime',
        'arrival_status' => 'string',
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

    public function checkInEvent()
    {
        return $this->belongsTo(AttendanceEvent::class, 'check_in_event_id');
    }

    public function checkOutEvent()
    {
        return $this->belongsTo(AttendanceEvent::class, 'check_out_event_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function anomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}
