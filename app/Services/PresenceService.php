<?php

namespace App\Services;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\SiteGeofence;
use App\Models\Stage;
use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PresenceService
{
    // ──────────────────────────────────────────────────────────────────────────
    //  NOUVEAU : distance maximale absolue (en mètres) pour accepter un pointage
    //  Tout pointage à plus de 100 mètres du site sera rejeté.
    // ──────────────────────────────────────────────────────────────────────────
    protected const MAX_ALLOWED_DISTANCE_METERS = 100;

    // ==========================================================================
    //  POINTAGE POUR LES STAGIAIRES (check-in / check-out)
    // ==========================================================================

    /**
     * Enregistre l'arrivée (check-in) d'un stagiaire.
     */
    public function registerCheckIn(Stage $stage, User $user, array $payload, ?string $observation_message = null): AttendanceEvent
    {
        return $this->registerEvent($stage, $user, $payload, 'check_in', $observation_message);
    }

    /**
     * Enregistre le départ (check-out) d'un stagiaire.
     */
    public function registerCheckOut(Stage $stage, User $user, array $payload): AttendanceEvent
    {
        return $this->registerEvent($stage, $user, $payload, 'check_out');
    }

    // ==========================================================================
    //  POINTAGE POUR LES EMPLOYÉS
    // ==========================================================================

    /**
     * Enregistre l'arrivée (check-in) d'un employé.
     */
    public function registerEmployeeCheckIn(User $user, array $payload, ?string $observation_message = null): AttendanceEvent
    {
        return $this->registerEmployeeEvent($user, $payload, 'check_in', $observation_message);
    }

    /**
     * Enregistre le départ (check-out) d'un employé.
     */
    public function registerEmployeeCheckOut(User $user, array $payload): AttendanceEvent
    {
        return $this->registerEmployeeEvent($user, $payload, 'check_out');
    }

    // ==========================================================================
    //  PARTIE EMPLOYÉS : LOGIQUE MÉTIER
    // ==========================================================================

    /**
     * Enregistre un événement (arrivée/départ) pour un employé.
     * Gère la géolocalisation, la distance (< 100 m) et les anomalies.
     */
    protected function registerEmployeeEvent(User $user, array $payload, string $eventType, ?string $observation_message = null): AttendanceEvent
    {
        return DB::transaction(function () use ($user, $payload, $eventType, $observation_message) {

            $hasCoordinates = isset($payload['latitude'], $payload['longitude'])
                && $payload['latitude'] !== null
                && $payload['longitude'] !== null;

            $isLate = $payload['is_late'] ?? false;

            $domaine = $user->domaine;
            if (!$domaine) {
                throw ValidationException::withMessages(['presence' => 'Aucun domaine assigné pour le pointage.']);
            }

            $device = $this->resolveTrustedDevice($user, $payload);

            if ($hasCoordinates) {
                // Trouve le site et la géofence la plus proche des coordonnées GPS
                [$site, $geofence, $distance] = $this->resolveEmployeeSiteGeofence($domaine, $payload);
            } else {
                $site     = $domaine->sites()->first();
                $geofence = $site?->geofences()->where('is_active', true)->first();
                $distance = null; // pas de distance calculable
            }

            // Évaluation de la validité du pointage (distance, précision, doublons...)
            $decision = $this->evaluateEmployeeEvent($user, $eventType, $payload, $geofence, $distance, $hasCoordinates);

            // Création de l'événement de pointage
            $event = AttendanceEvent::create([
                'stage_id'               => null,
                'etudiant_id'            => null,
                'site_id'                => $site?->id,
                'site_geofence_id'       => $geofence?->id,
                'user_id'                => $user->id,
                'trusted_device_id'      => $device?->id,
                'event_type'             => $eventType,
                'status'                 => $decision['status'],
                'occurred_at'            => now(),
                'latitude'               => $payload['latitude'],
                'longitude'              => $payload['longitude'],
                'accuracy_meters'        => $payload['accuracy_meters'] ?? null,
                'distance_to_site_meters'=> $distance,
                'ip_address'             => request()->ip(),
                'user_agent'             => request()->userAgent(),
                'device_fingerprint'     => $device?->device_fingerprint ?? $this->fallbackFingerprint($payload),
                'reason_code'            => $decision['reason_code'],
                'rejection_reason'       => $decision['status'] === 'rejected' ? $decision['message'] : null,
                'meta' => [
                    'domaine_id'          => $domaine->id,
                    'platform'            => $payload['platform'] ?? null,
                    'browser'             => $payload['browser'] ?? null,
                    'app_version'         => $payload['app_version'] ?? null,
                    'device_role'         => $device?->is_primary ? 'primary' : 'secondary',
                    'is_late_arrival'     => $isLate,
                    'location_method'     => $payload['location_method'] ?? 'unknown',
                    'confidence_score'    => $payload['confidence_score'] ?? 0,
                    'is_internal_network' => $this->isOnInternalNetwork(request()->ip()),
                ],
            ]);

            // Enregistrement d'une anomalie si la décision l'indique
            if (!empty($decision['anomaly'])) {
                $this->recordAnomaly($event, $decision['anomaly'], $decision['severity'] ?? 'medium', [
                    'message' => $decision['message'],
                    'domaine' => $domaine->nom,
                ]);
            }

            // Cas particulier : retard à l'arrivée avec observation
            if ($isLate && $eventType === 'check_in' && $observation_message) {
                $this->recordAnomaly($event, 'retard_arrivee', 'moyen', [
                    'message_observation' => $observation_message,
                    'minutes_retard'      => $this->computeLateMinutes(null, now()),
                    'domaine'             => $domaine->nom,
                    'type_utilisateur'    => 'employe',
                ]);
            }

            $this->recordDeviceSwitchAnomalyIfNeeded($event, $device);
            $this->syncEmployeeAttendanceDay($user, $event, $isLate);

            return $event;
        });
    }

    /**
     * Trouve le site (et sa géofence active) le plus proche des coordonnées fournies.
     * Retourne [site, geofence, distance].
     */
    protected function resolveEmployeeSiteGeofence(Domaine $domaine, array $payload): array
    {
        $latitude  = (float) $payload['latitude'];
        $longitude = (float) $payload['longitude'];

        $sites = $domaine->sites()->with(['geofences' => function ($query) {
            $query->where('is_active', true);
        }])->where('is_active', true)->get();

        $best = null;
        foreach ($sites as $site) {
            foreach ($site->geofences as $geofence) {
                $distance = $this->calculateDistanceMeters(
                    $latitude, $longitude,
                    (float) $geofence->center_latitude,
                    (float) $geofence->center_longitude
                );
                if ($best === null || $distance < $best['distance']) {
                    $best = ['site' => $site, 'geofence' => $geofence, 'distance' => $distance];
                }
            }
        }

        if (!$best) {
            return [null, null, null];
        }

        return [$best['site'], $best['geofence'], $best['distance']];
    }

    /**
     * Évalue si le pointage d'un employé doit être accepté, rejeté ou signalé.
     *
     * ✅ NOUVEAU : la distance au site doit être STRICTEMENT < 100 mètres.
     *    (utilisation de la constante MAX_ALLOWED_DISTANCE_METERS)
     */
    protected function evaluateEmployeeEvent(
        User         $user,
        string       $eventType,
        array        $payload,
        ?SiteGeofence $geofence,
        ?int         $distance,
        bool         $hasCoordinates = true
    ): array {

        // --- 1) Vérifications des doublons ---
        $day = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($eventType === 'check_in' && $day?->first_check_in_at) {
            return [
                'status'      => 'rejected',
                'reason_code' => 'duplicate_checkin',
                'message'     => "L'arrivée a déjà été enregistrée aujourd'hui.",
                'anomaly'     => 'duplicate_checkin',
                'severity'    => 'medium',
            ];
        }

        if ($eventType === 'check_out' && !$day?->first_check_in_at) {
            return [
                'status'      => 'rejected',
                'reason_code' => 'checkout_without_checkin',
                'message'     => "Impossible d'enregistrer le départ sans arrivée.",
                'anomaly'     => 'checkout_without_checkin',
                'severity'    => 'medium',
            ];
        }

        if ($eventType === 'check_out' && $day?->last_check_out_at) {
            return [
                'status'      => 'rejected',
                'reason_code' => 'duplicate_checkout',
                'message'     => 'Le départ a déjà été enregistré aujourd\'hui.',
                'anomaly'     => 'duplicate_checkout',
                'severity'    => 'medium',
            ];
        }

        // --- 2) Absence de géofence configurée → on accepte (pas de contrôle) ---
        if (!$geofence) {
            return [
                'status'      => 'approved',
                'reason_code' => 'no_geofence',
                'message'     => 'Pointage enregistré (aucune zone définie).',
            ];
        }

        // --- 3) Pas de coordonnées GPS → accepté avec anomalie légère ---
        if (!$hasCoordinates) {
            return [
                'status'      => 'approved',
                'reason_code' => 'no_gps',
                'message'     => 'Pointage enregistré (GPS non disponible).',
                'anomaly'     => 'no_gps',
                'severity'    => 'low',
            ];
        }

        // --- 4) Vérification de la précision GPS ---
        $accuracy = (int) ($payload['accuracy_meters'] ?? 9999);
        $isInternalNetwork = $this->isOnInternalNetwork(request()->ip());

        // Si l'utilisateur est sur le réseau interne, on assouplit le seuil de précision
        $effectiveAccuracyLimit = $isInternalNetwork
            ? max($geofence->allowed_accuracy_meters, 2000)
            : $geofence->allowed_accuracy_meters;

        if ($accuracy > $effectiveAccuracyLimit) {
            if ($isInternalNetwork) {
                // Réseau interne mais GPS imprécis → flagged (accepté sous réserve)
                return [
                    'status'      => 'flagged',
                    'reason_code' => 'low_gps_trusted_network',
                    'message'     => 'Précision GPS faible mais réseau interne reconnu.',
                    'anomaly'     => 'gps_accuracy_low',
                    'severity'    => 'low',
                ];
            }
            return [
                'status'      => 'rejected',
                'reason_code' => 'gps_accuracy_low',
                'message'     => 'Précision GPS insuffisante.',
                'anomaly'     => 'gps_accuracy_low',
                'severity'    => 'medium',
            ];
        }

        // --- 5) Vérification de la DISTANCE par rapport au site (NOUVEAU seuil 100 m) ---
        //    On utilise la constante MAX_ALLOWED_DISTANCE_METERS, pas le rayon de la geofence.
        if ($distance !== null && $distance > self::MAX_ALLOWED_DISTANCE_METERS) {
            // Si l'utilisateur est sur le réseau interne, on peut accepter avec un flag
            if ($isInternalNetwork && $accuracy > 300) {
                return [
                    'status'      => 'flagged',
                    'reason_code' => 'low_gps_trusted_network',
                    'message'     => 'Position GPS imprécise mais réseau interne reconnu.',
                    'anomaly'     => 'gps_accuracy_low',
                    'severity'    => 'low',
                ];
            }

            return [
                'status'      => 'rejected',
                'reason_code' => 'outside_geofence',
                'message'     => "Vous êtes à plus de " . self::MAX_ALLOWED_DISTANCE_METERS . " mètres du site autorisé.",
                'anomaly'     => 'outside_geofence',
                'severity'    => 'high',
            ];
        }

        // --- 6) Tout est conforme ---
        return [
            'status'      => 'approved',
            'reason_code' => 'ok',
            'message'     => $eventType === 'check_in' ? 'Arrivée enregistrée.' : 'Départ enregistré.',
        ];
    }

    /**
     * Vérifie si l'adresse IP appartient à un réseau privé (RFC-1918).
     * Utilisé pour détecter les connexions depuis le réseau interne de l'entreprise.
     */
    protected function isOnInternalNetwork(string $ip): bool
    {
        $privateRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '127.0.0.0/8',
        ];
        foreach ($privateRanges as $range) {
            if ($this->ipInCidr($ip, $range)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Vérifie si une IP appartient à un bloc CIDR donné.
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);
        $ip     = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask   = -1 << (32 - (int) $bits);
        return ($ip & $mask) === ($subnet & $mask);
    }

    /**
     * Synchronise les données de la journée pour un employé (mise à jour de AttendanceDay).
     */
    protected function syncEmployeeAttendanceDay(User $user, AttendanceEvent $event, bool $isLate = false): void
    {
        $day = AttendanceDay::firstOrNew([
            'user_id'         => $user->id,
            'attendance_date' => today()->toDateString(),
        ]);

        $day->etudiant_id = null;
        $day->stage_id    = null;

        if ($event->event_type === 'check_in' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_in_event_id = $event->id;
            $day->first_check_in_at = $event->occurred_at;
            $day->late_minutes      = $this->computeLateMinutes(null, $event->occurred_at);
            $day->arrival_status    = $this->computeArrivalStatus($event->occurred_at);
            $day->day_status        = $day->arrival_status === 'late' ? 'late' : 'present';
            $day->validation_status = $isLate ? 'a_reexaminer' : 'auto_approuve';
        }

        if ($event->event_type === 'check_out' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_out_event_id = $event->id;
            $day->last_check_out_at  = $event->occurred_at;
            $day->worked_minutes     = $day->first_check_in_at
                ? max(0, $day->first_check_in_at->diffInMinutes($event->occurred_at))
                : 0;
            $day->early_departure_minutes = 0;
        }

        $day->save();
    }

    // ==========================================================================
    //  PARTIE STAGIAIRES : LOGIQUE MÉTIER
    // ==========================================================================

    /**
     * Enregistre un événement (arrivée/départ) pour un stagiaire.
     * Vérifie également la distance < 100 m.
     */
    protected function registerEvent(Stage $stage, User $user, array $payload, string $eventType, ?string $observation_message = null): AttendanceEvent
    {
        return DB::transaction(function () use ($stage, $user, $payload, $eventType, $observation_message) {

            $etudiant = $this->resolveEtudiant($user, $stage);
            $this->ensureStageOwnership($stage, $etudiant);

            $device   = $this->resolveTrustedDevice($user, $payload);
            $geofence = $stage->site?->geofences()->where('is_active', true)->orderByDesc('is_primary')->first();

            $distance = $geofence
                ? $this->calculateDistanceMeters(
                    (float) $payload['latitude'],
                    (float) $payload['longitude'],
                    (float) $geofence->center_latitude,
                    (float) $geofence->center_longitude
                )
                : null;

            $isLate   = $payload['is_late'] ?? false;
            $decision = $this->evaluateEvent($stage, $eventType, $payload, $geofence, $distance);

            $event = AttendanceEvent::create([
                'stage_id'               => $stage->id,
                'etudiant_id'            => $etudiant->id,
                'site_id'                => $stage->site_id,
                'site_geofence_id'       => $geofence?->id,
                'user_id'                => $user->id,
                'trusted_device_id'      => $device?->id,
                'event_type'             => $eventType,
                'status'                 => $decision['status'],
                'occurred_at'            => now(),
                'latitude'               => $payload['latitude'],
                'longitude'              => $payload['longitude'],
                'accuracy_meters'        => $payload['accuracy_meters'] ?? null,
                'distance_to_site_meters'=> $distance,
                'ip_address'             => request()->ip(),
                'user_agent'             => request()->userAgent(),
                'device_fingerprint'     => $device?->device_fingerprint ?? $this->fallbackFingerprint($payload),
                'reason_code'            => $decision['reason_code'],
                'rejection_reason'       => $decision['message'],
                'meta' => [
                    'platform'          => $payload['platform'] ?? null,
                    'browser'           => $payload['browser'] ?? null,
                    'app_version'       => $payload['app_version'] ?? null,
                    'device_role'       => $device?->is_primary ? 'primary' : 'secondary',
                    'is_late_arrival'   => $isLate,
                    'location_method'   => $payload['location_method'] ?? 'unknown',
                    'confidence_score'  => $payload['confidence_score'] ?? 0,
                    'is_internal_network' => $this->isOnInternalNetwork(request()->ip()),
                ],
            ]);

            if (!empty($decision['anomaly'])) {
                $this->recordAnomaly($event, $decision['anomaly'], $decision['severity'] ?? 'medium', [
                    'message' => $decision['message'],
                ]);
            }

            if ($isLate && $eventType === 'check_in' && $observation_message) {
                $this->recordAnomaly($event, 'retard_arrivee', 'moyen', [
                    'message_observation' => $observation_message,
                    'minutes_retard'      => $this->computeLateMinutes($stage, now()),
                    'type_utilisateur'    => 'etudiant',
                    'stage_theme'         => $stage->theme,
                ]);
            }

            $this->recordDeviceSwitchAnomalyIfNeeded($event, $device);
            $this->syncAttendanceDay($stage, $etudiant, $event, $isLate);

            return $event;
        });
    }

    /**
     * Récupère l'étudiant correspondant à l'utilisateur ou au stage.
     */
    protected function resolveEtudiant(User $user, Stage $stage): Etudiant
    {
        if ($user->etudiant) {
            return $user->etudiant;
        }

        if ($stage->etudiant && $stage->etudiant->email && $stage->etudiant->email === $user->email) {
            return $stage->etudiant;
        }

        throw ValidationException::withMessages([
            'presence' => "Votre compte n'est pas encore rattache a une fiche etudiant.",
        ]);
    }

    /**
     * Vérifie que le stage appartient bien à l'étudiant.
     */
    protected function ensureStageOwnership(Stage $stage, Etudiant $etudiant): void
    {
        if ((int) $stage->etudiant_id !== (int) $etudiant->id) {
            throw ValidationException::withMessages([
                'presence' => "Vous n'etes pas autorise a pointer pour ce stage.",
            ]);
        }
    }

    /**
     * Enregistre ou met à jour un appareil de confiance pour l'utilisateur.
     */
    protected function resolveTrustedDevice(User $user, array $payload): TrustedDevice
    {
        $fingerprint        = $payload['device_fingerprint'] ?? $this->fallbackFingerprint($payload);
        $hasKnownDevices    = TrustedDevice::where('user_id', $user->id)->exists();
        $primaryDeviceExists = TrustedDevice::where('user_id', $user->id)->where('is_primary', true)->exists();

        $device = TrustedDevice::firstOrNew([
            'user_id'            => $user->id,
            'device_fingerprint' => $fingerprint,
        ]);

        if (!$device->exists) {
            $device->first_seen_at = now();
            $device->is_trusted    = true;
            $device->is_primary    = !$hasKnownDevices || !$primaryDeviceExists;
        } elseif (!$device->is_primary && !$primaryDeviceExists) {
            $device->is_primary = true;
        }

        $device->fill([
            'device_uuid'      => $payload['device_uuid'] ?? null,
            'device_label'     => $payload['device_label'] ?? null,
            'platform'         => $payload['platform'] ?? null,
            'browser'          => $payload['browser'] ?? null,
            'app_version'      => $payload['app_version'] ?? null,
            'last_ip_address'  => request()->ip(),
            'last_seen_at'     => now(),
        ]);
        $device->save();

        return $device;
    }

    /**
     * Enregistre une anomalie si le pointage provient d'un appareil secondaire.
     */
    protected function recordDeviceSwitchAnomalyIfNeeded(AttendanceEvent $event, ?TrustedDevice $device): void
    {
        if (!$device || !$device->wasRecentlyCreated || $device->is_primary) {
            return;
        }

        $this->recordAnomaly($event, 'secondary_device_detected', 'low', [
            'message'          => 'Pointage effectué depuis un appareil secondaire (non principal).',
            'trusted_device_id'=> $device->id,
            'device_label'     => $device->device_label,
            'device_uuid'      => $device->device_uuid,
            'is_trusted'       => $device->is_trusted,
        ]);
    }

    /**
     * Évalue la validité du pointage pour un stagiaire.
     * ✅ NOUVEAU : la distance maximale acceptée est de 100 mètres.
     */
    protected function evaluateEvent(Stage $stage, string $eventType, array $payload, ?SiteGeofence $geofence, ?int $distance): array
    {
        // Vérification que le stage est actif
        if (!$this->isStageActive($stage)) {
            return [
                'status'      => 'rejected',
                'reason_code' => 'stage_inactive',
                'message'     => "Le stage n'est pas actif pour ce pointage.",
                'anomaly'     => 'stage_inactive',
                'severity'    => 'high',
            ];
        }

        // Pas de géofence → pointage signalé (anomalie)
        if (!$geofence) {
            return [
                'status'      => 'flagged',
                'reason_code' => 'missing_geofence',
                'message'     => "Aucune zone de présence active n'est configurée pour ce site.",
                'anomaly'     => 'missing_geofence',
                'severity'    => 'high',
            ];
        }

        // Vérification de la précision GPS
        $accuracy = (int) ($payload['accuracy_meters'] ?? 9999);
        $isInternalNetwork = $this->isOnInternalNetwork(request()->ip());
        $effectiveAccuracyLimit = $isInternalNetwork
            ? max($geofence->allowed_accuracy_meters, 2000)
            : $geofence->allowed_accuracy_meters;

        if ($accuracy > $effectiveAccuracyLimit) {
            if ($isInternalNetwork) {
                return [
                    'status'      => 'flagged',
                    'reason_code' => 'low_gps_trusted_network',
                    'message'     => 'Précision GPS faible mais réseau interne reconnu.',
                    'anomaly'     => 'gps_accuracy_low',
                    'severity'    => 'low',
                ];
            }
            return [
                'status'      => 'rejected',
                'reason_code' => 'gps_accuracy_low',
                'message'     => 'La précision GPS est insuffisante pour valider votre présence.',
                'anomaly'     => 'gps_accuracy_low',
                'severity'    => 'medium',
            ];
        }

        // ✅ NOUVEAU : vérification de la distance (max 100 m)
        if ($distance !== null && $distance > self::MAX_ALLOWED_DISTANCE_METERS) {
            if ($isInternalNetwork && $accuracy > 300) {
                return [
                    'status'      => 'flagged',
                    'reason_code' => 'low_gps_trusted_network',
                    'message'     => 'Position GPS imprécise mais réseau interne reconnu.',
                    'anomaly'     => 'gps_accuracy_low',
                    'severity'    => 'low',
                ];
            }
            return [
                'status'      => 'rejected',
                'reason_code' => 'outside_geofence',
                'message'     => "Vous êtes à plus de " . self::MAX_ALLOWED_DISTANCE_METERS . " mètres du site autorisé.",
                'anomaly'     => 'outside_geofence',
                'severity'    => 'high',
            ];
        }

        // Vérification des doublons pour le stagiaire
        $day = AttendanceDay::where('etudiant_id', $stage->etudiant_id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($eventType === 'check_in' && $day?->first_check_in_at) {
            return [
                'status'      => 'rejected',
                'reason_code' => 'duplicate_checkin',
                'message'     => "L'arrivée a déjà été enregistrée aujourd'hui.",
                'anomaly'     => 'duplicate_checkin',
                'severity'    => 'medium',
            ];
        }

        if ($eventType === 'check_out' && !$day?->first_check_in_at) {
            return [
                'status'      => 'rejected',
                'reason_code' => 'checkout_without_checkin',
                'message'     => "Impossible d'enregistrer le départ sans arrivée.",
                'anomaly'     => 'checkout_without_checkin',
                'severity'    => 'medium',
            ];
        }

        if ($eventType === 'check_out' && $day?->last_check_out_at) {
            return [
                'status'      => 'rejected',
                'reason_code' => 'duplicate_checkout',
                'message'     => 'Le départ a déjà été enregistré aujourd\'hui.',
                'anomaly'     => 'duplicate_checkout',
                'severity'    => 'medium',
            ];
        }

        return [
            'status'      => 'approved',
            'reason_code' => 'ok',
            'message'     => $eventType === 'check_in'
                ? "Présence d'arrivée enregistrée."
                : 'Présence de départ enregistrée.',
        ];
    }

    /**
     * Synchronise les données de la journée pour un stagiaire (AttendanceDay).
     */
    protected function syncAttendanceDay(Stage $stage, Etudiant $etudiant, AttendanceEvent $event, bool $isLate = false): void
    {
        $day = AttendanceDay::firstOrNew([
            'etudiant_id'     => $etudiant->id,
            'attendance_date' => today()->toDateString(),
        ]);

        $day->fill(['stage_id' => $stage->id, 'site_id' => $stage->site_id]);

        if ($event->event_type === 'check_in' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_in_event_id = $event->id;
            $day->first_check_in_at = $event->occurred_at;
            $day->late_minutes      = $this->computeLateMinutes($stage, $event->occurred_at);
            $day->arrival_status    = $this->computeArrivalStatus($event->occurred_at);
            $day->day_status        = match ($day->arrival_status) {
                'late'    => 'late',
                'warning' => 'warning',
                'ontime'  => 'present',
                default   => 'present',
            };
            $day->validation_status = $isLate ? 'a_reexaminer' : 'auto_approuve';
        }

        if ($event->event_type === 'check_out' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_out_event_id       = $event->id;
            $day->last_check_out_at        = $event->occurred_at;
            $day->worked_minutes           = $day->first_check_in_at
                ? max(0, $day->first_check_in_at->diffInMinutes($event->occurred_at))
                : 0;
            $day->early_departure_minutes  = $this->computeEarlyDepartureMinutes($stage, $event->occurred_at);

            if ($day->early_departure_minutes > 0) {
                $day->day_status        = 'incomplete';
                $day->validation_status = 'a_reexaminer';
            }
        }

        $day->save();
    }

    /**
     * Enregistre une anomalie dans la table dédiée.
     */
    protected function recordAnomaly(AttendanceEvent $event, string $type, string $severity, array $payload = []): void
    {
        AttendanceAnomaly::create([
            'attendance_event_id' => $event->id,
            'attendance_day_id'   => $event->attendanceDay?->id ?? null,
            'stage_id'            => $event->stage_id ?? null,
            'etudiant_id'         => $event->etudiant_id ?? null,
            'user_id'             => $event->user_id,
            'anomaly_type'        => $type,
            'severity'            => $severity,
            'status'              => 'open',
            'detected_at'         => now(),
            'payload'             => array_merge($payload, [
                'event_type' => $event->event_type,
                'user_type'  => $event->etudiant_id ? 'etudiant' : 'employe',
                'status'     => $event->status,
            ]),
        ]);
    }

    // ==========================================================================
    //  MÉTHODES UTILITAIRES
    // ==========================================================================

    /**
     * Vérifie si le stage est actif (dates début/fin).
     */
    protected function isStageActive(Stage $stage): bool
    {
        if (!$stage->date_debut || !$stage->date_fin) {
            return false;
        }
        return now()->between($stage->date_debut->copy()->startOfDay(), $stage->date_fin->copy()->endOfDay());
    }

    /**
     * Calcule les minutes de retard par rapport à l'heure prévue (8h00).
     */
    protected function computeLateMinutes(?Stage $stage, $occurredAt): int
    {
        $expected = $occurredAt->copy()->setTime(8, 0);
        return $occurredAt->greaterThan($expected) ? $expected->diffInMinutes($occurredAt) : 0;
    }

    /**
     * Calcule les minutes de départ anticipé par rapport à l'heure attendue.
     */
    protected function computeEarlyDepartureMinutes(Stage $stage, $occurredAt): int
    {
        if (!$stage->expected_check_out_time) {
            return 0;
        }
        $expected        = $occurredAt->copy()->setTimeFromTimeString($stage->expected_check_out_time);
        $grace           = (int) ($stage->allowed_early_departure_minutes ?? 0);
        $effectiveExpected = $expected->copy()->subMinutes($grace);
        return $occurredAt->lessThan($effectiveExpected) ? $occurredAt->diffInMinutes($effectiveExpected) : 0;
    }

    /**
     * Détermine le statut d'arrivée : "ontime" (avant 8h) ou "late".
     */
    protected function computeArrivalStatus($occurredAt): string
    {
        $threshold = $occurredAt->copy()->setTime(8, 0);
        return $occurredAt->lessThan($threshold) ? 'ontime' : 'late';
    }

    /**
     * Calcule la distance en mètres entre deux points GPS (formule de Haversine).
     */
    protected function calculateDistanceMeters(float $latFrom, float $lngFrom, float $latTo, float $lngTo): int
    {
        $earthRadius = 6371000;
        $latDelta    = deg2rad($latTo - $latFrom);
        $lngDelta    = deg2rad($lngTo - $lngFrom);
        $a           = sin($latDelta / 2) * sin($latDelta / 2)
            + cos(deg2rad($latFrom)) * cos(deg2rad($latTo))
            * sin($lngDelta / 2) * sin($lngDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return (int) round($earthRadius * $c);
    }

    /**
     * Génère une empreinte (fingerprint) de secours pour l'appareil.
     */
    protected function fallbackFingerprint(array $payload): string
    {
        return sha1(implode('|', [
            $payload['device_uuid'] ?? 'no-device-uuid',
            request()->userAgent() ?? 'no-user-agent',
            request()->ip() ?? 'no-ip',
        ]));
    }
}