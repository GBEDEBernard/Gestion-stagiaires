<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeStage extends Model
{
    use HasFactory;
     use SoftDeletes; // ✅ active le soft delete
     
    protected $table = 'typestages';
    protected $fillable = ['libelle' ,'code']; 

    // Relations si tu veux
    public function stagiaires() {
        return $this->hasMany(Stage::class);
    }

    public function stages()
{
    return $this->hasMany(Stage::class, 'typestage_id');
}

}
