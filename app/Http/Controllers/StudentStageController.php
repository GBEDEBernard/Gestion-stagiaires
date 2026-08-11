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

        // ==================== Indicateurs calculés en temps réel ====================
        $joursRestants = 0;
        $presenceSemaine = 0;
        $joursPresentSemaine = 0;
        $joursTrackesSemaine = 0;
        $progressionStage = 0;

        if ($activeStage) {
            // Jours restants avant la fin du stage
            $joursRestants = $activeStage->date_fin
                ? max(0, $activeStage->date_fin->startOfDay()->diffInDays(now()->startOfDay()))
                : 0;

            // Présence de la semaine : jours pointés / jours enregistrés (ou jours ouvrés écoulés)
            $attendanceSemaine = AttendanceDay::where('stage_id', $activeStage->id)
                ->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->get();
            $joursTrackesSemaine = $attendanceSemaine->count();
            $joursPresentSemaine = $attendanceSemaine->whereNotNull('first_check_in_at')->count();

            if ($joursTrackesSemaine > 0) {
                $presenceSemaine = round(($joursPresentSemaine / $joursTrackesSemaine) * 100);
            }

            // Progression globale du stage (jours écoulés / durée totale)
            if ($activeStage->date_debut && $activeStage->date_fin) {
                $dureeTotale = $activeStage->date_debut->startOfDay()->diffInDays($activeStage->date_fin->startOfDay());
                if ($dureeTotale > 0) {
                    $joursEcoules = now()->startOfDay()->diffInDays($activeStage->date_debut->startOfDay());
                    $progressionStage = (int) round(($joursEcoules / $dureeTotale) * 100);
                    $progressionStage = max(0, min(100, $progressionStage));
                }
            }
        }

        return view('student.stage', [
            'activeStage' => $activeStage,
            'attendanceDay' => $attendanceDay,
            'todayReport' => $todayReport,
            'tasks' => $activeStage?->tasks ?? collect(),
            'completedTasksCount' => $activeStage?->tasks->where('status', 'completed')->count() ?? 0,
            'openTasksCount' => $activeStage?->tasks->where('status', '!=', 'completed')->count() ?? 0,
            'joursRestants' => $joursRestants,
            'presenceSemaine' => $presenceSemaine,
            'joursPresentSemaine' => $joursPresentSemaine,
            'joursTrackesSemaine' => $joursTrackesSemaine,
            'progressionStage' => $progressionStage,
        ]);
    }
}
