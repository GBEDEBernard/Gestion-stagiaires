<?php

namespace App\Rules;

use App\Models\Stagiaire;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class BadgeNotInActiveStage implements Rule
{
    protected $stagiaireId;

    /**
     * Crée une nouvelle instance de la règle.
     *
     * @param int|null $stagiaireId L'ID du stagiaire à exclure (pour les mises à jour)
     */
    public function __construct($stagiaireId = null)
    {
        $this->stagiaireId = $stagiaireId;
    }

    /**
     * Détermine si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $today = Carbon::today();

        // Vérifie si le badge est associé à un stagiaire avec un stage en cours
        $query = Stagiaire::where('badge_id', $value)
            ->where('date_debut', '<=', $today)
            ->where('date_fin', '>=', $today);

        // Exclut le stagiaire actuel pour les mises à jour
        if ($this->stagiaireId) {
            $query->where('id', '!=', $this->stagiaireId);
        }

        return !$query->exists();
    }

    /**
     * Message d'erreur en cas d'échec.
     *
     * @return string
     */
    public function message()
    {
        return 'Ce badge est déjà attribué à un stagiaire en cours de stage.';
    }
}