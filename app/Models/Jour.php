<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jour extends Model
{
    use HasFactory;

    protected $fillable = ['jour'];

    // Relation : un jour peut avoir plusieurs stages
    public function stages()
    {
        return $this->hasMany(Stage::class, 'jour_id'); 
        // Assure-toi que la table 'stages' contient la colonne 'jour_id'
    }
}
