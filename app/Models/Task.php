<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'stage_id',
        'etudiant_id',
        'owner_id',
        'assigned_by',
        'title',
        'description',
        'start_date',
        'priority',
        'status',
        'due_date',
        'pdf_path',
        'last_progress_percent',
        'base_progress_percent',
        'started_at',
        'completed_at',
        'validated_by',
        'validated_at',
        'discussion_reopened_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'validated_at' => 'datetime',
        'discussion_reopened_at' => 'datetime',
        'last_progress_percent' => 'integer',
    ];

    /**
     * Statuts du cycle de vie (cf. doc/UI-SPEC-T003.md §2 + T-005).
     * `awaiting_validation` : 100 % atteint, en attente de la clôture ADMIN.
     */
    public const STATUSES = ['pending', 'in_progress', 'blocked', 'changes_requested', 'awaiting_validation', 'completed'];

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

    /** Le producteur propriétaire de la tâche (employé ou étudiant). */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** T-008 : toutes les personnes assignées à cette tâche (N ↦ N). */
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignee', 'task_id', 'user_id')
            ->withPivot('assigned_at');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** Admin ayant validé (clôturé) la tâche. */
    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /** Curseurs de lecture (✓✓) des participants. */
    public function reads()
    {
        return $this->hasMany(TaskRead::class);
    }

    public function updates()
    {
        return $this->hasMany(TaskUpdate::class)->latest('happened_at');
    }

    public function reportItems()
    {
        return $this->hasMany(DailyReportItem::class);
    }

    /** Rapports journaliers rattachés à cette tâche (lien direct). */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class)->orderByDesc('report_date');
    }

    /** Fil de discussion de la tâche (messages + jalons + changements de statut). */
    public function messages()
    {
        return $this->hasMany(TaskMessage::class)->orderBy('created_at');
    }

    /** Sous-tâches de cette tâche, ordonnées. */
    public function subtasks()
    {
        return $this->hasMany(Subtask::class)->orderBy('display_order');
    }

    /* =======================
       HELPERS
    ======================= */

    /**
     * Calcule la progression à partir des sous-tâches terminées.
     * Si aucune sous-tâche → retourne last_progress_percent (compatibilité).
     */
    public function computeProgressFromSubtasks(): int
    {
        $total = $this->subtasks()->count();
        if ($total === 0) {
            return (int) $this->last_progress_percent;
        }
        $done = $this->subtasks()->where('is_completed', true)->count();
        return (int) round($done / $total * 100);
    }

    /**
     * Retourne le titre de la première sous-tâche non terminée (prochaine étape).
     * Null si toutes les sous-tâches sont terminées ou qu'il n'y en a pas.
     */
    public function nextStepLabel(): ?string
    {
        return $this->subtasks()
            ->where('is_completed', false)
            ->orderBy('display_order')
            ->value('title');
    }

    /**
     * Met à jour last_progress_percent depuis les sous-tâches et
     * ajuste le statut automatiquement.
     */
    public function syncProgressFromSubtasks(): void
    {
        $total = $this->subtasks()->count();
        if ($total === 0) {
            return; // Rien à recalculer sans sous-tâches.
        }

        $progress = $this->computeProgressFromSubtasks();
        $originalStatus = $this->status;

        $updates = ['last_progress_percent' => $progress];

        if ($progress >= 100 && !$this->isCompleted()) {
            $updates['status'] = 'awaiting_validation';
            $updates['completed_at'] = $this->completed_at ?: now();
        } elseif ($progress > 0 && in_array($originalStatus, ['pending', 'changes_requested'], true)) {
            $updates['status'] = 'in_progress';
            $updates['started_at'] = $this->started_at ?: now();
        }

        $this->update($updates);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isAwaitingValidation(): bool
    {
        return $this->status === 'awaiting_validation';
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && !$this->isCompleted()
            && $this->due_date->isPast();
    }

    /**
     * T-008 — Un utilisateur est-il participant de la tâche ?
     * (propriétaire ou assigné via la table pivot).
     */
    public function isParticipant(int $userId): bool
    {
        return (int) $this->owner_id === $userId
            || $this->assignees()->whereKey($userId)->exists();
    }

    /**
     * Un utilisateur reçoit-il déjà cette tâche (propriétaire ou assigné) ?
     * Utilisé pour dédupliquer les assignations multi-personnes.
     */
    public function alreadyReceivedBy(int $userId): bool
    {
        return $this->isParticipant($userId);
    }

    /**
     * État de la discussion (T-005) :
     *  - 'locked' : tâche créée mais aucun rapport encore → discussion pas ouverte.
     *  - 'closed' : tâche clôturée par l'admin → lecture seule (réouvrable).
     *  - 'open'   : au moins un rapport et tâche non clôturée → chat actif.
     */
    public function discussionState(): string
    {
        if ($this->isCompleted()) {
            return 'closed';
        }

        $hasReport = $this->relationLoaded('dailyReports')
            ? $this->dailyReports->isNotEmpty()
            : $this->dailyReports()->exists();

        return $hasReport ? 'open' : 'locked';
    }

    public function isDiscussionOpen(): bool
    {
        return $this->discussionState() === 'open';
    }

    /* =======================
       SCOPES
    ======================= */

    public function scopeVisibleTo($query, $user)
    {
        // 👑 ADMIN : voit tout
        if ($user->hasRole('admin')) {
            return $query;
        }

        // 👨‍🏫 SUPERVISEUR : voit les tâches des stages qu'il supervise
        //    + (le cas échéant) les tâches des producteurs de son domaine.
        if ($user->hasRole('superviseur')) {
            return $query->where(function ($q) use ($user) {
                $q->whereHas('stage', fn($s) => $s->where('supervisor_id', $user->id))
                    ->orWhere('owner_id', $user->id);
            });
        }

        // 👨‍🎓 / 👨‍🔧 PRODUCTEUR (étudiant ou employé) : uniquement ses tâches,
        //    en tant que propriétaire OU que personne assignée (T-008).
        return $query->where(function ($q) use ($user) {
            $q->where('owner_id', $user->id)
                ->orWhereHas('assignees', fn($a) => $a->whereKey($user->id));
        });
    }
}