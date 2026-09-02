<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un critère de la grille d'évaluation de stage.
 *
 * Rattaché à un type de stage, il n'appartient qu'à sa grille ; sans type, il
 * est commun à toutes. La grille effective d'un type est donc la réunion des
 * deux, résolue par EvaluationGridService.
 */
class EvaluationCriterion extends Model
{
    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'typestage_id',
        'label',
        'description',
        'weight',
        'is_auto',
        'auto_source',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_auto'   => 'boolean',
        'is_active' => 'boolean',
        'weight'    => 'integer',
        'sort_order' => 'integer',
    ];

    /** Sources de calcul disponibles pour un critère automatique. */
    public const AUTO_SOURCES = [
        'attendance' => "Assiduité calculée (présence, ponctualité, journées complètes)",
    ];

    public function typestage()
    {
        return $this->belongsTo(TypeStage::class, 'typestage_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Critères communs à toutes les grilles. */
    public function scopeShared($query)
    {
        return $query->whereNull('typestage_id');
    }

    /** La grille d'un type : ses critères propres, plus les critères communs. */
    public function scopeForTypeStage($query, ?int $typestageId)
    {
        return $query->where(function ($q) use ($typestageId) {
            $q->whereNull('typestage_id');
            if ($typestageId) {
                $q->orWhere('typestage_id', $typestageId);
            }
        });
    }

    public function isShared(): bool
    {
        return $this->typestage_id === null;
    }

    public function autoSourceLabel(): ?string
    {
        return $this->auto_source ? (self::AUTO_SOURCES[$this->auto_source] ?? $this->auto_source) : null;
    }
}
