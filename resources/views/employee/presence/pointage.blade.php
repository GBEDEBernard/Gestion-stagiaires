<x-app-layout>
    <main class="max-w-2xl mx-auto px-6 py-12 md:py-16">
        {{-- En-tête --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                Bonjour, <span class="text-emerald-600 dark:text-emerald-400">{{ explode(' ', Auth::user()->name)[0] }}</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">
                {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </p>
        </div>

        {{-- Messages flash --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 rounded-xl text-emerald-700 dark:text-emerald-400 flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i> {{ session('error') }}
        </div>
        @endif

        @if(session('rejection_reason'))
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 rounded-xl text-amber-700 dark:text-amber-400 flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i> {{ session('rejection_reason') }}
        </div>
        @endif

        {{-- Carte statut de présence --}}
        <div class="bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 md:p-8 shadow-sm mb-8">
            <h3 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-6 text-center md:text-left">Statut de présence</h3>
            <div class="flex flex-row items-center justify-around md:justify-start md:gap-12">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase mb-1">Arrivée</p>
                    <p class="text-3xl md:text-4xl font-bold {{ $attendanceDay?->first_check_in_at ? ($attendanceDay->isLate() ? 'text-red-500' : 'text-emerald-600') : 'text-slate-400' }}">
                        {{ $attendanceDay?->first_check_in_at?->format('H:i') ?? '--:--' }}
                    </p>
                </div>
                <div class="w-px h-10 bg-slate-200 dark:bg-slate-700"></div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase mb-1">Départ</p>
                    <p class="text-3xl md:text-4xl font-bold {{ $attendanceDay?->last_check_out_at ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">
                        {{ $attendanceDay?->last_check_out_at?->format('H:i') ?? '--:--' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Zone de statut GPS --}}
        <div id="presence-status" class="mb-6 p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-center text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700">
            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
            <span>Prêt pour la localisation</span>
        </div>

        {{-- Boutons d'action --}}
        @php
            $hasCheckIn = $attendanceDay && $attendanceDay->first_check_in_at;
            $hasCheckOut = $attendanceDay && $attendanceDay->last_check_out_at;
        @endphp

        <div class="flex flex-col sm:flex-row gap-4">
            @if(!$hasCheckOut)
                {{-- Arrivée --}}
                <form method="POST" action="{{ route('presence.prepareCheckin') }}" class="flex-1 presence-form">
                    @csrf
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <input type="hidden" name="accuracy_meters">
                    <input type="hidden" name="device_fingerprint">
                    <input type="hidden" name="device_uuid">
                    <input type="hidden" name="device_label">
                    <input type="hidden" name="platform">
                    <input type="hidden" name="browser">
                    <input type="hidden" name="app_version" value="presence-web-v1">
                    <button type="button" class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm transition-all duration-200
                        {{ $hasCheckIn ? 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}"
                        {{ $hasCheckIn ? 'disabled' : '' }}>
                        <i data-lucide="check" class="w-5 h-5"></i> Pointer l'arrivée
                    </button>
                </form>

                {{-- Départ --}}
                <form method="POST" action="{{ route('presence.prepareCheckout') }}" class="flex-1 presence-form">
                    @csrf
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <input type="hidden" name="accuracy_meters">
                    <input type="hidden" name="device_fingerprint">
                    <input type="hidden" name="device_uuid">
                    <input type="hidden" name="device_label">
                    <input type="hidden" name="platform">
                    <input type="hidden" name="browser">
                    <input type="hidden" name="app_version" value="presence-web-v1">
                    <button type="button" class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm transition-all duration-200
                        {{ !$hasCheckIn ? 'bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 cursor-not-allowed' : 'bg-white dark:bg-slate-800 border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white' }}"
                        {{ !$hasCheckIn ? 'disabled' : '' }}>
                        <i data-lucide="log-out" class="w-5 h-5"></i> Pointer le départ
                    </button>
                </form>
            @else
                <div class="w-full p-8 bg-emerald-50/50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex flex-col items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-600"></i>
                    <p class="text-lg font-semibold text-emerald-600">Journée terminée</p>
                </div>
            @endif
        </div>

        {{-- Lien vers l'historique --}}
        <div class="mt-8 text-center">
            <a href="{{ route('presence.historique') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                Voir mon historique de présence
            </a>
        </div>
    </main>

    @push('scripts')
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialiser les icônes Lucide
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // ─── Utilitaires device ──────────────────────────────────────────
            function getOrCreateDeviceUuid() {
                const key = 'jb_presence_device_uuid';
                let uuid = localStorage.getItem(key);
                if (!uuid) {
                    uuid = (crypto.randomUUID) ? crypto.randomUUID() : `jb-${Date.now()}-${Math.random().toString(16).slice(2)}`;
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

            // ─── Elements ────────────────────────────────────────────────────
            const forms = document.querySelectorAll('.presence-form');
            const statusDiv = document.getElementById('presence-status');
            const statusText = statusDiv?.querySelector('span') || statusDiv;
            const statusIcon = statusDiv?.querySelector('i') || null;

            // Pré‑calcul des données device (une seule fois)
            const deviceUuid = getOrCreateDeviceUuid();
            const fingerprint = generateFingerprint(deviceUuid);
            const browserName = detectBrowser();
            const platformName = navigator.userAgentData?.platform || navigator.platform || 'unknown';
            const deviceLabel = `${browserName} / ${platformName}`;

            // ─── GPS non disponible ──────────────────────────────────────────
            if (!navigator.geolocation) {
                if (statusText) statusText.textContent = '❌ GPS non supporté sur ce navigateur.';
                if (statusDiv) statusDiv.className = 'mb-6 p-3 bg-red-50 dark:bg-red-900/20 rounded-xl text-center text-sm font-medium text-red-700 dark:text-red-400 flex items-center justify-center gap-2 border border-red-200 dark:border-red-800';
                forms.forEach(f => f.querySelector('.presence-submit').disabled = true);
                return;
            }

            // ─── Fonction pour mettre à jour l'UI pendant la recherche ────────
            function setSearchingState(formBtn, isSearching, message = null) {
                if (isSearching) {
                    const originalHtml = formBtn.innerHTML;
                    formBtn.setAttribute('data-original', originalHtml);
                    formBtn.disabled = true;
                    formBtn.innerHTML = `
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Recherche GPS...
                    `;
                } else {
                    const original = formBtn.getAttribute('data-original');
                    if (original) formBtn.innerHTML = original;
                    formBtn.disabled = false;
                }
            }

            function setStatusMessage(message, type = 'info') {
                if (!statusDiv) return;
                let bgClass = '', textClass = '', borderClass = '', iconHtml = '';
                switch (type) {
                    case 'success':
                        bgClass = 'bg-emerald-50 dark:bg-emerald-900/20';
                        textClass = 'text-emerald-700 dark:text-emerald-400';
                        borderClass = 'border-emerald-200 dark:border-emerald-800';
                        iconHtml = '<i data-lucide="check-circle" class="w-4 h-4"></i>';
                        break;
                    case 'error':
                        bgClass = 'bg-red-50 dark:bg-red-900/20';
                        textClass = 'text-red-700 dark:text-red-400';
                        borderClass = 'border-red-200 dark:border-red-800';
                        iconHtml = '<i data-lucide="alert-circle" class="w-4 h-4"></i>';
                        break;
                    default:
                        bgClass = 'bg-blue-50 dark:bg-blue-900/20';
                        textClass = 'text-blue-700 dark:text-blue-400';
                        borderClass = 'border-blue-200 dark:border-blue-800';
                        iconHtml = '<i data-lucide="map-pin" class="w-4 h-4"></i>';
                }
                statusDiv.className = `mb-6 p-3 rounded-xl text-center text-sm font-medium flex items-center justify-center gap-2 border ${bgClass} ${textClass} ${borderClass}`;
                statusDiv.innerHTML = `${iconHtml}<span>${message}</span>`;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            // ─── Traitement de chaque formulaire ──────────────────────────────
            forms.forEach(form => {
                const btn = form.querySelector('.presence-submit');
                if (!btn) return;

                btn.addEventListener('click', () => {
                    // Désactiver tous les boutons pour éviter les doubles soumissions
                    forms.forEach(f => {
                        const b = f.querySelector('.presence-submit');
                        if (b && b !== btn) b.disabled = true;
                    });

                    setSearchingState(btn, true);
                    setStatusMessage('📍 Recherche de votre position GPS...', 'info');

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            // Remplir les champs cachés
                            form.querySelector('[name="latitude"]').value = pos.coords.latitude;
                            form.querySelector('[name="longitude"]').value = pos.coords.longitude;
                            form.querySelector('[name="accuracy_meters"]').value = Math.round(pos.coords.accuracy || 0);
                            form.querySelector('[name="device_fingerprint"]').value = fingerprint;
                            form.querySelector('[name="device_uuid"]').value = deviceUuid;
                            form.querySelector('[name="device_label"]').value = deviceLabel;
                            form.querySelector('[name="platform"]').value = platformName;
                            form.querySelector('[name="browser"]').value = browserName;

                            setStatusMessage(`✅ Position capturée (précision: ${Math.round(pos.coords.accuracy)}m) — envoi en cours...`, 'success');
                            form.submit();
                        },
                        (err) => {
                            let msg = 'Impossible de récupérer votre position.';
                            if (err.code === err.PERMISSION_DENIED) msg = '🔒 Autorisez la localisation dans votre navigateur.';
                            if (err.code === err.TIMEOUT) msg = '⏱️ GPS trop lent. Essayez en plein air ou près d\'une fenêtre.';
                            if (err.code === err.POSITION_UNAVAILABLE) msg = '📡 Position indisponible pour le moment.';

                            setStatusMessage(msg, 'error');
                            setSearchingState(btn, false);
                            // Réactiver les autres boutons
                            forms.forEach(f => {
                                const b = f.querySelector('.presence-submit');
                                if (b && b !== btn && !b.hasAttribute('disabled')) b.disabled = false;
                            });
                        },
                        {
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