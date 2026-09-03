<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    /**
     * Ajouter une sous-tâche à une tâche existante.
     * Accessible au propriétaire de la tâche et à l'admin.
     */
    public function store(Request $request, Task $task)
    {
        $this->authorizeManage($task);

        $taskStart = $task->start_date;
        $taskEnd   = $task->due_date;

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'start_date' => [
                'nullable', 'date',
                $taskStart ? 'after_or_equal:' . $taskStart->toDateString() : '',
                $taskEnd   ? 'before_or_equal:' . $taskEnd->toDateString() : '',
            ],
            'end_date' => [
                'nullable', 'date',
                'after_or_equal:start_date',
                $taskEnd   ? 'before_or_equal:' . $taskEnd->toDateString() : '',
            ],
            'assigned_to_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $order = $task->subtasks()->max('display_order') + 1;

        $task->subtasks()->create([
            'title'               => $data['title'],
            'start_date'          => $data['start_date'] ?? null,
            'end_date'            => $data['end_date']   ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
            'display_order'       => $order,
        ]);

        return back()->with('success', 'Sous-tâche ajoutée.');
    }

    /**
     * Modifier une sous-tâche.
     */
    public function update(Request $request, Task $task, Subtask $subtask)
    {
        $this->authorizeManage($task);
        abort_unless((int) $subtask->task_id === $task->id, 404);

        $taskStart = $task->start_date;
        $taskEnd   = $task->due_date;

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'start_date' => [
                'nullable', 'date',
                $taskStart ? 'after_or_equal:' . $taskStart->toDateString() : '',
                $taskEnd   ? 'before_or_equal:' . $taskEnd->toDateString() : '',
            ],
            'end_date' => [
                'nullable', 'date',
                'after_or_equal:start_date',
                $taskEnd   ? 'before_or_equal:' . $taskEnd->toDateString() : '',
            ],
            'assigned_to_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $subtask->update([
            'title'               => $data['title'],
            'start_date'          => $data['start_date'] ?? null,
            'end_date'            => $data['end_date']   ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
        ]);

        return back()->with('success', 'Sous-tâche mise à jour.');
    }

    /**
     * Supprimer une sous-tâche (soft delete).
     * Recalcule la progression après suppression.
     */
    public function destroy(Task $task, Subtask $subtask)
    {
        $this->authorizeManage($task);
        abort_unless((int) $subtask->task_id === $task->id, 404);

        $subtask->delete();
        $task->syncProgressFromSubtasks();

        return back()->with('success', 'Sous-tâche supprimée.');
    }

    /**
     * Marquer une sous-tâche comme terminée (verrou définitif).
     * Seul l'utilisateur assigné ou admin/superviseur peuvent le faire.
     */
    public function complete(Request $request, Task $task, Subtask $subtask)
    {
        $user = $request->user();
        abort_unless((int) $subtask->task_id === $task->id, 404);

        $canComplete = $subtask->isAssignedTo($user->id)
            || $user->hasAnyRole(['admin', 'superviseur'])
            || (int) $task->owner_id === $user->id;

        abort_unless($canComplete, 403, 'Vous n\'êtes pas assigné à cette sous-tâche.');

        if ($subtask->is_completed) {
            return back()->with('info', 'Cette sous-tâche est déjà terminée.');
        }

        $subtask->markComplete($user->id);

        return back()->with('success', '✅ Sous-tâche marquée comme terminée.');
    }

    /**
     * Réouvrir une sous-tâche — ADMIN UNIQUEMENT.
     */
    public function reopen(Task $task, Subtask $subtask)
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin'), 403);
        abort_unless((int) $subtask->task_id === $task->id, 404);

        $subtask->update([
            'is_completed' => false,
            'completed_at' => null,
            'completed_by' => null,
        ]);

        $task->syncProgressFromSubtasks();

        return back()->with('success', 'Sous-tâche réouverte.');
    }

    /* =======================
       HELPERS
    ======================= */

    /**
     * Seul le propriétaire de la tâche, un assigné ou l'admin peuvent gérer les sous-tâches.
     */
    protected function authorizeManage(Task $task): void
    {
        $user = auth()->user();
        $isAssignee = $task->assignees()->where('users.id', $user->id)->exists();
        abort_unless(
            $user->hasAnyRole(['admin', 'superviseur'])
            || (int) $task->owner_id === $user->id
            || $isAssignee,
            403,
            'Action non autorisée.'
        );
    }
}
