<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Models\Task;
use App\Services\DailyReportService;
use App\Services\UserProfileLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        if (!$etudiant) {
            if ($user && $user->hasAnyRole(['admin', 'superviseur'])) {
                return redirect()->route('dashboard');
            }
            abort(403, "Votre compte n'est pas encore rattaché à une fiche étudiant.");
        }

        $activeStage = $this->dailyReportService->resolveActiveStageForUser($user);
        abort_if(!$activeStage, 404, "Aucun stage actif trouve.");

        $activeStage->update(['theme' => $validated['theme']]);

        return back()->with('success', 'Theme du stage mis a jour avec succes.');
    }

    public function uploadFinalReport(Request $request)
    {
        $user = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        if (!$etudiant) {
            abort(403, "Votre compte n'est pas encore rattaché à une fiche étudiant.");
        }

        $activeStage = $this->dailyReportService->resolveActiveStageForUser($user);
        abort_if(!$activeStage, 404, "Aucun stage actif trouve.");

        $validated = $request->validate([
            'final_report' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($activeStage->final_report_path) {
            Storage::disk('public')->delete($activeStage->final_report_path);
        }

        $path = $request->file('final_report')->store('final_reports', 'public');

        $activeStage->update([
            'final_report_path' => $path,
            'final_report_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Rapport de fin de stage depose avec succes.');
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        if (!$etudiant) {
            if ($user && $user->hasAnyRole(['admin', 'superviseur'])) {
                return redirect()->route('dashboard')
                    ->with('info', "Vous êtes administrateur : redirection vers votre espace de travail.");
            }
            abort(403, "Votre compte n'est pas encore rattaché à une fiche étudiant.");
        }

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
        $dureeTotale = 0;
        $joursEcoules = 0;
        $presenceSemaine = 0;
        $joursPresentSemaine = 0;
        $joursTrackesSemaine = 0;
        $progressionStage = 0;

        if ($activeStage) {
            // Carbon 3 : diffInDays est signé (positif si $a est avant $b).
            // On utilise $debut->diffInDays($now) pour les jours écoulés et
            // $now->diffInDays($fin) pour les jours restants.
            $now = now()->startOfDay();
            $debut = $activeStage->date_debut?->startOfDay();
            $fin = $activeStage->date_fin?->startOfDay();

            // Durée totale du stage en jours calendaires
            $dureeTotale = $debut && $fin ? (int) $debut->diffInDays($fin) : 0;

            // Jours restants : compte à rebours (date_fin - aujourd'hui)
            $joursRestants = $fin ? max(0, (int) $now->diffInDays($fin)) : 0;

            // Jours écoulés depuis le début (0 si le stage n'a pas commencé)
            $joursEcoules = $debut ? max(0, (int) $debut->diffInDays($now)) : 0;

            // Présence de la semaine : jours pointés / jours enregistrés (ou jours ouvrés écoulés)
            $attendanceSemaine = AttendanceDay::where('stage_id', $activeStage->id)
                ->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->get();
            $joursTrackesSemaine = $attendanceSemaine->count();
            $joursPresentSemaine = $attendanceSemaine->whereNotNull('first_check_in_at')->count();

            if ($joursTrackesSemaine > 0) {
                $presenceSemaine = round(($joursPresentSemaine / $joursTrackesSemaine) * 100);
            }

            // Progression globale du stage : 0 % au jour 1, 100 % le dernier jour
            $progressionStage = $dureeTotale > 0
                ? min(100, (int) round(($joursEcoules / $dureeTotale) * 100))
                : 0;
        }

        // ==================== Tâches du stagiaire (participant uniquement) ====================
        // Seules les tâches qu'il a créées ou qui lui ont été assignées sont montrées,
        // pour rester cohérent avec l'espace de travail (workspace).
        $tasks = Task::where('stage_id', $activeStage?->id)
            ->visibleTo($user)
            ->with([
                'dailyReports' => fn($q) => $q
                    ->with(['user', 'etudiant.user'])
                    ->latest('report_date')
                    ->limit(5),
            ])
            ->latest('updated_at')
            ->get();

        return view('student.stage', [
            'activeStage' => $activeStage,
            'attendanceDay' => $attendanceDay,
            'todayReport' => $todayReport,
            'tasks' => $tasks,
            'completedTasksCount' => $tasks->where('status', 'completed')->count(),
            'openTasksCount' => $tasks->where('status', '!=', 'completed')->count(),
            'joursRestants' => $joursRestants,
            'dureeTotale' => $dureeTotale,
            'joursEcoules' => $joursEcoules,
            'presenceSemaine' => $presenceSemaine,
            'joursPresentSemaine' => $joursPresentSemaine,
            'joursTrackesSemaine' => $joursTrackesSemaine,
            'progressionStage' => $progressionStage,
        ]);
    }
}
