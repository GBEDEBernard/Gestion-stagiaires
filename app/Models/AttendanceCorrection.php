<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'attendance_day_id',
        'user_id',
        'field',
        'original_check_in_at',
        'original_arrival_status',
        'original_late_minutes',
        'original_check_out_at',
        'corrected_check_in_at',
        'corrected_check_out_at',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'original_check_in_at'  => 'datetime',
        'corrected_check_in_at' => 'datetime',
        'original_check_out_at'  => 'datetime',
        'corrected_check_out_at' => 'datetime',
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
