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
    public function registerCheckIn(Stage $stage, User $user, array $payload): AttendanceEvent
    {
        return $this->registerEvent($stage, $user, $payload, 'check_in');
    }

    public function registerCheckOut(Stage $stage, User $user, array $payload): AttendanceEvent
    {
        return $this->registerEvent($stage, $user, $payload, 'check_out');
    }

    /**
     * Register employee check-in (no stage required).
     */
    public function registerEmployeeCheckIn(User $user, array $payload): AttendanceEvent
    {
        return $this->registerEmployeeEvent($user, $payload, 'check_in');
    }

    /**
     * Register employee check-out (no stage required).
     */
    public function registerEmployeeCheckOut(User $user, array $payload): AttendanceEvent
    {
        return $this->registerEmployeeEvent($user, $payload, 'check_out');
    }

    protected function registerEmployeeEvent(User $user, array $payload, string $eventType): AttendanceEvent
    {
        return DB::transaction(function () use ($user, $payload, $eventType) {
            $hasCoordinates = isset($payload['latitude'], $payload['longitude']) && $payload['latitude'] !== null && $payload['longitude'] !== null;

            $domaine = $user->domaine;
            if (!$domaine) {
                throw ValidationException::withMessages(['presence' => 'Aucun domaine assigné pour le pointage.']);
            }

            $device = $this->resolveTrustedDevice($user, $payload);

            if ($hasCoordinates) {
                [$site, $geofence, $distance] = $this->resolveEmployeeSiteGeofence($domaine, $payload);
            } else {
                // No GPS, assume at primary site
                $site = $domaine->sites()->first();
                $geofence = $site?->geofences()->where('is_active', true)->first();
                $distance = 0;
            }

            $decision = $this->evaluateEmployeeEvent($user, $eventType, $payload, $geofence, $distance, $hasCoordinates);

            $event = AttendanceEvent::create([
                'stage_id' => null,
                'etudiant_id' => null,
                'site_id' => $site?->id,
                'site_geofence_id' => $geofence?->id,
                'user_id' => $user->id,
                'trusted_device_id' => $device?->id,
                'event_type' => $eventType,
                'status' => $decision['status'],
                'occurred_at' => now(),
                'latitude' => $payload['latitude'],
                'longitude' => $payload['longitude'],
                'accuracy_meters' => $payload['accuracy_meters'] ?? null,
                'distance_to_site_meters' => $distance,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_fingerprint' => $device?->device_fingerprint ?? $this->fallbackFingerprint($payload),
                'reason_code' => $decision['reason_code'],
                'rejection_reason' => $decision['status'] === 'rejected' ? $decision['message'] : null,
                'meta' => [
                    'domaine_id' => $domaine->id,
                    'platform' => $payload['platform'] ?? null,
                    'browser' => $payload['browser'] ?? null,
                    'app_version' => $payload['app_version'] ?? null,
                    'device_role' => $device?->is_primary ? 'primary' : 'secondary',
                ],
            ]);

            if (!empty($decision['anomaly'])) {
                $this->recordAnomaly($event, $decision['anomaly'], $decision['severity'] ?? 'medium', [
                    'message' => $decision['message'],
                    'domaine' => $domaine->nom,
                ]);
            }

            $this->recordDeviceSwitchAnomalyIfNeeded($event, $device);
            $this->syncEmployeeAttendanceDay($user, $event);

            return $event;
        });
    }

    protected function resolveEmployeeSiteGeofence(Domaine $domaine, array $payload): array
    {
        $latitude = (float) $payload['latitude'];
        $longitude = (float) $payload['longitude'];

        $sites = $domaine->sites()->with(['geofences' => function ($query) {
            $query->where('is_active', true);
        }])->where('is_active', true)->get();

        $best = null;
        foreach ($sites as $site) {
            foreach ($site->geofences as $geofence) {
                $distance = $this->calculateDistanceMeters(
                    $latitude,
                    $longitude,
                    (float) $geofence->center_latitude,
                    (float) $geofence->center_longitude
                );

                if ($best === null || $distance < $best['distance']) {
                    $best = [
                        'site' => $site,
                        'geofence' => $geofence,
                        'distance' => $distance,
                    ];
                }
            }
        }

        if (!$best) {
            return [null, null, null];
        }

        return [$best['site'], $best['geofence'], $best['distance']];
    }

    protected function evaluateEmployeeEvent(User $user, string $eventType, array $payload, ?SiteGeofence $geofence, ?int $distance, bool $hasCoordinates = true): array
    {
        $day = AttendanceDay::where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($eventType === 'check_in' && $day?->first_check_in_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'duplicate_checkin',
                'message' => "L'arrivée a déjà été enregistrée aujourd'hui.",
                'anomaly' => 'duplicate_checkin',
                'severity' => 'medium',
            ];
        }

        if ($eventType === 'check_out' && !$day?->first_check_in_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'checkout_without_checkin',
                'message' => "Impossible d'enregistrer le départ sans arrivée.",
                'anomaly' => 'checkout_without_checkin',
                'severity' => 'medium',
            ];
        }

        if ($eventType === 'check_out' && $day?->last_check_out_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'duplicate_checkout',
                'message' => 'Le départ a déjà été enregistré aujourd\'hui.',
                'anomaly' => 'duplicate_checkout',
                'severity' => 'medium',
            ];
        }

        if (!$geofence) {
            return [
                'status' => 'approved', // No geofence = OK for employees
                'reason_code' => 'no_geofence',
                'message' => 'Pointage enregistré (aucune géofence configurée).',
            ];
        }

        if (!$hasCoordinates) {
            return [
                'status' => 'approved',
                'reason_code' => 'no_gps',
                'message' => 'Pointage enregistré (GPS non disponible).',
                'anomaly' => 'no_gps',
                'severity' => 'low',
            ];
        }

        if (!empty($payload['accuracy_meters']) && $payload['accuracy_meters'] > $geofence->allowed_accuracy_meters) {
            return [
                'status' => 'rejected',
                'reason_code' => 'gps_accuracy_low',
                'message' => 'Précision GPS insuffisante.',
                'anomaly' => 'gps_accuracy_low',
                'severity' => 'medium',
            ];
        }

        if ($distance > $geofence->radius_meters) {
            return [
                'status' => 'rejected',
                'reason_code' => 'outside_geofence',
                'message' => "Hors zone autorisée.",
                'anomaly' => 'outside_geofence',
                'severity' => 'high',
            ];
        }

        return [
            'status' => 'approved',
            'reason_code' => 'ok',
            'message' => $eventType === 'check_in' ? "Arrivée enregistrée." : 'Départ enregistré.',
        ];
    }

    protected function syncEmployeeAttendanceDay(User $user, AttendanceEvent $event): void
    {
        $day = AttendanceDay::firstOrNew([
            'user_id' => $user->id,
            'attendance_date' => today()->toDateString(),
        ]);

        // Safeguard explicite pour employés (même avec $attributes model)
        $day->etudiant_id = null;
        $day->stage_id = null;

        if ($event->event_type === 'check_in' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_in_event_id = $event->id;
            $day->first_check_in_at = $event->occurred_at;
            $day->late_minutes = 0; // Fixed hours or config later
            $day->arrival_status = $this->computeArrivalStatus($event->occurred_at);
            $day->day_status = match ($day->arrival_status) {
                'late' => 'late',
                'warning' => 'warning',
                'ontime' => 'present',
                default => 'present',
            };
            $day->validation_status = 'auto_approved';
        }

        if ($event->event_type === 'check_out' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_out_event_id = $event->id;
            $day->last_check_out_at = $event->occurred_at;
            $day->worked_minutes = $day->first_check_in_at
                ? max(0, $day->first_check_in_at->diffInMinutes($event->occurred_at))
                : 0;
            $day->early_departure_minutes = 0; // Config later
        }

        $day->save();
    }

    // Existing student methods (kept intact)
    protected function registerEvent(Stage $stage, User $user, array $payload, string $eventType): AttendanceEvent
    {
        return DB::transaction(function () use ($stage, $user, $payload, $eventType) {
            $etudiant = $this->resolveEtudiant($user, $stage);
            $this->ensureStageOwnership($stage, $etudiant);

            $device = $this->resolveTrustedDevice($user, $payload);
            $geofence = $stage->site?->geofences()->where('is_active', true)->orderByDesc('is_primary')->first();

            $distance = $geofence
                ? $this->calculateDistanceMeters(
                    (float) $payload['latitude'],
                    (float) $payload['longitude'],
                    (float) $geofence->center_latitude,
                    (float) $geofence->center_longitude
                )
                : null;

            $decision = $this->evaluateEvent($stage, $eventType, $payload, $geofence, $distance);

            $event = AttendanceEvent::create([
                'stage_id' => $stage->id,
                'etudiant_id' => $etudiant->id,
                'site_id' => $stage->site_id,
                'site_geofence_id' => $geofence?->id,
                'user_id' => $user->id,
                'trusted_device_id' => $device?->id,
                'event_type' => $eventType,
                'status' => $decision['status'],
                'occurred_at' => now(),
                'latitude' => $payload['latitude'],
                'longitude' => $payload['longitude'],
                'accuracy_meters' => $payload['accuracy_meters'] ?? null,
                'distance_to_site_meters' => $distance,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_fingerprint' => $device?->device_fingerprint ?? $this->fallbackFingerprint($payload),
                'reason_code' => $decision['reason_code'],
                'rejection_reason' => $decision['message'],
                'meta' => [
                    'platform' => $payload['platform'] ?? null,
                    'browser' => $payload['browser'] ?? null,
                    'app_version' => $payload['app_version'] ?? null,
                    'device_role' => $device?->is_primary ? 'primary' : 'secondary',
                ],
            ]);

            if (!empty($decision['anomaly'])) {
                $this->recordAnomaly($event, $decision['anomaly'], $decision['severity'] ?? 'medium', [
                    'message' => $decision['message'],
                ]);
            }

            $this->recordDeviceSwitchAnomalyIfNeeded($event, $device);

            $this->syncAttendanceDay($stage, $etudiant, $event);

            return $event;
        });
    }

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

    protected function ensureStageOwnership(Stage $stage, Etudiant $etudiant): void
    {
        if ((int) $stage->etudiant_id !== (int) $etudiant->id) {
            throw ValidationException::withMessages([
                'presence' => "Vous n'etes pas autorise a pointer pour ce stage.",
            ]);
        }
    }

    protected function resolveTrustedDevice(User $user, array $payload): TrustedDevice
    {
        $fingerprint = $payload['device_fingerprint'] ?? $this->fallbackFingerprint($payload);
        $hasKnownDevices = TrustedDevice::where('user_id', $user->id)->exists();
        $primaryDeviceExists = TrustedDevice::where('user_id', $user->id)
            ->where('is_primary', true)
            ->exists();

        $device = TrustedDevice::firstOrNew([
            'user_id' => $user->id,
            'device_fingerprint' => $fingerprint,
        ]);

        if (!$device->exists) {
            $device->first_seen_at = now();
            $device->is_trusted = true;
            $device->is_primary = !$hasKnownDevices || !$primaryDeviceExists;
        } elseif (!$device->is_primary && !$primaryDeviceExists) {
            $device->is_primary = true;
        }

        $device->fill([
            'device_uuid' => $payload['device_uuid'] ?? null,
            'device_label' => $payload['device_label'] ?? null,
            'platform' => $payload['platform'] ?? null,
            'browser' => $payload['browser'] ?? null,
            'app_version' => $payload['app_version'] ?? null,
            'last_ip_address' => request()->ip(),
            'last_seen_at' => now(),
        ]);
        $device->save();

        return $device;
    }

    /**
     * Enregistre une anomalie si pointage depuis appareil secondaire.
     * Compatible employés/stagiaires via recordAnomaly amélioré.
     */
    protected function recordDeviceSwitchAnomalyIfNeeded(AttendanceEvent $event, ?TrustedDevice $device): void
    {
        if (!$device || !$device->wasRecentlyCreated || $device->is_primary) {
            return;
        }

        $this->recordAnomaly($event, 'secondary_device_detected', 'low', [
            'message' => 'Pointage effectué depuis un appareil secondaire (non principal).',
            'trusted_device_id' => $device->id,
            'device_label' => $device->device_label,
            'device_uuid' => $device->device_uuid,
            'is_trusted' => $device->is_trusted,
        ]);
    }



    protected function evaluateEvent(Stage $stage, string $eventType, array $payload, ?SiteGeofence $geofence, ?int $distance): array
    {
        if (!$this->isStageActive($stage)) {
            return [
                'status' => 'rejected',
                'reason_code' => 'stage_inactive',
                'message' => "Le stage n'est pas actif pour ce pointage.",
                'anomaly' => 'stage_inactive',
                'severity' => 'high',
            ];
        }

        if (!$geofence) {
            return [
                'status' => 'flagged',
                'reason_code' => 'missing_geofence',
                'message' => 'Aucune zone de présence active n\'est configurée pour ce site.',
                'anomaly' => 'missing_geofence',
                'severity' => 'high',
            ];
        }

        if (!empty($payload['accuracy_meters']) && $payload['accuracy_meters'] > $geofence->allowed_accuracy_meters) {
            return [
                'status' => 'rejected',
                'reason_code' => 'gps_accuracy_low',
                'message' => 'La précision GPS est insuffisante pour valider votre présence.',
                'anomaly' => 'gps_accuracy_low',
                'severity' => 'medium',
            ];
        }

        if ($distance !== null && $distance > $geofence->radius_meters) {
            return [
                'status' => 'rejected',
                'reason_code' => 'outside_geofence',
                'message' => "Vous êtes hors de la zone autorisée pour ce stage.",
                'anomaly' => 'outside_geofence',
                'severity' => 'high',
            ];
        }

        $day = AttendanceDay::where('etudiant_id', $stage->etudiant_id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($eventType === 'check_in' && $day?->first_check_in_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'duplicate_checkin',
                'message' => "L'arrivée a déjà été enregistrée aujourd'hui.",
                'anomaly' => 'duplicate_checkin',
                'severity' => 'medium',
            ];
        }

        if ($eventType === 'check_out' && !$day?->first_check_in_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'checkout_without_checkin',
                'message' => "Impossible d'enregistrer le départ sans arrivée.",
                'anomaly' => 'checkout_without_checkin',
                'severity' => 'medium',
            ];
        }

        if ($eventType === 'check_out' && $day?->last_check_out_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'duplicate_checkout',
                'message' => 'Le départ a déjà été enregistré aujourd\'hui.',
                'anomaly' => 'duplicate_checkout',
                'severity' => 'medium',
            ];
        }

        return [
            'status' => 'approved',
            'reason_code' => 'ok',
            'message' => $eventType === 'check_in'
                ? "Présence d'arrivée enregistrée."
                : 'Présence de départ enregistrée.',
        ];
    }

    protected function syncAttendanceDay(Stage $stage, Etudiant $etudiant, AttendanceEvent $event): void
    {
        $day = AttendanceDay::firstOrNew([
            'etudiant_id' => $etudiant->id,
            'attendance_date' => today()->toDateString(),
        ]);

        $day->fill([
            'stage_id' => $stage->id,
            'site_id' => $stage->site_id,
        ]);

        if ($event->event_type === 'check_in' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_in_event_id = $event->id;
            $day->first_check_in_at = $event->occurred_at;
            $day->late_minutes = $this->computeLateMinutes($stage, $event->occurred_at);
            $day->arrival_status = $this->computeArrivalStatus($event->occurred_at);
            $day->day_status = match ($day->arrival_status) {
                'late' => 'late',
                'warning' => 'warning',
                'ontime' => 'present',
                default => 'present',
            };
            $day->validation_status = 'auto_approved';
        }

        if ($event->event_type === 'check_out' && in_array($event->status, ['approved', 'flagged'])) {
            $day->check_out_event_id = $event->id;
            $day->last_check_out_at = $event->occurred_at;
            $day->worked_minutes = $day->first_check_in_at
                ? max(0, $day->first_check_in_at->diffInMinutes($event->occurred_at))
                : 0;
            $day->early_departure_minutes = $this->computeEarlyDepartureMinutes($stage, $event->occurred_at);

            if ($day->early_departure_minutes > 0) {
                $day->day_status = 'incomplete';
                $day->validation_status = 'needs_review';
            }
        }

        $day->save();
    }



    /**
     * Enregistre une anomalie de présence de manière compatible avec les employés et stagiaires.
     * Pour les employés: stage_id et etudiant_id = null, user_id renseigné.
     * Pour les stagiaires: stage_id et etudiant_id renseignés.
     */
    protected function recordAnomaly(AttendanceEvent $event, string $type, string $severity, array $payload = []): void
    {
        AttendanceAnomaly::create([
            'attendance_event_id' => $event->id,
            'attendance_day_id' => $event->attendanceDay?->id ?? null,
            'stage_id' => $event->stage_id ?? null,        // NULL pour employés
            'etudiant_id' => $event->etudiant_id ?? null,  // NULL pour employés
            'user_id' => $event->user_id,                  // TOUJOURS renseigné
            'anomaly_type' => $type,
            'severity' => $severity,
            'status' => 'open',
            'detected_at' => now(),
            'payload' => array_merge($payload, [
                'event_type' => $event->event_type,
                'user_type' => $event->etudiant_id ? 'etudiant' : 'employe',
                'status' => $event->status,
            ]),
        ]);
    }

    protected function isStageActive(Stage $stage): bool
    {
        if (!$stage->date_debut || !$stage->date_fin) {
            return false;
        }

        return now()->between($stage->date_debut->copy()->startOfDay(), $stage->date_fin->copy()->endOfDay());
    }

    protected function computeLateMinutes(Stage $stage, $occurredAt): int
    {
        if (!$stage->expected_check_in_time) {
            return 0;
        }

        $expected = $occurredAt->copy()->setTimeFromTimeString($stage->expected_check_in_time);
        $grace = (int) ($stage->allowed_late_minutes ?? 0);
        $effectiveExpected = $expected->copy()->addMinutes($grace);

        return $occurredAt->greaterThan($effectiveExpected)
            ? $effectiveExpected->diffInMinutes($occurredAt)
            : 0;
    }

    protected function computeEarlyDepartureMinutes(Stage $stage, $occurredAt): int
    {
        if (!$stage->expected_check_out_time) {
            return 0;
        }

        $expected = $occurredAt->copy()->setTimeFromTimeString($stage->expected_check_out_time);
        $grace = (int) ($stage->allowed_early_departure_minutes ?? 0);
        $effectiveExpected = $expected->copy()->subMinutes($grace);

        return $occurredAt->lessThan($effectiveExpected)
            ? $occurredAt->diffInMinutes($effectiveExpected)
            : 0;
    }

    /**
     * Calcule le statut d'arrivée: ontime (7h00-7h45), warning (7h50-7h59), late (>=8h00)
     */
    protected function computeArrivalStatus($occurredAt): string
    {
        $hour = $occurredAt->hour;
        $minute = $occurredAt->minute;

        $timeMinutes = $hour * 60 + $minute;

        if ($timeMinutes <= (7 * 60 + 45)) {
            return 'ontime'; // Vert: à l'heure
        } elseif ($timeMinutes <= (7 * 60 + 59)) {
            return 'warning'; // Jaune: tend vers le retard
        } else {
            return 'late'; // Rouge: en retard
        }
    }

    protected function calculateDistanceMeters(float $latFrom, float $lngFrom, float $latTo, float $lngTo): int
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

    protected function fallbackFingerprint(array $payload): string
    {
        return sha1(implode('|', [
            $payload['device_uuid'] ?? 'no-device-uuid',
            request()->userAgent() ?? 'no-user-agent',
            request()->ip() ?? 'no-ip',
        ]));
    }
}
