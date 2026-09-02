<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageEvaluationScore extends Model
{
    protected $fillable = [
        'stage_evaluation_id',
        'criterion_id',
        'label_snapshot',
        'weight_snapshot',
        'is_auto',
        'computed_score',
        'score',
        'comment',
        'sort_order',
    ];

    protected $casts = [
        'is_auto'        => 'boolean',
        'computed_score' => 'decimal:2',
        'score'          => 'decimal:2',
        'weight_snapshot' => 'integer',
        'sort_order'     => 'integer',
    ];

    public function evaluation()
    {
        return $this->belongsTo(StageEvaluation::class, 'stage_evaluation_id');
    }

    public function criterion()
    {
        return $this->belongsTo(EvaluationCriterion::class, 'criterion_id');
    }

    /**
     * La note retenue s'écarte-t-elle de celle calculée par le système ?
     * L'écart doit être visible sur le document remis, avec sa justification.
     */
    public function isOverridden(): bool
    {
        return $this->is_auto
            && $this->computed_score !== null
            && $this->score !== null
            && abs((float) $this->score - (float) $this->computed_score) > 0.01;
    }
}
