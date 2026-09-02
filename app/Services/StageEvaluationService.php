<?php

namespace App\Services;

use App\Models\EvaluationCriterion;
use App\Models\Stage;
use App\Models\StageEvaluation;
use App\Models\StageEvaluationScore;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Notation d'un stage : composition de la grille, calcul du critère automatique,
 * moyenne pondérée, et gel du tout à la finalisation.
 */
class StageEvaluationService
{
    public function __construct(
        protected EvaluationGridService $gridService,
        protected StageReportService $reportService
    ) {}

    /**
     * Récupère l'évaluation du stage, ou en ouvre une en brouillon.
     * Les lignes de note sont synchronisées avec la grille courante tant que
     * l'évaluation n'est pas figée : un critère ajouté au référentiel apparaît,
     * un critère retiré disparaît — sans toucher aux notes déjà saisies.
     */
    public function draftFor(Stage $stage, User $actor): StageEvaluation
    {
        // Les valeurs par défaut de la table ne remontent pas dans l'instance
        // créée : on les pose explicitement, sinon anomaly_disclosure reste null
        // en mémoire et la première sauvegarde viole la contrainte NOT NULL.
        $evaluation = StageEvaluation::firstOrCreate(
            ['stage_id' => $stage->id],
            [
                'evaluated_by'       => $actor->id,
                'status'             => StageEvaluation::STATUS_DRAFT,
                'anomaly_disclosure' => 'count',
            ]
        );

        if ($evaluation->isFinalized()) {
            return $evaluation->load('scores');
        }

        $this->syncScoresWithGrid($stage, $evaluation);

        return $evaluation->load('scores');
    }

    /**
     * Enregistre la grille saisie directement dans le tableau d'évaluation.
     *
     * Les critères restent rattachés au type de stage : composés une fois pour
     * Manon, ils seront déjà là pour le stagiaire suivant du même type. Le
     * tableau n'est donc pas un formulaire jetable, c'est le référentiel qu'on
     * remplit au fil de l'eau.
     *
     * @param array $rows lignes [{id?, label, weight}] dans l'ordre d'affichage
     */
    public function syncGrid(Stage $stage, array $rows, User $actor): StageEvaluation
    {
        $evaluation = $this->draftFor($stage, $actor);

        if ($evaluation->isFinalized()) {
            throw ValidationException::withMessages([
                'grid' => "L'évaluation est finalisée : rouvrez-la avant de modifier la grille.",
            ]);
        }

        DB::transaction(function () use ($stage, $rows, $actor, $evaluation) {
            $kept = [];
            $order = 0;

            foreach ($rows as $row) {
                $label = trim((string) ($row['label'] ?? ''));
                if ($label === '') {
                    continue; // ligne laissée vide : on l'ignore plutôt que de la rejeter
                }

                $weight = max(1, min(20, (int) ($row['weight'] ?? 1)));
                $order++;

                $criterion = !empty($row['id'])
                    ? EvaluationCriterion::find($row['id'])
                    : null;

                if ($criterion) {
                    $criterion->update([
                        'label'      => $label,
                        'weight'     => $weight,
                        'sort_order' => $order,
                        'is_active'  => true,
                    ]);
                } else {
                    $criterion = EvaluationCriterion::create([
                        // Grille unique : le critère vaut pour tous les stages.
                        'typestage_id' => null,
                        'label'        => $label,
                        'weight'       => $weight,
                        'sort_order'   => $order,
                        'created_by'   => $actor->id,
                    ]);
                }

                $kept[] = $criterion->id;
            }

            // Lignes retirées du tableau : on les désactive au lieu de les
            // supprimer, pour ne pas casser les évaluations déjà rendues qui
            // les référencent.
            EvaluationCriterion::where('is_active', true)
                ->whereNotIn('id', $kept ?: [0])
                ->update(['is_active' => false]);

            $evaluation->update(['grid_validated_at' => now()]);
        });

        $this->syncScoresWithGrid($stage, $evaluation->fresh());

        return $evaluation->fresh('scores');
    }

    /**
     * Rouvre la composition de la grille sans rien perdre des notes déjà saisies.
     */
    public function unlockGrid(StageEvaluation $evaluation): void
    {
        if ($evaluation->isFinalized()) {
            return;
        }

        $evaluation->update(['grid_validated_at' => null]);
    }

