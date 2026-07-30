<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Services\DailyReportService;
use App\Services\UserProfileLinkService;
use Illuminate\Http\Request;

class StudentStageController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService,
        protected UserProfileLinkService $profileLinkService
    ) {
    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattache a une fiche etudiant.");

        $activeStage = $this->dailyReportService->resolveActiveStageForUser($user);
        abort_if(!$activeStage, 404, "Aucun stage actif trouve.");

        $activeStage->update(['theme' => $validated['theme']]);

        return back()->with('success', 'Theme du stage mis a jour avec succes.');
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

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
