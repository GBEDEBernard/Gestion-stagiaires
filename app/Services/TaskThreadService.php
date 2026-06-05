<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\Task;
use App\Models\TaskMessage;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Construit la charge utile du fil de discussion d'une tâche (T-005).
 * Source unique réutilisée par l'endpoint thread.json, la réponse AJAX de
 * publication, et (Phase 3) les événements de broadcast temps réel.
 */
class TaskThreadService
{
    /**
     * Charge utile complète du fil pour un utilisateur donné.
     *
     * @return array<string, mixed>
     */
    public function payload(Task $task, User $viewer): array
    {
        $task->loadMissing(['owner', 'stage', 'dailyReports']);

        $messages = $task->messages()
            ->with(['user', 'parent.user', 'dailyReport', 'reactions'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $myRead = $task->reads()->where('user_id', $viewer->id)->first();

        return [
            'task' => [
                'id'                => $task->id,
                'status'            => $task->status,
                'discussion_state'  => $task->discussionState(),
                'progress'          => (int) $task->last_progress_percent,
                'last_message_id'   => $messages->last()?->id,
            ],
            'pinned_report' => $this->pinnedReport($task),
            'recipients'    => $this->recipients($task),
            'messages'      => $messages->map(fn(TaskMessage $m) => $this->serializeMessage($m, $task, $viewer))->values()->all(),
            'me' => [
                'id'                   => $viewer->id,
                'name'                 => $viewer->name,
                'initials'             => $this->initials($viewer->name),
                'last_read_message_id' => $myRead?->last_read_message_id,
            ],
        ];
    }

    /**
     * Sérialise un message pour le front (et le broadcast).
     *
     * @return array<string, mixed>
     */
    public function serializeMessage(TaskMessage $m, Task $task, ?User $viewer = null): array
    {
        $authorName = $m->user?->name ?? 'Système';

        return [
            'id'              => $m->id,
            'type'            => $m->type,
            'is_system'       => $m->isSystem(),
            'body'            => $m->body,
            'mine'            => $viewer && $m->user_id === $viewer->id,
            'is_owner'        => $m->user_id && $m->user_id === $task->owner_id,
            'user' => [
                'id'       => $m->user_id,
                'name'     => $authorName,
                'initials' => $this->initials($authorName),
            ],
            'parent' => $m->parent ? [
                'id'        => $m->parent->id,
                'user_name' => $m->parent->user?->name ?? 'Système',
                'excerpt'   => $this->excerpt($m->parent),
                'is_voice'  => $m->parent->isVoice(),
            ] : null,
            'attachment' => $m->hasAttachment() ? [
                'type'     => $m->attachment_type,
                'url'      => $m->attachmentUrl(),
                'name'     => $m->attachment_name,
                'duration' => $m->attachment_duration,
                'size'     => $m->attachment_size,
            ] : null,
            'reactions'       => $this->reactions($m, $viewer),
            'daily_report_id' => $m->daily_report_id,
            'edited'          => !is_null($m->edited_at),
            'created_at'      => $m->created_at->toIso8601String(),
            'time'            => $m->created_at->format('H:i'),
            'day_key'         => $m->created_at->format('Y-m-d'),
            'day_label'       => $this->dayLabel($m->created_at),
        ];
    }

    /** Dernier rapport (épinglé en tête du fil). */
    private function pinnedReport(Task $task): ?array
    {
        /** @var DailyReport|null $report */
        $report = $task->dailyReports->first(); // déjà trié report_date DESC

        if (!$report) {
            return null;
        }

        $authorName = $task->owner?->name ?? 'Producteur';

        return [
            'id'             => $report->id,
            'date'           => $report->report_date->format('d/m/Y'),
            'date_human'     => $report->report_date->translatedFormat('D j M'),
            'progress'       => is_null($report->task_progress_percent) ? null : (int) $report->task_progress_percent,
            'summary'        => $report->summary,
            'blockers'       => $report->blockers,
            'is_voice'       => $report->isVoice(),
            'voice_url'      => $report->voiceUrl(),
            'voice_duration' => $report->voice_duration,
            'author' => [
                'name'     => $authorName,
                'initials' => $this->initials($authorName),
            ],
        ];
    }

    /** Destinataires du rapport : superviseur(s) du stage + admins. */
    private function recipients(Task $task): array
    {
        $recipients = collect();

        if ($task->stage && $task->stage->supervisor_id) {
            $sup = User::find($task->stage->supervisor_id);
            if ($sup) {
                $recipients->push($sup);
            }
        }

        User::role('admin')->get()->each(fn($u) => $recipients->push($u));

        return $recipients
            ->unique('id')
            ->reject(fn(User $u) => $u->id === $task->owner_id)
            ->map(fn(User $u) => [
                'id'       => $u->id,
                'name'     => $u->name,
                'initials' => $this->initials($u->name),
            ])
            ->values()
            ->all();
    }

    private function reactions(TaskMessage $m, ?User $viewer): array
    {
        if ($m->reactions->isEmpty()) {
            return [];
        }

        return $m->reactions
            ->groupBy('emoji')
            ->map(fn($group, $emoji) => [
                'emoji' => $emoji,
                'count' => $group->count(),
                'mine'  => $viewer ? $group->contains('user_id', $viewer->id) : false,
            ])
            ->values()
            ->all();
    }

    private function excerpt(TaskMessage $m): string
    {
        if ($m->isVoice()) {
            return '🎤 Message vocal';
        }
        if ($m->isImage()) {
            return '🖼️ Photo';
        }
        if ($m->isFile()) {
            return '📎 ' . ($m->attachment_name ?? 'Fichier');
        }

        return Str::limit((string) $m->body, 80);
    }

    private function dayLabel(\Illuminate\Support\Carbon $date): string
    {
        if ($date->isToday()) {
            return "Aujourd'hui";
        }
        if ($date->isYesterday()) {
            return 'Hier';
        }

        return $date->translatedFormat('l j F Y');
    }

    private function initials(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '?';
        }

        $parts = preg_split('/\s+/', $name);
        $first = mb_substr($parts[0], 0, 1);
        $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '';

        return mb_strtoupper($first . $second);
    }
}
