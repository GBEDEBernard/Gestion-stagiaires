<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Qui effectue le pointage ? — {{ $site->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">

    <div class="w-full max-w-md bg-slate-800/95 backdrop-blur-xl border border-slate-700/60 rounded-3xl p-6 sm:p-8 shadow-2xl text-center relative overflow-hidden">
        
        <!-- Site Tag -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-700/60 border border-slate-600/50 text-xs font-semibold text-slate-300 mb-4">
            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>{{ $site->name }}</span>
        </div>

        <h1 class="text-2xl font-extrabold text-white mb-2">
            Qui effectue le pointage ?
        </h1>
        <p class="text-xs text-slate-400 mb-6">
            Plusieurs profils sont configurés sur cet appareil. Cliquez sur votre nom pour valider directement :
        </p>

        <!-- Users List -->
        <div id="usersListContainer" class="space-y-3 mb-6">
            @foreach($usersList as $item)
                @php
                    $u = $item['user'];
                    $token = $item['device_token'];
                @endphp
                <button type="button" 
                        onclick="selectUser({{ $u->id }}, '{{ $token }}', '{{ addslashes($u->prenom ?? $u->name) }}')"
                        class="w-full group flex items-center justify-between p-4 rounded-2xl bg-slate-900/80 hover:bg-indigo-950/60 border border-slate-700/70 hover:border-indigo-500/80 transition-all text-left shadow-md hover:shadow-indigo-500/10">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center font-bold text-white text-lg shadow-sm">
                            {{ strtoupper(substr($u->prenom ?? $u->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white group-hover:text-indigo-300 transition">
                                {{ $u->prenom ?? '' }} {{ $u->nom ?? $u->name }}
                            </h3>
                            <p class="text-xs text-slate-400">
                                {{ $u->roles->pluck('name')->first() ?? 'Membre' }}
                            </p>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-indigo-400 group-hover:bg-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Processing State (hidden by default) -->
        <div id="processingState" class="hidden py-6">
            <div class="relative w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-indigo-500/20"></div>
                <div class="absolute inset-0 rounded-full border-4 border-emerald-400 border-t-transparent animate-spin"></div>
            </div>
            <p id="processingName" class="text-sm font-semibold text-slate-200">Pointage en cours...</p>
            <p class="text-xs text-slate-400 mt-1">Capture GPS et validation immédiate...</p>
        </div>

        <!-- Hidden Form -->
        <form id="pointageForm" action="{{ route('presence.qr.process', ['site_token' => $site->qr_token]) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="user_id" id="inputUserId">
            <input type="hidden" name="device_token" id="inputDeviceToken">
            <input type="hidden" name="latitude" id="inputLat">
            <input type="hidden" name="longitude" id="inputLng">
            <input type="hidden" name="accuracy_meters" id="inputAccuracy">
            <input type="hidden" name="device_fingerprint" id="inputFingerprint">
            <input type="hidden" name="device_uuid" id="inputUuid">
            <input type="hidden" name="device_label" id="inputLabel">
            <input type="hidden" name="platform" id="inputPlatform">
            <input type="hidden" name="browser" id="inputBrowser">
        </form>

        <div class="pt-4 border-t border-slate-700/40 text-center">
            <p class="text-[11px] text-slate-500">Sélectionnez votre profil pour valider sans mot de passe</p>
        </div>
    </div>

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

        function selectUser(userId, token, userName) {
            document.getElementById('usersListContainer').classList.add('hidden');
            const proc = document.getElementById('processingState');
            proc.classList.remove('hidden');
            document.getElementById('processingName').innerText = `Validation pour ${userName}...`;

            document.getElementById('inputUserId').value = userId;
            document.getElementById('inputDeviceToken').value = token;
            document.getElementById('inputFingerprint').value = getDeviceFingerprint();
            document.getElementById('inputUuid').value = getDeviceUuid();
            document.getElementById('inputPlatform').value = detectPlatform();
            document.getElementById('inputBrowser').value = detectBrowser();
            document.getElementById('inputLabel').value = `${detectPlatform()} (${detectBrowser()})`;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('inputLat').value = position.coords.latitude;
                        document.getElementById('inputLng').value = position.coords.longitude;
                        document.getElementById('inputAccuracy').value = Math.round(position.coords.accuracy);
                        document.getElementById('pointageForm').submit();
                    },
                    function(error) {
                        alert("Veuillez autoriser l'accès GPS pour valider le pointage.");
                        document.getElementById('usersListContainer').classList.remove('hidden');
                        proc.classList.add('hidden');
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                alert("La géolocalisation n'est pas supportée sur cet appareil.");
                document.getElementById('usersListContainer').classList.remove('hidden');
                proc.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
