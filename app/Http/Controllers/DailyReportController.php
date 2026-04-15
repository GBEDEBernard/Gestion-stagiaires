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

        abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattache a une fiche etudiant.");

        $period = $request->get('period', 'daily');
        $activeStage = $this->dailyReportService->resolveActiveStageForUser($user);

        if ($period === 'daily') {
            $attendanceDay = $activeStage
                ? AttendanceDay::where('stage_id', $activeStage->id)
                ->whereDate('attendance_date', today())
                ->first()
                : null;
            $todayReport = $activeStage
                ? $activeStage->dailyReports()
                ->with(['items.task', 'reviews'])
                ->whereDate('report_date', today())
                ->first()
                : null;
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

            $reports = $activeStage
                ? $activeStage->dailyReports()
                ->with(['items.task', 'reviews'])
                ->where('report_date', '>=', $dateFrom)
                ->where('report_date', '<=', now())
                ->orderBy('report_date', 'desc')
                ->get()
                : collect();

            return view('reports.history', compact(
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
