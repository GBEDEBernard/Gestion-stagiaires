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

    /** Items personnels créés par l'utilisateur assigné (niveau 2). */
    public function items()
    {
        return $this->hasMany(SubtaskItem::class)->orderBy('display_order');
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
            return;
        }

        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => $userId,
        ]);

        $this->task->syncProgressFromSubtasks();
    }

    /**
     * Nombre d'items terminés pour cette sous-tâche.
     */
    public function completedItemsCount(): int
    {
        return $this->items()->where('is_completed', true)->count();
    }

    /**
     * Nombre total d'items pour cette sous-tâche.
     */
    public function totalItemsCount(): int
    {
        return $this->items()->count();
    }

    /**
     * Progression personnelle de l'utilisateur sur cette sous-tâche.
     * Basée sur les subtask_items créés par l'utilisateur.
     * Si pas d'items → 0% (fallback manuel).
     */
    public function personalProgress(): int
    {
        $total = $this->totalItemsCount();
        if ($total === 0) {
            return $this->is_completed ? 100 : 0;
        }
        return round(($this->completedItemsCount() / $total) * 100);
    }

    /**
     * Recalcule is_completed basé sur les items.
     * Si tous les items sont terminés → sous-tâche terminée.
     * Si un item est dé-terminé → sous-tâche rouverte (seul admin peut rouvrir).
     */
    public function syncProgressFromItems(): void
    {
        $total = $this->totalItemsCount();
        if ($total === 0) {
            return;
        }

        $completed = $this->completedItemsCount();
        $progress = round(($completed / $total) * 100);

        // Si tous les items sont terminés, marquer la sous-tâche terminée
        if ($progress >= 100 && !$this->is_completed) {
            $this->update([
                'is_completed' => true,
                'completed_at' => now(),
                'completed_by' => $this->assigned_to_user_id,
            ]);
        }

        // Recalcule la progression de la tâche parente
        $this->task->syncProgressFromSubtasks();
    }
}
