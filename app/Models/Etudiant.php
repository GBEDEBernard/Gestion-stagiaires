<?php
// app/Models/Etudiant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Personnel;
use App\Models\User;

class Etudiant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['personnel_id', 'ecole', 'niveau', 'supervisor_id']; // plus de champs personnels

    // Relation polymorphique avec Personnel
    public function personnel()
    {
        return $this->morphOne(Personnel::class, 'personnable');
    }

    // Accès direct à l'utilisateur via le personnel
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Personnel::class,
            'personnable_id', // foreign key on personnels table
            'personnel_id',   // foreign key on users table
            'id',             // local key on etudiants table
            'id'              // local key on personnels table
        )->where('personnels.personnable_type', self::class);
    }

    // Relations avec les stages et autres entités liées à l'étudiant
    public function stages()
    {
        return $this->hasMany(Stage::class, 'etudiant_id');
    }
    // Relations avec les présences et rapports
    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }
    // Relations avec les rapports quotidiens et tâches
    public function attendanceDays()
    {
        return $this->hasMany(AttendanceDay::class);
    }
    // Relations avec les anomalies de présence, rapports quotidiens et tâches
    public function attendanceAnomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }
    // Relations avec les rapports quotidiens et tâches
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
    // Relations avec les tâches liées aux rapports quotidiens
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getNomAttribute(): ?string
    {
        return $this->personnel?->nom;
    }

    public function getPrenomAttribute(): ?string
    {
        return $this->personnel?->prenom;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->personnel?->email;
    }

    public function getTelephoneAttribute(): ?string
    {
        return $this->personnel?->telephone;
    }

    public function getGenreAttribute(): ?string
    {
        return $this->personnel?->genre;
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));
    }
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
