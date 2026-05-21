<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Personnel;

class Employe extends Model
{
    //
     use HasFactory, SoftDeletes;

    protected $fillable = ['domaine_id', 'site_id', 'poste', 'matricule'];

    // Relation polymorphique avec Personnel
    public function personnel()
    {
        return $this->morphOne(Personnel::class, 'personnable');
    }
// Accès direct à l'utilisateur via le personnel
    public function user()
    {
        return $this->personnel->user();
    }
// Relations avec les stages et autres entités liées à l'employé
    public function domaine()
    {
        return $this->belongsTo(Domaine::class);
    }
// Relation avec le site de travail
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
