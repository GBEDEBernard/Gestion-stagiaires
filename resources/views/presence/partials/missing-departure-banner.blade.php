@php
    $day = $missingDepartureDay ?? null;
@endphp

@if($missingDeparture ?? false)
<div x-data="missingDepartureBanner()"
     x-show="visible"
     x-cloak
     class="fixed top-0 left-0 right-0 z-[100] animate-[slideDown_200ms_ease-out]"
     style="background: rgba(245, 158, 11, .97); color: #1c1917;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-start gap-3 min-w-0">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div class="min-w-0">
                <p class="text-sm font-semibold leading-snug">
                    Départ non pointé — journée clôturée d'office
                </p>
                <p class="text-xs opacity-90 mt-0.5 break-words">
                    La journée du <strong class="font-semibold" x-text="dayLabel"></strong>
                    est fermée sans votre départ. Déclarez votre heure réelle pour que votre responsable la rétablisse.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex gap-2">
            <button type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-amber-900 font-semibold text-sm hover:bg-amber-50 transition"
                @click="openDeclaration">
                Déclarer mon départ
            </button>
            <button type="button"
                class="px-3 py-2 rounded-lg text-amber-900 font-medium text-sm hover:bg-white/20 transition"
                @click="dismiss">
                Masquer
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function missingDepartureBanner() {
        return {
            visible: @json((bool) ($missingDeparture ?? false)),
            dayLabel: @json($day?->attendance_date?->locale('fr')->isoFormat('dddd D MMMM YYYY') ?? ''),
            dayId: @json($day?->id),

            dismiss() {
                this.visible = false;
                if (window.alpine) {
                    try {
                        const el = document.currentScript?.parentElement;
                        if (el && el.__x) {
                            el.__x.$data.visible = false;
                        }
                    } catch (e) {}
                }
                sessionStorage.setItem('tfg_missing_departure_dismissed', '1');
            },

            openDeclaration() {
                // Deux écrans, deux comportements.
                //
                // Sur la page de pointage, la modale de déclaration est déjà
                // là : on l'amène à l'écran. La déclaration se fait par un POST,
                // et la route presence.depart-oublie refuse le GET — d'où le
                // window.location qui produisait « Method Not Allowed ».
                //
                // Partout ailleurs (tableau de bord, historique), la modale
                // n'existe pas : le bouton emmène alors vers la page de
                // pointage, où la déclaration pourra être faite.
                const modale = document.querySelector('[data-declaration-oubliee]');
                if (modale) {
                    modale.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                window.location.href = @json(route('presence.pointage'));
            },
        };
    }
</script>
@endpush
@endif
