<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use SoftDeletes; // ✅ active le soft delete

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
=======
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

>>>>>>> e9635ab
    protected $fillable = [
        'name',
        'email',
        'password',
<<<<<<< HEAD
        'phone',
        'bio',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
=======
        'must_change_password',
        'temporary_password_created_at',
        'password_changed_at',
        'phone',
        'bio',
        'avatar',
        'status',
    ];

>>>>>>> e9635ab
    protected $hidden = [
        'password',
        'remember_token',
    ];

<<<<<<< HEAD
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
=======
>>>>>>> e9635ab
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
<<<<<<< HEAD
        ];
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
=======
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
        return $this->hasRole('etudiant') ? 'student.stage' : 'dashboard';
    }

>>>>>>> e9635ab
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
<<<<<<< HEAD
=======

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
>>>>>>> e9635ab
}
