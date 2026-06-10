<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\User;
use App\Models\TaskUpdate;
use App\Models\DailyReportItem;
use App\Models\DailyReport;
use App\Models\TaskMessage;

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
        'priority',
        'status',
        'due_date',
        'last_progress_percent',
        'started_at',
        'completed_at',
        'validated_by',
        'validated_at',
        'discussion_reopened_at',
    ];

    protected $casts = [
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

    /* =======================
       HELPERS
    ======================= */

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

<<<<<<< HEAD
//    scope pour filtrer les tâches visibles par un utilisateur donné, selon son rôle et ses liens avec les stages/étudiants/employés
        public function scopeVisibleTo($query, $user)
        {
            // ADMIN : voit tout
            if ($user->hasRole('admin')) {
                return $query;
            }
=======
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
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f

            // SUPERVISEUR : voit les tâches
            //   - des stages qu’il supervise (étudiants liés à un stage)
            //   - des employés dont il est le superviseur attitré (employes.supervisor_id)
            //   - des étudiants dont il est le superviseur direct (etudiants.supervisor_id)
            //   - ses propres tâches
            if ($user->hasRole('superviseur')) {
                // IDs des stages supervisés
                $stageIds = Stage::where('supervisor_id', $user->id)->pluck('id');

                // IDs des utilisateurs (employés) supervisés via employes.supervisor_id
                $supervisedEmployeUserIds = Employe::where('supervisor_id', $user->id)
                    ->with('personnel.user')
                    ->get()
                    ->map(fn($e) => optional($e->personnel?->user)->id)
                    ->filter()
                    ->values();

                // IDs des utilisateurs (étudiants) supervisés via etudiants.supervisor_id
                $supervisedEtudiantUserIds = Etudiant::where('supervisor_id', $user->id)
                    ->with('personnel.user')
                    ->get()
                    ->map(fn($e) => optional($e->personnel?->user)->id)
                    ->filter()
                    ->values();

                $supervisedUserIds = $supervisedEmployeUserIds
                    ->merge($supervisedEtudiantUserIds)
                    ->unique();

                return $query->where(function ($q) use ($user, $stageIds, $supervisedUserIds) {
                    $q->whereIn('stage_id', $stageIds)               // tâches des stages supervisés
                    ->orWhereIn('owner_id', $supervisedUserIds)     // tâches des employés/étudiants supervisés
                    ->orWhere('owner_id', $user->id);               // ses propres tâches
                });
            }

            // PRODUCTEUR (étudiant ou employé) : uniquement ses propres tâches
            return $query->where('owner_id', $user->id);
        }
}
