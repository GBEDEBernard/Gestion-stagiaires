# 🏗️ Architecture du Système de Cryptage

```
┌─────────────────────────────────────────────────────────────────┐
│                    UTILISATEUR              │
│                  Clique sur un lien         │
└────────────────────┬────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│          BLADE TEMPLATE (Vue)                                   │
│  <a href="{{ encrypted_route('stages.show', $stage) }}">Voir</a>│
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│        HELPER FUNCTION (app/Helpers/helpers.php)                │
│                                                                  │
│  function encrypted_route($route, $model)                      │
│  {                                                              │
│      $id = $model->getKey();  // 5                             │
│      $encrypted = Crypt::encryptString($id);                   │
│      return route($route, $encrypted);                         │
│      // /admin/stages/eyJpdiI6IkFF...                          │
│  }                                                              │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│          URL GÉNÉRÉE                                             │
│  /admin/stages/eyJpdiI6IkFFdUf3R4dUtBWEhuNEJMcTkxTzBpaXBmRUlVCQ==
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼ (Utilisateur clique)
┌─────────────────────────────────────────────────────────────────┐
│          LARAVEL ROUTER (routes/web.php)                         │
│  Route::get('{stage}', [StageController::class,'show'])         │
│  // Match: {stage} = 'eyJpdiI6IkFF...'                          │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│    ROUTE MODEL BINDING (app/Providers/AppServiceProvider.php)   │
│                                                                  │
│  Route::bind('stage', function ($value) {                      │
│      return $this->resolveEncryptedModel($value, Stage::class);│
│  });                                                            │
│                                                                  │
│  resolveEncryptedModel($value = 'eyJpdiI6IkFF...')             │
│  {                                                              │
│      $id = Crypt::decryptString($value);  // 5                 │
│      return Stage::findOrFail($id);  // Loading Stage #5       │
│  }                                                              │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│      CONTRÔLEUR (app/Http/Controllers/StageController.php)      │
│                                                                  │
│  public function show(Stage $stage)  // $stage = Stage #5 ✓    │
│  {                                                              │
│      return view('admin.stages.show', compact('stage'));       │
│  }                                                              │
│                                                                  │
│  • Pas de décryptage manuel                                    │
│  • Pas d'erreurs                                               │
│  • Code propre et lisible                                      │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│            VUE BLADE (affiche les données)                       │
│  <h1>{{ $stage->theme }}</h1>                                   │
│  <p>Étudiant: {{ $stage->etudiant->nom }}</p>                   │
│  <!-- Données du stage affichées -->                            │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│            UTILISATEUR VOIT LA PAGE ✅                           │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 Composants du Système

```
COMPOSANT 1: AppServiceProvider
├── Route::bind() pour chaque modèle
├── resolveEncryptedModel($value, $modelClass)
│   ├── Détecte si c'est crypté (heuristic)
│   ├── Essaie de décrypter
│   ├── Retrouve le modèle en base
│   └── Retourne le modèle
└── Fallback pour IDs non-cryptés

COMPOSANT 2: Helper Function
├── encrypted_route($routeName, $parameters)
│   ├── Extrait l'ID du modèle
│   ├── Crypte l'ID avec Crypt::encryptString()
│   ├── Appelle route($routeName, $encrypted)
│   └── Retourne l'URL cryptée
└── decrypt_route_param() [rarement utilisé]

COMPOSANT 3: Routes
├── Route::get('{stage}', ...)
├── Route::get('{stage}/edit', ...)
├── Route::put('{stage}', ...)
└── Route::delete('{stage}', ...)
    └── Le binding décrypte automatiquement

COMPOSANT 4: Contrôleurs
├── public function show(Stage $stage)
├── public function edit(Stage $stage)
├── public function update(Request $request, Stage $stage)
└── public function destroy(Stage $stage)
    └── Reçoivent le modèle décrypté
