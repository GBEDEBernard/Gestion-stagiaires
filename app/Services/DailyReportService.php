<?php

namespace App\Services;

use App\Models\AttendanceDay;
use App\Models\DailyReport;
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\Task;
use App\Models\TaskMessage;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DailyReportService
{
    public function __construct(
        private UserProfileLinkService $profileLinkService,
        private NotificationService $notificationService
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

            if (!$stage && !$user->hasRole('employe')) {
                throw ValidationException::withMessages([
                    'stage' => "Aucun stage actif.",
                ]);
            }

            $attendanceDay = AttendanceDay::whereDate('attendance_date', today())
                ->when($stage, fn($q) => $q->where('stage_id', $stage->id))
                ->when(!$stage, fn($q) => $q->where('user_id', $user->id))
                ->first();

            // 🔥 FIX ANTI DOUBLON PROPRE
            $query = DailyReport::whereDate('report_date', today());

            if ($user->hasRole('employe')) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('etudiant_id', $etudiant->id)
                    ->where('stage_id', $stage->id);
            }

            $report = $query->first();

            if (!$report) {
                $report = new DailyReport();
                $report->report_date = today();
            }

            // Résolution de la tâche rattachée (doit appartenir au producteur).
            $task = $this->resolveOwnedTask($payload['task_id'] ?? null, $user);

            $report->fill([
                'stage_id' => $stage?->id,
                'etudiant_id' => $etudiant?->id,
                'user_id' => $user->hasRole('employe') ? $user->id : null,
                'attendance_day_id' => $attendanceDay?->id,
                'task_id' => $task?->id,
                'task_progress_percent' => $task ? ($payload['task_progress_percent'] ?? $task->last_progress_percent) : null,
                'title' => 'Rapport du ' . today()->format('d/m/Y'),
                'summary' => $payload['summary'],
                'blockers' => $payload['blockers'] ?? null,
                'next_steps' => $payload['next_steps'] ?? null,
                'hours_declared' => $payload['hours_declared'] ?? 0,
                'status' => $status,
                'submitted_at' => $status === 'submitted' ? now() : null,
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
     * Retourne la tâche si elle appartient au producteur et n'est pas terminée.
     */
    private function resolveOwnedTask($taskId, User $user): ?Task
    {
        if (!$taskId) {
            return null;
        }

        $task = Task::find($taskId);

        if (!$task || $task->owner_id !== $user->id || $task->status === 'completed') {
            return null;
        }

        return $task;
    }

    /**
     * Applique la progression déclarée à la tâche, journalise (task_update),
     * insère un jalon dans le fil de discussion, gère l'auto-complétion à 100 %
     * et notifie superviseur + admins (seulement si le rapport est soumis).
     */
    public function syncTaskProgress(DailyReport $report, Task $task, User $user, bool $notify = true): void
    {
        $progress = (int) ($report->task_progress_percent ?? $task->last_progress_percent);
        $progress = max(0, min(100, $progress));

        $wasCompleted = $task->status === 'completed';

        // Transition de statut basée sur la progression.
        $newStatus = $task->status;
        if ($progress >= 100) {
            $newStatus = 'completed';
        } elseif ($progress > 0 && in_array($task->status, ['pending', 'changes_requested'], true)) {
            $newStatus = 'in_progress';
        }

        $task->update([
            'last_progress_percent' => $progress,
            'status' => $newStatus,
            'started_at' => $task->started_at ?: ($progress > 0 ? now() : null),
            'completed_at' => $progress >= 100 ? ($task->completed_at ?: now()) : null,
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

        // Jalon dans le fil de la tâche.
        TaskMessage::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'type' => 'report_jalon',
            'daily_report_id' => $report->id,
            'body' => 'Rapport du ' . $report->report_date->format('d/m/Y') . ' — progression ' . $progress . '%',
        ]);

        // Changement de statut « terminée ».
        if ($progress >= 100 && !$wasCompleted) {
            TaskMessage::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'type' => 'status_change',
                'body' => 'Tâche marquée comme terminée (100%).',
            ]);
        }

        if ($notify) {
            $this->notifyReviewersOfReport($task, $report, $user);
        }
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
}
