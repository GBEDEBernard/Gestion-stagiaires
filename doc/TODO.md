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

1. Ne **jamais** committer sans demande : l'utilisateur s'en charge (sauf instruction explicite).
2. À la fin d'une tâche → donner **✅ FEU VERT** + un **message de commit** prêt à copier.
3. Mettre ce fichier à jour **au fil de l'eau** (statut, notes, décisions).
4. Garder les modifications **ciblées** (pas de refactor hors périmètre non demandé).

> ⚠️ **Incident 2026-06-03** : un `git pull` (fast-forward) + 2 commits faits **en parallèle**
> pendant la session ont réinitialisé le working tree et **écrasé des modifications non commitées**.
> Leçon : committer tôt ; éviter de laisser l'agent travailler pendant un pull/reset.

---

## 🟢 Tâches

### T-001 — Menu profil sidebar : popup enrichie en Tailwind
- **Statut** : ✅ Terminé (en attente de commit utilisateur)
- **Fichier** : `resources/views/layouts/navigation.blade.php`
- **Historique** :
  - Version initiale visée : popup flottante en **CSS inline** → à convertir en Tailwind.
  - L'incident du 2026-06-03 a réinitialisé le fichier vers sa version simple d'origine
    (`userMenuOpen`, « Paramètres / Déconnexion »).
  - Décision utilisateur : **reconstruire** la popup enrichie, en Tailwind cette fois.
- **Action faite** : popup profil reconstruite **100 % Tailwind** (aucun `style=`/`onmouseover`) :
  - Bloc épinglé en **pied de sidebar** (`flex-shrink-0`, sorti de la zone scrollable).
  - Bouton déclencheur (avatar + nom + email + chevrons), popup flottante (`position: fixed`)
    avec header utilisateur (avatar + statut en ligne), liens **Paramètres du compte** et
    **Déconnexion**, overlay de fermeture au clic dehors, transitions Alpine + `x-cloak`.

### T-002 — Précision GPS du geofence : dynamique (pas de valeur par défaut figée)
- **Statut** : ✅ Terminé — **commité** (`e5f9dbb`)
- **Fichier** : `resources/views/admin/sites/partials/form.blade.php`
- **Demande** : l'admin doit pouvoir **saisir/modifier lui-même** la précision GPS max ; pas de
  valeur par défaut imposée (ni 50, ni 100).
- **Action faite** : champ `geofence_allowed_accuracy_meters` → **plus de défaut** (`?? ''`),
  vide en création (admin saisit), valeur existante affichée en édition. `required`, `min=5`,
  `max=500`, placeholder + texte d'aide.
- **Note** : la validation `SiteController@validatePayload` impose déjà `integer|min:5|max:500`.
  Plus de migration forçant la valeur (l'ancienne `2026_05_26...` a disparu au reset, jamais
  commitée — effet voulu).

### T-003 — EPIC : Rapports journaliers liés aux tâches (progression + chat)
- **Statut** : ✅ **TOUTES LES PHASES IMPLÉMENTÉES (P1→P5)** — en attente de commit + test visuel.
- **📄 Spec UI/UX détaillée (écran par écran)** : voir **`doc/UI-SPEC-T003.md`** — disposition,
  champs, boutons, composants, états, edge cases, statuts/couleurs, inspirations (Asana/Linear/
  Jira/Geekbot/Motion). À lire avant toute implémentation d'écran.
- **But métier** :
  - Les **producteurs** (employé/étudiant) créent leurs **tâches**.
  - Le **rapport du jour** se rattache à **une tâche** + déclare une **progression**.
  - On travaille **plusieurs jours** sur la même tâche jusqu'à 100 % (= terminée).
  - Chaque **tâche** porte un **fil de discussion** (producteur + superviseur + admin).
  - L'admin a un **suivi mensuel** complet des tâches.
