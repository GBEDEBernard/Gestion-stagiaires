{{--
    Motif de retard, demandé en modale plutôt qu'en pleine page : la page de
    pointage reste lisible, et le motif n'apparaît qu'au moment où il est requis.
    Le champ vit dans le formulaire parent, sa valeur part donc avec le pointage.
--}}
<div x-show="modalRetard" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-4"
     style="background: rgba(15, 18, 22, .6); backdrop-filter: blur(2px);"
     @click.self="modalRetard = false"
     @keydown.escape.window="modalRetard = false">

    <div class="w-full max-w-sm rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-4 sm:p-5">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200/60 dark:border-red-900/50 flex items-center justify-center flex-shrink-0 text-red-600 dark:text-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-red-700 dark:text-red-400 leading-tight">
                        Vous arrivez après {{ $heurePrevue }}
                    </h3>
                    <p class="mt-0.5 text-xs text-[#6e6862] dark:text-slate-400 leading-snug">
                        Expliquez brièvement la situation pour enregistrer votre arrivée.
                    </p>
                </div>
            </div>

            <textarea name="observation_message" x-model="motif" rows="3" maxlength="500"
                @keydown.enter.meta="validerMotif()"
                placeholder="Ex. : embouteillage sur la voie de Godomey."
                class="w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] text-[#241f1b] dark:text-white
                       focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96] shadow-2xs text-xs px-2.5 py-2 leading-relaxed transition"></textarea>

            <p class="mt-1.5 text-[11px]" :class="motifTropCourt ? 'text-red-600 dark:text-red-400 font-medium' : 'text-[#6e6862] dark:text-slate-400'"
               x-text="motifTropCourt
                    ? 'Encore un peu : dix caractères minimum.'
                    : 'Dix caractères minimum. Il sera lu par votre responsable.'"></p>

            <div class="mt-4 flex flex-col gap-2">
                <button type="button" @click="validerMotif()"
                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-md bg-[#1c3e96] hover:bg-[#162f73] text-white font-semibold text-xs transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Enregistrer mon arrivée</span>
                </button>
                <button type="button" @click="modalRetard = false"
                    class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded-md bg-white dark:bg-[#15181f] border border-[#bfbab2] dark:border-[#2b3240] text-[#241f1b] dark:text-slate-300 hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] font-medium text-xs transition">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
