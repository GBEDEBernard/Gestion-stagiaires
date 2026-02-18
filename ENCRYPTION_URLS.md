# Guide d'Utilisation: URLs Chiffrées pour la Sécurité

## Vue d'ensemble

Ce système chiffre les IDs dans les URLs pour empêcher les utilisateurs de deviner facilement les identifiants. 

**Avant:** `http://127.0.0.1:8000/admin/badges/1`  
**Après:** `http://127.0.0.1:8000/admin/badges/eyJpdiI6ImdrcDJuVDVmZEdQYzBaMng...`

---

## 🔧 Configuration

Le système est **automatiquement** intégré grâce à:
1. ✅ Service d'encryptage: `App\Services\UrlEncrypter`
2. ✅ Middleware de décryption: `App\Http\Middleware\DecryptRouteParams`
3. ✅ Helpers: `App\Helpers\RouteHelper` et fonctions globales

---

## 📝 Utilisation dans les Vues (Blade)

### 1. **Fonction helper globale** (Recommandée - Plus simple)

```blade
<!-- Lien vers edit -->
<a href="{{ encrypted_route('badges.edit', $badge) }}">Éditer</a>

<!-- Lien vers show -->
<a href="{{ encrypted_route('badges.show', $badge) }}">Voir</a>

<!-- Lien vers destroy avec DELETE -->
<form action="{{ encrypted_route('badges.destroy', $badge) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit">Supprimer</button>
</form>
```

### 2. **Directives Blade** (Plus lisible)

```blade
<!-- Show -->
<a href="@route_show('badges', $badge)">Voir le badge</a>

<!-- Edit -->
<a href="@route_edit('badges', $badge)">Éditer le badge</a>

<!-- Destroy -->
<form action="@route_destroy('badges', $badge)" method="POST">
    @csrf
    @method('DELETE')
    <button>Supprimer</button>
</form>

<!-- Update -->
<form action="@route_update('badges', $badge)" method="POST">
    @csrf
    @method('PUT')
    <!-- ... -->
</form>
```

### 3. **Routes spécifiques pour Stages**

```blade
<!-- Voir le badge du stage -->
<a href="@route_stage_badge($stage)">Voir le badge</a>

<!-- Voir l'attestation du stage -->
<a href="@route_stage_attestation($stage)">Voir l'attestation</a>

<!-- Télécharger l'attestation -->
<a href="@route_stage_attestation_download($stage)" download>Télécharger</a>

<!-- Imprimer l'attestation -->
<a href="@route_stage_attestation_print($stage)" target="_blank">Imprimer</a>
```

---

## 🎯 Utilisation dans les Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Helpers\RouteHelper;
use App\Services\UrlEncrypter;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::paginate(10);
        return view('admin.badges.index', compact('badges'));
    }

    public function show($id)
    {
        // Le middleware déchiffre automatiquement l'ID
        // Donc $id est déjà l'ID réel (pas chiffré)
        $badge = Badge::findOrFail($id);
        return view('admin.badges.show', compact('badge'));
    }

    public function edit($id)
    {
        // Le middleware déchiffre automatiquement l'ID
        $badge = Badge::findOrFail($id);
        return view('admin.badges.edit', compact('badge'));
    }

    public function store(Request $request)
    {
        $badge = Badge::create($request->validated());
        
        // Redirection avec lien chiffré
        return redirect(RouteHelper::show('badges', $badge));
    }
}
```

---

## 🔐 Utilisation des Helpers Directement

### Encrypter un ID
```php
// Dans une vue
{{ encrypt_id($badge->id) }}

// Dans un controller
use App\Services\UrlEncrypter;
$encrypted = UrlEncrypter::encrypt($badge->id);
```

### Déchiffrer un ID
```php
// Utile en cas de besoin manuel
use App\Services\UrlEncrypter;
$id = UrlEncrypter::decrypt($encryptedValue);

