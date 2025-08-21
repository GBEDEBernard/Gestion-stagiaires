<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\TypeStage;
use App\Models\Badge;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStagiaires = Stagiaire::count();
        $enCours = Stagiaire::where('date_debut', '<=', now())
                            ->where('date_fin', '>=', now())
                            ->count();
        $totalBadges = Badge::count();
        $totalTypes = TypeStage::count();

        return view('dashboard', compact(
            'totalStagiaires',
            'enCours',
            'totalBadges',
            'totalTypes'
        ));
    }
}
