<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRequest extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SENT = 'sent';

    public const TYPE_ABSENCE = 'absence';
    public const TYPE_LATENESS = 'retard';
    public const TYPE_EARLY_EXIT = 'sortie_anticipee';

    protected $fillable = [
        'user_id',
        'stage_id',
        'domaine_id',
        'first_approver_id',
        'reviewed_by_id',
        'type',
        'status',
        'first_approval_status',
        'request_date',
        'starts_at',
        'ends_at',
        'reason',
        'details',
        'first_review_notes',
        'pdf_path',
        'signataires_snapshot',
        'submitted_at',
        'first_reviewed_at',
        'approved_at',
        'rejected_at',
        'sent_at',
        'pdf_generated_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'submitted_at' => 'datetime',
        'first_reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'sent_at' => 'datetime',
        'pdf_generated_at' => 'datetime',
        'signataires_snapshot' => 'array',
    ];

    public static function typeOptions(): array
    {
        return [
            self::TYPE_ABSENCE => 'Absence',
            self::TYPE_LATENESS => 'Retard',
            self::TYPE_EARLY_EXIT => 'Sortie anticipee',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_SUBMITTED => 'Soumise',
            self::STATUS_UNDER_REVIEW => 'En cours de validation',
            self::STATUS_APPROVED => 'Approuvee',
            self::STATUS_REJECTED => 'Refusee',
            self::STATUS_SENT => 'Envoyee aux signataires',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->requester();
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function domaine(): BelongsTo
    {
        return $this->belongsTo(Domaine::class);
    }

    public function firstApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_approver_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return static::typeOptions()[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusOptions()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusToneAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'slate',
            self::STATUS_SUBMITTED => 'blue',
            self::STATUS_UNDER_REVIEW => 'amber',
            self::STATUS_APPROVED => 'emerald',
            self::STATUS_SENT => 'teal',
            self::STATUS_REJECTED => 'rose',
            default => 'slate',
        };
    }
}