```

---

## 🔄 Flux d'Exécution Complet

```
┌─────────────┐
│  User View  │  <a href="{{ encrypted_route('stages.show', $stage) }}">
│             │
└──────┬──────┘
       │
       ├─→ encrypted_route()
       │   ├─ Reçoit: stage object, route name
       │   ├─ Extrait: id = 5
       │   ├─ Crypte: 'eyJpdiI6IkFF...'
       │   └─ Retourne: /admin/stages/eyJpdiI6IkFF...
       │
       └─→ <a href="/admin/stages/eyJpdiI6IkFF...">

┌────────────────────┐
│   User Navigate    │ Clique sur le lien
│  Click the Link    │
└────────┬───────────┘
         │
         ├─→ GET /admin/stages/eyJpdiI6IkFF...
         │
         ├─→ Laravel Router
         │   └─ Match: Route::get('{stage}', ...)
         │   └─ Parameter: stage = 'eyJpdiI6IkFF...'
         │
         ├─→ AppServiceProvider::Route::bind('stage')
         │   ├─ Reçoit: value = 'eyJpdiI6IkFF...'
         │   ├─ Appel: resolveEncryptedModel($value, Stage::class)
         │   │   ├─ Détecte: looks encrypted
         │   │   ├─ Décrypte: Crypt::decryptString() = '5'
         │   │   └─ Charge: Stage::findOrFail(5) ✓
         │   └─ Retourne: Stage object (id: 5)
         │
         ├─→ StageController::show($stage)
         │   ├─ Reçoit: Stage object (id: 5) ✓
         │   └─ Retourne: view('admin.stages.show')
         │
         ├─→ View Rendered
         │   └─ $stage->theme, $stage->etudiant, etc.

┌──────────────────┐
│  Browser Display │ ✅ Page Affichée avec Données
│  Show Page Data  │
└──────────────────┘
```

---

## 📦 Structure des Fichiers

```
app/
├── Providers/
│   └── AppServiceProvider.php ✨ Route Model Binding
├── Http/
│   └── Controllers/
│       ├── StageController.php (simplifié)
│       ├── EtudiantController.php (ok)
│       ├── BadgeController.php (ok)
│       └── ...
├── Helpers/
│   └── helpers.php ✨ encrypted_route() function
├── Models/
│   ├── Stage.php
│   ├── Etudiant.php
│   └── ...
└── ...

routes/
└── web.php ✨ Routes avec model binding {stage}, etc.

resources/
└── views/
    ├── admin/
    │   ├── stages/
    │   │   └── index.blade.php ✨ Uses encrypted_route()
    │   ├── etudiants/
    │   │   └── index.blade.php (ok)
    │   └── ...
    └── ...

composer.json ✨ helpers.php en autoload

bootstrap/
└── app.php ✨ Nettoyé, middleware supprimé

.env
└── APP_KEY=base64:... (Configuration)
```

---

## ✅ Vérifications

```
✅ AppServiceProvider.php
   └─ Route::bind() pour chaque modèle

✅ helpers.php
   └─ encrypted_route() function chargée

✅ Routes (routes/web.php)
   └─ Utilisent le model binding {stage}, etc.

✅ Contrôleurs
   └─ Reçoivent les modèles directement

✅ Vues
   └─ Utilisent encrypted_route()

✅ Pas de middleware bugué
   └─ Supprimé et non utilisé

✅ Composer autoload
   └─ helpers.php incluse

✅ APP_KEY
   └─ Configurée dans .env
```

---

## 🚀 Commandes de Setup

```bash
# 1. Régénérer autoload
composer dump-autoload

# 2. Nettoyer les caches
php artisan optimize:clear
php artisan config:cache
php artisan route:clear

# 3. Vérifier la configuration
bash verify-encryption-system.sh

# 4. Démarrer le serveur
php artisan serve

# 5. Tester
# Allez sur http://localhost:8000/admin/stages
# Cliquez sur les liens pour vérifier que ça marche
```

---

**C'est une architecture simple, propre et fonctionnelle!** ✨
