<?php

namespace App\Services;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\Etudiant;
use App\Models\Employe;
use App\Models\Stage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminPresenceService
{
    /**
     * Date à partir de laquelle le système est considéré actif.
     * Aucune absence ne sera comptée avant cette date.
     */
    private function systemStartDate(): Carbon
    {
        // ✅ Prendre la date du premier utilisateur créé dans la base
        $firstUser = User::orderBy('created_at')->first();
        return $firstUser
            ? Carbon::parse($firstUser->created_at)->startOfDay()
            : Carbon::parse('2026-04-27')->startOfDay();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  OVERVIEW DU JOUR
    // ══════════════════════════════════════════════════════════════════════════

    public function getTodayOverview(): array
    {
        $today = today();
        return [
            'total_checkins'   => AttendanceEvent::where('event_type', 'check_in')->whereDate('occurred_at', $today)->where('status', 'approved')->count(),
            'total_checkouts'  => AttendanceEvent::where('event_type', 'check_out')->whereDate('occurred_at', $today)->where('status', 'approved')->count(),
            'open_anomalies'   => AttendanceAnomaly::where('status', 'open')->whereDate('detected_at', $today)->count(),
            'late_arrivals'    => AttendanceDay::whereDate('attendance_date', $today)->where('late_minutes', '>', 0)->count(),
            'early_departures' => AttendanceDay::whereDate('attendance_date', $today)->where('early_departure_minutes', '>', 0)->count(),
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  LISTE DES JOURS DE PRÉSENCE (filtres admin)
    // ══════════════════════════════════════════════════════════════════════════

    public function listAttendanceDays(array $filters = [], int $perPage = 50): Builder
    {
        $query = AttendanceDay::with([
            'stage.etudiant.user',
            'stage.site',
            'checkInEvent.trustedDevice',
            'checkOutEvent.trustedDevice',
            'anomalies',
        ])->orderByDesc('attendance_date');

        if (!empty($filters['date_from']))    $query->whereDate('attendance_date', '>=', $filters['date_from']);
        if (!empty($filters['date_to']))      $query->whereDate('attendance_date', '<=', $filters['date_to']);
        if (!empty($filters['etudiant_id']))  $query->where('etudiant_id', $filters['etudiant_id']);
        if (!empty($filters['user_id']))      $query->where('user_id', $filters['user_id']);
        if (!empty($filters['site_id']))      $query->where('site_id', $filters['site_id']);
        if (!empty($filters['school']))       $query->whereHas('etudiant', fn($q) => $q->where('ecole', $filters['school']));
        if (!empty($filters['status']))       $query->whereIn('validation_status', (array) $filters['status']);
        if (!empty($filters['anomalies_only'])) $query->whereHas('anomalies', fn($q) => $q->where('status', 'open'));

        return $query;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  STATS MENSUELLES
    // ══════════════════════════════════════════════════════════════════════════

    public function getMonthlyStats(int $year, int $month, ?int $userId = null): array
    {
        $start = Carbon::create($year, $month, 1);
        $end   = $start->copy()->endOfMonth();

        $query = AttendanceDay::with([
            'etudiant.personnel',
            'etudiant.user',
            'stage.etudiant.personnel',
            'stage.etudiant.user',
            'user.personnel',
            'anomalies',
        ])->whereBetween('attendance_date', [$start, $end]);

        if (DB::connection()->getDriverName() === 'sqlite') {
            $query->whereRaw("CAST(strftime('%w', attendance_date) AS INTEGER) BETWEEN 1 AND 5");
        } else {
            $query->whereRaw('WEEKDAY(attendance_date) BETWEEN 0 AND 4');
        }

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhere('validated_by', $userId)
                    ->orWhereHas('etudiant.user', fn($userQuery) => $userQuery->where('users.id', $userId))
                    ->orWhereHas('stage.etudiant.user', fn($userQuery) => $userQuery->where('users.id', $userId));
            });
        }

        return $query->get()
            ->groupBy(function (AttendanceDay $day) {
                $etudiant = $day->etudiant ?: $day->stage?->etudiant;
                $user = $day->user ?: $etudiant?->user;

                return $user?->id ?: 'etudiant-' . ($etudiant?->id ?? $day->id);
            })
            ->map(function (Collection $days) {
                $first = $days->first();
                $etudiant = $first->etudiant ?: $first->stage?->etudiant;
                $user = $first->user ?: $etudiant?->user;
                $displayName = $etudiant?->full_name ?: $user?->name ?: 'Utilisateur';

                return (object) [
                    'user_id' => $user?->id,
                    'user_name' => $displayName,
                    'etudiant_nom' => $etudiant?->nom,
                    'total_minutes' => $days->sum(fn(AttendanceDay $day) => (int) ($day->worked_minutes ?? 0)),
                    'avg_daily_minutes' => $days->avg(fn(AttendanceDay $day) => (int) ($day->worked_minutes ?? 0)),
                    'days_present' => $days->count(),
                    'total_late_minutes' => $days->sum(fn(AttendanceDay $day) => (int) ($day->late_minutes ?? 0)),
                    'total_early_minutes' => $days->sum(fn(AttendanceDay $day) => (int) ($day->early_departure_minutes ?? 0)),
                    'total_anomalies' => $days->sum(fn(AttendanceDay $day) => $day->anomalies->where('status', '!=', 'resolved')->count()),
                ];
            })
            ->values()
            ->all();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ANOMALIES OUVERTES
    // ══════════════════════════════════════════════════════════════════════════

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

    public function resolveAnomaly(int $anomalyId, array $data): bool
    {
        $anomaly = AttendanceAnomaly::findOrFail($anomalyId);
        $anomaly->update([
            'status'          => 'resolved',
            'reviewed_by'     => $data['reviewed_by'] ?? auth()->id(),
            'reviewed_at'     => now(),
            'resolution_note' => $data['resolution_note'] ?? null,
        ]);
        return true;
    }

    public function searchUsers(string $query): Collection
    {
        return User::select('id', 'name', 'email')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  STATS GLOBALES — graphiques admin
    // ══════════════════════════════════════════════════════════════════════════

    public function getGlobalStats(string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        // ── Plage de dates ────────────────────────────────────────────────────
        if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? Carbon::parse($dateFrom) : now()->startOfMonth();
            $endDate   = $dateTo   ? Carbon::parse($dateTo)   : now()->endOfMonth();
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
                default:
                    $startDate = today();
                    $endDate = today();
                    break;
            }
        }

        // ✅ Date d'activation du système — aucune absence avant cette date
        $systemStart = $this->systemStartDate();
        $today       = today();

        // ── Données pointage réelles (jours ouvrés uniquement) ───────────────
        $dailyStats = AttendanceDay::whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
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

        // Nombre d'employés actifs attendus
        $expectedEmployeesCount = User::whereHas('personnel', function ($query) {
            $query->where('personnable_type', Employe::class);
        })
            ->where('status', 'actif')
            ->count();

        $allDates      = [];
        $presentData   = [];
        $lateMinutesData = [];
        $lateDaysData  = [];
        $absentData    = [];
        $workedHoursData = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {

            // ── Ignorer week-ends ─────────────────────────────────────────────
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            // ✅ Ignorer les jours AVANT l'activation du système
            if ($currentDate->lt($systemStart)) {
                $currentDate->addDay();
                continue;
            }

            // ✅ Ignorer les jours FUTURS (après aujourd'hui) — courbe à 0 collée à l'axe X
            if ($currentDate->gt($today)) {
                $currentDate->addDay();
                continue;
            }

            $dateKey   = $currentDate->format('Y-m-d');
            $dateLabel = $currentDate->format('d/m');

            // Nombre de stagiaires attendus ce jour
            $studentsCount = Stage::whereDate('date_debut', '<=', $dateKey)
                ->whereDate('date_fin', '>=', $dateKey)
                ->distinct('etudiant_id')
                ->count('etudiant_id');

            $expectedTotal = $studentsCount + $expectedEmployeesCount;

            // Ignorer les jours où personne n'est encore inscrit dans le système
            if ($expectedTotal === 0) {
                $currentDate->addDay();
                continue;
            }

            $allDates[] = $dateLabel;
            $dayStats   = $dailyStats->get($dateKey);

            if ($dayStats) {
                $presentData[]     = (int) $dayStats->present;
                $lateMinutesData[] = (int) $dayStats->late_minutes;
                $lateDaysData[]    = (int) $dayStats->late_days;
                $workedHoursData[] = round((int) $dayStats->worked_minutes / 60, 1);
                // Absences = attendus - présents (min 0)
                $absentData[]      = max(0, $expectedTotal - (int) $dayStats->present);
            } else {
                // Personne n'a pointé → tous absents
                $presentData[]     = 0;
                $lateMinutesData[] = 0;
                $lateDaysData[]    = 0;
                $workedHoursData[] = 0;
                $absentData[]      = $expectedTotal;
            }

            $currentDate->addDay();
        }

        // ── KPI totaux ────────────────────────────────────────────────────────
        $totalDays       = array_sum($presentData) + array_sum($absentData);
        $presentDays     = array_sum($presentData);
        $totalLateMin    = array_sum($lateMinutesData);
        $totalWorkedMin  = (int) array_sum(array_map(fn($h) => round($h * 60), $workedHoursData));
        $totalLateDays   = array_sum($lateDaysData);
        $totalAbsent     = array_sum($absentData);
        $tauxPresence    = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        $anomaliesCount = AttendanceAnomaly::where('status', 'open')
            ->whereBetween('detected_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->count();

        return [
            'taux_presence'      => $tauxPresence,
            'present_days'       => $presentDays,
            'total_days'         => $totalDays,
            'total_late_minutes' => $totalLateMin,
            'total_late_days'    => $totalLateDays,
            'total_worked_hours' => round($totalWorkedMin / 60, 1),
            'total_absent'       => $totalAbsent,
            'total_anomalies'    => $anomaliesCount,
            'period_days'        => count($allDates),
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

    // ══════════════════════════════════════════════════════════════════════════
    //  STATS PAR GROUPE (étudiants / employés)
    // ══════════════════════════════════════════════════════════════════════════

    public function getStatsByGroup(string $group = 'all', string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? Carbon::parse($dateFrom) : now()->startOfMonth();
            $endDate   = $dateTo   ? Carbon::parse($dateTo)   : now()->endOfMonth();
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
            }
        }

        $etudiantsStats = AttendanceDay::whereNotNull('etudiant_id')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN first_check_in_at IS NOT NULL THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN arrival_status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN first_check_in_at IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(worked_minutes) as worked_minutes,
                SUM(late_minutes) as late_minutes
            ')->first();

        $employesStats = AttendanceDay::whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN first_check_in_at IS NOT NULL THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN arrival_status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN first_check_in_at IS NULL THEN 1 ELSE 0 END) as absent,
                SUM(worked_minutes) as worked_minutes,
                SUM(late_minutes) as late_minutes
            ')->first();

        $etudiantsChart = $this->generateChartData(
            AttendanceDay::whereNotNull('etudiant_id')->whereBetween('attendance_date', [$startDate, $endDate]),
            $startDate,
            $endDate
        );
        $employesChart = $this->generateChartData(
            AttendanceDay::whereNotNull('user_id')->whereNull('etudiant_id')->whereBetween('attendance_date', [$startDate, $endDate]),
            $startDate,
            $endDate
        );

        return [
            'etudiants' => [
                'count'           => $etudiantsStats->total ?? 0,
                'present'         => $etudiantsStats->present ?? 0,
                'late'            => $etudiantsStats->late ?? 0,
                'absent'          => $etudiantsStats->absent ?? 0,
                'worked_hours'    => round(($etudiantsStats->worked_minutes ?? 0) / 60, 1),
                'late_minutes'    => $etudiantsStats->late_minutes ?? 0,
                'taux_presence'   => ($etudiantsStats->total ?? 0) > 0 ? round(($etudiantsStats->present / $etudiantsStats->total) * 100, 1) : 0,
                'avg_worked_hours' => ($etudiantsStats->present ?? 0) > 0 ? round(($etudiantsStats->worked_minutes ?? 0) / 60 / ($etudiantsStats->present ?? 1), 1) : 0,
                'chart_data'      => $etudiantsChart,
            ],
            'employes' => [
                'count'           => $employesStats->total ?? 0,
                'present'         => $employesStats->present ?? 0,
                'late'            => $employesStats->late ?? 0,
                'absent'          => $employesStats->absent ?? 0,
                'worked_hours'    => round(($employesStats->worked_minutes ?? 0) / 60, 1),
                'late_minutes'    => $employesStats->late_minutes ?? 0,
                'taux_presence'   => ($employesStats->total ?? 0) > 0 ? round(($employesStats->present / $employesStats->total) * 100, 1) : 0,
                'avg_worked_hours' => ($employesStats->present ?? 0) > 0 ? round(($employesStats->worked_minutes ?? 0) / 60 / ($employesStats->present ?? 1), 1) : 0,
                'chart_data'      => $employesChart,
            ],
        ];
    }

    private function generateChartData(Builder $query, Carbon $startDate, Carbon $endDate): array
    {
        $systemStart = $this->systemStartDate();
        $today       = today();

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

        $labels = $present = $late = $absent = $lateMinutes = $workedHours = [];

        $currentDate = $startDate->copy()->startOfDay();
        while ($currentDate <= $endDate) {
            // ✅ Ignorer week-ends, avant activation, jours futurs
            if (
                $currentDate->isWeekend()
                || $currentDate->lt($systemStart)
                || $currentDate->gt($today)
            ) {
                $currentDate->addDay();
                continue;
            }

            $dateKey  = $currentDate->toDateString();
            $labels[] = $currentDate->isoFormat('D MMM');
            $stats    = $dailyStats->get($dateKey);

            if ($stats) {
                $present[]     = (int) $stats->present;
                $late[]        = (int) $stats->late;
                $absent[]      = (int) $stats->absent;
                $lateMinutes[] = (int) $stats->late_minutes;
                $workedHours[] = round($stats->worked_minutes / 60, 1);
            } else {
                $present[]     = 0;
                $late[]        = 0;
                $absent[]      = 0;
                $lateMinutes[] = 0;
                $workedHours[] = 0;
            }

            $currentDate->addDay();
        }

        return compact('labels', 'present', 'late', 'absent', 'lateMinutes', 'workedHours');
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  STATS DÉTAILLÉES UTILISATEUR
    // ══════════════════════════════════════════════════════════════════════════

    public function getUserDetailedStats(
        int     $userId,
        string  $period   = 'month',
        ?string $dateFrom = null,
        ?string $dateTo   = null
    ): array {
        $user       = User::with('etudiant')->findOrFail($userId);
        $isEtudiant = $user->etudiant !== null;

        // ── Plage de dates ────────────────────────────────────────────────────
        if ($dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom)->startOfDay();
            $endDate   = Carbon::parse($dateTo)->endOfDay();
        } else {
            switch ($period) {
                case 'today':
                    $startDate = today()->startOfDay();
                    $endDate = today()->endOfDay();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek()->startOfDay();
                    $endDate = now()->endOfWeek()->endOfDay();
                    break;
                case 'year':
                    $startDate = now()->startOfYear()->startOfDay();
                    $endDate = now()->endOfYear()->endOfDay();
                    break;
                default:
                    $startDate = now()->startOfMonth()->startOfDay();
                    $endDate = now()->endOfMonth()->endOfDay();
                    break;
            }
        }

        // ── Date d'activation de l'utilisateur ───────────────────────────────
        if ($isEtudiant) {
            $firstStage     = $user->etudiant->stages()->orderBy('date_debut')->first();
            $activationDate = $firstStage
                ? Carbon::parse($firstStage->date_debut)->startOfDay()
                : Carbon::parse($user->created_at)->startOfDay();
        } else {
            $activationDate = Carbon::parse($user->created_at)->startOfDay();
        }

        // ✅ La date effective est le MAX entre l'activation user et l'activation système
        $systemStart    = $this->systemStartDate();
        $activationDate = $activationDate->max($systemStart);

        // ── Récupérer les pointages ───────────────────────────────────────────
        $query = AttendanceDay::weekdays();
        if ($isEtudiant) $query->where('etudiant_id', $user->etudiant->id);
        else             $query->where('user_id', $user->id)->whereNull('etudiant_id');

        $days = $query
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('attendance_date')
            ->get()
            ->keyBy(fn($d) => Carbon::parse($d->attendance_date)->toDateString());

        $today = today()->startOfDay();

        $labels = $present = $onTime = $lateDays = $absences = $lateMinutes = $workedHours = [];

        $currentDate = $startDate->copy()->startOfDay();
        while ($currentDate->lte($endDate->copy()->startOfDay())) {

            // ✅ Ignorer week-ends et jours futurs
            if ($currentDate->isWeekend() || $currentDate->gt($today)) {
                $currentDate->addDay();
                continue;
            }

            $dateKey          = $currentDate->toDateString();
            $labels[]         = $currentDate->isoFormat('D MMM');
            $isBeforeActivation = $currentDate->lt($activationDate);
            $day              = $days->get($dateKey);

            if ($day) {
                $hasCheckIn = !is_null($day->first_check_in_at);
                $isLate     = $hasCheckIn && ($day->arrival_status === 'late');
                $isOnTime   = $hasCheckIn && !$isLate;

                $present[]     = $hasCheckIn ? 1 : 0;
                $onTime[]      = $isOnTime   ? 1 : 0;
                $lateDays[]    = $isLate     ? 1 : 0;
                $absences[]    = 0;
                $lateMinutes[] = (int) ($day->late_minutes ?? 0);
                $workedHours[] = round(($day->worked_minutes ?? 0) / 60, 1);
            } else {
                // ✅ Absence uniquement si l'utilisateur était déjà actif ce jour
                $isRealAbsence = !$isBeforeActivation;

                $present[]     = 0;
                $onTime[]      = 0;
                $lateDays[]    = 0;
                $absences[]    = $isRealAbsence ? 1 : 0;
                $lateMinutes[] = 0;
                $workedHours[] = 0;
            }

            $currentDate->addDay();
        }

        // ── KPI ───────────────────────────────────────────────────────────────
        $totalExpectedDays  = $presentDays = $lateDaysCount = 0;
        $totalLateMinutes   = $totalWorkedMinutes = 0;

        $checkDate = $startDate->copy()->startOfDay();
        while ($checkDate->lte($endDate->copy()->startOfDay())) {
            if ($checkDate->isWeekend()) {
                $checkDate->addDay();
                continue;
            }

            $isActive = $checkDate->gte($activationDate);
            $isFuture = $checkDate->gt($today);

            if ($isActive && !$isFuture) {
                $totalExpectedDays++;
                $day = $days->get($checkDate->toDateString());
                if ($day && !is_null($day->first_check_in_at)) {
                    $presentDays++;
                    if ($day->arrival_status === 'late') $lateDaysCount++;
                    $totalLateMinutes   += (int) ($day->late_minutes  ?? 0);
                    $totalWorkedMinutes += (int) ($day->worked_minutes ?? 0);
                }
            }
            $checkDate->addDay();
        }

        $anomalies = AttendanceAnomaly::where('user_id', $userId)
            ->whereIn('status', ['open', 'flagged'])
            ->count();

        // Note: les clés du chart_data utilisent 'on_time' et 'late_days' (camelCase dans la vue)
        return [
            'user'               => $user,
            'is_etudiant'        => $isEtudiant,
            'total_days'         => $totalExpectedDays,
            'present_days'       => $presentDays,
            'late_days'          => $lateDaysCount,
            'total_late_minutes' => $totalLateMinutes,
            'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
            'avg_daily_hours'    => $presentDays > 0
                ? round(($totalWorkedMinutes / 60) / $presentDays, 1)
                : 0,
            'open_anomalies'     => $anomalies,
            'chart_data'         => [
                'labels'       => $labels,
                'present'      => $present,
                'on_time'      => $onTime,
                'late_days'    => $lateDays,
                'absences'     => $absences,
                'late_minutes' => $lateMinutes,
                'worked_hours' => $workedHours,
            ],
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  TOP RETARDATAIRES
    // ══════════════════════════════════════════════════════════════════════════

    public function getTopLateUsers(int $limit = 10, string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        return AttendanceDay::topLate($limit, $period, $dateFrom, $dateTo)->get()->toArray();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ABSENCES
    // ══════════════════════════════════════════════════════════════════════════

    public function getAbsencesWithDetails(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfMonth();
            $endDate   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()     : now()->endOfMonth();
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek()->startOfDay();
                    $endDate = now()->endOfWeek()->endOfDay();
                    break;
                case 'year':
                    $startDate = now()->startOfYear()->startOfDay();
                    $endDate = now()->endOfYear()->endOfDay();
                    break;
                default:
                    $startDate = now()->startOfMonth()->startOfDay();
                    $endDate = now()->endOfMonth()->endOfDay();
                    break;
            }
        }

        $systemStart = $this->systemStartDate();
        if ($startDate->lt($systemStart)) {
            $startDate = $systemStart->copy();
        }

        $today = today()->endOfDay();

        $absentCountByUserName = [];
        $absentDaysByUserName = [];

        $employeeIds = User::whereHas('personnel', function ($query) {
            $query->where('personnable_type', Employe::class);
        })
            ->where('status', 'actif')
            ->pluck('id')
            ->values()
            ->all();

        $employeeNameById = User::with('personnel')->whereIn('id', $employeeIds)->get()->pluck('name', 'id')->toArray();
        $employeeCreatedAtById = User::whereIn('id', $employeeIds)->pluck('created_at', 'id')->map(fn($d) => Carbon::parse($d)->startOfDay())->toArray();

        $attendanceDays = AttendanceDay::whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
            ->select(['attendance_date', 'etudiant_id', 'user_id', 'first_check_in_at'])
            ->get()
            ->groupBy(fn($d) => Carbon::parse($d->attendance_date)->format('Y-m-d'));

        $days = $startDate->copy();
        while ($days->lte($endDate)) {
            if ($days->isWeekend() || $days->gt($today)) {
                $days->addDay();
                continue;
            }

            $dateKey         = $days->format('Y-m-d');
            $attendanceForDay = $attendanceDays->get($dateKey) ?? collect();

            $presentEtudiantIds = $attendanceForDay
                ->filter(fn($ad) => !empty($ad->first_check_in_at) && !is_null($ad->etudiant_id))
                ->pluck('etudiant_id')->unique()->values()->all();

            $presentEmployeeIds = $attendanceForDay
                ->filter(fn($ad) => !empty($ad->first_check_in_at) && !is_null($ad->user_id) && is_null($ad->etudiant_id))
                ->pluck('user_id')->unique()->values()->all();

            $activeStageEtudiantIds = Stage::whereDate('date_debut', '<=', $dateKey)
                ->whereDate('date_fin', '>=', $dateKey)
                ->distinct('etudiant_id')->pluck('etudiant_id')->values()->all();

            $absentEtudiantIds = array_values(array_diff($activeStageEtudiantIds, $presentEtudiantIds));
            if (!empty($absentEtudiantIds)) {
                $etudiantUsers = Etudiant::whereIn('id', $absentEtudiantIds)->with('user')->get();
                foreach ($etudiantUsers as $et) {
                    $userCreatedAt = Carbon::parse($et->user?->created_at ?? $systemStart)->startOfDay();
                    if ($days->gte($userCreatedAt)) {
                        $name = $et->user?->name ?? 'Inconnu';
                        $absentCountByUserName[$name] = ($absentCountByUserName[$name] ?? 0) + 1;
                        $absentDaysByUserName[$name][] = [
                            'label' => $days->locale('fr')->isoFormat('dddd D MMMM YYYY'),
                            'date' => $dateKey,
                        ];
                    }
                }
            }

            foreach ($employeeIds as $uid) {
                if (!in_array($uid, $presentEmployeeIds)) {
                    $empCreatedAt = $employeeCreatedAtById[$uid] ?? $systemStart;
                    if ($days->gte($empCreatedAt)) {
                        $name = $employeeNameById[$uid] ?? 'Inconnu';
                        $absentCountByUserName[$name] = ($absentCountByUserName[$name] ?? 0) + 1;
                        $absentDaysByUserName[$name][] = [
                            'label' => $days->isoFormat('dddd D MMMM YYYY'),
                            'date' => $dateKey,
                        ];
                    }
                }
            }

            $days->addDay();
        }

        arsort($absentCountByUserName);
        $counts = array_slice($absentCountByUserName, 0, 10, true);
        $details = [];

        foreach (array_keys($counts) as $name) {
            $details[$name] = $absentDaysByUserName[$name] ?? [];
        }

        $items = [];
        foreach ($counts as $name => $count) {
            $items[] = [
                'user' => $name,
                'count' => $count,
                'details' => $absentDaysByUserName[$name] ?? [],
            ];
        }

        return [
            'counts' => $counts,
            'details' => $details,
            'items' => $items,
        ];
    }

    public function getAbsences(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfMonth();
            $endDate   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()     : now()->endOfMonth();
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek()->startOfDay();
                    $endDate = now()->endOfWeek()->endOfDay();
                    break;
                case 'year':
                    $startDate = now()->startOfYear()->startOfDay();
                    $endDate = now()->endOfYear()->endOfDay();
                    break;
                default:
                    $startDate = now()->startOfMonth()->startOfDay();
                    $endDate = now()->endOfMonth()->endOfDay();
                    break;
            }
        }

        // ✅ Borner la date de départ à l'activation du système
        $systemStart = $this->systemStartDate();
        if ($startDate->lt($systemStart)) {
            $startDate = $systemStart->copy();
        }

        $today = today()->endOfDay();

        $absentCountByUserName = [];
        $employeeIds = User::whereHas('personnel', function ($query) {
            $query->where('personnable_type', Employe::class);
        })
            ->where('status', 'actif')
            ->pluck('id')
            ->values()
            ->all();

        $employeeNameById = User::with('personnel')->whereIn('id', $employeeIds)->get()->pluck('name', 'id')->toArray();
        $employeeCreatedAtById = User::whereIn('id', $employeeIds)->pluck('created_at', 'id')->map(fn($d) => Carbon::parse($d)->startOfDay())->toArray();

        $attendanceDays = AttendanceDay::whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
            ->select(['attendance_date', 'etudiant_id', 'user_id', 'first_check_in_at'])
            ->get()
            ->groupBy(fn($d) => Carbon::parse($d->attendance_date)->format('Y-m-d'));

        $days = $startDate->copy();
        while ($days->lte($endDate)) {

            // ✅ Ignorer week-ends et jours futurs
            if ($days->isWeekend() || $days->gt($today)) {
                $days->addDay();
                continue;
            }

            $dateKey         = $days->format('Y-m-d');
            $attendanceForDay = $attendanceDays->get($dateKey) ?? collect();

            $presentEtudiantIds = $attendanceForDay
                ->filter(fn($ad) => !empty($ad->first_check_in_at) && !is_null($ad->etudiant_id))
                ->pluck('etudiant_id')->unique()->values()->all();

            $presentEmployeeIds = $attendanceForDay
                ->filter(fn($ad) => !empty($ad->first_check_in_at) && !is_null($ad->user_id) && is_null($ad->etudiant_id))
                ->pluck('user_id')->unique()->values()->all();

            // Stagiaires actifs ce jour
            $activeStageEtudiantIds = Stage::whereDate('date_debut', '<=', $dateKey)
                ->whereDate('date_fin', '>=', $dateKey)
                ->distinct('etudiant_id')->pluck('etudiant_id')->values()->all();

            $absentEtudiantIds = array_values(array_diff($activeStageEtudiantIds, $presentEtudiantIds));
            if (!empty($absentEtudiantIds)) {
                $etudiantUsers = Etudiant::whereIn('id', $absentEtudiantIds)->with('user')->get();
                foreach ($etudiantUsers as $et) {
                    // ✅ Ne compter absent que si le user existait déjà à cette date
                    $userCreatedAt = Carbon::parse($et->user?->created_at ?? $systemStart)->startOfDay();
                    if ($days->gte($userCreatedAt)) {
                        $name = $et->user?->name ?? 'Inconnu';
                        $absentCountByUserName[$name] = ($absentCountByUserName[$name] ?? 0) + 1;
                    }
                }
            }

            foreach ($employeeIds as $uid) {
                if (!in_array($uid, $presentEmployeeIds)) {
                    // ✅ Ne compter absent que si l'employé existait à cette date
                    $empCreatedAt = $employeeCreatedAtById[$uid] ?? $systemStart;
                    if ($days->gte($empCreatedAt)) {
                        $name = $employeeNameById[$uid] ?? 'Inconnu';
                        $absentCountByUserName[$name] = ($absentCountByUserName[$name] ?? 0) + 1;
                    }
                }
            }

            $days->addDay();
        }

        arsort($absentCountByUserName);
        return array_slice($absentCountByUserName, 0, 10, true);
    }
}
