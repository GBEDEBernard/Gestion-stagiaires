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
}
