<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pointage & Configuration de l'appareil — {{ $site->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white">

    <div class="w-full max-w-md bg-slate-800/95 backdrop-blur-2xl border border-slate-700/60 rounded-3xl p-6 sm:p-8 shadow-2xl text-center relative overflow-hidden">
        
        <!-- Site Tag -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-700/60 border border-slate-600/50 text-xs font-semibold text-slate-300 mb-4">
            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>{{ $site->name }}</span>
        </div>

        <h1 class="text-2xl font-extrabold text-white mb-1">
            Connexion Réussie ! 🎉
        </h1>
        <p class="text-sm font-semibold text-slate-300 mb-6">
            Bonjour {{ $user->prenom ?? $user->name }}
        </p>

        <!-- Step 1 : Pointage en cours / Résultat -->
        <div id="stepPointage" class="py-4">
            <div class="relative w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-indigo-500/20"></div>
                <div class="absolute inset-0 rounded-full border-4 border-emerald-400 border-t-transparent animate-spin"></div>
            </div>
            <p id="pointageStatusText" class="text-sm font-medium text-slate-200">Validation de votre pointage en cours...</p>
            <p class="text-xs text-slate-400 mt-1">Capture GPS avec haute précision (≤ 25m)</p>
        </div>

        <!-- Step 2 : Proposition d'enrôlement de l'appareil (affiché après pointage) -->
        <div id="stepEnrollment" class="hidden text-left mt-2">
            
            <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/50 mb-5 flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-emerald-300" id="pointageSuccessMsg">Pointage validé avec succès !</h4>
                    <p class="text-xs text-emerald-200/80 mt-0.5" id="pointageTimeMsg">{{ now()->format('H:i') }} — {{ now()->translatedFormat('l d F Y') }}</p>
                </div>
            </div>

            @if($canEnroll)
                <div class="bg-slate-900/80 border border-indigo-500/40 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div class="absolute -top-12 -right-12 w-28 h-28 bg-indigo-500/20 rounded-full blur-xl"></div>
                    
                    <div class="flex items-center gap-2.5 text-indigo-400 font-bold text-sm mb-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Faire de ce téléphone votre badge</span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed mb-4">
                        Enregistrez cet appareil pour que vos prochains scans à la porte soient <strong>instantanés sans avoir à vous reconnecter</strong>.
                    </p>

                    <div class="mb-4">
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Nom de cet appareil :</label>
                        <input type="text" id="inputDeviceName" value="Mon Téléphone" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-medium focus:outline-none focus:border-indigo-500">
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="button" onclick="enrollDevice()" id="btnEnroll" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-md flex items-center justify-center gap-2">
                            <span>✅ Oui, enregistrer mon téléphone</span>
                        </button>
                        <button type="button" onclick="skipEnrollment()" class="w-full py-2 rounded-xl text-slate-400 hover:text-slate-200 text-xs font-medium transition text-center">
                            Fermer
                        </button>
                    </div>

                    <p class="text-[10px] text-slate-500 text-center mt-3">Appareils enregistrés : {{ $activeBadgesCount }}/2 maximum</p>
                </div>
            @else
                <div class="bg-slate-900/70 border border-slate-700/60 rounded-2xl p-4 text-center">
                    <p class="text-xs text-slate-300">
                        Vous avez déjà 2 appareils enregistrés comme badge. Votre présence du jour a bien été enregistrée !
                    </p>
                    <button type="button" onclick="skipEnrollment()" class="mt-4 px-6 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                        Fermer cette page
                    </button>
                </div>
            @endif

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

        // 1. Déclencher le pointage dès l'arrivée
        window.addEventListener('DOMContentLoaded', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const payload = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy_meters: Math.round(position.coords.accuracy),
                            device_fingerprint: getDeviceFingerprint(),
                            device_uuid: getDeviceUuid(),
                            platform: detectPlatform(),
                            browser: detectBrowser(),
                            device_label: `${detectPlatform()} (${detectBrowser()})`
                        };

                        fetch("{{ route('presence.qr.process', ['site_token' => $site->qr_token]) }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                "Accept": "application/json, text/html"
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(() => {
                            document.getElementById('stepPointage').classList.add('hidden');
                            document.getElementById('stepEnrollment').classList.remove('hidden');
                        })
                        .catch(err => {
                            document.getElementById('stepPointage').classList.add('hidden');
                            document.getElementById('stepEnrollment').classList.remove('hidden');
                        });
                    },
                    function(err) {
                        document.getElementById('stepPointage').classList.add('hidden');
                        document.getElementById('stepEnrollment').classList.remove('hidden');
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                document.getElementById('stepPointage').classList.add('hidden');
                document.getElementById('stepEnrollment').classList.remove('hidden');
            }
        });

        // 2. Enrôler l'appareil
        function enrollDevice() {
            const btn = document.getElementById('btnEnroll');
            btn.disabled = true;
            btn.innerHTML = '<span>Configuration en cours...</span>';

            const payload = {
                device_fingerprint: getDeviceFingerprint(),
                device_uuid: getDeviceUuid(),
                device_label: `${detectPlatform()} (${detectBrowser()})`,
                device_name: document.getElementById('inputDeviceName').value || `${detectPlatform()} de {{ $user->prenom ?? $user->name }}`,
                platform: detectPlatform(),
                browser: detectBrowser()
            };

            fetch("{{ route('presence.devices.enroll') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.className = "w-full py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold text-center";
                    btn.innerHTML = "<span>✨ Appareil enregistré avec succès !</span>";
                    setTimeout(() => {
                        window.location.href = "{{ route($user->homeRouteName()) }}";
                    }, 1500);
                } else {
                    alert(data.message || "Erreur lors de l'enregistrement.");
                    btn.disabled = false;
                    btn.innerHTML = '<span>Réessayer</span>';
                }
            })
            .catch(err => {
                alert("Erreur de connexion.");
                btn.disabled = false;
                btn.innerHTML = '<span>Réessayer</span>';
            });
        }

        function skipEnrollment() {
            window.location.href = "{{ route($user->homeRouteName()) }}";
        }
    </script>
</body>
</html>
