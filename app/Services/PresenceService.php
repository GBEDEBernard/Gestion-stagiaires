<?php

namespace App\Services;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\Etudiant;
use App\Models\SiteGeofence;
use App\Models\Stage;
use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
            // jb -> Premier telephone rencontre = appareil principal.
            // Les appareils suivants restent autorises pour ne pas freiner
            // le pointage, mais ils seront distingues comme secondaires.
            $device->first_seen_at = now();
            $device->is_trusted = true;
            $device->is_primary = !$hasKnownDevices || !$primaryDeviceExists;
        } elseif (!$device->is_primary && !$primaryDeviceExists) {
            // jb -> Filet de securite pour les anciennes donnees:
            // si aucun principal n'est defini, on promeut automatiquement
            // le premier appareil retrouve.
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

    protected function recordDeviceSwitchAnomalyIfNeeded(AttendanceEvent $event, ?TrustedDevice $device): void
    {
        if (!$device || !$device->wasRecentlyCreated || $device->is_primary) {
            return;
        }

        // jb -> Un nouvel appareil secondaire ne bloque pas le pointage,
        // mais il doit remonter en surveillance pour repérer les usages
        // inhabituels ou les relais de compte.
        $this->recordAnomaly($event, 'secondary_device_detected', 'low', [
            'message' => 'Pointage effectue depuis un appareil secondaire.',
            'trusted_device_id' => $device->id,
            'device_label' => $device->device_label,
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
                'message' => 'Aucune zone de presence active n’est configuree pour ce site.',
                'anomaly' => 'missing_geofence',
                'severity' => 'high',
            ];
        }

        if (!empty($payload['accuracy_meters']) && $payload['accuracy_meters'] > $geofence->allowed_accuracy_meters) {
            return [
                'status' => 'rejected',
                'reason_code' => 'gps_accuracy_low',
                'message' => 'La precision GPS est insuffisante pour valider votre presence.',
                'anomaly' => 'gps_accuracy_low',
                'severity' => 'medium',
            ];
        }

        if ($distance !== null && $distance > $geofence->radius_meters) {
            return [
                'status' => 'rejected',
                'reason_code' => 'outside_geofence',
                'message' => "Vous etes hors de la zone autorisee pour ce stage.",
                'anomaly' => 'outside_geofence',
                'severity' => 'high',
            ];
        }

        $day = AttendanceDay::where('etudiant_id', $etudiant->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($eventType === 'check_in' && $day?->first_check_in_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'duplicate_checkin',
                'message' => "L'arrivee a deja ete enregistree aujourd'hui.",
                'anomaly' => 'duplicate_checkin',
                'severity' => 'medium',
            ];
        }

        if ($eventType === 'check_out' && !$day?->first_check_in_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'checkout_without_checkin',
                'message' => "Impossible d'enregistrer le depart sans arrivee.",
                'anomaly' => 'checkout_without_checkin',
                'severity' => 'medium',
            ];
        }

        if ($eventType === 'check_out' && $day?->last_check_out_at) {
            return [
                'status' => 'rejected',
                'reason_code' => 'duplicate_checkout',
                'message' => 'Le depart a deja ete enregistre aujourd’hui.',
                'anomaly' => 'duplicate_checkout',
                'severity' => 'medium',
            ];
        }

        return [
            'status' => 'approved',
            'reason_code' => 'ok',
            'message' => $eventType === 'check_in'
                ? "Presence d'arrivee enregistree."
                : 'Presence de depart enregistree.',
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

    protected function recordAnomaly(AttendanceEvent $event, string $type, string $severity, array $payload = []): void
    {
        AttendanceAnomaly::create([
            'attendance_event_id' => $event->id,
            'stage_id' => $event->stage_id,
            'etudiant_id' => $event->etudiant_id,
            'anomaly_type' => $type,
            'severity' => $severity,
            'status' => 'open',
            'detected_at' => now(),
            'payload' => $payload,
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
