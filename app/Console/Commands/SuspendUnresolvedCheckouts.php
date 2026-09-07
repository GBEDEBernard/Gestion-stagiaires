<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\AttendanceDay;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Départ jamais réclamé : avertissement, puis suspension.
 *
 * Une journée clôturée d'office reste un trou dans le registre tant que
 * personne n'a dit ce qui s'est passé. La modale le demande à chaque visite,
 * mais rien n'obligeait à y répondre : on pouvait l'écarter indéfiniment.
 *
 * Deux jours après la journée oubliée, l'avertissement part. Le lendemain, si
 * rien n'a été déclaré, le compte passe inactif — le middleware déconnecte à
 * la requête suivante. La réactivation appartient à l'administrateur seul, en
 * personne : c'est tout l'objet de la suspension, obliger le passage devant
 * quelqu'un. Déclarer suffit à s'en préserver ; ce qui est sanctionné, c'est
 * le silence, pas l'oubli.
 */
class SuspendUnresolvedCheckouts extends Command
{
    protected $signature = 'attendance:suspend-unresolved
        {--warn-after=2 : Jours après la journée oubliée avant l\'avertissement}
        {--dry-run : Montrer ce qui serait fait, sans rien écrire}';

    protected $description = "Avertit puis suspend les comptes dont un départ oublié n'a jamais été déclaré";

    public function __construct(protected NotificationService $notifications)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry-run');
        $warnAfter = max(1, (int) $this->option('warn-after'));

        $days = AttendanceDay::where('departure_status', 'auto_closed')
            ->whereNull('claimed_at')
            ->whereDate('attendance_date', '<', today())
            ->orderBy('attendance_date')
            ->get();

        $avertis = 0;
        $suspendus = 0;

        // Une personne peut traîner plusieurs journées : la plus ancienne
        // commande, les autres suivront une fois celle-là réglée.
        foreach ($days->groupBy(fn ($day) => $this->ownerId($day)) as $userId => $journees) {
            if (!$userId) {
                continue;
            }

            $user = User::find($userId);

            if (!$user || $user->status !== 'actif') {
                continue;
            }

            // Suspendre un administrateur fermerait la porte à tout le monde,
            // y compris à celui qui doit rouvrir les comptes.
            if ($user->hasAnyRole(['admin', 'superviseur'])) {
                continue;
            }

            $journee = $journees->first();

            if ($journee->suspension_warned_at) {
                // L'avertissement doit avoir passé une nuit : suspendre le jour
                // même reviendrait à ne pas prévenir.
                if ($journee->suspension_warned_at->isToday()) {
                    continue;
                }

                $suspendus += $this->suspendre($user, $journee, $dryRun);
                continue;
            }

            if ($journee->attendance_date->diffInDays(today()) >= $warnAfter) {
                $avertis += $this->avertir($user, $journee, $dryRun);
            }
        }

        $this->info(($dryRun ? '[simulation] ' : '') . "{$avertis} avertissement(s), {$suspendus} suspension(s).");

        return Command::SUCCESS;
    }

    /** L'avertissement de la veille : le compte est encore ouvert. */
    protected function avertir(User $user, AttendanceDay $journee, bool $dryRun): int
    {
        $date = $journee->attendance_date->format('d/m/Y');

        $this->line("  Avertissement — {$user->name}, journée du {$date}");

        if ($dryRun) {
            return 1;
        }

        $journee->forceFill(['suspension_warned_at' => now()])->save();

        AppNotification::create([
            'unique_id' => 'suspension_avertissement_' . (string) Str::uuid(),
            'user_id'   => $user->id,
            'type'      => 'suspension_avertissement',
            'title'     => 'Votre compte sera suspendu demain',
            'message'   => "Le départ du {$date} n'a jamais été déclaré. Indiquez l'heure à laquelle vous êtes parti, "
                . "puis présentez-vous à votre responsable. Sans cela, votre accès sera suspendu demain.",
            'icon'      => 'alert-triangle',
            'color'     => 'red',
            'url'       => '/presence/pointage',
        ]);

        return 1;
    }

    /** La coupure, une fois l'avertissement resté sans suite. */
    protected function suspendre(User $user, AttendanceDay $journee, bool $dryRun): int
    {
        $date = $journee->attendance_date->format('d/m/Y');

        $this->line("  Suspension — {$user->name}, journée du {$date}");

        if ($dryRun) {
            return 1;
        }

        $user->forceFill(['status' => 'inactif'])->save();

        Log::info('Compte suspendu : départ non déclaré', [
            'user_id'          => $user->id,
            'attendance_day'   => $journee->id,
            'attendance_date'  => $journee->attendance_date->toDateString(),
        ]);

        // L'intéressé ne pourra plus lire ses notifications : c'est
        // l'administrateur qui doit savoir pourquoi la porte s'est fermée, et
        // ce qu'il faut régler pour la rouvrir.
        $this->notifications->notifyAdminsOfAttendanceSuspension($user, $journee);

        return 1;
    }

    /** Le compte derrière la journée, qu'elle soit portée par un stage ou non. */
    protected function ownerId(AttendanceDay $day): ?int
    {
        return $day->user_id ?? $day->etudiant?->user?->id;
    }
}
