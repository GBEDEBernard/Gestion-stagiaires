{{--
    Le scan du QR code de porte aboutit sur la carte de pointage — la même que
    dans l'application. Une seule interface à apprendre, et l'écran intermédiaire
    « Localisation en cours » disparaît : on voit son heure, son état du jour,
    et on appuie.

    La page est autonome (pas de layout applicatif) : le badge autorise à
    pointer, pas à entrer dans le compte. Personne n'est connecté ici.
--}}
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f9fafb">
    <title>Pointage — {{ $site->name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">

    <div class="min-h-full flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">

            @include('presence.partials.carte', [
                'lieu'          => $site->name,
                'prenom'        => $prenom,
                'day'           => $day,
                'expIn'         => $expIn,
                'expOut'        => $expOut,
                'etat'          => $etat,
                'late'          => $late,
                'departBloque'  => $departBloque,
                'action'        => route('presence.qr.process', ['site_token' => $site->qr_token]),
                'champs'        => ['user_id' => $user->id, 'device_token' => $deviceToken],
                'isWorkDay'     => $isWorkDay,
                'workDaysLabel' => $workDaysLabel,
                {{-- Le badge ne donne pas accès au compte : ni historique, ni
                     déclaration de départ oublié. Celle-ci sera demandée à la
                     prochaine ouverture de l'application. --}}
                'historiqueUrl'  => null,
                'journeeOubliee' => null,
                'declarationUrl' => null,
            ])

            <p class="mt-5 text-center text-xs text-gray-400 dark:text-gray-500">
                Appareil reconnu comme badge de {{ $prenom }}.
            </p>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
