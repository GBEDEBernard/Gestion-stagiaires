# TODO - Notification emails (TFG + salutation + civilité)

## 1) Compréhension & emplacements

- [x] Identifier les templates email concernés (new_message, reviewed)
- [x] Identifier la source du nom d’application (config('app.name'))
- [x] Identifier la donnée genre disponible (champ `genre` dans `personnels` via `personnel`)

## 2) Implémentation salutation

- [ ] Calculer `greeting` (Bonjour/Bonsoir) selon l’heure serveur (timezone Africa/Lagos)
- [ ] Déterminer civilité `Monsieur`/`Madame` selon `genre` (Masculin/Féminin)
- [ ] Mettre à jour les templates Blade pour afficher :
    - `# {{ $greeting }} {{ $civility }} {{ $recipientName }},`

## 3) Implémentation nom application TFG

- [ ] Remplacer `env('APP_NAME', ...)` par `'(Technology forever group )TFG'`
- [ ] Vérifier que tous les mails utilisant `config('app.name')` affichent bien TFG

## 4) Tests

- [ ] Envoyer un exemple (new message + reviewed) et vérifier visuellement le rendu
