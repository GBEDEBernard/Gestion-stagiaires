<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attestation extends Model
{
    use HasFactory;
<<<<<<< HEAD
    use SoftDeletes; // ✅ active le soft delete
=======
    use SoftDeletes;

>>>>>>> e9635ab
    protected $fillable = [
        'stage_id',
        'typestage_id',
        'reference',
        'date_delivrance',
        'fichier_pdf',
    ];

<<<<<<< HEAD
    // Relations
=======
    protected $casts = [
        'date_delivrance' => 'date',
    ];

>>>>>>> e9635ab
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function typestage()
    {
        return $this->belongsTo(TypeStage::class);
    }

    public function signataires()
<<<<<<< HEAD
{
    return $this->belongsToMany(Signataire::class, 'attestation_signataire')
                ->withPivot('par_ordre','ordre')
                ->withTimestamps();
}

=======
    {
        return $this->belongsToMany(Signataire::class, 'attestation_signataire')
            ->withPivot('par_ordre', 'ordre')
            ->withTimestamps();
    }

    public function approvals()
    {
        return $this->hasMany(AttestationApproval::class);
    }

    public function versions()
    {
        return $this->hasMany(AttestationVersion::class);
    }

    public function audits()
    {
        return $this->hasMany(AttestationAudit::class);
    }
>>>>>>> e9635ab
}
