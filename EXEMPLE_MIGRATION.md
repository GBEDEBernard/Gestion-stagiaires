# 📝 Exemple: Migration d'une Vue

## Vue Avant (Non sécurisée)

**File:** resources/views/admin/jours/index.blade.php

```blade
<x-app-layout>
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900">Jours de Stage</h1>
            <a href="{{ route('jours.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                + Ajouter
            </a>
        </div>

        <div class="overflow-x-auto shadow rounded-lg">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">Jour</th>
                        <th class="px-6 py-3"># Stagiaires</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jours as $jour)
                    <tr class="border-b">
                        <td class="px-6 py-4">{{ $jour->jour }}</td>
                        <td class="px-6 py-4">{{ $jour->stages->count() }}</td>
                        <td class="px-6 py-4 text-center">
                            <!-- ❌ URLs non sécurisées -->
                            <a href="{{ route('jours.edit', $jour->id) }}" class="btn btn-yellow">
                                Éditer
                            </a>
                            <form action="{{ route('jours.destroy', $jour->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-red">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
```

---

## Vue Après (Sécurisée)

**File:** resources/views/admin/jours/index.blade.php

```blade
<x-app-layout>
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900">Jours de Stage</h1>
            <a href="{{ route('jours.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                + Ajouter
            </a>
        </div>

        <div class="overflow-x-auto shadow rounded-lg">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">Jour</th>
                        <th class="px-6 py-3"># Stagiaires</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jours as $jour)
                    <tr class="border-b">
                        <td class="px-6 py-4">{{ $jour->jour }}</td>
                        <td class="px-6 py-4">{{ $jour->stages->count() }}</td>
                        <td class="px-6 py-4 text-center">
                            <!-- ✅ URLs sécurisées avec chiffrement -->
                            <a href="{{ encrypted_route('jours.edit', $jour) }}" class="btn btn-yellow">
                                Éditer
                            </a>
                            <form action="{{ encrypted_route('jours.destroy', $jour) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-red">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
```

---

## 🔍 Changements Effectués

### 1. URL d'édition
```diff
- <a href="{{ route('jours.edit', $jour->id) }}" ...>
+ <a href="{{ encrypted_route('jours.edit', $jour) }}" ...>
```

**Explication:**
- ❌ `route('jours.edit', $jour->id)` - Génère: `/admin/jours/1`
- ✅ `encrypted_route('jours.edit', $jour)` - Génère: `/admin/jours/eyJpdiI6IlpkdGVDM0...`

### 2. URL de delete
```diff
- <form action="{{ route('jours.destroy', $jour->id) }}" ...>
+ <form action="{{ encrypted_route('jours.destroy', $jour) }}" ...>
```

---

## 📋 Règles Importantes

### ✅ DO's (À Faire)

```blade
<!-- ✅ Correct: Passer l'objet entier -->
<a href="{{ encrypted_route('jours.edit', $jour) }}">Éditer</a>

<!-- ✅ Correct: Passer l'ID et laisser la magie -->
<a href="{{ encrypted_route('jours.edit', $jour->id) }}">Éditer</a>

<!-- ✅ Correct: Utiliser les directives -->
<a href="@route_edit('jours', $jour)">Éditer</a>

<!-- ✅ Correct: Dans les controllers, recevoir les IDs normaux -->
public function edit($id) {
    $jour = Jour::findOrFail($id); // $id est déjà normal (1, 2, 3...)
}
```

### ❌ DON'Ts (À Éviter)

```blade
<!-- ❌ MAUVAIS: Passer $jour->id à route() -->
<a href="{{ route('jours.edit', $jour->id) }}">Éditer</a>

<!-- ❌ MAUVAIS: URL en dur -->
<a href="/admin/jours/{{ $jour->id }}">Éditer</a>

<!-- ❌ MAUVAIS: Encrypter manuellement -->
<a href="/admin/jours/{{ encrypt_id($jour->id) }}">Éditer</a>

<!-- ❌ MAUVAIS: Mélanger les deux -->
<a href="{{ encrypted_route('jours.edit', encrypt_id($jour->id)) }}">Éditer</a>
```

---

## 🧪 Test d'Intégration

Après avoir mis à jour une vue, testez:

```bash
# 1. Ouvrez le site
http://localhost:8000/admin/jours

# 2. Observez les URLs dans les liens
# Devrait voir: /admin/jours/eyJpdiI6IlpkdGVDM0...

# 3. Cliquez sur "Éditer"
# Devrait charger la page d'édition normalement
# Les IDs sont automatiquement déchiffrés

# 4. Vérifiez que l'édition fonctionne
# Le controller reçoit l'ID normal (1, 2, 3...)
```

---

## 📝 Changelg Avancé

### Si vous avez des paramètres additionnels

```blade
<!-- Exemple: Ajouter des paramètres de requête -->
<a href="{{ encrypted_route('jours.edit', $jour, ['tab' => 'settings']) }}">
    Éditer
</a>
```

### Si vous avez des routes imbriquées

```blade
<!-- Exemple: Stage > Badge -->
<!-- Au lieu de: route('stages.badge', $stage->id) -->
<a href="@route_stage_badge($stage)">
    Voir Badge
</a>
```

---

## ✅ Checklist de Migration

Pour chaque fichier à migrer:

- [ ] Identifier tous les `route()` appels avec `->id`
- [ ] Remplacer par `encrypted_route()` ou directives Blade
- [ ] Tester le lien en cliquant
- [ ] Vérifier que l'action fonctionne (edit, update, delete)
- [ ] Tester sur mobile et desktop
- [ ] Valider que les confirmations de suppression marchent

---

## 🎯 Fichiers à Migrer en Priorité

1. **Critiques (URLs publiques):**
   - `resources/views/admin/badges/index.blade.php` ✅ (Déjà fait)
   - `resources/views/admin/stages/index.blade.php` ✅ (Déjà fait)

2. **Importants (APIs internes):**
   - `resources/views/admin/etudiants/index.blade.php`
   - `resources/views/admin/services/index.blade.php`
   - `resources/views/admin/jours/index.blade.php`

3. **Secondaires:**
   - Tous les fichiers `edit.blade.php`
   - Tous les fichiers `show.blade.php`
   - Les modals et popups

---

**Questions?** Vérifiez [ENCRYPTION_URLS.md](../ENCRYPTION_URLS.md) pour plus d'exemples.
