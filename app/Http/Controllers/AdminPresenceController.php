<?php

namespace App\Http\Controllers;

use App\Http\Requests\Presence\ResolveAnomalyRequest;
use App\Models\AttendanceDay;
use App\Models\AttendanceAnomaly;
use App\Models\Site;
use App\Models\User;
use App\Services\AdminPresenceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AttendanceEvent;
use App\Models\Etudiant;


class AdminPresenceController extends Controller
{
    public function __construct(
        protected AdminPresenceService $presenceService
    ) {}

    /**
     * Page principale de supervision des présences.
     */
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $period = $request->get('period', ($dateFrom || $dateTo) ? 'custom' : 'today');
        $group = $request->get('group', 'all');

        $overview = $this->presenceService->getTodayOverview();
        $globalStats = $this->presenceService->getGlobalStats($period, $dateFrom, $dateTo);
        $groupStats = $this->presenceService->getStatsByGroup($group, $period, $dateFrom, $dateTo);
        $topLate = AttendanceDay::topLate(10, $period, $dateFrom, $dateTo)->get();
        $absences = $this->presenceService->getAbsences($period, $dateFrom, $dateTo);

        $days = $this->presenceService->listAttendanceDays($request->only([
            'date_from',
            'date_to',
            'etudiant_id',
            'site_id',
            'status',
            'anomalies_only'
        ]), 25);

        return view('admin.presence.index', compact(
            'overview',
            'globalStats',
            'groupStats',
            'topLate',
            'absences',
            'period',
            'group',
            'days',
            'request'
        ));
    }

    /**
     * Stats mensuelles détaillées (legacy).
     */
    public function stats(Request $request)
    {
        $year = $request->get('year', today()->year);
        $month = $request->get('month', today()->month);
        $userId = $request->get('user_id');

        $stats = $this->presenceService->getMonthlyStats($year, $month, $userId);

        return response()->json([
            'stats' => $stats,
            'period' => [
                'year' => $year,
                'month' => $month,
                'label' => Carbon::create($year, $month)->isoFormat('MMMM YYYY'),
            ],
        ]);
    }

    /**
     * Dashboard stats globales avec graphs.
     */
    public function dashboardStats(Request $request)
    {
        $period = $request->get('period', 'today');
        $group = $request->get('group', 'all');

        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $period = $request->get('period', ($dateFrom || $dateTo) ? 'custom' : 'today');

        $globalStats = $this->presenceService->getGlobalStats($period, $dateFrom, $dateTo);
        $groupStats = $this->presenceService->getStatsByGroup($group, $period, $dateFrom, $dateTo);
        $topLate = AttendanceDay::topLate(10, $period, $dateFrom, $dateTo)->get();
        $absences = $this->presenceService->getAbsences($period, $dateFrom, $dateTo);

        if ($request->wantsJson()) {
            return response()->json([
                'global' => $globalStats,
                'groups' => $groupStats,
                'top_late' => $topLate,
                'absences' => $absences,
            ]);
        }

        return view('admin.presence.stats', compact(
            'globalStats',
            'groupStats',
            'topLate',
            'absences',
            'period',
            'group'
        ));
    }

    /**
     * Stats détaillées utilisateur (antécédents).
     */
    public function userStats(User $user, Request $request)
    {
        $period = $request->get('period', 'month');

        $userStats = $this->presenceService->getUserDetailedStats($user->id, $period);
        $anomalies = AttendanceAnomaly::where('user_id', $user->id)
            ->whereIn('status', ['open', 'flagged'])
            ->with('attendanceEvent.stage.site')
            ->latest()
            ->limit(20)
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'stats' => $userStats,
                'anomalies' => $anomalies,
            ]);
        }

        return view('admin.presence.user-stats', compact('user', 'userStats', 'anomalies', 'period'));
    }

    /**
     * Liste anomalies.
     */
    /**
     * Liste anomalies.
     */
    public function anomalies(Request $request)
    {
        $anomalies = $this->presenceService->getOpenAnomalies(100);

        if ($request->wantsJson()) {
            return response()->json($anomalies);
        }

        // ✅ Correction : utiliser Blade au lieu d'Inertia
        return view('admin.presence.anomalies', compact('anomalies'));
    }

    /**
     * ✅ Suivi Pointage - Admin
     */
