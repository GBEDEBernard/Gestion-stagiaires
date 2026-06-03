# 📋 Suivi des tâches — Gestion des Stagiaires

> Fichier de contexte **persistant** entre sessions. Il doit toujours suffire à reprendre le
> travail sans ré-explication. **Mettre à jour à chaque évolution** (tâche démarrée / terminée /
> nouvelle idée). L'utilisateur gère lui-même les `git commit` ; l'agent fournit le « feu vert »
> + le message de commit quand une tâche est terminée.

---

## 🧭 Contexte projet (rappel rapide)

- **Stack** : Laravel 12 / PHP 8.2, Breeze (auth), Blade + Alpine.js + Tailwind, Vite.
- **Permissions** : `spatie/laravel-permission`. Rôles : `admin`, `superviseur`, `etudiant`,
  `employe`/`fonctionnaire`.
- **Métier** : gestion stagiaires/personnel + **pointage géolocalisé** (geofence par site),
  rapports journaliers, tâches, attestations PDF, badges QR, demandes de permission/congé.
- **Architecture clé** : `Personnel` est le pivot (morphTo `personnable` → `Etudiant` ou
  `Employe`). `User` (compte) → `Personnel` (source de vérité nom/email).
- **Branche de travail** : `john-branch-2`.
- **Convention** : privilégier les **classes Tailwind** (pas de CSS inline) pour rester
  cohérent avec le reste des vues.

---

## ✅ Règles de fonctionnement (à respecter par l'agent)

1. Ne **jamais** committer : l'utilisateur s'en charge.
2. À la fin d'une tâche → donner **✅ FEU VERT** + un **message de commit** prêt à copier.
3. Mettre ce fichier à jour **au fil de l'eau** (statut, notes, décisions).
4. Garder les modifications **ciblées** (pas de refactor hors périmètre non demandé).

---

## 🟢 Tâches EN COURS / À FAIRE

### T-001 — Refonte du menu profil (`navigation.blade.php`) en Tailwind
- **Statut** : ⚠️ ANNULÉE / sans objet (voir note ci-dessous)
- **Fichier** : `resources/views/layouts/navigation.blade.php`
- **Problème initial** : la popup profil flottante était en CSS inline → à convertir en Tailwind.
- **Ce qui s'est passé** : un `git pull` (fast-forward) + 2 commits faits en parallèle pendant la
  session (2026-06-03) ont **réinitialisé le working tree** et écrasé la modification non commitée.
  La popup en CSS inline **n'existe plus** : `navigation.blade.php` est revenu à sa version simple
  d'origine (`userMenuOpen`, « Paramètres / Déconnexion »), **déjà en Tailwind**. La tâche n'a donc
  plus d'objet en l'état.
- **À décider** : soit on laisse tel quel (menu simple suffisant), soit on reconstruit une popup
  profil enrichie (avatar + email + actions) directement en Tailwind. À confirmer par l'utilisateur.

### T-002 — Précision GPS du geofence : dynamique (pas de valeur par défaut figée)
- **Statut** : ✅ Terminé (ré-appliqué après le reset)
- **Fichiers** :
  - `resources/views/admin/sites/partials/form.blade.php`
  - `database/migrations/2026_05_26_000000_update_geofence_accuracy_defaults.php` (supprimé)
- **Demande** : l'admin doit pouvoir **saisir/modifier lui-même** la précision GPS max ; pas de
  valeur par défaut imposée (ni 50, ni 100).
- **Actions faites** :
  - Form : champ `geofence_allowed_accuracy_meters` → **plus de défaut** (`?? ''`), vide en
    création (admin saisit), valeur existante affichée en édition. Reste `required`, ajout
    `min=5 max=500`, placeholder et texte d'aide.
  - La migration `2026_05_26...` qui forçait la valeur à 100 a disparu lors du reset (jamais
    commitée) — c'est l'effet voulu.
- **Note** : la validation côté `SiteController@validatePayload` impose déjà `integer|min:5|max:500`.

---

## 📦 Travaux déjà présents sur la branche (non liés aux demandes ci-dessus)
- `vite.config.js` : forçage IPv4 (`127.0.0.1`) pour corriger le HMR sur Windows.
- `public/build/manifest.json` : régénéré par un build Vite.

---

## 🗒️ Journal des décisions
- 2026-06-03 : création de ce fichier de suivi.
- 2026-06-03 : T-001 & T-002 implémentées (voir sections ci-dessus).
