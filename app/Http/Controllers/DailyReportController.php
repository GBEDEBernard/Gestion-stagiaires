<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyReport\StoreDailyReportRequest;
use App\Models\AttendanceDay;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService
    ) {}

    /**
     * 📊 AFFICHAGE (daily / history)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $period = $request->get('period', 'daily');

        $etudiant = $user->etudiant;
        $isEmployee = $user->hasRole('employe');

        if (!$etudiant && !$isEmployee && !$user->hasRole('admin')) {
            abort(403);
        }

        $activeStage = $etudiant
            ? $this->dailyReportService->resolveActiveStageForUser($user)
            : null;

        $query = DailyReport::query()->visibleTo($user)
            ->with(['items', 'reviews'])
            ->orderByDesc('report_date');

        /* ======================
       DAILY
    ====================== */
        if ($period === 'daily') {

            $todayReport = (clone $query)
                ->whereDate('report_date', today())
                ->first();

            $reports = (clone $query)->limit(10)->get();

            return view('reports.index', compact(
                'todayReport',
                'reports',
                'period',
                'activeStage',
                'isEmployee'
            ));
        }

        /* ======================
       WEEKLY / MONTHLY
    ====================== */

        $dateFrom = match ($period) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfWeek()
        };

        $reports = $query
            ->whereBetween('report_date', [$dateFrom, now()])
            ->get();

        return view('reports.index', compact(
            'reports',
            'period',
            'activeStage',
            'isEmployee'
        ));
    }

    public function store(StoreDailyReportRequest $request)
    {
        $this->dailyReportService
            ->storeForToday($request->user(), $request->validated());

        return back()->with('success', 'Rapport enregistré.');
    }

    public function update(Request $request, DailyReport $report)
    {
        $user = $request->user();

        if (
            $report->user_id !== $user->id &&
            $report->etudiant_id !== optional($user->etudiant)->id
        ) {
            abort(403);
        }

        $report->update($request->validate([
            'summary' => 'required|string',
            'blockers' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'hours_declared' => 'nullable|numeric|min:0|max:24',
        ]));

        return back()->with('success', 'Rapport mis à jour.');
    }
}
