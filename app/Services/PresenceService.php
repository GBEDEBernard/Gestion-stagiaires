<?php

namespace App\Services;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\Domaine;
use App\Models\Etudiant;
use App\Models\Holiday;
use App\Models\HolidayEmergencyExemption;
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
    //  Distance maximale absolue (en mètres) pour accepter un pointage/un rapport.
    //  Tout pointage ou rapport à plus de 25 mètres du site sera rejeté.
    // ──────────────────────────────────────────────────────────────────────────
    public const MAX_ALLOWED_DISTANCE_METERS = 25;

    // ==========================================================================
    //  POINTAGE POUR LES STAGIAIRES (check-in / check-out)
    // ==========================================================================

    /**
     * Vérifie si aujourd'hui est un jour férié actif et bloque le pointage
     * si l'utilisateur n'a pas la permission de contournement.
     */
    protected function checkHolidayRestriction(User $user): void
    {
        if (Holiday::todayIsHoliday() && !$user->can('holidays.bypass') && !$this->isEmergencyExempted($user)) {
            throw ValidationException::withMessages([
                'presence' => "Aujourd'hui est un jour férié déclaré. Le pointage est désactivé sauf pour le personnel d'urgence.",
            ]);
        }
    }

    /**
     * Vérifie que la date de début de pointage effective de l'utilisateur est passée.
     * Bloque le pointage tant que la date choisie à l'inscription n'est pas atteinte
     * (aucune dérogation, même en cas d'exemption d'urgence).
     */
    protected function ensurePointageStarted(User $user): void
    {
        $start = $user->personnel?->date_debut_pointage;
        if (!$start) {
            return;
        }

        if (\Carbon\Carbon::parse($start)->startOfDay()->gt(today())) {
            throw ValidationException::withMessages([
                'presence' => "Votre prise de poste commencera le "
                    . \Carbon\Carbon::parse($start)->format('d/m/Y')
                    . ". Le pointage n'est pas encore activé.",
            ]);
        }
    }

    /**
     * Vérifie si aujourd'hui est un jour de présence pour le stage concerné.
     * Bloque le pointage si le stage n'a pas les jours de présence eux-mêmes configurés.
     */
protected function checkWorkDayRestriction(Stage $stage): void
    {
        if (!$stage->isWorkDay()) {
            throw ValidationException::withMessages([
                'presence' => "Aujourd'hui n'est pas un jour de travail pour ce stage. Jours de présence : {$stage->workDaysLabel()}.",
            ]);
        }
    }

    /**
     * Vérifie que le pointage d'arrivée (check-in) des stagiaires n'a lieu
     * qu'à partir de 07h30. En-deçà, le pointage est refusé.
     */
    /**
     * Interdit de pointer une arrivée alors que la journée est finie.
     *
     * Sans cela, quelqu'un qui n'a pas pointé de la journée pouvait déclarer
     * son arrivée à 19h et se voir compté présent : l'absence disparaissait,
     * et la note d'assiduité avec elle.
     */
    protected function checkArrivalWindow(?Stage $stage): void
    {
        $fin = app(WorkScheduleResolver::class)->expectedDeparture($stage, now());

        if (now()->greaterThanOrEqualTo($fin)) {
            throw ValidationException::withMessages([
                'presence' => "La journée est terminée depuis {$fin->format('H:i')}. Vous ne pouvez plus pointer votre arrivée.",
            ]);
        }
    }

    protected function checkCheckInOpeningTime(): void
    {
        if (now()->format('H:i') < '07:30') {
            throw ValidationException::withMessages([
                'presence' => "Le pointage d'arrivée sera ouvert à partir de 07h30.",
            ]);
        }
    }

    protected function isEmergencyExempted(User $user): bool
    {
        return HolidayEmergencyExemption::where('user_id', $user->id)
            ->whereHas('holiday', function ($q) {
                $q->whereDate('date', today())->where('is_active', true);
            })
            ->exists();
    }

    /**
     * Enregistre l'arrivée (check-in) d'un stagiaire.
     */
