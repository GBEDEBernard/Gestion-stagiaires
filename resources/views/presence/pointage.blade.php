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
            $activeStage = $user->etudiant?->stages()
            ->where('date_debut', '<=', now())
                ->where('date_fin', '>=', now())
                ->first();
                $attendanceDay = $activeStage ? \App\Models\AttendanceDay::where('stage_id', $activeStage->id)->whereDate('attendance_date', today())->first() : null;
                @endphp

                @if(!$activeStage)
                <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 rounded-3xl p-10 text-center">
                    <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400 mx-auto mb-4"></i>
                    <h2 class="text-xl font-semibold">Aucun stage actif</h2>
                    <p class="text-slate-500">Contactez l'administration.</p>
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
                    <form method="POST" action="{{ route('presence.prepareCheckin') }}" class="flex-1 presence-form">
                        @csrf
                        <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        <input type="hidden" name="latitude"><input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters"><input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid"><input type="hidden" name="device_label">
                        <input type="hidden" name="platform"><input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">
                        <button type="button" class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm {{ $hasCheckIn ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-[#4154f1] text-white' }}" {{ $hasCheckIn ? 'disabled' : '' }}>
                            <i data-lucide="check" class="w-5 h-5"></i> Pointer l'arrivée
                        </button>
                    </form>
                    {{-- Départ --}}
                    <form method="POST" action="{{ route('presence.prepareCheckout') }}" class="flex-1 presence-form">
                        @csrf
                        <input type="hidden" name="stage_id" value="{{ $activeStage->id }}">
                        <input type="hidden" name="latitude"><input type="hidden" name="longitude">
                        <input type="hidden" name="accuracy_meters"><input type="hidden" name="device_fingerprint">
                        <input type="hidden" name="device_uuid"><input type="hidden" name="device_label">
                        <input type="hidden" name="platform"><input type="hidden" name="browser">
                        <input type="hidden" name="app_version" value="presence-web-v1">
                        <button type="button" class="presence-submit w-full py-3.5 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-sm {{ !$hasCheckIn ? 'bg-red-50 border border-[#eb0000] text-[#eb0000] cursor-not-allowed' : 'bg-white border-2 border-[#eb0000] text-[#eb0000] hover:bg-[#eb0000] hover:text-white' }}" {{ !$hasCheckIn ? 'disabled' : '' }}>
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
<<<<<<< HEAD
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            const getDeviceUuid = () => {
                const key = 'jb_presence_device_uuid';
                let uuid = localStorage.getItem(key);
                if (!uuid) {
                    uuid = crypto.randomUUID ? crypto.randomUUID() : `jb-${Date.now()}-${Math.random().toString(16).slice(2)}`;
                    localStorage.setItem(key, uuid);
                }
                return uuid;
            };

            const generateFingerprint = (uuid) => {
=======

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
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                const raw = [
                    navigator.userAgent,
                    navigator.platform || '',
                    screen.width,
                    screen.height,
                    Intl.DateTimeFormat().resolvedOptions().timeZone,
<<<<<<< HEAD
                    uuid
=======
                    deviceUuid,
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                ].join('|');
                let hash = 0;
                for (let i = 0; i < raw.length; i++) {
                    hash = ((hash << 5) - hash) + raw.charCodeAt(i);
                    hash |= 0;
                }
                return `jb-${Math.abs(hash)}`;
<<<<<<< HEAD
            };

            const detectBrowser = () => {
=======
            }

            function detectBrowser() {
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                const ua = navigator.userAgent;
                if (ua.includes('Edg/')) return 'Edge';
                if (ua.includes('Chrome/')) return 'Chrome';
                if (ua.includes('Firefox/')) return 'Firefox';
                if (ua.includes('Safari/')) return 'Safari';
                return 'Unknown';
<<<<<<< HEAD
            };

            const deviceUuid = getDeviceUuid();
=======
            }

            // ─── Init ────────────────────────────────────────────────────────
            const forms = document.querySelectorAll('.presence-form');
            const status = document.getElementById('presence-status');

            // Pré-calcul des valeurs device (une seule fois)
            const deviceUuid = getOrCreateDeviceUuid();
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
            const fingerprint = generateFingerprint(deviceUuid);
            const browserName = detectBrowser();
            const platformName = navigator.userAgentData?.platform || navigator.platform || 'unknown';
            const deviceLabel = `${browserName} / ${platformName}`;

<<<<<<< HEAD
            const forms = document.querySelectorAll('.presence-form');
            const statusDiv = document.getElementById('presence-status');
            const statusSpan = statusDiv?.querySelector('span');

            if (!navigator.geolocation) {
                statusSpan.textContent = 'GPS non supporté';
                statusDiv.className = 'mb-6 p-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl text-center text-sm font-medium text-red-700 dark:text-red-400 flex items-center justify-center gap-2';
=======
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
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                forms.forEach(f => f.querySelector('.presence-submit').disabled = true);
                return;
            }

<<<<<<< HEAD
            forms.forEach(form => {
                const btn = form.querySelector('.presence-submit');
                if (!btn || btn.disabled) return;

                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    btn.disabled = true;
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = `<i data-lucide="loader" class="w-5 h-5 animate-spin"></i> Localisation...`;
                    statusSpan.textContent = 'Localisation en cours...';
                    statusDiv.className = 'mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl text-center text-sm font-medium text-blue-700 dark:text-blue-400 flex items-center justify-center gap-2';

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            form.querySelector('[name="latitude"]').value = pos.coords.latitude;
                            form.querySelector('[name="longitude"]').value = pos.coords.longitude;
                            form.querySelector('[name="accuracy_meters"]').value = Math.round(pos.coords.accuracy);
=======
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
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                            form.querySelector('[name="device_fingerprint"]').value = fingerprint;
                            form.querySelector('[name="device_uuid"]').value = deviceUuid;
                            form.querySelector('[name="device_label"]').value = deviceLabel;
                            form.querySelector('[name="platform"]').value = platformName;
                            form.querySelector('[name="browser"]').value = browserName;

<<<<<<< HEAD
                            statusSpan.textContent = `Position capturée (${Math.round(pos.coords.accuracy)}m)`;
                            statusDiv.className = 'mb-6 p-3 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-xl text-center text-sm font-medium text-green-700 dark:text-green-400 flex items-center justify-center gap-2';

                            fetch(form.action, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify(Object.fromEntries(new FormData(form)))
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        const preview = data.preview;
                                        let html = `
                                    <div style="text-align: left; font-size: 0.9rem;">
                                        <p><strong>${preview.etudiant_name ?? preview.user_name}</strong></p>
                                        <p>Site : ${preview.site_name}</p>
                                        <p>Heure : ${preview.pointage_time}</p>
                                        <p>Type : ${preview.type}</p>
                                `;
                                        if (preview.distance) {
                                            html += `<p>Distance : ${preview.distance} m</p>`;
                                        }
                                        if (preview.accuracy) {
                                            html += `<p>Précision GPS : ${preview.accuracy} m</p>`;
                                        }
                                        html += `</div>`;

                                        Swal.fire({
                                            title: 'Confirmer le pointage',
                                            html: html,
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Valider',
                                            cancelButtonText: 'Annuler',
                                            confirmButtonColor: '#4154f1',
                                            cancelButtonColor: '#eb0000',
                                            width: '400px',
                                            padding: '1.5rem',
                                            customClass: {
                                                popup: 'rounded-2xl',
                                                title: 'text-lg font-bold',
                                                confirmButton: 'px-4 py-2 text-sm rounded-xl',
                                                cancelButton: 'px-4 py-2 text-sm rounded-xl'
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                fetch('{{ route("presence.confirm.ajax") }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                            'Accept': 'application/json',
                                                        }
                                                    })
                                                    .then(response => response.json())
                                                    .then(confirmData => {
                                                        if (confirmData.success) {
                                                            Swal.fire({
                                                                title: 'Validé !',
                                                                text: confirmData.message,
                                                                icon: 'success',
                                                                confirmButtonColor: '#4154f1',
                                                                width: '360px',
                                                                padding: '1.5rem'
                                                            }).then(() => window.location.href = confirmData.redirect);
                                                        } else if (confirmData.rejected) {
                                                            Swal.fire({
                                                                title: 'Pointage refusé',
                                                                text: confirmData.reason,
                                                                icon: 'error',
                                                                confirmButtonColor: '#4154f1',
                                                                width: '400px',
                                                                padding: '1.5rem'
                                                            }).then(() => window.location.reload());
                                                        } else {
                                                            Swal.fire({
                                                                title: 'Erreur',
                                                                text: confirmData.message,
                                                                icon: 'error',
                                                                confirmButtonColor: '#4154f1'
                                                            });
                                                            btn.disabled = false;
                                                            btn.innerHTML = originalHTML;
                                                            statusSpan.textContent = 'Prêt pour la localisation';
                                                            statusDiv.className = 'mb-6 p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-center text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2 border border-slate-100 dark:border-slate-700';
                                                        }
                                                    });
                                            } else {
                                                btn.disabled = false;
                                                btn.innerHTML = originalHTML;
                                                statusSpan.textContent = 'Prêt pour la localisation';
                                                statusDiv.className = 'mb-6 p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-center text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2 border border-slate-100 dark:border-slate-700';
                                            }
                                        });
                                    } else {
                                        throw new Error('Erreur serveur');
                                    }
                                })
                                .catch(err => {
                                    Swal.fire({
                                        title: 'Erreur',
                                        text: 'Impossible de préparer le pointage.',
                                        icon: 'error',
                                        confirmButtonColor: '#4154f1',
                                        width: '400px'
                                    });
                                    btn.disabled = false;
                                    btn.innerHTML = originalHTML;
                                    statusSpan.textContent = 'Prêt pour la localisation';
                                    statusDiv.className = 'mb-6 p-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl text-center text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2 border border-slate-100 dark:border-slate-700';
                                });
                        },
                        (err) => {
                            let msg = 'Position indisponible';
                            if (err.code === 1) msg = 'Accès à la localisation refusé';
                            statusSpan.textContent = msg;
                            statusDiv.className = 'mb-6 p-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl text-center text-sm font-medium text-red-700 dark:text-red-400 flex items-center justify-center gap-2';
                            btn.disabled = false;
                            btn.innerHTML = originalHTML;
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
=======
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
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                        }
                    );
                });
            });
        });
    </script>
    @endpush
<<<<<<< HEAD
</x-pointage-layout>
=======

</x-app-layout>
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
