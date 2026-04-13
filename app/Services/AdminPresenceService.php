<?php

namespace App\Services;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminPresenceService
{
    /**
     * Récupère les présences du jour avec stats de synthèse.
     */
    public function getTodayOverview(): array
    {
        $today = today();

        return [
            'total_checkins' => AttendanceEvent::where('event_type', 'check_in')
                ->whereDate('occurred_at', $today)
                ->where('status', 'approved')
                ->count(),
            'total_checkouts' => AttendanceEvent::where('event_type', 'check_out')
                ->whereDate('occurred_at', $today)
                ->where('status', 'approved')
                ->count(),
            'open_anomalies' => AttendanceAnomaly::where('status', 'open')
                ->whereDate('detected_at', $today)
                ->count(),
            'late_arrivals' => AttendanceDay::whereDate('attendance_date', $today)
                ->where('late_minutes', '>', 0)
                ->count(),
            'early_departures' => AttendanceDay::whereDate('attendance_date', $today)
                ->where('early_departure_minutes', '>', 0)
                ->count(),
        ];
    }

    /**
     * Liste des présences filtrées (admin).
     */
    public function listAttendanceDays(
        array $filters = [],
        int $perPage = 50
    ): Builder {
        $query = AttendanceDay::with([
            'stage.etudiant.user',
            'stage.site',
            'checkInEvent.trustedDevice',
            'checkOutEvent.trustedDevice',
            'anomalies',
        ])->orderByDesc('attendance_date');

        // Filtres
        if (!empty($filters['date_from'])) {
            $query->whereDate('attendance_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('attendance_date', '<=', $filters['date_to']);
        }
        if (!empty($filters['etudiant_id'])) {
            $query->where('etudiant_id', $filters['etudiant_id']);
        }
        if (!empty($filters['site_id'])) {
            $query->where('site_id', $filters['site_id']);
        }
        if (!empty($filters['status'])) {
            $query->whereIn('validation_status', (array) $filters['status']);
        }
        if (!empty($filters['anomalies_only'])) {
            $query->whereHas('anomalies', fn($q) => $q->where('status', 'open'));
        }

        return $query;
    }

    /**
     * Stats mensuelles par utilisateur/étudiant.
     */
    public function getMonthlyStats(int $year, int $month, ?int $userId = null): array
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();

        $stats = DB::table('attendance_days')
            ->selectRaw('
                COALESCE(etudiants.user_id, attendance_days.validated_by) as user_id,
                users.name as user_name,
                etudiants.nom as etudiant_nom,
                SUM(worked_minutes) as total_minutes,
                AVG(worked_minutes) as avg_daily_minutes,
                COUNT(*) as days_present,
                SUM(late_minutes) as total_late_minutes,
                SUM(early_departure_minutes) as total_early_minutes,
                COUNT(a.id) as total_anomalies
            ')
            ->leftJoin('stages', 'attendance_days.stage_id', '=', 'stages.id')
            ->leftJoin('etudiants', 'stages.etudiant_id', '=', 'etudiants.id')
            ->leftJoin('users', 'etudiants.user_id', '=', 'users.id')
            ->leftJoin('attendance_anomalies as a', function ($join) {
                $join->on('a.attendance_day_id', '=', 'attendance_days.id')
                    ->where('a.status', '!=', 'resolved');
            })
            ->whereBetween('attendance_date', [$start, $end]);

        if ($userId) {
            $stats->where(function ($q) use ($userId) {
                $q->where('etudiants.user_id', $userId)
                    ->orWhere('attendance_days.validated_by', $userId);
            });
        }

        return $stats->groupBy('user_id', 'user_name', 'etudiant_nom')
            ->get()
            ->toArray();
    }

    /**
     * Anomalies ouvertes à reviewer.
     */
    public function getOpenAnomalies(int $limit = 20): Collection
    {
        return AttendanceAnomaly::with([
            'attendanceEvent.stage.etudiant.user',
            'attendanceDay.stage.site',
        ])
            ->where('status', 'open')
            ->orderByDesc('detected_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Résoudre une anomalie.
     */
    public function resolveAnomaly(int $anomalyId, array $data): bool
    {
        $anomaly = AttendanceAnomaly::findOrFail($anomalyId);
        $anomaly->update([
            'status' => 'resolved',
            'reviewed_by' => $data['reviewed_by'] ?? auth()->id(),
            'reviewed_at' => now(),
            'resolution_note' => $data['resolution_note'] ?? null,
        ]);

        return true;
    }

    /**
     * Recherche utilisateurs par nom/email pour filtres.
     */
    public function searchUsers(string $query): Collection
    {
        return User::select('id', 'name', 'email')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get();
    }
}
