<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Services\NotificationService;
use App\Services\UserProfileLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function __construct(
        protected UserProfileLinkService $profileLink,
        protected NotificationService $notifications
    ) {}

    /**
     * Liste « Mes tâches » (producteur) — admin/superviseur voient via scopeVisibleTo.
     */
    /**
     * Espace de travail (master-détail). Sans tâche sélectionnée = colonne droite vide.
     */
    public function index(Request $request)
    {
        return view('tasks.workspace', $this->workspaceData($request, null));
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
    public function show(Request $request, Task $task)
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
            'dailyReports.reviews.reviewer',
            'dailyReports.user',
            'dailyReports.etudiant.user',
        ]);

        return view('tasks.workspace', $this->workspaceData($request, $task));
    }

    /**
     * Données communes de l'espace de travail : liste (gauche) + tâche sélectionnée (droite).
     *
     * @return array<string, mixed>
     */
    protected function workspaceData(Request $request, ?Task $selected): array
    {
        $user = auth()->user();
        $status = $request->get('status');
        $q = $request->get('q');

        $tasks = Task::with(['owner'])
            ->visibleTo($user)
            ->when(in_array($status, Task::STATUSES, true), fn($qb) => $qb->where('status', $status))
            ->when($q, fn($qb) => $qb->where('title', 'like', "%{$q}%"))
            ->latest('updated_at')
            ->limit(100)
            ->get();

        $base = Task::query()->visibleTo($user);
        $stats = [
            'pending'     => (clone $base)->where('status', 'pending')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'blocked'     => (clone $base)->where('status', 'blocked')->count(),
            'completed'   => (clone $base)->where('status', 'completed')->count(),
        ];

        // Rapport déjà soumis aujourd'hui pour la tâche sélectionnée (par le propriétaire).
        $todayReport = null;
        if ($selected) {
            if ($selected->owner_id === $user->id) {
                $todayReport = $selected->dailyReports
                    ->first(fn($r) => $r->report_date->isToday());
            }
        }

        return [
            'tasks'       => $tasks,
            'stats'       => $stats,
            'status'      => $status,
            'selected'    => $selected,
            'todayReport' => $todayReport,
        ];
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

    /**
     * Action de revue (superviseur / admin) : demander des corrections ou valider.
     */
    public function review(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless($user->hasAnyRole(['admin', 'superviseur']), 403);
        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        $data = $request->validate([
            'action'  => 'required|in:request_changes,approve',
            'comment' => 'nullable|string|max:5000',
        ]);

        if ($data['action'] === 'request_changes') {
            if (!$task->isCompleted()) {
                $task->update(['status' => 'changes_requested']);
            }

            Activity::create([
                'user_id'     => $user->id,
                'action'      => 'Corrections demandées',
                'description' => 'Corrections demandées par ' . $user->name
                    . ' sur « ' . Str::limit($task->title, 40) . ' »'
                    . (!empty($data['comment']) ? ' : ' . $data['comment'] : ''),
            ]);

            $title = '✏️ Corrections demandées';
            $message = $user->name . ' demande des corrections sur « ' . Str::limit($task->title, 40) . ' »';
        } else {
            Activity::create([
                'user_id'     => $user->id,
                'action'      => 'Travail validé',
                'description' => $user->name . ' a validé « ' . Str::limit($task->title, 40) . ' »'
                    . (!empty($data['comment']) ? ' : ' . $data['comment'] : ''),
            ]);

            $title = '✅ Travail validé';
            $message = $user->name . ' a validé « ' . Str::limit($task->title, 40) . ' »';
        }

        if ($task->owner_id && (int) $task->owner_id !== (int) $user->id) {
            $this->notifications->push(
                (int) $task->owner_id,
                'task_review',
                $title,
                $message,
                encrypted_route('tasks.show', $task),
                'clipboard-check',
                $data['action'] === 'request_changes' ? 'amber' : 'green'
            );
        }

        return back()->with('success', 'Action enregistrée.');
    }

    /**
     * Clôture de la tâche — ADMIN UNIQUEMENT (T-005).
     * L'admin déclare la tâche terminée quand il est satisfait des rapports.
     * → status=completed, discussion fermée (lecture seule).
     */
    public function complete(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless($user->hasRole('admin'), 403);
        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        $data = $request->validate([
            'comment' => 'nullable|string|max:5000',
        ]);

        if (!$task->isCompleted()) {
            $task->update([
                'status'                 => 'completed',
                'last_progress_percent'  => 100,
                'completed_at'           => $task->completed_at ?: now(),
                'validated_by'           => $user->id,
                'validated_at'           => now(),
                'discussion_reopened_at' => null,
            ]);

            Activity::create([
                'user_id'     => $user->id,
                'action'      => 'Tâche clôturée',
                'description' => '✅ Tâche « ' . Str::limit($task->title, 40) . ' » clôturée par ' . $user->name
                    . (!empty($data['comment']) ? ' — ' . $data['comment'] : ''),
            ]);

            if ($task->owner_id && (int) $task->owner_id !== (int) $user->id) {
                $this->notifications->push(
                    (int) $task->owner_id,
                    'task_completed',
                    '✅ Tâche validée',
                    $user->name . ' a clôturé « ' . Str::limit($task->title, 40) . ' »',
                    encrypted_route('tasks.show', $task),
                    'check-circle',
                    'green'
                );
            }
        }

        return back()->with('success', 'Tâche clôturée.');
    }

    /**
     * Réouverture de la discussion — ADMIN UNIQUEMENT (T-005).
     * La tâche reprend (awaiting_validation si déjà à 100 %, sinon in_progress).
     */
    public function reopen(Request $request, Task $task)
    {
        $user = auth()->user();

        abort_unless($user->hasRole('admin'), 403);
        abort_unless(
            Task::whereKey($task->getKey())->visibleTo($user)->exists(),
            403
        );

        if ($task->isCompleted()) {
            $task->update([
                'status'                 => $task->last_progress_percent >= 100 ? 'awaiting_validation' : 'in_progress',
                'completed_at'           => null,
                'validated_by'           => null,
                'validated_at'           => null,
                'discussion_reopened_at' => now(),
            ]);

            Activity::create([
                'user_id'     => $user->id,
                'action'      => 'Tâche rouverte',
                'description' => '🔓 Tâche « ' . Str::limit($task->title, 40) . ' » rouverte par ' . $user->name . '.',
            ]);

            if ($task->owner_id && (int) $task->owner_id !== (int) $user->id) {
                $this->notifications->push(
                    (int) $task->owner_id,
                    'task_reopened',
                    '🔓 Tâche rouverte',
                    $user->name . ' a rouvert « ' . Str::limit($task->title, 40) . ' »',
                    encrypted_route('tasks.show', $task),
                    'lock-open',
                    'amber'
                );
            }
        }

        return back()->with('success', 'Discussion rouverte.');
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
