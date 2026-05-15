<?php
// app/Models/Etudiant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etudiant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['ecole', 'niveau']; // plus de champs personnels

    // Relation polymorphique avec Personnel
    public function personnel() 
    { return $this->morphOne(Personnel::class, 'personnable'); }
    // Accès direct à l'utilisateur via le personnel
    public function user()
     { return $this->personnel->user(); }
    // Relations avec les stages et autres entités liées à l'étudiant
    public function stages()
     { return $this->hasMany(Stage::class, 'etudiant_id'); }
    // Relations avec les présences et rapports
    public function attendanceEvents()
     { return $this->hasMany(AttendanceEvent::class); }
    // Relations avec les rapports quotidiens et tâches
    public function attendanceDays()
     { return $this->hasMany(AttendanceDay::class); }
    // Relations avec les anomalies de présence, rapports quotidiens et tâches
    public function attendanceAnomalies()
     { return $this->hasMany(AttendanceAnomaly::class); }
    // Relations avec les rapports quotidiens et tâches
    public function dailyReports() 
    { return $this->hasMany(DailyReport::class); }
    // Relations avec les tâches liées aux rapports quotidiens
    public function tasks() 
    { return $this->hasMany(Task::class); }
}