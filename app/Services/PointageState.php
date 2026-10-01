<?php

namespace App\Services;

use App\Models\AttendanceDay;
use App\Models\Site;
use App\Models\Stage;
use App\Models\User;

/**
 * L'état du jour tel que la carte de pointage doit l'afficher : heures
 * attendues, journée en cours, retard, départ encore bloqué.
 *
 * Existe parce que le scan du QR code aboutit désormais sur la même carte que
 * l'application : deux entrées, un seul calcul, sinon les deux écrans finissent
 * par ne plus dire la même chose.
 */
class PointageState
{
    public function __construct(
        protected WorkScheduleResolver $schedule,
        protected PresenceService $presence,
    ) {}

    /**
     * @return array{stage: ?Stage, day: ?AttendanceDay, expIn: \Carbon\Carbon,
     *               expOut: \Carbon\Carbon, etat: string, late: bool,
     *               departBloque: bool, isWorkDay: bool, workDaysLabel: ?string,
     *               journeeOubliee: ?AttendanceDay}
     */
    public function forUser(User $user, ?Site $site = null): array
    {
        $stage = $this->activeStage($user, $site);

        $day = $stage
            ? AttendanceDay::where('stage_id', $stage->id)->whereDate('attendance_date', today())->first()
            : AttendanceDay::where('user_id', $user->id)->whereDate('attendance_date', today())->first();

        $expIn  = $this->schedule->expectedArrival($stage, now());
        $expOut = $this->schedule->expectedDeparture($stage, now());

        $arrive = (bool) $day?->first_check_in_at;
        $parti  = (bool) $day?->last_check_out_at;

        $etat = !$arrive ? 'arrivee' : (!$parti ? 'depart' : 'termine');

        // Le départ reste bloqué avant l'heure, sauf permission approuvée
        // portant la date du jour : c'est le même verrou que le serveur.
        $peutPartir = now()->greaterThanOrEqualTo($expOut)
            || $this->presence->approvedEarlyDepartureForToday($user) !== null;

        // Deux questions distinctes, une seule source de vérité côté serveur.
        //
        // « Faut-il déclarer un départ oublié ? » vaut dès le lendemain : c'est
        // l'invitation à régulariser, et elle ne doit dépendre d'aucune heure.
        //
        // « L'arrivée est-elle bloquée ? » ne vaut qu'une fois le délai de grâce
        // écoulé. Tant qu'il reste, l'arrivée passe et la personne garde une
        // journée entière pour faire la déclaration elle-même.
        $journeeOubliee    = $this->presence->forgottenDepartureToDeclare($user, $stage);
        $journeeBloqueuse = $this->presence->unresolvedForgottenDeparture($user, $stage);
        $arriveeBloquee   = !$arrive && $journeeBloqueuse !== null;

        // Un rapport de travail soumis du jour est requis avant le départ.
        $rapportSoumis = $this->reportSubmittedToday($user, $stage);
        $departBloqueRapport = $etat === 'depart' && !$rapportSoumis;

        return [
            'stage'         => $stage,
            'day'           => $day,
            'expIn'         => $expIn,
            'expOut'        => $expOut,
            'etat'          => $etat,
            'late'          => !$arrive && now()->greaterThan($expIn),
            'departBloque'  => $etat === 'depart' && !$peutPartir,
            'arriveeBloquee'=> $arriveeBloquee,
            'journeeBloqueuse' => $arriveeBloquee ? $journeeBloqueuse : null,
            'rapportSoumis' => $rapportSoumis,
            'departBloqueRapport' => $departBloqueRapport,
            'isWorkDay'     => $stage ? $stage->isWorkDay() : true,
            'workDaysLabel' => $stage?->workDaysLabel(),
            // On ne demande l'heure du départ oublié qu'une fois l'arrivée du
            // jour pointée : la question de la veille ne doit pas retarder le
            // geste d'aujourd'hui.
            'journeeOubliee' => $arrive ? $journeeOubliee : null,
        ];
    }

    /**
     * Un rapport de travail soumis existe-t-il pour aujourd'hui ?
     */
    protected function reportSubmittedToday(User $user, ?Stage $stage): bool
    {
        $query = \App\Models\DailyReport::whereDate('report_date', today());

        if ($stage) {
            $query->where('stage_id', $stage->id);
        } else {
            $query->where('user_id', $user->id);
        }

        return (clone $query)->where('status', 'submitted')->exists();
    }

    /**
     * La dernière journée clôturée d'office sans que l'intéressé ait dit à
     * quelle heure il était parti. Une seule à la fois : on règle la plus
     * ancienne avant de passer à la suivante.
     *
     * Délégué à PresenceService : c'est là que vivent les critères « non
     * réglée » (déclarée par l'intéressé ou corrigée par un responsable).
     * Les duplications ont déjà divergé une fois, et l'écran annonçait un
     * départ oublié que le serveur, lui, ne voyait plus.
     */
    public function journeeOubliee(User $user, ?Stage $stage): ?AttendanceDay
    {
        return $this->presence->forgottenDepartureToDeclare($user, $stage);
    }

    /**
     * Le stage du jour. Quand un site est connu — le scan vient d'une porte —
     * on privilégie le stage qui s'y déroule, sans exclure les stages sans site.
     */
    protected function activeStage(User $user, ?Site $site): ?Stage
    {
        if (!$user->etudiant) {
            return null;
        }

        $duJour = fn () => $user->etudiant->stages()
            ->whereDate('date_debut', '<=', today())
            ->whereDate('date_fin', '>=', today())
            ->with('site', 'typestage');

        if ($site) {
            $surPlace = $duJour()
                ->where(fn ($q) => $q->where('site_id', $site->id)->orWhereNull('site_id'))
                ->orderByDesc('date_debut')
                ->first();

            if ($surPlace) {
                return $surPlace;
            }
        }

        return $duJour()->orderByDesc('date_debut')->first();
    }
}
