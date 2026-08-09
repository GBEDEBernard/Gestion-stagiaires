<?php

namespace App\Services;

use App\Models\AttendanceDay;
use App\Models\DailyReport;
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DailyReportService
{
    public function __construct(
        private UserProfileLinkService $profileLinkService,
        private NotificationService $notificationService,
        private PresenceService $presenceService,
    ) {}

    public function resolveActiveStageForUser(User $user): ?Stage
    {
        $etudiant = $this->profileLinkService->ensureStudentProfile($user);

        if (!$etudiant) return null;

        return $etudiant->stages()
            ->whereDate('date_debut', '<=', today())
            ->whereDate('date_fin', '>=', today())
            ->latest('date_debut')
            ->first();
    }

    public function storeForToday(User $user, array $payload): DailyReport
    {
        return DB::transaction(function () use ($user, $payload) {

            $status = $payload['status_action'] === 'submit'
                ? 'submitted'
                : 'draft';

            $etudiant = $this->profileLinkService->ensureStudentProfile($user);
            $stage = $this->resolveActiveStageForUser($user);

            // Résolution de la tâche rattachée (doit appartenir au producteur
            // ou être assignée à lui via la table pivot, T-008).
            $task = $this->resolveOwnedTask($payload['task_id'] ?? null, $user);

            // Un stage est requis UNIQUEMENT pour un rapport de présence classique
            // (sans tâche rattachée) par un non-employé. Quiconque possède une tâche
            // (admin, superviseur, employé, étudiant) peut rapporter dessus sans stage.
            if (!$stage && !$user->hasRole('employe') && !$task) {
                throw ValidationException::withMessages([
                    'stage' => "Aucun stage actif.",
                ]);
            }

            $attendanceDay = AttendanceDay::whereDate('attendance_date', today())
                ->when($stage, fn($q) => $q->where('stage_id', $stage->id))
                ->when(!$stage, fn($q) => $q->where('user_id', $user->id))
                ->first();

            // 🔒 Vérification de la position + règle de fermeture après pointage.
            [$locationVerified, $locationMeta] = $this->verifyReportSubmission($user, $stage, $task, $payload);

            // 🔥 ANTI-DOUBLON (T-005 / T-008) : par TÂCHE + PRODUCTEUR/jour si rattaché
            // à une tâche (chaque personne assignée a SON rapport du jour sur la
            // tâche partagée), sinon par producteur/jour (rapport de présence
            // legacy, hors tâche).
            $query = DailyReport::whereDate('report_date', today());

            if ($task) {
                $query->where('task_id', $task->id)
                    ->where('user_id', $user->id);
            } elseif ($user->hasRole('employe')) {
                $query->where('user_id', $user->id)->whereNull('task_id');
            } else {
                $query->where('etudiant_id', $etudiant->id)
                    ->where('stage_id', $stage->id)
                    ->whereNull('task_id');
            }

            $report = $query->first();

            if (!$report) {
                $report = new DailyReport();
                $report->report_date = today();
            }

            $report->fill([
                'stage_id' => $stage?->id,
                'etudiant_id' => $etudiant?->id,
                'user_id' => ($user->hasRole('employe') || $task) ? $user->id : null,
                'attendance_day_id' => $attendanceDay?->id,
                'task_id' => $task?->id,
                'task_progress_percent' => $task
                    ? ($payload['task_progress_percent'] ?? $this->latestOwnProgress($task, $user))
                    : null,
                'title' => 'Rapport du ' . today()->format('d/m/Y'),
                'introduction' => $payload['introduction'] ?? null,
                'summary' => $payload['summary'] ?? null,
                'blockers' => $payload['blockers'] ?? null,
                'next_steps' => $payload['next_steps'] ?? null,
                'hours_declared' => $payload['hours_declared'] ?? 0,
                'status' => $status,
                'submitted_at' => $status === 'submitted' ? now() : null,
                // 🔒 Données de localisation de la soumission.
                'latitude' => $payload['latitude'] ?? null,
                'longitude' => $payload['longitude'] ?? null,
                'accuracy_meters' => $payload['accuracy_meters'] ?? null,
                'distance_to_site_meters' => $locationMeta['distance_meters'] ?? null,
                'location_method' => $payload['location_method'] ?? null,
                'location_verified' => $locationVerified,
            ]);

            $report->save();

            // Répercute la progression sur la tâche + fil + notifications.
            if ($task) {
                $this->syncTaskProgress($report->fresh(), $task, $user, $status === 'submitted');
            }

            return $report->load(['reviews', 'task']);
        });
    }

    /**
     * Retourne la tâche si le producteur y participe (propriétaire ou
     * personne assignée via pivot) et qu'elle n'est pas terminée.
     */
    private function resolveOwnedTask($taskId, User $user): ?Task
    {
        if (!$taskId) {
            return null;
        }

        $task = Task::find($taskId);

        if (!$task || !$task->isParticipant($user->id) || $task->status === 'completed') {
            return null;
        }

        return $task;
    }

    /**
     * Dernière progression déclarée par l'utilisateur sur la tâche
     * (pour pré-remplir son rapport quand il ne renseigne rien).
     */
    private function latestOwnProgress(Task $task, User $user): int
    {
        return (int) DailyReport::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->whereNotNull('task_progress_percent')
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->value('task_progress_percent');
    }

    /**
     * Applique la progression déclarée à la tâche, journalise (task_update),
     * gère l'auto-complétion à 100 % et notifie superviseur + admins (seulement si le rapport est soumis).
     */
public function syncTaskProgress(DailyReport $report, Task $task, User $user, bool $notify = true): void
    {
        // T-008 : tâche partagée → la progression affichée est l'AGRÉGAT des
        // dernières progressions déclarées par chaque participant (propriétaire
        // + personnes assignées), moyenne arrondie.
        $progress = (int) $this->aggregateProgress($task);
        $progress = max(0, min(100, $progress));

        $originalStatus = $task->status;

        $newStatus = $task->status;
        if ($originalStatus === 'completed') {
            $newStatus = 'completed';
        } elseif ($progress >= 100) {
            $newStatus = 'awaiting_validation';
        } elseif ($progress > 0 && in_array($originalStatus, ['pending', 'changes_requested', 'awaiting_validation'], true)) {
            $newStatus = 'in_progress';
        }

        $task->update([
            'last_progress_percent' => $progress,
            'status' => $newStatus,
            'started_at' => $task->started_at ?: ($progress > 0 ? now() : null),
        ]);

        // Historique de progression.
        TaskUpdate::create([
            'task_id' => $task->id,
            'daily_report_id' => $report->id,
            'updated_by' => $user->id,
            'status' => $newStatus,
            'progress_percent' => $progress,
            'note' => Str::limit($report->summary, 280),
            'happened_at' => now(),
        ]);

        if ($notify) {
            $this->notifyReviewersOfReport($task, $report, $user);
        }
    }

    /**
     * T-008 — Pourcentage GLOBAL d'une tâche partagée :
     *
     *   global = (BASE + progression de chaque membre de l'équipe) / (n + 1)
     *
     * - BASE   = base_progress_percent : la progression figée AU MOMENT où la
     *   tâche a été assignée à l'équipe (le travail déjà fait avant) ;
     * - chaque MÈMBRE = sa dernière progression déclarée (son dernier rapport) ;
     * - un membre sans rapport compte pour 0 % ;
     * - si la tâche n'a JAMAIS été assignée à une équipe (pas de base), le
     *   global est la moyenne des dernières progression déclarées des membres.
     */
    public function aggregateProgress(Task $task): int
    {
        $participantIds = collect([$task->owner_id])
            ->merge($task->assignees()->pluck('users.id'))
            ->filter()
            ->unique()
            ->values();

        $latest = DailyReport::where('task_id', $task->id)
            ->whereNotNull('task_progress_percent')
            ->whereIn('user_id', $participantIds)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->get(['user_id', 'task_progress_percent'])
            ->unique('user_id')
            ->keyBy('user_id');

        if ($task->base_progress_percent !== null) {
            // Équipe : base figée au moment de l'assignation + chaque membre.
            $sum = (int) $task->base_progress_percent;
            foreach ($participantIds as $id) {
                $sum += (int) ($latest->get((int) $id)?->task_progress_percent ?? 0);
            }

            $count = $participantIds->count() + 1;

            return $count > 0 ? (int) round($sum / $count) : 0;
        }

        // Pas d'équipe (jamais assignée) : moyenne des membres ayant rapporté,
        // sinon on retombe sur la progression actuelle.
        if ($latest->isEmpty()) {
            return (int) $task->last_progress_percent;
        }

        return (int) round($latest->avg('task_progress_percent'));
    }

    /**
     * Notifie le superviseur du stage + les admins qu'un rapport a été soumis sur la tâche.
     */
    private function notifyReviewersOfReport(Task $task, DailyReport $report, User $author): void
    {
        $recipients = collect();

        if ($task->stage && $task->stage->supervisor_id) {
            $recipients->push($task->stage->supervisor_id);
        }

        User::role('admin')->pluck('id')->each(fn($id) => $recipients->push($id));

        $url = encrypted_route('tasks.show', $task);

        $recipients->unique()
            ->reject(fn($id) => (int) $id === (int) $author->id)
            ->each(function ($id) use ($author, $task, $report, $url) {
                $this->notificationService->push(
                    (int) $id,
                    'task_report',
                    '📋 Nouveau rapport',
                    $author->name . ' a rapporté ' . (int) $report->task_progress_percent . '% sur « ' . Str::limit($task->title, 40) . ' »',
                    $url,
                    'clipboard-list',
                    'blue'
                );
            });
    }

    /**
     * Vérifie la soumission d'un rapport selon deux règles (T-006) :
     *
     * 1) POSITION : si l'utilisateur n'est pas en télétravail autorisé, le rapport
     *    doit être soumis depuis le site (distance ≤ MAX_ALLOWED_DISTANCE_METERS).
     * 2) FERMETURE : si le check-out du jour est déjà fait ET que l'utilisateur
     *    n'est pas autorisé au télétravail (flag + tâche active), la soumission
     *    d'un rapport est refusée.
     *
     * @return array{0: bool, 1: array} [locationVerified, meta]
     */
    private function verifyReportSubmission(User $user, ?Stage $stage, ?Task $task, array $payload): array
    {
        // Propriétaire d'une tâche sans stage (admin, superviseur, employé ou
        // étudiant hors stage) : pas de pointage ni de site → aucun contrôle de
        // position n'est applicable, il peut rapporter depuis n'importe où.
        if (!$stage && $task) {
            return [false, [
                'distance_meters' => null,
                'message' => 'Tâche sans stage : position non vérifiée.',
            ]];
        }

        $isRemoteAllowed = $user->canWorkRemotely() && $user->hasAssignedActiveTask();

        // ── Règle 2 : fermeture après pointage de départ ──
        $attendanceDay = AttendanceDay::whereDate('attendance_date', today())
            ->when($stage, fn($q) => $q->where('stage_id', $stage->id))
            ->when(!$stage, fn($q) => $q->where('user_id', $user->id))
            ->first();

        $hasCheckedOut = $attendanceDay && $attendanceDay->last_check_out_at;

        if ($hasCheckedOut && !$isRemoteAllowed) {
            throw ValidationException::withMessages([
                'presence' => 'Votre journée est terminée (pointage de départ effectué). '
                    . 'Seuls les utilisateurs autorisés au télétravail avec une tâche active '
                    . 'peuvent soumettre un rapport après le pointage de départ.',
            ]);
        }

        // ── Règle 1 : vérification de la position (sauf télétravail autorisé) ──
        if ($isRemoteAllowed) {
            return [false, [
                'distance_meters' => null,
                'message' => 'Télétravail autorisé : position non vérifiée.',
            ]];
        }

        $latitude  = $payload['latitude'] ?? null;
        $longitude = $payload['longitude'] ?? null;

        if ($latitude === null || $longitude === null) {
            throw ValidationException::withMessages([
                'location' => 'La position GPS est requise pour soumettre un rapport. '
                    . 'Veuillez activer la géolocalisation et soumettre depuis le site.',
            ]);
        }

        $site = $stage?->site;

        $verification = $this->presenceService->verifyLocationOnSite(
            (float) $latitude,
            (float) $longitude,
            $site
        );

        if (!$verification['verified']) {
            throw ValidationException::withMessages([
                'location' => 'Rapport refusé : ' . $verification['message'],
            ]);
        }

        return [true, [
            'distance_meters' => $verification['distance_meters'],
            'message' => $verification['message'],
        ]];
    }
}
