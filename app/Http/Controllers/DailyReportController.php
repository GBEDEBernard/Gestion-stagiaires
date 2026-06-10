<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyReport\StoreDailyReportRequest;
use App\Models\DailyReport;
use App\Models\DailyReportReview;
use App\Models\Task;
use App\Services\DailyReportService;
use App\Services\UserProfileLinkService;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService,
        protected UserProfileLinkService $profileLinkService,
        protected EmailNotificationService $emailService
    ) {}

    /**
     * Liste des rapports (tous rôles producteurs + admin/superviseur).
     */
    public function index(Request $request)
    {
<<<<<<< HEAD
        if ($request->user()->hasAnyRole(['etudiant', 'employe'])) {
            return redirect()->route('tasks.index');
        }

        $user = $request->user();
        $period = $request->get('period', 'daily');
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;
=======
        $user    = $request->user();
        $period  = $request->get('period', 'daily');

        $etudiant   = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
        $isEmployee = $user->hasRole('employe');

        $activeStage = $etudiant
            ? $this->dailyReportService->resolveActiveStageForUser($user)
            : null;

<<<<<<< HEAD
=======
        // Tâches actives du producteur (pour sélecteur dans le formulaire).
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
        $activeTasks = Task::where('owner_id', $user->id)
            ->where('status', '!=', 'completed')
            ->latest()
            ->get(['id', 'title', 'last_progress_percent']);

        $query = DailyReport::query()
            ->visibleTo($user)
            ->with(['task', 'reviews'])
            ->orderByDesc('report_date');

<<<<<<< HEAD
        $editReport = null;
=======
        $editReport  = null;
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
        $todayReport = null;

        if ($period === 'daily') {
            $todayReport = (clone $query)->whereDate('report_date', today())->first();
<<<<<<< HEAD
            $reports = (clone $query)->limit(10)->get();
            return view('reports.index', compact(
                'todayReport', 'reports', 'period', 'activeStage', 'activeTasks', 'isEmployee', 'editReport'
=======
            $reports     = (clone $query)->limit(10)->get();

            return view('reports.index', compact(
                'todayReport', 'reports', 'period',
                'activeStage', 'activeTasks', 'isEmployee', 'editReport'
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
            ));
        }

        $dateFrom = match ($period) {
            'weekly'  => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default   => now()->startOfWeek(),
        };

        $reports = $query->whereBetween('report_date', [$dateFrom, now()])->get();

        return view('reports.index', compact(
<<<<<<< HEAD
            'reports', 'period', 'activeStage', 'activeTasks', 'isEmployee', 'editReport', 'todayReport'
        ));
    }

    public function edit(DailyReport $report)
    {
        $user = auth()->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        if ($report->user_id !== $user->id && $report->etudiant_id !== optional($etudiant)->id) {
            abort(403);
        }

        $report->load(['task', 'reviews']);

        $activeTasks = Task::where('owner_id', $user->id)
            ->where(function ($q) use ($report) {
                $q->where('status', '!=', 'completed')->orWhere('id', $report->task_id);
            })
            ->latest()
            ->get(['id', 'title', 'last_progress_percent']);

        return view('reports.edit', compact('report', 'activeTasks'));
    }

=======
            'reports', 'period', 'activeStage', 'activeTasks',
            'isEmployee', 'editReport', 'todayReport'
        ));
    }

    /**
     * Détails d'un rapport (JSON pour la modale).
     */
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
    public function show(Request $request, DailyReport $report)
    {
        $user     = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

<<<<<<< HEAD
        if ($report->user_id !== $user->id &&
=======
        if (
            $report->user_id !== $user->id &&
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
            $report->etudiant_id !== optional($etudiant)->id &&
            !$user->hasRole('admin') &&
            !$user->hasRole('superviseur')) {
            abort(403);
        }

        $report->load(['reviews.reviewer']);

        return response()->json([
            'report' => [
                'id'                   => $report->id,
                'introduction'         => $report->introduction,
                'summary'              => $report->summary,
                'blockers'             => $report->blockers,
                'next_steps'           => $report->next_steps,
                'hours_declared'       => $report->hours_declared,
                'status'               => $report->status,
                'report_date'          => $report->report_date,
                'report_date_formatted'=> $report->report_date->format('l j F Y'),
                'created_at'           => $report->created_at,
                'created_at_formatted' => $report->created_at->diffForHumans(),
                'updated_at'           => $report->updated_at,
                'updated_at_formatted' => $report->updated_at->diffForHumans(),
            ],
            'reviews' => $report->reviews->map(fn($r) => [
                'id'            => $r->id,
                'comment'       => $r->comment,
                'reviewer_name' => $r->reviewer->name,
                'created_at'    => $r->created_at->diffForHumans(),
                'action'        => $r->action ?? null,
            ]),
        ]);
    }

    /**
     * Formulaire d'édition d'un rapport (page dédiée).
     */
    public function edit(DailyReport $report)
    {
        $user     = auth()->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        if (
            $report->user_id !== $user->id &&
            $report->etudiant_id !== optional($etudiant)->id
        ) {
            abort(403);
        }

        $report->load(['task', 'reviews']);

        $activeTasks = Task::where('owner_id', $user->id)
            ->where(fn($q) => $q->where('status', '!=', 'completed')->orWhere('id', $report->task_id))
            ->latest()
            ->get(['id', 'title', 'last_progress_percent']);

        return view('reports.edit', compact('report', 'activeTasks'));
    }

    /**
     * Créer un rapport (appel depuis la vue index ou workspace).
     */
    public function store(StoreDailyReportRequest $request)
    {
<<<<<<< HEAD
        $this->dailyReportService->storeForToday($request->user(), $request->validated());

        // Notifier par email si le rapport est lié à une tâche
        if ($request->filled('task_id')) {
            $task = Task::find($request->task_id);
            if ($task) {
                $this->emailService->notifyReportSubmitted($task);
            }
        }
=======
        $data = $request->validated();

        unset($data['voice']); // résidu éventuel de l'ancienne implémentation

        $this->dailyReportService->storeForToday($request->user(), $data);
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f

        return back()->with('success', 'Rapport enregistré.');
    }

    /**
     * Mettre à jour un rapport existant.
     */
    public function update(Request $request, DailyReport $report)
    {
        $user     = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        if ($report->user_id !== $user->id && $report->etudiant_id !== optional($etudiant)->id) {
            abort(403);
        }

        $data = $request->validate([
            'status_action'        => 'nullable|in:draft,submit',
            'introduction'         => 'nullable|string|max:5000',
            'summary'              => 'required|string',
            'blockers'             => 'nullable|string',
            'next_steps'           => 'nullable|string',
            'hours_declared'       => 'nullable|numeric|min:0|max:24',
            'report_date'          => 'nullable|date',
            'task_id'              => 'nullable|integer|exists:tasks,id',
            'task_progress_percent'=> 'nullable|integer|min:0|max:100',
        ]);

        $statusAction = $data['status_action'] ?? null;
        unset($data['status_action']);

        if ($statusAction === 'submit') {
            $data['status']       = 'submitted';
            $data['submitted_at'] = now();
        } elseif ($statusAction === 'draft') {
            $data['status'] = 'draft';
        }

        $report->update($data);

<<<<<<< HEAD
=======
        // Répercuter la progression sur la tâche si rattachée.
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
        if (!empty($data['task_id'])) {
            $task = Task::find($data['task_id']);
            if ($task && $task->owner_id === $user->id && $task->status !== 'completed') {
                $report->forceFill([
                    'task_id'              => $task->id,
                    'task_progress_percent'=> $data['task_progress_percent'] ?? $task->last_progress_percent,
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
<<<<<<< HEAD
}
=======

    /**
     * Ajouter un commentaire sur un rapport (superviseur / admin / auteur du rapport).
     */
    public function storeComment(Request $request, DailyReport $report)
    {
        $user     = $request->user();
        $etudiant = $this->profileLinkService->ensureStudentProfile($user) ?? $user->etudiant;

        // L'auteur, le superviseur et l'admin peuvent commenter.
        $isAuthor = $report->user_id === $user->id
            || $report->etudiant_id === optional($etudiant)->id;

        if (!$isAuthor && !$user->hasAnyRole(['admin', 'superviseur'])) {
            abort(403);
        }

        $data = $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        DailyReportReview::create([
            'daily_report_id' => $report->id,
            'reviewer_id'     => $user->id,
            'comment'         => $data['comment'],
            'reviewed_at'     => now(),
            'action'          => $user->hasAnyRole(['admin', 'superviseur']) ? 'comment' : 'author_reply',
        ]);

        // Marquer le rapport comme relu si c'est un superviseur/admin.
        if ($user->hasAnyRole(['admin', 'superviseur']) && $report->status === 'submitted') {
            $report->update([
                'status'      => 'reviewed',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Commentaire ajouté.');
    }
}
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
