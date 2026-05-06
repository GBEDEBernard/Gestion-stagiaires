<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stage_id',
        'etudiant_id',
        'user_id',
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
        'hours_declared' => 'float',
        'completion_rate' => 'integer',
    ];

    /* =======================
       RELATIONS
    ======================= */

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceDay()
    {
        return $this->belongsTo(AttendanceDay::class);
    }

    public function items()
    {
        return $this->hasMany(DailyReportItem::class)->orderBy('display_order');
    }

    // 🔥 FIX IMPORTANT (ton erreur)
    public function reviews()
    {
        return $this->hasMany(DailyReportReview::class, 'daily_report_id')
            ->latest('reviewed_at');
    }

    /* =======================
       HELPERS
    ======================= */

    public function isEmployeeReport(): bool
    {
        return !is_null($this->user_id);
    }

    public function isStudentReport(): bool
    {
        return !is_null($this->etudiant_id);
    }

    /* =======================
       SCOPES
    ======================= */

    public function scopeVisibleTo($query, $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('superviseur')) {
            return $query->whereHas('stage', fn($q) =>
                $q->where('supervisor_id', $user->id)
            );
        }

        if ($user->hasRole('etudiant')) {
            return $query->where('etudiant_id', optional($user->etudiant)->id);
        }

        return $query->where('user_id', $user->id);
    }
}