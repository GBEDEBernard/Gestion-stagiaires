#!/bin/bash
# Script de Vérification - Système de Cryptage des URLs

echo "=== Vérification du Système de Cryptage des URLs ==="
echo ""

# Vérifiez la structure
echo "1️⃣  Vérifier que AppServiceProvider.php contient Route::bind..."
if grep -q "Route::bind('stage'" app/Providers/AppServiceProvider.php; then
    echo "✅ AppServiceProvider configuré correctement"
else
    echo "❌ AppServiceProvider NOT configuré"
    exit 1
fi

echo ""
echo "2️⃣  Vérifier que helpers.php contient encrypted_route..."
if grep -q "function encrypted_route" app/Helpers/helpers.php; then
    echo "✅ helpers.php - encrypted_route() trouvée"
else
    echo "❌ helpers.php - encrypted_route() NOT trouvée"
    exit 1
fi

echo ""
echo "3️⃣  Vérifier que composer.json inclut helpers.php en autoload..."
if grep -q '"app/Helpers/helpers.php"' composer.json; then
    echo "✅ composer.json - helpers.php en autoload"
else
    echo "❌ composer.json - helpers.php NOT dans autoload"
    exit 1
fi

echo ""
echo "4️⃣  Vérifier que les vues utilisent encrypted_route()..."
STAGES_VIEW=$(grep -c "encrypted_route" resources/views/admin/stages/index.blade.php 2>/dev/null || echo 0)
if [ "$STAGES_VIEW" -ge 1 ]; then
    echo "✅ Vues stages - encrypted_route() trouvée ($STAGES_VIEW fois)"
else
    echo "❌ Vues stages - encrypted_route() NOT trouvée"
fi

echo ""
echo "5️⃣  Vérifier que le middleware bugué est supprimé..."
if ! grep -q "decrypt.route" routes/web.php; then
    echo "✅ routes/web.php - middleware bugué supprimé ✓"
else
    echo "❌ routes/web.php - middleware bugué ENCORE PRÉSENT"
fi

if ! grep -q "DecryptRouteParameter" bootstrap/app.php; then
    echo "✅ bootstrap/app.php - DecryptRouteParameter supprimé ✓"
else
    echo "❌ bootstrap/app.php - DecryptRouteParameter ENCORE PRÉSENT"
fi

echo ""
echo "6️⃣  Vérifier que StageController est nettoyé..."
if grep -q "getStageFromEncrypted" app/Http/Controllers/StageController.php; then
    echo "❌ StageController - getStageFromEncrypted ENCORE PRÉSENTE"
else
    echo "✅ StageController - nettoyé (getStageFromEncrypted supprimée)"
fi

if grep -q "public function show(Stage \$stage)" app/Http/Controllers/StageController.php; then
    echo "✅ StageController - show() utilise le model binding"
else
    echo "❌ StageController - show() n'utilise pas le model binding"
fi

echo ""
echo "7️⃣  Vérifier APP_KEY..."
if grep -q "^APP_KEY=" .env && [ ! -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
    echo "✅ APP_KEY configurée"
else
    echo "❌ APP_KEY NOT configurée - run: php artisan key:generate"
fi

echo ""
echo "========================================="
echo "✅ Vérifications complètes!"
echo ""
echo "Prochaines étapes:"
echo "1. composer dump-autoload"
echo "2. php artisan optimize:clear"
echo "3. php artisan config:cache"
echo "4. Tester: http://localhost:8000/admin/stages"
echo ""
echo "========================================="
