<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stage;
use Illuminate\Database\Eloquent\SoftDeletes;


class Badge extends Model
{
     use SoftDeletes; // ✅ active le soft delete
    // Nom de la table
    protected $table = 'badges';

    // Champs assignables
    protected $fillable = ['badge'];

    /**
     * Générer le prochain numéro de badge automatiquement
     */
    public static function getNextBadgeNumber(): string
    {
        $lastBadge = self::orderBy('id', 'desc')->first();

        if (!$lastBadge) {
            return 'TFG0001';
        }

        preg_match('/(\d+)$/', $lastBadge->badge, $matches);

        if ($matches) {
            $lastNumber = (int) $matches[1];
            $prefix = substr($lastBadge->badge, 0, -strlen($matches[1]));
            return $prefix . str_pad($lastNumber + 1, strlen($matches[1]), '0', STR_PAD_LEFT);
        }

        return 'TFG0001';
    }

    /**
     * Récupérer les badges disponibles pour attribution à un stage
     *
     * @param int|null $excludeStageId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAvailableBadges($excludeStageId = null)
    {
        $today = Carbon::today();

        // Badges déjà utilisés dans des stages en cours
        $usedBadgeIds = Stage::where('date_debut', '<=', $today)
            ->where('date_fin', '>=', $today)
            ->when($excludeStageId, function ($query) use ($excludeStageId) {
                $query->where('id', '!=', $excludeStageId);
            })
            ->pluck('badge_id')
            ->filter() // pour éviter les null
            ->toArray();

        // Retourne les badges libres
        return self::whereNotIn('id', $usedBadgeIds)->get();
    }

    /**
     * Relation : un badge peut être utilisé par plusieurs stages
     */
    public function stages()
    {
        return $this->hasMany(Stage::class);
    }
}
