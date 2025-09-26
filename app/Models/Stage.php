<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
     use SoftDeletes; // ✅ active le soft delete
    protected $fillable = [
        'etudiant_id',
        'typestage_id',
        'service_id',
        'badge_id',
        'theme',
        'date_debut',
        'date_fin',
    ];

   protected $casts = [
    'date_debut' => 'datetime',
    'date_fin'   => 'datetime',
];


    // Relations
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

   public function typestage()
{
    return $this->belongsTo(TypeStage::class, 'typestage_id');
}


    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Relation MANY-TO-MANY avec jours
    public function jours()
    {
        return $this->belongsToMany(Jour::class, 'stage_jour');
    }

    // Getter pour le statut
   public function getStatutAttribute()
{
    $now = now();

    if (!$this->date_debut || !$this->date_fin) {
        return 'À venir';
    }

    if ($now->lt($this->date_debut)) {
        return 'À venir';
    } elseif ($now->between($this->date_debut, $this->date_fin)) {
        return 'En cours';
    } else { // $now > date_fin
        return 'Terminé';
    }
}
// Stage.php
public function signataires()
{
    return $this->belongsToMany(Signataire::class, 'attestation_signataire', 'attestation_id', 'signataire_id')
                ->withPivot('par_ordre', 'ordre')
                ->orderBy('pivot_ordre');
}
public function attestation()
{
    return $this->hasOne(Attestation::class);
}

}
