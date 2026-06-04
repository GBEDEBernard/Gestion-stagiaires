<?php
// app/Models/Employe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'personnel_id',
        'domaine_id',
        'site_id',
        'poste',
        'matricule',
        'supervisor_id',
    ];


    /**
     * Relation inverse du polymorphisme :
     * personnels.personnable_type = 'App\Models\Employe'
     * personnels.personnable_id  = employes.id
     */
    public function personnel()
    {
        return $this->morphOne(Personnel::class, 'personnable');
    }

    /**
     * Accès direct à l'utilisateur via le personnel (polymorphisme).
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Personnel::class,
            'personnable_id', // FK sur personnels (pointe vers employes.id)
            'personnel_id',   // FK sur users (pointe vers personnels.id)
            'id',             // local key sur employes
            'id'              // local key sur personnels
        )->where('personnels.personnable_type', self::class);
    }

    public function domaine()
    {
        return $this->belongsTo(Domaine::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function supervisedEmployees()
    {
        return $this->hasMany(Employe::class, 'supervisor_id');
    }
}
