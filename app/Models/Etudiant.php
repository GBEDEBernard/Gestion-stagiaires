<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Etudiant extends Model
{
use HasFactory;


protected $fillable = [
'nom','prenom','email','telephone','ecole'
];


public function stages()
{
return $this->hasMany(Stage::class, 'etudiant_id');
}

}