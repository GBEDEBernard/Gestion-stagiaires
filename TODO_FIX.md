# TODO - Fix Pagination Error & Sidebar

## Plan d'action:

### 1. Fix JourController - Add Pagination

- [x] Modifier `JourController.php` - changer `Jour::all()` en `Jour::paginate()`

### 2. Fix SignataireController - Add Pagination

- [x] Modifier `SignataireController.php` - changer `Signataire::orderBy('ordre')->get()` en `Signataire::orderBy('ordre')->paginate()`

### 3. Fix TypeStageController - Add Pagination

- [x] Modifier `TypeStageController.php` - changer `TypeStage::all()` en `TypeStage::paginate(10)`

### 4. Add Corbeille to Sidebar Stages Submenu

- [x] Ajouter le lien "Corbeille" dans le sous-menu Stages de `navigation.blade.php`

---

## STATUT: TERMINÉ ✅

## Controllers deja corrects (pas de modification necessaire):

- ServiceController - utilise `paginate(10)`
- BadgeController - utilise `paginate(10)`
- EtudiantController - utilise `paginate(10)`
- StageController - utilise `paginate(5)`

## Détails des modifications:

### JourController.php

```php
// Avant
$jours = Jour::all();
// Après
$jours = Jour::paginate(10);
```

### SignataireController.php

```php
// Avant
$signataires = Signataire::orderBy('ordre')->get();
// Après
$signataires = Signataire::orderBy('ordre')->paginate(10);
```

### TypeStageController.php

```php
// Avant
$type_stages = TypeStage::all();
// Après
$type_stages = TypeStage::paginate(10);
```

### navigation.blade.php

- Ajouté un lien "Corbeille" dans le sous-menu Stages avec route `stages.trash`
