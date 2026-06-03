<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Signataire extends Model
{
    use HasFactory;
 use SoftDeletes; // ✅ active le soft delete
    protected $fillable = [
        'nom',
        'email',
        'poste',
        'sigle',
        'ordre',
        'peut_par_ordre',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNomAttribute($value)
    {
        return $this->user?->personnel?->full_name ?? $value;
    }

    public function getEmailAttribute($value)
    {
        return $this->user?->personnel?->email ?? $value;
    }

    public function getPosteAttribute($value)
    {
        return $this->user?->personnel?->personnable?->poste ?? $value;
    }

    public function getSigleAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $poste = $this->user?->personnel?->personnable?->poste ?? ($this->attributes['poste'] ?? '');
        if (stripos($poste, 'directeur général') !== false) {
            return 'DG';
        }
        if (stripos($poste, 'directeur technique adjoint') !== false) {
            return 'DTA';
        }
        if (stripos($poste, 'directeur technique') !== false) {
            return 'DT';
        }

        return 'SIG';
    }

    public function permissionRecipients()
    {
        return $this->hasMany(\App\Models\PermissionRequestRecipient::class);
    }

    public function attestations()
    {
        return $this->belongsToMany(Attestation::class, 'attestation_signataire')
                    ->withPivot('par_ordre', 'ordre')
                    ->withTimestamps();
    }

    // DG
    public function isDG(): bool
    {
        return strtolower($this->poste) === 'directeur général';
    }

    // DT
    public function isDT(): bool
    {
        return stripos($this->poste, 'directeur technique') !== false && !$this->isDG();
    }

    // DTA
    public function isDTA(): bool
    {
        return stripos($this->poste, 'directeur technique adjoint') !== false && !$this->isDG() && !$this->isDT();
    }
}
