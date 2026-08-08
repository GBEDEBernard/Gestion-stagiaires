<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Libellés personnalisés pour les permissions
    |--------------------------------------------------------------------------
    |
    | Vous pouvez définir ici des libellés personnalisés pour chaque permission.
    | Si aucun libellé n'est défini, le service PermissionLabelService générera
    | un libellé automatiquement à partir du nom de la permission.
    |
    */

    'users.view' => 'Voir les utilisateurs',
    'users.create' => 'Créer des utilisateurs',
    'users.edit' => 'Modifier des utilisateurs',
    'users.delete' => 'Supprimer des utilisateurs',
    'users.restore' => 'Restaurer des utilisateurs',
    'users.force-delete' => 'Supprimer définitivement des utilisateurs',

    'roles.view' => 'Voir les rôles',
    'roles.create' => 'Créer des rôles',
    'roles.edit' => 'Modifier des rôles',
    'roles.delete' => 'Supprimer des rôles',

    'etudiants.view' => 'Voir les étudiants',
    'etudiants.create' => 'Créer des étudiants',
    'etudiants.edit' => 'Modifier des étudiants',
    'etudiants.delete' => 'Supprimer des étudiants',
    'etudiants.restore' => 'Restaurer des étudiants',
    'etudiants.force-delete' => 'Supprimer définitivement des étudiants',

    'stages.view' => 'Voir les stages',
    'stages.create' => 'Créer des stages',
    'stages.edit' => 'Modifier des stages',
    'stages.delete' => 'Supprimer des stages',
    'stages.restore' => 'Restaurer des stages',

    'badges.view' => 'Voir les badges',
    'badges.create' => 'Créer des badges',
    'badges.edit' => 'Modifier des badges',
    'badges.delete' => 'Supprimer des badges',
    'badges.download' => 'Télécharger des badges',

    'attestation.view' => 'Voir les attestations',
    'attestation.create' => 'Créer des attestations',
    'attestation.approve' => 'Approuver les attestations',
    'attestation.download' => 'Télécharger des attestations',
    'attestation.print' => 'Imprimer des attestations',

    'presence.view' => 'Voir les présences',
    'presence.checkin' => 'Pointer les arrivées',
    'presence.checkout' => 'Pointer les départs',

    'domaines.view' => 'Voir les domaines',
    'domaines.create' => 'Créer des domaines',
    'domaines.edit' => 'Modifier des domaines',
    'domaines.delete' => 'Supprimer des domaines',

    'sites.view' => 'Voir les sites',
    'sites.create' => 'Créer des sites',
    'sites.edit' => 'Modifier des sites',
    'sites.delete' => 'Supprimer des sites',

    'tasks.view' => 'Voir les tâches',
    'tasks.create' => 'Créer des tâches',
    'tasks.edit' => 'Modifier des tâches',
    'tasks.delete' => 'Supprimer des tâches',
    'tasks.assign' => 'Assigner des tâches',
    'tasks.review' => 'Valider des tâches',

    'signataires.view' => 'Voir les signataires',
    'signataires.create' => 'Créer des signataires',
    'signataires.edit' => 'Modifier des signataires',
    'signataires.delete' => 'Supprimer des signataires',

    'corbeille.view' => 'Voir la corbeille',

    'jour_stage.view' => 'Voir les jours de stage',
    'jour_stage.create' => 'Créer des jours de stage',
    'jour_stage.edit' => 'Modifier des jours de stage',
    'jour_stage.delete' => 'Supprimer des jours de stage',

    'type_stages.view' => 'Voir les types de stage',
    'type_stages.create' => 'Créer des types de stage',
    'type_stages.edit' => 'Modifier des types de stage',
    'type_stages.delete' => 'Supprimer des types de stage',

    'employes.view' => 'Voir les employés',
    'employes.create' => 'Créer des employés',
    'employes.edit' => 'Modifier des employés',
    'employes.delete' => 'Supprimer des employés',

    'personnels.view' => 'Voir le personnel',
    'personnels.create' => 'Créer du personnel',
    'personnels.edit' => 'Modifier le personnel',
    'personnels.delete' => 'Supprimer du personnel',

    'daily_reports.view' => 'Voir les rapports journaliers',

    'holidays.view' => 'Voir les jours fériés',
    'holidays.create' => 'Créer des jours fériés',
    'holidays.edit' => 'Modifier des jours fériés',
    'holidays.delete' => 'Supprimer des jours fériés',
    'holidays.toggle' => 'Activer/désactiver un jour férié',
    'holidays.bypass' => 'Pointer les jours fériés (exception)',

    'attendance_anomalies.view' => 'Voir les anomalies de présence',
    'attendance_anomalies.audit' => 'Auditer les anomalies de présence',
    'attendance_anomalies.review' => 'Revoir les anomalies de présence',

    'badges.approve' => 'Approuver les badges',
    'badges.audit' => 'Auditer les badges',
    'badges.cancel' => 'Annuler des badges',
    'badges.checkin' => 'Pointer arrivée badges',
    'badges.checkout' => 'Pointer départ badges',
    'badges.review' => 'Revoir les badges',
    'badges.submit' => 'Soumettre des badges',
    'badges.force-delete' => 'Supprimer définitivement des badges',
    'badges.restore' => 'Restaurer des badges',
    'badges.print' => 'Imprimer des badges',

    'domaines.approve' => 'Approuver les domaines',
    'domaines.audit' => 'Auditer les domaines',
    'domaines.cancel' => 'Annuler des domaines',
    'domaines.checkin' => 'Pointer arrivée domaines',
    'domaines.checkout' => 'Pointer départ domaines',
    'domaines.download' => 'Télécharger des domaines',
    'domaines.force-delete' => 'Supprimer définitivement des domaines',
    'domaines.print' => 'Imprimer des domaines',
    'domaines.restore' => 'Restaurer des domaines',
    'domaines.review' => 'Revoir les domaines',
    'domaines.submit' => 'Soumettre des domaines',

    'presence_stats.view' => 'Voir les statistiques de présence',
    'presence_stats.create' => 'Créer des statistiques',
    'presence_stats.edit' => 'Modifier les statistiques',
    'presence_stats.delete' => 'Supprimer les statistiques',

    'qr_code.view' => 'Voir les QR codes',
    'qr_code.create' => 'Créer des QR codes',
    'qr_code.edit' => 'Modifier des QR codes',
    'qr_code.delete' => 'Supprimer des QR codes',
    'qr_code.download' => 'Télécharger des QR codes',
    'qr_code.print' => 'Imprimer des QR codes',

    'services.view' => 'Voir les services',
    'services.create' => 'Créer des services',
    'services.edit' => 'Modifier des services',
    'services.delete' => 'Supprimer des services',
    'services.approve' => 'Approuver les services',
    'services.review' => 'Revoir les services',

    'signataires.approve' => 'Approuver les signataires',
    'signataires.audit' => 'Auditer les signataires',
    'signataires.cancel' => 'Annuler des signataires',
    'signataires.review' => 'Revoir les signataires',
    'signataires.submit' => 'Soumettre des signataires',
    'signataires.download' => 'Télécharger des signataires',
    'signataires.print' => 'Imprimer des signataires',
    'signataires.restore' => 'Restaurer des signataires',
    'signataires.force-delete' => 'Supprimer définitivement des signataires',
    'signataires.checkin' => 'Pointer arrivée signataires',
    'signataires.checkout' => 'Pointer départ signataires',

    'signer_attestation' => 'Signer les attestations',

    'permissions.view' => 'Voir les permissions',
    'permissions.create' => 'Créer des permissions',
    'permissions.approve' => 'Approuver les permissions',
    'permissions.cancel' => 'Annuler des permissions',
    'permissions.review' => 'Revoir les permissions',

    'daily_reports.view' => 'Voir les rapports journaliers',
    'daily_reports.create' => 'Créer des rapports journaliers',
    'daily_reports.approve' => 'Approuver les rapports journaliers',
    'daily_reports.review' => 'Revoir les rapports journaliers',
    'daily_reports.submit' => 'Soumettre des rapports journaliers',
];

