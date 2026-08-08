<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TaskThreadService;
use App\Services\UserProfileLinkService;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function __construct(
        protected UserProfileLinkService $profileLink,
        protected NotificationService $notifications,
        protected EmailNotificationService $emailService,
        protected TaskThreadService $threadService
    ) {}

public function index(Request $request)
    {
        return view('tasks.workspace', $this->workspaceData($request, null));
    }

    /**
     * Formulaire d'assignation d'une tâche (T-008) — ADMIN UNIQUEMENT.
     * La même tâche peut être assignée à plusieurs personnes : une seule
     * occurrence dans le workspace, chaque personne travaille dessus et
     * dépose ses propres rapports.
     */
    public function assignForm(Request $request)
    {
        $user = auth()->user();

        $this->authorizeAssign($user);

        // Producteurs disponibles : étudiants + employés ayant un compte actif.
        $producers = User::role(['etudiant', 'employe'])
            ->with('personnel')
            ->where('status', 'actif')
            ->orderBy('name')
            ->get()
            ->filter(fn($u) => $u->profil() !== null)
            ->values();

        // Toutes les tâches en cours (sources assignables).
        $tasks = Task::with('owner', 'assignees')
            ->where('status', '!=', 'completed')
            ->latest('updated_at')
            ->get(['id', 'title', 'status', 'priority', 'due_date', 'last_progress_percent', 'owner_id']);

        // Destinataires actuels par tâche (propriétaire + assignés pivot).
        $taskHolders = $tasks->mapWithKeys(function ($task) {
            $holders = collect([$task->owner_id])->filter()
                ->merge($task->assignees->pluck('id'))
                ->map(fn($k) => (int) $k)
                ->unique()
                ->values()
                ->all();

            return [$task->id => $holders];
        });

        return view('tasks.assign', compact('producers', 'tasks', 'taskHolders'));
    }

    /**
     * Assignation / transfert d'une tâche (T-008) — ADMIN UNIQUEMENT.
     *
     * Mode "assign" (multi-personnes) :
     *   - task_id + owner_ids[] → chaque personne est ajoutée comme ASSIGNÉE
     *     (table pivot task_assignee). Une seule tâche reste dans le workspace ;
     *   - chacun dépose ses propres rapports sur la tâche et discute dans le
     *     même fil ; la progression affichée est agrégée des rapports de chacun ;
     *   - notifications à chaque nouveau destinataire.
     *
     * Mode "transfer" (réassignation) :
     *   - déplace une tâche existante d'une personne à une autre (contexte
     *     stage/étudiant re-résolu, progression/statut conservés).
     */
    public function assign(Request $request)
    {
        $user = auth()->user();

        $this->authorizeAssign($user);

        $mode = $request->input('mode', 'assign');
        if (!in_array($mode, ['assign', 'transfer'], true)) {
            abort(422, 'Mode d\'assignation invalide.');
        }

        $owner = $task = null;

        if ($mode === 'transfer') {
            $payload = $request->validate([
                'owner_id' => 'required|integer|exists:users,id',
                'task_id'  => 'required|integer|exists:tasks,id',
            ], [
                'owner_id.required' => 'Veuillez sélectionner le nouveau propriétaire.',
                'owner_id.exists'   => 'Le nouveau propriétaire est introuvable.',
                'task_id.required'  => 'Veuillez sélectionner une tâche à transférer.',
                'task_id.exists'    => 'La tâche sélectionnée est introuvable.',
            ]);

            $owner = User::findOrFail($payload['owner_id']);
            $task = Task::findOrFail($payload['task_id']);

            // La tâche doit être visible (admin : tout ; superviseur : stage supervisé).
            abort_unless(Task::whereKey($task->getKey())->visibleTo($user)->exists(), 403);

            if ($task->isCompleted()) {
                abort(403, 'Impossible de transférer une tâche déjà terminée.');
            }

            $isTransfer = (int) $task->owner_id !== (int) $owner->id;
            $previousOwnerId = (int) $task->owner_id;

            if ($isTransfer || (int) $task->assigned_by !== (int) $user->id) {
                // Transfert : l'ancien propriétaire quitte la tâche, la cible devient
                // propriétaire et reste assignée.
                $task->assignees()->detach($previousOwnerId);

                [$stageId, $etudiantId] = $this->resolveStudentContext($owner);

                $task->update([
                    'owner_id'    => $owner->id,
                    'assigned_by' => $user->id,
                    'stage_id'    => $stageId,
                    'etudiant_id' => $etudiantId,
                ]);

                $task->assignees()->syncWithoutDetaching([$owner->id]);

                Activity::create([
                    'user_id'     => $user->id,
                    'action'      => $isTransfer ? 'Transfert tache' : 'Assignment tache',
                    'description' => $isTransfer
                        ? "Tache « {$task->title} » transferee a {$owner->name}"
                        : "Tache « {$task->title} » assignee a {$owner->name}",
                ]);

                $url = encrypted_route('tasks.show', $task);

                // Notification au nouveau propriétaire.
                $this->notifications->push(
                    $owner->id,
                    'task_assigned',
                    '📋 Nouvelle tâche assignée',
                    $user->name . ' vous a ' . ($isTransfer ? 'transféré' : 'désigné') . ' « ' . Str::limit($task->title, 40) . ' » pour le travail à domicile',
                    $url,
                    'clipboard-list',
                    'blue'
                );

                $this->emailService->notifyTaskCreated($task);

                // Notification à l'ancien propriétaire (s'il existe et diffère).
                if ($isTransfer && $previousOwnerId) {
                    $this->notifications->push(
                        $previousOwnerId,
                        'task_unassigned',
                        '↔️ Tâche réassignée',
                        'La tâche « ' . Str::limit($task->title, 40) . ' » ne vous est plus attribuée',
                        $url,
                        'arrows-right-left',
                        'amber'
                    );
                }
            }

            return redirect()
                ->to(encrypted_route('tasks.show', $task))
                ->with('success', 'Tâche « ' . $task->title . ' » ' . ($isTransfer ? 'transférée à ' : 'désignée à ') . $owner->name . '.');
        }

        // ── Mode "assign" : une tâche → plusieurs personnes (pivot) ──
        $payload = $request->validate([
            'task_id'    => 'required|integer|exists:tasks,id',
            'owner_ids'  => 'required|array|min:1',
            'owner_ids.*' => 'required|integer|exists:users,id',
        ], [
            'task_id.required'   => 'Veuillez sélectionner une tâche à assigner.',
            'task_id.exists'     => 'La tâche sélectionnée est introuvable.',
            'owner_ids.required' => 'Sélectionnez au moins une personne.',
            'owner_ids.min'      => 'Sélectionnez au moins une personne.',
            'owner_ids.*.exists' => 'Une des personnes sélectionnées est introuvable.',
        ]);

        $task = Task::findOrFail($payload['task_id']);

        if ($task->isCompleted()) {
            abort(403, 'Impossible d\'assigner une tâche déjà terminée.');
        }

        $targets = User::whereIn('id', array_values(array_unique($payload['owner_ids'])))
            ->get();

        // Seuls les producteurs (étudiants/employés) ayant un profil sont reçus.
        $targets = $targets->filter(fn($u) => $u->profil() !== null)
            ->reject(fn($u) => $u->hasRole('admin'));

        // T-008 : au moment où la tâche passe d'un propriétaire isolé à une
        // ÉQUIPE, on fige sa progression courante dans base_progress_percent.
        // Ce % « d'avant » entre ensuite dans le calcul du global :
        //   global = (base + progression de chaque membre) / (n + 1)
        if ($task->assignees()->count() === 0 && is_null($task->base_progress_percent)) {
            $task->update(['base_progress_percent' => max(0, min(100, (int) $task->last_progress_percent))]);
        }

        $created = [];
        $skipped = [];

        foreach ($targets as $target) {
            if ($task->alreadyReceivedBy($target->id)) {
                $skipped[] = $target->name;
                continue;
            }

            $task->assignees()->attach($target->id, ['assigned_at' => now()]);
            $created[] = $target->name;

            Activity::create([
                'user_id'     => $user->id,
                'action'      => 'Assignment tache',
                'description' => "Tache « {$task->title} » assignee a {$target->name}",
            ]);

            $this->notifications->push(
                $target->id,
                'task_assigned',
                '📋 Nouvelle tâche assignée',
                $user->name . ' vous a assigné « ' . Str::limit($task->title, 40) . ' » pour le travail à domicile',
                encrypted_route('tasks.show', $task),
                'clipboard-list',
                'blue'
            );

            $this->emailService->notifyTaskCreated($task);
        }

        $msg = '';
        if (!empty($created)) {
            $msg .= 'Tâche « ' . $task->title . ' » assignée à : ' . implode(', ', $created) . '.';
        }
        if (!empty($skipped)) {
            $msg .= ($msg ? ' ' : '') . count($skipped) . ' personne(s) possède(nt) déjà cette tâche.';
        }

        return redirect()
            ->to(encrypted_route('tasks.show', $task))
            ->with('success', $msg ?: 'Aucune nouvelle assignation.');
    }

    /**
     * L'assignation de tâches est réservée à l'ADMIN (T-007).
     */
    protected function authorizeAssign(User $user): void
    {
        abort_unless($user->hasRole('admin'), 403);
        abort_unless($user->can('tasks.assign'), 403);
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

        // Notification email + DB
        $this->emailService->notifyTaskCreated($task);

        $url = encrypted_route('tasks.show', $task);
        $recipients = collect();

        if ($task->stage && $task->stage->supervisor_id) {
            $recipients->push($task->stage->supervisor_id);
        } else {
            $supervisor = $task->owner?->profil()?->supervisor;
            if ($supervisor) {
                $recipients->push($supervisor->id);
            }
        }

        User::role('admin')->pluck('id')->each(fn($id) => $recipients->push($id));

        $recipients->unique()
            ->reject(fn($id) => (int) $id === (int) $user->id)
            ->each(fn($id) => $this->notifications->push(
                (int) $id,
                'task_created',
                '📋 Nouvelle tâche',
                $user->name . ' a créé « ' . Str::limit($task->title, 40) . ' »',
                $url,
                'clipboard-list',
                'blue'
            ));

        return redirect()
            ->to(encrypted_route('tasks.show', $task))
            ->with('success', 'Tâche créée avec succès.');
    }

   public function show(Request $request, Task $task)
{
    $user = auth()->user();

    // Si l'utilisateur connecté ne peut pas voir cette tâche,
    // on le déconnecte et redirige vers login avec un message
    if (!Task::whereKey($task->getKey())->visibleTo($user)->exists()) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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

    $task->load([
        'owner',
        'assignedBy',
        'stage.etudiant',
        'dailyReports',
        'messages.user',
    ]);

    return view('tasks.workspace', $this->workspaceData($request, $task));
}

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

        $todayReport = null;
        if ($selected) {
            // T-008 : chaque participant voit SON rapport du jour sur la tâche
            // partagée (le rapport est rattaché à user_id).
            $todayReport = $selected->dailyReports
                ->first(fn($r) => $r->user_id === $user->id && $r->report_date->isToday());
        }

        // T-008 — Groupe d'assignation : propriétaire + personnes assignées
        // via la table pivot.
        $group = collect();
        if ($selected) {
            $group = collect([$selected->owner])
                ->filter()
                ->merge($selected->assignees)
                ->unique('id')
                ->sortBy(fn($u) => $u->name)
                ->values();
        }

        // T-009 — Discussion GLOBALE unique de la tâche (équipe + admin).
        // Charge utile du fil + URLs du thread pour le widget Alpine.
        $chat = null;
        if ($selected) {
            $chat = [
                'thread' => $this->threadService->payload($selected, $user),
                'cfg' => [
                    'threadUrl' => route('tasks.thread', $selected),
                    'storeUrl'  => route('tasks.messages.store', $selected),
                    'readUrl'   => route('tasks.read', $selected),
                ],
            ];
        }

        return compact('tasks', 'stats', 'status', 'selected', 'todayReport', 'group', 'chat');
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

        $this->notifyParticipants(
            $task,
            'task_review',
            $title,
            $message,
            'clipboard-check',
            $data['action'] === 'request_changes' ? 'amber' : 'green'
        );

        // Notification email
        if ($task->owner && (int) $task->owner->id !== (int) $user->id) {
            $this->emailService->notifyTaskReviewed($task, $user, $data['action'], $data['comment'] ?? null);
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

            $this->notifyParticipants(
                $task,
                'task_completed',
                '✅ Tâche validée',
                $user->name . ' a clôturé « ' . Str::limit($task->title, 40) . ' »',
                'check-circle',
                'green'
            );
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

            $this->notifyParticipants(
                $task,
                'task_reopened',
                '🔓 Tâche rouverte',
                $user->name . ' a rouvert « ' . Str::limit($task->title, 40) . ' »',
                'lock-open',
                'amber'
            );
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
     * T-008 — Notifie TOUS les participants de la tâche (propriétaire +
     * assignés pivot), sauf l'auteur de l'action.
     */
    protected function notifyParticipants(
        Task $task,
        string $type,
        string $title,
        string $message,
        string $icon,
        string $color
    ): void {
        $recipients = collect([$task->owner_id])
            ->merge($task->assignees->pluck('id'))
            ->filter()
            ->unique()
            ->reject(fn($id) => (int) $id === (int) auth()->id());

        $url = encrypted_route('tasks.show', $task);

        $recipients->each(fn($id) => $this->notifications->push(
            (int) $id,
            $type,
            $title,
            $message,
            $url,
            $icon,
            $color
        ));
    }

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