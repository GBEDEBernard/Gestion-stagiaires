<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    // jb -> Le compte utilisateur porte a la fois l'identite, les roles
    // et l'etat d'onboarding (verification email + mot de passe temporaire).
    use MustVerifyEmailTrait;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'temporary_password_created_at',
        'password_changed_at',
        'phone',
        'bio',
        'avatar',
        'status',
        'domaine_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'temporary_password_created_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    public function requiresPasswordChange(): bool
    {
        // jb -> Point d'entree unique pour savoir si le compte est encore
        // dans son etat provisoire apres creation admin.
        return (bool) $this->must_change_password;
    }

    public function homeRouteName(): string
    {
        // jb -> Cette methode centralise la destination post-authentification
        // selon le role principal pour eviter les redirections dupliquees
        // dans plusieurs controlleurs.
        if ($this->hasRole('etudiant')) {
            return 'student.stage';
        }
        if ($this->hasRole('superviseur')) {
            return 'superviseur.dashboard';
        }
        return 'dashboard';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        // jb -> On force ici la notification de verification du projet
        // pour garder un texte et un ton coherents lors des renvois.
        $this->notify(new VerifyEmailNotification());
    }

    public function supervisedStages()
    {
        return $this->hasMany(Stage::class, 'supervisor_id');
    }

    public function etudiant()
    {
        return $this->hasOne(Etudiant::class);
    }

    public function trustedDevices()
    {
        return $this->hasMany(TrustedDevice::class);
    }

    public function domaine()
    {
        return $this->belongsTo(Domaine::class);
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function validatedAttendanceDays()
    {
        return $this->hasMany(AttendanceDay::class, 'validated_by');
    }

    public function reviewedAttendanceAnomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class, 'reviewed_by');
    }

    public function reviewedDailyReports()
    {
        return $this->hasMany(DailyReport::class, 'reviewed_by');
    }

    public function dailyReportReviews()
    {
        return $this->hasMany(DailyReportReview::class, 'reviewer_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function taskUpdates()
    {
        return $this->hasMany(TaskUpdate::class, 'updated_by');
    }

    public function attestationApprovals()
    {
        return $this->hasMany(AttestationApproval::class, 'approver_id');
    }

    public function generatedAttestationVersions()
    {
        return $this->hasMany(AttestationVersion::class, 'generated_by');
    }

    public function attestationAudits()
    {
        return $this->hasMany(AttestationAudit::class);
    }
}
