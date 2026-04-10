# Configuration du système de réinitialisation de mot de passe

## ✅ Implémentation complète

Le système de réinitialisation de mot de passe a été entièrement configuré avec les éléments suivants :

### 1. **Vues (Blade Templates)**

- **forgot-password.blade.php** - Page pour demander la réinitialisation
- **reset-password.blade.php** - Page pour créer un nouveau mot de passe avec affichage/masquage

### 2. **Base de données**

- Migration `password_reset_tokens` table créée pour stocker les tokens de réinitialisation

### 3. **Notification**

- `ResetPasswordNotification.php` - Notification personnalisée en français

### 4. **Routes (existantes)**

- `GET /forgot-password` - Affiche le formulaire de demande
- `POST /forgot-password` - Traite la demande d'email
- `GET /reset-password/{token}` - Affiche le formulaire de réinitialisation
- `POST /reset-password` - Valide et change le mot de passe

---

## ⚙️ Configuration du mail

### En développement (LOCAL)

Le fichier `.env` actuel utilise `MAIL_MAILER=log`, ce qui signifie que les emails sont enregistrés dans les logs au lieu d'être envoyés.

Pour **voir les emails de test** :

```bash
tail -f storage/logs/laravel.log
```

### En production

Modifiez votre `.env` pour utiliser un service SMTP (Mailtrap, SendGrid, Gmail, etc.):

**Exemple avec Mailtrap** :

```env
MAIL_MAILER=smtp
MAIL_HOST=send.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@gestion-stagiaires.app"
MAIL_FROM_NAME="Gestion Stagiaires"
```

**Exemple avec Gmail** :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="votre_email@gmail.com"
MAIL_FROM_NAME="Gestion Stagiaires"
```

---

## 🚀 Déploiement de la migration

Avant de tester, exécutez la migration :

```bash
php artisan migrate
```

---

## ✨ Fonctionnalités

✅ Page "Mot de passe oublié" avec design cohérent
✅ Email de réinitialisation en français
✅ Page de réinitialisation avec affichage/masquage du mot de passe
✅ Tokens sécurisés et expiration automatique
✅ Validation des données
✅ Responsive et moderne avec Tailwind CSS

---

## 🔒 Sécurité

- Les tokens expirent après 60 minutes
- Les mots de passe sont hashés avec bcrypt
- Les confirmations de mot de passe sont validées
- Protection CSRF sur tous les formulaires
