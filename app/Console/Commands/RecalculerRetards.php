<?php

namespace App\Console\Commands;

use App\Models\AttendanceDay;
use App\Models\Stage;
use App\Services\WorkScheduleResolver;
use Illuminate\Console\Command;

/**
 * Requalifie les retards enregistrés sous l'ancienne règle du 08:00 en dur.
 *
 * arrival_status et late_minutes sont des colonnes stockées : contrairement aux
 * ratios du rapport, qui se recalculent à la lecture, elles gardent le verdict
 * rendu au moment du pointage. Un stage commençant à 08:30 traîne donc des
 * retards qui n'en étaient pas.
 */
class RecalculerRetards extends Command
{
    protected $signature = 'presence:recalculer-retards
                            {--dry-run : Affiche ce qui changerait sans rien écrire}
                            {--stage= : Limiter à un stage (id)}';

    protected $description = "Requalifie les retards passés selon l'horaire réel du stage";

    public function handle(WorkScheduleResolver $resolver): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $query = AttendanceDay::whereNotNull('first_check_in_at');

        if ($stageId = $this->option('stage')) {
            $query->where('stage_id', $stageId);
        }

        $jours = $query->orderBy('attendance_date')->get();

        $this->info("Journées pointées à examiner : {$jours->count()}");

        $stages     = Stage::with('jours')->get()->keyBy('id');
        $lignes     = [];
        $corrigees  = 0;

        foreach ($jours as $jour) {
            $stage   = $jour->stage_id ? $stages->get($jour->stage_id) : null;
            $attendu = $resolver->expectedArrival($stage, $jour->first_check_in_at);

            $enRetard = $jour->first_check_in_at->greaterThan($attendu);
            // diffInMinutes rend un flottant : sans la conversion, la comparaison
            // au champ stocké échoue toujours et tout paraît à requalifier.
            $minutes  = $enRetard ? (int) $attendu->diffInMinutes($jour->first_check_in_at) : 0;
            $statut   = $enRetard ? 'late' : 'ontime';

            if ($statut === $jour->arrival_status && $minutes === (int) $jour->late_minutes) {
                continue;
            }

            $lignes[] = [
                $jour->attendance_date instanceof \DateTimeInterface
                    ? $jour->attendance_date->format('d/m/Y')
                    : (string) $jour->attendance_date,
                $jour->first_check_in_at->format('H:i'),
                $attendu->format('H:i'),
                ($jour->arrival_status ?? '—') . ' ' . (int) $jour->late_minutes . ' min',
                $statut . ' ' . $minutes . ' min',
            ];

            $corrigees++;

            if (!$dryRun) {
                $jour->update(['arrival_status' => $statut, 'late_minutes' => $minutes]);
            }
        }

        if ($lignes) {
            $this->table(['Date', 'Arrivée', 'Attendue', 'Avant', 'Après'], array_slice($lignes, 0, 30));

            if (count($lignes) > 30) {
                $this->line('… et ' . (count($lignes) - 30) . ' autre(s).');
            }
        }

        $this->newLine();

        if ($dryRun) {
            $this->warn("Simulation : {$corrigees} journée(s) seraient requalifiée(s). Relancez sans --dry-run pour appliquer.");
            return self::SUCCESS;
        }

        $this->info("Journées requalifiées : {$corrigees}");

        if ($corrigees > 0) {
            $this->line("La ponctualité des rapports concernés change en conséquence.");
            $this->line("Les évaluations déjà finalisées gardent leur note : leur instantané est figé.");
        }

        return self::SUCCESS;
    }
}
