<?php

namespace App\Console\Commands;

use App\Models\PermissionRequest;
use App\Services\PermissionAttendanceSync;
use Illuminate\Console\Command;

/**
 * Rattrape l'historique : toutes les permissions approuvées avant la mise en
 * place du pont permission → présence sont encore comptées comme des absences.
 */
class SyncPermissionAttendance extends Command
{
    protected $signature = 'permissions:sync-attendance
                            {--dry-run : Affiche ce qui serait fait sans rien écrire}
                            {--user= : Limiter à un utilisateur (id)}';

    protected $description = "Génère les jours excusés manquants pour les permissions déjà approuvées";

    public function handle(PermissionAttendanceSync $sync): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $query = PermissionRequest::with('type', 'user.etudiant')
            ->where('status', 'approved');

        if ($userId = $this->option('user')) {
            $query->where('user_id', $userId);
        }

        $requests = $query->orderBy('id')->get();

        // Seules les permissions qui excusent des journées entières nous intéressent :
        // un retard ou un départ anticipé ne retire pas la journée du décompte.
        $eligible = $requests->filter(fn($r) => $r->type?->attendance_effect === 'excuses_day');

        $this->info("Permissions approuvées : {$requests->count()}");
        $this->info("Dont journées entières : {$eligible->count()}");

        if ($eligible->isEmpty()) {
            $this->line('Rien à faire.');
            return self::SUCCESS;
        }

        $totals = ['created' => 0, 'skipped_present' => 0, 'skipped_existing' => 0, 'skipped_weekend' => 0, 'skipped_holiday' => 0];
        $rows   = [];

        foreach ($eligible as $request) {
            if ($dryRun) {
                $range = $sync->resolveDateRange($request);
                $rows[] = [
                    $request->id,
                    $request->user->name ?? "#{$request->user_id}",
                    $request->type->name,
                    $range ? $range[0]->toDateString() . ' → ' . $range[1]->toDateString() : 'dates illisibles',
                    $range ? ($range[0]->diffInDays($range[1]) + 1) . ' j' : '—',
                ];
                continue;
            }

            $report = $sync->sync($request);
            foreach ($totals as $key => $_) {
                $totals[$key] += $report[$key];
            }

            if ($report['created'] > 0) {
                $rows[] = [
                    $request->id,
                    $request->user->name ?? "#{$request->user_id}",
                    $request->type->name,
                    $report['created'] . ' jour(s) excusé(s)',
                    '',
                ];
            }
        }

        if ($rows) {
            $this->table(['#', 'Utilisateur', 'Type', 'Effet', 'Durée'], $rows);
        }

        if ($dryRun) {
            $this->warn('Simulation : aucune écriture effectuée. Relancez sans --dry-run pour appliquer.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("Jours excusés créés     : {$totals['created']}");
        $this->line("Ignorés — déjà présent  : {$totals['skipped_present']}");
        $this->line("Ignorés — déjà excusé   : {$totals['skipped_existing']}");
        $this->line("Ignorés — week-end      : {$totals['skipped_weekend']}");
        $this->line("Ignorés — jour férié    : {$totals['skipped_holiday']}");

        return self::SUCCESS;
    }
}
