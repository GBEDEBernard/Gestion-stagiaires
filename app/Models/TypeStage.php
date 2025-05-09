<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeStage extends Model
{
    use HasFactory;
    
    protected $table = 'typestages'; // <-- DIT LUI LA VÉRITÉ
    protected $fillable = ['libelle']; // ou autres colonnes si besoin

    // Relations si tu veux
    public function stagiaires() {
        return $this->hasMany(Stagiaire::class);
    }
}
