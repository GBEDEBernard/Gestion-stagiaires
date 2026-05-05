<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDay;
use App\Models\Stage;
use App\Services\DailyReportService;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperviseurDashboardController extends Controller
{
    public function __construct(
        protected DailyReportService $dailyReportService,
        protected PresenceService $presenceService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasRole('superviseur'), 403);

        $supervisedStages = $user->supervisedStages()
            ->with(['etudiant.user', 'site', 'attendanceDays' => function ($query) {
                $query->whereDate('attendance_date', today());
            }, 'dailyReports' => function ($query) {
                $query->whereDate('report_date', today())->latest();
            }])
            ->active() // assume scope for active stages
            ->get();

        $todayAttendanceDays = AttendanceDay::whereIn('stage_id', $supervisedStages->pluck('id'))
            ->whereDate('attendance_date', today())
            ->get();

        $pendingReviews = $supervisedStages->flatMap->dailyReports->filter(fn($r) => $r->status === 'submitted');

        return view('superviseur.dashboard', [
            'supervisedStages' => $supervisedStages,
            'todayAttendanceDays' => $todayAttendanceDays,
            'pendingReviews' => $pendingReviews,
        ]);
    }
}
