<?php

namespace App\Console\Commands;

use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\Stage;
use App\Models\User;
use App\Services\WorkScheduleResolver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Prépare un cas de test du départ oublié : une arrivée pointée, aucun départ.
 *
 * Cette commande écrit des journées à une date passée, ce que le service de
 * pointage refuse de faire (il travaille sur now()). Elle passe donc par les
 * modèles, mais elle ne réinvente aucune règle métier : la clôture d'office est
 * ensuite confiée à attendance:auto-checkout, dont c'est le rôle. Le scénario
 * obtenu est exactement celui du cron de 00h02, ce qui permet de tester le
 * message sans attendre la nuit.
 *
 * La commande est idempotente : relancer sur une journée déjà pointée ne
 * duplique rien, elle prévient et passe.
 */
class DemoJourneesOubliees extends Command
{
    protected $signature = 'attendance:demo-depart-oublie
        {--date= : Date des journées à ouvrir (défaut : hier)}
        {--all : Traiter tous les Moist pointeurs, pas seulement les accounts de démonstration}
        {--close : Enchaîner immédiatement sur la clôture d\'office}';

    protected $description = "Crée une journée avec arrivée mais sans départ, pour tester le message de départ oublié";

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : today()->subDay();

        if ($date->greaterThanOrEqualTo(today())) {
            $this->error('La date doit être dans le passé : un départ oublié suppose une journée déjà terminée.');
            return Command::FAILURE;
        }

        $users = $this->cibles();

        if ($users->isEmpty()) {
            $this->error('Aucun compte trouvé. Relancez avec --all pour traiter tous les pointeurs.');
            return Command::FAILURE;
        }

        $this->line("Journée du {$date->format('d/m/Y')} — arrivée pointée, départ oublié.");
        $this->newLine();

        $creees = 0;

        foreach ($users as $user) {
            $journee = $this->journeeOuverte($user, $date);

            if ($journee === AttendanceDay::class) {
                $this->line("  <fg=gray>—</> {$this->libelle($user)} : journée déjà pointée, laissée en place.");
                continue;
            }

            $creees++;
            $this->line("  <fg=green>✓</> {$this->libelle($user)} : arrivée à {$journee->first_check_in_at->format('H:i')}, aucun départ.");
        }

        $this->newLine();
        $this->info("{$creees} journée(s) ouverte(s) sans départ.");

        if (! $this->option('close')) {
            $this->line('Pour déclencher le message maintenant, relancez avec <info>--close</info>.');
            return Command::SUCCESS;
        }

        // La clôture n'est pas réimplémentée ici : on appelle la commande qui
        // fait déjà cela, pour qu'un changement de règle soit appliqué partout
        // en même temps.
        $this->newLine();

        $code = $this->call('attendance:auto-checkout', [
            '--date' => $date->toDateString(),
        ]);

        return $code;
    }

    /**
     * Les comptes à préparer. Par défaut, un représentant de chaque profil
     * qui a un accès au pointage : un stagiaire, un employé, un admin qui
     * pointe comme employé. Les profils sont choisis par rôle, pas par nom :
     * un compte renommé ne doit pas disparaître du scénario.
     */
    protected function cibles()
    {
        $query = User::query()->where('status', 'actif');

        if ($this->option('all')) {
            return $query->get()->filter(fn (User $u) => $u->peutPointer());
        }

        return collect([
            $this->premierRole('etudiant'),
            $this->premierRole('employe'),
            $this->premierAdminEmploye(),
        ])->filter();
    }

    protected function premierRole(string $role): ?User
    {
        return User::where('status', 'actif')
            ->whereHas('roles', fn ($q) => $q->where('name', $role))
            ->orderBy('id')
            ->first();
    }

    /**
     * Un administrateur qui pointe aussi comme employé.
     *
     * C'est un cas à part entière : il cumule les droits de gestion et le
     * pointage, et c'est précisément le profil qui sert de contre-exemple au
     * blocage quand on touche aux rôles.
     */
    protected function premierAdminEmploye(): ?User
    {
        return User::where('status', 'actif')
            ->whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->whereHas('roles', fn ($q) => $q->where('name', 'employe'))
            ->orderBy('id')
            ->first();
    }

    /**
     * Une journée ouverte pour ce compte : l'événement d'arrivée et la ligne
     * de présence qui va avec.
     */
    protected function journeeOuverte(User $user, Carbon $date): AttendanceDay|string
    {
        // Un employé n'a pas de stage : sa journée se rattache à lui seul. Le
        // stage n'est donc pas une condition d'existence, seulement la source
        // du rattachement quand la personne est stagiaire.
        $stage = $this->stageActif($user, $date);

        $dejaLa = AttendanceDay::query()
            ->when($stage, fn ($q) => $q->where('stage_id', $stage->id), fn ($q) => $q->where('user_id', $user->id))
            ->whereDate('attendance_date', $date->toDateString())
            ->exists();

        if ($dejaLa) {
            return AttendanceDay::class;
        }

        $resolver = app(WorkScheduleResolver::class);
        $expIn = $resolver->expectedArrival($stage, $date);

        return DB::transaction(function () use ($user, $stage, $date, $expIn) {
            $stageId = $stage?->id;
            $etudiantId = $stage?->etudiant_id ?? $user->etudiant?->id;

            $event = AttendanceEvent::create([
                'stage_id'     => $stageId,
                'etudiant_id'  => $etudiantId,
                'site_id'      => $stage?->site_id,
                'user_id'      => $user->id,
                'event_type'   => 'check_in',
                'status'       => 'approved',
                'occurred_at'  => $expIn,
                'reason_code'  => 'demo_depart_oublie',
                'meta'         => [
                    'demo' => true,
                    'demo_depart_oublie' => true,
                ],
            ]);

            return AttendanceDay::create([
                'stage_id'          => $stageId,
                'etudiant_id'       => $etudiantId,
                'user_id'           => $user->id,
                'site_id'           => $stage?->site_id,
                'check_in_event_id' => $event->id,
                'attendance_date'   => $date->toDateString(),
                'first_check_in_at' => $expIn,
                'arrival_status'    => 'ontime',
                'day_status'        => 'present',
            ]);
        });
    }

    /**
     * Le stage qui court à cette date. Un employé n'a pas de stage : sa
     * journée se rattache à lui seul.
     */
    protected function stageActif(User $user, Carbon $date): ?Stage
    {
        if (! $user->etudiant) {
            return null;
        }

        return $user->etudiant->stages()
            ->whereDate('date_debut', '<=', $date->toDateString())
            ->whereDate('date_fin', '>=', $date->toDateString())
            ->orderByDesc('date_debut')
            ->first();
    }

    protected function libelle(User $user): string
    {
        return $user->name . ' (' . $user->getRoleNames()->implode('+') . ')';
    }
}