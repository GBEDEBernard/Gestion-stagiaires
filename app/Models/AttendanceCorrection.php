<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'attendance_day_id',
        'user_id',
        'original_check_in_at',
        'original_arrival_status',
        'original_late_minutes',
        'corrected_check_in_at',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'original_check_in_at'  => 'datetime',
        'corrected_check_in_at' => 'datetime',
        'original_late_minutes' => 'integer',
    ];

    public function attendanceDay()
    {
        return $this->belongsTo(AttendanceDay::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
