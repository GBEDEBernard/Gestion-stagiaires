- [ ] Corriger l’erreur PHP « espace de noms doit être la toute première instruction » dans `app/Mail/TaskNewMessageMail.php` (et vérifier si d’autres classes Mail ont le même problème).
- [ ] Relancer `php artisan optimize:clear` et un test d’envoi du mail concerné (ou redémarrer le serveur).

