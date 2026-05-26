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

    /**
     * Relation polymorphique principale.
     * Retourne l'Etudiant ou l'Employe selon personnable_type.
     */
    public function personnable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // ACCESSEURS POLYMORPHIQUES (raccourcis)
    // =========================================================================

    /**
     * Raccourci lecture vers la fiche Etudiant.
     * Usage : $personnel->etudiant
     * Utilisé dans UserController@store pour $personnel->etudiant->id
     */
    public function getEtudiantAttribute(): ?Etudiant
    {
        if ($this->personnable_type === Etudiant::class) {
            // On s'assure que la relation est chargée
            return $this->personnable instanceof Etudiant
                ? $this->personnable
                : $this->personnable()->first();
        }
        return null;
    }

    /**
     * Raccourci lecture vers la fiche Employe.
     * Usage : $personnel->employe
     * Utilisé dans AccountGenerationService pour $personnel->employe->domaine_id
     */
    public function getEmployeAttribute(): ?Employe
    {
        if ($this->personnable_type === Employe::class) {
            return $this->personnable instanceof Employe
                ? $this->personnable
                : $this->personnable()->first();
        }
        return null;
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
    // AUTRES ACCESSEURS
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