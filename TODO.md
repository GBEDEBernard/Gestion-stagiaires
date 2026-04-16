# TODO pour Sidebar Historique et Dates Français

## Plan approuvé - Progression

### ✅ Étape 1: Créer ce TODO.md

- [x] Fichier créé avec étapes

### ⏳ Étape 2: Modifier navigation.blade.php

- Ajouter onglet Historique dans section "Suivi" pour employés (comme etudiant style)

### ⏳ Étape 3: Corriger dates en français

- `resources/views/presence/historique.blade.php`: Fix `format('d MMM Y')` → French
- `resources/views/components/presence-history-table.blade.php`: Fix dates

### ⏳ Étape 4: Vérifier et tester

- Routes: `php artisan route:list | grep historique`
- Views: presence.historique pour employee/etudiant
- Dates: "16 avril 2026" + "jeudi"

### ⏳ Étape 5: Completion

- attempt_completion

_Maj à chaque étape terminée._
