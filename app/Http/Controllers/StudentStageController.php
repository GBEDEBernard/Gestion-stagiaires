<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Services\DailyReportService;
use Illuminate\Http\Request;

class StudentStageController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService
    ) {
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattache a une fiche etudiant.");

        $activeStage = $this->dailyReportService->resolveActiveStageForUser($user);
        $attendanceDay = $activeStage
            ? AttendanceDay::where('stage_id', $activeStage->id)
                ->whereDate('attendance_date', today())
                ->first()
            : null;
        $todayReport = $activeStage
            ? $activeStage->dailyReports()
                ->with(['reviews.reviewer'])
                ->whereDate('report_date', today())
                ->first()
            : null;

        return view('student.stage', [
            'activeStage' => $activeStage,
            'attendanceDay' => $attendanceDay,
            'todayReport' => $todayReport,
            'tasks' => $activeStage?->tasks ?? collect(),
            'completedTasksCount' => $activeStage?->tasks->where('status', 'completed')->count() ?? 0,
            'openTasksCount' => $activeStage?->tasks->where('status', '!=', 'completed')->count() ?? 0,
        ]);
    }
}
