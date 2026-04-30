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
        $period = $request->get('period', 'day');
        $userId = $request->get('user_id');
        $siteId = $request->get('site_id');
        $schoolFilter = $request->get('school');

        // Convertir period → date_from/to
        $dateCarbon = Carbon::parse($date);
        $dateFrom = $dateTo = $dateCarbon->format('Y-m-d');

        switch ($period) {
            case 'week':
                $dateFrom = $dateCarbon->copy()->startOfWeek()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $dateFrom = $dateCarbon->copy()->startOfMonth()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfMonth()->format('Y-m-d');
                break;
        }

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'user_id' => $userId,
            'site_id' => $siteId,
            'school' => $schoolFilter,
        ];

        $query = $this->presenceService->listAttendanceDays($filters, 9999)
            ->with(['user', 'etudiant.user', 'stage.site', 'checkInEvent.site', 'checkInEvent.geofence.site', 'checkOutEvent', 'anomalies'])
            ->orderByDesc('attendance_date');

        $days = $query->paginate(10);

        // Propriété virtuelle resolved_site_name
        $days->getCollection()->transform(function ($day) {
            $day->resolved_site_name = $day->checkInEvent?->site?->name
                ?? $day->checkInEvent?->geofence?->site?->name
                ?? $day->stage?->site?->name
                ?? null;
            return $day;
        });

        // Stats
        $today = today();
        $todayCount = AttendanceEvent::whereDate('occurred_at', $today)->count();
        $checkinsToday = AttendanceEvent::where('event_type', 'check_in')->whereDate('occurred_at', $today)->count();
        $checkoutsToday = AttendanceEvent::where('event_type', 'check_out')->whereDate('occurred_at', $today)->count();
        $recentAnomalies = AttendanceAnomaly::where('status', 'open')
            ->where('detected_at', '>=', now()->subDays(7))
            ->count();
        $avgAccuracy = AttendanceEvent::whereDate('occurred_at', $today)->avg('accuracy_meters') ?? 0;
        $periodDays = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;

        // Listes filtres
        $users = User::whereHas('attendanceDays')->orderBy('name')->limit(50)->get(['id', 'name']);
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $schools = Etudiant::whereNotNull('ecole')->distinct()->pluck('ecole')->sort();

        return view('admin.presence.pointage-suivi', compact(
            'days',
            'todayCount',
            'checkinsToday',
            'checkoutsToday',
            'recentAnomalies',
            'avgAccuracy',
            'users',
            'sites',
            'schools',
            'date',
            'period',
            'userId',
            'siteId',
            'schoolFilter',
            'periodDays'
        ));
    }
    // Version épurée pour impression (sans pagination, avec tous les résultats)
    public function pointageSuiviPrint(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $period = $request->get('period', 'day');
        $userId = $request->get('user_id');
        $siteId = $request->get('site_id');
        $schoolFilter = $request->get('school');

        // Même logique que pointageSuivi mais sans pagination
        $dateCarbon = Carbon::parse($date);
        $dateFrom = $dateTo = $dateCarbon->format('Y-m-d');

        switch ($period) {
            case 'week':
                $dateFrom = $dateCarbon->copy()->startOfWeek()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $dateFrom = $dateCarbon->copy()->startOfMonth()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfMonth()->format('Y-m-d');
                break;
        }

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'user_id' => $userId,
            'site_id' => $siteId,
            'school' => $schoolFilter,
        ];

        $query = $this->presenceService->listAttendanceDays($filters, 9999)
            ->with(['user', 'etudiant.user', 'stage.site', 'checkInEvent.site', 'checkInEvent.geofence.site', 'checkOutEvent', 'anomalies'])
            ->orderByDesc('attendance_date');

        $days = $query->get(); // Tous les résultats pour impression

        $days->transform(function ($day) {
            $day->resolved_site_name = $day->checkInEvent?->site?->name
                ?? $day->checkInEvent?->geofence?->site?->name
                ?? $day->stage?->site?->name
                ?? null;
            return $day;
        });

        return view('admin.presence.print', compact('days', 'date', 'period', 'userId', 'siteId', 'schoolFilter'));
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
