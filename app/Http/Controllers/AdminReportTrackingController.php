<?php

namespace App\Http\Controllers;

use App\Mail\BilanHebdomadaireMail;
use App\Models\DailyReport;
use App\Models\DailyReportReview;
use App\Models\TaskMessage;
use App\Models\WeeklyBilanSend;
use App\Services\AdminPresenceService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportTrackingController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        $dateFilter = $request->get('date', now()->format('Y-m-d'));
        $filterDate = Carbon::createFromFormat('Y-m-d', $dateFilter);

        $query = DailyReport::with(['etudiant.user', 'user', 'stage'])
            ->where(function ($query) {
                $query->whereNotNull('etudiant_id')
                    ->orWhere(function ($query) {
                        $query->whereNull('etudiant_id')
                            ->where('user_id', '<>', auth()->id());
                    });
            });

        if ($period === 'weekly') {
            $startOfWeek = $filterDate->copy()->startOfWeek();
            $endOfWeek = $filterDate->copy()->endOfWeek();
            $query->whereBetween('report_date', [$startOfWeek, $endOfWeek]);
        } elseif ($period === 'monthly') {
            $startOfMonth = $filterDate->copy()->startOfMonth();
            $endOfMonth = $filterDate->copy()->endOfMonth();
            $query->whereBetween('report_date', [$startOfMonth, $endOfMonth]);
        } else {
            $query->whereDate('report_date', $filterDate);
        }

        $reports = $query->orderBy('report_date', 'desc')->get();

        $studentReports = $reports->filter(fn($report) => $report->etudiant_id !== null);
        $employeeReports = $reports->filter(fn($report) => $report->etudiant_id === null);

        $summary = [
            'total' => $reports->count(),
            'submitted' => $reports->where('status', 'submitted')->count(),
            'draft' => $reports->where('status', 'draft')->count(),
            'reviewed' => $reports->whereNotNull('reviewed_at')->count(),
        ];

        return view('admin.reports.index', compact(
            'period',
            'filterDate',
            'reports',
            'studentReports',
            'employeeReports',
            'summary'
        ));
    }

    public function show(Request $request, $id)
    {
        $report = DailyReport::with([
            'etudiant.user',
            'etudiant.personnel',
            'user.personnel',
            'user.domaine',
            'stage.supervisor.personnel',
            'stage.domaine',
            'stage.site',
            'reviews.reviewer',
            'reviews.reviewer.personnel',
            'task.assignees.personnel',
            'task.owner.personnel',
        ])->findOrFail(decrypt_route_param($id) ?? $id);

        // Vérification des permissions
        if (!auth()->user()->can('daily_reports.view')) {
            abort(403);
        }

        $authorUser = $report->user ?? $report->etudiant?->user;
        $authorEtudiant = $report->etudiant ?? $authorUser?->etudiant;

        // Récupérer toutes les tâches du stage ou associées à cet utilisateur/étudiant avec leurs rapports
        $relatedTasks = Task::with([
            'owner.personnel',
            'assignees.personnel',
            'stage',
            'dailyReports' => function ($q) {
                $q->with(['user.personnel', 'etudiant.personnel', 'reviews.reviewer.personnel'])
                  ->orderBy('report_date', 'desc')
                  ->orderBy('created_at', 'desc');
            },
        ])->where(function ($q) use ($report, $authorUser, $authorEtudiant) {
            if ($report->stage_id) {
                $q->where('stage_id', $report->stage_id);
            }
            if ($authorEtudiant) {
                $q->orWhere('etudiant_id', $authorEtudiant->id);
            }
            if ($authorUser) {
                $q->orWhere('owner_id', $authorUser->id)
                  ->orWhereHas('assignees', fn($aq) => $aq->where('users.id', $authorUser->id));
            }
        })->distinct()->get();

        // Pour le JSON (si appelé via AJAX)
        if ($request->wantsJson()) {
            return response()->json([
                'report' => [
                    'id'                    => $report->id,
                    'summary'               => $report->summary,
                    'introduction'          => $report->introduction,
                    'blockers'              => $report->blockers,
                    'next_steps'            => $report->next_steps,
                    'hours_declared'        => $report->hours_declared,
                    'status'                => $report->status,
                    'author_name'           => $report->user?->personnel?->prenom . ' ' . $report->user?->personnel?->nom ?? $report->user?->name ?? 'N/A',
                    'author_email'          => $report->user?->email ?? $report->etudiant?->user?->email ?? 'N/A',
                    'author_type'           => $report->etudiant_id ? 'etudiant' : 'employe',
                    'stage_theme'           => $report->stage?->theme,
                    'task_title'            => $report->task?->title,
                    'task_progress_percent' => $report->task_progress_percent,
                    'report_date_formatted' => $report->report_date->format('l j F Y'),
                    'created_at_formatted'  => $report->created_at->diffForHumans(),
                    'updated_at_formatted'  => $report->updated_at->diffForHumans(),
                    'created_at_full'       => $report->created_at->format('d/m/Y à H:i'),
                    'updated_at_full'       => $report->updated_at->format('d/m/Y à H:i'),
                    'latitude'              => $report->latitude,
                    'longitude'             => $report->longitude,
                    'accuracy_meters'       => $report->accuracy_meters,
                    'location_method'       => $report->location_method,
                ],
                'reviews' => $report->reviews->map(function ($review) {
                    return [
                        'id'            => $review->id,
                        'comment'       => $review->comment,
                        'reviewer_name' => $review->reviewer?->personnel?->prenom . ' ' . $review->reviewer?->personnel?->nom ?? $review->reviewer?->name ?? 'Utilisateur',
                        'reviewer_id'   => $review->reviewer_id,
                        'created_at'    => $review->created_at->diffForHumans(),
                        'created_at_full' => $review->created_at->format('d/m/Y à H:i'),
                        'action'        => $review->action,
                        'is_author'     => $review->reviewer_id === $report->user_id || $review->reviewer_id === $report->etudiant?->user?->id,
                    ];
                }),
                'can_send_bilan' => auth()->user()->can('admin.reports.send-bilan'),
            ]);
        }

        // Pour la vue HTML (page complète)
        return view('admin.reports.show', compact('report', 'relatedTasks'));
    }

    /**
     * Téléchargement du détail complet du rapport en PDF
     */
    public function downloadPdf(Request $request, $id)
    {
        if (!auth()->user()->can('daily_reports.view')) {
            abort(403);
        }

        $report = DailyReport::with([
            'etudiant.user',
            'etudiant.personnel',
            'user.personnel',
            'user.domaine',
            'stage.supervisor.personnel',
            'stage.domaine',
            'stage.site',
            'stage.etudiant',
            'reviews.reviewer.personnel',
            'task.owner.personnel',
            'task.assignees.personnel',
            'task.stage',
        ])->findOrFail(decrypt_route_param($id) ?? $id);

        $authorUser = $report->user ?? $report->etudiant?->user;
        $authorEtudiant = $report->etudiant ?? $authorUser?->etudiant;

        // Récupérer toutes les tâches du stage ou associées avec tous leurs rapports
        $relatedTasks = Task::with([
            'owner.personnel',
            'assignees.personnel',
            'stage',
            'dailyReports' => function ($q) {
                $q->with(['user.personnel', 'etudiant.personnel', 'reviews.reviewer.personnel'])
                  ->orderBy('report_date', 'desc')
                  ->orderBy('created_at', 'desc');
            },
        ])->where(function ($q) use ($report, $authorUser, $authorEtudiant) {
            if ($report->stage_id) {
                $q->where('stage_id', $report->stage_id);
            }
            if ($authorEtudiant) {
                $q->orWhere('etudiant_id', $authorEtudiant->id);
            }
            if ($authorUser) {
                $q->orWhere('owner_id', $authorUser->id)
                  ->orWhereHas('assignees', fn($aq) => $aq->where('users.id', $authorUser->id));
            }
        })->distinct()->get();

        $logoPath    = public_path('images/TFGLOGO.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : '';
        $isPdf       = true;

        $pdf = Pdf::loadView('admin.reports.report_pdf', compact('report', 'relatedTasks', 'logoDataUri', 'isPdf'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        $authorName = Str::slug($report->user?->personnel?->nom ?? $report->user?->name ?? 'rapport');
        $dateStr    = $report->report_date->format('Y-m-d');
        $fileName   = 'rapport_' . $dateStr . '_' . $authorName . '_' . $report->id . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Tableau complet de TOUS les rapports (étudiants + employés confondus),
     * avec le nom de l'utilisateur et sa description, paginé et filtrable.
     */
    public function all(Request $request)
    {
        $period     = $request->get('period', 'all');
        $dateFilter = $request->get('date', now()->format('Y-m-d'));
        $filterDate = Carbon::createFromFormat('Y-m-d', $dateFilter);
        $search     = trim($request->get('q', ''));

        $query = DailyReport::with(['etudiant.user', 'user', 'stage']);

        if ($period === 'weekly') {
            $query->whereBetween('report_date', [
                $filterDate->copy()->startOfWeek(),
                $filterDate->copy()->endOfWeek(),
            ]);
        } elseif ($period === 'monthly') {
            $query->whereBetween('report_date', [
                $filterDate->copy()->startOfMonth(),
                $filterDate->copy()->endOfMonth(),
            ]);
        } elseif ($period === 'daily') {
            $query->whereDate('report_date', $filterDate);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('etudiant.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('introduction', 'like', "%{$search}%");
            });
        }

        $summary = [
            'total'     => (clone $query)->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'draft'     => (clone $query)->where('status', 'draft')->count(),
            'reviewed'  => (clone $query)->whereNotNull('reviewed_at')->count(),
        ];

        $reports = $query->orderBy('report_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.all', compact('period', 'filterDate', 'search', 'reports', 'summary'));
    }

    /**
     * Envoi du bilan hebdomadaire de présence à l'UTILISATEUR d'un rapport précis
     * (action "Envoyer" sur chaque ligne du tableau « Tous les rapports »).
     *
     * La période couverte est la semaine du rapport : taux de présence, absences,
     * retards, heures travaillées et nombre de rapports soumis. Un envoi est
     * bloqué (dédup) si un bilan a déjà été envoyé à cet utilisateur pour la
     * même semaine.
     */
    public function sendWeeklyBilan(Request $request, $id)
    {
        $report = DailyReport::with(['etudiant.user', 'user', 'etudiant'])
            ->findOrFail($id);

        $user = $report->etudiant?->user ?? $report->user;

        if (!$user) {
            return back()->with('error', 'Impossible de retrouver l\'utilisateur associé à ce rapport.');
        }

        $weekStart = $report->report_date->copy()->startOfWeek();
        $weekEnd   = $report->report_date->copy()->endOfWeek();
        $start     = $weekStart->copy()->startOfDay();
        $end       = $weekEnd->copy()->endOfDay();
        $weekLabel = $weekStart->format('d/m/Y') . ' au ' . $weekEnd->format('d/m/Y');

        $alreadySent = WeeklyBilanSend::where('user_id', $user->id)
            ->where('period_start', $weekStart->toDateString())
            ->exists();

        if ($alreadySent) {
            return back()->with('error', "Le bilan hebdomadaire ($weekLabel) a déjà été envoyé à {$user->name}.");
        }

        try {
            $stats = app(AdminPresenceService::class)->getUserDetailedStats(
                $user->id,
                'week',
                $start->toDateString(),
                $end->toDateString()
            );

            $reportsCount = DailyReport::whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if ($user->etudiant) {
                        $q->orWhere('etudiant_id', $user->etudiant->id);
                    }
                })
                ->count();

            $email = $user->getEmailForVerification();
            if (!$email) {
                return back()->with('error', "Aucune adresse email valide pour {$user->name}.");
            }

            Mail::to($email)->send(new BilanHebdomadaireMail(
                $user,
                $stats,
                $reportsCount,
                $weekStart->copy(),
                $weekEnd->copy()
            ));

            WeeklyBilanSend::create([
                'user_id'      => $user->id,
                'period_start' => $weekStart->toDateString(),
                'period_end'   => $weekEnd->toDateString(),
                'sent_at'      => now(),
            ]);

            return back()->with('success', "Bilan hebdomadaire ($weekLabel) envoyé à {$user->name}.");
        } catch (\Throwable $e) {
            Log::error('bilan_hebdomadaire.send_failed', [
                'user_id' => $user->id,
                'report_id' => $report->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with('error', 'L\'envoi du bilan à ' . $user->name . ' a échoué. Consultez les logs.');
        }
    }

    /**
     * Répondre à un rapport. Si le rapport est rattaché à une tâche, la réponse
     * alimente le fil de discussion de la tâche (source unique) ; sinon on crée
     * une review classique. Notifie l'auteur du rapport.
     */
    public function respond(Request $request, NotificationService $notifications)
    {
        $data = $request->validate([
            'report_id' => 'required|integer|exists:daily_reports,id',
            'comment'   => 'required|string|max:5000',
        ]);

        $user = $request->user();
        $report = DailyReport::with(['task', 'etudiant.user'])->findOrFail($data['report_id']);

        DailyReportReview::create([
            'daily_report_id' => $report->id,
            'reviewer_id'     => $user->id,
            'action'          => 'comment',
            'comment'         => $data['comment'],
            'reviewed_at'     => now(),
        ]);
        $url = $report->task ? encrypted_route('tasks.show', $report->task) : route('admin.reports.index');

        $report->forceFill([
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'status'      => $report->status === 'draft' ? $report->status : 'reviewed',
        ])->save();

        // Notifier l'auteur (étudiant ou employé).
        $authorUserId = $report->etudiant?->user?->id ?? $report->user_id;
        if ($authorUserId && (int) $authorUserId !== (int) $user->id) {
            $notifications->push(
                (int) $authorUserId,
                'report_response',
                '💬 Réponse à votre rapport',
                $user->name . ' : ' . Str::limit($data['comment'], 60),
                $url,
                'chat',
                'indigo'
            );
        }

        // La page de suivi poste en AJAX et attend du JSON.
        return response()->json(['success' => true, 'message' => 'Réponse envoyée.']);
    }
}
