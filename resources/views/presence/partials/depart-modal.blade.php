{{--
    Départ demandé avant l'heure. Le serveur refuse de toute façon ; cette
    modale évite d'y arriver, et surtout donne la sortie : demander une
    permission plutôt que de rester bloqué devant un message.
--}}
<div x-show="modalDepart" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
     style="background: rgba(15, 18, 22, .5)"
     @click.self="modalDepart = false"
     @keydown.escape.window="modalDepart = false">

    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 shadow-xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-6">
            <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Il n'est pas encore l'heure
            </h3>
            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                Votre départ est prévu à <strong class="text-gray-700 dark:text-gray-200">{{ $heureDepart }}</strong>.
                Pour partir avant, il faut une permission de départ anticipé approuvée
                pour aujourd'hui ({{ now()->format('d/m/Y') }}).
            </p>

            <div class="mt-5 flex flex-col gap-2">
                <a href="{{ route('permissions.index') }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                          bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium text-sm hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M9 12h6m-3-3v6" stroke-linecap="round"/><circle cx="12" cy="12" r="9"/>
                    </svg>
                    Demander une permission
                </a>
                <button type="button" @click="modalDepart = false"
                    class="w-full px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-300 font-medium text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
