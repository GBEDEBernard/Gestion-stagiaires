{{--
    Départ oublié la veille. La modale n'apparaît qu'une fois l'arrivée du jour
    pointée : la question d'hier ne doit pas retarder le geste d'aujourd'hui.

    Ce qui est saisi ici n'est pas appliqué. La journée reste clôturée à
    l'heure de fin prévue tant que le responsable n'a pas tranché — sinon
    chacun déclarerait 20h.

    Passé l'avertissement, la même modale vire au rouge et annonce la
    suspension du lendemain : une seconde fenêtre par-dessus la première
    aurait caché le formulaire qui permet justement de s'en sortir.

    Attendus : $journee (AttendanceDay), $declarationUrl
--}}
@php
    $avertie = (bool) $journee->suspension_warned_at;
@endphp
<div x-data="{ ouvert: true, heure: '', motif: '', erreur: '' }"
     x-show="ouvert" x-cloak
     data-declaration-oubliee
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-3 sm:p-4"
     style="background: rgba(15, 18, 22, .6); backdrop-filter: blur(2px);"
     @keydown.escape.window="{{ $avertie ? '' : 'ouvert = false' }}">

    <div class="w-full max-w-sm rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        <form method="POST" action="{{ $declarationUrl }}" class="p-4 sm:p-5"
              @submit="if (heure === '' || motif.trim().length < 10) { $event.preventDefault(); erreur = heure === '' ? 'Indiquez votre heure de départ.' : 'Le motif doit faire au moins dix caractères.'; }">
            @csrf
            <input type="hidden" name="day_id" value="{{ $journee->id }}">

            <div class="flex items-start gap-3 mb-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 border
                            {{ $avertie
                                ? 'bg-red-50 dark:bg-red-950/40 border-red-200/60 dark:border-red-900/50 text-red-600 dark:text-red-400'
                                : 'bg-amber-50 dark:bg-amber-950/40 border-amber-200/60 dark:border-amber-900/50 text-amber-600 dark:text-amber-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        @if($avertie)
                            <path d="M12 9v4" stroke-linecap="round"/><path d="M12 17v.01" stroke-linecap="round"/>
                            <path d="M10.3 4.3L2.6 17.6A2 2 0 004.3 20.6h15.4a2 2 0 001.7-3L13.7 4.3a2 2 0 00-3.4 0z" stroke-linejoin="round"/>
                        @else
                            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/>
                        @endif
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    @if($avertie)
                        <h3 class="text-sm font-semibold text-red-700 dark:text-red-400 leading-tight">
                            Votre compte sera suspendu demain
                        </h3>
                    @else
                        <h3 class="text-sm font-semibold text-[#241f1b] dark:text-white leading-tight">
                            Départ non pointé hier
                        </h3>
                    @endif
                    <p class="mt-0.5 text-xs text-[#6e6862] dark:text-slate-400 leading-snug">
                        Journée du <strong class="font-semibold text-[#241f1b] dark:text-slate-200">{{ $journee->attendance_date->format('d/m/Y') }}</strong> clôturée à {{ $journee->last_check_out_at?->format('H:i') ?? '--:--' }}. Indiquez votre départ réel :
                    </p>
                </div>
            </div>

            <div class="space-y-2.5">
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400 mb-1">
                        Heure de départ
                    </label>
                    <input type="time" name="claimed_time" x-model="heure" required
                           class="w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] text-[#241f1b] dark:text-white
                                  focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96] text-xs px-2.5 py-1.5 tabular-nums transition">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-[#6e6862] dark:text-slate-400 mb-1">
                        Motif
                    </label>
                    <textarea name="claimed_reason" x-model="motif" rows="3" maxlength="500" required
                              placeholder="Ex. : téléphone déchargé, je suis parti à 18h30."
                              class="w-full rounded-md border border-[#dcdad2] dark:border-[#222630] bg-white dark:bg-[#15181f] text-[#241f1b] dark:text-white
                                     focus:border-[#1c3e96] focus:ring-1 focus:ring-[#1c3e96] text-xs px-2.5 py-2 leading-relaxed transition"></textarea>
                </div>
            </div>

            <p class="mt-2 text-[11px] {{ $avertie ? 'text-red-600 dark:text-red-400 font-medium' : 'text-[#6e6862] dark:text-slate-400' }}" x-show="!erreur">
                @if($avertie)
                    Sans déclaration, votre accès sera coupé demain. Seul un administrateur pourra le rouvrir en personne.
                @else
                    Votre déclaration sera transmise à votre responsable pour validation de l'horaire.
                @endif
            </p>
            <p class="mt-2 text-[11px] text-red-600 dark:text-red-400 font-medium" x-show="erreur" x-cloak x-text="erreur"></p>

            <div class="mt-4 flex flex-col gap-2">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-md bg-[#1c3e96] hover:bg-[#162f73] text-white font-semibold text-xs shadow-2xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Envoyer ma déclaration</span>
                </button>
                @unless($avertie)
                    <button type="button" @click="ouvert = false"
                        class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded-md bg-white dark:bg-[#15181f] border border-[#bfbab2] dark:border-[#2b3240] text-[#241f1b] dark:text-slate-300 hover:bg-[#f1f0ed] dark:hover:bg-[#1c212c] font-medium text-xs transition">
                        Plus tard
                    </button>
                @endunless
            </div>
        </form>
    </div>
</div>
