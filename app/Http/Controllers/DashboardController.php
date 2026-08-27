<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Domaine;
use App\Models\TypeStage;
use App\Models\Badge;
use App\Models\Activity;
use App\Models\Etudiant;
use App\Models\Attestation;
use App\Models\AppNotification;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Admin/Superviseur toujours sur le dashboard global, même avec le rôle employe en plus
        if (Auth::user()?->hasAnyRole(['admin', 'superviseur'])) {
            return $this->globalDashboard();
        }

        if (Auth::user()?->hasRole('etudiant')) {
            return redirect()
                ->route('student.stage')
                ->with('info', "L'espace stagiaire remplace le dashboard global pour votre compte.");
        }

        if (Auth::user()?->hasRole('employe')) {
            $user = Auth::user();
            $today = Carbon::now()->startOfDay();
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            $todayAttendance = AttendanceDay::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->first();

            $daysPresentThisWeek = AttendanceDay::where('user_id', $user->id)
                ->whereBetween('attendance_date', [$weekStart, $weekEnd])
                ->whereNotNull('first_check_in_at')
                ->count();

            $daysTrackedThisWeek = AttendanceDay::where('user_id', $user->id)
                ->whereBetween('attendance_date', [$weekStart, $weekEnd])
                ->count();

            $attendanceEventsThisWeek = AttendanceEvent::where('user_id', $user->id)
                ->whereBetween('occurred_at', [$weekStart, $weekEnd])
                ->count();

            // ==================== Mes tâches (créées ou assignées) ====================
            $myTasks = Task::with([
                'dailyReports' => fn($q) => $q
                    ->with('user')
                    ->latest('report_date')
                    ->limit(5),
            ])
                ->visibleTo($user)
                ->latest('updated_at')
                ->limit(15)
                ->get();

            $myTasksCreated = $myTasks->where('owner_id', $user->id)->count();
            $myTasksAssigned = $myTasks->where('owner_id', '!=', $user->id)->count();
            $myTasksCompleted = $myTasks->where('status', 'completed')->count();

            return view('employe.dashboard', compact(
                'user',
                'todayAttendance',
                'daysPresentThisWeek',
                'daysTrackedThisWeek',
                'attendanceEventsThisWeek',
                'myTasks',
                'myTasksCreated',
                'myTasksAssigned',
                'myTasksCompleted'
            ));
        }

        abort_unless(Auth::user()?->can('dashboard.view'), 403);

        return $this->globalDashboard();
    }

    private function globalDashboard()
    {
        $today = Carbon::now()->startOfDay();

        // ==================== Notifications (via Service + ViewComposer universel) ====================
        $notificationService = app(\App\Services\NotificationService::class);
        $notificationService->generateNotifications(); // Génère si nécessaire

        $notifications = $notificationService->getUnreadNotifications();
        $notificationCount = $notificationService->getUnreadCount();

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
        $totalDomaines = Domaine::count();

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

        // Top domaines (avec le plus de stages)
        $topDomaines = Domaine::withCount('stages')
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
            ->with(['etudiant', 'domaine'])
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

        // ==================== BORNES DES PÉRIODES (pour la courbe cliquable) ====================
        $rangesJour = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $rangesJour[] = [$date->format('Y-m-d'), $date->format('Y-m-d')];
        }

        $rangesSemaine = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->subWeeks($i)->startOfWeek();
            $end = Carbon::now()->subWeeks($i)->endOfWeek();
            $rangesSemaine[] = [$start->format('Y-m-d'), $end->format('Y-m-d')];
        }

        $rangesMois = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $rangesMois[] = [$date->copy()->startOfMonth()->format('Y-m-d'), $date->copy()->endOfMonth()->format('Y-m-d')];
        }

        // ==================== Distribution par Type de Stage ====================
        $typesStages = TypeStage::withCount('stages')->get();
        $typesLabels = $typesStages->pluck('libelle')->toArray();
        $typesData = $typesStages->pluck('stages_count')->toArray();

        // ==================== Stats par Domaine ====================
        $domainesStats = Domaine::all()->map(function ($domaine) use ($today) {
            $allStages = Stage::where('domaine_id', $domaine->id)
                ->select('id', 'date_debut', 'date_fin')
                ->get();

            $enCoursDomaine = 0;
            $terminesDomaine = 0;
            $inscritsDomaine = 0;

            foreach ($allStages as $stage) {
                $debut = $stage->date_debut->startOfDay();
                $fin = $stage->date_fin->endOfDay();

                if ($today->between($debut, $fin)) {
                    $enCoursDomaine++;
                } elseif ($today->gt($fin)) {
                    $terminesDomaine++;
                } elseif ($today->lt($debut)) {
                    $inscritsDomaine++;
                }
            }

            return [
                'domaine' => $domaine->nom,
                'enCours' => $enCoursDomaine,
                'termines' => $terminesDomaine,
                'inscrits' => $inscritsDomaine,
                'total' => $enCoursDomaine + $terminesDomaine + $inscritsDomaine
            ];
        });

        // Données pour le graphique domaines (pré-formatées)
        $domainesLabelsJson = json_encode($domainesStats->pluck('domaine')->toArray());
        $domainesEnCoursJson = json_encode($domainesStats->pluck('enCours')->toArray());
        $domainesTerminesJson = json_encode($domainesStats->pluck('termines')->toArray());
        $domainesInscritsJson = json_encode($domainesStats->pluck('inscrits')->toArray());

        // ==================== Taux et Pourcentages ====================
        $tauxPresence = $totalEtudiants > 0
            ? min(100, round(($enCoursGlobal / $totalEtudiants) * 100))
            : 0;
        $tauxReussite = $totalStages > 0
            ? round(($terminesGlobal / $totalStages) * 100)
            : 0;

        // Taux d'abandon réel : stages terminés (date passée) sans attestation délivrée
        $terminesSansAttestation = Stage::whereDate('date_fin', '<', $today)
            ->whereDoesntHave('attestation')
            ->count();
        $tauxAbandon = $terminesGlobal > 0
            ? round(($terminesSansAttestation / $terminesGlobal) * 100)
            : 0;

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
            ? round(($terminesGlobal / $totalStages) * 100)
            : 0;

        // ==================== Évolutions 30 jours (badges KPI) ====================
        $debutPeriode = Carbon::now()->subDays(30);
        $debutPeriodePrecedente = Carbon::now()->subDays(60);

        $stages30 = Stage::where('created_at', '>=', $debutPeriode)->count();
        $stages30Precedents = Stage::whereBetween('created_at', [$debutPeriodePrecedente, $debutPeriode])->count();
        $evolutionStages30j = $stages30Precedents > 0
            ? round((($stages30 - $stages30Precedents) / $stages30Precedents) * 100)
            : ($stages30 > 0 ? 100 : 0);

        $etudiants30 = Etudiant::where('created_at', '>=', $debutPeriode)->count();
        $etudiants30Precedents = Etudiant::whereBetween('created_at', [$debutPeriodePrecedente, $debutPeriode])->count();
        $evolutionEtudiants30j = $etudiants30Precedents > 0
            ? round((($etudiants30 - $etudiants30Precedents) / $etudiants30Precedents) * 100)
            : ($etudiants30 > 0 ? 100 : 0);

        // ==================== Listes ====================
        $activities = Activity::latest()->take(5)->get();
        $derniersEtudiants = Etudiant::with(['stages' => function ($query) {
            $query->latest()->limit(1);
        }])->latest()->take(5)->get();

        // ==================== Corbeille ====================
        $stagesTrash = Stage::onlyTrashed()->get();
        $etudiantsTrash = Etudiant::onlyTrashed()->get();
        $badgesTrash = Badge::onlyTrashed()->get();
        $totalTrash = $stagesTrash->count() + $etudiantsTrash->count() +
            $badgesTrash->count();

        // ==================== SUIVI DES POINTAGES ====================
        $todayAttendance = AttendanceDay::forActiveUsers()->whereDate('attendance_date', $today)
            ->with(['etudiant.user', 'stage.site'])
            ->count();

        $todayPresent = AttendanceDay::forActiveUsers()->whereDate('attendance_date', $today)
            ->whereNotNull('first_check_in_at')
            ->count();

        $todayLate = AttendanceDay::forActiveUsers()->whereDate('attendance_date', $today)
            ->where('arrival_status', 'late')
            ->count();

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weekLateMinutes = AttendanceDay::forActiveUsers()->whereBetween('attendance_date', [$weekStart, $weekEnd])
            ->sum('late_minutes');

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
            'totalDomaines',
            'totalTypes',

            // Nouvelles statistiques
            'totalAttestations',
            'dureeMoyenne',
            'etudiantsActifs',
            'tauxEtudiantsActifs',
            'stagesParMois',
            'labelsMoisAnnee',
            'topDomaines',
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
            'rangesJour',
            'rangesSemaine',
            'rangesMois',

            'typesLabels',
            'typesData',
            'domainesStats',
            'domainesLabelsJson',
            'domainesEnCoursJson',
            'domainesTerminesJson',
            'domainesInscritsJson',
            'tauxPresence',
            'tauxReussite',
            'tauxAbandon',
            'tauxConversion',
            'evolutionInscriptionsMois',
            'evolutionStages',
            'evolutionStages30j',
            'evolutionEtudiants30j',
            'tauxCompletion',
            'activities',
            'derniersEtudiants',
            'stagesTrash',
            'etudiantsTrash',
            'badgesTrash',

            'totalTrash',

            // Suivi pointages
            'todayAttendance',
            'todayPresent',
            'todayLate',
            'weekLateMinutes'
        ));
    }

    /**
     * Détail des stagiaires inscrits sur une période précise (courbe cliquable).
     */
    public function registrationsDetail(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to'   => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'])->startOfDay();
        $to   = Carbon::parse($validated['to'])->endOfDay();

        $perPage = min(max((int) $request->input('per_page', 10), 1), 1000);

        $etudiants = Etudiant::with([
            'personnel.user',
            'stages' => fn ($q) => $q->with(['domaine', 'site'])->orderByDesc('date_debut'),
        ])
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'from'       => $from->format('Y-m-d'),
            'to'         => $to->format('Y-m-d'),
            'total'      => $etudiants->total(),
            'pagination' => [
                'current_page' => $etudiants->currentPage(),
                'last_page'    => $etudiants->lastPage(),
                'per_page'     => $etudiants->perPage(),
            ],
            'data' => $etudiants->map(fn (Etudiant $etudiant) => [
                'id'          => $etudiant->id,
                'full_name'   => $etudiant->full_name ?: '—',
                'email'       => $etudiant->email,
                'telephone'   => $etudiant->telephone,
                'genre'       => $etudiant->genre,
                'ecole'       => $etudiant->ecole,
                'niveau'      => $etudiant->niveau,
                'created_at'  => $etudiant->created_at?->format('d/m/Y H:i'),
                'account'     => $this->accountSummary($etudiant),
                'stages'      => $etudiant->stages->map(fn (Stage $stage) => [
                    'theme'       => $stage->theme,
                    'domaine'     => $stage->domaine?->nom,
                    'site'        => $stage->site?->nom,
                    'statut'      => $stage->statut,
                    'date_debut'  => $stage->date_debut?->format('d/m/Y'),
                    'date_fin'    => $stage->date_fin?->format('d/m/Y'),
                ])->values(),
            ]),
        ]);
    }

    private function accountSummary(Etudiant $etudiant): array
    {
        $user = $etudiant->personnel?->user;

        if (!$user) {
            return ['status' => 'none', 'label' => 'Sans compte'];
        }

        if ($user->email_verified_at) {
            return ['status' => 'active', 'label' => 'Compte actif'];
        }

        return ['status' => 'pending', 'label' => 'Compte en attente'];
    }
}
