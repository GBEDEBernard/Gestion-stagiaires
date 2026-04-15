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

    public function attendanceEvent()
    {
        return $this->belongsTo(AttendanceEvent::class);
    }

    public function attendanceDay()
    {
        return $this->belongsTo(AttendanceDay::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

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
}
