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
     * Prépare la validation du check-in (stocke en session, affiche page validation).
     */
    public function prepareCheckIn(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy_meters' => 'nullable|numeric|min:0',
            'device_fingerprint' => 'required|string',
        ]);

        $user = $request->user();
        $etudiant = $user->etudiant;
        $stage = $etudiant->stages()->findOrFail($request->stage_id);

        $previewData = [
            'etudiant_name' => $etudiant->nom . ' ' . $etudiant->prenom,
            'site_name' => $stage->site?->name ?? 'Site principal',
            'theme' => $stage->theme,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy_meters ?? 'N/A',
            'pointage_time' => now()->format('H:i'),
            'type' => 'arrivée',
        ];

        // Calculer distance si geofence disponible
        $geofence = $stage->site?->geofences()->where('is_active', true)->first();
        if ($geofence) {
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $geofence->center_latitude,
                $geofence->center_longitude
            );
            $previewData['distance'] = $distance;
        }

        // Stocker données complètes pour confirmation
        session(['pending_pointage' => [
            'type' => 'check_in',
            'stage_id' => $request->stage_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy_meters' => $request->accuracy_meters,
            'device_fingerprint' => $request->device_fingerprint,
            'device_uuid' => $request->device_uuid ?? '',
            'device_label' => $request->device_label ?? '',
            'platform' => $request->platform ?? '',
            'browser' => $request->browser ?? '',
            'app_version' => $request->app_version ?? '',
        ]]);

        return view('presence.validate', $previewData);
    }

    /**
     * Prépare la validation du check-out (stocke en session, affiche page validation).
     */
    public function prepareCheckOut(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy_meters' => 'nullable|numeric|min:0',
            'device_fingerprint' => 'required|string',
        ]);

        $user = $request->user();
        $etudiant = $user->etudiant;
        $stage = $etudiant->stages()->findOrFail($request->stage_id);

        $previewData = [
            'etudiant_name' => $etudiant->nom . ' ' . $etudiant->prenom,
            'site_name' => $stage->site?->name ?? 'Site principal',
            'theme' => $stage->theme,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy_meters ?? 'N/A',
            'pointage_time' => now()->format('H:i'),
            'type' => 'départ',
        ];

        // Calculer distance si geofence disponible
        $geofence = $stage->site?->geofences()->where('is_active', true)->first();
        if ($geofence) {
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $geofence->center_latitude,
                $geofence->center_longitude
            );
            $previewData['distance'] = $distance;
        }

        // Stocker données complètes pour confirmation
        session(['pending_pointage' => [
            'type' => 'check_out',
            'stage_id' => $request->stage_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy_meters' => $request->accuracy_meters,
            'device_fingerprint' => $request->device_fingerprint,
            'device_uuid' => $request->device_uuid ?? '',
            'device_label' => $request->device_label ?? '',
            'platform' => $request->platform ?? '',
            'browser' => $request->browser ?? '',
            'app_version' => $request->app_version ?? '',
        ]]);

        return view('presence.validate', $previewData);
    }

    /**
     * Affiche la page de validation (récupère depuis session).
     */
    public function validate(Request $request)
    {
        $pending = session('pending_pointage');
        if (!$pending) {
            return redirect()->route('presence.pointage')->with('error', 'Aucune donnée de pointage en attente.');
        }

        $user = $request->user();
        $etudiant = $user->etudiant;
        $stage = $etudiant->stages()->findOrFail($pending['stage_id']);

        $previewData = [
            'etudiant_name' => $etudiant->nom . ' ' . $etudiant->prenom,
            'site_name' => $stage->site?->name ?? 'Site principal',
            'theme' => $stage->theme,
            'latitude' => $pending['latitude'],
            'longitude' => $pending['longitude'],
            'accuracy' => $pending['accuracy_meters'] ?? 'N/A',
            'pointage_time' => now()->format('H:i'),
            'type' => $pending['type'] === 'check_in' ? 'arrivée' : 'départ',
            'form_data' => $pending,
        ];

        // Distance
        $geofence = $stage->site?->geofences()->where('is_active', true)->first();
        if ($geofence) {
            $distance = $this->calculateDistance(
                $pending['latitude'],
                $pending['longitude'],
                $geofence->center_latitude,
                $geofence->center_longitude
            );
            $previewData['distance'] = $distance;
        }

        return view('presence.validate', $previewData);
    }

    /**
     * Confirme et enregistre le pointage depuis session.
     */
    public function confirm(Request $request)
    {
        $pending = session('pending_pointage');
        if (!$pending) {
            return redirect()->route('presence.pointage')->with('error', 'Données de pointage expirées.');
        }

        try {
            $user = $request->user();
            $stage = $user->etudiant->stages()->findOrFail($pending['stage_id']);

            $data = $pending;
            $data['device_uuid'] = $pending['device_uuid'];
            $data['device_label'] = $pending['device_label'];
            $data['platform'] = $pending['platform'];
            $data['browser'] = $pending['browser'];
            $data['app_version'] = $pending['app_version'];

            if ($pending['type'] === 'check_in') {
                $event = $this->presenceService->registerCheckIn($stage, $user, $data);
            } else {
                $event = $this->presenceService->registerCheckOut($stage, $user, $data);
            }

            // Nettoyer session
            request()->session()->forget('pending_pointage');

            if ($event->status === 'rejected') {
                return redirect()->route('presence.pointage')
                    ->with('rejection_reason', $event->rejection_reason)
                    ->with('reason_code', $event->reason_code);
            }

            $message = $event->status === 'approved'
                ? '✅ Pointage confirmé et enregistré !'
                : '⚠️ Pointage enregistré mais nécessite validation admin.';

            return redirect()->route('presence.historique')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Pointage confirm failed', ['error' => $e->getMessage()]);
            request()->session()->forget('pending_pointage');
            return redirect()->route('presence.pointage')->with('error', 'Erreur lors de la confirmation.');
        }
    }

    /**
     * Enregistre l'arrivée (check-in) - Ancienne méthode (compatibilité).
     */
    public function checkIn(Request $request)
    {
        return $this->prepareCheckIn($request);
    }

    /**
     * Enregistre le départ (check-out) - Ancienne méthode (compatibilité).
     */
    public function checkOut(Request $request)
    {
        return $this->prepareCheckOut($request);
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

        $attendanceEvents = \App\Models\AttendanceEvent::where('etudiant_id', $etudiant->id)
            ->whereBetween('occurred_at', [$dateFrom, now()])
            ->with(['user', 'site', 'stage'])
            ->orderByDesc('occurred_at')
            ->get();

        return view('presence.historique', compact('attendanceEvents', 'period'));
    }

    /**
     * Calcule distance entre 2 points GPS (mètres).
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $earthRadius = 6371000; // Rayon Terre en mètres

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }
}
