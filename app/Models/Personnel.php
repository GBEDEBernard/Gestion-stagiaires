<?php
// app/Models/Personnel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Personnel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'genre',
        'date_naissance',
        'adresse',
        'personnable_type',
        'personnable_id',
        'created_by',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /** Compte utilisateur lié à ce personnel. */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /** Relation polymorphique (Etudiant ou Employe). */
    public function personnable()
    {
        return $this->morphTo();
    }

    /**
     * Raccourci direct vers la fiche Etudiant.
     * Utilisé notamment dans UserController@store lors de la création d'un étudiant
     * pour récupérer $personnel->etudiant->id sans passer par personnable.
     */
    public function etudiant()
    {
        return $this->hasOne(Etudiant::class);
    }

    /**
     * Raccourci direct vers la fiche Employe.
     */
    public function employe()
    {
        return $this->hasOne(Employe::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isEtudiant(): bool
    {
        return $this->personnable_type === Etudiant::class;
    }

    public function isEmploye(): bool
    {
        return $this->personnable_type === Employe::class;
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    public function getFullNameAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->personnable_type) {
            Etudiant::class => 'Étudiant',
            Employe::class  => 'Employé',
            default         => 'Inconnu',
        };
    }
}
