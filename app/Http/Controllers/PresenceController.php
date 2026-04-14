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
            // Logique pour employé
            $domaine = $user->domaine;

            if (!$domaine) {
                abort(403, "Votre compte n'est pas rattaché à un domaine de travail.");
            }

            // Pour les employés, on peut créer un "stage virtuel" ou utiliser une logique différente
            // Pour l'instant, utilisons la même logique mais sans stage
            $activeStage = null; // Pas de stage pour les employés
            $attendanceDay = null; // TODO: Implémenter pour employés

            return view('presence.pointage', compact('activeStage', 'attendanceDay', 'domaine'));
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
                'site_name' => 'Site principal', // TODO: Lier employés à des sites
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy_meters ?? 'N/A',
                'pointage_time' => now()->format('H:i'),
                'type' => 'arrivée',
            ];

            // TODO: Calculer distance si geofence disponible pour employés

            // Stocker données complètes pour confirmation
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
                'site_name' => 'Site principal', // TODO: Lier employés à des sites
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy_meters ?? 'N/A',
                'pointage_time' => now()->format('H:i'),
                'type' => 'départ',
            ];

            // TODO: Calculer distance si geofence disponible pour employés

            // Stocker données complètes pour confirmation
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
    public function validate(Request $request)
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
                'site_name' => 'Site principal', // TODO: Lier employés à des sites
                'latitude' => $pending['latitude'],
                'longitude' => $pending['longitude'],
                'accuracy' => $pending['accuracy_meters'] ?? 'N/A',
                'pointage_time' => now()->format('H:i'),
                'type' => $pending['type'] === 'check_in' ? 'arrivée' : 'départ',
                'form_data' => $pending,
            ];

            // TODO: Calculer distance si geofence disponible pour employés
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
                    $event = $this->registerEmployeeCheckIn($user, $data);
                } else {
                    $event = $this->registerEmployeeCheckOut($user, $data);
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

        if ($etudiant) {
            // Logique pour stagiaire
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
        } else {
            // Logique pour employé
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

            return view('presence.historique', compact('attendanceDays', 'period'));
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

    /**
     * Enregistre l'arrivée pour un employé.
     */
    private function registerEmployeeCheckIn(User $user, array $data): AttendanceEvent
    {
        return DB::transaction(function () use ($user, $data) {
            // Créer ou récupérer le jour d'assiduité
            $attendanceDay = AttendanceDay::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'attendance_date' => today(),
                ],
                [
                    'site_id' => null, // TODO: Lier à un site pour employés
                    'first_check_in_at' => now(),
                    'day_status' => 'present',
                ]
            );

            // Créer l'événement
            $event = AttendanceEvent::create([
                'stage_id' => null,
                'etudiant_id' => null,
                'user_id' => $user->id,
                'attendance_day_id' => $attendanceDay->id,
                'event_type' => 'check_in',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'accuracy_meters' => $data['accuracy_meters'] ?? null,
                'device_fingerprint' => $data['device_fingerprint'],
                'device_uuid' => $data['device_uuid'] ?? null,
                'device_label' => $data['device_label'] ?? null,
                'platform' => $data['platform'] ?? null,
                'browser' => $data['browser'] ?? null,
                'app_version' => $data['app_version'] ?? null,
                'status' => 'approved', // Pour l'instant, approuver automatiquement
                'processed_at' => now(),
            ]);

            // Mettre à jour le jour d'assiduité
            $attendanceDay->update([
                'first_check_in_at' => $event->created_at,
                'arrival_status' => 'on_time', // TODO: Calculer si en retard
            ]);

            return $event;
        });
    }

    /**
     * Enregistre le départ pour un employé.
     */
    private function registerEmployeeCheckOut(User $user, array $data): AttendanceEvent
    {
        return DB::transaction(function () use ($user, $data) {
            // Récupérer le jour d'assiduité
            $attendanceDay = AttendanceDay::where('user_id', $user->id)
                ->whereDate('attendance_date', today())
                ->first();

            if (!$attendanceDay) {
                throw new \Exception('Aucun check-in trouvé pour aujourd\'hui.');
            }

            // Créer l'événement
            $event = AttendanceEvent::create([
                'stage_id' => null,
                'etudiant_id' => null,
                'user_id' => $user->id,
                'attendance_day_id' => $attendanceDay->id,
                'event_type' => 'check_out',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'accuracy_meters' => $data['accuracy_meters'] ?? null,
                'device_fingerprint' => $data['device_fingerprint'],
                'device_uuid' => $data['device_uuid'] ?? null,
                'device_label' => $data['device_label'] ?? null,
                'platform' => $data['platform'] ?? null,
                'browser' => $data['browser'] ?? null,
                'app_version' => $data['app_version'] ?? null,
                'status' => 'approved', // Pour l'instant, approuver automatiquement
                'processed_at' => now(),
            ]);

            // Mettre à jour le jour d'assiduité
            $attendanceDay->update([
                'last_check_out_at' => $event->created_at,
                'departure_status' => 'on_time', // TODO: Calculer si départ anticipé
                'worked_minutes' => $attendanceDay->first_check_in_at
                    ? $attendanceDay->first_check_in_at->diffInMinutes($event->created_at)
                    : null,
            ]);

            return $event;
        });
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
