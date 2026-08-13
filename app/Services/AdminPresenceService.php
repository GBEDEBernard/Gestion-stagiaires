<?php

namespace App\Services;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\AttendanceException;
use App\Models\Etudiant;
use App\Models\Employe;
use App\Models\Holiday;
use App\Models\Stage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminPresenceService
{
    /**
     * Retourne la requête de base des stages actifs pour une date donnée,
     * en ne gardant que ceux dont le jour de la semaine est un jour de travail.
     * - Stages sans jours configurés → considérés travaillés tous les jours ouvrés.
     * - Stages avec jours → uniquement les jours cochés.
     */
    private function activeStagesOnDate(string $dateKey): Builder
    {
        $dayName = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ][Carbon::parse($dateKey)->format('N')] ?? '';

        return Stage::whereDate('date_debut', '<=', $dateKey)
            ->whereDate('date_fin', '>=', $dateKey)
            ->whereHas('etudiant.user', function ($q) use ($dateKey) {
                $q->where('status', 'actif')
                  ->where(function ($inner) use ($dateKey) {
                      // Le stagiaire n'est attendu qu'à partir de sa date effective de début :
                      //  - date_debut_pointage renseignée ET déjà passée (ou aujourd'hui)
                      $inner->whereHas('personnel', fn ($p) => $p
                          ->whereNotNull('date_debut_pointage')
                          ->where('date_debut_pointage', '<=', $dateKey))
                          //  - sinon, aucun début fixé → le compte utilisateur doit exister à cette date
                          ->orWhere(function ($fallback) use ($dateKey) {
                              $fallback->where(function ($f) {
                                  $f->whereNull('personnel_id')
                                    ->orWhereHas('personnel', fn ($p) => $p->whereNull('date_debut_pointage'));
                              })->whereDate('users.created_at', '<=', $dateKey);
                          });
                  });
            })
            ->where(function (Builder $q) use ($dayName) {
                // Aucun jour configuré → tous les jours ouvrés
                $q->whereDoesntHave('jours')
                  // OU un jour configuré correspondant au jour courant
                  ->orWhereHas('jours', fn ($j) => $j->whereRaw('LOWER(jour) = ?', [strtolower($dayName)]));
            });
    }

    /**
     * Détermine si un stagiaire a un jour de travail prévu à la date donnée.
     */
    private function isStudentWorkDay(int $etudiantId, string $dateKey): bool
    {
        return $this->activeStagesOnDate($dateKey)
            ->where('etudiant_id', $etudiantId)
            ->exists();
    }

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

    /**
     * Date effective de début de pointage d'un utilisateur.
     * Priorité : 1) date_debut_pointage (personnels)  2) $fallback (ex: stage.date_debut)
     *            3) users.created_at. Toujours bornée par la date d'activation du système.
     * Avant cette date : aucune absence comptée, aucun attendu.
     */
    private function debutPointage(?User $user, ?Carbon $fallback = null): Carbon
    {
        if (!$user) {
            return $this->systemStartDate();
        }

        $personnelDate = $user->personnel?->date_debut_pointage;
        $base = $personnelDate
            ? Carbon::parse($personnelDate)->startOfDay()
            : ($fallback ?? Carbon::parse($user->created_at)->startOfDay());

        return $base->max($this->systemStartDate());
    }

    /**
     * Retourne un tableau [date => true] pour les jours fériés actifs dans un intervalle.
     */
    private function getActiveHolidaysInRange(Carbon $start, Carbon $end): array
    {
        return Holiday::active()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('is_active', 'date')
            ->keys()
            ->flip()
            ->map(fn() => true)
            ->toArray();
    }

    /**
     * Jours d'absence corrigés (exceptions) pour un utilisateur dans un intervalle,
     * indexés par date [Y-m-d => AttendanceException].
     */
    private function getUserExceptions(int $userId, Carbon $start, Carbon $end): Collection
    {
        return AttendanceException::where('user_id', $userId)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($e) => $e->attendance_date->format('Y-m-d'));
    }

    /**
     * Clés [userId:Y-m-d => true] de toutes les exceptions d'un intervalle,
     * pour éviter de compter un jour corrigé comme absence.
     */
    private function getExceptionKeysInRange(Carbon $start, Carbon $end): array
    {
        return AttendanceException::whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->get(['user_id', 'attendance_date'])
            ->mapWithKeys(fn($e) => [$e->user_id . ':' . $e->attendance_date->format('Y-m-d') => true])
            ->all();
    }

    /**
     * Nombre de jours corrigés (exceptions) par date dans un intervalle.
     */
    private function getExceptionsCountByDate(Carbon $start, Carbon $end): array
    {
        return AttendanceException::whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('DATE(attendance_date) as date, COUNT(*) as cnt')
            ->groupBy('date')
            ->pluck('cnt', 'date')
            ->map(fn($cnt) => (int) $cnt)
            ->all();
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
            'late_arrivals'    => AttendanceDay::forActiveUsers()->whereDate('attendance_date', $today)->where('late_minutes', '>', 0)->count(),
            'early_departures' => AttendanceDay::forActiveUsers()->whereDate('attendance_date', $today)->where('early_departure_minutes', '>', 0)->count(),
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  LISTE DES JOURS DE PRÉSENCE (filtres admin)
    // ══════════════════════════════════════════════════════════════════════════

    public function listAttendanceDays(array $filters = [], int $perPage = 50): Builder
    {
        $query = AttendanceDay::forActiveUsers()->with([
            'stage.etudiant.user',
            'stage.site',
            'checkInEvent.trustedDevice',
            'checkOutEvent.trustedDevice',
            'anomalies',
            'lateAnomaly',
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

        if (!$userId) {
            $query->forActiveUsers();
        }

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

    /**
     * Calcule la plage de dates Du→Au pour une période donnée.
     * Utilise $dateFrom comme date de RÉFÉRENCE (pas seulement comme borne custom) :
     * si l'utilisateur a choisi une date puis clique "Mois", on calcule le mois de CETTE date.
     */
    public function resolvePeriodRange(string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        // Recherche manuelle explicite (Du + Au tous les deux remplis, bouton "Afficher")
        if ($period === 'custom' && $dateFrom && $dateTo) {
            return [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()];
        }

        $reference = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now();

        return match ($period) {
            'week'  => [$reference->copy()->startOfWeek(), $reference->copy()->endOfWeek()],
            'month' => [$reference->copy()->startOfMonth(), $reference->copy()->endOfMonth()],
            'year'  => [$reference->copy()->startOfYear(), $reference->copy()->endOfYear()],
            default => [$reference->copy()->startOfDay(), $reference->copy()->endOfDay()], // today
        };
    }

    public function getGlobalStats(string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        [$startDate, $endDate] = $this->resolvePeriodRange($period, $dateFrom, $dateTo);

        $today = today();

        // ✅ Date d'activation du système — aucun absent compté avant
        $systemStart = $this->systemStartDate();

        // ✅ Jours fériés actifs dans la plage
        $holidays = $this->getActiveHolidaysInRange($startDate, $endDate);

        // ── Plage du graphique ────────────────────────────────────────────────
        // Une courbe nécessite plusieurs points. Si la plage contient moins de
        // 4 jours ouvrés (ex : "Aujourd'hui", une plage custom d'un jour), on
        // élargit la fenêtre d'affichage vers le passé (au plus 6 jours avant le
        // dernier jour non futur de la plage). Les KPI restent sur la période réelle.
        $weekdaysInPeriod = 0;
        $probe = $startDate->copy();
        while ($probe <= $endDate) {
            if (!$probe->isWeekend()) $weekdaysInPeriod++;
            $probe->addDay();
        }

        $chartStart = $startDate->copy();
        if ($weekdaysInPeriod < 4) {
            $chartLimit = $endDate->copy();
            if ($chartLimit->gt($today)) $chartLimit = $today->copy();
            $chartStart = $chartLimit->subDays(6)->startOfDay();
            if ($chartStart->gt($startDate)) $chartStart = $startDate->copy();
        }

        // ✅ Jours fériés actifs sur la plage élargie du graphique
        $chartHolidays = $this->getActiveHolidaysInRange($chartStart, $endDate);

        // ── Données pointage réelles (jours ouvrés uniquement) ───────────────
        $dailyStats = AttendanceDay::forActiveUsers()
            ->whereBetween('attendance_date', [$chartStart, $endDate])
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

        // Employés actifs attendus (exclure les admin), avec leur date effective de début
        $employees = User::with('personnel')
            ->whereHas('personnel', function ($query) {
                $query->where('personnable_type', Employe::class);
            })
            ->where('status', 'actif')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->get();

        $employeeStartDates = $employees
            ->mapWithKeys(fn($u) => [$u->id => $this->debutPointage($u)])
            ->all();

        // ── Séries du graphique (fenêtre élargie) ─────────────────────────────
        $chartSeries = $this->buildSeries($dailyStats, $chartStart, $endDate, $systemStart, $chartHolidays, $today, $employeeStartDates);

        // ── Séries des KPI (période réelle) — jours futurs inclus à 0 mais ignorés ──
        $periodSeries = $this->buildSeries($dailyStats, $startDate, $endDate, $systemStart, $holidays, $today, $employeeStartDates);

        // ── KPI totaux : uniquement sur les jours déjà passés ────────────────
        $presentDays = $totalLateMin = $totalLateDays = $totalWorkedMin = $totalAbsent = 0;
        foreach ($periodSeries['labels'] as $i => $label) {
            if ($periodSeries['future'][$i]) continue;
            $presentDays    += $periodSeries['present'][$i];
            $totalLateMin   += $periodSeries['late_minutes'][$i];
            $totalLateDays  += $periodSeries['late_days'][$i];
            $totalWorkedMin += (int) round($periodSeries['worked_hours'][$i] * 60);
            $totalAbsent    += $periodSeries['absent'][$i];
        }
        $totalDays    = $presentDays + $totalAbsent;
        $tauxPresence = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

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
            'period_days'        => count($periodSeries['labels']),
            'range_start'        => $startDate,
            'range_end'          => $endDate,
            'chart_data' => [
                'labels'       => $chartSeries['labels'],
                'present'      => $chartSeries['present'],
                'late_minutes' => $chartSeries['late_minutes'],
                'late_days'    => $chartSeries['late_days'],
                'absent'       => $chartSeries['absent'],
                'worked_hours' => $chartSeries['worked_hours'],
                'holidays'     => $chartSeries['holidays'],
                'future'       => $chartSeries['future'],
            ],
        ];
    }

    /**
     * Construit les séries journalières (labels + valeurs) pour toute la plage
     * Du→Au, SANS exclure les jours futurs : ils sont inclus à 0 (aucune absence
     * comptée), ce qui donne une vraie courbe qui "monte" avec les jours passés
     * puis retombe à 0 sur les jours pas encore arrivés.
     */
    private function buildSeries(Collection $dailyStats, Carbon $rangeStart, Carbon $rangeEnd, Carbon $systemStart, array $holidays, Carbon $today, array $employeeStartDates): array
    {
        $labels          = [];
        $presentData     = [];
        $lateMinutesData = [];
        $lateDaysData    = [];
        $absentData      = [];
        $workedHoursData = [];
        $holidayFlags    = [];
        $futureFlags     = [];

        // ✅ Jours d'absence corrigés par date (non comptés comme absences)
        $exceptionsByDate = $this->getExceptionsCountByDate($rangeStart, $rangeEnd);

        $currentDate = $rangeStart->copy();
        while ($currentDate <= $rangeEnd) {

            // ── Ignorer week-ends (jours non travaillés) ──────────────────────
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            $dateKey = $currentDate->format('Y-m-d');
            $future  = $currentDate->gt($today);
            $holiday = isset($holidays[$dateKey]);
            $exceptionsCount = (int) ($exceptionsByDate[$dateKey] ?? 0);

            $labels[]       = $currentDate->format('d/m');
            $holidayFlags[] = $holiday;
            $futureFlags[]  = $future;

            if ($future) {
                // ✅ Jour pas encore arrivé : 0 partout, PAS compté en absence
                $presentData[]     = 0;
                $lateMinutesData[] = 0;
                $lateDaysData[]    = 0;
                $workedHoursData[] = 0;
                $absentData[]      = 0;
                $currentDate->addDay();
                continue;
            }

            // ── Compter les stagiaires attendus ce jour (uniquement actifs) ────
            $studentsCount = $this->activeStagesOnDate($dateKey)
                ->distinct('etudiant_id')
                ->count('etudiant_id');

            // ── Employés attendus ce jour : uniquement ceux déjà entrés en pointage ──
            $expectedEmployeesCount = count(array_filter(
                $employeeStartDates,
                fn(Carbon $start) => $start->lte($currentDate)
            ));

            $expectedTotal = $studentsCount + $expectedEmployeesCount;

            $isBeforeSystemStart = $currentDate->lt($systemStart);
            $dayStats            = $dailyStats->get($dateKey);

            if ($dayStats) {
                $presentData[]     = (int) $dayStats->present;
                $lateMinutesData[] = (int) $dayStats->late_minutes;
                $lateDaysData[]    = (int) $dayStats->late_days;
                $workedHoursData[] = round((int) $dayStats->worked_minutes / 60, 1);
                $absentData[]      = $holiday ? 0 : ($isBeforeSystemStart ? 0 : max(0, $expectedTotal - (int) $dayStats->present - $exceptionsCount));
            } else {
                $presentData[]     = 0;
                $lateMinutesData[] = 0;
                $lateDaysData[]    = 0;
                $workedHoursData[] = 0;
                $absentData[]      = $holiday ? 0 : ($isBeforeSystemStart ? 0 : max(0, $expectedTotal - $exceptionsCount));
            }

            $currentDate->addDay();
        }

        return [
            'labels'       => $labels,
            'present'      => $presentData,
            'late_minutes' => $lateMinutesData,
            'late_days'    => $lateDaysData,
            'absent'       => $absentData,
            'worked_hours' => $workedHoursData,
            'holidays'     => $holidayFlags,
            'future'       => $futureFlags,
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  STATS PAR GROUPE (étudiants / employés)
    // ══════════════════════════════════════════════════════════════════════════

    public function getStatsByGroup(string $group = 'all', string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        [$startDate, $endDate] = $this->resolvePeriodRange($period, $dateFrom, $dateTo);

        $etudiantsStats = AttendanceDay::forActiveUsers()->whereNotNull('etudiant_id')
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

        $employesStats = AttendanceDay::forActiveUsers()->whereNotNull('user_id')
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

        // ── Plage du graphique ─────────────────────────────────────────────────
        // Même logique que les stats globales : si la plage contient moins de
        // 4 jours ouvrés, on élargit la fenêtre du graphique vers le passé.
        // Les KPI du groupe restent calculés sur la période réelle.
        $today = today();
        $weekdaysInPeriod = 0;
        $probe = $startDate->copy();
        while ($probe <= $endDate) {
            if (!$probe->isWeekend()) $weekdaysInPeriod++;
            $probe->addDay();
        }

        $chartStart = $startDate->copy();
        if ($weekdaysInPeriod < 4) {
            $chartLimit = $endDate->copy();
            if ($chartLimit->gt($today)) $chartLimit = $today->copy();
            $chartStart = $chartLimit->subDays(6)->startOfDay();
            if ($chartStart->gt($startDate)) $chartStart = $startDate->copy();
        }

        $etudiantsChart = $this->generateChartData(
            AttendanceDay::forActiveUsers()->whereNotNull('etudiant_id')->whereBetween('attendance_date', [$chartStart, $endDate]),
            $chartStart,
            $endDate
        );
        $employesChart = $this->generateChartData(
            AttendanceDay::forActiveUsers()->whereNotNull('user_id')->whereNull('etudiant_id')->whereBetween('attendance_date', [$chartStart, $endDate]),
            $chartStart,
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
        $today = today();

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

        $holidays = $this->getActiveHolidaysInRange($startDate, $endDate);

        $labels = $present = $late = $absent = $lateMinutes = $workedHours = $isHoliday = $isFuture = [];

        $currentDate = $startDate->copy()->startOfDay();
        while ($currentDate <= $endDate) {
            // ✅ Ignorer week-ends (jours non travaillés)
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            $dateKey  = $currentDate->toDateString();
            $labels[] = $currentDate->isoFormat('D MMM');
            $isHoliday[] = isset($holidays[$dateKey]);
            $isFuture[]  = $currentDate->gt($today);

            if ($currentDate->gt($today)) {
                // ✅ Jour pas encore arrivé : 0 partout, PAS compté en absence
                $present[]     = 0;
                $late[]        = 0;
                $absent[]      = 0;
                $lateMinutes[] = 0;
                $workedHours[] = 0;
            } elseif (isset($holidays[$dateKey])) {
                $present[]     = 0;
                $late[]        = 0;
                $absent[]      = 0;
                $lateMinutes[] = 0;
                $workedHours[] = 0;
            } else {
                $stats = $dailyStats->get($dateKey);
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
            }

            $currentDate->addDay();
        }

        return compact('labels', 'present', 'late', 'absent', 'lateMinutes', 'workedHours', 'isHoliday', 'isFuture');
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

        // ── Plage de dates (date_from = date de référence) ─────────────────────
        [$startDate, $endDate] = $this->resolvePeriodRange($period, $dateFrom, $dateTo);

        // ── Date d'activation de l'utilisateur ───────────────────────────────
        // Priorité : date_debut_pointage → date_debut du 1er stage (stagiaire) → users.created_at
        if ($isEtudiant) {
            $firstStage     = $user->etudiant->stages()->orderBy('date_debut')->first();
            $stageStart     = $firstStage
                ? Carbon::parse($firstStage->date_debut)->startOfDay()
                : null;
            $activationDate = $stageStart
                ? $this->debutPointage($user, $stageStart)->max($stageStart)
                : $this->debutPointage($user);
        } else {
            $activationDate = $this->debutPointage($user);
        }

        // ✅ La date effective est le MAX entre l'activation user et l'activation système
        $systemStart    = $this->systemStartDate();
        $activationDate = $activationDate->max($systemStart);

        // ✅ Jours fériés actifs dans la plage
        $holidays = $this->getActiveHolidaysInRange($startDate, $endDate);

        $today = today()->startOfDay();

        // ── Plage du graphique ─────────────────────────────────────────────────
        // Même logique que les stats globales : si la plage contient moins de
        // 4 jours ouvrés, on élargit la fenêtre du graphique vers le passé.
        // Les KPI restent calculés sur la période réelle.
        $weekdaysInPeriod = 0;
        $probe = $startDate->copy();
        while ($probe->lte($endDate->copy()->startOfDay())) {
            if (!$probe->isWeekend()) $weekdaysInPeriod++;
            $probe->addDay();
        }

        $chartStart = $startDate->copy();
        if ($weekdaysInPeriod < 4) {
            $chartLimit = $endDate->copy();
            if ($chartLimit->gt($today)) $chartLimit = $today->copy();
            $chartStart = $chartLimit->subDays(6)->startOfDay();
            if ($chartStart->gt($startDate)) $chartStart = $startDate->copy();
        }

        // ✅ La courbe ne commence qu'à la date effective de début de pointage
        $chartStart = $chartStart->max($activationDate);
        if ($chartStart->gt($endDate->copy()->startOfDay())) {
            $chartStart = $endDate->copy()->startOfDay();
        }

        // ✅ Jours fériés actifs sur la plage élargie du graphique
        $chartHolidays = $this->getActiveHolidaysInRange($chartStart, $endDate);

        // ✅ Jours d'absence corrigés (exceptions) — non comptés comme absences
        $exceptions = $this->getUserExceptions($user->id, $chartStart, $endDate);

        // ── Récupérer les pointages (plage élargie pour le graphique) ──────────
        $query = AttendanceDay::weekdays();
        if ($isEtudiant) $query->where('etudiant_id', $user->etudiant->id);
        else             $query->where('user_id', $user->id)->whereNull('etudiant_id');

        $days = $query
            ->whereBetween('attendance_date', [$chartStart->toDateString(), $endDate->toDateString()])
            ->orderBy('attendance_date')
            ->get()
            ->keyBy(fn($d) => Carbon::parse($d->attendance_date)->toDateString());

        $labels = $dates = $present = $onTime = $lateDays = $absences = $lateMinutes = $workedHours = $isHoliday = $isFuture = [];

        $currentDate = $chartStart->copy()->startOfDay();
        while ($currentDate->lte($endDate->copy()->startOfDay())) {

            // ✅ Ignorer week-ends (jours non travaillés)
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            $dateKey          = $currentDate->toDateString();
            $labels[]         = $currentDate->isoFormat('D MMM');
            $dates[]          = $dateKey;
            $future           = $currentDate->gt($today);
            $isBeforeActivation = $currentDate->lt($activationDate);
            $isCurrentHoliday   = isset($chartHolidays[$dateKey]);
            $isCorrectedAbsence = isset($exceptions[$dateKey]);
            $isStudentRestDay   = $isEtudiant && !$this->isStudentWorkDay($user->etudiant->id, $dateKey);
            $isHoliday[]        = $isCurrentHoliday;
            $isFuture[]         = $future;
            $day                = $days->get($dateKey);

            if ($future) {
                // ✅ Jour pas encore arrivé : 0 partout, PAS compté en absence
                $present[]     = 0;
                $onTime[]      = 0;
                $lateDays[]    = 0;
                $absences[]    = 0;
                $lateMinutes[] = 0;
                $workedHours[] = 0;
            } elseif ($isCurrentHoliday || $isStudentRestDay || $isCorrectedAbsence) {
                // ✅ Jours fériés, repos, ou absences corrigées : non comptés
                $present[]     = 0;
                $onTime[]      = 0;
                $lateDays[]    = 0;
                $absences[]    = 0;
                $lateMinutes[] = 0;
                $workedHours[] = 0;
            } elseif ($day) {
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
            $isFutureDay = $checkDate->gt($today);
            $isHolidayCheck = isset($holidays[$checkDate->toDateString()]);
            $isCorrectedAbsence = isset($exceptions[$checkDate->toDateString()]);
            $isStudentRestDay = $isEtudiant && !$this->isStudentWorkDay($user->etudiant->id, $checkDate->toDateString());

            if ($isActive && !$isFutureDay && !$isHolidayCheck && !$isStudentRestDay && !$isCorrectedAbsence) {
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
                'dates'        => $dates,
                'present'      => $present,
                'on_time'      => $onTime,
                'late_days'    => $lateDays,
                'absences'     => $absences,
                'late_minutes' => $lateMinutes,
                'worked_hours' => $workedHours,
                'holidays'     => $isHoliday,
                'future'       => $isFuture,
            ],
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  TOP RETARDATAIRES
    // ══════════════════════════════════════════════════════════════════════════

    public function getTopLateUsers(int $limit = 10, string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        return AttendanceDay::topLate($limit, $period, $dateFrom, $dateTo)->forActiveUsers()->get()->toArray();
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ABSENCES
    // ══════════════════════════════════════════════════════════════════════════

    public function getAbsencesWithDetails(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        [$startDate, $endDate] = $this->resolvePeriodRange($period, $dateFrom, $dateTo);

        $systemStart = $this->systemStartDate();
        if ($startDate->lt($systemStart)) {
            $startDate = $systemStart->copy();
        }

        $today = today()->endOfDay();

        $holidays = $this->getActiveHolidaysInRange($startDate, $endDate);

        $absentCountByUserName = [];
        $absentDaysByUserName = [];

        $exceptionsByKey = $this->getExceptionKeysInRange($startDate, $endDate);

        $employeeIds = User::whereHas('personnel', function ($query) {
            $query->where('personnable_type', Employe::class);
        })
            ->where('status', 'actif')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->pluck('id')
            ->values()
            ->all();

        $employeeNameById = User::with('personnel')->whereIn('id', $employeeIds)->get()->pluck('name', 'id')->toArray();
        $employeeStartDateById = User::with('personnel')->whereIn('id', $employeeIds)->get()
            ->mapWithKeys(fn($u) => [$u->id => $this->debutPointage($u)])
            ->all();

        $attendanceDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
            ->select(['attendance_date', 'etudiant_id', 'user_id', 'first_check_in_at'])
            ->get()
            ->groupBy(fn($d) => Carbon::parse($d->attendance_date)->format('Y-m-d'));

        $days = $startDate->copy();
        while ($days->lte($endDate)) {
            if ($days->isWeekend() || $days->gt($today) || isset($holidays[$days->format('Y-m-d')])) {
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

            $activeStageEtudiantIds = $this->activeStagesOnDate($dateKey)
                ->distinct('etudiant_id')->pluck('etudiant_id')->values()->all();

            $absentEtudiantIds = array_values(array_diff($activeStageEtudiantIds, $presentEtudiantIds));
            if (!empty($absentEtudiantIds)) {
                $etudiantUsers = Etudiant::whereIn('id', $absentEtudiantIds)->with('user')->get();
                foreach ($etudiantUsers as $et) {
                    // ✅ Ne pas compter un jour corrigé (exception)
                    if (isset($exceptionsByKey[($et->user?->id ?? 0) . ':' . $dateKey])) continue;
                    // ✅ Absent uniquement si la date de début de pointage effective est passée
                    if ($days->gte($this->debutPointage($et->user))) {
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
                    // ✅ Ne pas compter un jour corrigé (exception)
                    if (isset($exceptionsByKey[$uid . ':' . $dateKey])) continue;
                    $empStartDate = $employeeStartDateById[$uid] ?? $this->systemStartDate();
                    if ($days->gte($empStartDate)) {
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
        [$startDate, $endDate] = $this->resolvePeriodRange($period, $dateFrom, $dateTo);

        // ✅ Borner la date de départ à l'activation du système
        $systemStart = $this->systemStartDate();
        if ($startDate->lt($systemStart)) {
            $startDate = $systemStart->copy();
        }

        $today = today()->endOfDay();

        $holidays = $this->getActiveHolidaysInRange($startDate, $endDate);

        $absentCountByUserName = [];
        $exceptionsByKey = $this->getExceptionKeysInRange($startDate, $endDate);

        $employeeIds = User::whereHas('personnel', function ($query) {
            $query->where('personnable_type', Employe::class);
        })
            ->where('status', 'actif')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->pluck('id')
            ->values()
            ->all();

        $employeeNameById = User::with('personnel')->whereIn('id', $employeeIds)->get()->pluck('name', 'id')->toArray();
        $employeeStartDateById = User::with('personnel')->whereIn('id', $employeeIds)->get()
            ->mapWithKeys(fn($u) => [$u->id => $this->debutPointage($u)])
            ->all();

        $attendanceDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
            ->select(['attendance_date', 'etudiant_id', 'user_id', 'first_check_in_at'])
            ->get()
            ->groupBy(fn($d) => Carbon::parse($d->attendance_date)->format('Y-m-d'));

        $days = $startDate->copy();
        while ($days->lte($endDate)) {

            // ✅ Ignorer week-ends, jours futurs et jours fériés
            if ($days->isWeekend() || $days->gt($today) || isset($holidays[$days->format('Y-m-d')])) {
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
            $activeStageEtudiantIds = $this->activeStagesOnDate($dateKey)
                ->distinct('etudiant_id')->pluck('etudiant_id')->values()->all();

            $absentEtudiantIds = array_values(array_diff($activeStageEtudiantIds, $presentEtudiantIds));
            if (!empty($absentEtudiantIds)) {
                $etudiantUsers = Etudiant::whereIn('id', $absentEtudiantIds)->with('user')->get();
                foreach ($etudiantUsers as $et) {
                    // ✅ Ne pas compter un jour corrigé (exception)
                    if (isset($exceptionsByKey[($et->user?->id ?? 0) . ':' . $dateKey])) continue;
                    // ✅ Absent uniquement si la date de début de pointage effective est passée
                    if ($days->gte($this->debutPointage($et->user))) {
                        $name = $et->user?->name ?? 'Inconnu';
                        $absentCountByUserName[$name] = ($absentCountByUserName[$name] ?? 0) + 1;
                    }
                }
            }

            foreach ($employeeIds as $uid) {
                if (!in_array($uid, $presentEmployeeIds)) {
                    // ✅ Ne pas compter un jour corrigé (exception)
                    if (isset($exceptionsByKey[$uid . ':' . $dateKey])) continue;
                    // ✅ Absent uniquement si la date de début de pointage effective est passée
                    $empStartDate = $employeeStartDateById[$uid] ?? $this->systemStartDate();
                    if ($days->gte($empStartDate)) {
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

    /**
     * Calcule en direct les absences (stagiaires + employés) pour une plage,
     * en respectant les jours de présence des stages (exclusion jours de repos).
     * Retourne une pagination de lignes : user, attendance_date, stage/site.
     */
    public function getAbsenceRows(string $dateFrom, string $dateTo, array $filters = []): LengthAwarePaginator
    {
        $startDate = Carbon::parse($dateFrom)->startOfDay();
        $endDate   = Carbon::parse($dateTo)->endOfDay();

        $systemStart = $this->systemStartDate();
        if ($startDate->lt($systemStart)) {
            $startDate = $systemStart->copy();
        }

        $today = today()->endOfDay();
        $holidays = $this->getActiveHolidaysInRange($startDate, $endDate);

        $exceptionsByKey = $this->getExceptionKeysInRange($startDate, $endDate);

        $userId    = $filters['user_id'] ?? null;
        $siteId    = $filters['site_id'] ?? null;
        $school    = $filters['school'] ?? null;

        $employeeIds = User::whereHas('personnel', function ($query) {
            $query->where('personnable_type', Employe::class);
        })
            ->where('status', 'actif')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->pluck('id')
            ->values()
            ->all();

        $employees = User::with('personnel')->whereIn('id', $employeeIds)->get();

        $attendanceDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startDate, $endDate])
            ->weekdays()
            ->select(['id', 'attendance_date', 'etudiant_id', 'user_id', 'first_check_in_at', 'stage_id'])
            ->get()
            ->groupBy(fn($d) => Carbon::parse($d->attendance_date)->format('Y-m-d'));

        $rows = [];

        $days = $startDate->copy();
        while ($days->lte($endDate)) {
            if ($days->isWeekend() || $days->gt($today) || isset($holidays[$days->format('Y-m-d')])) {
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

            // ── Stagiaires attendus ce jour (jours de travail du stage respectés) ──
            $stagesForDay = $this->activeStagesOnDate($dateKey)
                ->with(['site:id,name', 'etudiant.user'])
                ->get();

            foreach ($stagesForDay as $stage) {
                $etudiantId = $stage->etudiant_id;
                if ($userId && $stage->etudiant->user_id !== $userId) continue;
                if ($school && ($stage->etudiant->ecole ?? null) !== $school) continue;
                if ($siteId && ($stage->site?->id ?? null) !== (int) $siteId) continue;

                if (in_array($etudiantId, $presentEtudiantIds)) continue;

                $user = $stage->etudiant?->user;
                if (!$user || $user->status !== 'actif') continue;
                // ✅ Pas d'absence avant la date de début de pointage effective
                if ($days->lt($this->debutPointage($user))) continue;

                // ✅ Ne pas compter un jour corrigé (exception)
                if (isset($exceptionsByKey[$user->id . ':' . $dateKey])) continue;

                $rows[] = [
                    'user'    => $user,
                    'group'   => 'etudiant',
                    'date'    => Carbon::parse($dateKey),
                    'stage'   => $stage,
                ];
            }

            // ── Employés actifs ce jour ──
            foreach ($employees as $employee) {
                if ($userId && $employee->id !== (int) $userId) continue;
                if (in_array($employee->id, $presentEmployeeIds)) continue;
                // ✅ Pas d'absence avant la date de début de pointage effective
                if ($days->lt($this->debutPointage($employee))) continue;

                // ✅ Ne pas compter un jour corrigé (exception)
                if (isset($exceptionsByKey[$employee->id . ':' . $dateKey])) continue;

                $rows[] = [
                    'user'    => $employee,
                    'group'   => 'employe',
                    'date'    => Carbon::parse($dateKey),
                    'stage'   => null,
                ];
            }

            $days->addDay();
        }

        $rows = collect($rows)->sortByDesc('date')->values();

        $page      = LengthAwarePaginator::resolveCurrentPage('absences_page');
        $perPage   = $filters['per_page'] ?? 10;
        $items     = $rows->forPage($page, $perPage)->values();
        $total     = $rows->count();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }
}
