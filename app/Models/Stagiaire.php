<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stagiaire extends Model
{
    //
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
    
    
}