public function pointageSuivi(Request $request)
{
    $date = $request->get('date', today()->format('Y-m-d'));
    $userId = $request->get('user_id');
    $siteId = $request->get('site_id');
    $schoolFilter = $request->get('school');

    // Requête sur AttendanceDay (un enregistrement par jour et par utilisateur)
    $query = AttendanceDay::with([
        'user',
        'etudiant.user',
        'stage.site',
        'checkInEvent.site',
        'checkInEvent.geofence.site',
        'checkOutEvent',
        'anomalies'
    ])->whereDate('attendance_date', $date);

    if ($userId) {
        $query->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereHas('etudiant', fn($q2) => $q2->where('user_id', $userId));
        });
    }
    if ($siteId) {
        $query->whereHas('checkInEvent.site', fn($q) => $q->where('id', $siteId));
    }
    if ($schoolFilter) {
        $query->whereHas('etudiant', fn($q) => $q->where('ecole', $schoolFilter));
    }

    // Pagination (10 résultats par page)
    $days = $query->orderByDesc('attendance_date')->paginate(10);

    // Ajouter une propriété virtuelle "resolved_site_name"
    foreach ($days as $day) {
        $day->resolved_site_name = $day->checkInEvent?->site?->name
            ?? $day->checkInEvent?->geofence?->site?->name
            ?? $day->stage?->site?->name
            ?? null;
    }

    // Statistiques rapides (inchangées)
    $today = today();
    $todayCount = AttendanceEvent::whereDate('occurred_at', $today)->count();
    $checkinsToday = AttendanceEvent::where('event_type', 'check_in')->whereDate('occurred_at', $today)->count();
    $checkoutsToday = AttendanceEvent::where('event_type', 'check_out')->whereDate('occurred_at', $today)->count();
    $recentAnomalies = AttendanceAnomaly::where('status', 'open')
        ->where('detected_at', '>=', now()->subDays(7))
        ->count();
    $avgAccuracy = AttendanceEvent::whereDate('occurred_at', $today)->avg('accuracy_meters') ?? 0;

    // Listes pour les filtres
    $users = User::whereHas('attendanceEvents')->orderBy('name')->get(['id', 'name']);
    $sites = Site::where('is_active', true)->orderBy('name')->get();
    $schools = Etudiant::whereNotNull('ecole')->distinct()->pluck('ecole')->sort();

    return view('admin.presence.pointage-suivi', compact(
        'days', 'todayCount', 'checkinsToday', 'checkoutsToday', 'recentAnomalies',
        'avgAccuracy', 'users', 'sites', 'schools', 'date', 'userId', 'siteId', 'schoolFilter'
    ));
}
// Version épurée pour impression (sans pagination, avec tous les résultats)
public function pointageSuiviPrint(Request $request)
{
    $date = $request->get('date', today()->format('Y-m-d'));
    $userId = $request->get('user_id');
    $siteId = $request->get('site_id');
    $schoolFilter = $request->get('school');

    $query = AttendanceDay::with([
        'user',
        'etudiant.user',
        'stage.site',
        'checkInEvent.site',
        'checkInEvent.geofence.site',
        'checkOutEvent',
        'anomalies'
    ])->whereDate('attendance_date', $date);

    if ($userId) {
        $query->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereHas('etudiant', fn($q2) => $q2->where('user_id', $userId));
        });
    }
    if ($siteId) {
        $query->whereHas('checkInEvent.site', fn($q) => $q->where('id', $siteId));
    }
    if ($schoolFilter) {
        $query->whereHas('etudiant', fn($q) => $q->where('ecole', $schoolFilter));
    }

    $days = $query->orderByDesc('attendance_date')->get(); // Pas de pagination, tout pour l'impression

    foreach ($days as $day) {
        $day->resolved_site_name = $day->checkInEvent?->site?->name
            ?? $day->checkInEvent?->geofence?->site?->name
            ?? $day->stage?->site?->name
            ?? null;
    }

    return view('admin.presence.print', compact('days', 'date', 'userId', 'siteId', 'schoolFilter'));
}
    /**
     * Export pointages CSV
     */
    public function exportPointages(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $query = \App\Models\AttendanceEvent::with(['user', 'checkInDay.stage.site', 'checkOutDay.stage.site'])
            ->whereDate('occurred_at', $date);

        $events = $query->get();

        $csv = $events->map(function ($event) {
            return [
                $event->occurred_at->format('d/m/Y H:i'),
                $event->user?->name ?? 'N/A',
                $event->event_type === 'check_in' ? 'Entrée' : 'Sortie',
                $event->attendanceDay?->stage?->site?->nom ?? 'Hors site',
                $event->gps_accuracy ?? 'N/A',
                $event->status,
            ];
        });

        return response()->streamDownload(function () use ($csv) {
            echo "Date Heure,Utilisateur,Type,Site,Précision,Statut\n";
            foreach ($csv as $row) {
                echo implode(';', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
            }
        }, 'pointages-' . $date . '.csv');
    }
    /**
     * Résoudre anomalie.
     */
    public function resolveAnomaly(ResolveAnomalyRequest $request, int $anomalyId)
    {
        $this->presenceService->resolveAnomaly($anomalyId, $request->validated());

        return redirect()->back()->with('success', 'Anomalie résolue.');
    }

    /**
     * Export CSV amélioré.
     */
    public function export(Request $request)
    {
        $days = $this->presenceService->listAttendanceDays($request->only([
            'date_from',
            'date_to'
        ]));

        $csv = $days->cursor()->map(function ($day) {
            return [
                $day->attendance_date->format('d/m/Y'),
                $day->etudiant?->nom ?? $day->user?->name ?? 'Personnel',
                round($day->worked_minutes / 60, 1),
                $day->late_minutes,
                $day->early_departure_minutes,
                $day->day_status ?? 'N/A',
                $day->anomalies->where('status', 'open')->count(),
            ];
        });

        return response()->streamDownload(function () use ($csv) {
            echo "Date,Nom,Heures,Retard min,Départ anticipé,Statut,Anomalies ouvertes\n";
            foreach ($csv as $row) {
                echo implode(';', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
            }
        }, 'presence-stats-' . now()->format('Y-m-d-His') . '.csv');
    }
}
