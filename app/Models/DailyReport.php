<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReport extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'stage_id',
        'etudiant_id',
        'attendance_day_id',
        'report_date',
        'title',
        'summary',
        'blockers',
        'next_steps',
        'hours_declared',
        'completion_rate',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'supervisor_comment',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'hours_declared' => 'decimal:2',
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function attendanceDay()
    {
        return $this->belongsTo(AttendanceDay::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function items()
    {
        return $this->hasMany(DailyReportItem::class)->orderBy('display_order');
    }

    public function reviews()
    {
        return $this->hasMany(DailyReportReview::class)->latest('reviewed_at');
    }
}
