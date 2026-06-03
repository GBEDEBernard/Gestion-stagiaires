<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Services\UserProfileLinkService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected UserProfileLinkService $profileLink
    ) {}

    /**
     * Liste « Mes tâches » (producteur) — admin/superviseur voient via scopeVisibleTo.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status');

        $tasks = Task::with(['owner', 'stage.etudiant'])
            ->visibleTo($user)
            ->when(
                in_array($status, Task::STATUSES, true),
                fn($q) => $q->where('status', $status)
            )
            ->when($request->filled('q'), fn($q) => $q->where('title', 'like', '%' . $request->get('q') . '%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $base = Task::query()->visibleTo($user);
        $stats = [
            'pending'     => (clone $base)->where('status', 'pending')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'blocked'     => (clone $base)->where('status', 'blocked')->count(),
            'completed'   => (clone $base)->where('status', 'completed')->count(),
        ];

        return view('tasks.index', compact('tasks', 'stats', 'status'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $payload = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority'    => 'required|in:low,normal,high,urgent',
            'due_date'    => 'nullable|date',
        ]);

        [$stageId, $etudiantId] = $this->resolveStudentContext($user);

        $task = Task::create([
            'owner_id'              => $user->id,
            'assigned_by'           => $user->id,
            'stage_id'              => $stageId,
            'etudiant_id'           => $etudiantId,
            'title'                 => $payload['title'],
            'description'           => $payload['description'] ?? null,
            'priority'              => $payload['priority'],
            'status'                => 'pending',
            'due_date'              => $payload['due_date'] ?? null,
            'last_progress_percent' => 0,
        ]);

        Activity::create([
            'user_id'     => $user->id,
            'action'      => 'Creation tache',
            'description' => "Tache {$task->title} creee",
        ]);

        return redirect()
            ->to(encrypted_route('tasks.show', $task))
            ->with('success', 'Tâche créée avec succès.');
    }

    /**
     * Détail tâche = hub (header + progression + rapports liés + fil de discussion).
     */
    public function show(Task $task)
    {
        $user = auth()->user();

        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        $task->load([
            'owner',
            'assignedBy',
            'stage.etudiant',
            'dailyReports',
            'messages.user',
        ]);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorizeOwner($task);

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeOwner($task);

        $payload = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority'    => 'required|in:low,normal,high,urgent',
            'status'      => 'required|in:pending,in_progress,blocked,completed',
            'due_date'    => 'nullable|date',
        ]);

        $status = $payload['status'];

        $task->update([
            'title'                 => $payload['title'],
            'description'           => $payload['description'] ?? null,
            'priority'              => $payload['priority'],
            'status'                => $status,
            'due_date'              => $payload['due_date'] ?? null,
            'started_at'            => in_array($status, ['in_progress', 'blocked'], true)
                ? ($task->started_at ?: now())
                : $task->started_at,
            'completed_at'          => $status === 'completed'
                ? ($task->completed_at ?: now())
                : null,
            'last_progress_percent' => $status === 'completed'
                ? 100
                : ($status === 'pending' ? 0 : $task->last_progress_percent),
        ]);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Mise a jour tache',
            'description' => "Tache {$task->title} modifiee",
        ]);

        return redirect()
            ->to(encrypted_route('tasks.show', $task))
            ->with('success', 'Tâche mise à jour.');
    }

    public function destroy(Task $task)
    {
        $this->authorizeOwner($task);

        $title = $task->title;
        $task->delete();

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'Suppression tache',
            'description' => "Tache {$title} supprimee",
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée.');
    }

    /* =========================================================================
       HELPERS
    ========================================================================= */

    /** Seul le propriétaire (producteur) peut éditer/supprimer sa tâche. */
    protected function authorizeOwner(Task $task): void
    {
        abort_unless($task->owner_id === auth()->id(), 403);
    }

    /**
     * Résout le contexte étudiant (stage actif + etudiant) pour rattacher la tâche.
     * Les employés n'ont ni stage ni etudiant → [null, null].
     *
     * @return array{0: int|null, 1: int|null}
     */
    protected function resolveStudentContext($user): array
    {
        if (!$user->hasRole('etudiant')) {
            return [null, null];
        }

        $etudiant = $this->profileLink->ensureStudentProfile($user) ?? $user->etudiant;

        if (!$etudiant) {
            return [null, null];
        }

        $stage = $etudiant->stages()
            ->whereDate('date_debut', '<=', today())
            ->whereDate('date_fin', '>=', today())
            ->latest('date_debut')
            ->first();

        return [$stage?->id, $etudiant->id];
    }
}
