<?php

namespace App\Console\Commands;

use App\Models\Personnel;
use App\Models\Etudiant;
use App\Models\Employe;
use Illuminate\Console\Command;
use App\Models\Domaine;
use App\Models\Site;

class SyncPersonnelToSpecificTables extends Command
{
    protected $signature = 'sync:personnel-tables';
    protected $description = 'Recrée les enregistrements manquants dans etudiants/employes à partir du personnel';

    public function handle()
    {
        $personnels = Personnel::withTrashed()->get();
        $fixed = 0;

        foreach ($personnels as $p) {
            if ($p->personnable_type === Etudiant::class && !$p->personnable) {
                $etudiant = Etudiant::create([
                    'ecole' => 'À définir',
                    'niveau' => null, // ou autre valeur par défaut si nécessaire
                ]);
                $p->personnable()->associate($etudiant)->save();
                $this->info("✓ Étudiant recréé pour {$p->nom} (#{$p->id})");
                $fixed++;
            }
            elseif ($p->personnable_type === Employe::class && !$p->personnable) {
                // Récupère un domaine et site par défaut (ajuste les IDs selon ta base)
                $domaineId = Domaine::first()?->id ?? 1;
                $siteId = Site::first()?->id ?? 1;

                $employe = Employe::create([
                    'matricule'  => 'AUTO-'.$p->id,
                    'domaine_id' => $domaineId,
                    'site_id'    => $siteId,
                    'poste'      => 'Poste à définir',
                ]);
                $p->personnable()->associate($employe)->save();
                $this->info("✓ Employé recréé pour {$p->nom} (#{$p->id})");
                $fixed++;
            }
        }

        if ($fixed > 0) {
            $this->info("Synchronisation terminée. $fixed enregistrement(s) corrigé(s).");
        } else {
            $this->info("Aucune incohérence trouvée. Tout est synchronisé.");
        }
    }
}