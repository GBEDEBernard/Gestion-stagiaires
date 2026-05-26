<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyEmailTrait, HasFactory, Notifiable, HasRoles, SoftDeletes, CanResetPassword;

    protected $fillable = [
        'personnel_id',
        'email',
        'password',
        'must_change_password',
        'temporary_password_created_at',
        'password_changed_at',
        'status',
        'email_verified_at',
        'domaine_id',   // ← Ajouter cette ligne
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'             => 'datetime',
        'password'                      => 'hashed',
        'must_change_password'          => 'boolean',
        'temporary_password_created_at' => 'datetime',
        'password_changed_at'           => 'datetime',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================
    // Note : la relation vers Domaine est redondante avec celle d'Employe, mais elle facilite les requêtes directes sur User sans devoir faire un join vers Employe.
    public function domaine()
    {
        return $this->belongsTo(Domaine::class);
    }
    // Note : la relation vers Personnel est indispensable pour accéder aux données personnelles (nom, email, etc.) et pour la logique métier qui lie un compte utilisateur à une fiche personnel.
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    /** Accès direct à l'étudiant (via personnel). */
    public function etudiant()
    {
        return $this->hasOneThrough(
            Etudiant::class,
            Personnel::class,
            'id',           // FK sur personnels
            'personnel_id', // FK sur etudiants
            'personnel_id', // local key sur users
            'id'            // local key sur personnels
        )->where('personnable_type', Etudiant::class);
    }

    public function profil()
    {
        return $this->personnel?->personnable;
    }

    public function supervisedStages()
    {
        return $this->hasMany(Stage::class, 'supervisor_id');
    }

    public function trustedDevices()
    {
        return $this->hasMany(TrustedDevice::class);
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function attendanceAnomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class);
    }

    public function reviewedAttendanceAnomalies()
    {
        return $this->hasMany(AttendanceAnomaly::class, 'reviewed_by');
    }

    public function reviewedDailyReports()
    {
        return $this->hasMany(DailyReport::class, 'reviewed_by');
    }

    public function attendanceDays()
    {
        return $this->hasMany(AttendanceDay::class);
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

    public function permissionRequests()
    {
        return $this->hasMany(\App\Models\PermissionRequest::class);
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Le nom affiché provient du personnel s'il est lié, sinon on garde la valeur
     * stockée en base.
     */
    public function getNameAttribute($value)
    {
        return $this->personnel?->full_name ?? $value;
    }

    /**
     * L'email "affiché" provient du personnel.
     * ATTENTION : cette surcharge ne doit PAS impacter getEmailForVerification()
     * ni getEmailForPasswordReset() car Laravel utilise ces méthodes pour les
     * tokens — on les redéfinit explicitement ci-dessous.
     */
    public function getEmailAttribute($value)
    {
        return $this->personnel?->email ?? $value;
    }

    public function getPhoneAttribute()
    {
        return $this->personnel?->telephone;
    }

    // =========================================================================
    // SURCHARGES AUTH — CRITIQUE
    // =========================================================================

    /**
     * Email utilisé par Laravel pour les tokens de vérification d'email.
     * On pointe vers l'email du personnel (source de vérité).
     */
    public function getEmailForVerification(): string
    {
        return $this->personnel?->email ?? $this->attributes['email'] ?? '';
    }

    /**
     * Email utilisé par Laravel pour les tokens de réinitialisation de mot de passe.
     * Doit correspondre à l'email stocké dans password_reset_tokens.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->personnel?->email ?? $this->attributes['email'] ?? '';
    }

    // =========================================================================
    // NOTIFICATIONS
    // =========================================================================

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    // =========================================================================
    // HELPERS MÉTIER
    // =========================================================================

    /**
     * Détermine la route d'accueil après connexion en fonction du rôle.
     */
    public function homeRouteName(): string
    {
        if ($this->hasRole('admin')) {
            return 'dashboard';
        }

        if (
            $this->hasRole('etudiant') ||
            $this->hasRole('employe') ||
            $this->hasRole('fonctionnaire') ||
            $this->hasRole('superviseur')
        ) {
            return 'presence.pointage';
        }

        return 'dashboard';
    }

    /**
     * Indique si l'utilisateur doit changer son mot de passe temporaire.
     */
    public function requiresPasswordChange(): bool
    {
        return (bool) $this->must_change_password;
    }
}