    /**
     * Aligne les lignes de note sur la grille du type de stage.
     */
    protected function syncScoresWithGrid(Stage $stage, StageEvaluation $evaluation): void
    {
        $criteria = $this->gridService->forStage($stage);
        $existing = $evaluation->scores()->get()->keyBy('criterion_id');

        DB::transaction(function () use ($criteria, $existing, $evaluation, $stage) {
            $keptIds = [];

            foreach ($criteria as $index => $criterion) {
                $keptIds[] = $criterion->id;

                $computed = $criterion->is_auto
                    ? $this->computeAutoScore($stage, $criterion->auto_source)
                    : null;

                $row = $existing->get($criterion->id);

                if ($row) {
                    // Le libellé et le coefficient suivent le référentiel tant
                    // que rien n'est figé ; la note saisie, elle, est préservée.
                    $row->update([
                        'label_snapshot'  => $criterion->label,
                        'weight_snapshot' => $criterion->weight,
                        'is_auto'         => $criterion->is_auto,
                        'computed_score'  => $computed,
                        'sort_order'      => $index,
                    ]);
                    continue;
                }

                StageEvaluationScore::create([
                    'stage_evaluation_id' => $evaluation->id,
                    'criterion_id'        => $criterion->id,
                    'label_snapshot'      => $criterion->label,
                    'weight_snapshot'     => $criterion->weight,
                    'is_auto'             => $criterion->is_auto,
                    'computed_score'      => $computed,
                    // Un critère automatique arrive pré-rempli, prêt à être
                    // accepté d'un clic ou remplacé avec justification.
                    'score'               => $computed,
                    'sort_order'          => $index,
                ]);
            }

            // Critères retirés du référentiel : la ligne disparaît du brouillon.
            // criterion_id à NULL signale un critère supprimé définitivement —
            // un NOT IN ne l'attraperait jamais, la ligne resterait orpheline.
            if (empty($keptIds)) {
                $evaluation->scores()->delete();
            } else {
                $evaluation->scores()
                    ->where(fn($q) => $q->whereNull('criterion_id')->orWhereNotIn('criterion_id', $keptIds))
                    ->delete();
            }
        });
    }

    /**
     * Note calculée d'un critère automatique, sur 20.
     *
     * Retourne null quand les données manquent : mettre 0 à un stagiaire dont
     * on n'a aucune donnée de présence serait une sanction, pas une mesure.
     */
    public function computeAutoScore(Stage $stage, ?string $source): ?float
    {
        if ($source !== 'attendance') {
            return null;
        }

        $report = $this->reportService->build($stage);
        $r      = $report['ratios'];

        $parts = [
            'presence'     => $r['assiduite'],
            'punctuality'  => $r['ponctualite'],
            'completeness' => $r['journees_completes'],
        ];

        // Aucun jour attendu ni pointé : il n'y a rien à noter.
        if (($parts['presence']['denominator'] ?? 0) === 0) {
            return null;
        }

        $weights = config('evaluation.attendance_weights');
        $max     = (float) config('evaluation.max_score', 20);

        $total = 0.0;
        $used  = 0.0;

        foreach ($parts as $key => $ratio) {
            $den = $ratio['denominator'] ?? 0;
            if ($den === 0) {
                continue; // ce volet ne peut pas être mesuré, on ne l'invente pas
            }

            $rate   = $ratio['numerator'] / $den;
            $weight = (float) ($weights[$key] ?? 0);

            $total += $rate * $weight;
            $used  += $weight;
        }

        if ($used <= 0) {
            return null;
        }

        // Renormalisation sur les volets réellement mesurables.
        return round($max * ($total / $used), 2);
    }

    /**
     * Enregistre les notes saisies. Refuse toute écriture sur une évaluation figée.
     *
     * @param array<int, array{score: mixed, comment: ?string}> $input indexé par id de ligne
     */
    public function saveScores(StageEvaluation $evaluation, array $input, ?string $generalComment, ?string $disclosure): StageEvaluation
    {
        $this->assertEditable($evaluation);

        DB::transaction(function () use ($evaluation, $input, $generalComment, $disclosure) {
            foreach ($evaluation->scores()->get() as $row) {
                if (!array_key_exists($row->id, $input)) {
                    continue;
                }

                $raw     = $input[$row->id];
                $score   = ($raw['score'] === null || $raw['score'] === '') ? null : (float) $raw['score'];
                $comment = $raw['comment'] ?? null;

                $row->update(['score' => $score, 'comment' => $comment]);
            }

            $evaluation->update([
                'general_comment'    => $generalComment,
                'anomaly_disclosure' => $disclosure ?: ($evaluation->anomaly_disclosure ?: 'count'),
            ]);
        });

        return $evaluation->fresh('scores');
    }

