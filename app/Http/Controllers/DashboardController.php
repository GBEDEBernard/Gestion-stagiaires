<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Service;
use App\Models\TypeStage;
use App\Models\Badge;
use App\Models\Activity;
use App\Models\Etudiant;
use App\Models\Attestation;
use App\Models\AppNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
=======
        if (Auth::user()?->hasRole('etudiant')) {
            // jb -> Le dashboard global reste reserve au pilotage admin.
            // Un stagiaire qui tape l'URL ou garde un ancien lien est
            // renvoye vers son espace dedie pour eviter toute confusion.
            return redirect()
                ->route('student.stage')
                ->with('info', "L'espace stagiaire remplace le dashboard global pour votre compte.");
        }

        abort_unless(Auth::user()?->can('dashboard.view'), 403);

>>>>>>> e9635ab
        $today = Carbon::now()->startOfDay();

        // ==================== Notifications ====================
        $notifications = AppNotification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();
        $notificationCount = AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

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

        // ==================== NOUVELLES STATISTIQUES ====================
        // Total attestations délivrées
        $totalAttestations = Attestation::count();

        // Durée moyenne des stages (en jours)
        $dureeMoyenne = Stage::whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->get()
            ->avg(function ($stage) {
                return $stage->date_debut->diffInDays($stage->date_fin);
            });
        $dureeMoyenne = $dureeMoyenne ? round($dureeMoyenne) : 0;

        // Taux d'étudiants actifs (avec un stage en cours)
        $etudiantsActifs = Etudiant::whereHas('stages', function ($query) use ($today) {
            $query->whereDate('date_debut', '<=', $today)
                ->whereDate('date_fin', '>=', $today);
        })->count();

        $tauxEtudiantsActifs = $totalEtudiants > 0
            ? round(($etudiantsActifs / $totalEtudiants) * 100)
            : 0;

        // Stages par mois (pour graphique annuel)
        $stagesParMois = [];
        $labelsMoisAnnee = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labelsMoisAnnee[] = $date->locale('fr')->isoFormat('MMM');
            $stagesParMois[] = Stage::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        // Top services (avec le plus de stages)
        $topServices = Service::withCount('stages')
            ->orderByDesc('stages_count')
            ->take(5)
            ->get();

        // Top types de stages
        $topTypes = TypeStage::withCount('stages')
            ->orderByDesc('stages_count')
            ->take(5)
            ->get();

        // Étudiants sans stage
        $etudiantsSansStage = Etudiant::doesntHave('stages')->count();

        // Dernière activité
        $dernieresActivites = Activity::latest()->take(8)->get();

        // Stages upcoming (à venir dans les 7 prochains jours)
        $stagesUpcoming = Stage::whereDate('date_debut', '>', $today)
            ->whereDate('date_debut', '<=', $today->copy()->addDays(7))
            ->with(['etudiant', 'service'])
            ->orderBy('date_debut')
            ->take(5)
            ->get();

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

        // Données pour le graphique services (pré-formatées)
        $servicesLabelsJson = json_encode($servicesStats->pluck('service')->toArray());
        $servicesEnCoursJson = json_encode($servicesStats->pluck('enCours')->toArray());
        $servicesTerminesJson = json_encode($servicesStats->pluck('termines')->toArray());
        $servicesInscritsJson = json_encode($servicesStats->pluck('inscrits')->toArray());

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
        $derniersEtudiants = Etudiant::with(['stages' => function ($query) {
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
            // Notifications
            'notifications',
            'notificationCount',

            // KPIs Principaux
            'totalStages',
            'totalEtudiants',
            'enCoursGlobal',
            'terminesGlobal',
            'inscritsGlobal',
            'totalBadges',
            'totalServices',
            'totalTypes',

            // Nouvelles statistiques
            'totalAttestations',
            'dureeMoyenne',
            'etudiantsActifs',
            'tauxEtudiantsActifs',
            'stagesParMois',
            'labelsMoisAnnee',
            'topServices',
            'topTypes',
            'etudiantsSansStage',
            'dernieresActivites',
            'stagesUpcoming',

            // Évolutions multi-périodes
            'evolutionJour',
            'labelsJour',
            'evolutionSemaine',
            'labelsSemaine',
            'evolutionMois',
            'labelsMois',

            'typesLabels',
            'typesData',
            'servicesStats',
            'servicesLabelsJson',
            'servicesEnCoursJson',
            'servicesTerminesJson',
            'servicesInscritsJson',
            'tauxPresence',
            'tauxReussite',
            'tauxAbandon',
            'tauxConversion',
            'evolutionInscriptionsMois',
            'evolutionStages',
            'tauxCompletion',
            'activities',
            'derniersEtudiants',
            'stagesTrash',
            'etudiantsTrash',
            'badgesTrash',
            'servicesTrash',
            'totalTrash'
        ));
    }
}
