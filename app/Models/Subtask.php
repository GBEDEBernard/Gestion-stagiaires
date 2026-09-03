<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subtask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'assigned_to_user_id',
        'title',
        'start_date',
        'end_date',
        'is_completed',
        'completed_at',
        'completed_by',
        'display_order',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /* =======================
       RELATIONS
    ======================= */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /** Utilisateur assigné à cette sous-tâche (un seul). */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /** Utilisateur qui a marqué la sous-tâche comme terminée. */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /* =======================
       HELPERS
    ======================= */

    /**
     * Vérifie si un utilisateur est assigné à cette sous-tâche
     * ou s'il est propriétaire/admin (permission étendue).
     */
    public function isAssignedTo(int $userId): bool
    {
        return (int) $this->assigned_to_user_id === $userId;
    }

    /**
     * Marque la sous-tâche comme terminée (verrou définitif).
     * Seul un admin peut réouvrir via SubtaskController::reopen.
     */
    public function markComplete(int $userId): void
    {
        if ($this->is_completed) {
            return; // Déjà terminée — verrou.
        }

        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => $userId,
        ]);

        // Recalcule la progression de la tâche parente.
        $this->task->syncProgressFromSubtasks();
    }
}
