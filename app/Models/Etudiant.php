<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Etudiant extends Model
{
use HasFactory;
 use SoftDeletes; // ✅ active le soft delete

protected $fillable = [
'nom','prenom','email','telephone','ecole','genre'
];


public function stages()
{
return $this->hasMany(Stage::class, 'etudiant_id');
}

}