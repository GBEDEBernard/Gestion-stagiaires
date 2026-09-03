<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'attachment_type',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];

    protected $casts = [
        'reviewed_at'     => 'datetime',
        'edited_at'       => 'datetime',
        'attachment_size' => 'integer',
    ];

    protected $appends = [
        'attachment_url',
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

    public function hasAttachment(): bool
    {
        return !is_null($this->attachment_path);
    }

    public function isImage(): bool
    {
        return $this->attachment_type === 'image';
    }

    public function isFile(): bool
    {
        return $this->attachment_type === 'file';
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path
            ? Storage::disk('public')->url($this->attachment_path)
            : null;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachmentUrl();
    }

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
