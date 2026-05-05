<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etudiant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'ecole',
        'genre',
    ];

    public function stages()
    {
        return $this->hasMany(Stage::class, 'etudiant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
