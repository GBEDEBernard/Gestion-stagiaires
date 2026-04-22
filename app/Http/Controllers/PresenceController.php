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
<<<<<<< HEAD
            abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattaché à une fiche étudiant.");

=======
            // Logique pour stagiaire
            abort_if(!$etudiant, 403, "Votre compte n'est pas encore rattaché à une fiche étudiant.");

            // Trouve le stage actif (le plus récent en cours)
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
            $activeStage = $etudiant->stages()
                ->where('date_debut', '<=', now())
                ->where('date_fin', '>=', now())
                ->with('site', 'typestage')
                ->orderByDesc('date_debut')
                ->first();

            if (!$activeStage) {
                return view('presence.pointage', compact('activeStage'));
            }

<<<<<<< HEAD
=======
            // Statut du jour
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
            $attendanceDay = AttendanceDay::where('stage_id', $activeStage->id)
                ->whereDate('attendance_date', today())
                ->first();

            return view('presence.pointage', compact('activeStage', 'attendanceDay'));
        } else {
<<<<<<< HEAD
=======
            // Logique pour employé
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
            $domaine = $user->domaine;

            if (!$domaine) {
                abort(403, "Votre compte n'est pas rattaché à un domaine de travail.");
            }

<<<<<<< HEAD
            $activeStage = null;
=======
            // Pour les employés, on peut créer un "stage virtuel" ou utiliser une logique différente
            // Pour l'instant, utilisons la même logique mais sans stage
            $activeStage = null; // Pas de stage pour les employés
            // Query today's attendance for employee
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
            $attendanceDay = AttendanceDay::where('user_id', $user->id)
                ->whereDate('attendance_date', today())
                ->first();

            return view('presence.pointage', compact('activeStage', 'attendanceDay', 'domaine'));
        }
    }

    /**
<<<<<<< HEAD
     * Prépare le check-in et renvoie les données pour la popup de confirmation.
=======
     * Prépare la validation du check-in (stocke en session, affiche page validation).
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
     */
    public function prepareCheckIn(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        if ($etudiant) {
<<<<<<< HEAD
=======
            // Logique pour stagiaire
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
=======
            // Calculer distance si geofence disponible
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
=======
            // Stocker données complètes pour confirmation
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
            return response()->json([
                'success' => true,
                'preview' => $previewData,
            ]);
        } else {
=======
            return view('presence.validate', $previewData);
        } else {
            // Logique pour employé
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
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

<<<<<<< HEAD
=======
            // No geofence for employee preview (calculated later in service)
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
            return response()->json([
                'success' => true,
                'preview' => $previewData,
            ]);
=======
            return view('presence.validate', $previewData);
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
        }
    }

    /**
<<<<<<< HEAD
     * Prépare le check-out et renvoie les données pour la popup de confirmation.
=======
     * Prépare la validation du check-out (stocke en session, affiche page validation).
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
     */
    public function prepareCheckOut(Request $request)
    {
        $user = $request->user();
        $etudiant = $user->etudiant;

        if ($etudiant) {
<<<<<<< HEAD
=======
            // Logique pour stagiaire
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
=======
            // Calculer distance si geofence disponible
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
=======
            // Stocker données complètes pour confirmation
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
            return response()->json([
                'success' => true,
                'preview' => $previewData,
            ]);
        } else {
=======
            return view('presence.validate', $previewData);
        } else {
            // Logique pour employé
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
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

<<<<<<< HEAD
=======
            // No geofence for employee preview (calculated later in service)
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
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

<<<<<<< HEAD
            return response()->json([
                'success' => true,
                'preview' => $previewData,
            ]);
=======
            return view('presence.validate', $previewData);
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
        }
    }

    /**
<<<<<<< HEAD
     * Confirmation AJAX du pointage.
     */
    public function confirmAjax(Request $request)
    {
        $pending = session('pending_pointage');
        if (!$pending) {
            return response()->json([
                'success' => false,
                'message' => 'Données de pointage expirées.',
            ], 400);
=======
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
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
        }

        try {
            $user = $request->user();
<<<<<<< HEAD
            $data = $pending;

            if ($pending['user_type'] === 'etudiant') {
                $stage = $user->etudiant->stages()->findOrFail($pending['stage_id']);
                if ($pending['type'] === 'check_in') {
                    $event = $this->presenceService->registerCheckIn($stage, $user, $data);
                } else {
                    $event = $this->presenceService->registerCheckOut($stage, $user, $data);
                }
            } else {
                if ($pending['type'] === 'check_in') {
                    $event = $this->presenceService->registerEmployeeCheckIn($user, $data);
                } else {
                    $event = $this->presenceService->registerEmployeeCheckOut($user, $data);
                }
            }

            session()->forget('pending_pointage');

            if ($event->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'rejected' => true,
                    'reason' => $event->rejection_reason,
                    'code' => $event->reason_code,
                ]);
            }

            $message = $event->status === 'approved'
                ? '✅ Pointage confirmé et enregistré !'
                : '⚠️ Pointage enregistré mais nécessite validation admin.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('presence.historique'),
            ]);
        } catch (\Exception $e) {
            Log::error('Pointage confirmation failed: ' . $e->getMessage(), ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement.',
            ], 500);
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
        }

        return view('presence.validate', $previewData);
