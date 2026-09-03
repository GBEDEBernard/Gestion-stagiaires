<?php

namespace App\Services;

use App\Models\Stage;
use App\Models\WorkScheduleSetting;
use Carbon\Carbon;

/**
 * Résout l'horaire applicable à une journée donnée.
 *
 * Deux niveaux : l'horaire du stage, puis l'horaire de référence de
 * l'entreprise pour qui n'en déclare pas.
 *
 * Un horaire par jour de la semaine a existé puis été retiré : un stage garde
 * le même horaire tous ses jours. Une demi-journée se règle sur le stage
 * lui-même (08:00–12:30), pas jour par jour. Les colonnes correspondantes
 * subsistent sur stage_jour mais ne sont plus lues.
 *
 * Aucune tolérance : l'heure attendue est l'heure exacte. Une minute après,
 * c'est un retard.
 *
 * Toute la logique de retard et de départ anticipé passe par ici : c'est le
 * seul endroit qui décide à quelle heure quelqu'un est attendu.
 */
class WorkScheduleResolver
{
    /** Réglage de l'entreprise, lu une seule fois par requête. */
    private ?array $defaults = null;

    /**
     * @return array{start: string, end: string, break_minutes: int}
     */
    public function forStage(?Stage $stage, $date = null): array
    {
        $defaults = $this->companyDefaults();

        if (!$stage) {
            return $this->normalise($defaults);
        }

        $schedule = [
            'start'         => $stage->expected_check_in_time ?: $defaults['start'],
            'end'           => $stage->expected_check_out_time ?: $defaults['end'],
            'break_minutes' => $stage->break_minutes ?? $defaults['break_minutes'],
        ];

        return $this->normalise($schedule);
    }

    /** Heure d'arrivée attendue pour la journée de l'instant donné. */
    public function expectedArrival(?Stage $stage, Carbon $occurredAt): Carbon
    {
        return $occurredAt->copy()
            ->setTimeFromTimeString($this->forStage($stage, $occurredAt)['start']);
    }

    /** Heure de départ attendue pour la journée de l'instant donné. */
    public function expectedDeparture(?Stage $stage, Carbon $occurredAt): Carbon
    {
        return $occurredAt->copy()
            ->setTimeFromTimeString($this->forStage($stage, $occurredAt)['end']);
    }

    /**
     * Minutes de travail effectif : présence moins la pause.
     * La pause n'est retirée que si la présence la dépasse, sinon une journée
     * écourtée tomberait à zéro et effacerait le temps réellement passé.
     */
    public function workedMinutes(?Stage $stage, Carbon $checkIn, Carbon $checkOut): int
    {
        $presence = max(0, $checkIn->diffInMinutes($checkOut));
        $break    = $this->forStage($stage, $checkIn)['break_minutes'];

        return $presence > $break ? $presence - $break : $presence;
    }

    /**
     * Horaire de référence de l'entreprise, depuis la base.
     * Mémorisé sur l'instance : le résolveur est appelé plusieurs fois par
     * pointage, une requête suffit.
     */
    public function companyDefaults(): array
    {
        if ($this->defaults !== null) {
            return $this->defaults;
        }

        $setting = WorkScheduleSetting::current();

        return $this->defaults = [
            'start'         => $setting->start_time,
            'end'           => $setting->end_time,
            'break_minutes' => (int) $setting->break_minutes,
        ];
    }

    private function normalise(array $schedule): array
    {
        return [
            'start'         => substr((string) $schedule['start'], 0, 5),
            'end'           => substr((string) $schedule['end'], 0, 5),
            'break_minutes' => (int) $schedule['break_minutes'],
        ];
    }
}
