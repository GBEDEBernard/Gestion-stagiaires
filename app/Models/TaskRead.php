<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Curseur de lecture (✓✓) par (tâche, utilisateur). T-005.
 * Un message est « lu » par X si X.last_read_message_id >= message.id.
 */
class TaskRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'last_read_message_id',
        'last_read_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
