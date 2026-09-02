<?php

namespace App\Services;

use App\Models\EvaluationCriterion;
use App\Models\Stage;
use App\Models\TypeStage;
use Illuminate\Support\Collection;

/**
 * Résout la grille d'évaluation applicable à un type de stage.
 *
 * La grille n'est pas stockée : elle se compose à la lecture des critères
 * communs et des critères propres au type. C'est ce qui permet de corriger
 * « Assiduité » une seule fois pour tout le monde.
 */
class EvaluationGridService
{
    /**
     * Critères actifs applicables à un type de stage, ordonnés pour la saisie.
     * Les critères communs passent avant ceux du type, à rang égal.
     */
    public function forTypeStage(?TypeStage $type): Collection
    {
        // Grille unique : les mêmes critères servent à tous les stages, quel
        // que soit leur type. La colonne typestage_id demeure en base mais
        // n'est plus renseignée, aucune migration destructive n'est nécessaire.
        return EvaluationCriterion::active()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    public function forStage(Stage $stage): Collection
    {
        return $this->forTypeStage($stage->typestage);
    }

    /**
     * Somme des coefficients : dénominateur de la moyenne pondérée.
     * Zéro signale une grille vide ou entièrement à coefficient nul, cas où
     * aucune note ne peut être calculée.
     */
    public function totalWeight(Collection $criteria): int
    {
        return (int) $criteria->sum('weight');
    }

    /**
     * Aperçu de toutes les grilles, pour l'écran d'administration : chaque type
     * avec le nombre de critères qu'il hérite et ceux qui lui sont propres.
     */
    public function overview(): Collection
    {
        $shared = EvaluationCriterion::active()->shared()->get();

        return TypeStage::orderBy('libelle')->get()->map(function (TypeStage $type) use ($shared) {
            $own = EvaluationCriterion::active()->where('typestage_id', $type->id)->get();
            $all = $shared->concat($own);

            return [
                'typestage'   => $type,
                'shared'      => $shared->count(),
                'own'         => $own->count(),
                'total'       => $all->count(),
                'weight'      => $this->totalWeight($all),
                'has_auto'    => $all->contains('is_auto', true),
            ];
        });
    }
}
