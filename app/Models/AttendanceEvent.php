<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'etudiant_id',
        'site_id',
        'site_geofence_id',
        'user_id',
        'trusted_device_id',
        'event_type',
        'status',
        'occurred_at',
        'latitude',
        'longitude',
        'accuracy_meters',
        'distance_to_site_meters',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'reason_code',
        'rejection_reason',
        'meta',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'meta' => 'array',
    ];
// un événement peut être lié à un stage spécifique (stage_id) pour filtrer les événements par stage (pour stagiaires)
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
// un événement peut être lié à un étudiant spécifique (etudiant_id) pour filtrer les événements par étudiant (pour stagiaires)
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
// un événement peut être lié à un site spécifique (site_id) et à une zone géographique du site (site_geofence_id)
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
// un événement peut être lié à une zone géographique spécifique du site (site_geofence_id)
    public function geofence()
    {
        return $this->belongsTo(SiteGeofence::class, 'site_geofence_id');
    }
// un événement peut être lié à un utilisateur spécifique (user_id) pour filtrer les événements par utilisateur (pour employés)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
// un événement peut être lié à un appareil de confiance spécifique (trusted_device_id) pour filtrer les événements par appareil de confiance
    public function trustedDevice()
    {
        return $this->belongsTo(TrustedDevice::class);
    }
// un événement peut être lié à une anomalie spécifique (via la relation anomalies) pour accéder facilement aux détails des anomalies associées à cet événement
    public function anomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }
// Relations pour accéder facilement aux jours de présence associés à cet événement, que ce soit en tant que check-in ou check-out
    public function checkInDay()
    {
        return $this->hasOne(AttendanceDay::class, 'check_in_event_id');
    }
// Relation pour accéder au jour de présence associé à cet événement en tant que check-out, utile pour les rapports détaillés et les anomalies liées au départ
    public function checkOutDay()
    {
        return $this->hasOne(AttendanceDay::class, 'check_out_event_id');
    }
}
