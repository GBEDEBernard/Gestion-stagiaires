<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\EvaluationCriterion;
use App\Models\TypeStage;
use App\Services\EvaluationGridService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Référentiel des critères d'évaluation. Réservé à l'administrateur : c'est lui
 * qui sait ce qu'un stage doit mesurer dans son entreprise.
 */
class EvaluationCriterionController extends Controller
{
    public function __construct(
        protected EvaluationGridService $gridService
    ) {}

    public function index(Request $request)
    {
        $typestages = TypeStage::orderBy('libelle')->get();

        $selected = $request->query('typestage')
            ? $typestages->firstWhere('id', (int) $request->query('typestage'))
            : null;

        // Un aller-retour depuis une notation : on garde le chemin du retour.
        // back() conserve la chaîne de requête, le lien survit donc aux
        // enregistrements successifs de critères.
        $retourStage = $request->query('retour')
            ? \App\Models\Stage::with('etudiant.personnel')->find((int) $request->query('retour'))
            : null;

        return view('admin.evaluations.criteres.index', [
            'typestages'  => $typestages,
            'retourStage' => $retourStage,
            'selected'   => $selected,
            'overview'   => $this->gridService->overview(),
            'shared'     => EvaluationCriterion::shared()->orderBy('sort_order')->orderBy('label')->get(),
            'criteria'   => $selected
                ? EvaluationCriterion::where('typestage_id', $selected->id)
                    ->orderBy('sort_order')->orderBy('label')->get()
                : collect(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $criterion = EvaluationCriterion::create($data + ['created_by' => $request->user()->id]);

        $this->log($request, 'Creation critere evaluation', $criterion);

        return back()->with('success', "Le critère « {$criterion->label} » a été ajouté.");
    }

    public function update(Request $request, EvaluationCriterion $criterion)
    {
        $data = $this->validated($request, $criterion);

        $criterion->update($data);

        $this->log($request, 'Modification critere evaluation', $criterion);

        return back()->with('success', "Le critère « {$criterion->label} » a été mis à jour.");
    }

    /**
     * Bascule l'activité plutôt que de supprimer : un critère retiré doit
     * disparaître des nouvelles grilles sans effacer ce qu'il a déjà servi à noter.
     */
    public function toggle(Request $request, EvaluationCriterion $criterion)
    {
        $criterion->update(['is_active' => !$criterion->is_active]);

        $this->log($request, 'Activation critere evaluation', $criterion);

        return back()->with('success', $criterion->is_active
            ? "Le critère « {$criterion->label} » est de nouveau utilisé."
            : "Le critère « {$criterion->label} » ne sera plus proposé dans les nouvelles évaluations.");
    }

    public function destroy(Request $request, EvaluationCriterion $criterion)
    {
        $label = $criterion->label;
        $criterion->delete();

        $this->log($request, 'Suppression critere evaluation', $criterion);

        return back()->with('success', "Le critère « {$label} » a été supprimé.");
    }

    protected function validated(Request $request, ?EvaluationCriterion $current = null): array
    {
        $typestageId = $request->input('typestage_id') ?: null;

        $validated = $request->validate([
            'typestage_id' => ['nullable', 'exists:typestages,id'],
            'label'        => [
                'required', 'string', 'max:120',
                // Deux critères du même nom dans une même grille rendraient la
                // note illisible : on refuse le doublon, commun ou propre au type.
                Rule::unique('evaluation_criteria', 'label')
                    ->where(fn($q) => $typestageId
                        ? $q->where('typestage_id', $typestageId)
                        : $q->whereNull('typestage_id'))
                    ->ignore($current?->id),
            ],
            'description'  => ['nullable', 'string', 'max:1000'],
            'weight'       => ['required', 'integer', 'min:1', 'max:20'],
            'is_auto'      => ['nullable', 'boolean'],
            'auto_source'  => ['nullable', 'required_if:is_auto,1', Rule::in(array_keys(EvaluationCriterion::AUTO_SOURCES))],
            'sort_order'   => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'label.unique'          => 'Cette grille contient déjà un critère portant ce nom.',
            'auto_source.required_if' => 'Indiquez sur quoi le critère automatique doit se calculer.',
        ]);

        $isAuto = $request->boolean('is_auto');

        // Valeurs normalisées écrites après coup : une union de tableaux
        // conserverait la version brute du formulaire (chaîne vide, « 1 » texte).
        $validated['typestage_id'] = $typestageId;
        $validated['is_auto']      = $isAuto;
        $validated['auto_source']  = $isAuto ? ($validated['auto_source'] ?? null) : null;
        $validated['sort_order']   = (int) $request->input('sort_order', 0);

        return $validated;
    }

    protected function log(Request $request, string $action, EvaluationCriterion $criterion): void
    {
        Activity::create([
            'user_id'     => $request->user()->id,
            'action'      => $action,
            'description' => "Critère « {$criterion->label} » ("
                . ($criterion->typestage_id ? ($criterion->typestage?->libelle ?? 'type inconnu') : 'commun')
                . ").",
        ]);
    }
}
