<x-app-layout>
<main class="max-w-2xl mx-auto px-6 py-12 md:py-16">
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    Bonjour, <span class="text-[#4154f1]">{{ explode(' ', Auth::user()->name)[0] }}</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">
                    {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                </p>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i> {{ session('success') }}
            </div>
            @endif

            @php
            $user = Auth::user();
            $isEmployee = isset($domaine);
            $canCheckIn = $isEmployee ? true : ($activeStage ? true : false);
            @endphp

            @if(!$canCheckIn)
                <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 rounded-3xl p-10 text-center">
                    <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400 mx-auto mb-4"></i>
                    @if($isEmployee)
                        <h2 class="text-xl font-semibold">Accès non autorisé</h2>
                        <p class="text-slate-500">Votre compte n'est pas rattaché à un domaine de travail.</p>
                    @else
                        <h2 class="text-xl font-semibold">Aucun stage actif</h2>
                        <p class="text-slate-500">Contactez l'administration.</p>
                    @endif
                </div>
            @else
                <div class="bg-white dark:bg-slate-800/50 border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm mb-8">
                    <h3 class="text-xs font-semibold text-[#4154f1] uppercase tracking-wider mb-6 text-center md:text-left">Statut de présence</h3>
                    <div class="flex flex-row items-center justify-around md:justify-start md:gap-12">
                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase mb-1">Arrivée</p>
                            <p class="text-3xl md:text-4xl font-bold {{ $attendanceDay?->first_check_in_at ? ($attendanceDay->isLate() ? 'text-[#eb0000]' : 'text-emerald-600') : 'text-slate-400' }}">
                                {{ $attendanceDay?->first_check_in_at?->format('H:i') ?? '--:--' }}
                            </p>
                        </div>
                        <div class="w-px h-10 bg-slate-200"></div>
                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase mb-1">Départ</p>
                            <p class="text-3xl md:text-4xl font-bold {{ $attendanceDay?->last_check_out_at ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                {{ $attendanceDay?->last_check_out_at?->format('H:i') ?? '--:--' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div id="presence-status" class="mb-6 p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-center text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2 border">
                    <i data-lucide="map-pin" class="w-4 h-4 text-[#4154f1]"></i>
                    <span>Prêt pour la localisation</span>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    @php $hasCheckIn = $attendanceDay && $attendanceDay->first_check_in_at; $hasCheckOut = $attendanceDay && $attendanceDay->last_check_out_at; @endphp
                    @if(!$hasCheckOut)
                    {{-- Arrivée --}}
                    <form method="POST" action="{{ route('presence.prepareCheckin') }}" class="flex-1 presence-form" {{ $isEmployee ? 'data-is-employee="true"' : '' }}>
                        @csrf
                        @if(!$isEmployee)
                            <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        @endif
                        <input type="hidden" name="latitude"><input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters"><input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid"><input type="hidden" name="device_label">
                        <input type="hidden" name="platform"><input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">
                        <button type="button" class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm {{ $hasCheckIn ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-[#4154f1] text-white' }}" {{ $hasCheckIn ? 'disabled' : '' }} data-action="arrivée">
                            <i data-lucide="check" class="w-5 h-5"></i> Pointer l'arrivée
                        </button>
                    </form>
                    {{-- Départ --}}
                    <form method="POST" action="{{ route('presence.prepareCheckout') }}" class="flex-1 presence-form" {{ $isEmployee ? 'data-is-employee="true"' : '' }}>
                        @csrf
                        @if(!$isEmployee)
                            <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        @endif
                        <input type="hidden" name="latitude"><input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters"><input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid"><input type="hidden" name="device_label">
                        <input type="hidden" name="platform"><input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">
                        <button type="button" class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm {{ !$hasCheckIn ? 'bg-red-50 border border-[#eb0000] text-[#eb0000] cursor-not-allowed' : 'bg-white border-2 border-[#eb0000] text-[#eb0000] hover:bg-[#eb0000] hover:text-white' }}" {{ !$hasCheckIn ? 'disabled' : '' }} data-action="départ">
                            <i data-lucide="log-out" class="w-5 h-5"></i> Pointer le départ
                        </button>
                    </form>
                    @else
                    <div class="w-full p-8 bg-blue-50/50 border border-blue-100 rounded-2xl flex flex-col items-center gap-3">
                        <i data-lucide="check-circle-2" class="w-8 h-8 text-[#4154f1]"></i>
                        <p class="text-lg font-semibold text-[#4154f1]">Journée terminée</p>
                    </div>
                    @endif
                </div>
                @endif
        </main>

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
                const actionName = btn.dataset.action || 'présence';

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

                            // For employees, allow check-in without GPS
                            if (form.hasAttribute('data-is-employee')) {
                                status.textContent = `⚠️ ${msg} — Pointage sans GPS en cours...`;
                                status.className = 'p-4 bg-yellow-50 text-yellow-700 rounded-xl text-center text-sm';
                                form.submit();
                                return;
                            }

                            status.textContent = msg;
                            status.className = 'p-4 bg-red-50 text-red-700 rounded-xl text-center text-sm';
                            btn.disabled = false;
                            btn.textContent = actionName === 'arrivée' ? 'Pointer l\'arrivée' : 'Pointer le départ';
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
