@php
    $isApproved         = in_array($status, ['approved', 'success'], true);
    $isAlreadyCompleted = $status === 'already_completed';
    $isRejected         = !$isApproved && !$isAlreadyCompleted;

    $forgottenDeparture = $forgotten_departure ?? null;
    $showDepartureReminder = $isApproved && $forgottenDeparture !== null;

    $event      = $event ?? null;
    // Un refus qui n'a pas d'événement derrière lui (compte désactivé, compte
    // non habilité) porte son propre code : sinon l'écran retomberait sur le
    // titre générique et la consigne disparaîtrait.
    $reasonCode = $event?->reason_code ?? ($reasonCode ?? null);

    // Un refus est d'abord une consigne : ce qui s'est passé, puis ce que la
    // personne peut faire dans les secondes qui suivent, devant la porte.
    $reasons = [
        'gps_accuracy_low' => [
            'Signal GPS insuffisant',
            "Votre position n'a pas pu être confirmée avec assez de précision. Rapprochez-vous d'une fenêtre ou sortez quelques secondes, puis scannez à nouveau.",
        ],
        'no_gps' => [
            'Position non transmise',
            "Aucune coordonnée n'a été reçue. Activez la localisation de votre téléphone, puis scannez à nouveau.",
        ],
        'outside_geofence' => [
            'Hors de la zone du site',
            "Vous vous trouvez trop loin de l'entrée pour que la présence soit enregistrée. Rapprochez-vous du bâtiment et scannez à nouveau.",
        ],
        'no_geofence' => [
            'Zone de pointage non configurée',
            "Aucune zone de présence n'est définie pour ce site. Signalez-le à votre administrateur, il pourra enregistrer votre présence manuellement.",
        ],
        'missing_geofence' => [
            'Zone de pointage non configurée',
            "Aucune zone de présence n'est définie pour ce site. Signalez-le à votre administrateur, il pourra enregistrer votre présence manuellement.",
        ],
        'duplicate_checkin' => [
            'Arrivée déjà enregistrée',
            "Votre arrivée a déjà été validée aujourd'hui. Aucun nouveau pointage n'est nécessaire.",
        ],
        'duplicate_checkout' => [
            'Départ déjà enregistré',
            "Votre départ a déjà été validé aujourd'hui.",
        ],
        'checkout_without_checkin' => [
            'Aucune arrivée enregistrée',
            "Un départ ne peut être enregistré sans arrivée correspondante. Contactez votre responsable pour régulariser la journée.",
        ],
        'departure_correction_pending' => [
            'Pointage bloqué — départ à régulariser',
            "Un départ que vous n'avez pas pointé n'a pas été déclaré dans les délais prévus pour le faire vous-même. Votre pointage est bloqué jusqu'à ce qu'un administrateur rétablisse votre heure de sortie. Présentez-vous à votre responsable : il débloquera votre pointage en quelques minutes.",
        ],
        'stage_inactive' => [
            'Stage hors période',
            "Votre stage n'est pas actif à cette date. Contactez votre administrateur si les dates doivent être prolongées.",
        ],
        'account_inactive' => [
            'Compte désactivé',
            "Votre compte est désactivé : aucun pointage n'est enregistré. Contactez votre administrateur pour être réactivé.",
        ],
        'not_pointable' => [
            'Compte non habilité au pointage',
            "Vous êtes connecté avec un compte d'administration ou de supervision, qui ne relève pas du pointage. Si vous exercez aussi un poste sur le terrain, demandez le rôle « Employé » à votre administrateur : votre présence sera alors enregistrée normalement.",
        ],
    ];

    // Les refus d'horaire arrivent en ValidationException : sans code, ils
    // retomberaient tous sur le même texte générique. On les rattache donc à
    // leur contrôle d'origine pour que la porte dise l'heure à laquelle
    // repasser, plutôt que « pointage non enregistré ».
    $scheduleReasons = [
        'checkin_before_opening' => [
            'Trop tôt pour pointer',
            "Le pointage du jour ouvre à 07h30. Revenez à partir de 07h30, votre présence sera enregistrée normalement.",
        ],
        'after_expected_departure' => [
            'Journée de présence terminée',
            "Les horaires de présence d'aujourd'hui sont écoulés. Si vous êtes resté après l'heure prévue, prévenez votre responsable : il régularisera la journée.",
        ],
        'not_a_work_day' => [
            "Aujourd'hui n'est pas un jour de présence",
            "Votre planning ne prévoit pas de présence aujourd'hui. Le pointage rouvrira à votre prochain jour de travail.",
        ],
        'holiday' => [
            'Jour férié',
            "Le pointage est fermé aujourd'hui, jour férié. Aucune présence n'est attendue.",
        ],
        'pointage_not_started' => [
            'Pointage pas encore actif',
            "Votre prise de poste n'a pas commencé. Le pointage s'ouvrira à la date indiquée par votre responsable.",
        ],
        'retard_permission' => [
            'Permission de retard en cours',
            "Une permission justifie votre arrivée tardive à partir de l'heure prévue. Revenez à ce moment-là : votre pointage sera accepté.",
        ],
    ];

    [$errorTitle, $errorText] = $reasons[$reasonCode]
        ?? $scheduleReasons[$reasonCode]
        ?? ['Pointage non enregistré', $message ?? "Votre présence n'a pas pu être validée."];

    if ($isApproved) {
        $heading = $eventType === 'check_in' ? 'Arrivée enregistrée' : 'Départ enregistré';
    } elseif ($isAlreadyCompleted) {
        $heading = 'Journée déjà complète';
    } else {
        $heading = $errorTitle;
    }

    $prenom   = $user?->prenom ?? $user?->name ?? null;
    $salut    = now()->hour < 18 ? 'Bonjour' : 'Bonsoir';

    $forgottenDeparture = $forgotten_departure ?? null;
    $showDepartureReminder = $isApproved && $forgottenDeparture !== null && $eventType === 'check_in';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f5f4ee">
    <title>{{ $heading }} — {{ $site->name ?? 'Pointage' }}</title>
    @include('presence.qr.partials.theme')
