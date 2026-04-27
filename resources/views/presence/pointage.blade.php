<x-app-layout>
    <div class="w-full max-w-3xl mx-auto">
        @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700">
            Le pointage n'a pas pu être validé.
            <ul class="mt-2 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-bold text-slate-900 text-sm sm:text-base">Pointage de présence</p>
                    <p class="text-xs text-slate-500">Pointage géolocalisé</p>
                </div>
            </div>

            <div class="p-4 sm:p-6 space-y-5">
                @if (session('success'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-700 text-sm">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700 text-sm">
                    {{ session('error') }}
                </div>
                @endif

                @if (! $activeStage)
                <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-4 text-amber-800 text-sm">
                    Aucun stage actif n'est disponible pour le pointage aujourd'hui.
                </div>
                @else
                <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Stage actif</p>
                        <p class="text-base font-semibold text-slate-900">{{ $activeStage->theme ?: 'Stage sans thème' }}</p>
                        <p class="text-sm text-slate-600 mt-0.5">{{ $activeStage->site?->name ?: 'Site non défini' }}</p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Statut du jour</p>
                        <p class="text-base font-semibold text-slate-900 capitalize">{{ $attendanceDay?->day_status ?: 'En attente' }}</p>
                        <p class="text-sm text-slate-600 mt-0.5">
                            Arrivée : <strong>{{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}</strong>
                            &nbsp;·&nbsp;
                            Départ : <strong>{{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }}</strong>
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 p-4 sm:p-5">
                    <p class="text-sm font-semibold text-slate-700 mb-3">Pointage rapide</p>

                    <div id="presence-status" class="mb-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600">
                        La localisation sera capturée automatiquement au moment du pointage.
                    </div>

                    {{-- Arrivée --}}
                    <form method="POST" action="{{ route('presence.checkin') }}" class="presence-form" data-presence-action="arrivee" novalidate>
                        @csrf
                        <input type="hidden" name="stage_id"          value="{{ $activeStage->id }}">
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters">
                        <input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid">
                        <input type="hidden" name="device_label">
                        <input type="hidden" name="platform">
                        <input type="hidden" name="browser">
                        <input type="hidden" name="app_version"       value="presence-web-v1">
                        <button type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white presence-submit hover:bg-slate-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Pointer l'arrivée
                        </button>
                    </form>

                    {{-- Départ --}}
                    <form method="POST" action="{{ route('presence.checkout') }}" class="presence-form mt-3" data-presence-action="depart" novalidate>
                        @csrf
                        <input type="hidden" name="stage_id"          value="{{ $activeStage->id }}">
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters">
                        <input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid">
                        <input type="hidden" name="device_label">
                        <input type="hidden" name="platform">
                        <input type="hidden" name="browser">
                        <input type="hidden" name="app_version"       value="presence-web-v1">
                        <button type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white presence-submit hover:bg-emerald-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Pointer le départ
                        </button>
                    </form>

                    <div class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        Besoin de décrire la journée ensuite ?
                        <a href="{{ route('reports.index') }}" class="font-semibold text-slate-900 underline underline-offset-2 hover:text-emerald-700">
                            Ouvrir le rapport journalier →
                        </a>
                    </div>
                </div>

                @if($activeStage && isset($attendanceDays))
                <x-presence-history-table :attendance-days="$attendanceDays" :period="$period" />
                @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    const bootPresenceForms = () => {
        const forms = document.querySelectorAll('.presence-form');
        const statusBox = document.getElementById('presence-status');

        if (!forms.length || !statusBox) return;

        if (!('geolocation' in navigator)) {
            statusBox.textContent = 'La géolocalisation n\'est pas disponible sur ce navigateur.';
            statusBox.className = 'mb-4 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700';
            forms.forEach(form => form.querySelector('.presence-submit').disabled = true);
            return;
        }

        const browserName   = detectBrowser();
        const deviceUuid    = getDeviceUuid();
        const platformName  = navigator.userAgentData?.platform || navigator.platform || 'unknown';
        const fingerprint   = hashValue([navigator.userAgent, platformName, window.screen.width, window.screen.height, Intl.DateTimeFormat().resolvedOptions().timeZone, deviceUuid].join('|'));

        forms.forEach(form => {
            const submitButton = form.querySelector('.presence-submit');
            if (!submitButton || submitButton.dataset.presenceBound === 'true') return;
            submitButton.dataset.presenceBound = 'true';

            form.querySelector('[name="device_uuid"]').value        = deviceUuid;
            form.querySelector('[name="device_label"]').value       = `${browserName} / ${platformName}`;
            form.querySelector('[name="device_fingerprint"]').value = fingerprint;
            form.querySelector('[name="platform"]').value           = platformName;
            form.querySelector('[name="browser"]').value            = browserName;

            submitButton.addEventListener('click', () => {
                const actionLabel = form.dataset.presenceAction || 'présence';
                submitButton.disabled = true;
                submitButton.dataset.originalLabel = submitButton.textContent.trim();
                submitButton.textContent = 'Localisation en cours...';
                setStatus(`Vérification de la localisation pour le pointage ${actionLabel}...`, 'info');

                navigator.geolocation.getCurrentPosition(
                    position => {
                        form.querySelector('[name="latitude"]').value        = position.coords.latitude;
                        form.querySelector('[name="longitude"]').value       = position.coords.longitude;
                        form.querySelector('[name="accuracy_meters"]').value = Math.round(position.coords.accuracy || 0);
                        setStatus(`Localisation capturée (${Math.round(position.coords.accuracy || 0)} m). Validation en cours...`, 'success');
                        form.submit();
                    },
                    error => {
                        let message = 'Impossible de récupérer votre position.';
                        if (error.code === error.PERMISSION_DENIED)      message = 'Autorisez la localisation pour valider la présence.';
                        else if (error.code === error.TIMEOUT)           message = 'Le GPS a mis trop de temps à répondre. Réessayez plus près du site.';
                        else if (error.code === error.POSITION_UNAVAILABLE) message = 'La position n\'est pas disponible pour le moment.';
                        setStatus(message, 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = submitButton.dataset.originalLabel || 'Pointer';
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            });
        });

        function setStatus(message, tone) {
            statusBox.textContent = message;
            const map = {
                info:    'mb-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600',
                success: 'mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700',
                error:   'mb-4 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700',
            };
            statusBox.className = map[tone] || map.info;
        }

        function getDeviceUuid() {
            const key = 'jb_presence_device_uuid';
            let uuid = window.localStorage.getItem(key);
            if (!uuid) {
                uuid = window.crypto?.randomUUID ? window.crypto.randomUUID() : `jb-${Date.now()}-${Math.random().toString(16).slice(2)}`;
                window.localStorage.setItem(key, uuid);
            }
            return uuid;
        }

        function detectBrowser() {
            const ua = navigator.userAgent;
            if (ua.includes('Edg/'))     return 'Edge';
            if (ua.includes('Chrome/'))  return 'Chrome';
            if (ua.includes('Firefox/')) return 'Firefox';
            if (ua.includes('Safari/'))  return 'Safari';
            return 'Unknown';
        }

        function hashValue(value) {
            let hash = 0;
            for (let i = 0; i < value.length; i++) {
                hash = ((hash << 5) - hash) + value.charCodeAt(i);
                hash |= 0;
            }
            return `jb-${Math.abs(hash)}`;
        }
    };

    @if(session('rejection_reason'))
    Swal.fire({
        icon: 'error',
        title: '🚫 Pointage refusé',
        html: `{!! addslashes(session('rejection_reason')) !!}<br><br><strong>Déplacez-vous vers le site et réessayez.</strong>`,
        confirmButtonText: 'OK, je réessaie',
        confirmButtonColor: '#10b981',
    });
    @endif

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootPresenceForms);
    } else {
        bootPresenceForms();
    }
</script>
@endpush