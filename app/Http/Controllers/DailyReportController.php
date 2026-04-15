<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyReport\StoreDailyReportRequest;
use App\Models\AttendanceDay;
use App\Services\DailyReportService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DailyReportController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        // For employees, we might handle differently, but for now allow access
        if (!$etudiant && !$user->hasRole('employe')) {
            abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattache a une fiche etudiant.");
        }

        $period = $request->get('period', 'daily');
        $activeStage = $etudiant ? $this->dailyReportService->resolveActiveStageForUser($user) : null;

        if ($period === 'daily') {
            $attendanceDay = null;
            if ($etudiant && $activeStage) {
                $attendanceDay = AttendanceDay::where('stage_id', $activeStage->id)
                    ->whereDate('attendance_date', today())
                    ->first();
            } elseif ($user->hasRole('employe')) {
                $attendanceDay = AttendanceDay::where('user_id', $user->id)
                    ->whereDate('attendance_date', today())
                    ->first();
            }

            $todayReport = null;
            if ($etudiant && $activeStage) {
                $todayReport = $activeStage->dailyReports()
                    ->with(['items.task', 'reviews'])
                    ->whereDate('report_date', today())
                    ->first();
            }

            $taskItems = $todayReport
                ? $todayReport->items->whereNotNull('task_id')->keyBy('task_id')
                : collect();
            $freeItems = $todayReport
                ? $todayReport->items->whereNull('task_id')->values()
                : collect();

            return view('reports.index', compact(
                'activeStage',
                'attendanceDay',
                'todayReport',
                'taskItems',
                'freeItems',
                'period'
            ));
        } else {
            // For weekly/monthly, show history of reports
            $dateFrom = match ($period) {
                'weekly' => now()->startOfWeek(),
                'monthly' => now()->startOfMonth(),
                default => now()->startOfWeek()
            };

            $reports = collect();
            if ($etudiant && $activeStage) {
                $reports = $activeStage->dailyReports()
                    ->with(['items.task', 'reviews'])
                    ->where('report_date', '>=', $dateFrom)
                    ->where('report_date', '<=', now())
                    ->orderBy('report_date', 'desc')
                    ->get();
            }

            return view('reports.index', compact(
                'activeStage',
                'reports',
                'period'
            ));
        }
    }

    public function store(StoreDailyReportRequest $request)
    {
        try {
            $report = $this->dailyReportService->storeForToday($request->user(), $request->validated());
        } catch (ValidationException $exception) {
            throw $exception;
        }

        $message = $report->status === 'submitted'
            ? 'Rapport du jour soumis avec succes.'
            : 'Brouillon du rapport du jour enregistre.';

        return redirect()
            ->route('reports.index')
            ->with('success', $message);
    }
}
