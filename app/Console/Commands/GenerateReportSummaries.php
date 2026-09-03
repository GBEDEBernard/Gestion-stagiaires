<?php

namespace App\Console\Commands;

use App\Services\AiSummaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateReportSummaries extends Command
{
    protected $signature = 'summaries:generate
        {period : weekly, monthly, or yearly}
        {--force : Regenerate even if already exists}
        {--user= : Generate for a specific user ID only}';

    protected $description = 'Generate AI summaries of daily reports for a given period';

    public function handle(AiSummaryService $service): int
    {
        $period = $this->argument('period');
        $force = $this->option('force');
        $userId = $this->option('user');

        $periods = [
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            'yearly' => [now()->startOfYear(), now()->endOfYear()],
        ];

        if (!isset($periods[$period])) {
            $this->error("Période invalide. Utilise: weekly, monthly, ou yearly.");
            return 1;
        }

        [$start, $end] = $periods[$period];

        $label = [
            'weekly' => 'semaine',
            'monthly' => 'mois',
            'yearly' => 'année',
        ];

        $this->info("Génération des résumés {$label[$period]} du {$start->toDateString()} au {$end->toDateString()}...");

        if ($userId) {
            $user = \App\Models\User::find($userId);
            if (!$user) {
                $this->error("Utilisateur #{$userId} introuvable.");
                return 1;
            }

            $this->line("  → {$user->name}...");
            $summary = $service->generateForUser($user, $period, $start, $end, $force);
            $this->info("  ✅ Résumé #{$summary->id} généré.");
        } else {
            $results = $service->generateForAllUsers($period, $start, $end, $force);

            $success = collect($results)->where('status', 'success')->count();
            $errors = collect($results)->where('status', 'error');

            $this->info("✅ {$success} résumé(s) généré(s).");

            foreach ($errors as $result) {
                $this->warn("  ⚠️  {$result['user']} : {$result['error']}");
            }
        }

        return 0;
    }
}
