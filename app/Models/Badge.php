<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stage;

class Badge extends Model
{
    // Nom de la table
    protected $table = 'badges';

    // Champs assignables
    protected $fillable = ['badge'];

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
