# TODO - Responsive Complet Projet

## ✅ Déjà responsive :

- Sidebar → Hamburger mobile (`navigation.blade.php`)
- Layout principal (`app.blade.php`)

## À améliorer (pages principales) :

- [ ] `resources/views/admin/presence/index.blade.php`
- [ ] `resources/views/presence/pointage.blade.php`
- [ ] `resources/views/presence/historique.blade.php`
- [ ] `resources/views/admin/users/index.blade.php`
- [ ] Forms admin (grid `md:grid-cols-2`)
- [ ] Tables (`overflow-x-auto` mobile)

## Étapes :

1. **Tables** : `min-w-full overflow-x-auto` + `whitespace-nowrap`
2. **Grilles** : `grid-cols-1 md:grid-cols-2 lg:grid-cols-3`
3. **Forms** : Labels/inputs stack mobile
4. **Cards** : `w-full sm:w-auto`
5. Test mobile/tablette

**Breakpoints** : `sm:` (640px), `md:` (768px), `lg:` (1024px)
