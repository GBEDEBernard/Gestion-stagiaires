<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Stage;
use App\Services\StageEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Notation d'un stage. Réservé à l'administrateur : décision prise, le tuteur
 * n'a pas la main sur la grille.
 */
class StageEvaluationController extends Controller
{
    public function __construct(
        protected StageEvaluationService $service
    ) {}

    public function edit(Stage $stage)
    {
        $evaluation = $this->service->draftFor($stage, request()->user());

        // Lignes du tableau lors de la composition : la grille du type de stage,
        // que l'administrateur remplit et corrige directement à l'écran.
        $gridRows = app(\App\Services\EvaluationGridService::class)
            ->forStage($stage)
            ->map(fn($c) => ['id' => $c->id, 'label' => $c->label, 'weight' => $c->weight])
            ->values();

        return view('admin.stages.evaluation', [
            'stage'      => $stage->load('etudiant.personnel', 'typestage'),
            'evaluation' => $evaluation,
            'gridRows'   => $gridRows,
            'issues'     => $evaluation->isDraft() ? $this->service->blockingIssues($evaluation) : [],
            'preview'    => $this->service->weightedAverage($evaluation),
        ]);
    }

    public function update(Request $request, Stage $stage)
    {
        $evaluation = $this->service->draftFor($stage, $request->user());

        $validated = $request->validate([
            'scores'              => ['array'],
            'scores.*.score'      => ['nullable', 'numeric', 'min:0', 'max:' . config('evaluation.max_score', 20)],
            'scores.*.comment'    => ['nullable', 'string', 'max:2000'],
            'general_comment'     => ['nullable', 'string', 'max:5000'],
            'anomaly_disclosure'  => ['nullable', 'in:count,grouped,detailed'],
        ], [
            'scores.*.score.max' => 'Les notes sont sur ' . config('evaluation.max_score', 20) . '.',
        ]);

        $this->service->saveScores(
            $evaluation,
            $validated['scores'] ?? [],
            $validated['general_comment'] ?? null,
            $validated['anomaly_disclosure'] ?? null
        );

        return back()->with('success', 'Notes enregistrées.');
    }

    /**
     * Enregistre la grille composée dans le tableau, puis ouvre la saisie des notes.
     */
    public function saveGrid(Request $request, Stage $stage)
    {
        $validated = $request->validate([
            'rows'            => 'required|array|min:1',
            'rows.*.id'       => 'nullable|integer',
            'rows.*.label'    => 'nullable|string|max:120',
            'rows.*.weight'   => 'nullable|integer|min:1|max:20',
        ]);

        $rows = collect($validated['rows'])
            ->filter(fn($r) => trim((string) ($r['label'] ?? '')) !== '')
            ->values()
            ->all();

        if (empty($rows)) {
            return back()->withErrors(['rows' => "Ajoutez au moins un critère avant de valider la grille."]);
        }

        try {
            $this->service->syncGrid($stage, $rows, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Grille enregistrée. Vous pouvez saisir les notes.');
    }

    /**
     * Revient à la composition de la grille sans perdre les notes déjà saisies.
     */
    public function editGrid(Request $request, Stage $stage)
    {
        $evaluation = $stage->evaluation;

        if ($evaluation) {
            $this->service->unlockGrid($evaluation);
        }

        return back()->with('success', 'Vous pouvez de nouveau modifier les critères.');
    }

    public function finalize(Request $request, Stage $stage)
    {
        $evaluation = $this->service->draftFor($stage, $request->user());

        try {
            $this->service->finalize($evaluation, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        Activity::create([
            'user_id'     => $request->user()->id,
            'action'      => 'Finalisation evaluation de stage',
            'description' => "Stage #{$stage->id} noté {$evaluation->fresh()->final_score}/20.",
        ]);

        return back()->with('success', "Évaluation finalisée. Le rapport PDF est prêt à être imprimé et remis au stagiaire.");
    }

    public function reopen(Request $request, Stage $stage)
    {
        $evaluation = $stage->evaluation;

        if (!$evaluation) {
            return back();
        }

        $this->service->reopen($evaluation, $request->user());

        Activity::create([
            'user_id'     => $request->user()->id,
            'action'      => 'Reouverture evaluation de stage',
            'description' => "Stage #{$stage->id} : évaluation rouverte pour correction.",
        ]);

        return back()->with('success', "Évaluation rouverte. Le rapport repasse en document provisoire.");
    }
}