=======

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

        // Stats détaillées via service
        $userStats = $this->adminPresenceService->getUserDetailedStats($user->id, $period);

        $dateFrom = match ($period) {
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subWeek()
        };

        $filters = [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            $etudiant ? 'etudiant_id' : 'user_id' => $etudiant ? $etudiant->id : $user->id,
        ];

        $attendanceDaysQuery = $this->adminPresenceService->listAttendanceDays($filters, 100)
            ->with(['stage.site', 'anomalies', 'dailyReports']);

        $attendanceDays = $attendanceDaysQuery->get();

        return view('presence.historique', compact('attendanceDays', 'period', 'userStats'));
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
    private function calculateDistance(float $latFrom, float $lngFrom, float $latTo, float $lngTo): int
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($latTo - $latFrom);
        $lngDelta = deg2rad($lngTo - $lngFrom);

        $a = sin($latDelta / 2) * sin($latDelta / 2)
            + cos(deg2rad($latFrom)) * cos(deg2rad($latTo))
            * sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * Historique des présences pour les employés.
     */
    public function employeeHistorique(Request $request)
    {
        $user = $request->user();

        abort_if(!$user->domaine, 403, "Votre compte n'est pas encore rattaché à un domaine.");

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
            'user_id' => $user->id,
        ];

        $attendanceDaysQuery = $this->adminPresenceService->listAttendanceDays($filters, 100)
            ->with(['anomalies']);

        $attendanceDays = $attendanceDaysQuery->get()
            ->groupBy(fn($day) => $day->attendance_date->format('Y-W'));

        return view('employee.presence.historique', compact('attendanceDays', 'period'));
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
    }

    /**
     * Confirme et enregistre le pointage depuis session (version non-AJAX de secours).
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
                $stage = $user->etudiant->stages()->findOrFail($pending['stage_id']);

                if ($pending['type'] === 'check_in') {
                    $event = $this->presenceService->registerCheckIn($stage, $user, $data);
                } else {
                    $event = $this->presenceService->registerCheckOut($stage, $user, $data);
                }
            } else {
                if ($pending['type'] === 'check_in') {
                    $event = $this->presenceService->registerEmployeeCheckIn($user, $data);
                } else {
                    $event = $this->presenceService->registerEmployeeCheckOut($user, $data);
                }
            }

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

        $userStats = $this->adminPresenceService->getUserDetailedStats($user->id, $period);

        $dateFrom = match ($period) {
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subWeek()
        };

        $filters = [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            $etudiant ? 'etudiant_id' : 'user_id' => $etudiant ? $etudiant->id : $user->id,
        ];

        $attendanceDaysQuery = $this->adminPresenceService->listAttendanceDays($filters, 100)
            ->with(['stage.site', 'anomalies', 'dailyReports']);

        $attendanceDays = $attendanceDaysQuery->get();

        return view('presence.historique', compact('attendanceDays', 'period', 'userStats'));
    }

    /**
     * Affiche la page de pointage pour les employés.
     */
    public function employeePointage(Request $request)
    {
        $user = $request->user();

        abort_if(!$user->domaine, 403, "Votre compte n'est pas encore rattaché à un domaine.");

        $attendanceDay = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        return view('employee.presence.pointage', compact('attendanceDay', 'user'));
    }

    /**
     * Calculate distance between two GPS coordinates (copied from PresenceService)
     */
    private function calculateDistance(float $latFrom, float $lngFrom, float $latTo, float $lngTo): int
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($latTo - $latFrom);
        $lngDelta = deg2rad($lngTo - $lngFrom);

        $a = sin($latDelta / 2) * sin($latDelta / 2)
            + cos(deg2rad($latFrom)) * cos(deg2rad($latTo))
            * sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * Historique des présences pour les employés.
     */
    public function employeeHistorique(Request $request)
    {
        $user = $request->user();

        abort_if(!$user->domaine, 403, "Votre compte n'est pas encore rattaché à un domaine.");

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
            'user_id' => $user->id,
        ];

        $attendanceDaysQuery = $this->adminPresenceService->listAttendanceDays($filters, 100)
            ->with(['anomalies']);

        $attendanceDays = $attendanceDaysQuery->get()
            ->groupBy(fn($day) => $day->attendance_date->format('Y-W'));

        return view('employee.presence.historique', compact('attendanceDays', 'period'));
    }
}