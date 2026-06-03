# 🎨 Spec UI/UX — T-003 : Rapports liés aux tâches (progression + chat)

> Référence de conception **écran par écran**. À valider avant implémentation.
> Inspirations : Asana / Linear / Jira (création de tâche), Asana & Motion (activity feed au
> bas de la tâche), Geekbot / Standuply (check-in quotidien : « fait / en cours / blocages »),
> UXPin (progress trackers). Voir « Sources » en bas.

---

## 0. Principes directeurs (issus de la recherche)

1. **Création de tâche ultra-rapide** : si créer une tâche prend > 30 s, l'adoption chute.
   → formulaire **modal**, **une seule colonne**, **champs requis minimaux** (titre seulement),
   le reste optionnel/repliable.
2. **Une tâche = un hub** : la page détail concentre description + progression + historique +
   discussion (activity feed en bas, façon Asana/Motion).
3. **Rapport quotidien = check-in** : on rattache le rapport à une tâche, on déclare la
   progression, on note blocages et prochaines étapes (modèle Geekbot).
4. **Progression visible partout** : barre + % sur chaque carte/tâche (progress tracker).
5. **Cohérence avec l'existant** : on réutilise le design system déjà en place (cf. §1).

---

## 1. Design system réutilisé (ne pas réinventer)

- **Layout** : `<x-app-layout title="...">`, sidebar déjà en place.
- **2 ambiances déjà présentes dans le projet** :
  - **Côté producteur (étudiant/employé)** = palette **slate**, conteneur `max-w-4xl mx-auto`,
    cartes blanches `rounded-xl border border-slate-100 shadow-sm`, **création en modal**
    (cf. `resources/views/reports/index.blade.php`).
  - **Côté admin** = palette **emerald**, cartes `rounded-2xl shadow-sm border border-gray-100
    dark:border-gray-700`, inputs `rounded-xl bg-gray-50 dark:bg-gray-900`, **dark mode**
    (cf. `resources/views/admin/sites/partials/form.blade.php`).
- **Composants Blade existants** : `x-stats-card`, `x-primary-button`, `x-modal`,
  `x-input-label`, `x-text-input`, `x-input-error`, `x-trash-table`.
- **Libs** : Alpine.js (interactions), Chart.js (courbes de progression), Lucide (icônes).
- **Tailwind only** (aucun CSS inline — règle projet).

---

## 2. Référentiel : statuts, priorités, couleurs

### Statut de TÂCHE (cycle de vie)
| Statut | Libellé FR | Couleur | Déclencheur |
|---|---|---|---|
| `pending` | À faire | slate/gris | création |
| `in_progress` | En cours | bleu | 1er rapport ou démarrage manuel → `started_at` |
| `blocked` | Bloquée | rouge | l'auteur marque un blocage |
| `changes_requested` | Corrections demandées | amber | un superviseur/admin répond en demandant des changements |
| `completed` | Terminée | emerald | progression atteint **100 %** → `completed_at` |

### Priorité
| Valeur | Libellé | Couleur |
|---|---|---|
| `low` | Basse | slate |
| `normal` | Normale | bleu |
| `high` | Haute | amber |
| `urgent` | Urgente | rouge |

### Statut de RAPPORT (inchangé)
`draft` (brouillon) · `submitted` (soumis) · `reviewed` (revu).
→ **Clarification** : le statut du rapport décrit le document du jour ; le statut de la **tâche**
décrit l'avancement global. Les deux coexistent (un rapport `submitted` peut faire avancer une
tâche `in_progress`).

---

## 3. ÉCRAN A — « Mes tâches » (liste, producteur)

**Route** : `tasks.index` (vue producteur). **Rôles** : étudiant, employé (ses tâches via
`owner_id`). **But** : voir/filtrer ses tâches, en créer, accéder au détail.

**Disposition (haut → bas)** :
1. **En-tête** : titre « Mes tâches » + sous-titre ; à droite **bouton `+ Nouvelle tâche`**
   (ouvre le modal §4).
2. **Bandeau stats** (4 `x-stats-card`) : À faire · En cours · Bloquées · Terminées (compteurs).
3. **Barre de filtres** : segments `Toutes / À faire / En cours / Bloquées / Terminées` +
   champ recherche (titre) + tri (échéance, récentes, priorité).
4. **Liste de cartes** (1 colonne, façon liste Asana). Chaque **carte tâche** affiche :
   - Pastille de **priorité** (point coloré) + **titre** (lien vers détail).
   - **Badge statut** (couleur §2).
   - **Barre de progression** + `xx %` à droite.
   - **Échéance** (`due_date`) avec badge **« En retard »** si dépassée et non terminée.
   - Mini-méta : date dernier rapport lié, nb de messages non lus (pastille).
   - **Actions** au survol : `Ouvrir`, `Éditer` (si non terminée), `Supprimer` (corbeille).
