<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
