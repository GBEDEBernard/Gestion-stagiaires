<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Réaction emoji sur un message du fil (T-005).
 */
class TaskMessageReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_message_id',
        'user_id',
        'emoji',
    ];

    public function message()
    {
        return $this->belongsTo(TaskMessage::class, 'task_message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
