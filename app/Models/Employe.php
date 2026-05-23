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
        'personnel_id',   // <-- indispensable maintenant
        'domaine_id',
        'site_id',
        'poste',
        'matricule',
    ];

    public function personnel()
    {
        return $this->morphOne(Personnel::class, 'personnable');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Personnel::class,
            'personnable_id',
            'personnel_id',
            'id',
            'id'
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
}