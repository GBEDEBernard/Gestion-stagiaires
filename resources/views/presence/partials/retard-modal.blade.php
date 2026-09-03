{{--
    Motif de retard, demandé en modale plutôt qu'en pleine page : la page de
    pointage reste lisible, et le motif n'apparaît qu'au moment où il est requis.
    Le champ vit dans le formulaire parent, sa valeur part donc avec le pointage.
--}}
<div x-show="modalRetard" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
     style="background: rgba(15, 18, 22, .5)"
     @click.self="modalRetard = false"
     @keydown.escape.window="modalRetard = false">

    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 shadow-xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-6">
            <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">
                Vous arrivez après {{ $heurePrevue }}
            </h3>
            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                Expliquez brièvement ce qui s'est passé pour enregistrer votre arrivée.
            </p>

            <textarea name="observation_message" x-model="motif" rows="3" maxlength="500"
                @keydown.enter.meta="validerMotif()"
                placeholder="Ex. : embouteillage sur la voie de Godomey."
                class="mt-4 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-white
                       focus:border-red-500 focus:ring-2 focus:ring-red-500/40 shadow-sm text-sm"></textarea>

            <p class="mt-1.5 text-xs" :class="motifTropCourt ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'"
               x-text="motifTropCourt
                    ? 'Encore un peu : dix caractères minimum.'
                    : 'Dix caractères minimum. Il sera lu par votre responsable.'"></p>

            <div class="mt-5 flex flex-col gap-2">
                <button type="button" @click="validerMotif()"
                    class="w-full px-4 py-2.5 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium text-sm hover:opacity-90 transition">
                    Enregistrer mon arrivée
                </button>
                <button type="button" @click="modalRetard = false"
                    class="w-full px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-300 font-medium text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
