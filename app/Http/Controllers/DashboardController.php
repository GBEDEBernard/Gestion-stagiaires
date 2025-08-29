<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Service;
use App\Models\TypeStage;
use App\Models\Badge;
use App\Models\Activity;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->startOfDay(); // On compare uniquement la date

        // Total de tous les stages
        $totalStages = Stage::count();

        // Stages en cours global
        $enCoursGlobal = Stage::whereDate('date_debut', '<=', $today)
                              ->whereDate('date_fin', '>=', $today)
                              ->count();

        // Stages terminés global
        $terminesGlobal = Stage::whereDate('date_fin', '<', $today)->count();

        // Total badges et types de stages
        $totalBadges = Badge::count();
        $totalTypes  = TypeStage::count();

        // Dernières activités
        $activities = Activity::latest()->take(5)->get();

        // Stats par service
        $servicesStats = Service::all()->map(function ($service) use ($today) {

            $stages = Stage::where('service_id', $service->id)->get();

            $enCoursService  = $stages->filter(fn($stage) => $today->between($stage->date_debut->startOfDay(), $stage->date_fin->endOfDay()))->count();
            $terminesService = $stages->filter(fn($stage) => $today->gt($stage->date_fin->endOfDay()))->count();
            $inscritsService = $stages->filter(fn($stage) => $today->lt($stage->date_debut->startOfDay()))->count();

            return [
                'service'  => $service->nom,
                'enCours'  => $enCoursService,
                'termines' => $terminesService,
                'inscrits' => $inscritsService,
            ];
        });

        return view('dashboard', compact(
            'totalStages',
            'enCoursGlobal',
            'terminesGlobal',
            'totalBadges',
            'totalTypes',
            'activities',
            'servicesStats'
        ));
    }
}
