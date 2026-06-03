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
        'priority',
        'status',
        'due_date',
        'last_progress_percent',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_progress_percent' => 'integer',
    ];

    /** Statuts du cycle de vie (cf. doc/UI-SPEC-T003.md §2). */
    public const STATUSES = ['pending', 'in_progress', 'blocked', 'changes_requested', 'completed'];

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

    public function isOverdue(): bool
    {
        return $this->due_date
            && !$this->isCompleted()
            && $this->due_date->isPast();
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

        // 👨‍🎓 / 👨‍🔧 PRODUCTEUR (étudiant ou employé) : uniquement ses tâches.
        return $query->where('owner_id', $user->id);
    }
}
