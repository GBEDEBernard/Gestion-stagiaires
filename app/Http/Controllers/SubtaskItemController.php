<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\SubtaskItem;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskItemController extends Controller
{
    /**
     * Ajouter un item personnel à une sous-tâche.
     * Seul l'utilisateur assigné à la sous-tâche peut créer.
     */
    public function store(Request $request, Task $task, Subtask $subtask)
    {
        $user = $request->user();
        abort_unless((int) $subtask->task_id === $task->id, 404);

        $canManage = $subtask->isAssignedTo($user->id)
            || $user->hasAnyRole(['admin', 'superviseur'])
            || (int) $task->owner_id === $user->id;

        abort_unless($canManage, 403, 'Vous n\'êtes pas assigné à cette sous-tâche.');

        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $order = $subtask->items()->max('display_order') + 1;

        $subtask->items()->create([
            'title'         => $data['title'],
            'display_order' => $order,
        ]);

        $subtask->task->syncProgressFromSubtasks();

        return back()->with('success', 'Item ajouté.');
    }

    /**
     * Modifier un item.
     */
    public function update(Request $request, Task $task, Subtask $subtask, SubtaskItem $item)
    {
        $user = $request->user();
        abort_unless((int) $subtask->task_id === $task->id, 404);
        abort_unless((int) $item->subtask_id === $subtask->id, 404);

        $canManage = $subtask->isAssignedTo($user->id)
            || $user->hasAnyRole(['admin', 'superviseur'])
            || (int) $task->owner_id === $user->id;

        abort_unless($canManage, 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $item->update(['title' => $data['title']]);

        return back()->with('success', 'Item mis à jour.');
    }

    /**
     * Supprimer un item (soft delete).
     */
    public function destroy(Task $task, Subtask $subtask, SubtaskItem $item)
    {
        $user = auth()->user();
        abort_unless((int) $subtask->task_id === $task->id, 404);
        abort_unless((int) $item->subtask_id === $subtask->id, 404);

        $canManage = $subtask->isAssignedTo($user->id)
            || $user->hasAnyRole(['admin', 'superviseur'])
            || (int) $task->owner_id === $user->id;

        abort_unless($canManage, 403);

        $item->delete();
        $subtask->task->syncProgressFromSubtasks();

        return back()->with('success', 'Item supprimé.');
    }

    /**
     * Marquer un item comme terminé ou non terminé (toggle).
     */
    public function toggle(Request $request, Task $task, Subtask $subtask, SubtaskItem $item)
    {
        $user = $request->user();
        abort_unless((int) $subtask->task_id === $task->id, 404);
        abort_unless((int) $item->subtask_id === $subtask->id, 404);

        $canToggle = $subtask->isAssignedTo($user->id)
            || $user->hasAnyRole(['admin', 'superviseur'])
            || (int) $task->owner_id === $user->id;

        abort_unless($canToggle, 403);

        if ($item->is_completed) {
            $item->markIncomplete();
        } else {
            $item->markComplete($user->id);
        }

        return back()->with('success', $item->is_completed ? '✅ Item terminé.' : 'Item réouvert.');
    }
}