    /**
     * Vérifie qu'une évaluation peut être figée, et renvoie les manquements.
     *
     * @return array<string> liste des raisons de blocage, vide si tout est prêt
     */
    public function blockingIssues(StageEvaluation $evaluation): array
    {
        $issues = [];
        $scores = $evaluation->scores()->get();

        if ($scores->isEmpty()) {
            $issues[] = "La grille est vide : aucun critère n'est défini pour ce type de stage.";
            return $issues;
        }

        foreach ($scores as $row) {
            if ($row->score === null) {
                $issues[] = "« {$row->label_snapshot} » n'est pas encore noté.";
            }
        }

        // Remplacer une note calculée sans dire pourquoi rendrait l'écart
        // inexplicable au stagiaire comme à son école.
        foreach ($scores as $row) {
            if ($row->isOverridden() && trim((string) $row->comment) === '') {
                $issues[] = "« {$row->label_snapshot} » remplace la note calculée : une justification est obligatoire.";
            }
        }

        // Les coefficients gelés portent le nom weight_snapshot, pas weight :
        // passer par totalWeight() du référentiel sommerait une colonne absente.
        if ((int) $scores->sum('weight_snapshot') <= 0) {
            $issues[] = "La somme des coefficients est nulle : la moyenne ne peut pas être calculée.";
        }

        return $issues;
    }

    /**
     * Fige l'évaluation : moyenne pondérée calculée, ratios d'assiduité
     * capturés, plus aucune modification possible sans réouverture explicite.
     */
    public function finalize(StageEvaluation $evaluation, User $actor): StageEvaluation
    {
        $this->assertEditable($evaluation);

        $issues = $this->blockingIssues($evaluation);
        if ($issues) {
            throw ValidationException::withMessages(['evaluation' => $issues]);
        }

        $report = $this->reportService->build($evaluation->stage);

        $evaluation->update([
            'status'              => StageEvaluation::STATUS_FINALIZED,
            'evaluated_by'        => $actor->id,
            'final_score'         => $this->weightedAverage($evaluation),
            'attendance_snapshot' => [
                'counts'     => $report['counts'],
                'ratios'     => $report['ratios'],
                'anomalies'  => [
                    'total'    => $report['anomalies']['total'],
                    'open'     => $report['anomalies']['open'],
                    'resolved' => $report['anomalies']['resolved'],
                    'by_type'  => $report['anomalies']['by_type']->toArray(),
                ],
                'frozen_at'  => now()->toDateTimeString(),
            ],
            'finalized_at' => now(),
        ]);

        return $evaluation->fresh('scores');
    }

    /**
     * Rouvre une évaluation figée. Trace qui l'a fait : revenir sur une note
     * remise n'est pas anodin.
     */
    public function reopen(StageEvaluation $evaluation, User $actor): StageEvaluation
    {
        if (!$evaluation->isFinalized()) {
            return $evaluation;
        }

        $evaluation->update([
            'status'      => StageEvaluation::STATUS_DRAFT,
            'reopened_by' => $actor->id,
            'reopened_at' => now(),
        ]);

        return $evaluation->fresh('scores');
    }

    /** Moyenne pondérée sur 20 des notes retenues. */
    public function weightedAverage(StageEvaluation $evaluation): ?float
    {
        $scores = $evaluation->scores()->get()->filter(fn($r) => $r->score !== null);

        if ($scores->isEmpty()) {
            return null;
        }

        $weightSum = (float) $scores->sum('weight_snapshot');
        if ($weightSum <= 0) {
            return null;
        }

        $total = $scores->sum(fn($r) => (float) $r->score * (float) $r->weight_snapshot);

        return round($total / $weightSum, 2);
    }

    protected function assertEditable(StageEvaluation $evaluation): void
    {
        if ($evaluation->isFinalized()) {
            throw ValidationException::withMessages([
                'evaluation' => "Cette évaluation est finalisée. Rouvrez-la pour la modifier.",
            ]);
        }
    }
}
