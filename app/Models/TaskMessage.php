<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Message du fil de discussion d'une tâche (T-003 / T-005).
 *
 * type :
 *  - 'message'       => saisi par un humain (producteur / superviseur / admin)
 *  - 'report_jalon'  => entrée système référençant un rapport du jour
 *  - 'status_change' => entrée système (changement de statut / progression)
 *
 * Pièce jointe (T-005) : vocal / image / fichier via attachment_*.
 * Réponse citée (WhatsApp-like) : parent_id.
 */
class TaskMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'parent_id',
        'type',
        'body',
        'attachment_type',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'attachment_duration',
        'daily_report_id',
        'edited_at',
    ];

    protected $casts = [
        'attachment_size'     => 'integer',
        'attachment_duration' => 'integer',
        'edited_at'           => 'datetime',
    ];

    /* =======================
       RELATIONS
    ======================= */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    /** Message cité (réponse façon WhatsApp). */
    public function parent()
    {
        return $this->belongsTo(TaskMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(TaskMessage::class, 'parent_id');
    }

    public function reactions()
    {
        return $this->hasMany(TaskMessageReaction::class);
    }

    /* =======================
       HELPERS
    ======================= */

    public function isSystem(): bool
    {
        return in_array($this->type, ['report_jalon', 'status_change'], true);
    }

    public function hasAttachment(): bool
    {
        return !is_null($this->attachment_path);
    }

    public function isVoice(): bool
    {
        return $this->attachment_type === 'audio';
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
}
