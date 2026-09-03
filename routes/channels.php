<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Task Discussion Channels
|--------------------------------------------------------------------------
|
| Private channels for real-time task discussion.
| Only users with access to the task (owner, supervisors, admins) can listen.
|
*/

Broadcast::channel('task.{taskId}', function (User $user, $taskId) {
    $task = Task::find($taskId);

    if (!$task) {
        return false;
    }

    // Owner can always listen
    if ((int) $task->owner_id === (int) $user->id) {
        return true;
    }

    // Supervisors and admins can listen
    if ($user->hasAnyRole(['admin', 'superviseur'])) {
        // If there's a stage and the user is the stage supervisor
        if ($task->stage && (int) $task->stage->supervisor_id === (int) $user->id) {
            return true;
        }
        // Admins can always listen
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    return false;
});
