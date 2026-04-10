<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">
        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700">
                <p class="font-medium">Le pointage n'a pas pu etre valide.</p>
                <ul class="mt-2 space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <h1 class="text-2xl font-semibold text-slate-900">Pointage de presence</h1>
                <p class="text-sm text-slate-500 mt-1">Pointage geolocalise, pense pour rester rapide et fiable.</p>
            </div>

            <div class="p-6 space-y-6">
                @if (session('success'))
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if (! $activeStage)
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-4 text-amber-800">
                        Aucun stage actif n'est disponible pour le pointage aujourd'hui.
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-sm text-slate-500">Stage actif</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $activeStage->theme ?: 'Stage sans theme' }}</p>
                            <p class="text-sm text-slate-600 mt-1">{{ $activeStage->site?->name ?: 'Site non defini' }}</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-sm text-slate-500">Statut du jour</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $attendanceDay?->day_status ?: 'pending' }}</p>
                            <p class="text-sm text-slate-600 mt-1">
                                Arrivee: {{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}
                                |
                                Depart: {{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-5">
                        <p class="text-sm font-medium text-slate-700 mb-4">Pointage rapide</p>

                        <div id="presence-status" class="mb-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            La localisation sera capturee automatiquement au moment du pointage.
                        </div>

                        <form method="POST" action="{{ route('presence.checkin') }}" class="space-y-4 presence-form" data-presence-action="arrivee" novalidate>
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

                            <div class="flex flex-wrap gap-3">
                                <button type="button" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-white presence-submit">
                                    Pointer l'arrivee
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('presence.checkout') }}" class="space-y-4 mt-4 presence-form" data-presence-action="depart" novalidate>
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

                            <div class="flex flex-wrap gap-3">
                                <button type="button" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2.5 text-white presence-submit">
                                    Pointer le depart
                                </button>
                            </div>
                        </form>

                        <div class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                            Besoin de decrire la journee ensuite ?
                            <a href="{{ route('reports.index') }}" class="font-medium text-slate-900 underline underline-offset-2">
                                Ouvrir le rapport journalier
                            </a>
                        </div>
                    </div>
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

            if (!forms.length || !statusBox) {
                return;
            }

            if (!('geolocation' in navigator)) {
                statusBox.textContent = 'La geolocalisation n\'est pas disponible sur ce navigateur.';
                statusBox.className = 'mb-4 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700';
                forms.forEach((form) => {
                    form.querySelector('.presence-submit').disabled = true;
                });
                return;
            }

            const browserName = detectBrowser();
            const deviceUuid = getDeviceUuid();
            const platformName = navigator.userAgentData?.platform || navigator.platform || 'unknown';
            const fingerprint = hashValue([
                navigator.userAgent,
                platformName,
                window.screen.width,
                window.screen.height,
                Intl.DateTimeFormat().resolvedOptions().timeZone,
                deviceUuid,
            ].join('|'));

            forms.forEach((form) => {
                const submitButton = form.querySelector('.presence-submit');

                if (!submitButton || submitButton.dataset.presenceBound === 'true') {
                    return;
                }

                submitButton.dataset.presenceBound = 'true';

                form.querySelector('[name="device_uuid"]').value = deviceUuid;
                form.querySelector('[name="device_label"]').value = `${browserName} / ${platformName}`;
                form.querySelector('[name="device_fingerprint"]').value = fingerprint;
                form.querySelector('[name="platform"]').value = platformName;
                form.querySelector('[name="browser"]').value = browserName;

                submitButton.addEventListener('click', () => {
                    const actionLabel = form.dataset.presenceAction || 'presence';
                    submitButton.disabled = true;
                    submitButton.dataset.originalLabel = submitButton.textContent.trim();
                    submitButton.textContent = 'Localisation en cours...';
                    setStatus(`Verification de la localisation pour le pointage ${actionLabel}...`, 'info');

                    // jb -> On declenche le GPS uniquement sur clic explicite
                    // puis on soumet ensuite le formulaire une fois les champs
                    // caches correctement remplis.
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            form.querySelector('[name="latitude"]').value = position.coords.latitude;
                            form.querySelector('[name="longitude"]').value = position.coords.longitude;
                            form.querySelector('[name="accuracy_meters"]').value = Math.round(position.coords.accuracy || 0);

                            setStatus(
                                `Localisation capturee (${Math.round(position.coords.accuracy || 0)} m). Validation du pointage en cours...`,
                                'success'
                            );

                            if (!form.querySelector('[name="latitude"]').value || !form.querySelector('[name="longitude"]').value) {
                                setStatus('La geolocalisation a echoue juste avant l\'envoi. Reessaie en laissant le GPS actif.', 'error');
                                submitButton.disabled = false;
                                submitButton.textContent = submitButton.dataset.originalLabel || 'Pointer';
                                return;
                            }

                            form.submit();
                        },
                        (error) => {
                            let message = 'Impossible de recuperer votre position.';

                            if (error.code === error.PERMISSION_DENIED) {
                                message = 'Autorise la localisation pour valider la presence.';
                            } else if (error.code === error.TIMEOUT) {
                                message = 'Le GPS a mis trop de temps a repondre. Reessaie plus pres du site.';
                            } else if (error.code === error.POSITION_UNAVAILABLE) {
                                message = 'La position n\'est pas disponible pour le moment.';
                            }

                            setStatus(message, 'error');
                            submitButton.disabled = false;
                            submitButton.textContent = submitButton.dataset.originalLabel || 'Pointer';
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0,
                        }
                    );
                });
            });

            function setStatus(message, tone) {
                statusBox.textContent = message;

                const classMap = {
                    info: 'mb-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600',
                    success: 'mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700',
                    error: 'mb-4 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700',
                };

                statusBox.className = classMap[tone] || classMap.info;
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
                const userAgent = navigator.userAgent;

                if (userAgent.includes('Edg/')) {
                    return 'Edge';
                }

                if (userAgent.includes('Chrome/')) {
                    return 'Chrome';
                }

                if (userAgent.includes('Firefox/')) {
                    return 'Firefox';
                }

                if (userAgent.includes('Safari/')) {
                    return 'Safari';
                }

                return 'Unknown';
            }

            function hashValue(value) {
                let hash = 0;

                for (let index = 0; index < value.length; index += 1) {
                    hash = ((hash << 5) - hash) + value.charCodeAt(index);
                    hash |= 0;
                }

                return `jb-${Math.abs(hash)}`;
            }
        };

        // jb -> Cette vue peut etre injectee apres que la page ait deja
        // fini de charger. On demarre donc le module tout de suite si le
        // DOM est pret, sinon on attend l'evenement standard.
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bootPresenceForms);
        } else {
            bootPresenceForms();
        }
    </script>
@endpush
