<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Etudiant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceDay extends Model
{
    use HasFactory;

    protected $attributes = [
        'etudiant_id' => null,
        'stage_id' => null,
    ];

    protected $fillable = [
        'stage_id',
        'etudiant_id',
        'user_id',
        'site_id',
        'check_in_event_id',
        'check_out_event_id',
        'attendance_date',
        'first_check_in_at',
        'last_check_out_at',
        'worked_minutes',
        'late_minutes',
        'early_departure_minutes',
        'anomaly_count',
        'day_status',
        'validation_status',
        'validated_by',
        'validated_at',
        'summary_notes',
        'arrival_status',
        'departure_status',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'first_check_in_at' => 'datetime',
        'last_check_out_at' => 'datetime',
        'validated_at' => 'datetime',
        'arrival_status' => 'string',
        'departure_status' => 'string',
    ];

    // ========== RELATIONS ==========
    // Relations pour filtrer les présences par stage/étudiant (pour stagiaires) ou par utilisateur (pour employés)
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
    // Relation avec étudiant pour filtrer les présences par étudiant (pour stagiaires)
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
    // Relation avec utilisateur pour filtrer les présences par utilisateur (pour employés)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relation avec site pour filtrer les présences par site
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
    // Relations avec les événements de pointage pour accéder facilement aux détails des check-in/check-out
    public function checkInEvent()
    {
        return $this->belongsTo(AttendanceEvent::class, 'check_in_event_id');
    }
    // Relation pour accéder à l'événement de check-out, utile pour les rapports détaillés et les anomalies liées au départ
    public function checkOutEvent()
    {
        return $this->belongsTo(AttendanceEvent::class, 'check_out_event_id');
    }
    // Relation avec l'utilisateur qui a validé la présence (pour les validations manuelles)
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
    // Relation avec les anomalies de présence pour accéder facilement aux anomalies détectées sur ce jour de présence
    public function anomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }
    // Relation avec les rapports quotidiens pour accéder aux rapports générés à partir de ce jour de présence
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
    // Relation pour accéder à l'anomalie de retard d'arrivée
    public function lateAnomaly()
    {
        return $this->hasOne(AttendanceAnomaly::class)
            ->where('anomaly_type', 'retard_arrivee')
            ->orderByDesc('detected_at');
    }

    public function getLateObservationAttribute(): ?string
    {
        return $this->lateAnomaly?->payload['message_observation'] ?? null;
    }

    // ========== SCOPES ==========

    /**
     * Restreint aux jours ouvrés (lundi au vendredi)
     */
    public function scopeWeekdays($query)
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return $query->whereRaw("CAST(strftime('%w', attendance_date) AS INTEGER) BETWEEN 1 AND 5");
        }

        return $query->whereRaw('WEEKDAY(attendance_date) BETWEEN 0 AND 4');
    }

    /**
     * Scope pour la période (avec dates personnalisées)
     */
    public function scopeGlobalStats($query, $period = 'today', ?string $dateFrom = null, ?string $dateTo = null)
    {
        if ($dateFrom && $dateTo) {
            return $query->whereBetween('attendance_date', [Carbon::parse($dateFrom), Carbon::parse($dateTo)]);
        }

        switch ($period) {
            case 'week':
                return $query->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()]);
            case 'month':
                return $query->whereMonth('attendance_date', now()->month)->whereYear('attendance_date', now()->year);
            case 'year':
                return $query->whereYear('attendance_date', now()->year);
            default:
                return $query->whereDate('attendance_date', today());
        }
    }
    // Scopes pour filtrer les présences par type d'utilisateur et par statut d'anomalie
    public function scopeEtudiants($query)
    {
        return $query->whereNotNull('etudiant_id');
    }
    // Scope pour filtrer les présences des employés (user_id non null et etudiant_id null)
    public function scopeEmployes($query)
    {
        return $query->whereNotNull('user_id')->whereNull('etudiant_id');
    }
    //    Scope pour filtrer les présences validées
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('etudiant_id', $userId)->orWhere('user_id', $userId);
        });
    }
    // Scope pour filtrer les présences avec anomalies (anomaly_count > 0)
    public function scopeWithAnomalies($query)
    {
        return $query->where('anomaly_count', '>', 0);
    }
    // Scope pour filtrer les présences sans anomalies    public function scopeWithoutAnomalies($query) { return $query->where('anomaly_count', 0); }
    public function scopeAbsences($query)
    {
        return $query->whereNull('first_check_in_at');
    }
    //    Scope pour filtrer les présences en retard (late_minutes > 0)
    public function isLate()
    {
        return $this->late_minutes > 0;
    }

    /**
     * Scope TOP LATE – uniquement jours ouvrés
     */
    public function scopeTopLate($query, int $limit = 10, string $period = 'today', ?string $dateFrom = null, ?string $dateTo = null)
    {
        $query->globalStats($period, $dateFrom, $dateTo)
            ->weekdays()
            ->where('late_minutes', '>', 0)
            ->leftJoin('stages', 'attendance_days.stage_id', 'stages.id')
            ->leftJoin('etudiants', 'stages.etudiant_id', 'etudiants.id')
            ->leftJoin('personnels as etudiant_personnels', function ($join) {
                $join->on('etudiant_personnels.personnable_id', '=', 'etudiants.id')
                    ->where('etudiant_personnels.personnable_type', '=', Etudiant::class);
            })
            ->leftJoin('users as etudiant_users', 'etudiant_users.personnel_id', '=', 'etudiant_personnels.id')
            ->leftJoin('users as direct_users', 'attendance_days.user_id', 'direct_users.id')
            ->leftJoin('personnels as direct_personnels', 'direct_personnels.id', 'direct_users.personnel_id')
            ->selectRaw('COALESCE(etudiant_users.id, direct_users.id) as user_id, '
                . 'COALESCE(CONCAT(etudiant_personnels.prenom, " ", etudiant_personnels.nom), CONCAT(direct_personnels.prenom, " ", direct_personnels.nom)) as user_name, '
                . 'SUM(late_minutes) as total_late, '
                . 'COUNT(*) as days_count, '
                . 'AVG(late_minutes) as avg_late')
            ->groupByRaw('COALESCE(etudiant_users.id, direct_users.id), COALESCE(CONCAT(etudiant_personnels.prenom, " ", etudiant_personnels.nom), CONCAT(direct_personnels.prenom, " ", direct_personnels.nom))')
            ->orderByDesc('total_late')
            ->limit($limit);
    }
}
