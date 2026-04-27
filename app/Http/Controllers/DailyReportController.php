<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyReport\StoreDailyReportRequest;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService
    ) {}

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

        $query = DailyReport::query()
            ->visibleTo($user)
            ->with(['items', 'reviews.reviewer'])
            ->orderByDesc('report_date');

        $ownedReports = $this->ownedReportsQuery($user)
            ->with(['items', 'reviews.reviewer'])
            ->orderByDesc('report_date');

        $editReport = $request->filled('edit')
            ? (clone $ownedReports)->findOrFail((int) $request->get('edit'))
            : null;

        if ($period === 'daily') {
            $todayReport = (clone $ownedReports)
                ->whereDate('report_date', today())
                ->first();

            $editReport ??= $todayReport;
            $reports = (clone $query)->limit(10)->get();

            return view('reports.index', compact(
                'todayReport',
                'reports',
                'period',
                'activeStage',
                'isEmployee',
                'editReport'
            ));
        }

        $dateFrom = match ($period) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfWeek(),
        };

        $reports = $query
            ->whereBetween('report_date', [$dateFrom, now()])
            ->get();

        return view('reports.index', compact(
            'reports',
            'period',
            'activeStage',
            'isEmployee',
            'editReport'
        ));
    }

    public function store(StoreDailyReportRequest $request)
    {
        $this->dailyReportService
            ->storeForToday($request->user(), $request->validated());

        return back()->with('success', 'Rapport enregistre.');
    }

    public function update(Request $request, DailyReport $report)
    {
        $user = $request->user();

        if (!(clone $this->ownedReportsQuery($user))->whereKey($report->id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'status_action' => 'required|in:draft,submit',
            'summary' => 'required|string',
            'blockers' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'hours_declared' => 'nullable|numeric|min:0|max:24',
        ]);

        $status = $validated['status_action'] === 'submit'
            ? 'submitted'
            : 'draft';

        $report->fill([
            'summary' => $validated['summary'],
            'blockers' => $validated['blockers'] ?? null,
            'next_steps' => $validated['next_steps'] ?? null,
            'hours_declared' => $validated['hours_declared'] ?? 0,
            'status' => $status,
            'submitted_at' => $status === 'submitted' ? now() : null,
            // Une nouvelle edition relance le workflow de validation.
            'reviewed_by' => null,
            'reviewed_at' => null,
            'supervisor_comment' => null,
        ])->save();

        return back()->with('success', 'Rapport mis a jour.');
    }

    protected function ownedReportsQuery($user)
    {
        return DailyReport::query()->where(function ($query) use ($user) {
            $query->where('user_id', $user->id);

            if ($user->etudiant) {
                $query->orWhere('etudiant_id', $user->etudiant->id);
            }
        });
    }
}
