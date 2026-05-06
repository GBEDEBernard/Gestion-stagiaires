<?php

namespace App\Services;

use App\Models\AttendanceDay;
use App\Models\DailyReport;
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DailyReportService
{
    public function resolveActiveStageForUser(User $user): ?Stage
    {
        $etudiant = $user->etudiant;

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

            $etudiant = $user->etudiant;
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

            $report->fill([
                'stage_id' => $stage?->id,
                'etudiant_id' => $etudiant?->id,
                'user_id' => $user->hasRole('employe') ? $user->id : null,
                'attendance_day_id' => $attendanceDay?->id,
                'title' => 'Rapport du ' . today()->format('d/m/Y'),
                'summary' => $payload['summary'],
                'blockers' => $payload['blockers'] ?? null,
                'next_steps' => $payload['next_steps'] ?? null,
                'hours_declared' => $payload['hours_declared'] ?? 0,
                'status' => $status,
                'submitted_at' => $status === 'submitted' ? now() : null,
            ]);

            $report->save();

            return $report->load(['items', 'reviews']);
        });
    }
}
