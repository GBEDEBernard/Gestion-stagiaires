{{--
    Départ oublié la veille. La modale n'apparaît qu'une fois l'arrivée du jour
    pointée : la question d'hier ne doit pas retarder le geste d'aujourd'hui.

    Ce qui est saisi ici n'est pas appliqué. La journée reste clôturée à
    l'heure de fin prévue tant que le responsable n'a pas tranché — sinon
    chacun déclarerait 20h.

    Attendus : $journee (AttendanceDay), $declarationUrl
--}}
<div x-data="{ ouvert: true, heure: '', motif: '', erreur: '' }"
     x-show="ouvert" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
     style="background: rgba(15, 18, 22, .5)"
     @keydown.escape.window="ouvert = false">

    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 shadow-xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <form method="POST" action="{{ $declarationUrl }}" class="p-6"
              @submit="if (heure === '' || motif.trim().length < 10) { $event.preventDefault(); erreur = heure === '' ? 'Indiquez votre heure de départ.' : 'Le motif doit faire au moins dix caractères.'; }">
            @csrf
            <input type="hidden" name="day_id" value="{{ $journee->id }}">

            <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Hier, votre départ n'a pas été pointé
            </h3>
            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                {{-- On ne qualifie pas l'heure de clôture : ce n'est pas toujours
                     la fin prévue, une arrivée tardive la repousse. --}}
                La journée du <strong class="text-gray-700 dark:text-gray-200">{{ $journee->attendance_date->format('d/m/Y') }}</strong>
                a été clôturée d'office à {{ $journee->last_check_out_at?->format('H:i') ?? '--:--' }}.
                À quelle heure êtes-vous réellement parti ?
            </p>

            <label class="mt-4 block">
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Heure de départ</span>
                <input type="time" name="claimed_time" x-model="heure" required
                       class="mt-1.5 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-white
                              focus:border-amber-500 focus:ring-2 focus:ring-amber-500/40 shadow-sm text-sm tabular-nums">
            </label>

            <label class="mt-3 block">
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Motif</span>
                <textarea name="claimed_reason" x-model="motif" rows="3" maxlength="500" required
                          placeholder="Ex. : téléphone déchargé, je suis parti à 18h30."
                          class="mt-1.5 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-white
                                 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/40 shadow-sm text-sm"></textarea>
            </label>

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-show="!erreur">
                Votre déclaration ne modifie pas la journée : présentez-vous à votre responsable,
                lui seul peut rétablir l'heure.
            </p>
            <p class="mt-2 text-xs text-red-600 dark:text-red-400" x-show="erreur" x-cloak x-text="erreur"></p>

            <div class="mt-5 flex flex-col gap-2">
                <button type="submit"
                    class="w-full px-4 py-2.5 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium text-sm hover:opacity-90 transition">
                    Envoyer ma déclaration
                </button>
                <button type="button" @click="ouvert = false"
                    class="w-full px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-300 font-medium text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Plus tard
                </button>
            </div>
        </form>
    </div>
</div>
