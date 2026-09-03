<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubtaskController extends Controller
{
    public function __construct(
        protected NotificationService $notifications,
    ) {}

    /**
     * Ajouter une sous-tâche à une tâche existante.
     * Accessible au propriétaire de la tâche et à l'admin.
     */
    public function store(Request $request, Task $task)
    {
        $this->authorizeManage($task);

        $taskStart = $task->start_date;
        $taskEnd   = $task->due_date;

        try {
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
        } catch (ValidationException $e) {
            if ($this->wantsJson($request)) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            throw $e;
        }

        $order = $task->subtasks()->max('display_order') + 1;

        $task->subtasks()->create([
            'title'               => $data['title'],
            'start_date'          => $data['start_date'] ?? null,
            'end_date'            => $data['end_date']   ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
            'display_order'       => $order,
        ]);

        if ($this->wantsJson($request)) {
            return response()->json(['success' => true, 'message' => 'Sous-tâche ajoutée.']);
        }

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

        try {
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
        } catch (ValidationException $e) {
            if ($this->wantsJson($request)) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            throw $e;
        }

        $subtask->update([
            'title'               => $data['title'],
            'start_date'          => $data['start_date'] ?? null,
            'end_date'            => $data['end_date']   ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
        ]);

        if ($this->wantsJson($request)) {
            return response()->json(['success' => true, 'message' => 'Sous-tâche mise à jour.']);
        }

        return back()->with('success', 'Sous-tâche mise à jour.');
    }

    /**
     * Réassigner une sous-tâche à un autre participant de la tâche
     * (ou la remettre "non attribuée" en passant une valeur vide).
     * Réservé à l'admin, au superviseur et au propriétaire de la tâche.
     */
    public function assign(Request $request, Task $task, Subtask $subtask)
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['admin', 'superviseur']) || (int) $task->owner_id === $user->id, 403, 'Action non autorisée.');
        abort_unless((int) $subtask->task_id === $task->id, 404);
        abort_if($subtask->is_completed, 409, 'Une sous-tâche terminée ne peut plus être réassignée.');

        try {
            $data = $request->validate([
                'assigned_to_user_id' => 'nullable|integer|exists:users,id',
            ]);
        } catch (ValidationException $e) {
            if ($this->wantsJson($request)) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            throw $e;
        }

        $targetId = $data['assigned_to_user_id'] ?? null;

        // La cible doit être un participant de la tâche (propriétaire ou assigné).
        if ($targetId !== null && !$task->isParticipant((int) $targetId)) {
            $message = 'Cette personne ne participe pas à la tâche.';
            if ($this->wantsJson($request)) {
                return response()->json(['message' => $message], 422);
            }
            return back()->withErrors(['assigned_to_user_id' => $message])->withInput();
        }

        $previousId = $subtask->assigned_to_user_id;

        $subtask->update([
            'assigned_to_user_id' => $targetId !== null ? (int) $targetId : null,
        ]);

        $task->syncProgressFromSubtasks();

        $taskUrl = encrypted_route('tasks.show', $task);

        // Prévenir l'ancien assigné qu'on lui a retiré la sous-tâche.
        if ($previousId && (int) $previousId !== (int) $targetId) {
            $this->notifications->push(
                (int) $previousId,
                'subtask_unassigned',
                '🧩 Sous-tâche retirée',
                $user->name . ' vous a retiré la sous-tâche « ' . $subtask->title . ' » de la tâche « ' . $task->title . ' ».',
                $taskUrl,
                'clipboard-list',
                'amber'
            );
        }

        // Prévenir le nouveau assigné.
        if ($targetId !== null && (int) $targetId !== (int) $previousId) {
            $this->notifications->push(
                (int) $targetId,
                'subtask_assigned',
                '🧩 Sous-tâche assignée',
                $user->name . ' vous a assigné la sous-tâche « ' . $subtask->title . ' » de la tâche « ' . $task->title . ' ».',
                $taskUrl,
                'clipboard-list',
                'blue'
            );
        }

        if ($this->wantsJson($request)) {
            return response()->json(['success' => true, 'message' => 'Sous-tâche réassignée.']);
        }

        return back()->with('success', 'Sous-tâche réassignée.');
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

    /**
     * Indique si la requête attend une réponse JSON (AJAX / fetch).
     */
    protected function wantsJson(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->boolean('ajax');
    }
}
