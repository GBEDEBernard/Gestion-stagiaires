<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class PermissionLabelService
{
    public function getLabel(string $permissionName): string
    {
        // Priorité 1 : Libellé personnalisé dans le config
        $label = Config::get("permissions_labels.{$permissionName}");
        if ($label) {
            return $label;
        }

        // Fallback intelligent (gère les permissions à plusieurs niveaux : presence.admin.view)
        $parts = explode('.', $permissionName);
        
        if (count($parts) >= 2) {
            $groupKey = $parts[0];
            $action   = array_pop($parts);           // Dernier élément = l'action
            
            $actionMap = [
                'view'          => 'Voir',
                'create'        => 'Créer',
                'edit'          => 'Modifier',
                'update'        => 'Modifier',
                'delete'        => 'Supprimer',
                'force-delete'  => 'Supprimer définitivement',
                'restore'       => 'Restaurer',
                'approve'       => 'Approuver',
                'audit'         => 'Auditer',
                'checkin'       => 'Pointer arrivée',
                'checkout'      => 'Pointer départ',
                'download'      => 'Télécharger',
                'print'         => 'Imprimer',
                'review'        => 'Revoir',
                'submit'        => 'Soumettre',
            ];

            $groupLabel = Config::get("permissions_groups.{$groupKey}", Str::replace(['-', '_'], ' ', ucfirst($groupKey)));
            $actionFr   = $actionMap[$action] ?? Str::replace(['-', '_'], ' ', ucfirst($action));

            return "{$groupLabel} : {$actionFr}";
        }

        // Fallback ultime
        return Str::replace(['.', '-', '_'], ' ', ucfirst($permissionName));
    }

    public function getGroupLabel(string $groupKey): string
    {
        return Config::get("permissions_groups.{$groupKey}", 
            Str::replace(['-', '_'], ' ', ucfirst($groupKey))
        );
    }
}