public function registerCheckIn(Stage $stage, User $user, array $payload, ?string $observation_message = null): AttendanceEvent
    {
        $this->ensurePointageStarted($user);
        $this->checkHolidayRestriction($user);
        $this->checkWorkDayRestriction($stage);
        $this->checkCheckInOpeningTime(); // ✅ pointage d'arrivée stagiaire ouvert à partir de 07h30
        $this->checkArrivalWindow($stage);
        return $this->registerEvent($stage, $user, $payload, 'check_in', $observation_message);
    }

    /**
     * Enregistre le départ (check-out) d'un stagiaire.
     */
    /**
     * Interdit le départ avant l'heure prévue.
     *
     * Le départ anticipé n'était jusqu'ici qu'une anomalie constatée après coup :
     * la journée était déjà écrite. Ici on refuse le pointage.
     *
     * Seule une permission « départ anticipé » approuvée POUR CE JOUR précis lève
     * l'interdiction — et seulement à partir de l'heure qu'elle autorise. Une
     * permission accordée un autre jour ne vaut pas laissez-passer permanent.
     */
    protected function checkDepartureTime(?Stage $stage, User $user): void
    {
        $expected = app(WorkScheduleResolver::class)->expectedDeparture($stage, now());

        if (now()->greaterThanOrEqualTo($expected)) {
            return;
        }

        $permission = $this->approvedEarlyDepartureForToday($user);

        if (!$permission) {
            throw ValidationException::withMessages([
                'presence' => "La journée n'est pas terminée. Votre départ est prévu à {$expected->format('H:i')}.",
            ]);
        }

        // La permission autorise un départ à une heure donnée, pas n'importe quand.
        $allowed = $permission->fields_data['departure_time'] ?? null;

        if ($allowed) {
            try {
                $allowedAt = today()->setTimeFromTimeString($allowed);
            } catch (\Throwable $e) {
                return; // heure illisible : on s'en tient à la permission accordée
            }

            if (now()->lessThan($allowedAt)) {
                throw ValidationException::withMessages([
                    'presence' => "Votre permission autorise un départ à partir de {$allowedAt->format('H:i')}.",
                ]);
            }
        }
    }

    /**
     * Permission de départ anticipé approuvée pour aujourd'hui, et pour aujourd'hui
     * seulement : on compare la date portée par la demande à la date du jour.
     */
    protected function approvedEarlyDepartureForToday(User $user): ?\App\Models\PermissionRequest
    {
        $type = \App\Models\PermissionType::where('slug', 'depart-anticipe')->first();

        if (!$type) {
            return null;
        }

        return \App\Models\PermissionRequest::where('user_id', $user->id)
            ->where('permission_type_id', $type->id)
            ->where('status', 'approved')
            ->latest('decided_at')
            ->get()
            ->first(function ($p) {
                $date = $p->fields_data['date'] ?? null;
                return $date && \Carbon\Carbon::parse($date)->isSameDay(today());
            });
    }

    public function registerCheckOut(Stage $stage, User $user, array $payload): AttendanceEvent
    {
        $this->checkHolidayRestriction($user);
        $this->checkWorkDayRestriction($stage);
        $this->checkDepartureTime($stage, $user);
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
        $this->ensurePointageStarted($user);
        $this->checkHolidayRestriction($user);
        $this->checkArrivalWindow(null);
        return $this->registerEmployeeEvent($user, $payload, 'check_in', $observation_message);
    }

    /**
     * Enregistre le départ (check-out) d'un employé.
     */
    public function registerEmployeeCheckOut(User $user, array $payload): AttendanceEvent
    {
        $this->checkHolidayRestriction($user);
        $this->checkDepartureTime(null, $user);
        return $this->registerEmployeeEvent($user, $payload, 'check_out');
    }

    /**
     * Pointage direct universel par QR Code (Stagiaires & Employés).
     * Détermine automatiquement s'il s'agit d'une Arrivée ou d'un Départ.
     */
    public function registerFromQrScan(User $user, \App\Models\Site $site, array $payload, ?TrustedDevice $device = null, ?string $observation = null): array
    {
        $this->ensurePointageStarted($user);
        $this->checkHolidayRestriction($user);

        // 1. CAS STAGIAIRE
        if ($user->etudiant) {
            $etudiant = $user->etudiant;

            // Recherche du stage actif pour ce site (ou stage actif général)
            $stage = $etudiant->stages()
                ->where('date_debut', '<=', today())
                ->where('date_fin', '>=', today())
                ->where(function ($q) use ($site) {
                    $q->where('site_id', $site->id)->orWhereNull('site_id');
                })
                ->first();

            if (!$stage) {
                $stage = $etudiant->stages()
                    ->where('date_debut', '<=', today())
                    ->where('date_fin', '>=', today())
                    ->first();
            }

            if (!$stage) {
                throw ValidationException::withMessages([
                    'presence' => "Aucun stage actif trouvé pour aujourd'hui.",
                ]);
            }

            $this->checkWorkDayRestriction($stage);

            $day = AttendanceDay::where('stage_id', $stage->id)
                ->where('etudiant_id', $etudiant->id)
                ->whereDate('attendance_date', today())
                ->first();

            if (!$day || !$day->first_check_in_at) {
                $this->checkCheckInOpeningTime();
                $this->checkArrivalWindow($stage);
                $eventType = 'check_in';
            } elseif (!$day->last_check_out_at) {
                $this->checkDepartureTime($stage, $user);
                $eventType = 'check_out';
            } else {
                return [
                    'status'     => 'already_completed',
                    'event_type' => 'completed',
                    'message'    => "Vos pointages d'arrivée et de départ sont déjà enregistrés pour aujourd'hui.",
                    'event'      => $day->checkOutEvent ?? $day->checkInEvent,
                ];
            }

            // Le retard est calculé ici, à partir de l'horaire du stage : le
            // navigateur n'a pas à en décider.
            $payload['is_late'] = $eventType === 'check_in'
                && now()->greaterThan(app(WorkScheduleResolver::class)->expectedArrival($stage, now()));

            $event = $this->registerEvent($stage, $user, $payload, $eventType, $observation);

            return [
                'status'     => $event->status === 'approved' ? 'approved' : 'rejected',
                'event_type' => $eventType,
                'message'    => $event->status === 'approved'
                    ? ($eventType === 'check_in' ? "Arrivée enregistrée avec succès !" : "Départ enregistré avec succès !")
                    : ($event->rejection_reason ?? "Pointage non validé."),
                'event'      => $event,
            ];
        }

        // 2. CAS EMPLOYÉ
        $domaine = $user->domaine ?? $user->getDomaineAttribute() ?? $site->domaines()->first() ?? Domaine::first();
        if (!$domaine) {
            throw ValidationException::withMessages([
                'presence' => "Aucun domaine assigné pour votre compte.",
            ]);
        }

        $day = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (!$day || !$day->first_check_in_at) {
            $this->checkArrivalWindow(null);
            $eventType = 'check_in';
        } elseif (!$day->last_check_out_at) {
            $this->checkDepartureTime(null, $user);
            $eventType = 'check_out';
        } else {
            return [
                'status'     => 'already_completed',
                'event_type' => 'completed',
                'message'    => "Vos pointages d'arrivée et de départ sont déjà enregistrés pour aujourd'hui.",
                'event'      => $day->checkOutEvent ?? $day->checkInEvent,
            ];
        }

        $payload['is_late'] = $eventType === 'check_in'
            && now()->greaterThan(app(WorkScheduleResolver::class)->expectedArrival(null, now()));

        $event = $this->registerEmployeeEvent($user, $payload, $eventType, $observation);

        return [
            'status'     => $event->status === 'approved' ? 'approved' : 'rejected',
            'event_type' => $eventType,
            'message'    => $event->status === 'approved'
                ? ($eventType === 'check_in' ? "Arrivée enregistrée avec succès !" : "Départ enregistré avec succès !")
                : ($event->rejection_reason ?? "Pointage non validé."),
            'event'      => $event,
        ];
    }

    /**
     * Ce que le scan va produire, sans rien enregistrer.
     *
     * Permet de demander l'observation de retard avant le pointage, comme le
     * fait le formulaire classique, plutôt que de constater le retard une fois
     * la journée écrite.
     *
     * @return array{event_type: string, is_late: bool, expected: string}
     */
    public function qrPreflight(User $user, \App\Models\Site $site): array
    {
        $resolver = app(WorkScheduleResolver::class);
        $stage    = null;

        if ($user->etudiant) {
            $stage = $user->etudiant->stages()
                ->where('date_debut', '<=', today())
                ->where('date_fin', '>=', today())
                ->where(function ($q) use ($site) {
                    $q->where('site_id', $site->id)->orWhereNull('site_id');
                })
                ->first()
                ?: $user->etudiant->stages()
                    ->where('date_debut', '<=', today())
                    ->where('date_fin', '>=', today())
                    ->first();

            $day = $stage
                ? AttendanceDay::where('stage_id', $stage->id)
                    ->whereDate('attendance_date', today())->first()
                : null;
        } else {
            $day = AttendanceDay::where('user_id', $user->id)
                ->whereDate('attendance_date', today())->first();
        }

        if (!$day || !$day->first_check_in_at) {
            $eventType = 'check_in';
        } elseif (!$day->last_check_out_at) {
            $eventType = 'check_out';
        } else {
            $eventType = 'completed';
        }

        $expected = $resolver->expectedArrival($stage, now());

        return [
            'event_type' => $eventType,
            'is_late'    => $eventType === 'check_in' && now()->greaterThan($expected),
            'expected'   => $expected->format('H:i'),
        ];
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

            $domaine = $user->domaine ?? $user->getDomaineAttribute() ?? Domaine::first();
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
            if ($this->isEmergencyExempted($user)) {
                $decision = [
                    'status'      => 'approved',
                    'reason_code' => 'emergency_exemption',
                    'message'     => 'Pointage urgence : toutes les contraintes sont levées.',
                ];
            } else {
                $decision = $this->evaluateEmployeeEvent($user, $eventType, $payload, $geofence, $distance, $hasCoordinates);
            }

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

            // ✅ Sync AttendanceDay FIRST so attendance_day_id is available for anomalies
            $this->syncEmployeeAttendanceDay($user, $event, $isLate);

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

        // --- 5) Vérification de la DISTANCE par rapport au site ---
        $maxDistance = $geofence?->radius_meters ?? self::MAX_ALLOWED_DISTANCE_METERS;
        if ($distance !== null && $distance > $maxDistance) {
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
                'message'     => "Vous êtes à plus de " . $maxDistance . " mètres du site autorisé.",
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
                ? app(WorkScheduleResolver::class)->workedMinutes(null, $day->first_check_in_at, $event->occurred_at)
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

            if ($this->isEmergencyExempted($user)) {
                $decision = [
                    'status'      => 'approved',
                    'reason_code' => 'emergency_exemption',
                    'message'     => 'Pointage urgence : toutes les contraintes sont levées.',
                ];
            } else {
                $decision = $this->evaluateEvent($stage, $eventType, $payload, $geofence, $distance);
            }

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

            // ✅ Sync AttendanceDay FIRST so attendance_day_id is available for anomalies
            $this->syncAttendanceDay($stage, $etudiant, $event, $isLate);

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
    public function resolveTrustedDevice(User $user, array $payload): TrustedDevice
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
            'device_uuid'      => $payload['device_uuid'] ?? $device->device_uuid ?? null,
            'device_label'     => $payload['device_label'] ?? $device->device_label ?? null,
            'device_name'      => $payload['device_name'] ?? $device->device_name ?? null,
            'platform'         => $payload['platform'] ?? $device->platform ?? null,
            'browser'          => $payload['browser'] ?? $device->browser ?? null,
            'app_version'      => $payload['app_version'] ?? $device->app_version ?? null,
            'last_ip_address'  => request()->ip(),
            'last_seen_at'     => now(),
        ]);
        $device->save();

        return $device;
    }

    /**
     * Enregistre une anomalie si le pointage provient d'un appareil secondaire, partagé ou nouveau.
     */
    protected function recordDeviceSwitchAnomalyIfNeeded(AttendanceEvent $event, ?TrustedDevice $device): void
    {
        if (!$device) {
            return;
        }

        $user = $event->user ?? User::find($event->user_id);
        if (!$user) {
            return;
        }

        $notificationService = app(NotificationService::class);

        // 1. Détection de téléphone partagé/prêté (appartient comme badge à un autre utilisateur)
        $sharedDevice = TrustedDevice::where('device_fingerprint', $device->device_fingerprint)
            ->where('user_id', '!=', $user->id)
            ->where('is_qr_badge', true)
            ->whereNull('revoked_at')
            ->with('user')
            ->first();

        if ($sharedDevice && $sharedDevice->user) {
            $ownerName = $sharedDevice->user->name;
            $this->recordAnomaly($event, 'shared_device_detected', 'medium', [
                'message'          => "Pointage effectué avec l'appareil de {$ownerName}.",
                'owner_user_id'    => $sharedDevice->user_id,
                'owner_name'       => $ownerName,
                'device_fingerprint' => $device->device_fingerprint,
                'device_label'     => $device->device_label ?? 'Smartphone',
            ]);

            $notificationService->notifyAdminsOfDeviceAnomaly($user, 'shared_device_detected', [
                'owner_name'   => $ownerName,
                'device_label' => $device->device_label ?? 'Smartphone',
            ]);
            return;
        }

        // 2. Nouvel appareil secondaire
        if ($device->wasRecentlyCreated && !$device->is_primary) {
            $this->recordAnomaly($event, 'secondary_device_detected', 'low', [
                'message'          => 'Pointage effectué depuis un nouvel appareil secondaire.',
                'trusted_device_id'=> $device->id,
                'device_label'     => $device->device_label ?? 'Smartphone',
                'device_uuid'      => $device->device_uuid,
                'is_trusted'       => $device->is_trusted,
            ]);

            $notificationService->notifyAdminsOfDeviceAnomaly($user, 'new_device_detected', [
                'device_label' => $device->device_label ?? 'Smartphone',
            ]);
        }
    }

    /**
     * Évalue la validité du pointage pour un stagiaire.
     * NOUVEAU : la distance maximale acceptée est de 100 mètres.
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

        // Vérification de la distance par rapport au rayon autorisé
        $maxDistance = $geofence?->radius_meters ?? self::MAX_ALLOWED_DISTANCE_METERS;
        if ($distance !== null && $distance > $maxDistance) {
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
                'message'     => "Vous êtes à plus de " . $maxDistance . " mètres du site autorisé.",
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
            $day->arrival_status    = $this->computeArrivalStatus($event->occurred_at, $stage);
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
                ? app(WorkScheduleResolver::class)->workedMinutes($stage, $day->first_check_in_at, $event->occurred_at)
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
            'attendance_day_id'   => $event->checkInDay?->id ?? $event->checkOutDay?->id,
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
        $expected = app(WorkScheduleResolver::class)->expectedArrival($stage, $occurredAt);

        return $occurredAt->greaterThan($expected) ? $expected->diffInMinutes($occurredAt) : 0;
    }

    /**
     * Calcule les minutes de départ anticipé par rapport à l'heure attendue.
     */
    protected function computeEarlyDepartureMinutes(Stage $stage, $occurredAt): int
    {
        $expected = app(WorkScheduleResolver::class)->expectedDeparture($stage, $occurredAt);

        return $occurredAt->lessThan($expected) ? $occurredAt->diffInMinutes($expected) : 0;
    }

    /**
     * Détermine le statut d'arrivée : "ontime" (avant 8h) ou "late".
     */
    protected function computeArrivalStatus($occurredAt, ?Stage $stage = null): string
    {
        // L'heure attendue vient de l'horaire du jour, du stage, ou de la
        // configuration. Avant, un 08:00 en dur déclarait en retard un stagiaire
        // arrivé à 08:20 pour un stage commençant à 08:30.
        $threshold = app(WorkScheduleResolver::class)->expectedArrival($stage, $occurredAt);

        return $occurredAt->greaterThan($threshold) ? 'late' : 'ontime';
    }

    /**
     * Vérifie si une position GPS est à moins de MAX_ALLOWED_DISTANCE_METERS
     * du site (géofence active) fourni.
     *
     * @param float $latitude
     * @param float $longitude
     * @param \App\Models\Site|null $site
     * @return array{verified: bool, distance_meters: ?int, message: string, geofence_center_lat: ?float, geofence_center_lng: ?float}
     */
    public function verifyLocationOnSite(float $latitude, float $longitude, ?\App\Models\Site $site): array
    {
        if (!$site) {
            return [
                'verified'     => false,
                'distance_meters' => null,
                'message'      => 'Aucun site associé pour vérifier la position.',
                'geofence_center_lat' => null,
                'geofence_center_lng' => null,
            ];
        }

        $geofence = $site->geofences()->where('is_active', true)->orderByDesc('is_primary')->first();

        if (!$geofence) {
            return [
                'verified'     => false,
                'distance_meters' => null,
                'message'      => 'Aucune zone de présence (géofence) active configurée pour ce site.',
                'geofence_center_lat' => null,
                'geofence_center_lng' => null,
            ];
        }

        $distance = $this->calculateDistanceMeters(
            $latitude,
            $longitude,
            (float) $geofence->center_latitude,
            (float) $geofence->center_longitude
        );

        $maxDistance = $geofence->radius_meters ?? self::MAX_ALLOWED_DISTANCE_METERS;
        $verified = $distance <= $maxDistance;

        return [
            'verified'     => $verified,
            'distance_meters' => $distance,
            'message'      => $verified
                ? "Position validée (à {$distance} m du site)."
                : "Vous êtes à {$distance} m du site. Maximum autorisé : " . $maxDistance . " m.",
            'geofence_center_lat' => (float) $geofence->center_latitude,
            'geofence_center_lng' => (float) $geofence->center_longitude,
        ];
    }

    /**
     * Calcule la distance en mètres entre deux points GPS (formule de Haversine).
     */
    public function calculateDistanceMeters(float $latFrom, float $lngFrom, float $latTo, float $lngTo): int
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