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
        'poste',
        'sigle',
        'ordre',
        'peut_par_ordre',
    ];

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
