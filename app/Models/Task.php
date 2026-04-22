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
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
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

    public function scopeVisibleTo($query, $user)
    {
        // 👑 ADMIN : voit tout
        if ($user->hasRole('admin')) {
            return $query;
        }

        // 👨‍🏫 SUPERVISEUR : voit les tâches de ses étudiants
        if ($user->hasRole('superviseur')) {
            return $query->whereHas('stage', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            });
        }

        // 👨‍🎓 ÉTUDIANT : voit uniquement ses tâches
        if ($user->hasRole('etudiant')) {
            return $query->where('etudiant_id', optional($user->etudiant)->id);
        }

        // 👨‍🔧 EMPLOYÉ : ses propres tâches assignées
        return $query->where('assigned_by', $user->id);
    }
}
