<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminTaskTrackingController extends Controller
{
    /**
     * Suivi des tâches (admin / superviseur) par jour / semaine / mois.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 'monthly');
        $status = $request->get('status');
        $q = $request->get('q');
        $date = $request->filled('date') ? Carbon::parse($request->get('date')) : now();

        [$from, $to] = match ($period) {
            'daily'  => [$date->copy()->startOfDay(), $date->copy()->endOfDay()],
            'weekly' => [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()],
            default  => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
        };

        $tasks = Task::with(['owner', 'stage.etudiant'])
            ->withCount(['messages', 'dailyReports'])
            ->visibleTo($user)
            ->whereBetween('updated_at', [$from, $to])
            ->when(in_array($status, Task::STATUSES, true), fn($qb) => $qb->where('status', $status))
            ->when($q, fn($qb) => $qb->where('title', 'like', "%{$q}%"))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $base = Task::query()->visibleTo($user)->whereBetween('updated_at', [$from, $to]);
        $stats = [
            'total'       => (clone $base)->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'completed'   => (clone $base)->where('status', 'completed')->count(),
            'overdue'     => (clone $base)->whereNotNull('due_date')
                ->where('status', '!=', 'completed')
                ->whereDate('due_date', '<', now())
                ->count(),
            'avg'         => (int) round((clone $base)->avg('last_progress_percent') ?? 0),
        ];

        return view('admin.tasks.tracking', [
            'tasks'   => $tasks,
            'stats'   => $stats,
            'period'  => $period,
            'status'  => $status,
            'date'    => $date,
        ]);
    }
}
