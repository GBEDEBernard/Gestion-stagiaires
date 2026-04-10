<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'etudiant_id',
        'typestage_id',
        'service_id',
        'site_id',
        'supervisor_id',
        'badge_id',
        'theme',
        'date_debut',
        'date_fin',
        'expected_check_in_time',
        'expected_check_out_time',
        'allowed_late_minutes',
        'allowed_early_departure_minutes',
        'presence_mode',
        'follow_up_status',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function typestage()
    {
        return $this->belongsTo(TypeStage::class, 'typestage_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

    public function jours()
    {
        return $this->belongsToMany(Jour::class, 'stage_jour');
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function attendanceDays()
    {
        return $this->hasMany(AttendanceDay::class);
    }

    public function attendanceAnomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function attestation()
    {
        return $this->hasOne(Attestation::class);
    }

    public function getStatutAttribute()
    {
        $now = now();

        if (!$this->date_debut || !$this->date_fin) {
            return 'A venir';
        }

        if ($now->lt($this->date_debut)) {
            return 'A venir';
        }

        if ($now->between($this->date_debut, $this->date_fin)) {
            return 'En cours';
        }

        return 'Termine';
    }
}
