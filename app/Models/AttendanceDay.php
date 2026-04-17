<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDay extends Model
{
    use HasFactory;

    /**
     * Defaults pour employés: pas de stage/etudiant nécessaire
     * Garantit NULL sur firstOrNew(['user_id', 'attendance_date'])
     */
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

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function checkInEvent()
    {
        return $this->belongsTo(AttendanceEvent::class, 'check_in_event_id');
    }

    public function checkOutEvent()
    {
        return $this->belongsTo(AttendanceEvent::class, 'check_out_event_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function anomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    /**
     * Scopes pour statistiques optimisées.
     */

    /**
     * Scope pour stats globales (avec période).
     */
    public function scopeGlobalStats($query, $period = 'today')
    {
        switch ($period) {
            case 'week':
                return $query->whereBetween('attendance_date', [now()->startOfWeek(), now()->endOfWeek()]);
            case 'month':
                return $query->whereMonth('attendance_date', now()->month)
                    ->whereYear('attendance_date', now()->year);
            case 'year':
                return $query->whereYear('attendance_date', now()->year);
            default:
                return $query->whereDate('attendance_date', today());
        }
    }

    /**
     * Scope par groupe (étudiants/employés).
     */
    public function scopeEtudiants($query)
    {
        return $query->whereNotNull('etudiant_id');
    }

    public function scopeEmployes($query)
    {
        return $query->whereNotNull('user_id')->whereNull('etudiant_id');
    }

    /**
     * Scope utilisateur spécifique (détecte étudiant/employé).
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('etudiant_id', $userId)
                ->orWhere('user_id', $userId);
        });
    }

    /**
     * Scope top retards.
     */
    public function scopeTopLate($query, $limit = 10, $period = null)
    {
        if ($period && $period !== 'today') {
            $query->globalStats($period);
        }

        return $query->selectRaw('
                COALESCE(etudiant_users.id, direct_users.id) as user_id,
                COALESCE(etudiant_users.name, direct_users.name) as user_name,
                SUM(late_minutes) as total_late,
                COUNT(*) as days_count,
                AVG(late_minutes) as avg_late
            ')
            ->leftJoin('stages', 'attendance_days.stage_id', 'stages.id')
            ->leftJoin('etudiants', 'stages.etudiant_id', 'etudiants.id')
            ->leftJoin('users as etudiant_users', 'etudiants.user_id', 'etudiant_users.id')
            ->leftJoin('users as direct_users', 'attendance_days.user_id', 'direct_users.id')
            ->groupBy('user_id', 'user_name')
            ->orderByDesc('total_late')
            ->limit($limit);
    }

    /**
     * Scope absences (sans check-in).
     */
    public function scopeAbsences($query)
    {
        return $query->whereNull('first_check_in_at');
    }
}
