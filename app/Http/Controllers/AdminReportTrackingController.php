<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportReview;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminReportTrackingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if($user->hasRole('etudiant'), 403);

        $period = $request->get('period', 'daily');
        $dateFilter = $request->get('date', now()->format('Y-m-d'));
        $filterDate = Carbon::createFromFormat('Y-m-d', $dateFilter);

        $query = DailyReport::query()
            ->visibleTo($user)
            ->with(['etudiant.user', 'user', 'stage.service', 'reviews.reviewer']);

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
        } else {
            $query->whereDate('report_date', $filterDate);
        }

        $reports = $query->orderByDesc('report_date')->get();

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
            'summary',
            'user'
        ));
    }

    public function review(Request $request, DailyReport $report)
    {
        $user = $request->user();

        abort_if($user->hasRole('etudiant'), 403);
        abort_unless($user->can('daily_reports.review') || $user->can('daily_reports.approve'), 403);
        abort_unless(DailyReport::query()->visibleTo($user)->whereKey($report->id)->exists(), 403);

        $validated = $request->validate([
            'action' => 'required|in:approved,rejected,changes_requested',
            'comment' => 'nullable|string|max:5000',
        ]);

        $report->update([
            'status' => $validated['action'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'supervisor_comment' => $validated['comment'] ?? null,
        ]);

        DailyReportReview::create([
            'daily_report_id' => $report->id,
            'reviewer_id' => $user->id,
            'action' => $validated['action'],
            'comment' => $validated['comment'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Rapport revu avec succes.');
    }
}
