<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\Employe; 

class Personnel extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'genre',
        'date_naissance', 'adresse', 'personnable_type', 'personnable_id', 'created_by'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function personnable()
    {
        return $this->morphTo();
    }

    public function isEtudiant()
    {
        return $this->personnable_type === Etudiant::class;
    }

    public function isEmploye()
    {
        return $this->personnable_type === Employe::class;
    }

    public function getFullNameAttribute()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
