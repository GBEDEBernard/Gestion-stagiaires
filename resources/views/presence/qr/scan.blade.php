@php
    $fullName = trim(($user->prenom ?? '') . ' ' . ($user->nom ?? $user->name ?? ''));
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f5f4ee">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pointage en cours — {{ $site->name }}</title>
    @include('presence.qr.partials.theme')
    <style>
        .spinner {
            width: 20px;
            height: 20px;
            border: 1.75px solid var(--border-strong);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 700ms linear infinite;
            flex: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .progress {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 0 20px;
        }
        .progress-text { min-width: 0; }
        .progress-title {
            margin: 0 0 2px;
            font-size: 14px;
            font-weight: 500;
        }
        .progress-hint {
            margin: 0;
            font-size: 13px;
            color: var(--text-secondary);
        }

        [hidden] { display: none !important; }
    </style>
</head>
<body>

    <main class="card">

        <p class="eyebrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>{{ $site->name }}</span>
        </p>

        <h1 class="title">Bonjour {{ $user->prenom ?? $user->name }}</h1>
        <p class="subtitle">Nous vérifions que vous êtes bien sur le site.</p>

        {{-- État de progression --}}
        <div id="progress" class="progress">
            <div class="spinner" role="status" aria-live="polite"></div>
            <div class="progress-text">
                <p class="progress-title" id="statusText">Localisation en cours</p>
                <p class="progress-hint" id="subStatusText">Autorisez l'accès à votre position si votre téléphone le demande.</p>
            </div>
        </div>

        {{-- État d'erreur --}}
        <div id="errorBlock" hidden>
            <div class="notice notice--danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v4"/>
                    <path d="M12 17v.01"/>
                    <path d="M10.3 4.3L2.6 17.6A2 2 0 004.3 20.6h15.4a2 2 0 001.7-3L13.7 4.3a2 2 0 00-3.4 0z"/>
                </svg>
                <div class="notice-body">
                    <p class="notice-title" id="errorTitle">Localisation impossible</p>
                    <p class="notice-text" id="errorMessage"></p>
                </div>
            </div>
            <button type="button" class="btn btn--primary" onclick="requestGpsAndSubmit()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 0115.5-6.2M21 12a9 9 0 01-15.5 6.2"/>
                    <path d="M18.5 3.5v2.3h-2.3M5.5 20.5v-2.3h2.3"/>
                </svg>
                <span>Réessayer</span>
            </button>
        </div>

        <form id="pointageForm" action="{{ route('presence.qr.process', ['site_token' => $site->qr_token]) }}" method="POST" hidden>
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <input type="hidden" name="device_token" value="{{ $device_token }}">
            <input type="hidden" name="latitude" id="inputLat">
            <input type="hidden" name="longitude" id="inputLng">
            <input type="hidden" name="accuracy_meters" id="inputAccuracy">
            <input type="hidden" name="device_fingerprint" id="inputFingerprint">
            <input type="hidden" name="device_uuid" id="inputUuid">
            <input type="hidden" name="device_label" id="inputLabel">
            <input type="hidden" name="platform" id="inputPlatform">
            <input type="hidden" name="browser" id="inputBrowser">
        </form>

        <p class="footnote">
            Votre position sert uniquement à confirmer votre présence sur le site
            au moment du pointage. Elle n'est pas suivie en dehors de ce scan.
        </p>

    </main>

    <script>
        function getDeviceUuid() {
            let uuid = localStorage.getItem('tfg_device_uuid');
            if (!uuid) {
                uuid = 'dev_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now().toString(36);
                localStorage.setItem('tfg_device_uuid', uuid);
            }
            return uuid;
        }

        function getDeviceFingerprint() {
            const screenInfo = `${window.screen.width}x${window.screen.height}x${window.screen.colorDepth}`;
            const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
            const navInfo = `${navigator.userAgent}|${navigator.language}|${navigator.hardwareConcurrency || 2}`;
            const raw = `${getDeviceUuid()}|${screenInfo}|${timeZone}|${navInfo}`;

            // Simple hash for fingerprint
            let hash = 0;
            for (let i = 0; i < raw.length; i++) {
                hash = ((hash << 5) - hash) + raw.charCodeAt(i);
                hash |= 0;
            }
            return 'fp_' + Math.abs(hash).toString(36) + '_' + getDeviceUuid().substring(0, 8);
        }

        function detectPlatform() {
            const ua = navigator.userAgent;
            if (/android/i.test(ua)) return 'Android';
            if (/iPhone|iPad|iPod/i.test(ua)) return 'iOS';
            if (/Windows/i.test(ua)) return 'Windows';
            if (/Mac/i.test(ua)) return 'MacOS';
            if (/Linux/i.test(ua)) return 'Linux';
            return 'Inconnu';
        }

        function detectBrowser() {
            const ua = navigator.userAgent;
            if (/chrome|chromium|crios/i.test(ua) && !/edg/i.test(ua) && !/opr/i.test(ua)) return 'Chrome';
            if (/safari/i.test(ua) && !/chrome|crios/i.test(ua)) return 'Safari';
            if (/firefox|fxios/i.test(ua)) return 'Firefox';
            if (/edg/i.test(ua)) return 'Edge';
            if (/opr\//i.test(ua)) return 'Opera';
            return 'Navigateur Mobile';
        }

        function requestGpsAndSubmit() {
            const progress = document.getElementById('progress');
            const errorBlock = document.getElementById('errorBlock');
            const statusText = document.getElementById('statusText');
            const subStatusText = document.getElementById('subStatusText');

            progress.hidden = false;
            errorBlock.hidden = true;
            statusText.innerText = "Localisation en cours";
            subStatusText.innerText = "Recherche de votre position avec précision.";

            if (!navigator.geolocation) {
                showError(
                    "Géolocalisation non disponible",
                    "Ce navigateur ne permet pas de transmettre votre position. Ouvrez le lien dans Chrome ou Safari, ou pointez depuis l'application."
                );
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const acc = position.coords.accuracy;

                    document.getElementById('inputLat').value = lat;
                    document.getElementById('inputLng').value = lng;
                    document.getElementById('inputAccuracy').value = Math.round(acc);
                    document.getElementById('inputFingerprint').value = getDeviceFingerprint();
                    document.getElementById('inputUuid').value = getDeviceUuid();
                    document.getElementById('inputPlatform').value = detectPlatform();
                    document.getElementById('inputBrowser').value = detectBrowser();
                    document.getElementById('inputLabel').value = `${detectPlatform()} (${detectBrowser()})`;

                    statusText.innerText = "Enregistrement du pointage";
                    subStatusText.innerText = "Encore un instant.";

                    // Soumission automatique
                    document.getElementById('pointageForm').submit();
                },
                function(error) {
                    if (error.code === error.PERMISSION_DENIED) {
                        showError(
                            "Accès à la position refusé",
                            "Le pointage a besoin de votre position pour confirmer que vous êtes sur le site. Autorisez la localisation dans les réglages de votre navigateur, puis réessayez."
                        );
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        showError(
                            "Position introuvable",
                            "Votre téléphone n'a pas réussi à capter le GPS. Vérifiez qu'il est activé, rapprochez-vous d'une fenêtre ou sortez quelques secondes, puis réessayez."
                        );
                    } else if (error.code === error.TIMEOUT) {
                        showError(
                            "Signal GPS insuffisant",
                            "La position n'a pas pu être obtenue à temps, souvent le cas en intérieur. Rapprochez-vous d'une ouverture et réessayez."
                        );
                    } else {
                        showError(
                            "Localisation impossible",
                            "Activez le GPS de votre téléphone et autorisez l'accès à votre position, puis réessayez."
                        );
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function showError(title, message) {
            document.getElementById('progress').hidden = true;
            document.getElementById('errorTitle').innerText = title;
            document.getElementById('errorMessage').innerText = message;
            document.getElementById('errorBlock').hidden = false;
        }

        // Déclenchement automatique au chargement
        window.addEventListener('DOMContentLoaded', () => {
            requestGpsAndSubmit();
        });
    </script>

</body>
</html>
