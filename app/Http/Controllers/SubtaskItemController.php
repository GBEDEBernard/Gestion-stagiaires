<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\SubtaskItem;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        $this->authorizeAssignedUser($subtask, $user);

        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            if ($this->wantsJson($request)) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            throw $e;
        }

        $order = $subtask->items()->max('display_order') + 1;

        $item = $subtask->items()->create([
            'title'         => $data['title'],
            'display_order' => $order,
        ]);

        $subtask->task->syncProgressFromSubtasks();

        if ($this->wantsJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Item ajouté.',
                'item'    => [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'is_completed' => false,
                ],
            ]);
        }

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

        $this->authorizeAssignedUser($subtask, $user);

        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            if ($this->wantsJson($request)) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            throw $e;
        }

        $item->update(['title' => $data['title']]);

        if ($this->wantsJson($request)) {
            return response()->json(['success' => true, 'message' => 'Item mis à jour.']);
        }

        return back()->with('success', 'Item mis à jour.');
    }

    /**
     * Supprimer un item (soft delete).
     */
    public function destroy(Request $request, Task $task, Subtask $subtask, SubtaskItem $item)
    {
        $user = $request->user();
        abort_unless((int) $subtask->task_id === $task->id, 404);
        abort_unless((int) $item->subtask_id === $subtask->id, 404);

        $this->authorizeAssignedUser($subtask, $user);

        $item->delete();
        $subtask->task->syncProgressFromSubtasks();

        if ($this->wantsJson($request)) {
            return response()->json(['success' => true, 'message' => 'Item supprimé.']);
        }

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

        $this->authorizeAssignedUser($subtask, $user);

        if ($item->is_completed) {
            $item->markIncomplete();
        } else {
            $item->markComplete($user->id);
        }

        $message = $item->is_completed ? 'Item terminé.' : 'Item réouvert.';

        if ($this->wantsJson($request)) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_completed' => (bool) $item->is_completed,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Indique si la requête attend une réponse JSON (AJAX / fetch).
     */
    protected function wantsJson(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->boolean('ajax');
    }

    /**
     * Un item (niveau 2) est strictement personnel : seul l'utilisateur
     * assigné à la sous-tâche peut le créer, le modifier, le supprimer
     * ou le cocher. L'admin/superviseur/promoteur peut le voir mais pas le gérer.
     */
    protected function authorizeAssignedUser(Subtask $subtask, ?\App\Models\User $user): void
    {
        abort_unless(
            $user && $subtask->isAssignedTo($user->id),
            403,
            'Seul l\'utilisateur assigné à cette sous-tâche peut gérer ses items.'
        );
    }
}
