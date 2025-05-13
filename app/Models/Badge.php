<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badges';
    protected $fillable = ['badge'];

    public static function getAvailableBadges($excludeStagiaireId = null)
    {
        $today = Carbon::today();

        $usedBadgeIds = Stagiaire::where('date_debut', '<=', $today)
            ->where('date_fin', '>=', $today)
            ->when($excludeStagiaireId, function ($query) use ($excludeStagiaireId) {
                $query->where('id', '!=', $excludeStagiaireId);
            })
            ->pluck('badge_id');

        return self::whereNotIn('id', $usedBadgeIds)->get();
    }
}