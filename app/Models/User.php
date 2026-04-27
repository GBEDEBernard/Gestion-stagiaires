<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
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
        // Point d'entree unique pour l'onboarding des comptes crees par l'admin.
        return (bool) $this->must_change_password;
    }

    public function homeRouteName(): string
    {
        if ($this->hasRole('admin')) {
            return 'dashboard';
        }

        if ($this->hasRole('superviseur')) {
            return 'superviseur.dashboard';
        }

        if ($this->hasRole('etudiant') || $this->hasRole('employe')) {
            return 'presence.pointage';
        }

        return 'dashboard';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        // On force la notification projet pour garder des messages coherents.
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

    public function permissionRequests()
    {
        return $this->hasMany(PermissionRequest::class);
    }

    public function permissionRequestsToReview()
    {
        return $this->hasMany(PermissionRequest::class, 'first_approver_id');
    }

    public function reviewedPermissionRequests()
    {
        return $this->hasMany(PermissionRequest::class, 'reviewed_by_id');
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
}
