<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyReport\StoreDailyReportRequest;
use App\Models\AttendanceDay;
use App\Models\DailyReport;
use App\Models\Task;
use App\Services\DailyReportService;
use App\Services\UserProfileLinkService;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService,
        protected UserProfileLinkService $profileLinkService
    ) {}

    /**
     * 📊 AFFICHAGE (daily / history)
     */
    public function index(Request $request)
    {
        // T-004 : les rapports sont désormais gérés dans l'Espace de travail (sous chaque tâche).
        if ($request->user()->hasAnyRole(['etudiant', 'employe'])) {
            return redirect()->route('tasks.index');
        }

        $user = $request->user();
        $period = $request->get('period', 'daily');

        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;
        $isEmployee = $user->hasRole('employe');

        if (!$etudiant && !$isEmployee && !$user->hasRole('admin')) {
            abort(403);
        }

        $activeStage = $etudiant
            ? $this->dailyReportService->resolveActiveStageForUser($user)
            : null;

        // Tâches actives du producteur, proposées dans le formulaire de rapport.
        $activeTasks = Task::where('owner_id', $user->id)
            ->where('status', '!=', 'completed')
            ->latest()
            ->get(['id', 'title', 'last_progress_percent']);

        $query = DailyReport::query()->visibleTo($user)
            ->with(['task', 'reviews'])
            ->orderByDesc('report_date');

        /* ======================
       DAILY
    ====================== */
        $editReport = null;
        $todayReport = null;

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
                'activeTasks',
                'isEmployee',
                'editReport'
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
            'activeTasks',
            'isEmployee',
            'editReport',
            'todayReport'
        ));
    }

    /**
     * 📝 ÉDITER UN RAPPORT
     */
    public function edit(DailyReport $report)
    {
        $user = auth()->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        // Vérifier les permissions
        if (
            $report->user_id !== $user->id &&
            $report->etudiant_id !== optional($etudiant)->id
        ) {
            abort(403);
        }

        $report->load(['task', 'reviews']);

        $activeTasks = Task::where('owner_id', $user->id)
            ->where(function ($q) use ($report) {
                $q->where('status', '!=', 'completed')->orWhere('id', $report->task_id);
            })
            ->latest()
            ->get(['id', 'title', 'last_progress_percent']);

        return view('reports.edit', [
            'report'      => $report,
            'activeTasks' => $activeTasks,
        ]);
    }

    public function show(Request $request, DailyReport $report)
    {
        $user = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        // Vérifier que l'utilisateur peut voir ce rapport
        if (
            $report->user_id !== $user->id &&
            $report->etudiant_id !== optional($etudiant)->id &&
            !$user->hasRole('admin') &&
            !$user->hasRole('superviseur')
        ) {
            abort(403);
        }

        $report->load(['reviews.reviewer']);

        return response()->json([
            'report' => [
                'id' => $report->id,
                'summary' => $report->summary,
                'blockers' => $report->blockers,
                'next_steps' => $report->next_steps,
                'hours_declared' => $report->hours_declared,
                'status' => $report->status,
                'report_date' => $report->report_date,
                'report_date_formatted' => $report->report_date->format('l j F Y'),
                'created_at' => $report->created_at,
                'created_at_formatted' => $report->created_at->diffForHumans(),
                'updated_at' => $report->updated_at,
                'updated_at_formatted' => $report->updated_at->diffForHumans(),
            ],
            'reviews' => $report->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'comment' => $review->comment,
                    'reviewer_name' => $review->reviewer->name,
                    'created_at' => $review->created_at->diffForHumans(),
                    'action' => $review->action,
                ];
            }),
        ]);
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
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        if (
            $report->user_id !== $user->id &&
            $report->etudiant_id !== optional($etudiant)->id
        ) {
            abort(403);
        }

        $data = $request->validate([
            'summary' => 'required|string',
            'blockers' => 'nullable|string',
            'next_steps' => 'nullable|string',
            'hours_declared' => 'nullable|numeric|min:0|max:24',
            'report_date' => 'nullable|date',
            'task_id' => 'nullable|integer|exists:tasks,id',
            'task_progress_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $report->update($data);

        // Si une tâche (du producteur) est rattachée, répercuter la progression.
        if (!empty($data['task_id'])) {
            $task = \App\Models\Task::find($data['task_id']);
            if ($task && $task->owner_id === $user->id && $task->status !== 'completed') {
                $report->forceFill([
                    'task_id' => $task->id,
                    'task_progress_percent' => $data['task_progress_percent'] ?? $task->last_progress_percent,
                ])->save();

                $this->dailyReportService->syncTaskProgress(
                    $report->fresh(),
                    $task,
                    $user,
                    $report->status === 'submitted'
                );
            }
        }

        return back()->with('success', 'Rapport mis à jour.');
    }
}
