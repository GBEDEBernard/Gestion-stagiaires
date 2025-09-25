<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeStage extends Model
{
    use HasFactory;
    
    protected $table = 'typestages';
    protected $fillable = ['libelle' ,'code']; 

    // Relations si tu veux
    public function stagiaires() {
        return $this->hasMany(Stage::class);
    }
}
