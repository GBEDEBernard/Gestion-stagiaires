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

---

## 📦 Travaux présents sur la branche (non liés aux demandes ci-dessus)
- `vite.config.js` : forçage IPv4 (`127.0.0.1`) pour corriger le HMR sur Windows. **Non commité.**

---

## 🗒️ Journal des décisions
- 2026-06-03 : création de ce fichier de suivi.
- 2026-06-03 : T-002 (précision GPS dynamique) implémentée et **commitée** (`e5f9dbb`).
- 2026-06-03 : incident reset (pull parallèle) → travail non commité écrasé ; T-002 ré-appliquée.
- 2026-06-03 : T-001 — décision de reconstruire la popup profil enrichie en Tailwind ; faite.