</head>
<body>

    <main class="card">

        <p class="eyebrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>{{ $site->name ?? 'Site' }}</span>
        </p>

        @if($isApproved)
            <div class="badge badge--success">
                <svg class="draw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12.5l4.5 4.5L19 7"/>
                </svg>
            </div>
        @elseif($isAlreadyCompleted)
            <div class="badge badge--info">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>
        @else
            <div class="badge badge--danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v5"/>
                    <path d="M12 16.5v.01"/>
                </svg>
            </div>
        @endif

        @if($prenom)
            <p class="greeting">{{ $salut }}, <strong>{{ $prenom }}</strong></p>
        @endif

        <p class="clock">{{ $time ?? now()->format('H:i') }}</p>
        <p class="clock-date">{{ now()->isoFormat('dddd Do MMMM') }}</p>

        <h1 class="title">{{ $heading }}</h1>

        @if($isRejected)
            <div class="notice notice--danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v4"/>
                    <path d="M12 17v.01"/>
                    <path d="M10.3 4.3L2.6 17.6A2 2 0 004.3 20.6h15.4a2 2 0 001.7-3L13.7 4.3a2 2 0 00-3.4 0z"/>
                </svg>
                <div class="notice-body">
                    {{-- Le titre est déjà porté par le <h1> : ici, uniquement la marche à suivre. --}}
                    <p class="notice-text">{{ $errorText }}</p>
                </div>
            </div>
        @elseif($isAlreadyCompleted)
            <div class="notice">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-tertiary)">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 11v5"/><path d="M12 8v.01"/>
                </svg>
                <div class="notice-body">
                    <p class="notice-text">{{ $message ?? "Vos deux pointages du jour sont déjà validés." }}</p>
                </div>
            </div>
        @endif

        @if($showDepartureReminder && $forgottenDeparture)
            <div class="notice notice--warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
                </svg>
                <div class="notice-body">
                    <p class="notice-text">
                        Hier, votre départ n'a pas été pointé.
                        La journée du <strong>{{ $forgottenDeparture->attendance_date->format('d/m/Y') }}</strong>
                        a été clôturée d'office.
                        Déclarez votre heure réelle de départ pour que votre responsable la valide.
                    </p>
                    <p class="mt-3">
                        <a href="{{ route('presence.depart-oublie') }}" class="btn-ghost">
                            Déclarer mon départ oublié
                        </a>
                    </p>
                </div>
            </div>
        @endif

        {{-- L'heure et la date sont portées par l'horloge, le lieu par la
             pastille en tête : il ne reste que la nature du pointage. --}}
        @if($isApproved)
            <dl class="details">
                <div class="row">
                    <dt>Type</dt>
                    <dd>{{ $eventType === 'check_in' ? 'Arrivée' : 'Départ' }}</dd>
                </div>
            </dl>
        @endif

        <p class="actions">
            <a class="btn-ghost" href="{{ route('presence.historique') }}">Mon historique</a>
        </p>

        @if($isApproved)
            <p class="footnote">Votre présence est enregistrée. Vous pouvez fermer cette page.</p>
        @elseif($isAlreadyCompleted)
            <p class="footnote">Rien de plus à faire aujourd'hui. À demain.</p>
        @else
            <p class="footnote">
                Si le problème persiste, signalez-le à votre responsable de site : votre présence
                pourra être régularisée manuellement.
            </p>
        @endif

    </main>

</body>
</html>
