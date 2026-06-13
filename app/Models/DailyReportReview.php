<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'reviewer_id',
        'action',
        'comment',
        'edited_at',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'edited_at'   => 'datetime',
    ];

    /* ── Relations ─────────────────────────────────────── */

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /* ── Scopes ─────────────────────────────────────────── */

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('reviewed_at');
    }

    /* ── Helpers ─────────────────────────────────────────── */

    public function isApproved(): bool
    {
        return $this->action === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->action === 'rejected';
    }

    public function wasEdited(): bool
    {
        return !is_null($this->edited_at);
    }
}