<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use Illuminate\Validation\ValidationException;
use App\Services\AttendanceCorrectionService;
use App\Models\Activity;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceException;
use App\Models\Etudiant;
use App\Models\Employe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAttendanceTrackingController extends Controller
{
    /**
     * Vue globale du suivi des pointages.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'day');
        $dateFilter = $request->get('date', now()->format('Y-m-d'));
        $filterDate = Carbon::createFromFormat('Y-m-d', $dateFilter);
        $selectedUserId = $request->get('user_id');

        $data = match ($period) {
            'week' => $this->getWeeklyData($filterDate),
            'month' => $this->getMonthlyData($filterDate),
            'year' => $this->getYearlyData($filterDate),
            default => $this->getDailyData($filterDate),
        };

        $students = Etudiant::with('user')->get()->filter(function ($etudiant) {
            return $etudiant->user !== null;
        })->map(function ($etudiant) {
            return [
                'id' => $etudiant->user->id,
                'name' => $etudiant->user->name . ' (Stagiaire)',
                'type' => 'student'
            ];
        });

        $employees = $this->employeeUsersQuery()
            ->with('personnel.personnable')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' (Employé - ' . (optional(optional($user->personnel)->personnable)->domaine->nom ?? 'N/A') . ')',
                    'type' => 'employee'
                ];
            });

        $allUsers = $students->concat($employees)->sortBy('name')->values();

        $userStats = null;
        if ($selectedUserId) {
            $selectedUser = User::find($selectedUserId);
            if ($selectedUser) {
                $userStats = app(\App\Services\AdminPresenceService::class)->getUserDetailedStats($selectedUser->id, $period);
            }
        }

        return view('attendance.tracking.index', array_merge($data, [
            'period' => $period,
            'filterDate' => $filterDate,
            'allUsers' => $allUsers,
            'selectedUserId' => $selectedUserId,
            'userStats' => $userStats
        ]));
    }

    /**
     * Données pour un jour spécifique.
     */
    protected function getDailyData(Carbon $date): array
    {
        $studentDays = AttendanceDay::forActiveUsers()->whereDate('attendance_date', $date)
            ->whereNotNull('etudiant_id')
            ->with([
                'etudiant.user',
                'stage.site',
                'anomalies',
                'lateAnomaly',
                'checkInEvent.geofence.site',
            ])
            ->orderBy('etudiant_id')
            ->get();

        $employeeDays = AttendanceDay::forActiveUsers()->whereDate('attendance_date', $date)
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->with([
                'user',
                'stage.site',
                'anomalies',
                'lateAnomaly',
                'checkInEvent.geofence.site',
            ])
            ->orderBy('user_id')
            ->get();

        // Calcul des totaux réels (base attendue) - Étudiants en stage complet avec compte actif
        $activeEtudiantsIds = Etudiant::whereHas('stages', function ($q) use ($date) {
            $q->where('date_debut', '<=', $date)
                ->where('date_fin', '>=', $date);
        })->whereHas('user', fn ($q) => $q->where('status', 'actif'))->pluck('id');

        $studentTotal = $activeEtudiantsIds->count();

        $studentPresentIds = AttendanceDay::forActiveUsers()->whereDate('attendance_date', $date)
            ->whereIn('etudiant_id', $activeEtudiantsIds)
            ->whereNotNull('first_check_in_at')
            ->distinct('etudiant_id')
            ->pluck('etudiant_id');
        $studentPresent = $studentPresentIds->count();

        // Employés (utilisateurs avec fiche Employe ou rôle employe)
        $employeeUsersIds = $this->employeeUsersQuery()->pluck('id');

        $employeeTotal = $employeeUsersIds->count();

        $employeePresentIds = AttendanceDay::forActiveUsers()->whereDate('attendance_date', $date)
            ->whereNull('etudiant_id')
            ->whereIn('user_id', $employeeUsersIds)
            ->whereNotNull('first_check_in_at')
            ->distinct('user_id')
            ->pluck('user_id');
        $employeePresent = $employeePresentIds->count();

        $summary = [
            'student_total' => $studentTotal,
            'student_present' => $studentPresent,
            'employee_total' => $employeeTotal,
            'employee_present' => $employeePresent,
            'student_rate' => $studentTotal > 0 ? round(($studentPresent / $studentTotal) * 100) : 0,
            'employee_rate' => $employeeTotal > 0 ? round(($employeePresent / $employeeTotal) * 100) : 0,
        ];

        return [
            'attendanceStudents' => $studentDays,
            'attendanceEmployees' => $employeeDays,
            'summary' => $summary,
            'global_rate' => $summary['student_rate'] + $summary['employee_rate'] / 2, // Moyenne générale
            'displayDate' => $date->translatedFormat('d F Y'),
        ];
    }

    /**
     * Données pour une semaine.
     */
    protected function getWeeklyData(Carbon $date): array
    {
        $startOfWeek = $date->clone()->startOfWeek();
        $endOfWeek = $date->clone()->endOfWeek();

        $studentDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->whereNotNull('etudiant_id')
            ->with([
                'etudiant.user',
                'stage.site',
                'anomalies',
                'checkInEvent.geofence.site',
            ])
            ->get()
            ->groupBy('etudiant_id');

        $employeeDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->with([
                'user',
                'stage.site',
                'anomalies',
                'checkInEvent.geofence.site',
            ])
            ->get()
            ->groupBy('user_id');

        $studentSummary = $this->groupWeeklySummary($studentDays, 'etudiant');
        $employeeSummary = $this->groupWeeklySummary($employeeDays, 'user');

        return [
            'studentWeekData' => $studentSummary,
            'employeeWeekData' => $employeeSummary,
            'displayDate' => $startOfWeek->translatedFormat('d F') . ' - ' . $endOfWeek->translatedFormat('d F Y'),
            'weekStart' => $startOfWeek,
            'weekEnd' => $endOfWeek,
        ];
    }

    protected function groupWeeklySummary($grouped, string $relationKey): array
    {
        $summary = [];

        foreach ($grouped as $ownerId => $days) {
            $owner = $days->first()->{$relationKey};
            $totalLateMinutes = $days->sum('late_minutes');
            $presentDays = $days->filter(fn($d) => $d->first_check_in_at)->count();

            $summary[$ownerId] = [
                'owner' => $owner,
                'present_days' => $presentDays,
                'total_late_minutes' => $totalLateMinutes,
                'days' => $days->sortBy('attendance_date'),
            ];
        }

        return $summary;
    }

    /**
     * Données pour un mois.
     */
    protected function getMonthlyData(Carbon $date): array
    {
        $startOfMonth = $date->clone()->startOfMonth();
        $endOfMonth = $date->clone()->endOfMonth();

        $studentDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('etudiant_id')
            ->with([
                'etudiant.user',
                'stage.site',
                'checkInEvent.geofence.site',
            ])
            ->get()
            ->groupBy('etudiant_id');

        $employeeDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->with([
                'user',
                'stage.site',
                'checkInEvent.geofence.site',
            ])
            ->get()
            ->groupBy('user_id');

        $studentSummary = $this->groupMonthlySummary($studentDays, 'etudiant');
        $employeeSummary = $this->groupMonthlySummary($employeeDays, 'user');

        return [
            'studentMonthlySummary' => $studentSummary,
            'employeeMonthlySummary' => $employeeSummary,
            'displayDate' => $date->translatedFormat('F Y'),
            'monthStart' => $startOfMonth,
            'monthEnd' => $endOfMonth,
        ];
    }

    protected function groupMonthlySummary($grouped, string $relationKey): array
    {
        $summary = [];

        foreach ($grouped as $ownerId => $days) {
            $owner = $days->first()->{$relationKey};
            $totalLateMinutes = $days->sum('late_minutes');
            $presentDays = $days->filter(fn($d) => $d->first_check_in_at)->count();
            $totalWorkedMinutes = $days->sum('worked_minutes');

            $summary[$ownerId] = [
                'owner' => $owner,
                'present_days' => $presentDays,
                'total_late_minutes' => $totalLateMinutes,
                'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
                'days' => $days->sortBy('attendance_date'),
            ];
        }

        return $summary;
    }

    /**
     * Données pour une année.
     */
    protected function getYearlyData(Carbon $date): array
    {
        $startOfYear = $date->clone()->startOfYear();
        $endOfYear = $date->clone()->endOfYear();

        $studentDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startOfYear, $endOfYear])
            ->whereNotNull('etudiant_id')
            ->with([
                'etudiant.user',
                'stage.site',
                'anomalies',
                'checkInEvent.geofence.site',
            ])
            ->get()
            ->groupBy('etudiant_id');

        $employeeDays = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$startOfYear, $endOfYear])
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->with([
                'user',
                'stage.site',
                'anomalies',
                'checkInEvent.geofence.site',
            ])
            ->get()
            ->groupBy('user_id');

        $studentSummary = $this->groupYearlySummary($studentDays, 'etudiant');
        $employeeSummary = $this->groupYearlySummary($employeeDays, 'user');

        return [
            'studentYearlySummary' => $studentSummary,
            'employeeYearlySummary' => $employeeSummary,
            'displayDate' => $date->year,
            'yearStart' => $startOfYear,
            'yearEnd' => $endOfYear,
        ];
    }

    protected function groupYearlySummary($grouped, string $relationKey): array
    {
        $summary = [];

        foreach ($grouped as $ownerId => $days) {
            $owner = $days->first()->{$relationKey};
            $totalLateMinutes = $days->sum('late_minutes');
            $presentDays = $days->filter(fn($d) => $d->first_check_in_at)->count();
            $totalWorkedMinutes = $days->sum('worked_minutes');
            $anomalies = $days->sum(fn($d) => $d->anomalies->count());

            $summary[$ownerId] = [
                'owner' => $owner,
                'present_days' => $presentDays,
                'total_late_minutes' => $totalLateMinutes,
                'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
                'anomalies_count' => $anomalies,
                'days' => $days->sortBy('attendance_date'),
            ];
        }

        return $summary;
    }

    /**
     * Export CSV des données.
     */
    public function export(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateFilter = $request->get('date', now()->format('Y-m-d'));
        $filterDate = Carbon::createFromFormat('Y-m-d', $dateFilter);

        $filename = 'suivi-pointages-' . $period . '-' . $filterDate->format('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($period, $filterDate) {
            $file = fopen('php://output', 'w');

            if ($period === 'month') {
                fputcsv($file, ['Suivi - ' . $filterDate->translatedFormat('F Y')]);
                fputcsv($file, []);
                fputcsv($file, ['Nom', 'Jours présents', 'Retard total (min)', 'Heures travaillées']);

                $data = $this->getMonthlyData($filterDate);
                foreach ($data['studentMonthlySummary'] as $summary) {
                    fputcsv($file, [
                        $summary['owner']->user->name ?? 'N/A',
                        $summary['present_days'],
                        $summary['total_late_minutes'],
                        $summary['total_worked_hours'],
                    ]);
                }
                foreach ($data['employeeMonthlySummary'] as $summary) {
                    fputcsv($file, [
                        $summary['owner']->name ?? 'N/A',
                        $summary['present_days'],
                        $summary['total_late_minutes'],
                        $summary['total_worked_hours'],
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Historique des présences pour un utilisateur spécifique (admin).
     */
    public function userHistorique(Request $request, User $user)
    {
        $period = $request->get('period', 'month');

        $etudiant = $user->etudiant;
        $ownerType = $etudiant ? 'etudiant' : 'user';
        $ownerId = $etudiant ? $etudiant->id : $user->id;

        $userStats = app(\App\Services\AdminPresenceService::class)->getUserDetailedStats($user->id, $period);

        $dateFrom = match ($period) {
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subWeek()
        };

        $filters = [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            $etudiant ? 'etudiant_id' : 'user_id' => $ownerId,
        ];

        $attendanceDaysQuery = app(\App\Services\AdminPresenceService::class)->listAttendanceDays($filters, 100)
            ->with(['stage.site', 'anomalies', 'dailyReports', 'checkInEvent.geofence.site']);

        $attendanceDays = $attendanceDaysQuery->get();

        // ── Jours d'absence détectés sur le graphe (pour les corriger) ─────────
        $chart = $userStats['chart_data'] ?? [];
        $absenceDates = [];
        foreach ($chart['dates'] ?? [] as $i => $date) {
            if (isset($chart['absences'][$i]) && (int) $chart['absences'][$i] === 1) {
                $absenceDates[] = [
                    'date' => $date,
                    'label' => Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY'),
                ];
            }
        }

        // ── Jours déjà corrigés (exceptions) ───────────────────────────────────
        $exceptions = AttendanceException::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$dateFrom->format('Y-m-d'), now()->format('Y-m-d')])
            ->with('creator')
            ->orderByDesc('attendance_date')
            ->get();

        return view('presence.historique', compact('attendanceDays', 'period', 'userStats', 'user', 'absenceDates', 'exceptions'));
    }

    /**
     * Utilisateurs considérés comme employés (qui doivent pointer) :
     *  - ceux liés à une fiche personnel de type Employé ;
     *  - ceux ayant le rôle `employe` (ex. un admin qui a aussi le rôle employe).
     */
    protected function employeeUsersQuery()
    {
        return User::query()
            ->where('status', 'actif')
            ->where(function ($q) {
                $q->whereHas('personnel', fn($p) => $p->where('personnable_type', Employe::class))
                    ->orWhereHas('roles', fn($r) => $r->where('name', 'employe'));
            });
    }

    /**
     * Corrige un jour d'absence : le jour n'est plus compté comme absence.
     */
    public function storeException(Request $request, User $user)
    {
        $validated = $request->validate([
            'attendance_date' => ['required', 'date'],
            'reason'          => ['nullable', 'string', 'max:1000'],
        ]);

        $date = Carbon::parse($validated['attendance_date'])->toDateString();

        // Refuser si un pointage (check-in) existe déjà ce jour-là
        $hasCheckIn = AttendanceDay::whereDate('attendance_date', $date)
            ->whereNotNull('first_check_in_at')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('etudiant_id', $user->etudiant?->id);
            })
            ->exists();

        if ($hasCheckIn) {
            return back()
                ->withInput()
                ->withErrors(['attendance_date' => 'Impossible de corriger ce jour : un pointage existe déjà.']);
        }

        AttendanceException::updateOrCreate(
            ['user_id' => $user->id, 'attendance_date' => $date],
            ['reason' => $validated['reason'] ?? null, 'created_by' => Auth::id()]
        );

        return back()->with('success', 'Absence du ' . Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') . ' corrigée. Ce jour ne sera plus compté comme absence.');
    }

    /**
     * Rétablit l'heure d'arrivée réelle d'une journée où le pointage a échoué.
     * Le retard cesse de compter, mais l'heure constatée reste consultable.
     */
    /**
     * Rétablit l'heure de départ d'une journée clôturée d'office.
     *
     * La personne a déclaré une heure sur son écran de pointage ; cette
     * déclaration n'a rien modifié. C'est ici qu'elle prend effet, si le
     * responsable la valide.
     */
    public function storeDepartureCorrection(Request $request, User $user, AttendanceDay $day)
    {
        $etudiantId = $user->etudiant?->id;
        $belongsToUser = ($day->user_id !== null && $day->user_id === $user->id)
            || ($etudiantId !== null && $day->etudiant_id === $etudiantId);

        if (!$belongsToUser) {
            abort(404);
        }

        $validated = $request->validate([
            'time'   => ['required', 'string'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'reason.required' => "Le motif est obligatoire : un volume horaire corrigé sans justification serait indéfendable.",
            'reason.min'      => "Le motif doit être un minimum explicite.",
        ]);

        try {
            app(AttendanceCorrectionService::class)
                ->applyCheckOut($user, $day, $validated['time'], $validated['reason'], $request->user());
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        Activity::create([
            'user_id'     => $request->user()->id,
            'action'      => 'Correction heure depart',
            'description' => "Journée du {$day->attendance_date->format('d/m/Y')} pour {$user->name} : départ rétabli à {$validated['time']}.",
        ]);

        return back()->with('success', "Heure de départ rétablie. Le volume horaire de cette journée a été recalculé.");
    }

    public function storeTimeCorrection(Request $request, User $user, AttendanceDay $day)
    {
        // Deux nulls ne prouvent pas une appartenance : comparer directement
        // etudiant_id laissait passer la journée d'un employé sous l'identifiant
        // d'un autre, et la correction aurait été inscrite au mauvais nom.
        $etudiantId = $user->etudiant?->id;
        $belongsToUser = ($day->user_id !== null && $day->user_id === $user->id)
            || ($etudiantId !== null && $day->etudiant_id === $etudiantId);

        if (!$belongsToUser) {
            abort(404);
        }

        $validated = $request->validate([
            'time'   => ['required', 'string'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'reason.required' => "Le motif est obligatoire : une ponctualité corrigée sans justification serait indéfendable.",
            'reason.min'      => "Le motif doit être un minimum explicite.",
        ]);

        try {
            app(AttendanceCorrectionService::class)
                ->apply($user, $day, $validated['time'], $validated['reason'], $request->user());
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        Activity::create([
            'user_id'     => $request->user()->id,
            'action'      => 'Correction heure arrivee',
            'description' => "Journée du {$day->attendance_date->format('d/m/Y')} pour {$user->name} : heure rétablie à {$validated['time']}.",
        ]);

        return back()->with('success', "Heure d'arrivée rétablie. Ce jour n'est plus compté comme un retard.");
    }

    /**
     * Annule une correction d'heure et restitue le pointage constaté.
     */
    public function destroyTimeCorrection(User $user, AttendanceCorrection $correction)
    {
        if ($correction->user_id !== $user->id) {
            abort(404);
        }

        app(AttendanceCorrectionService::class)->revert($correction);

        return back()->with('success', "Correction annulée. Le pointage constaté est rétabli.");
    }

    /**
     * Annule une correction d'absence.
     */
    public function destroyException(User $user, AttendanceException $exception)
    {
        abort_unless($exception->user_id === $user->id, 403);

        $exception->delete();

        return back()->with('success', 'Correction du ' . Carbon::parse($exception->attendance_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') . ' annulée.');
    }
}
