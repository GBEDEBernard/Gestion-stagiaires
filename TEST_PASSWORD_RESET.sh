#!/bin/bash
# Guide de test du système de réinitialisation de mot de passe

echo "==================================================="
echo "Test du système de réinitialisation de mot de passe"
echo "==================================================="
echo ""

echo "1️⃣  Démarrer le serveur Laravel"
echo "   php artisan serve"
echo ""

echo "2️⃣  Accéder à la page de connexion"
echo "   http://localhost:8000/login"
echo ""

echo "3️⃣  Cliquer sur 'Mot de passe oublié ?'"
echo "   → Vous serez redirigé vers /forgot-password"
echo ""

echo "4️⃣  Entrer votre adresse email"
echo "   → Un email avec un lien de réinitialisation sera envoyé"
echo ""

echo "5️⃣  Consulter les logs pour voir l'email"
echo "   tail -f storage/logs/laravel.log"
echo "   (en développement, MAIL_MAILER=log enregistre les emails)"
echo ""

echo "6️⃣  Cliquer sur le lien dans l'email"
echo "   → La page de réinitialisation s'affichera"
echo ""

echo "7️⃣  Entrer un nouveau mot de passe"
echo "   → Avec l'icône d'œil pour afficher/masquer"
echo ""

echo "8️⃣  Confirmer le mot de passe et soumettre"
echo "   → Vous serez redirigé vers la page de connexion"
echo "   → Un message de succès s'affichera"
echo ""

echo "9️⃣  Vous pouvez maintenant vous connecter avec le nouveau mot de passe"
echo ""

echo "==================================================="
echo "✅ Processus de réinitialisation terminé !"
echo "==================================================="
