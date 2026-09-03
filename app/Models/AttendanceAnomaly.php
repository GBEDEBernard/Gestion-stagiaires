<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceAnomaly extends Model
{
    use HasFactory;

    /**
     * Champs fillables pour création d'anomalies.
     * Compatible stagiaires (stage_id/etudiant_id) et employés (user_id).
     */
    protected $fillable = [
        'attendance_event_id',
        'attendance_day_id',
        'stage_id',           // NULL pour employés
        'etudiant_id',        // NULL pour employés
        'user_id',            // Pour employés
        'reviewed_by',
        'anomaly_type',
        'severity',
        'status',
        'detected_at',
        'reviewed_at',
        'resolution_note',
        'payload',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'payload' => 'array',
    ];
//   protected $dates = ['detected_at', 'reviewed_at'];
    public function attendanceEvent()
    {
        return $this->belongsTo(AttendanceEvent::class);
    }
// relation avec attendance_day pour faciliter les requêtes basées sur la date de présence
    public function attendanceDay()
    {
        return $this->belongsTo(AttendanceDay::class);
    }
// relation avec stage pour filtrer les anomalies par stage (pour stagiaires)
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
// relation avec étudiant pour filtrer les anomalies par étudiant (pour stagiaires)
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Relation avec l'utilisateur concerné (pour employés).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le reviewer/admin qui traite l'anomalie.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope : Retards d'arrivée ouverts (pour admin)
     */
    public function scopeRetardsOuverts($query)
    {
        return $query->where('anomaly_type', 'retard_arrivee')
            ->where('status', 'open')
            ->with(['attendanceEvent.user', 'reviewer'])
            ->orderBy('detected_at', 'desc');
    }

    /**
     * Scope : Toutes observations retard (archivées incluses)
     */
    public function scopeToutesObservationsRetard($query)
    {
        return $query->where('anomaly_type', 'retard_arrivee')
            ->with(['attendanceEvent.user', 'reviewer'])
            ->orderBy('detected_at', 'desc');
    }

    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'outside_geofence'          => 'Hors zone autorisée',
            'no_gps'                    => 'GPS non disponible',
            'gps_accuracy_low'          => 'Précision GPS insuffisante',
            'missing_geofence'          => 'Zone non configurée',
            'duplicate_checkin'         => "Double pointage d'arrivée",
            'duplicate_checkout'        => 'Double pointage de départ',
            'checkout_without_checkin'  => 'Départ sans arrivée',
            'stage_inactive'            => 'Stage inactif',
            'retard_arrivee'            => "Retard à l'arrivée",
            'secondary_device_detected' => 'Appareil secondaire détecté',
            'shared_device_detected'    => "Téléphone d'un collègue utilisé",
            'new_device_detected'       => 'Nouvel appareil détecté',
        ];
        return $labels[$this->anomaly_type] ?? ucfirst(str_replace('_', ' ', $this->anomaly_type));
    }

    public function getTypeDescriptionAttribute(): string
    {
        $descriptions = [
            'outside_geofence'          => "L'utilisateur a pointé en dehors du périmètre autorisé (géofence). La position GPS enregistrée est à plus de 100 m du site.",
            'no_gps'                    => "L'événement de pointage ne comporte aucune coordonnée GPS. Impossible de vérifier la localisation.",
            'gps_accuracy_low'          => "La précision du GPS était insuffisante (probablement réseau externe). L'utilisateur était peut-être connecté via données mobiles.",
            'missing_geofence'          => "Aucune zone de géofence n'est configurée pour ce site. Les contrôles de localisation ne peuvent pas être effectués.",
            'duplicate_checkin'         => "L'utilisateur a tenté un deuxième pointage d'arrivée alors qu'il est déjà en statut présent.",
            'duplicate_checkout'        => "L'utilisateur a tenté un deuxième pointage de départ alors qu'il est déjà en statut absent.",
            'checkout_without_checkin'  => "L'utilisateur a pointé un départ sans avoir pointé d'arrivée au préalable ce jour.",
            'stage_inactive'            => "Le stage de l'étudiant n'est pas actif (hors période de stage). Le pointage ne devrait pas être autorisé.",
            'retard_arrivee'            => "L'utilisateur est arrivé après l'heure de référence (retard constaté).",
            'secondary_device_detected' => "Un pointage a été détecté depuis un appareil différent de celui habituellement utilisé par l'utilisateur.",
            'shared_device_detected'    => "L'utilisateur a pointé en utilisant l'appareil enregistré d'un autre utilisateur (téléphone partagé/prêté).",
            'new_device_detected'       => "L'utilisateur a pointé depuis un nouvel appareil non encore enregistré comme badge de confiance.",
        ];
        return $descriptions[$this->anomaly_type] ?? 'Aucune description disponible pour ce type d\'anomalie.';
    }

    public function getTypeSolutionAttribute(): string
    {
        $solutions = [
            'outside_geofence'          => "1. Vérifier si l'utilisateur était bien sur site (contacter l'utilisateur ou le responsable).\n2. Si l'utilisateur était bien présent sur site, il peut s'agir d'une dérive GPS → marquer comme résolu sans action.\n3. Si l'utilisateur n'était pas sur site, appliquer les sanctions prévues.",
            'no_gps'                    => "1. Demander à l'utilisateur d'activer le GPS sur son appareil.\n2. Vérifier que l'application a bien les permissions de localisation.\n3. Si le problème persiste, vérifier le paramétrage du téléphone.",
            'gps_accuracy_low'          => "1. Vérifier la qualité du réseau mobile/WiFi sur le site.\n2. Si l'utilisateur était physiquement présent, marquer comme résolu.\n3. Recommander l'utilisation du WiFi du site si disponible.",
            'missing_geofence'          => "1. Contacter l'administrateur pour configurer une zone de géofence pour ce site.\n2. En attendant, marquer comme résolu — le pointage reste valide.\n3. La configuration se fait dans Admin > Sites > Géofence.",
            'duplicate_checkin'         => "1. Vérifier si l'utilisateur a pointé par erreur.\n2. Conserver le premier pointage et ignorer le doublon.\n3. Marquer comme résolu.",
            'duplicate_checkout'        => "1. Vérifier si l'utilisateur a pointé par erreur.\n2. Conserver le premier pointage de départ et ignorer le doublon.\n3. Marquer comme résolu.",
            'checkout_without_checkin'  => "1. Vérifier si l'utilisateur a oublié de pointer son arrivée.\n2. Si l'utilisateur était bien présent, corriger manuellement l'heure d'arrivée.\n3. Marquer comme résolu après correction.",
            'stage_inactive'            => "1. Vérifier les dates du stage dans Admin > Stages.\n2. Prolonger le stage si nécessaire.\n3. Si le stage est bien expiré, contacter l'étudiant et son responsable.",
            'retard_arrivee'            => "1. Consulter l'observation laissée par l'utilisateur dans le payload.\n2. Apprécier le motif du retard (valable ou non).\n3. Si le motif est recevable, marquer comme résolu. Sinon, appliquer les règles de ponctualité.",
            'secondary_device_detected' => "1. Vérifier auprès de l'utilisateur s'il a changé d'appareil.\n2. Si c'est un nouvel appareil autorisé, mettre à jour l'appareil principal.\n3. Marquer comme résolu.",
            'shared_device_detected'    => "1. Vérifier si le prêt d'appareil était légitime (panne de batterie, oubli).\n2. Rappeler à l'utilisateur que chaque membre doit utiliser son propre appareil.\n3. Marquer comme résolu si le motif est vérifié.",
            'new_device_detected'       => "1. Vérifier avec l'utilisateur s'il s'agit bien de son nouveau téléphone.\n2. Si oui, l'enrôler comme badge autorisé dans son profil.\n3. Marquer comme résolu.",
        ];
        return $solutions[$this->anomaly_type] ?? 'Aucune procédure définie. Vérifier manuellement et marquer comme résolu si approprié.';
    }
}
