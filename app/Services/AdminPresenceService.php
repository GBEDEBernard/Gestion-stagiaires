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
    public function getGlobalStats(string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null)
    {
        // Déterminer la période complète
        if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? Carbon::parse($dateFrom) : now()->startOfMonth();
            $endDate = $dateTo ? Carbon::parse($dateTo) : now()->endOfMonth();
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default: // today
                    $startDate = today();
                    $endDate = today();
            }
        }

        // Récupérer les données réelles
        $query = AttendanceDay::whereBetween('attendance_date', [$startDate, $endDate]);

        $dailyStats = $query
            ->selectRaw('
            DATE(attendance_date) as date,
            COUNT(*) as total_days,
            SUM(CASE WHEN first_check_in_at IS NOT NULL THEN 1 ELSE 0 END) as present,
            SUM(late_minutes) as late_minutes,
            SUM(CASE WHEN arrival_status = "late" THEN 1 ELSE 0 END) as late_days,
            SUM(worked_minutes) as worked_minutes,
            SUM(CASE WHEN first_check_in_at IS NULL THEN 1 ELSE 0 END) as absent
        ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Générer les données complètes pour tous les jours de la période
        $allDates = [];
        $presentData = [];
        $lateMinutesData = [];
        $lateDaysData = [];
        $absentData = [];
        $workedHoursData = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dateLabel = $currentDate->format('d/m');

            $allDates[] = $dateLabel;

            $stats = $dailyStats->get($dateKey);
            if ($stats) {
                $presentData[] = (int) $stats->present;
                $lateMinutesData[] = (int) $stats->late_minutes;
                $lateDaysData[] = (int) $stats->late_days;
                $absentData[] = (int) $stats->absent;
                $workedHoursData[] = round($stats->worked_minutes / 60, 1);
            } else {
                $presentData[] = 0;
                $lateMinutesData[] = 0;
                $lateDaysData[] = 0;
                $absentData[] = 0;
                $workedHoursData[] = 0;
            }

            $currentDate->addDay();
        }

        // Calculer les totaux
        $totalDays = $dailyStats->sum('total_days');
        $presentDays = $dailyStats->sum('present');
        $totalLateMinutes = $dailyStats->sum('late_minutes');
        $totalWorkedMinutes = $dailyStats->sum('worked_minutes');
        $totalLateDays = $dailyStats->sum('late_days');
        $totalAbsent = $dailyStats->sum('absent');

        $tauxPresence = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        // Anomalies pour la période
        $anomaliesQuery = AttendanceAnomaly::where('status', 'open')
            ->whereBetween('detected_at', [$startDate, $endDate]);

        return [
            'taux_presence'       => $tauxPresence,
            'present_days'        => $presentDays,
            'total_days'          => $totalDays,
            'total_late_minutes'  => $totalLateMinutes,
            'total_late_days'     => $totalLateDays,
            'total_worked_hours'  => round($totalWorkedMinutes / 60, 1),
            'total_absent'        => $totalAbsent,
            'total_anomalies'     => $anomaliesQuery->count(),
            'period_days'         => $startDate->diffInDays($endDate) + 1,
            'chart_data' => [
                'labels'       => $allDates,
                'present'      => $presentData,
                'late_minutes' => $lateMinutesData,
                'late_days'    => $lateDaysData,
                'absent'       => $absentData,
                'worked_hours' => $workedHoursData,
            ],
        ];
    }
    /**
     * Stats par groupe (étudiants vs employés).
     */
    /**
     * Stats par groupe (étudiants vs employés).
     */
    public function getStatsByGroup(string $group = 'all', string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        // Déterminer la période complète
        if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? Carbon::parse($dateFrom) : now()->startOfMonth();
            $endDate = $dateTo ? Carbon::parse($dateTo) : now()->endOfMonth();
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default: // today
                    $startDate = today();
                    $endDate = today();
            }
        }

        $etudiantsQuery = AttendanceDay::whereNotNull('etudiant_id')
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        $employesQuery = AttendanceDay::whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        // Stats étudiants
        $etudiantsStats = $etudiantsQuery
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN first_check_in_at IS NOT NULL THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN arrival_status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN first_check_in_at IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(worked_minutes) as worked_minutes,
                SUM(late_minutes) as late_minutes
            ')
            ->first();

        // Stats employés
        $employesStats = $employesQuery
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN first_check_in_at IS NOT NULL THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN arrival_status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN first_check_in_at IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(worked_minutes) as worked_minutes,
                SUM(late_minutes) as late_minutes
            ')
            ->first();

        // Générer données de graphique pour étudiants
        $etudiantsChart = $this->generateChartData(
            AttendanceDay::whereNotNull('etudiant_id')->whereBetween('attendance_date', [$startDate, $endDate]),
            $startDate,
            $endDate
        );

        // Générer données de graphique pour employés
        $employesChart = $this->generateChartData(
            AttendanceDay::whereNotNull('user_id')->whereNull('etudiant_id')->whereBetween('attendance_date', [$startDate, $endDate]),
            $startDate,
            $endDate
        );

        return [
            'etudiants' => [
                'count' => $etudiantsStats->total ?? 0,
                'present' => $etudiantsStats->present ?? 0,
                'late' => $etudiantsStats->late ?? 0,
                'absent' => $etudiantsStats->absent ?? 0,
                'worked_hours' => round(($etudiantsStats->worked_minutes ?? 0) / 60, 1),
                'late_minutes' => $etudiantsStats->late_minutes ?? 0,
                'taux_presence' => $etudiantsStats->total > 0 ? round(($etudiantsStats->present / $etudiantsStats->total) * 100, 1) : 0,
                'chart_data' => $etudiantsChart,
            ],
            'employes' => [
                'count' => $employesStats->total ?? 0,
                'present' => $employesStats->present ?? 0,
                'late' => $employesStats->late ?? 0,
                'absent' => $employesStats->absent ?? 0,
                'worked_hours' => round(($employesStats->worked_minutes ?? 0) / 60, 1),
                'late_minutes' => $employesStats->late_minutes ?? 0,
                'taux_presence' => $employesStats->total > 0 ? round(($employesStats->present / $employesStats->total) * 100, 1) : 0,
                'chart_data' => $employesChart,
            ],
        ];
    }

    /**
     * Génère les données de graphique complètes pour une période donnée.
     */
    private function generateChartData(Builder $query, Carbon $startDate, Carbon $endDate): array
    {
        $dailyStats = $query
            ->selectRaw('
                DATE(attendance_date) as date,
                SUM(CASE WHEN first_check_in_at IS NOT NULL THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN arrival_status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN first_check_in_at IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(late_minutes) as late_minutes,
                SUM(worked_minutes) as worked_minutes
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $present = [];
        $late = [];
        $absent = [];
        $lateMinutes = [];
        $workedHours = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dateLabel = $currentDate->format('d/m');

            $labels[] = $dateLabel;

            $stats = $dailyStats->get($dateKey);
            if ($stats) {
                $present[] = (int) $stats->present;
                $late[] = (int) $stats->late;
                $absent[] = (int) $stats->absent;
                $lateMinutes[] = (int) $stats->late_minutes;
                $workedHours[] = round($stats->worked_minutes / 60, 1);
            } else {
                $present[] = 0;
                $late[] = 0;
                $absent[] = 0;
                $lateMinutes[] = 0;
                $workedHours[] = 0;
            }

            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'late_minutes' => $lateMinutes,
            'worked_hours' => $workedHours,
        ];
    }

    /**
     * Stats détaillées utilisateur (pour vue admin individuelle).
     */
    public function getUserDetailedStats(int $userId, string $period = 'month'): array
    {
        $user = User::with('etudiant')->findOrFail($userId);

        $isEtudiant = $user->etudiant !== null;

        $query = AttendanceDay::query();

        if ($isEtudiant) {
            $query->where('etudiant_id', $user->etudiant->id);
        } else {
            $query->where('user_id', $user->id);
        }
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
    public function getAbsences(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = AttendanceDay::whereNull('first_check_in_at');

        if ($dateFrom || $dateTo) {
            if ($dateFrom) {
                $query->whereDate('attendance_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('attendance_date', '<=', $dateTo);
            }
        } else {
            $query->globalStats($period);
        }

        return $query->with(['user', 'etudiant.user'])
            ->get()
            ->groupBy(fn($day) => $day->user?->name ?? $day->etudiant?->user?->name ?? 'Inconnu')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(10)
            ->toArray();
    }
}
