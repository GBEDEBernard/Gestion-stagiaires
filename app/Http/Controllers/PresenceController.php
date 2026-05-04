<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Services\AdminPresenceService;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        if ($etudiant) {
            // Logique pour stagiaire
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
        } else {
            // Logique pour employé - utilise la vue dédiée aux employés
            $domaine = $user->domaine;

            if (!$domaine) {
                abort(403, "Votre compte n'est pas rattaché à un domaine de travail.");
            }

            // Query today's attendance for employee
            $attendanceDay = AttendanceDay::where('user_id', $user->id)
                ->whereDate('attendance_date', today())
                ->first();

            return view('employee.presence.pointage', compact('attendanceDay', 'user'));
        }
    }

    /**
     * Prépare la validation du check-in (stocke en session, affiche page validation).
     */
    public function prepareCheckIn(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        if ($etudiant) {
            // Logique pour stagiaire
            $request->validate([
                'stage_id' => 'required|exists:stages,id',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'accuracy_meters' => 'nullable|numeric|min:0',
                'device_fingerprint' => 'required|string',
            ]);

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
                'user_type' => 'etudiant',
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
        } else {
            // Logique pour employé
            $request->validate([
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'accuracy_meters' => 'nullable|numeric|min:0',
                'device_fingerprint' => 'required|string',
            ]);

            $domaine = $user->domaine;
            abort_if(!$domaine, 403, "Votre compte n'est pas rattaché à un domaine de travail.");

            $previewData = [
                'user_name' => $user->name,
                'domaine_name' => $domaine->nom,
                'site_name' => 'Site principal',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy_meters ?? 'N/A',
                'pointage_time' => now()->format('H:i'),
                'type' => 'arrivée',
            ];

            // No geofence for employee preview (calculated later in service)
            session(['pending_pointage' => [
                'type' => 'check_in',
                'user_id' => $user->id,
                'user_type' => 'employe',
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
    }

    /**
     * Prépare la validation du check-out (stocke en session, affiche page validation).
     */
    public function prepareCheckOut(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        if ($etudiant) {
            // Logique pour stagiaire
            $request->validate([
                'stage_id' => 'required|exists:stages,id',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'accuracy_meters' => 'nullable|numeric|min:0',
                'device_fingerprint' => 'required|string',
            ]);

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
                'user_type' => 'etudiant',
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
        } else {
            // Logique pour employé
            $request->validate([
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'accuracy_meters' => 'nullable|numeric|min:0',
                'device_fingerprint' => 'required|string',
            ]);

            $domaine = $user->domaine;
            abort_if(!$domaine, 403, "Votre compte n'est pas rattaché à un domaine de travail.");

            $previewData = [
                'user_name' => $user->name,
                'domaine_name' => $domaine->nom,
                'site_name' => 'Site principal',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy_meters ?? 'N/A',
                'pointage_time' => now()->format('H:i'),
                'type' => 'départ',
            ];

            // No geofence for employee preview (calculated later in service)
            session(['pending_pointage' => [
                'type' => 'check_out',
                'user_id' => $user->id,
                'user_type' => 'employe',
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
    }

    /**
     * Affiche la page de validation (récupère depuis session).
     */
    public function showValidation(Request $request)
    {
        $pending = session('pending_pointage');
        if (!$pending) {
            return redirect()->route('presence.pointage')->with('error', 'Aucune donnée de pointage en attente.');
        }

        $user = $request->user();

        if ($pending['user_type'] === 'etudiant') {
            // Logique pour stagiaire
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
        } else {
            // Logique pour employé
            $domaine = $user->domaine;

            $previewData = [
                'user_name' => $user->name,
                'domaine_name' => $domaine->nom,
                'site_name' => 'Site principal',
                'latitude' => $pending['latitude'],
                'longitude' => $pending['longitude'],
                'accuracy' => $pending['accuracy_meters'] ?? 'N/A',
                'pointage_time' => now()->format('H:i'),
                'type' => $pending['type'] === 'check_in' ? 'arrivée' : 'départ',
                'form_data' => $pending,
            ];

            // No geofence preview for employees
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

            $data = $pending;
            $data['device_uuid'] = $pending['device_uuid'];
            $data['device_label'] = $pending['device_label'];
            $data['platform'] = $pending['platform'];
            $data['browser'] = $pending['browser'];
            $data['app_version'] = $pending['app_version'];

            if ($pending['user_type'] === 'etudiant') {
                // Logique pour stagiaire
                $stage = $user->etudiant->stages()->findOrFail($pending['stage_id']);

                if ($pending['type'] === 'check_in') {
                    $event = $this->presenceService->registerCheckIn($stage, $user, $data);
                } else {
                    $event = $this->presenceService->registerCheckOut($stage, $user, $data);
                }
            } else {
                // Logique pour employé
                if ($pending['type'] === 'check_in') {
                    $event = $this->presenceService->registerEmployeeCheckIn($user, $data);
                } else {
                    $event = $this->presenceService->registerEmployeeCheckOut($user, $data);
                }
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
            Log::error('Pointage confirmation failed: ' . $e->getMessage(), ['user_id' => $request->user()->id]);
            return redirect()->route('presence.pointage')
                ->with('error', 'Erreur lors de l\'enregistrement. Réessayez ou contactez l\'admin.');
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
    $period = $request->get('period', 'month');
    $dateFrom = $request->get('date_from');
    $dateTo = $request->get('date_to');
    
    // Stats détaillées via service
    // ⚠️ ICI : passer les dates au service
    
    $userStats = $this->adminPresenceService->getUserDetailedStats(
        $user->id,
        $period,
        $dateFrom,
        $dateTo
    );


        if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? \Carbon\Carbon::parse($dateFrom) : now()->startOfMonth();
            $endDate = $dateTo ? \Carbon\Carbon::parse($dateTo) : now()->endOfMonth();
        } else {
            switch ($period) {
                case 'today':
                    $startDate = today();
                    $endDate = today();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default:
                    $startDate = now()->subWeek();
                    $endDate = now();
            }
        }

        $filters = [
            'date_from' => $startDate->format('Y-m-d'),
            'date_to' => $endDate->format('Y-m-d'),
        ];

        if ($etudiant) {
            $filters['etudiant_id'] = $etudiant->id;
        } else {
            $filters['user_id'] = $user->id;
        }

        $attendanceDaysQuery = $this->adminPresenceService->listAttendanceDays($filters, 100)
            ->with(['stage.site', 'anomalies', 'dailyReports']);

        $attendanceDays = $attendanceDaysQuery->get();

        if ($etudiant) {
            return view('presence.historique', compact('attendanceDays', 'period', 'userStats', 'dateFrom', 'dateTo'));
        } else {
            return view('employee.presence.historique', compact('attendanceDays', 'period', 'userStats', 'dateFrom', 'dateTo'));
        }
    }

    /**
     * Affiche la page de pointage pour les employés.
     */
    public function employeePointage(Request $request)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur a un domaine
        abort_if(!$user->domaine, 403, "Votre compte n'est pas encore rattaché à un domaine.");

        // Statut du jour pour l'employé
        $attendanceDay = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        return view('employee.presence.pointage', compact('attendanceDay', 'user'));
    }

    /**
     * Calculate distance between two GPS coordinates (copied from PresenceService)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {        $earthRadius = 6371000; // in meters    
        $dLat = deg2rad($lat2 - $lat1);  
        $dLon = deg2rad($lon2 - $lon1);  
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);  
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));  
        return $earthRadius * $c; 
    }
    /**
     * Historique des présences pour les employés.
     */
    public function employeeHistorique(Request $request)
    {
        $user = $request->user();

        abort_if(!$user->domaine, 403, "Votre compte n'est pas encore rattaché à un domaine.");

        $period = $request->get('period', 'month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Stats détaillées via service
        $userStats = $this->adminPresenceService->getUserDetailedStats($user->id, $period, $dateFrom, $dateTo);
         if ($dateFrom || $dateTo) {
            $startDate = $dateFrom ? \Carbon\Carbon::parse($dateFrom) : now()->startOfMonth();
            $endDate = $dateTo ? \Carbon\Carbon::parse($dateTo) : now()->endOfMonth();
        } else {
            switch ($period) {
                case 'today':
                    $startDate = today();
                    $endDate = today();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default:
                    $startDate = now()->subWeek();
                    $endDate = now();
            }
        }

        $filters = [
            'date_from' => $startDate->format('Y-m-d'),
            'date_to' => $endDate->format('Y-m-d'),
            'user_id' => $user->id,
        ];

        $attendanceDaysQuery = $this->adminPresenceService->listAttendanceDays($filters, 100)
            ->with(['anomalies']);

        $attendanceDays = $attendanceDaysQuery->get();

        return view('employee.presence.historique', compact('attendanceDays', 'period', 'userStats', 'dateFrom', 'dateTo'));
    }
}
