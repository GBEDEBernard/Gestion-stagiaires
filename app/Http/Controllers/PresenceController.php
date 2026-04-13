<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Services\AdminPresenceService;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PresenceController extends Controller
{
    public function __construct(
        private PresenceService $presenceService,
        private AdminPresenceService $adminPresenceService
    ) {}

    /**
     * Affiche la page de pointage avec statut actuel.
     */
    public function pointage(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattaché à une fiche étudiant.");

        // Trouve le stage actif (le plus récent en cours)
        $activeStage = $etudiant->stages()
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->with('site', 'typestage')
            ->orderByDesc('date_debut')
            ->first();

        if (!$activeStage) {
            return view('presence.pointage', compact('activeStage'));
        }

        // Statut du jour
        $attendanceDay = AttendanceDay::where('stage_id', $activeStage->id)
            ->whereDate('attendance_date', today())
            ->first();

        return view('presence.pointage', compact('activeStage', 'attendanceDay'));
    }

    /**
     * Enregistre l'arrivée (check-in).
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy_meters' => 'nullable|numeric|min:0',
            'device_fingerprint' => 'required|string',
        ]);

        try {
            $user = $request->user();
            $stage = $user->etudiant->stages()->findOrFail($request->stage_id);

            $event = $this->presenceService->registerCheckIn($stage, $user, $request->all());

            if ($event->status === 'rejected') {
                return redirect()->route('presence.pointage')
                    ->with('rejection_reason', $event->rejection_reason)
                    ->with('reason_code', $event->reason_code);
            }

            return redirect()->route('presence.pointage')
                ->with(
                    'success',
                    $event->status === 'approved'
                        ? '✅ Pointage d\'arrivée enregistré avec succès.'
                        : '⚠️ Pointage enregistré mais nécessite validation.'
                );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Check-in failed', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return redirect()->back()->with('error', 'Erreur lors du pointage. Réessayez.');
        }
    }

    /**
     * Enregistre le départ (check-out).
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy_meters' => 'nullable|numeric|min:0',
            'device_fingerprint' => 'required|string',
        ]);

        try {
            $user = $request->user();
            $stage = $user->etudiant->stages()->findOrFail($request->stage_id);

            $event = $this->presenceService->registerCheckOut($stage, $user, $request->all());

            if ($event->status === 'rejected') {
                return redirect()->route('presence.pointage')
                    ->with('rejection_reason', $event->rejection_reason)
                    ->with('reason_code', $event->reason_code);
            }

            return redirect()->route('presence.pointage')
                ->with(
                    'success',
                    $event->status === 'approved'
                        ? '✅ Pointage de départ enregistré avec succès.'
                        : '⚠️ Pointage enregistré mais nécessite validation.'
                );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Check-out failed', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return redirect()->back()->with('error', 'Erreur lors du pointage. Réessayez.');
        }
    }

    /**
     * Historique des présences.
     */
    public function historique(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattaché à une fiche étudiant.");

        $period = $request->get('period', 'week');

        $dateFrom = match ($period) {
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subWeek()
        };

        $filters = [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'etudiant_id' => $etudiant->id,
        ];

        $attendanceDaysQuery = $this->adminPresenceService->listAttendanceDays($filters, 100)
            ->with(['stage.site', 'anomalies']);

        $attendanceDays = $attendanceDaysQuery->get()
            ->groupBy(fn($day) => $day->attendance_date->format('Y-W'));

        return view('presence.historique', compact('attendanceDays', 'period'));
    }
}
