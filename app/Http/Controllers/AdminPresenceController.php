<?php

namespace App\Http\Controllers;

use App\Http\Requests\Presence\ResolveAnomalyRequest;
use App\Models\AttendanceDay;
use App\Models\AttendanceAnomaly;
use App\Models\DailyReport;
use App\Models\Site;
use App\Models\User;
use App\Services\AdminPresenceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AttendanceEvent;
use App\Models\Etudiant;
use Illuminate\Pagination\LengthAwarePaginator;


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
        $period = $request->get('period', 'today');
        $group = $request->get('group', 'all');

        $overview = $this->presenceService->getTodayOverview();
        $globalStats = $this->presenceService->getGlobalStats($period, $dateFrom, $dateTo);
        $groupStats = $this->presenceService->getStatsByGroup($group, $period, $dateFrom, $dateTo);

        // ✅ Plage réellement appliquée — sert à remplir Du/Au et à construire les liens des onglets
        $rangeStart = $globalStats['range_start'];
        $rangeEnd   = $globalStats['range_end'];
        $topLate = AttendanceDay::topLate(10, $period, $dateFrom, $dateTo)->forActiveUsers()->get();
        $absenceData = $this->presenceService->getAbsencesWithDetails($period, $dateFrom, $dateTo);
        $absences = $absenceData['counts'];
        $absenceDays = $absenceData['details'];
        $absenceItems = $absenceData['items'];

        $days = $this->presenceService->listAttendanceDays($request->only([
            'date_from',
            'date_to',
            'etudiant_id',
            'site_id',
            'status',
            'anomalies_only'
        ]), 25);

        // ── Rapports journaliers : stats dynamiques ──
        $reportStats = [
            'drafts'   => DailyReport::where('status', 'draft')->whereDate('report_date', today())->count(),
            'pending'  => DailyReport::where('status', 'submitted')->count(),
            'approved' => DailyReport::where('status', 'reviewed')
                ->whereBetween('reviewed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
        $totalReports = DailyReport::count();
        $reviewedCount = DailyReport::where('status', 'reviewed')->count();
        $reportStats['validation_rate'] = $totalReports > 0 ? round(($reviewedCount / $totalReports) * 100) : 0;

        return view('admin.presence.index', compact(
            'overview',
            'globalStats',
            'groupStats',
            'topLate',
            'absences',
            'absenceItems',
            'period',
            'group',
            'days',
            'reportStats',
            'request',
            'rangeStart',
            'rangeEnd'
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
        $topLate = AttendanceDay::topLate(10, $period, $dateFrom, $dateTo)->forActiveUsers()->get();
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
    $filter = $request->get('filter', 'all'); // all | today | week

    $query = AttendanceAnomaly::with([
            'attendanceEvent.stage.etudiant.user',
            'attendanceDay.stage.site',
            'user',
        ])
        ->where('status', 'open');

    if ($filter === 'today') {
        $query->whereDate('detected_at', today());
    } elseif ($filter === 'week') {
        $query->whereBetween('detected_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    $anomalies = $query->orderByDesc('detected_at')->get();

    // ✅ Regroupement par utilisateur, puis par type d'anomalie à l'intérieur
    $grouped = $anomalies
        ->groupBy(fn($a) => $a->attendanceEvent->stage?->etudiant?->nom ?? $a->user?->name ?? 'Inconnu')
        ->map(function ($items, $name) {
            $severityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];

            $types = $items->groupBy('type')->map(function ($typeItems) {
                $first = $typeItems->first();
                return [
                    'type'        => $first->type,
                    'label'       => $first->type_label,
                    'description' => $first->type_description,
                    'solution'    => $first->type_solution,
                    'severity'    => $first->severity,
                    'count'       => $typeItems->count(),
                    'ids'         => $typeItems->pluck('id')->values(),
                    'items'       => $typeItems->map(fn($a) => [
                        'id'          => $a->id,
                        'date'        => $a->detected_at->format('d/m/Y H:i'),
                        'observation' => $a->payload['message_observation'] ?? null,
                    ])->values(),
                ];
            })->sortByDesc(fn($t) => $severityOrder[$t['severity']] ?? 0)->values();

            return [
                'user'          => $name,
                'total'         => $items->count(),
                'max_severity'  => $types->pluck('severity')->map(fn($s) => $severityOrder[$s] ?? 0)->max(),
                'last_detected' => $items->max('detected_at'),
                'types'         => $types,
            ];
        })
        ->sortByDesc('total')
        ->values();

    if ($request->wantsJson()) {
        return response()->json($grouped);
    }

    return view('admin.presence.anomalies', compact('anomalies', 'grouped', 'filter'));
}

    /**
     * Suivi Pointage - Admin
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
            case 'custom':
                $dateFrom = $request->get('date_from', $dateFrom);
                $dateTo = $request->get('date_to', $dateTo);
                break;
            case 'week':
                $dateFrom = $dateCarbon->copy()->startOfWeek()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $dateFrom = $dateCarbon->copy()->startOfMonth()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfMonth()->format('Y-m-d');
                break;
        }

        // ── Rapport détaillé par utilisateur (présents + retards + absences) ──
        $detailCollection = $this->presenceService->getPointageDetail($dateFrom, $dateTo, [
            'user_id' => $userId,
            'site_id' => $siteId,
            'school'  => $schoolFilter,
        ]);

        // Détail ciblé si un utilisateur précis est choisi, sinon pagination par utilisateur
        if ($userId) {
            $detail = $detailCollection->values();
        } else {
            $page    = LengthAwarePaginator::resolveCurrentPage('detail_page');
            $perPage = 10;
            $dataset = $detailCollection->forPage($page, $perPage)->values();
            $detail  = new LengthAwarePaginator(
                $dataset,
                $detailCollection->count(),
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query(), 'pageName' => 'detail_page']
            );
        }

        // ── Stats ──
        $today = today();
        $todayCount = AttendanceEvent::whereDate('occurred_at', $today)->count();
        $checkinsToday = AttendanceEvent::where('event_type', 'check_in')->whereDate('occurred_at', $today)->count();
        $checkoutsToday = AttendanceEvent::where('event_type', 'check_out')->whereDate('occurred_at', $today)->count();
        $recentAnomalies = AttendanceAnomaly::where('status', 'open')
            ->where('detected_at', '>=', now()->subDays(7))
            ->count();
        $avgAccuracy = AttendanceEvent::whereDate('occurred_at', $today)->avg('accuracy_meters') ?? 0;
        $periodDays = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;

        // ── Listes filtres ──
        $users = User::where(function ($q) {
                $q->whereHas('personnel.personnable', fn($sq) => $sq)
                  ->orWhereHas('etudiant');
            })
            ->orderBy('name')
            ->get();
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $schools = Etudiant::whereNotNull('ecole')->distinct()->pluck('ecole')->sort();

        return view('admin.presence.pointage-suivi', compact(
            'detail',
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
            'dateFrom',
            'dateTo',
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
            case 'custom':
                $dateFrom = $request->get('date_from', $dateFrom);
                $dateTo = $request->get('date_to', $dateTo);
                break;
            case 'week':
                $dateFrom = $dateCarbon->copy()->startOfWeek()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $dateFrom = $dateCarbon->copy()->startOfMonth()->format('Y-m-d');
                $dateTo = $dateCarbon->copy()->endOfMonth()->format('Y-m-d');
                break;
        }

        $detail = $this->presenceService->getPointageDetail($dateFrom, $dateTo, [
            'user_id' => $userId,
            'site_id' => $siteId,
            'school'  => $schoolFilter,
        ]);

        // ── Totaux globaux du rapport ──
        $globalTotals = [
            'users'          => $detail->count(),
            'present'        => $detail->sum(fn ($b) => $b['totals']['present']),
            'absent'         => $detail->sum(fn ($b) => $b['totals']['absent']),
            'corrected'      => $detail->sum(fn ($b) => $b['totals']['corrected']),
            'late_minutes'   => $detail->sum(fn ($b) => $b['totals']['late_minutes']),
            'worked_minutes' => $detail->sum(fn ($b) => $b['totals']['worked_minutes']),
        ];

        $userNames = [];

        return view('admin.presence.print', compact(
            'detail',
            'globalTotals',
            'date',
            'period',
            'dateFrom',
            'dateTo',
            'userId',
            'siteId',
            'schoolFilter',
            'userNames'
        ));
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
  /**
 * Résoudre une anomalie individuelle.
 */
public function resolveAnomaly(ResolveAnomalyRequest $request, int $anomalyId)
{
    $this->presenceService->resolveAnomaly($anomalyId, $request->validated());

    return redirect()->back()->with('success', 'Anomalie résolue.');
}

/**
 * Résoudre plusieurs anomalies identiques en une fois (bouton "Tout résoudre").
 */
public function resolveAnomaliesBulk(Request $request)
{
    $ids = (array) $request->input('ids', []);
    $note = $request->input('resolution_note');

    foreach ($ids as $id) {
        $this->presenceService->resolveAnomaly((int) $id, [
            'reviewed_by'     => auth()->id(),
            'resolution_note' => $note,
        ]);
    }

    return redirect()->back()->with('success', count($ids) . ' anomalie(s) résolue(s).');
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
