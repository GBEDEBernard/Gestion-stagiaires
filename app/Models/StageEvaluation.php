<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageEvaluation extends Model
{
    protected $fillable = [
        'stage_id',
        'evaluated_by',
        'status',
        'grid_validated_at',
        'general_comment',
        'anomaly_disclosure',
        'attendance_snapshot',
        'final_score',
        'finalized_at',
        'reopened_by',
        'reopened_at',
    ];

    protected $casts = [
        'attendance_snapshot' => 'array',
        'final_score'         => 'decimal:2',
        'finalized_at'        => 'datetime',
        'reopened_at'         => 'datetime',
        'grid_validated_at'   => 'datetime',
    ];

    /** La grille est composée : la colonne des notes peut apparaître. */
    public function gridIsValidated(): bool
    {
        return $this->grid_validated_at !== null;
    }

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_FINALIZED = 'finalized';

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function scores()
    {
        return $this->hasMany(StageEvaluationScore::class)->orderBy('sort_order')->orderBy('id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function isFinalized(): bool
    {
        return $this->status === self::STATUS_FINALIZED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /** Mention scolaire correspondant à la note, pour l'affichage. */
    public function mention(): ?string
    {
        if ($this->final_score === null) {
            return null;
        }

        $s = (float) $this->final_score;

        return match (true) {
            $s >= 16 => 'Très bien',
            $s >= 14 => 'Bien',
            $s >= 12 => 'Assez bien',
            $s >= 10 => 'Passable',
            default  => 'Insuffisant',
        };
    }
}
