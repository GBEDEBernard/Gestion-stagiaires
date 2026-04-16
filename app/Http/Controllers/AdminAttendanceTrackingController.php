<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Models\Etudiant;
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

        $data = match ($period) {
            'week' => $this->getWeeklyData($filterDate),
            'month' => $this->getMonthlyData($filterDate),
            'year' => $this->getYearlyData($filterDate),
            default => $this->getDailyData($filterDate),
        };

        return view('attendance.tracking.index', array_merge($data, [
            'period' => $period,
            'filterDate' => $filterDate
        ]));
    }

    /**
     * Données pour un jour spécifique.
     */
    protected function getDailyData(Carbon $date): array
    {
        $studentDays = AttendanceDay::whereDate('attendance_date', $date)
            ->whereNotNull('etudiant_id')
            ->with(['etudiant.user', 'stage.site', 'anomalies'])
            ->orderBy('etudiant_id')
            ->get();

        $employeeDays = AttendanceDay::whereDate('attendance_date', $date)
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->where('user_id', '!=', Auth::id())
            ->with(['user', 'stage.site', 'anomalies'])
            ->orderBy('user_id')
            ->get();

        $summary = [
            'student_total' => $studentDays->count(),
            'student_present' => $studentDays->where('first_check_in_at', '!=', null)->count(),
            'employee_total' => $employeeDays->count(),
            'employee_present' => $employeeDays->where('first_check_in_at', '!=', null)->count(),
        ];

        return [
            'attendanceStudents' => $studentDays,
            'attendanceEmployees' => $employeeDays,
            'summary' => $summary,
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

        $studentDays = AttendanceDay::whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->whereNotNull('etudiant_id')
            ->with(['etudiant.user', 'stage.site', 'anomalies'])
            ->get()
            ->groupBy('etudiant_id');

        $employeeDays = AttendanceDay::whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->where('user_id', '!=', Auth::id())
            ->with(['user', 'stage.site', 'anomalies'])
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

        $studentDays = AttendanceDay::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('etudiant_id')
            ->with(['etudiant.user', 'stage.site'])
            ->get()
            ->groupBy('etudiant_id');

        $employeeDays = AttendanceDay::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->where('user_id', '!=', Auth::id())
            ->with(['user', 'stage.site'])
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

        $studentDays = AttendanceDay::whereBetween('attendance_date', [$startOfYear, $endOfYear])
            ->whereNotNull('etudiant_id')
            ->with(['etudiant.user', 'stage.site'])
            ->get()
            ->groupBy('etudiant_id');

        $employeeDays = AttendanceDay::whereBetween('attendance_date', [$startOfYear, $endOfYear])
            ->whereNotNull('user_id')
            ->whereNull('etudiant_id')
            ->where('user_id', '!=', Auth::id())
            ->with(['user', 'stage.site'])
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
                $startOfMonth = $filterDate->clone()->startOfMonth();
                $endOfMonth = $filterDate->clone()->endOfMonth();

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

        // Déterminer si c'est un étudiant ou un employé
        $etudiant = $user->etudiant;
        $ownerType = $etudiant ? 'etudiant' : 'user';
        $ownerId = $etudiant ? $etudiant->id : $user->id;

        // Stats détaillées via service (utiliser AdminPresenceService si disponible)
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
            ->with(['stage.site', 'anomalies', 'dailyReports']);

        $attendanceDays = $attendanceDaysQuery->get();

        return view('presence.historique', compact('attendanceDays', 'period', 'userStats', 'user'));
    }
}
