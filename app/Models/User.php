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

class User extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyEmailTrait, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'personnel_id', 'password', 'must_change_password',
        'temporary_password_created_at', 'password_changed_at', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'temporary_password_created_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'avatar',
             'bio' ,
        ];
    }

    public function personnel() {
        return $this->belongsTo(Personnel::class);
    }

    // Accesseurs pour compatibilité ascendante
    public function getNameAttribute() {
        return $this->personnel?->full_name;
    }

    public function getEmailAttribute() {
        return $this->personnel?->email;
    }

    public function getPhoneAttribute() {
        return $this->personnel?->telephone;
    }

    // Ancienne relation (pour code legacy)
    public function etudiant() {
        return $this->hasOneThrough(Etudiant::class, Personnel::class, 'id', 'personnel_id', 'personnel_id')
            ->where('personnable_type', Etudiant::class);
    }

    public function profil() {
        return $this->personnel->personnable;
    }

    /**
     * Détermine la route d'accueil après connexion en fonction du rôle.
     */
    public function homeRouteName(): string {
        // 👑 Admin
        if ($this->hasRole('admin')) {
            return 'dashboard';
        }

        // 👨‍🎓 Étudiants + employés + superviseurs → pointage
        if (
            $this->hasRole('etudiant') ||
            $this->hasRole('employe') ||
            $this->hasRole('fonctionnaire') ||
            $this->hasRole('superviseur')
        ) {
            return 'presence.pointage';
        }

        // fallback sécurisé
        return 'dashboard';
    }

    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmailNotification());
    }

    // --- Relations existantes ---
    public function supervisedStages() {
        return $this->hasMany(Stage::class, 'supervisor_id');
    }

    public function trustedDevices() {
        return $this->hasMany(TrustedDevice::class);
    }

    public function attendanceEvents() {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function attendanceAnomalies() {
        return $this->hasMany(AttendanceAnomaly::class);
    }

    public function reviewedAttendanceAnomalies() {
        return $this->hasMany(AttendanceAnomaly::class, 'reviewed_by');
    }

    public function reviewedDailyReports() {
        return $this->hasMany(DailyReport::class, 'reviewed_by');
    }

    public function attendanceDays() {
        return $this->hasMany(AttendanceDay::class);
    }

    public function dailyReportReviews() {
        return $this->hasMany(DailyReportReview::class, 'reviewer_id');
    }

    public function assignedTasks() {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function taskUpdates() {
        return $this->hasMany(TaskUpdate::class, 'updated_by');
    }

    public function attestationApprovals() {
        return $this->hasMany(AttestationApproval::class, 'approver_id');
    }

    public function generatedAttestationVersions() {
        return $this->hasMany(AttestationVersion::class, 'generated_by');
    }

    public function attestationAudits() {
        return $this->hasMany(AttestationAudit::class);
    }

    public function permissionRequests() {
        return $this->hasMany(\App\Models\PermissionRequest::class);
    }
}