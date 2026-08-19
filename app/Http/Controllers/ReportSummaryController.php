<?php

namespace App\Http\Controllers;

use App\Models\ReportSummary;
use App\Models\User;
use App\Services\AiSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportSummaryController extends Controller
{
    public function __construct(
        protected AiSummaryService $aiSummary
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $summaries = ReportSummary::where('user_id', $user->id)
            ->orderBy('period_end', 'desc')
            ->get();

        return view('summaries.index', compact('summaries'));
    }

    public function show(ReportSummary $summary)
    {
        $this->authorize('view', $summary);

        return view('summaries.show', compact('summary'));
    }

    public function generate(Request $request)
    {
        $user = $request->user();
        $period = $request->input('period', 'weekly');

        $periods = [
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            'yearly' => [now()->startOfYear(), now()->endOfYear()],
        ];

        if (!isset($periods[$period])) {
            return back()->with('error', 'Période invalide.');
        }

        [$start, $end] = $periods[$period];

        try {
            $summary = $this->aiSummary->generateForUser($user, $period, $start, $end, true);
            return redirect()->route('summaries.show', $summary)
                ->with('success', 'Résumé généré avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function adminIndex(Request $request)
    {
        $query = ReportSummary::with('user')
            ->orderBy('created_at', 'desc');

        $period = $request->input('period');
        $userId = $request->input('user_id');

        if ($period) {
            $query->where('period_type', $period);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $summaries = $query->paginate(20);
        $users = User::orderBy('name')->get();

        return view('admin.summaries.index', compact('summaries', 'users'));
    }

    public function destroy(ReportSummary $summary)
    {
        $this->authorize('view', $summary);

        $summary->delete();

        return redirect()->route('summaries.index')
            ->with('success', 'Résumé supprimé avec succès.');
    }
}
