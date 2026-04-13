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
        $attendanceDays = AttendanceDay::whereDate('attendance_date', $date)
            ->with(['etudiant.user', 'stage.site', 'anomalies'])
            ->orderBy('etudiant_id')
            ->get();

        $summary = [
            'total' => $attendanceDays->count(),
            'present' => $attendanceDays->where('first_check_in_at', '!=', null)->count(),
            'absent' => $attendanceDays->where('first_check_in_at', null)->count(),
            'late' => $attendanceDays->where('arrival_status', 'late')->count(),
            'warning' => $attendanceDays->where('arrival_status', 'warning')->count(),
        ];

        return [
            'attendanceDays' => $attendanceDays,
            'summary' => $summary,
            'displayDate' => $date->format('d MMM Y'),
        ];
    }

    /**
     * Données pour une semaine.
     */
    protected function getWeeklyData(Carbon $date): array
    {
        $startOfWeek = $date->clone()->startOfWeek();
        $endOfWeek = $date->clone()->endOfWeek();

        $attendanceDays = AttendanceDay::whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->with(['etudiant.user', 'stage.site', 'anomalies'])
            ->get()
            ->groupBy(function ($day) {
                return $day->etudiant_id;
            });

        $weekSummary = [];
        foreach ($attendanceDays as $etudiantId => $days) {
            $totalLateMinutes = $days->sum('late_minutes');
            $presentDays = $days->filter(fn($d) => $d->first_check_in_at)->count();

            $weekSummary[$etudiantId] = [
                'present_days' => $presentDays,
                'total_late_minutes' => $totalLateMinutes,
                'days' => $days->sortBy('attendance_date'),
            ];
        }

        return [
            'weekData' => $weekSummary,
            'displayDate' => $startOfWeek->format('d MMM') . ' - ' . $endOfWeek->format('d MMM Y'),
            'weekStart' => $startOfWeek,
            'weekEnd' => $endOfWeek,
        ];
    }

    /**
     * Données pour un mois.
     */
    protected function getMonthlyData(Carbon $date): array
    {
        $startOfMonth = $date->clone()->startOfMonth();
        $endOfMonth = $date->clone()->endOfMonth();

        $attendanceDays = AttendanceDay::whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->with(['etudiant.user', 'stage.site'])
            ->get()
            ->groupBy(function ($day) {
                return $day->etudiant_id;
            });

        $monthlySummary = [];
        foreach ($attendanceDays as $etudiantId => $days) {
            $totalLateMinutes = $days->sum('late_minutes');
            $presentDays = $days->filter(fn($d) => $d->first_check_in_at)->count();
            $totalWorkedMinutes = $days->sum('worked_minutes');

            $monthlySummary[$etudiantId] = [
                'present_days' => $presentDays,
                'total_late_minutes' => $totalLateMinutes,
                'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
                'etudiant' => $days->first()->etudiant,
            ];
        }

        return [
            'monthlySummary' => $monthlySummary,
            'displayDate' => $date->translatedFormat('F Y'),
            'monthStart' => $startOfMonth,
            'monthEnd' => $endOfMonth,
        ];
    }

    /**
     * Données pour une année.
     */
    protected function getYearlyData(Carbon $date): array
    {
        $startOfYear = $date->clone()->startOfYear();
        $endOfYear = $date->clone()->endOfYear();

        $attendanceDays = AttendanceDay::whereBetween('attendance_date', [$startOfYear, $endOfYear])
            ->with(['etudiant.user', 'stage.site'])
            ->get()
            ->groupBy(function ($day) {
                return $day->etudiant_id;
            });

        $yearlySummary = [];
        foreach ($attendanceDays as $etudiantId => $days) {
            $totalLateMinutes = $days->sum('late_minutes');
            $presentDays = $days->filter(fn($d) => $d->first_check_in_at)->count();
            $totalWorkedMinutes = $days->sum('worked_minutes');
            $anomalies = $days->sum(fn($d) => $d->anomalies->count());

            $yearlySummary[$etudiantId] = [
                'present_days' => $presentDays,
                'total_late_minutes' => $totalLateMinutes,
                'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
                'anomalies_count' => $anomalies,
                'etudiant' => $days->first()->etudiant,
            ];
        }

        return [
            'yearlySummary' => $yearlySummary,
            'displayDate' => $date->year,
            'yearStart' => $startOfYear,
            'yearEnd' => $endOfYear,
        ];
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
                foreach ($data['monthlySummary'] as $summary) {
                    fputcsv($file, [
                        $summary['etudiant']->user->name ?? 'N/A',
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
}
