<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Stagiaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'typestage_id',
        'badge_id',
        'ecole',
        'theme',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
    ];

    public function jours()
    {
        return $this->belongsToMany(Jour::class);
    }

    public function typestage()
    {
        return $this->belongsTo(TypeStage::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    // Getter pour le statut
    public function getStatutAttribute()
    {
        $now = Carbon::now();
        return $now->between($this->date_debut, $this->date_fin) ? 'En cours' : 'Terminé';
    }
}
