# Correction 403 Notifications Anomalies - Plan Approuvé ✅

## Objectif

Corriger 403 sur Admin > Présence > Anomalies pour Admins. Employees voient notifications liées à eux.

## Étapes (Progression) :

### 1. [✅ TODO créé] Créer TODO.md

### 2. [✅] Créer AuthServiceProvider.php + Gate `accessAdminPresence`

### 3. [✅] Modifier routes/web.php : middleware → `can:accessAdminPresence`

### 4. [✅] Permissions explicites ajoutées dans RolePermissionSeeder

### 5. [✅] Command `php artisan permission:sync-admin` créée

### 6. [✅] RolePermissionSeeder mis à jour (admin a TOUTES perms)

### 7. [✅] Commandes test fournies + instructions françaises

### 7. [✅] FIX Erreur Inertia → Vue Blade admin.presence.anomalies.blade.php créée

### 8. [✅] **TERMINÉ** : 403 résolu + page Anomalies fonctionnelle !

**Status** : Étape 1 terminée.
