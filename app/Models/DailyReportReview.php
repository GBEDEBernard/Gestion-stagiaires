<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\DailyReport;

class DailyReportReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'reviewer_id',
        'action',
        'comment',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /*
    |-----------------------
    | RELATIONS
    |-----------------------
    */

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /*
    |-----------------------
    | SCOPES UTILES
    |-----------------------
    */

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('reviewed_at');
    }

    /*
    |-----------------------
    | HELPERS (OPTIONNEL)
    |-----------------------
    */

    public function isApproved(): bool
    {
        return $this->action === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->action === 'rejected';
    }
}
