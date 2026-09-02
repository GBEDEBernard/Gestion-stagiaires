<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Note d'assiduité calculée
    |--------------------------------------------------------------------------
    |
    | Pondérations du critère automatique, sur une note ramenée à 20 :
    |
    |   assiduité/20 = 20 × ( presence · taux_présence
    |                       + punctuality · taux_ponctualité
    |                       + completeness · taux_journées_complètes )
    |
    | La somme doit valoir 1. Ces valeurs sont un réglage métier, pas une
    | constante technique : elles se discutent avec la direction, et changer
    | d'avis ne doit pas demander un déploiement.
    |
    */

    'attendance_weights' => [
        'presence'     => 0.50,
        'punctuality'  => 0.30,
        'completeness' => 0.20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Note maximale
    |--------------------------------------------------------------------------
    */

    'max_score' => 20,

];
