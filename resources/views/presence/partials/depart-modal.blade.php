{{--
    Départ demandé avant l'heure. Le serveur refuse de toute façon ; cette
    modale évite d'y arriver, et surtout donne la sortie : demander une
    permission plutôt que de rester bloqué devant un message.
--}}
<div x-show="modalDepart" x-cloak
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-4"
     style="background: rgba(15, 18, 22, .6); backdrop-filter: blur(2px);"
     @click.self="modalDepart = false"
     @keydown.escape.window="modalDepart = false">

    <div class="w-full max-w-sm rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <div class="p-4 sm:p-5">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/50 flex items-center justify-center flex-shrink-0 text-amber-600 dark:text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-[#241f1b] dark:text-white leading-tight">
                        Il n'est pas encore l'heure
                    </h3>
                    <p class="mt-1 text-xs text-[#6e6862] dark:text-slate-400 leading-relaxed">
                        Votre départ est prévu à <strong class="font-semibold text-[#241f1b] dark:text-slate-200">{{ $heureDepart }}</strong>.
                        Pour partir plus tôt, vous devez disposer d'une permission de départ anticipé validée pour aujourd'hui ({{ now()->format('d/m/Y') }}).
                    </p>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-2">
                <a href="{{ route('permissions.index') }}"
                   class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-md bg-[#1c3e96] hover:bg-[#162f73] text-white font-semibold text-xs shadow-2xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6m-3-3v6" stroke-linecap="round"/><circle cx="12" cy="12" r="9"/>
                    </svg>
                    <span>Demander une permission</span>
                </a>
                <button type="button" @click="modalDepart = false"
                    class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded-md bg-white dark:bg-[#15181f] border border-[#bfbab2] dark:border-[#2b3240] text-[#241f1b] dark:text-slate-300 hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] font-medium text-xs transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
