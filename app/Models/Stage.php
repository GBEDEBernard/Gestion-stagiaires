<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
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

}
