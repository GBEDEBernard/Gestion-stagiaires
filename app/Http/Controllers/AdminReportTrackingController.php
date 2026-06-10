<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportReview;
use App\Models\TaskMessage;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $report = DailyReport::with(['etudiant.user', 'user', 'stage', 'reviews.reviewer'])
            ->findOrFail($id);

        $authorName = $report->etudiant?->user?->name ?? $report->user?->name ?? 'N/A';

        return response()->json([
            'report' => [
                'id'                    => $report->id,
                'summary'               => $report->summary,
                'blockers'              => $report->blockers,
                'next_steps'            => $report->next_steps,
                'hours_declared'        => $report->hours_declared,
                'status'                => $report->status,
                'author_name'           => $authorName,
                'stage_theme'           => $report->stage?->theme,
                'report_date_formatted' => $report->report_date->format('l j F Y'),
                'created_at_formatted'  => $report->created_at->diffForHumans(),
                'updated_at_formatted'  => $report->updated_at->diffForHumans(),
            ],
            'reviews' => $report->reviews->map(function ($review) {
                return [
                    'id'            => $review->id,
                    'comment'       => $review->comment,
                    'reviewer_name' => $review->reviewer->name,
                    'created_at'    => $review->created_at->diffForHumans(),
                    'action'        => $review->action,
                ];
            }),
        ]);
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
