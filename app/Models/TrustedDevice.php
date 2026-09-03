<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'pointage_token_hash',
        'device_uuid',
        'device_label',
        'device_name',
        'platform',
        'browser',
        'app_version',
        'last_ip_address',
        'first_seen_at',
        'last_seen_at',
        'is_trusted',
        'is_primary',
        'is_qr_badge',
        'revoked_at',
        'token_expires_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'revoked_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'is_trusted' => 'boolean',
        'is_primary' => 'boolean',
        'is_qr_badge' => 'boolean',
    ];

    /**
     * Scope pour les appareils enrôlés comme badge actif.
     *
     * Le filtre d'expiration doit rester aligné sur isBadgeActive() : sans lui,
     * un stagiaire dont les badges ont expiré avec son stage précédent voit
     * encore « 2/2 appareils » dans son profil et se heurte au plafond
     * d'enrôlement alors qu'aucun de ses badges ne fonctionne plus.
     */
    public function scopeActiveQrBadges($query)
    {
        return $query->where('is_qr_badge', true)
            ->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('token_expires_at')
                    ->orWhere('token_expires_at', '>', now());
            });
    }

    /**
     * Vérifie si le badge de cet appareil est actuellement valide
     */
    public function isBadgeActive(): bool
    {
        if (!$this->is_qr_badge || $this->revoked_at !== null) {
            return false;
        }

        // Vérification de la date d'expiration si définie
        if ($this->token_expires_at && $this->token_expires_at->isPast()) {
            return false;
        }

        // Vérification de l'utilisateur. Le pointage par badge ne passe pas par
        // le middleware account_active (route publique) : ce test est le seul
        // filtre, il refuse donc tout statut qui n'est pas explicitement actif.
        $user = $this->user;
        if (!$user || $user->status !== 'actif') {
            return false;
        }

        // Si l'utilisateur est un stagiaire, vérifier son stage
        if ($user->etudiant) {
            $hasActiveStage = $user->etudiant->stages()
                ->where('date_debut', '<=', today())
                ->where('date_fin', '>=', today())
                ->exists();

            if (!$hasActiveStage) {
                return false;
            }
        }

        return true;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceEvents()
    {
        return $this->hasMany(AttendanceEvent::class);
    }
}
