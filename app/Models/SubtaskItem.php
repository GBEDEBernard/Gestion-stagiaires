<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubtaskItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subtask_id',
        'title',
        'is_completed',
        'completed_at',
        'completed_by',
        'display_order',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /* =======================
       RELATIONS
    ======================= */

    public function subtask()
    {
        return $this->belongsTo(Subtask::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /* =======================
       HELPERS
    ======================= */

    public function markComplete(int $userId): void
    {
        if ($this->is_completed) {
            return;
        }

        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => $userId,
        ]);

        $this->subtask->syncProgressFromItems();
    }

    public function markIncomplete(): void
    {
        if (!$this->is_completed) {
            return;
        }

        $this->update([
            'is_completed' => false,
            'completed_at' => null,
            'completed_by' => null,
        ]);

        $this->subtask->syncProgressFromItems();
    }
}
