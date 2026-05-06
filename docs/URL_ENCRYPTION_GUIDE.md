# Guide - Système de Cryptage des URLs

## 📋 Vue d'ensemble

Ce projet utilise un système de **cryptage automatique des IDs dans les URLs** pour obfusquer les identifiants des ressources en base de données.

**Exemple:**

- Au lieu de: `/admin/stages/5`
- Vous obtenez: `/admin/stages/eyJpdiI6IkFFdUf3R4d...` (crypté)

---

## 🏗️ Architecture

Le système fonctionne sur **3 couches**:

### 1️⃣ **Route Model Binding** (AppServiceProvider)

```php
// Automatiquement, quand une route contient {stage}, {etudiant}, etc.
// Le binding décrypte le paramètre et charge le modèle
Route::bind('stage', function ($value) {
    return $this->resolveEncryptedModel($value, Stage::class);
});
```

- **Localisation**: `app/Providers/AppServiceProvider.php`
- **Responsabilité**: Décrypter les paramètres de route et charger les modèles
- **Fonctionnement**:
    - Reçoit le paramètre crypté de l'URL
    - Essaie de le décrypter
    - Si décryptage échoue, traite comme ID normal (fallback)
    - Cherche le modèle par ID dans la base de données

### 2️⃣ **Helper Function** (helpers.php)

```php
encrypted_route('stages.show', $stage)
// Génère: /admin/stages/eyJpdiI6IkFFdUf3R4d...
```

- **Localisation**: `app/Helpers/helpers.php`
- **Responsabilité**: Générer les URLs cryptées
- **Utilisation**:
    ```blade
    <a href="{{ encrypted_route('stages.show', $stage) }}">Voir</a>
    <a href="{{ encrypted_route('stages.edit', $stage->id) }}">Modifier</a>
    ```

### 3️⃣ **Routes** (routes/web.php)

```php
// Route standard - le model binding fait le décryptage automatiquement
Route::get('{stage}', [StageController::class,'show'])->name('stages.show');
```

- **Localisation**: `routes/web.php`
- **Responsabilité**: Définir les routes RESTful standard
- **Important**: Utiliser le model binding standard `{stage}`, `{etudiant}`, etc

### 4️⃣ **Contrôleurs**

```php
// Reçoit automatiquement le Stage décrypté via le model binding
public function show(Stage $stage)
{
    return view('admin.stages.show', compact('stage'));
}
```

- **Responsabilité**: Logique métier uniquement
- **Avantage**: Pas besoin de décrypter manuellement!

---

## 📝 Guide d'Utilisation

### Dans les vues Blade:

```blade
<!-- Générer une URL cryptée -->
<a href="{{ encrypted_route('stages.show', $stage) }}" class="btn-view">
    Voir le stage
</a>

<!-- Ou avec un ID directement -->
<a href="{{ encrypted_route('stages.edit', $stage->id) }}">
    Modifier
</a>

<!-- Ou dans un formulaire -->
<form action="{{ encrypted_route('stages.destroy', $stage) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit">Supprimer</button>
</form>
```

### Dans les contrôleurs:

```php
class StageController extends Controller
{
    // Le $stage est AUTOMATIQUEMENT trouvé et décrypté
    public function show(Stage $stage)
    {
        return view('admin.stages.show', compact('stage'));
    }

    // Pas besoin de faire:
    // $stage = Stage::find(decrypt_route_param($id));
}
```

---

## 🔒 Sécurité

### Points Clés:

1. **Cryptage** - Utilise `Crypt::encryptString()` de Laravel (AES-256)
2. **Clé** - Utilise l'`APP_KEY` du fichier `.env`
3. **Fallback** - Si décryptage échoue, traite comme ID normal (sauf avec URL modifiées)
4. **404** - Si l'ID trouvé n'existe pas, retourne 404 automatiquement

### Configuration `.env`:

```env
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_CIPHER=AES-256-CBC
```

Si vous n'avez pas de clé:

```bash
php artisan key:generate
```

---

## 📦 Modèles Supportés

Le système fonctionne avec ces modèles:

- ✅ `Stage`
- ✅ `Etudiant`
- ✅ `Badge`
- ✅ `Service`
- ✅ `Jour`
- ✅ `TypeStage`
- ✅ `Signataire`

Pour **ajouter un nouveau modèle**:

1. Ajoutez dans `app/Providers/AppServiceProvider.php`:

```php
use App\Models\VotreModele;

public function boot()
{
    // ...
    Route::bind('votremodele', function ($value) {
        return $this->resolveEncryptedModel($value, VotreModele::class);
    });
}
```

2. Utilisez dans la route:

```php
Route::get('{votremodele}', [...]);
```

3. Utilisez dans les vues:

```blade
{{ encrypted_route('votremodele.show', $votremodele) }}
```

---

## 🧪 Tests

### Test Simple:

1. Allez sur `/admin/stages`
2. Cliquez sur "Voir" ou "Modifier"
3. Vérifiez que l'URL est cryptée (commence par des caractères aléatoires)
4. Vérifiez que la page affiche le bon stage

### Debug:

Ajoutez dans `app/Providers/AppServiceProvider.php` si vous avez des problèmes:

```php
Route::bind('stage', function ($value) {
    \Log::debug("Stage binding received: $value");
    $result = $this->resolveEncryptedModel($value, Stage::class);
    \Log::debug("Stage binding resolved to ID: " . $result->id);
    return $result;
});
```

Puis vérifiez `storage/logs/laravel.log`

---

## ⚠️ Problèmes Courants

### "Not Found" (404)

- ✅ Vérifier que l'ID existe en base de données
- ✅ Vérifier que `APP_KEY` est configuré
- ✅ Vérifier que les routes utilisent le model binding `{stage}`

### URL pas cryptée

- ✅ Vérifier que vous utilisez `encrypted_route()` dans Blade
- ✅ Vérifier que la fonction est chargée (dans `app/Helpers/helpers.php`)
- ✅ Vérifier que `composer dump-autoload` a été exécuté

### Cryptage/Décryptage échoue

- ✅ Exécutez: `php artisan config:clear && php artisan cache:clear`
- ✅ Vérifiez que `APP_KEY` n'a pas changé

---

## 📚 Fichiers Clés

| Fichier                                | Responsabilité                                |
| -------------------------------------- | --------------------------------------------- |
| `app/Providers/AppServiceProvider.php` | Route Model Binding                           |
| `app/Helpers/helpers.php`              | Fonction `encrypted_route()`                  |
| `routes/web.php`                       | Routes                                        |
| `app/Http/Controllers/*`               | Contrôleurs reçoivent modèles automatiquement |

---

## ✅ Checklist Installation

- [x] `AppServiceProvider.php` - Route bindings configurés
- [x] `helpers.php` - Helper function ajoutée
- [x] `composer.json` - helpers.php ajouté à autoload
- [x] Routes - Utilisent le model binding `{stage}`, etc
- [x] Contrôleurs - Accèptent les modèles en paramètres
- [x] Vues - Utilisent `encrypted_route()` pour les liens

```bash
# Pour mettre en place:
composer dump-autoload
php artisan optimize:clear
php artisan config:cache
```

---

**C'est tout!** Le système fonctionne automatiquement. 🎉
