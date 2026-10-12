@php
    // Proposé une seule fois, à la première connexion depuis ce téléphone,
    // et seulement si l'utilisateur n'a pas déjà un badge actif.
    // Un compte de gestion n'a pas de badge à faire : on ne le lui propose pas,
    // plutôt que de lui laisser un refus en pleine page de pointage.
    $dejaBadge     = auth()->user()?->trustedDevices()->activeQrBadges()->exists();
    $peutPointer   = (bool) auth()->user()?->peutPointer();
@endphp

@if(session('proposer_badge') && !$dejaBadge && $peutPointer)
<div x-data="badgePopup()" x-show="ouvert" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-4"
     style="background: rgba(15, 18, 22, .6); backdrop-filter: blur(2px);"
     @click.self="ouvert = false">

    <div class="w-full max-w-sm rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-4 sm:p-5">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-8 h-8 rounded-lg bg-[#eef1fb] dark:bg-[#1c3e96]/20 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center flex-shrink-0 text-[#1c3e96] dark:text-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M12 18h.01" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-[#241f1b] dark:text-white leading-tight">Activer ce téléphone comme badge</h3>
                    <p class="mt-0.5 text-xs text-[#6e6862] dark:text-slate-400 leading-snug">
                        Vos scans à la borne d'entrée seront instantanés sans besoin de vous reconnecter.
                    </p>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-2">
                <button type="button" @click="enregistrer()" :disabled="busy"
                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-md bg-[#1c3e96] hover:bg-[#162f73] text-white font-semibold text-xs shadow-2xs disabled:opacity-60 transition">
                    <span x-show="!busy">Activer ce smartphone</span>
                    <span x-show="busy" x-cloak>Enregistrement en cours…</span>
                </button>

                <button type="button" @click="ouvert = false"
                    class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded-md bg-white dark:bg-[#15181f] border border-[#bfbab2] dark:border-[#2b3240] text-[#241f1b] dark:text-slate-300 hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] font-medium text-xs transition">
                    Plus tard
                </button>
            </div>

            <p x-show="erreur" x-cloak class="mt-2 text-[11px] text-red-600 dark:text-red-400 font-medium" x-text="erreur"></p>
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
