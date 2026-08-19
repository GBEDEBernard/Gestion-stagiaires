<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StudentStageExpiryService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class DeactivateExpiredStudentAccounts extends Command
{
    protected $signature = 'students:deactivate-expired {--dry-run : Affiche les comptes concernés sans les désactiver}';

    protected $description = 'Désactive automatiquement les comptes des stagiaires dont tous les stages sont terminés';

    public function handle(StudentStageExpiryService $service): int
    {
        $this->info('Recherche des stagiaires à stages terminés…');

        if ($this->option('dry-run')) {
            foreach ($this->candidates($service) as $user) {
                $this->line("- {$user->email} ({$user->name})");
            }

            $this->info("{$this->candidates($service)->count()} compte(s) seraient désactivés.");
            return self::SUCCESS;
        }

        $count = $service->deactivateExpiredAccounts();

        if ($count === 0) {
            $this->info('Aucun compte à désactiver.');
        } else {
            $this->info("{$count} compte(s) désactivé(s).");
        }

        return self::SUCCESS;
    }

    protected function candidates(StudentStageExpiryService $service): Collection
    {
        return User::role('etudiant')
            ->where('status', 'actif')
            ->with('etudiant')
            ->latest()
            ->get()
            ->filter(fn($user) => $service->hasExpiredStage($user));
    }
}