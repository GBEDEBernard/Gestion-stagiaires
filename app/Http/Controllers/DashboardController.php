<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Service;
use App\Models\TypeStage;
use App\Models\Badge;
use App\Models\Activity;
use App\Models\Etudiant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->startOfDay();

        // ==================== KPIs Principaux ====================
        $totalStages = Stage::count();
        $totalEtudiants = Etudiant::count();
        $enCoursGlobal = Stage::whereDate('date_debut', '<=', $today)
                              ->whereDate('date_fin', '>=', $today)
                              ->count();
        $terminesGlobal = Stage::whereDate('date_fin', '<', $today)->count();
        $inscritsGlobal = Stage::whereDate('date_debut', '>', $today)->count();
        $totalBadges = Badge::count();
        $totalTypes = TypeStage::count();
        $totalServices = Service::count();

        // ==================== ÉVOLUTION PAR JOUR (30 derniers jours) ====================
        $evolutionJour = [];
        $labelsJour = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labelsJour[] = $date->locale('fr')->isoFormat('DD MMM');
            $evolutionJour[] = Etudiant::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        // ==================== ÉVOLUTION PAR SEMAINE (12 dernières semaines) ====================
        $evolutionSemaine = [];
        $labelsSemaine = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $labelsSemaine[] = $startOfWeek->locale('fr')->isoFormat('DD MMM');
            $evolutionSemaine[] = Etudiant::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        }

        // ==================== ÉVOLUTION PAR MOIS (12 derniers mois) ====================
        $evolutionMois = [];
        $labelsMois = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labelsMois[] = $date->locale('fr')->isoFormat('MMM YYYY');
            $evolutionMois[] = Etudiant::whereMonth('created_at', $date->month)
                                       ->whereYear('created_at', $date->year)
                                       ->count();
        }

        // ==================== Distribution par Type de Stage ====================
        $typesStages = TypeStage::withCount('stages')->get();
        $typesLabels = $typesStages->pluck('libelle')->toArray();
        $typesData = $typesStages->pluck('stages_count')->toArray();

        // ==================== Stats par Service ====================
        $servicesStats = Service::all()->map(function ($service) use ($today) {
            $allStages = Stage::where('service_id', $service->id)
                ->select('id', 'date_debut', 'date_fin')
                ->get();

            $enCoursService = 0;
            $terminesService = 0;
            $inscritsService = 0;

            foreach ($allStages as $stage) {
                $debut = $stage->date_debut->startOfDay();
                $fin = $stage->date_fin->endOfDay();

                if ($today->between($debut, $fin)) {
                    $enCoursService++;
                } elseif ($today->gt($fin)) {
                    $terminesService++;
                } elseif ($today->lt($debut)) {
                    $inscritsService++;
                }
            }

            return [
                'service' => $service->nom,
                'enCours' => $enCoursService,
                'termines' => $terminesService,
                'inscrits' => $inscritsService,
                'total' => $enCoursService + $terminesService + $inscritsService
            ];
        });

        // ==================== Taux et Pourcentages ====================
        $tauxPresence = $totalEtudiants > 0 
            ? min(100, round(($enCoursGlobal / $totalEtudiants) * 100)) 
            : 0;
        $tauxReussite = $totalStages > 0 
            ? round(($terminesGlobal / $totalStages) * 100) 
            : 0;
        $tauxAbandon = 8;
        $etudiantsAvecStages = Etudiant::has('stages')->count();
        $tauxConversion = $totalEtudiants > 0 
            ? round(($etudiantsAvecStages / $totalEtudiants) * 100) 
            : 0;

        // ==================== Comparaisons Période ====================
        $inscriptionsCeMois = Etudiant::whereMonth('created_at', Carbon::now()->month)
                                      ->whereYear('created_at', Carbon::now()->year)
                                      ->count();
        $inscriptionsMoisDernier = Etudiant::whereMonth('created_at', Carbon::now()->subMonth()->month)
                                           ->whereYear('created_at', Carbon::now()->subMonth()->year)
                                           ->count();
        $evolutionInscriptionsMois = $inscriptionsMoisDernier > 0 
            ? round((($inscriptionsCeMois - $inscriptionsMoisDernier) / $inscriptionsMoisDernier) * 100) 
            : ($inscriptionsCeMois > 0 ? 100 : 0);

        $dateUnMoisAvant = Carbon::now()->subMonth();
        $stagesMoisDernier = Stage::whereDate('date_debut', '<=', $dateUnMoisAvant)
                                  ->whereDate('date_fin', '>=', $dateUnMoisAvant)
                                  ->count();
        $evolutionStages = $stagesMoisDernier > 0 
            ? round((($enCoursGlobal - $stagesMoisDernier) / $stagesMoisDernier) * 100) 
            : ($enCoursGlobal > 0 ? 100 : 0);

        $tauxCompletion = $totalStages > 0 
            ? round((($terminesGlobal - ($totalStages * 0.08)) / $totalStages) * 100) 
            : 0;

        // ==================== Listes ====================
        $activities = Activity::latest()->take(5)->get();
        $derniersEtudiants = Etudiant::with(['stages' => function($query) {
            $query->latest()->limit(1);
        }])->latest()->take(5)->get();

        // ==================== Corbeille ====================
        $stagesTrash = Stage::onlyTrashed()->get();
        $etudiantsTrash = Etudiant::onlyTrashed()->get();
        $badgesTrash = Badge::onlyTrashed()->get();
        $servicesTrash = Service::onlyTrashed()->get();
        $totalTrash = $stagesTrash->count() + $etudiantsTrash->count() + 
                      $badgesTrash->count() + $servicesTrash->count();

        // ==================== Retour à la Vue ====================
        return view('dashboard', compact(
            'totalStages', 'totalEtudiants', 'enCoursGlobal', 'terminesGlobal', 'inscritsGlobal',
            'totalBadges', 'totalServices', 'totalTypes',
            
            // Évolutions multi-périodes
            'evolutionJour', 'labelsJour',
            'evolutionSemaine', 'labelsSemaine',
            'evolutionMois', 'labelsMois',
            
            'typesLabels', 'typesData', 'servicesStats',
            'tauxPresence', 'tauxReussite', 'tauxAbandon', 'tauxConversion',
            'evolutionInscriptionsMois', 'evolutionStages', 'tauxCompletion',
            'activities', 'derniersEtudiants',
            'stagesTrash', 'etudiantsTrash', 'badgesTrash', 'servicesTrash', 'totalTrash'
        ));
    }
}