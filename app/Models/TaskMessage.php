<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Message du fil de discussion d'une tâche (T-003).
 *
 * type :
 *  - 'message'       => saisi par un humain (producteur / superviseur / admin)
 *  - 'report_jalon'  => entrée système référençant un rapport du jour
 *  - 'status_change' => entrée système (changement de statut / progression)
 */
class TaskMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'type',
        'body',
        'daily_report_id',
    ];

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

    public function isSystem(): bool
    {
        return in_array($this->type, ['report_jalon', 'status_change'], true);
    }
}
