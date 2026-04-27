<x-app-layout title="Pointage">
    @php
        $user = auth()->user();
        $isEmployee = isset($domaine) && ! $activeStage;
        $hasCheckIn = (bool) $attendanceDay?->first_check_in_at;
        $hasCheckOut = (bool) $attendanceDay?->last_check_out_at;
        $contextLabel = $isEmployee
            ? ($domaine->nom ?? 'Domaine non assigne')
            : ($activeStage?->theme ?? 'Aucun stage actif');
        $locationLabel = $isEmployee
            ? 'Pointage employe'
            : ($activeStage?->site?->name ?? 'Site non defini');
    @endphp

    <main class="max-w-2xl mx-auto px-4 sm:px-6 py-10 md:py-14">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                Bonjour, <span class="text-[#4154f1]">{{ \Illuminate\Support\Str::before($user->name, ' ') ?: $user->name }}</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">
                {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(!$isEmployee && !$activeStage)
            <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-3xl p-10 text-center">
                <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400 mx-auto mb-4"></i>
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Aucun stage actif</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Contactez l'administration pour activer ou affecter votre stage.</p>
            </div>
        @else
            <div class="bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 md:p-8 shadow-sm mb-8">
                <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h3 class="text-xs font-semibold text-[#4154f1] uppercase tracking-wider mb-3">
                            {{ $isEmployee ? 'Statut employe' : 'Statut de presence' }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $contextLabel }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $locationLabel }}</p>
                    </div>

                    <div class="flex flex-row items-center justify-around md:justify-start md:gap-12">
                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase mb-1">Arrivee</p>
                            <p class="text-3xl md:text-4xl font-bold {{ $attendanceDay?->first_check_in_at ? (($attendanceDay->late_minutes ?? 0) > 0 ? 'text-[#eb0000]' : 'text-emerald-600') : 'text-slate-400' }}">
                                {{ $attendanceDay?->first_check_in_at?->format('H:i') ?? '--:--' }}
                            </p>
                        </div>

                        <div class="w-px h-10 bg-slate-200 dark:bg-slate-700"></div>

                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase mb-1">Depart</p>
                            <p class="text-3xl md:text-4xl font-bold {{ $attendanceDay?->last_check_out_at ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                {{ $attendanceDay?->last_check_out_at?->format('H:i') ?? '--:--' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="presence-status" class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-center text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-3 border border-slate-200 dark:border-slate-700">
                <span id="presence-status-icon" class="flex h-9 w-9 items-center justify-center rounded-full bg-[#4154f1]/10 text-[#4154f1]">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                </span>
                <div class="text-left">
                    <p id="presence-status-title" class="font-semibold text-slate-700 dark:text-slate-200">Pret pour la localisation</p>
                    <p id="presence-status-detail" class="text-xs text-slate-500 dark:text-slate-400">Le navigateur attend votre action pour demander la permission GPS.</p>
                </div>
            </div>

            @if($hasCheckOut)
                <div class="w-full p-8 bg-blue-50/50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-2xl flex flex-col items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-8 h-8 text-[#4154f1]"></i>
                    <p class="text-lg font-semibold text-[#4154f1]">Journee terminee</p>
                </div>
            @else
                <div class="flex flex-col sm:flex-row gap-4">
                    <form method="POST" action="{{ route('presence.prepareCheckin') }}" class="flex-1 presence-form">
                        @csrf
                        @if($activeStage)
                            <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        @endif
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters">
                        <input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid">
                        <input type="hidden" name="device_label">
                        <input type="hidden" name="platform">
                        <input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">

                        <button
                            type="button"
                            class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm {{ $hasCheckIn ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed' : 'bg-[#4154f1] text-white hover:opacity-95' }}"
                            data-default-label="Pointer l'arrivee"
                            data-progress-label="Envoi arrivee"
                            {{ $hasCheckIn ? 'disabled' : '' }}
                        >
                            <i data-lucide="check" class="w-5 h-5"></i>
                            {{ $hasCheckIn ? "Arrivee deja pointee" : "Pointer l'arrivee" }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('presence.prepareCheckout') }}" class="flex-1 presence-form">
                        @csrf
                        @if($activeStage)
                            <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        @endif
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters">
                        <input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid">
                        <input type="hidden" name="device_label">
                        <input type="hidden" name="platform">
                        <input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">

                        <button
                            type="button"
                            class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm {{ !$hasCheckIn ? 'bg-red-50 dark:bg-red-500/10 border border-[#eb0000]/20 text-[#eb0000] cursor-not-allowed' : 'bg-white dark:bg-slate-800 border-2 border-[#eb0000] text-[#eb0000] hover:bg-[#eb0000] hover:text-white' }}"
                            data-default-label="Pointer le depart"
                            data-progress-label="Envoi depart"
                            {{ !$hasCheckIn ? 'disabled' : '' }}
                        >
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                            {{ !$hasCheckIn ? "Arrivee requise d'abord" : "Pointer le depart" }}
                        </button>
                    </form>
                </div>
            @endif

        @endif
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const forms = document.querySelectorAll('.presence-form');
                const statusRoot = document.getElementById('presence-status');
                const statusIcon = document.getElementById('presence-status-icon');
                const statusTitle = document.getElementById('presence-status-title');
                const statusDetail = document.getElementById('presence-status-detail');

                const toneClasses = {
                    neutral: {
                        root: 'mb-6 p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-3 border border-slate-200 dark:border-slate-700',
                        icon: 'flex h-9 w-9 items-center justify-center rounded-full bg-[#4154f1]/10 text-[#4154f1]',
                        iconName: 'map-pin',
                    },
                    info: {
                        root: 'mb-6 p-4 bg-blue-50 dark:bg-blue-500/10 rounded-xl text-sm font-medium text-blue-700 dark:text-blue-300 flex items-center justify-center gap-3 border border-blue-100 dark:border-blue-500/20',
                        icon: 'flex h-9 w-9 items-center justify-center rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-300',
                        iconName: 'locate-fixed',
                    },
                    success: {
                        root: 'mb-6 p-4 bg-green-50 dark:bg-green-500/10 rounded-xl text-sm font-medium text-green-700 dark:text-green-300 flex items-center justify-center gap-3 border border-green-100 dark:border-green-500/20',
                        icon: 'flex h-9 w-9 items-center justify-center rounded-full bg-green-500/10 text-green-600 dark:text-green-300',
                        iconName: 'badge-check',
                    },
                    danger: {
                        root: 'mb-6 p-4 bg-red-50 dark:bg-red-500/10 rounded-xl text-sm font-medium text-red-700 dark:text-red-300 flex items-center justify-center gap-3 border border-red-100 dark:border-red-500/20',
                        icon: 'flex h-9 w-9 items-center justify-center rounded-full bg-red-500/10 text-red-600 dark:text-red-300',
                        iconName: 'octagon-alert',
                    },
                };

                const setStatus = (tone, title, detail) => {
                    if (!statusRoot) {
                        return;
                    }

                    const config = toneClasses[tone] || toneClasses.neutral;
                    statusRoot.className = config.root;
                    statusIcon.className = config.icon;
                    statusIcon.innerHTML = `<i data-lucide="${config.iconName}" class="w-4 h-4"></i>`;
                    statusTitle.textContent = title;
                    statusDetail.textContent = detail;
                    window.refreshLucideIcons();
                };

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
                    if (ua.includes('Edg/')) return 'Edge';
                    if (ua.includes('Chrome/')) return 'Chrome';
                    if (ua.includes('Firefox/')) return 'Firefox';
                    if (ua.includes('Safari/')) return 'Safari';
                    return 'Unknown';
                }

                @if(session('rejection_reason'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Pointage refuse',
                        text: @json(session('rejection_reason')),
                        confirmButtonText: 'Compris',
                        confirmButtonColor: '#4154f1',
                    });
                @endif

                if (!statusRoot || forms.length === 0) {
                    return;
                }

                if (!navigator.geolocation) {
                    setStatus('danger', 'GPS indisponible', 'Ce navigateur ne permet pas la geolocalisation requise pour le pointage.');
                    forms.forEach((form) => form.querySelector('.presence-submit').disabled = true);
                    return;
                }

                const deviceUuid = getOrCreateDeviceUuid();
                const fingerprint = generateFingerprint(deviceUuid);
                const browserName = detectBrowser();
                const platformName = navigator.userAgentData?.platform || navigator.platform || 'unknown';
                const deviceLabel = `${browserName} / ${platformName}`;

                setStatus('neutral', 'Pret pour la localisation', 'Le navigateur attend votre action pour demander la permission GPS.');

                forms.forEach((form) => {
                    const button = form.querySelector('.presence-submit');

                    if (!button || button.disabled) {
                        return;
                    }

                    const defaultLabel = button.dataset.defaultLabel || 'Envoyer';
                    const progressLabel = button.dataset.progressLabel || 'Envoi';

                    button.addEventListener('click', () => {
                        button.disabled = true;
                        button.textContent = 'Demande GPS...';
                        setStatus('info', 'Demande GPS', 'Autorisez la localisation dans votre navigateur pour poursuivre.');

                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                form.querySelector('[name="latitude"]').value = position.coords.latitude;
                                form.querySelector('[name="longitude"]').value = position.coords.longitude;
                                form.querySelector('[name="accuracy_meters"]').value = Math.round(position.coords.accuracy || 0);
                                form.querySelector('[name="device_fingerprint"]').value = fingerprint;
                                form.querySelector('[name="device_uuid"]').value = deviceUuid;
                                form.querySelector('[name="device_label"]').value = deviceLabel;
                                form.querySelector('[name="platform"]').value = platformName;
                                form.querySelector('[name="browser"]').value = browserName;

                                button.textContent = `${progressLabel}...`;
                                setStatus('success', 'Position capturee', `Precision approx. ${Math.round(position.coords.accuracy || 0)} m. Preparation de l'envoi en cours.`);
                                form.submit();
                            },
                            (error) => {
                                let message = 'Impossible de recuperer votre position.';

                                if (error.code === error.PERMISSION_DENIED) {
                                    message = 'La permission GPS a ete refusee. Autorisez la localisation puis recommencez.';
                                } else if (error.code === error.TIMEOUT) {
                                    message = 'Le delai GPS a expire. Reessayez dans une zone mieux couverte.';
                                } else if (error.code === error.POSITION_UNAVAILABLE) {
                                    message = 'La position est temporairement indisponible sur cet appareil.';
                                }

                                setStatus('danger', 'Pointage interrompu', message);
                                button.disabled = false;
                                button.textContent = defaultLabel;
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
