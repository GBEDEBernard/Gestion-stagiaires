<?php

namespace App\Services;

use App\Models\AttendanceDay;
use App\Models\DailyReport;
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DailyReportService
{
    public function resolveActiveStageForUser(User $user): ?Stage
    {
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            return null;
        }

        return $etudiant->stages()
            ->with(['site', 'supervisor', 'tasks' => function ($query) {
                $query->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                    ->orderBy('due_date')
                    ->orderBy('title');
            }])
            ->whereDate('date_debut', '<=', today())
            ->whereDate('date_fin', '>=', today())
            ->latest('date_debut')
            ->first();
    }

    public function storeForToday(User $user, array $payload): DailyReport
    {
        return DB::transaction(function () use ($user, $payload) {
            if ($user->hasRole('employe')) {
                return $this->storeForEmployeeToday($user, $payload);
            }

            // Original student logic
            $etudiant = $this->resolveEtudiant($user);
            $stage = $this->resolveActiveStage($user, $etudiant);
            $attendanceDay = AttendanceDay::where('stage_id', $stage->id)
                ->whereDate('attendance_date', today())
                ->first();

            $report = DailyReport::withTrashed()->firstOrNew([
                'stage_id' => $stage->id,
                'report_date' => today()->toDateString(),
            ]);

            if ($report->trashed()) {
                $report->restore();
            }

            $status = $payload['status_action'] === 'submit' ? 'submitted' : 'draft';

            $report->fill([
                'etudiant_id' => $etudiant->id,
                'attendance_day_id' => $attendanceDay?->id,
                'title' => sprintf('Rapport du %s', today()->format('d/m/Y')),
                'summary' => trim($payload['summary']),
                'blockers' => $this->nullableText($payload['blockers'] ?? null),
                'next_steps' => $this->nullableText($payload['next_steps'] ?? null),
                'hours_declared' => $payload['hours_declared'] ?? 0,
                'completion_rate' => $payload['completion_rate'] ?? $this->computeCompletionRate($payload['items'] ?? []),
                'status' => $status,
                'submitted_at' => $status === 'submitted' ? now() : null,
            ]);
            $report->save();

            $this->syncReportItems($report, $stage, $user, $payload['items'] ?? []);

            return $report->fresh(['items.task', 'attendanceDay', 'stage.tasks']);
        });
    }

    protected function storeForEmployeeToday(User $user, array $payload): DailyReport
    {
        $attendanceDay = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        $report = DailyReport::withTrashed()->firstOrNew([
            'user_id' => $user->id,
            'report_date' => today()->toDateString(),
        ]);

        if ($report->trashed()) {
            $report->restore();
        }

        $status = $payload['status_action'] === 'submit' ? 'submitted' : 'draft';

        $report->fill([
            'user_id' => $user->id,
            'attendance_day_id' => $attendanceDay?->id,
            'title' => sprintf('Rapport du %s', today()->format('d/m/Y')),
            'summary' => trim($payload['summary']),
            'blockers' => $this->nullableText($payload['blockers'] ?? null),
            'next_steps' => $this->nullableText($payload['next_steps'] ?? null),
            'hours_declared' => $payload['hours_declared'] ?? 0,
            'completion_rate' => $payload['completion_rate'] ?? null,
            'status' => $status,
            'submitted_at' => $status === 'submitted' ? now() : null,
        ]);
        $report->save();

        // For employees, only sync free-form items (no tasks)
        $this->syncFreeReportItems($report, $user, $payload['items'] ?? []);

        return $report->fresh(['items', 'attendanceDay']);
    }

    protected function syncFreeReportItems(DailyReport $report, User $user, array $items): void
    {
        $report->items()->delete();

        foreach ($items as $index => $item) {
            if (!$this->isMeaningfulItem($item)) {
                continue;
            }

            $description = $this->nullableText(Arr::get($item, 'description'));
            $outcome = $this->nullableText(Arr::get($item, 'outcome'));
            $duration = (int) (Arr::get($item, 'duration_minutes') ?? 0);

            $report->items()->create([
                'task_id' => null,
                'work_type' => 'free_entry',
                'description' => $description ?: 'Activite du jour',
                'outcome' => $outcome,
                'duration_minutes' => $duration,
                'progress_percent' => null,
                'display_order' => $index + 1,
            ]);
        }
    }

    protected function resolveEtudiant(User $user): Etudiant
    {
        if ($user->etudiant) {
            return $user->etudiant;
        }

        throw ValidationException::withMessages([
            'report' => "Votre compte n'est pas encore rattache a une fiche etudiant.",
        ]);
    }

    protected function resolveActiveStage(User $user, Etudiant $etudiant): Stage
    {
        $stage = $this->resolveActiveStageForUser($user);

        if (!$stage || (int) $stage->etudiant_id !== (int) $etudiant->id) {
            throw ValidationException::withMessages([
                'report' => "Aucun stage actif n'est disponible pour le rapport du jour.",
            ]);
        }

        return $stage;
    }

    protected function syncReportItems(DailyReport $report, ?Stage $stage, User $user, array $items): void
    {
        $report->items()->delete();

        foreach ($items as $index => $item) {
            if (!$this->isMeaningfulItem($item)) {
                continue;
            }

            $task = $stage ? $this->resolveTask($stage, Arr::get($item, 'task_id')) : null;
            $description = $this->nullableText(Arr::get($item, 'description'));
            $outcome = $this->nullableText(Arr::get($item, 'outcome'));
            $duration = (int) (Arr::get($item, 'duration_minutes') ?? 0);
            $progress = Arr::get($item, 'progress_percent');

            $reportItem = $report->items()->create([
                'task_id' => $task?->id,
                'work_type' => Arr::get($item, 'work_type') ?: ($task ? 'task_update' : 'free_entry'),
                'description' => $description ?: ($task ? "Point d'avancement sur {$task->title}" : 'Activite du jour'),
                'outcome' => $outcome,
                'duration_minutes' => $duration,
                'progress_percent' => $progress,
                'display_order' => $index + 1,
            ]);

            if ($task) {
                $this->syncTaskProgress($task, $report, $user, $reportItem->progress_percent, $description, $outcome);
            }
        }
    }

    protected function resolveTask(?Stage $stage, mixed $taskId): ?Task
    {
        if (!$taskId || !$stage) {
            return null;
        }

        $task = $stage->tasks()->find($taskId);

        if (!$task) {
            throw ValidationException::withMessages([
                'items' => "Une tache du rapport n'appartient pas au stage actif.",
            ]);
        }

        return $task;
    }

    protected function syncTaskProgress(
        Task $task,
        DailyReport $report,
        User $user,
        ?int $progressPercent,
        ?string $description,
        ?string $outcome
    ): void {
        $hasNarrative = !empty($description) || !empty($outcome);
        $progress = $progressPercent ?? $task->last_progress_percent;

        if (!$hasNarrative && $progressPercent === null) {
            return;
        }

        if ($progress >= 100) {
            $task->status = 'completed';
            $task->completed_at = $task->completed_at ?: now();
        } elseif ($progress > 0 || $hasNarrative) {
            $task->status = 'in_progress';
            $task->started_at = $task->started_at ?: now();
            $task->completed_at = null;
        }

        $task->last_progress_percent = $progress;
        $task->save();

        TaskUpdate::create([
            'task_id' => $task->id,
            'daily_report_id' => $report->id,
            'updated_by' => $user->id,
            'status' => $task->status,
            'progress_percent' => $progress,
            'note' => $outcome ?: $description,
            'happened_at' => now(),
        ]);
    }

    protected function isMeaningfulItem(array $item): bool
    {
        return !empty(Arr::get($item, 'task_id'))
            || !empty(trim((string) Arr::get($item, 'description')))
            || !empty(trim((string) Arr::get($item, 'outcome')))
            || (int) (Arr::get($item, 'duration_minutes') ?? 0) > 0
            || Arr::get($item, 'progress_percent') !== null;
    }

    protected function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function computeCompletionRate(array $items): ?int
    {
        $progressValues = collect($items)
            ->pluck('progress_percent')
            ->filter(static fn($value) => $value !== null && $value !== '')
            ->map(static fn($value) => (int) $value)
            ->values();

        if ($progressValues->isEmpty()) {
            return null;
        }

        return (int) round($progressValues->avg());
    }
}
