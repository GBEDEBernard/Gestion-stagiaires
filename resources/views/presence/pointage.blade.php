<x-app-layout>
    <main class="max-w-2xl mx-auto px-6 py-12 md:py-16">

        {{-- En-tête --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                Bonjour, <span class="text-emerald-600 dark:text-emerald-400">
                    {{ explode(' ', Auth::user()->name)[0] }}
                </span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">
                {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </p>
        </div>

        {{-- Messages flash --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-700 dark:text-emerald-400 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        @if(session('rejection_reason'))
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-700 dark:text-amber-400 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('rejection_reason') }}
        </div>
        @endif

        @if(!$activeStage)
        {{-- Pas de stage actif --}}
        <div class="bg-white dark:bg-slate-800/50 border border-amber-200 dark:border-amber-700 rounded-3xl p-8 text-center">
            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Aucun stage actif</h2>
            <p class="text-slate-500 dark:text-slate-400">Vous n'avez pas de stage en cours aujourd'hui. Contactez votre responsable.</p>
        </div>

        @else

        {{-- Infos du stage --}}
        <div class="bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl p-4 mb-6 flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase font-semibold tracking-wide">Stage actif</p>
                <p class="font-semibold text-slate-900 dark:text-white truncate">
                    {{ $activeStage->theme ?: 'Stage en cours' }}
                </p>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $activeStage->site?->name ?? 'Site non défini' }}
                </p>
            </div>
        </div>

        {{-- Carte statut de présence --}}
        <div class="bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 md:p-8 shadow-sm mb-6">
            <h3 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-6 text-center">
                Statut de présence — {{ now()->locale('fr')->isoFormat('D MMMM') }}
            </h3>
            <div class="flex flex-row items-center justify-around md:justify-start md:gap-12">
                <div class="text-center">
                    <p class="text-xs font-medium text-slate-400 uppercase mb-1">Arrivée</p>
                    <p class="text-3xl md:text-4xl font-bold
                        {{ $attendanceDay?->first_check_in_at
                            ? ($attendanceDay->arrival_status === 'late' ? 'text-amber-500' : 'text-emerald-600')
                            : 'text-slate-400 dark:text-slate-600' }}">
                        {{ $attendanceDay?->first_check_in_at?->format('H:i') ?? '--:--' }}
                    </p>
                    @if($attendanceDay?->first_check_in_at && $attendanceDay->arrival_status === 'late')
                    <p class="text-xs text-amber-500 mt-1">{{ $attendanceDay->late_minutes }} min de retard</p>
                    @endif
                </div>
                <div class="w-px h-12 bg-slate-200 dark:bg-slate-700"></div>
                <div class="text-center">
                    <p class="text-xs font-medium text-slate-400 uppercase mb-1">Départ</p>
                    <p class="text-3xl md:text-4xl font-bold
                        {{ $attendanceDay?->last_check_out_at ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-600' }}">
                        {{ $attendanceDay?->last_check_out_at?->format('H:i') ?? '--:--' }}
                    </p>
                </div>
            </div>

            {{-- Heures travaillées si check-out fait --}}
            @if($attendanceDay?->last_check_out_at && $attendanceDay->worked_minutes > 0)
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 text-center">
                <p class="text-sm text-slate-500">Temps travaillé aujourd'hui :
                    <span class="font-bold text-slate-900 dark:text-white">
                        {{ floor($attendanceDay->worked_minutes / 60) }}h{{ str_pad($attendanceDay->worked_minutes % 60, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </p>
            </div>
            @endif
        </div>

        {{-- Zone GPS --}}
        <div id="presence-status"
            class="mb-6 p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-center text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Prêt pour la localisation</span>
        </div>

        {{-- Boutons d'action --}}
        @php
            $hasCheckIn  = $attendanceDay && $attendanceDay->first_check_in_at;
            $hasCheckOut = $attendanceDay && $attendanceDay->last_check_out_at;
        @endphp

        @if($hasCheckOut)
        {{-- Journée terminée --}}
        <div class="w-full p-8 bg-emerald-50/50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex flex-col items-center gap-3">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-lg font-semibold text-emerald-600">Journée terminée ✓</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Arrivée {{ $attendanceDay->first_check_in_at->format('H:i') }} · Départ {{ $attendanceDay->last_check_out_at->format('H:i') }}</p>
        </div>

        @else
        <div class="flex flex-col sm:flex-row gap-4">

            {{-- Formulaire Arrivée --}}
            <form method="POST" action="{{ route('presence.prepareCheckin') }}"
                class="flex-1 presence-form" id="form-checkin">
                @csrf
                <input type="hidden" name="stage_id"          value="{{ $activeStage->id }}">
                <input type="hidden" name="latitude"           id="checkin_latitude">
                <input type="hidden" name="longitude"          id="checkin_longitude">
                <input type="hidden" name="accuracy_meters"    id="checkin_accuracy">
                <input type="hidden" name="device_fingerprint" id="checkin_fingerprint">
                <input type="hidden" name="device_uuid"        id="checkin_uuid">
                <input type="hidden" name="device_label"       id="checkin_label">
                <input type="hidden" name="platform"           id="checkin_platform">
                <input type="hidden" name="browser"            id="checkin_browser">
                <input type="hidden" name="app_version"        value="presence-web-v1">

                <button type="button"
                    id="btn-checkin"
                    {{ $hasCheckIn ? 'disabled' : '' }}
                    class="w-full py-4 rounded-2xl font-semibold flex items-center justify-center gap-2 shadow-sm transition-all duration-200
                        {{ $hasCheckIn
                            ? 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed'
                            : 'bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white shadow-emerald-600/30' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $hasCheckIn ? 'Arrivée déjà pointée' : "Pointer l'arrivée" }}
                </button>
            </form>

            {{-- Formulaire Départ --}}
            <form method="POST" action="{{ route('presence.prepareCheckout') }}"
                class="flex-1 presence-form" id="form-checkout">
                @csrf
                <input type="hidden" name="stage_id"          value="{{ $activeStage->id }}">
                <input type="hidden" name="latitude"           id="checkout_latitude">
                <input type="hidden" name="longitude"          id="checkout_longitude">
                <input type="hidden" name="accuracy_meters"    id="checkout_accuracy">
                <input type="hidden" name="device_fingerprint" id="checkout_fingerprint">
                <input type="hidden" name="device_uuid"        id="checkout_uuid">
                <input type="hidden" name="device_label"       id="checkout_label">
                <input type="hidden" name="platform"           id="checkout_platform">
                <input type="hidden" name="browser"            id="checkout_browser">
                <input type="hidden" name="app_version"        value="presence-web-v1">

                <button type="button"
                    id="btn-checkout"
                    {{ !$hasCheckIn ? 'disabled' : '' }}
                    class="w-full py-4 rounded-2xl font-semibold flex items-center justify-center gap-2 shadow-sm transition-all duration-200
                        {{ !$hasCheckIn
                            ? 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed'
                            : 'bg-white dark:bg-slate-800 border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white active:scale-95' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Pointer le départ
                </button>
            </form>
        </div>
        @endif

        @endif

        {{-- Lien historique --}}
        <div class="mt-8 text-center">
            <a href="{{ route('presence.historique') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Voir mon historique de présence
            </a>
        </div>
    </main>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        const statusDiv = document.getElementById('presence-status');

        // ── Helpers device ──────────────────────────────────────────
        function getOrCreateDeviceUuid() {
            const key = 'jb_presence_device_uuid';
            let uuid = localStorage.getItem(key);
            if (!uuid) {
                uuid = crypto.randomUUID
                    ? crypto.randomUUID()
                    : `jb-${Date.now()}-${Math.random().toString(16).slice(2)}`;
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
            if (ua.includes('Edg/'))    return 'Edge';
            if (ua.includes('Chrome/')) return 'Chrome';
            if (ua.includes('Firefox/'))return 'Firefox';
            if (ua.includes('Safari/')) return 'Safari';
            return 'Unknown';
        }

        function setStatus(message, type = 'info') {
            if (!statusDiv) return;
            const styles = {
                info:    'bg-blue-50 dark:bg-blue-900/20 border-blue-200 text-blue-700',
                success: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 text-emerald-700',
                error:   'bg-red-50 dark:bg-red-900/20 border-red-200 text-red-700',
                loading: 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 text-amber-700',
            };
            statusDiv.className = `mb-6 p-3 rounded-xl text-center text-sm font-medium flex items-center justify-center gap-2 border ${styles[type] || styles.info}`;
            statusDiv.innerHTML = `<span>${message}</span>`;
        }

        // ── Infos appareil ──────────────────────────────────────────
        const deviceUuid   = getOrCreateDeviceUuid();
        const fingerprint  = generateFingerprint(deviceUuid);
        const browserName  = detectBrowser();
        const platformName = navigator.userAgentData?.platform || navigator.platform || 'unknown';
        const deviceLabel  = `${browserName} / ${platformName}`;

        // ── Vérification GPS disponible ─────────────────────────────
        if (!navigator.geolocation) {
            setStatus('❌ La géolocalisation n\'est pas disponible sur ce navigateur.', 'error');
            document.querySelectorAll('#btn-checkin, #btn-checkout').forEach(b => b.disabled = true);
            return;
        }

        // ── Fonction générique de pointage ──────────────────────────
        function handlePointing(formId, btnId, prefixId, actionLabel) {
            const btn  = document.getElementById(btnId);
            const form = document.getElementById(formId);

            if (!btn || btn.disabled) return;

            btn.addEventListener('click', () => {

                // Bloquer les deux boutons
                document.getElementById('btn-checkin').disabled  = true;
                document.getElementById('btn-checkout').disabled = true;

                // State chargement
                const originalHTML = btn.innerHTML;
                btn.innerHTML = `
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Recherche GPS...
                `;

                setStatus('📍 Récupération de votre position GPS en cours...', 'loading');

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        // Remplir les champs cachés
                        document.getElementById(`${prefixId}_latitude`).value   = pos.coords.latitude;
                        document.getElementById(`${prefixId}_longitude`).value  = pos.coords.longitude;
                        document.getElementById(`${prefixId}_accuracy`).value   = Math.round(pos.coords.accuracy || 0);
                        document.getElementById(`${prefixId}_fingerprint`).value = fingerprint;
                        document.getElementById(`${prefixId}_uuid`).value       = deviceUuid;
                        document.getElementById(`${prefixId}_label`).value      = deviceLabel;
                        document.getElementById(`${prefixId}_platform`).value   = platformName;
                        document.getElementById(`${prefixId}_browser`).value    = browserName;

                        setStatus(
                            `Position capturée (précision : ${Math.round(pos.coords.accuracy)}m) — redirection vers la validation...`,
                            'success'
                        );

                        // Soumettre après petit délai pour que l'utilisateur voie le message
                        setTimeout(() => form.submit(), 600);
                    },
                    (err) => {
                        let msg = 'Impossible de récupérer votre position.';
                        if (err.code === err.PERMISSION_DENIED)    msg = 'Veuillez autoriser la localisation dans votre navigateur.';
                        if (err.code === err.TIMEOUT)              msg = ' Le GPS a mis trop de temps à répondre. Réessayez.';
                        if (err.code === err.POSITION_UNAVAILABLE) msg = ' Position GPS indisponible. Vérifiez votre connexion.';

                        setStatus(msg, 'error');

                        // Restaurer bouton
                        btn.innerHTML = originalHTML;
                        btn.disabled  = false;

                        // Débloquer l'autre bouton uniquement s'il n'était pas disabled à l'origine
                        const otherBtnId = btnId === 'btn-checkin' ? 'btn-checkout' : 'btn-checkin';
                        const otherBtn = document.getElementById(otherBtnId);
                        if (otherBtn && !otherBtn.hasAttribute('disabled')) {
                            otherBtn.disabled = false;
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0,
                    }
                );
            });
        }

        // ── Attacher les listeners ──────────────────────────────────
        handlePointing('form-checkin',  'btn-checkin',  'checkin',  'arrivée');
        handlePointing('form-checkout', 'btn-checkout', 'checkout', 'départ');
    });
    </script>
    @endpush
</x-app-layout>