#!/bin/bash

# Script pour trouver toutes les URLs non sécurisées et suggérer les remplacements

echo "🔍 Cherchant les URLs non sécurisées dans les vues..."
echo ""

# Chercher les patterns route() avec $xx->id dans les vues
echo "❌ Routes avec IDs non chiffré détectés:"
echo ""

grep -r "route(" resources/views/admin --include="*.blade.php" | \
grep -E "\\\$[a-zA-Z]+->id|\\\$[a-zA-Z]+\)" | \
grep -v "encrypted_route" | while read line; do
    file=$(echo "$line" | cut -d: -f1)
    echo "📄 $file"
    echo "   $line" | sed 's/^[^:]*:/   /'
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Fichiers déjà convertis:"
echo ""

grep -r "encrypted_route" resources/views --include="*.blade.php" | \
cut -d: -f1 | sort -u | while read file; do
    echo "✓ $file"
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📋 Conseils pour la migration:"
echo ""
echo "1. Remplacez: route('resource.edit', \$model->id)"
echo "   Par:       encrypted_route('resource.edit', \$model)"
echo ""
echo "2. Remplacez: route('resource.show', \$model->id)" 
echo "   Par:       @route_show('resource', \$model)"
echo ""
echo "3. Pour les stages avec badges/attestations:"
echo "   Par:       @route_stage_badge(\$stage)"
echo "   Par:       @route_stage_attestation(\$stage)"
echo ""