- **Décisions verrouillées (2026-06-03)** :
  1. Tâches créées **uniquement par les producteurs** ; admin/superviseur = lecture + commentaire.
  2. Chat **au niveau de la TÂCHE** (fil unique ; les rapports s'y insèrent comme jalons).
  3. Progression = **dernière valeur déclarée** ; **100 % ⇒ tâche `completed`**.
  4. **Une seule tâche par rapport** (lien direct ; `daily_report_items` non utilisé).
- **État de l'existant (constat)** :
  - `tasks` : créées par admin only, `etudiant_id` NOT NULL (employés exclus). Champs utiles déjà
    là (`status`, `priority`, `last_progress_percent`, `started_at`, `completed_at`).
  - `daily_reports` : 1/jour, pas de lien tâche câblé ; `completion_rate` inutilisé.
  - `daily_report_items` (rapport→tâche) et `task_updates` : **tables présentes mais jamais
    utilisées**.
  - `daily_report_reviews` : **affiché** mais **aucune route pour créer** ; route
    `admin.reports.respond` **cassée** (méthode `respond()` absente).
  - `DailyReportService.storeForToday` : ignore `items`, ne touche pas les tâches.
- **Schéma cible (migrations)** :
  - `tasks` : + `owner_id` (FK users) ; `etudiant_id` & `stage_id` → nullable ; backfill.
  - `daily_reports` : + `task_id` (FK tasks, nullable) + `task_progress_percent` (0–100).
  - **`task_messages`** (NOUVELLE) : `task_id`, `user_id`, `body`, `type`
    (message|report_jalon|status_change), `daily_report_id` nullable, timestamps.
  - `task_updates` : réutilisée comme historique de progression.
- **Plan par phases** :
  - **P1** ✅ **FAIT** (en attente commit) — Schéma & modèles :
    - Migrations `2026_06_03_000001/2/3` : `tasks.owner_id` (+ `etudiant_id`/`stage_id` nullable,
      backfill owner_id=assigned_by) ; `daily_reports.task_id` + `task_progress_percent` ;
      table `task_messages`.
    - Modèles : `Task` (owner/messages/dailyReports + `scopeVisibleTo` par owner + helpers
      `isCompleted`/`isOverdue` + `STATUSES`), `DailyReport` (task + champs), `TaskMessage` (new),
      `User` (ownedTasks/taskMessages). Vérifié via tinker (schéma + relations OK).
  - **P2** ✅ **FAIT** (en attente commit) — Tâches côté producteur :
    - Permissions : `tasks.view/create/edit/delete` accordées à `etudiant` + `employe` ;
      `superviseur` = `tasks.view/review` (lecture + commentaire). Maj du preset
      (`RolePermissionPresetService`) + migration `2026_06_03_000004` qui applique aux rôles.
    - Routes : groupe `admin/tasks` remplacé par `/tasks` (producteur) + route `tasks.show`.
    - `TaskController` réécrit : ownership (`owner_id`), contexte stage auto pour étudiant,
      employé sans stage, redirections via `encrypted_route` (⚠️ id nu = 404 à cause de
      `DecryptRouteParameter::isEncrypted`), filtres statut/recherche, stats.
    - Vues producteur : `tasks/index` (slate, modal création, stats, filtres, cartes),
      `tasks/show` (hub : header+progression+rapports liés+fil — scaffold P3/P4), `tasks/edit`.
    - Composants réutilisables : `x-task-status-badge`, `x-progress-bar`, `x-priority-dot`.
    - Nav : item « Mes tâches » (étudiant + employé). Anciennes vues `admin/tasks/*` supprimées.
    - Vérifié : migrations OK, `route:list`, `view:cache` (compile sans erreur), permissions
      par rôle, aucune référence orpheline.
  - **P3** ✅ **FAIT** — Rapport ↔ tâche + progression :
    - `StoreDailyReportRequest` : `task_id` + `task_progress_percent` (items.* retirés).
    - `DailyReportService` : `storeForToday` applique la tâche ; `syncTaskProgress()` met à jour
      `last_progress_percent`/statut, crée `task_update`, insère un jalon (`task_messages`),
      auto-complète à 100 %, notifie superviseur+admins. `NotificationService::push()` ajouté.
    - `DailyReportController` : passe `$activeTasks` (index + edit) ; `update()` gère la tâche.
    - UI : sélecteur de tâche + curseur progression dans le modal de création **et** l'édition.
    - Testé end-to-end (transaction rollback) : 40% → in_progress + update + jalon ; 100% →
      completed + completed_at + status_change. ✔
  - **P4** ✅ **FAIT** — Chat par tâche :
    - `TaskMessageController@store` (POST `tasks/{task}/messages`) ; `TaskController@review`
      (POST `tasks/{task}/review` : request_changes / approve) ; notifications ciblées.
    - `tasks/show` : fil (bulles humaines alignées owner/relecteur + entrées système jalon/
      status_change), composer de message, boutons de revue (superviseur/admin).
    - `AdminReportTrackingController@respond` **implémenté** (route réparée) : poste dans le fil
      de la tâche liée sinon crée une review ; notifie l'auteur.
  - **P5** ✅ **FAIT** — Suivi admin :
    - `AdminTaskTrackingController@index` (period jour/semaine/mois + filtres statut/recherche/
      date, stats : total/en cours/terminées/en retard/avancement moyen).
    - Route `admin/tasks-tracking` (role:admin|superviseur) ; vue `admin/tasks/tracking`
      (tableau emerald : producteur, tâche, statut, progression, rapports, messages, échéance).
    - Nav : entrée admin/superviseur repointée vers « Suivi des tâches ».
    - Courbe de progression : **sparkline SVG server-side** sur `tasks/show` (Chart.js non
      bundlé → pas de dépendance JS ajoutée).
- **Phase 0 (réparée en chemin)** : route `admin.reports.respond` désormais fonctionnelle.
  Champs legacy `reviewed_by`/`reviewed_at` du rapport réutilisés par `respond()`.

### T-003-BUGFIX — Correctifs après test utilisateur (2026-06-03)
- **404 sur les tâches** (tasks.show/edit/messages/review) : **CAUSE** = il manquait
  `Route::bind('task', ...)` dans `AppServiceProvider` (le pattern de binding chiffré existait
  pour stage/etudiant/user… mais pas task → l'id restait chiffré → `findOrFail(token)` → 404).
  **FIX** : ajout du binding `task` (même `resolveEncryptedModel`). Roundtrip vérifié. ✔
  → Le **chat devient accessible** (la page tâche s'ouvre) ; superviseurs/admins peuvent répondre.
- **403 / "Unexpected token '<'" sur `admin/reports/respond`** : le JS attend du JSON mais
  `respond()` renvoyait une redirection. **FIX** : `respond()` renvoie `response()->json(...)`.
- Tokens chiffrés de l'app vérifiés **URL-safe** (0/20 avec `/ + =`) → pas d'autre correctif requis.

### T-004 — Refonte UX : tout sur une seule vue (unifié, moderne, pro) — 🟡 À PLANIFIER
- **Demande** : ne plus avoir d'onglets séparés (Suivi tâches vs Suivi rapports ; Mes tâches vs
  Rapports). **Tout géré ensemble** sur une vue, de façon dynamique et professionnelle.
  Style moderne, **hovers doux** (pas d'effet agressif/translate/scale ni gradients criards),
  **espacement** généreux, icônes cohérentes.
- **Direction proposée** (master-détail) :
  - **Producteur** : 1 vue « Espace de travail » = liste de mes tâches (gauche) + détail tâche
    (droite) avec description, progression, **ses rapports** + **chat** + **rapport du jour inline**.
    La page `/reports` séparée devient redondante.
  - **Admin/Superviseur** : 1 vue « Suivi » = liste tâches (filtres producteur/période/statut) +
    détail (rapports + chat + actions de revue). Fusionne suivi-tâches & suivi-rapports.
  - **Style** : palette neutre slate/zinc, `hover:bg-*/50` discrets, `rounded-xl`, icônes line
    stroke 1.5, beaucoup d'espace. Adoucir aussi la sidebar (retirer `hover:translate-x-1`,
    gradients vifs).
- **Statut** : ✅ **FAIT** (producteur + sidebar) — en attente commit + test visuel.
- **Décisions** : master-détail 2 colonnes ; `/reports` fusionné ; restyle T-003 + sidebar.
- **Réalisé** :
  - `TaskController` : `index`/`show` rendent **`tasks/workspace`** (master-détail) via
    `workspaceData()` (liste gauche + tâche sélectionnée droite + `todayReport`).
  - `resources/views/tasks/workspace.blade.php` (NOUVEAU) : liste filtrable/recherche à gauche,
    détail à droite, modal création, style neutre slate, hovers doux.
  - `resources/views/tasks/partials/detail.blade.php` (NOUVEAU) : header+actions (owner/relecteur),
    progression+sparkline, **rapport du jour inline** (compose vers `reports.store`), rapports,
    **chat** — le tout sur la même vue.
  - Anciennes `tasks/index.blade.php` & `tasks/show.blade.php` supprimées.
  - `DailyReportController@index` : producteurs redirigés vers `tasks.index` (`/reports` fusionné).
  - Sidebar : retrait `hover:translate-x-1` (×21), gradients vifs tonifiés, entrées rapports
    séparées retirées (étudiant/employé « Rapport journalier », admin « Suivi rapports »),
    « Mes tâches » → « Espace de travail ».
  - Rendu vérifié via tinker (workspace vide + détail propriétaire) sans erreur.
- **Reste (optionnel, non bloquant)** : restyle de la page `admin/tasks/tracking` en master-détail
  (actuellement table → clique vers le workspace, ce qui couvre déjà la fusion rapports+chat).

---

## 📦 Travaux présents sur la branche (non liés aux demandes ci-dessus)
- `vite.config.js` : forçage IPv4 (`127.0.0.1`) pour corriger le HMR sur Windows. **Non commité.**

---

## 🗒️ Journal des décisions
- 2026-06-03 : création de ce fichier de suivi.
- 2026-06-03 : T-002 (précision GPS dynamique) implémentée et **commitée** (`e5f9dbb`).
- 2026-06-03 : incident reset (pull parallèle) → travail non commité écrasé ; T-002 ré-appliquée.
- 2026-06-03 : T-001 — décision de reconstruire la popup profil enrichie en Tailwind ; faite.