// Ou la fonction helper
$id = decrypt_id($encryptedValue);
```

---

## 📋 Exemple Complet: Vue de liste avec liens

**resources/views/admin/badges/index.blade.php**

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Badges</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($badges as $badge)
            <tr>
                <td>{{ $badge->id }}</td>
                <td>{{ $badge->badge }}</td>
                <td>
                    <!-- Voir -->
                    <a href="{{ encrypted_route('badges.show', $badge) }}" class="btn btn-sm btn-info">
                        Voir
                    </a>
                    
                    <!-- Éditer -->
                    <a href="{{ encrypted_route('badges.edit', $badge) }}" class="btn btn-sm btn-primary">
                        Éditer
                    </a>
                    
                    <!-- Supprimer -->
                    <form action="{{ encrypted_route('badges.destroy', $badge) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression?')">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

---

## 🚀 Migration des URLs Existantes

Pour mettreà jour une vue existante, remplacez:

### ❌ Avant (URLs non sécurisées)
```blade
<a href="{{ route('badges.edit', $badge->id) }}">Éditer</a>
<a href="/admin/badges/{{ $badge->id }}">Voir</a>
```

### ✅ Après (URLs sécurisées)
```blade
<a href="{{ encrypted_route('badges.edit', $badge) }}">Éditer</a>
<a href="@route_show('badges', $badge)">Voir</a>
```

---

## 🔎 Détails Techniques

### Comment ça fonctionne ?

1. **Vue genère URL chiffrée:**
   - `{{ encrypted_route('badges.edit', $badge) }}`
   - Résultat: `/admin/badges/eyJpdiI6Ijh...` (ID chiffré)

2. **Navigateur envoie requête** avec URL chiffrée

3. **Middleware DecryptRouteParams:**
   - Intercepte la requête
   - Détecte les paramètres chiffrés
   - Les déchiffre automatiquement
   - Le controller reçoit l'ID normal

4. **Controller traite l'ID normal:**
   ```php
   public function edit($id) // $id est déjà l'ID réel
   {
       $badge = Badge::findOrFail($id);
   }
   ```

### Chiffrement / Déchiffrement

- **Algorithme:** AES-256-GCM (Laravel Encryption)
- **Clé:** Votre `APP_KEY` dans `.env`
- **Réversibilité:** Oui, chaque ID chiffré se déchiffre toujours au même ID

---

## ⚙️ Customisation

### Ajouter de nouvelles directives Blade

Modifiez `app/Providers/BladeServiceProvider.php`:

```php
// Nouvelle directive
Blade::directive('route_custom', function ($expression) {
    return "<?php echo RouteHelper::custom($expression); ?>";
});
```

### Personnaliser le service d'encryptage

Modifiez `app/Services/UrlEncrypter.php` pour changer l'algorithme (ex: utiliser Hashids).

---

## 📊 Ressources

- **Service principal:** [app/Services/UrlEncrypter.php](../Services/UrlEncrypter.php)
- **Helper Blade:** [app/Providers/BladeServiceProvider.php](../Providers/BladeServiceProvider.php)
- **Middleware:** [app/Http/Middleware/DecryptRouteParams.php](../Http/Middleware/DecryptRouteParams.php)
- **Helpers globaux:** [app/Helpers/helpers.php](../Helpers/helpers.php)

---

## ✅ Checklist d'implémentation

Remplacez les URLs dans ces fichiers:

- [ ] `resources/views/admin/badges/` - Utiliser `encrypted_route()` dans les liens
- [ ] `resources/views/admin/stages/` - Utiliser `encrypted_route()` 
- [ ] `resources/views/admin/etudiants/` - Utiliser `encrypted_route()`
- [ ] `resources/views/admin/jours/` - Utiliser `encrypted_route()`
- [ ] Tous les autres modèles...

---

## 🎓 Besoin d'aide ?

Consultez les exemples dans les vues existantes ou le fichier `ENCRYPTION_URLS.md` pour plus de détails.
