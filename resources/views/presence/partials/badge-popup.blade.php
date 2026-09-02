@php
    // Proposé une seule fois, à la première connexion depuis ce téléphone,
    // et seulement si l'utilisateur n'a pas déjà un badge actif.
    $dejaBadge = auth()->user()?->trustedDevices()->activeQrBadges()->exists();
@endphp

@if(session('proposer_badge') && !$dejaBadge)
<div x-data="badgePopup()" x-show="ouvert" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
     style="background: rgba(15, 18, 22, .45)"
     @click.self="ouvert = false">

    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 shadow-xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-6">
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M12 18h.01" stroke-linecap="round"/>
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Faire de ce téléphone votre badge</h3>
            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                Vos prochains scans à la porte seront immédiats, sans reconnexion.
            </p>

            <div class="mt-5 flex flex-col gap-2">
                <button type="button" @click="enregistrer()" :disabled="busy"
                    class="w-full px-4 py-2.5 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium text-sm hover:opacity-90 disabled:opacity-60 transition">
                    <span x-show="!busy">Enregistrer ce téléphone</span>
                    <span x-show="busy" x-cloak>Enregistrement…</span>
                </button>

                <button type="button" @click="ouvert = false"
                    class="w-full px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-300 font-medium text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Plus tard
                </button>
            </div>

            <p x-show="erreur" x-cloak class="mt-3 text-xs text-red-600 dark:text-red-400" x-text="erreur"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function badgePopup() {
        return {
            ouvert: true,
            busy: false,
            erreur: '',

            async enregistrer() {
                this.busy = true;
                this.erreur = '';

                // Même empreinte que le pointage, sinon le téléphone serait vu
                // comme un appareil différent au scan suivant.
                const uuid = localStorage.getItem('tfg_device_uuid')
                    || ('dev_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now().toString(36));
                localStorage.setItem('tfg_device_uuid', uuid);

                const brut = `${uuid}|${screen.width}x${screen.height}x${screen.colorDepth}`
                    + `|${Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC'}`
                    + `|${navigator.userAgent}|${navigator.language}|${navigator.hardwareConcurrency || 2}`;

                let h = 0;
                for (let i = 0; i < brut.length; i++) { h = ((h << 5) - h) + brut.charCodeAt(i); h |= 0; }
                const empreinte = 'fp_' + Math.abs(h).toString(36) + '_' + uuid.substring(0, 8);

                try {
                    const r = await fetch(@json(route('presence.devices.enroll')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        },
                        body: JSON.stringify({
                            device_fingerprint: empreinte,
                            device_uuid: uuid,
                            device_name: 'Mon téléphone',
                        }),
                    });

                    const data = await r.json();

                    if (!r.ok || !data.success) {
                        this.busy = false;
                        this.erreur = data.message ?? "L'enregistrement n'a pas abouti.";
                        return;
                    }

                    this.ouvert = false;
                } catch (e) {
                    this.busy = false;
                    this.erreur = "Connexion interrompue. Réessayez.";
                }
            },
        };
    }
</script>
@endpush
@endif
