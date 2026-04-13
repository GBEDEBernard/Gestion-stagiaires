<?php

namespace App\Http\Controllers;

use App\Http\Requests\Presence\ResolveAnomalyRequest;
use App\Models\AttendanceDay;
use App\Models\Site;
use App\Models\User;
use App\Services\AdminPresenceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $overview = $this->presenceService->getTodayOverview();

        $days = $this->presenceService->listAttendanceDays($request->only([
            'date_from',
            'date_to',
            'etudiant_id',
            'site_id',
            'status',
            'anomalies_only'
        ]), 25);

        if ($request->wantsJson()) {
            return response()->json($days->paginate(25));
        }

        return Inertia::render('Admin/Presence/Index', [
            'overview' => $overview,
            'days' => $days->paginate(25),
            'users' => $this->presenceService->searchUsers($request->get('user_search', '')),
            'sites' => Site::select('id', 'name')->get(),
            'filters' => $request->only(['date_from', 'date_to', 'etudiant_id', 'site_id']),
        ]);
    }

    /**
     * Stats mensuelles détaillées.
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
     * Liste des anomalies ouvertes.
     */
    public function anomalies(Request $request)
    {
        $anomalies = $this->presenceService->getOpenAnomalies(100);

        if ($request->wantsJson()) {
            return response()->json($anomalies);
        }

        return Inertia::render('Admin/Presence/Anomalies', [
            'anomalies' => $anomalies,
        ]);
    }

    /**
     * Résoudre une anomalie.
     */
    public function resolveAnomaly(ResolveAnomalyRequest $request, int $anomalyId)
    {
        $this->presenceService->resolveAnomaly($anomalyId, $request->validated());

        return redirect()->back()->with('success', 'Anomalie résolue avec succès.');
    }

    /**
     * Export CSV des présences (simple).
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
                $day->etudiant?->nom ?? 'Personnel',
                $day->worked_minutes / 60,
                $day->late_minutes,
                $day->early_departure_minutes,
                $day->day_status,
                $day->anomaly_count,
            ];
        });

        return response()->streamDownload(function () use ($csv) {
            echo "Date,Nom,Heures travaillées,Retard (min),Départ anticipé,Statut,Anomalies\n";
            foreach ($csv as $row) {
                echo implode(',', $row) . "\n";
            }
        }, 'presences-' . today()->format('Y-m-d') . '.csv');
    }
}