5. **État vide** : illustration + texte « Aucune tâche pour l'instant » + bouton créer.
6. **Pagination** (10/page, comme l'existant).

**Option (amélioration)** : bascule **Liste ⇄ Kanban** (colonnes par statut, drag pour changer
de statut). → proposé en Phase ultérieure, pas P2.

---

## 4. ÉCRAN B — Création / édition de tâche (MODAL)

**Déclenchement** : bouton `+ Nouvelle tâche`. **Form** : `tasks.store` / `tasks.update`.
**Principe** : rapide, 1 colonne, requis minimal.

**Champs (ordre)** :
1. **Titre*** (`title`) — input texte, autofocus. *Seul champ obligatoire.*
2. **Description** (`description`) — textarea, optionnel.
3. **Priorité** (`priority`) — segmented control (Basse/Normale/Haute/Urgente), défaut Normale.
4. **Échéance** (`due_date`) — date picker, optionnel.
5. *(Étudiant uniquement)* **Stage** (`stage_id`) — pré-rempli avec le stage actif, en lecture
   seule ou caché si un seul. *(Employé : pas de stage.)*
6. *(Repliable « Options avancées »)* **Statut initial** — défaut `pending` ; rarement changé.

**Actions (footer)** : `Annuler` (ferme) · **`Créer la tâche`** (bouton primaire).
**Validation inline** sous les champs (`x-input-error`).
**Note UX** : pas de champ « progression » à la création (toujours 0 %) ; pas de champ
« assigné à » (la tâche appartient au créateur — décision verrouillée).

---

## 5. ÉCRAN C — Détail de la tâche (LE HUB)

**Route** : `tasks.show`. **Accès** : owner + superviseur du stage + admin (lecture ; commentaire
pour tous ; édition réservée à l'owner tant que non terminée).

**Disposition en 2 zones (desktop : 2 colonnes ; mobile : empilé)** :

### Colonne principale (gauche, ~2/3)
1. **En-tête tâche** :
   - Titre (éditable inline par l'owner) + **badge statut** + **pastille priorité**.
   - **Barre de progression** large avec `xx %`.
   - Ligne méta : propriétaire (avatar+nom), stage (si étudiant), échéance, créée le.
   - **Boutons d'action contextuels** :
     - Owner : `Marquer comme bloquée` / `Reprendre`, `Éditer`, `Supprimer`.
     - Superviseur/Admin : `Demander des corrections`, `Valider` (lecture seule sinon).
2. **Description** (carte).
3. **Onglet/section « Rapports liés »** : timeline des rapports quotidiens rattachés à la tâche,
   chacun en carte compacte : date, résumé, **progression déclarée ce jour-là**, blocages,
   heures. (Chronologie descendante.)
4. **Fil de discussion (Activity feed + Chat)** — *cœur du hub* :
   - Flux unifié chronologique mélangeant :
     - **Messages** (producteur / superviseur / admin) — bulle avec avatar, nom, rôle, heure.
     - **Jalons de rapport** (`report_jalon`) — entrée système « 📄 Rapport du 03/06 — 40 % » avec
       lien vers le rapport.
     - **Changements de statut** (`status_change`) — entrée système « ⚙️ Passée En cours », « ✅
       Terminée ».
   - **Zone de saisie** en bas (sticky) : textarea + bouton `Envoyer`. Visible pour owner +
     superviseur + admin. (Plus tard : @mentions, pièces jointes.)
   - Distinction visuelle : messages de l'owner alignés à droite / superviseurs-admin à gauche,
     ou bandeau de rôle coloré (à choisir au build).

### Colonne latérale (droite, ~1/3) — desktop
- **Carte « Avancement »** : grand %, mini **courbe de progression** (Chart.js, x = dates des
  rapports, y = %).
- **Carte « Détails »** : statut, priorité, échéance, dates début/fin, nb rapports, nb messages.
- **Carte « Participants »** : owner + superviseur(s) + admins ayant participé.

---

## 6. ÉCRAN D — Rapport du jour (check-in lié à la tâche)

**Où** : formulaire de création de rapport (modal existant dans `reports/index.blade.php`, à
enrichir). **Modèle mental** : standup async (Geekbot).

**Champs (ordre)** :
1. **Tâche concernée*** (`task_id`) — **select** des tâches actives de l'utilisateur (non
   terminées). Si **aucune tâche** → message + bouton **`Créer une tâche d'abord`** (renvoie §4).
2. **Ce que j'ai fait aujourd'hui** (`summary`) — textarea (= résumé, requis).
3. **Progression de la tâche** (`task_progress_percent`) — **curseur 0–100 %** avec valeur
   affichée ; pré-rempli à la dernière progression connue de la tâche ; ne peut pas **descendre**
   sans note (garde-fou). Case **« Tâche terminée (100 %) »** = raccourci.
4. **Blocages** (`blockers`) — textarea optionnel ; si rempli → propose de passer la tâche
   « Bloquée ».
5. **Prochaines étapes** (`next_steps`) — textarea optionnel.
6. **Heures déclarées** (`hours_declared`) + **Date** (`report_date`) — comme aujourd'hui.

**Actions** : `Enregistrer brouillon` (status_action=draft) · **`Soumettre`** (submit).
**Effets au submit** (logique, pas UI) : applique la progression à la tâche, crée un
`task_update`, insère un **jalon** dans le fil de la tâche, auto-complète si 100 %, notifie
superviseur + admin.

**Garde-fous UX** : 1 rapport/jour/tâche (anti-doublon déjà géré) ; impossible de rattacher à une
tâche terminée.

---

## 7. ÉCRAN E — Suivi des tâches (admin / superviseur)

**Route** : nouvelle page sous `admin/reports` ou `admin/tasks-tracking`. **But** : vue
d'ensemble par **mois**, filtrable.

**Disposition** :
1. **En-tête** + sélecteur de **période** (Jour / Semaine / **Mois**) + date.
2. **Filtres** : producteur, rôle (étudiant/employé), domaine, statut, recherche.
3. **Bandeau stats** : tâches actives, terminées ce mois, en retard, % moyen d'avancement.
4. **Tableau de suivi** (façon `attendance/tracking`) : Producteur · Tâche · Statut · **Progression
   (barre+%)** · Dernier rapport · Échéance · Messages · Action `Ouvrir`.
   - Tri par colonne, lignes en retard surlignées.
5. **(Option)** vue **Kanban global** par statut, ou heatmap mensuelle d'activité.
6. **Export** (CSV/PDF) cohérent avec les exports présents (présence).

---

## 8. ÉCRAN F — Détail tâche côté admin/superviseur (répondre)

Réutilise **§5 (le hub)** en mode revue :
- Lecture complète (description, rapports, courbe).
- **Zone de réponse** active dans le fil → poste un message (implémente
  `AdminReportTrackingController@respond`, route à réparer).
- Boutons **`Demander des corrections`** (passe la tâche `changes_requested` + message) et
  **`Valider`** (accuse réception / approuve).
- Notifie le producteur.

---

## 9. Composants transverses (à standardiser)

- **`x-task-status-badge`** : badge couleur selon statut (§2).
- **`x-priority-dot`** : pastille priorité.
- **`x-progress-bar`** : barre + % (variantes tailles).
- **`x-task-card`** : carte de liste réutilisable (A & E).
- **`x-message-bubble`** : bulle de fil (auteur, rôle, heure, corps).
- **`x-timeline-item`** : entrée système (jalon / changement de statut).
- **État vide** standardisé (icône + texte + CTA).
- **Toasts** de succès (déjà présents).

---

## 10. Edge cases & règles UX (à respecter)

1. **Pas de tâche active** au moment du rapport → bloquer + CTA « Créer une tâche ».
2. **Tâche terminée** → non éditable, non rattachable à un nouveau rapport, fil reste consultable.
3. **Progression** : ne peut pas reculer sans note ; 100 % → confirmation « marquer terminée ? ».
4. **Blocage** : marquer « Bloquée » exige une note (raison).
5. **Permissions de message** : owner + superviseur du stage + admin uniquement.
6. **Suppression** : SoftDelete (corbeille déjà en place sur `tasks`).
7. **Notifications** (cloche `AppNotification`) : nouveau rapport → superviseur+admin ; nouvelle
   réponse → producteur ; passage `changes_requested` → producteur.
8. **Responsive** : liste et hub empilés en mobile ; fil de discussion plein écran sur mobile.
9. **Dark mode** : respecté côté admin (déjà supporté).
10. **Accessibilité** : labels, focus visible, contrôles clavier sur le curseur de progression.

---

## 11. Aspects que TU n'avais pas mentionnés (ajoutés par moi — à valider)

- **Cycle de vie clair** avec statut `changes_requested` (boucle de correction admin → producteur).
- **Garde-fous progression** (anti-recul, note obligatoire sur blocage).
- **Courbe de progression** (Chart.js) sur le hub.
- **Notifications cloche** intégrées (réutilise l'existant).
- **Filtres/recherche/tri** + **export** côté admin.
- **État « En retard »** sur échéance dépassée.
- **Participants** d'une tâche (qui a échangé).
- **Responsive + dark mode + accessibilité** explicitement cadrés.
- **(Futur, hors scope initial)** : Kanban drag-and-drop, @mentions, pièces jointes dans le chat,
  sous-tâches, étiquettes/labels.

---

## 12. Hors-scope (pour ne pas dériver)
Kanban DnD, @mentions, pièces jointes, sous-tâches, labels, temps réel websocket. → backlog
« améliorations futures », à rouvrir après le socle P1–P5.

---

## Sources (recherche UI/UX)
- Atlassian — Best practices for form design (Jira) : https://www.atlassian.com/software/jira/service-management/product-guide/tips-and-tricks/form-design-best-practices
- Asana — App components on tasks (modal create) : https://developers.asana.com/docs/app-components-on-tasks
- Motion — Activity Feed: Tracking and Logging : https://www.usemotion.com/help/project-management/task/concept-tasks/activity-feed-tracking-and-logging
- UXPin — Progress Tracker Design best practices : https://www.uxpin.com/studio/blog/design-progress-trackers/
- Geekbot — Daily Standup template (check-in async) : https://geekbot.com/templates/daily-standup/
