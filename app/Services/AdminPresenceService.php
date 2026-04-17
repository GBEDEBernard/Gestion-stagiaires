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
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
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

    /**
     * Stats globales par période (admin dashboard).
     * Retourne datasets Chart.js-ready.
     */
    /**
     * Stats globales selon la période.
     */
    public function getGlobalStats(string $period = 'today')
    {
        $query = AttendanceDay::query()
            ->with('user') // si besoin pour d'autres stats
            ->selectRaw('
            COUNT(*) as total_days,
            SUM(CASE WHEN first_check_in_at IS NOT NULL THEN 1 ELSE 0 END) as present_days,
            SUM(late_minutes) as total_late_minutes,
            SUM(worked_minutes) as total_worked_minutes,
            SUM(CASE WHEN arrival_status = "late" THEN 1 ELSE 0 END) as total_late_days,
            attendance_date
        ');

        $groupByColumn = 'attendance_date';
        $dateFormat = 'Y-m-d';

        switch ($period) {
            case 'today':
                $query->whereDate('attendance_date', today());
                break;

            case 'week':
                $query->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()]);
                $groupByColumn = 'attendance_date'; // ou DAYNAME si tu veux par jour de la semaine
                break;

            case 'month':
                $query->whereYear('attendance_date', now()->year)
                    ->whereMonth('attendance_date', now()->month);
                break;

            case 'year':
                $query->whereYear('attendance_date', now()->year);
                $groupByColumn = 'MONTH(attendance_date)'; // ou juste l'année
                $dateFormat = 'Y-m';
                break;

            default:
                $query->whereDate('attendance_date', today());
        }

        $dailyStats = $query->groupBy($groupByColumn)
            ->get()
            ->map(function ($day) use ($dateFormat) {
                return [
                    'date'              => $day->attendance_date?->format($dateFormat) ?? 'N/A',
                    'total_days'        => (int) $day->total_days,
                    'present'           => (int) $day->present_days,
                    'total_late_minutes' => (int) $day->total_late_minutes,
                    'total_worked_minutes' => (int) $day->total_worked_minutes,
                    'total_late_days'   => (int) $day->total_late_days,
                ];
            });

        // Calcul des stats globales
        $totalDays = $dailyStats->sum('total_days');
        $presentDays = $dailyStats->sum('present');
        $totalLateMinutes = $dailyStats->sum('total_late_minutes');
        $totalWorkedMinutes = $dailyStats->sum('total_worked_minutes');
        $totalLateDays = $dailyStats->sum('total_late_days');

        $tauxPresence = $totalDays > 0
            ? round(($presentDays / $totalDays) * 100, 1)
            : 0;

        // Préparation des données pour le graphique
        $chartData = [
            'labels'  => $dailyStats->pluck('date')->toArray(),
            'present' => $dailyStats->pluck('present')->toArray(),
            'late'    => $dailyStats->pluck('total_late_days')->toArray(), // ou late_minutes si tu préfères
        ];

        return [
            'taux_presence'       => $tauxPresence,
            'present_days'        => $presentDays,
            'total_days'          => $totalDays,
            'total_late_minutes'  => $totalLateMinutes,
            'total_late_days'     => $totalLateDays,
            'total_worked_hours'  => round($totalWorkedMinutes / 60, 1),
            'total_anomalies'     => AttendanceAnomaly::where('status', 'open')->count(), // ou selon ta logique
            'chart_data'          => $chartData,
        ];
    }
    /**
     * Stats par groupe (étudiants vs employés).
     */
    /**
 * Stats par groupe (étudiants vs employés).
 */
public function getStatsByGroup(string $group = 'all', string $period = 'today'): array
{
    $etudiantsQuery = AttendanceDay::whereNotNull('etudiant_id');
    $employesQuery  = AttendanceDay::whereNotNull('user_id')->whereNull('etudiant_id');

    // Appliquer le filtre de période
    if ($period !== 'today') {
        $start = match ($period) {
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year'  => now()->startOfYear(),
            default => today()
        };

        $end = match ($period) {
            'week'  => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'year'  => now()->endOfYear(),
            default => today()
        };

        $etudiantsQuery->whereBetween('attendance_date', [$start, $end]);
        $employesQuery->whereBetween('attendance_date', [$start, $end]);
    }

    return [
        'etudiants' => [
            'count'            => $etudiantsQuery->count(),
            'present'          => $etudiantsQuery->whereNotNull('first_check_in_at')->count(),
            'late'             => $etudiantsQuery->where('arrival_status', 'late')->count(),
            'avg_worked_hours' => round(($etudiantsQuery->avg('worked_minutes') ?? 0) / 60, 1),
        ],
        'employes' => [
            'count'            => $employesQuery->count(),
            'present'          => $employesQuery->whereNotNull('first_check_in_at')->count(),
            'late'             => $employesQuery->where('arrival_status', 'late')->count(),
            'avg_worked_hours' => round(($employesQuery->avg('worked_minutes') ?? 0) / 60, 1),
        ],
    ];
}

    /**
     * Stats détaillées utilisateur (pour vue admin individuelle).
     */
    public function getUserDetailedStats(int $userId, string $period = 'month'): array
    {
        $user = User::findOrFail($userId);
        $isEtudiant = $user->etudiant_id !== null;

        $query = AttendanceDay::where($isEtudiant ? 'etudiant_id' : 'user_id', $userId);

        // Period filter
        switch ($period) {
            case 'week':
                $query->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('attendance_date', now()->month)->whereYear('attendance_date', now()->year);
                break;
            case 'year':
                $query->whereYear('attendance_date', now()->year);
                break;
        }

        $days = $query->get();
        $anomalies = AttendanceAnomaly::where('user_id', $userId)
            ->whereIn('status', ['open', 'flagged'])
            ->count();

        return [
            'user' => $user,
            'is_etudiant' => $isEtudiant,
            'total_days' => $days->count(),
            'present_days' => $days->whereNotNull('first_check_in_at')->count(),
            'late_days' => $days->where('arrival_status', 'late')->count(),
            'total_late_minutes' => $days->sum('late_minutes'),
            'total_worked_hours' => round($days->sum('worked_minutes') / 60, 1),
            'avg_daily_hours' => round($days->avg('worked_minutes') / 60, 1),
            'open_anomalies' => $anomalies,
            'chart_data' => [
                'labels' => $days->pluck('attendance_date')->map(fn($d) => $d->isoFormat('D MMM')),
                'worked_hours' => $days->map(fn($d) => round($d->worked_minutes / 60, 1)),
                'late_minutes' => $days->pluck('late_minutes'),
            ],
        ];
    }

    /**
     * Top utilisateurs en retard.
     */
    public function getTopLateUsers(int $limit = 10, string $period = 'month'): array
    {
        return AttendanceDay::topLate($limit, $period)->get()->toArray();
    }

    /**
     * Jours d'absence.
     */
    public function getAbsences(string $period = 'month'): array
    {
        // Simplified: days with no check-in
        return AttendanceDay::whereNull('first_check_in_at')
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->with(['user', 'etudiant.user'])
            ->get()
            ->groupBy(fn($day) => $day->user?->name ?? $day->etudiant->user->name)
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(10)
            ->toArray();
    }
}
