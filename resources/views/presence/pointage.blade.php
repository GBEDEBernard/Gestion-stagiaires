<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-6">

        {{-- Errors --}}
        @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm">
            <strong>Erreur :</strong>
            <ul class="mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow border overflow-hidden">

            {{-- Header --}}
            <div class="p-5 border-b bg-gray-50">
                <h1 class="text-xl md:text-2xl font-bold">Pointage de présence</h1>
                <p class="text-sm text-gray-500">Rapide • sécurisé • géolocalisé</p>
            </div>

            <div class="p-5 space-y-6">

                {{-- Messages --}}
                @if(session('success'))
                <div class="p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
                @endif

                {{-- Aucun stage --}}
                @if(!$activeStage)
                <div class="text-center p-6 bg-yellow-50 border rounded-xl">
                    <h3 class="font-semibold text-lg">Aucun stage actif</h3>
                    <p class="text-sm text-gray-600 mt-2">Contacte l'administration.</p>
                </div>
                @else

                {{-- Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-green-50 rounded-xl border">
                        <p class="text-sm text-green-700 font-semibold">Stage</p>
                        <h2 class="font-bold text-lg">
                            {{ $activeStage->theme ?? 'Sans thème' }}
                        </h2>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl border">
                        <p class="text-sm text-gray-600">Site</p>
                        <h2 class="font-semibold">
                            {{ $activeStage->site?->name ?? 'Site principal' }}
                        </h2>
                    </div>
                </div>

                {{-- Status du jour --}}
                <div class="p-4 bg-black text-white rounded-xl text-center">
                    <p class="text-sm opacity-70">Aujourd'hui</p>
                    <div class="flex justify-center gap-6 mt-3 text-lg font-bold flex-wrap">
                        <span>Arrivée: {{ $attendanceDay?->first_check_in_at?->format('H:i') ?? '--' }}</span>
                        <span>Départ: {{ $attendanceDay?->last_check_out_at?->format('H:i') ?? '--' }}</span>
                    </div>
                </div>

                {{-- GPS STATUS --}}
                <div id="presence-status" class="p-4 bg-gray-100 rounded-xl text-center text-sm">
                    📍 En attente de localisation...
                </div>

                {{-- BUTTONS --}}
                <div class="space-y-4">

                    {{-- CHECKIN --}}
                    <form method="POST" action="{{ route('presence.prepareCheckin') }}" class="presence-form" data-action="arrivée" novalidate>
                        @csrf
                        <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters">
                        <input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid">
                        <input type="hidden" name="device_label">
                        <input type="hidden" name="platform">
                        <input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">

                        <button type="button" class="presence-btn w-full bg-black text-white py-4 rounded-xl text-lg font-bold presence-submit">
                            Pointer arrivée
                        </button>
                    </form>

                    {{-- CHECKOUT --}}
                    <form method="POST" action="{{ route('presence.prepareCheckout') }}" class="presence-form" data-action="départ" novalidate>
                        @csrf
                        <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters">
                        <input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid">
                        <input type="hidden" name="device_label">
                        <input type="hidden" name="platform">
                        <input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">

                        <button type="button" class="presence-btn w-full bg-green-600 text-white py-4 rounded-xl text-lg font-bold presence-submit">
                            Pointer départ
                        </button>
                    </form>

                </div>

                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ─── Utilitaires device ──────────────────────────────────────────
            function getOrCreateDeviceUuid() {
                const key = 'jb_presence_device_uuid';
                let uuid = localStorage.getItem(key);
                if (!uuid) {
                    uuid = (crypto.randomUUID) ?
                        crypto.randomUUID() :
                        `jb-${Date.now()}-${Math.random().toString(16).slice(2)}`;
                    localStorage.setItem(key, uuid);
                }
                return uuid;
            }

            function generateFingerprint(deviceUuid) {
                const raw = [
                    navigator.userAgent,
                    navigator.platform || '',
                    screen.width,
                    screen.height,
                    Intl.DateTimeFormat().resolvedOptions().timeZone,
                    deviceUuid,
                ].join('|');
                let hash = 0;
                for (let i = 0; i < raw.length; i++) {
                    hash = ((hash << 5) - hash) + raw.charCodeAt(i);
                    hash |= 0;
                }
                return `jb-${Math.abs(hash)}`;
            }

            function detectBrowser() {
                const ua = navigator.userAgent;
                if (ua.includes('Edg/')) return 'Edge';
                if (ua.includes('Chrome/')) return 'Chrome';
                if (ua.includes('Firefox/')) return 'Firefox';
                if (ua.includes('Safari/')) return 'Safari';
                return 'Unknown';
            }

            // ─── Init ────────────────────────────────────────────────────────
            const forms = document.querySelectorAll('.presence-form');
            const status = document.getElementById('presence-status');

            // Pré-calcul des valeurs device (une seule fois)
            const deviceUuid = getOrCreateDeviceUuid();
            const fingerprint = generateFingerprint(deviceUuid);
            const browserName = detectBrowser();
            const platformName = navigator.userAgentData?.platform || navigator.platform || 'unknown';
            const deviceLabel = `${browserName} / ${platformName}`;

            // ─── Vérification SweetAlert (rejet serveur) ────────────────────
            @if(session('rejection_reason'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: '🚫 Pointage refusé',
                    html: "{{ addslashes(session('rejection_reason')) }}<br><small>Déplacez-vous vers le site et réessayez.</small>",
                    confirmButtonText: 'OK, nouveau pointage',
                    confirmButtonColor: '#10b981',
                    allowOutsideClick: true,
                });
            }
            @endif

            // ─── GPS non disponible ──────────────────────────────────────────
            if (!navigator.geolocation) {
                status.textContent = '❌ GPS non supporté sur ce navigateur.';
                status.className = 'p-4 bg-red-50 text-red-700 rounded-xl text-center text-sm';
                forms.forEach(f => f.querySelector('.presence-submit').disabled = true);
                return;
            }

            // ─── Bind click sur chaque formulaire ───────────────────────────
            forms.forEach(form => {
                const btn = form.querySelector('.presence-submit');
                const actionName = form.dataset.action || 'présence';

                btn.addEventListener('click', () => {
                    btn.disabled = true;
                    btn.textContent = '📡 Recherche GPS...';
                    status.textContent = '📡 Localisation en cours...';
                    status.className = 'p-4 bg-blue-50 text-blue-700 rounded-xl text-center text-sm';

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            // ── Coordonnées GPS ──────────────────────────────
                            form.querySelector('[name="latitude"]').value = pos.coords.latitude;
                            form.querySelector('[name="longitude"]').value = pos.coords.longitude;
                            form.querySelector('[name="accuracy_meters"]').value = Math.round(pos.coords.accuracy || 0);

                            // ── Informations device (CORRECTION PRINCIPALE) ──
                            form.querySelector('[name="device_fingerprint"]').value = fingerprint;
                            form.querySelector('[name="device_uuid"]').value = deviceUuid;
                            form.querySelector('[name="device_label"]').value = deviceLabel;
                            form.querySelector('[name="platform"]').value = platformName;
                            form.querySelector('[name="browser"]').value = browserName;

                            status.textContent = `✅ Position capturée (précision: ${Math.round(pos.coords.accuracy)}m) — envoi en cours...`;
                            status.className = 'p-4 bg-green-50 text-green-700 rounded-xl text-center text-sm';

                            form.submit();
                        },
                        (err) => {
                            let msg = 'Impossible de récupérer votre position.';
                            if (err.code === err.PERMISSION_DENIED) msg = '🔒 Autorise la localisation dans ton navigateur.';
                            if (err.code === err.TIMEOUT) msg = '⏱️ GPS trop lent. Essaie en plein air ou près d\'une fenêtre.';
                            if (err.code === err.POSITION_UNAVAILABLE) msg = '📡 Position indisponible pour le moment.';

                            status.textContent = msg;
                            status.className = 'p-4 bg-red-50 text-red-700 rounded-xl text-center text-sm';
                            btn.disabled = false;
                            btn.textContent = actionName === 'arrivée' ? 'Pointer arrivée' : 'Pointer départ';
                        }, {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0,
                        }
                    );
                });
            });
        });
    </script>
    @endpush

</x-app-layout>