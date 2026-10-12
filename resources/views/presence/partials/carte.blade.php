{{--
    pointage.blade.php — contenu de la page, pas de layout.
    La sidebar et le header ("Présence - Pointage") viennent du layout
    parent : ce fichier ne doit rendre QUE le contenu de la zone principale.

    Attendus :
      $lieu, $prenom, $day, $expIn, $expOut, $etat, $late, $departBloque,
      $action, $champs, $isWorkDay, $workDaysLabel, $historiqueUrl,
      $journeeOubliee, $declarationUrl, $arriveeBloquee, $rapportSoumis
--}}
@include('presence.partials.missing-departure-banner', ['missingDeparture' => ($journeeOubliee ?? null) && ($declarationUrl ?? null), 'missingDepartureDay' => $journeeOubliee])
@include('presence.partials.pointage-script')

@php
    $arriveeEnRetard = $day?->arrival_status === 'late';
    $aPointeArrivee  = (bool) $day?->first_check_in_at;
    $aPointeDepart   = (bool) $day?->last_check_out_at;
    $departAnticipe  = (($day?->early_departure_minutes ?? 0) > 0);

    // Une seule source de vérité pour la teinte du hero, dérivée de l'état
    // le plus avancé de la journée (départ > arrivée > rien).
    $statutJournee = $aPointeDepart
        ? ($departAnticipe ? 'attention' : 'ok')
        : ($aPointeArrivee ? ($arriveeEnRetard ? 'attention' : 'ok') : 'neutre');

    $heroTint = match($statutJournee) {
        'ok'        => 'from-emerald-50 via-white to-white dark:from-emerald-900/10 dark:via-slate-900 dark:to-slate-900',
        'attention' => 'from-amber-50 via-white to-white dark:from-amber-900/10 dark:via-slate-900 dark:to-slate-900',
        default     => 'from-slate-50 via-white to-white dark:from-slate-800/40 dark:via-slate-900 dark:to-slate-900',
    };

    $arriveeBloque       = ($arriveeBloquee ?? false) && $etat === 'arrivee';
    $departBloqueRapport = $etat === 'depart' && !($rapportSoumis ?? true);

    // Un constat, une seule porte de sortie. Les messages sont construits ici
    // et non dans le balisage : écrits à plat, ils s'empilent et l'écran finit
    // par annoncer une action qui n'y est pas.
    //
    // Le départ pas encore ouvert reste un cas à part : il n'empêche pas de
    // pointer, il empêche de partir. Le formulaire reste donc atteignable, et
    // le modal ne fait qu'expliquer avant que la personne ne tente le geste.
    $verif = null;

    if (! $isWorkDay) {
        $verif = [
            'titre'   => "Aujourd'hui n'est pas un jour de présence",
            'message' => 'Jours prévus : <strong>' . ($workDaysLabel ?? '—') . '</strong>. Aucun pointage n\'est attendu aujourd\'hui.',
            'ton'     => 'info',
            'icone'   => 'calendrier',
            'bouton'  => 'Voir les jours prévus',
        ];
    } elseif ($arriveeBloque) {
        $verif = [
            'titre'   => 'Arrivée bloquée',
            'message' => 'Un départ oublié n\'a pas été déclaré dans les délais. Votre pointage est bloqué jusqu\'à ce qu\'un administrateur rétablisse votre heure de sortie : présentez-vous à votre responsable.',
            'ton'     => 'danger',
            'icone'   => 'interdit',
            'bouton'  => 'Voir pourquoi',
        ];
    } elseif ($departBloqueRapport) {
        $verif = [
            'titre'       => 'Rapport de travail du jour',
            'message'     => "Votre départ n'est enregistré qu'une fois votre rapport de travail déposé.",
            'ton'         => 'warning',
            'icone'       => 'document',
            'actionUrl'   => route('reports.index', ['nouveau' => 1]),
            'actionLabel' => 'Déposer mon rapport',
            'bouton'      => 'Déposer mon rapport',
        ];
    } elseif ($departBloque) {
        $verif = [
            'titre'       => 'Départ pas encore ouvert',
            'message'     => ($aPointeArrivee ? 'Arrivée enregistrée à <strong>' . $day->first_check_in_at->format('H:i') . '</strong>. ' : '')
                            . 'Le départ sera possible à partir de <strong>' . ($expOut?->format('H:i') ?? '—') . '</strong>. Pour partir avant, il faut une permission approuvée.',
            'ton'         => 'info',
            'icone'       => 'horloge',
            'actionUrl'   => route('permissions.index'),
            'actionLabel' => 'Demander une permission',
            'bouton'      => 'Demander une permission',
        ];
    }

    // Le modal ne sert qu'à la porte. Dans l'application, la page porte
    // déjà des alertes plus détaillées — elles nomment la journée concernée —
    // et le modal ne ferait que les répéter en plus court.
    //
    // Une exception : le rapport de travail manquant. Là, le bandeau se limite
    // à nommer le blocage, et c'est le clic sur « Pointer mon départ » qui doit
    // livrer l'explication complète et le raccourci vers le dépôt. Sans ce
    // modal, le bouton serait nécessairement inerte, puisque le départ est
    // refusé et qu'aucun formulaire n'a de quoi être soumis.
    $verifModal = ($autoPointage ?? false) || $departBloqueRapport ? $verif : null;

    // Pointage automatique : le scan du QR code de porte suffit, le bouton
    // disparaît. Il revient si la localisation a échoué, pour réessayer sans
    // rescanner.
    //
    // Un retard n'arrête pas l'automatisme : le script ouvre la modale de
    // justification au scan, et n'envoie le pointage qu'une fois le motif
    // écrit. L'arrivée est donc enregistrée sans clic, avec la raison
    // obligatoire. Seul le départ anticipé reste une décision humaine à part
    // entière : il demande une permission, et le bouton demeure.
    $autoActif = ($autoPointage ?? false) && !$departBloque;

    // Condition d'affichage du bouton, en syntaxe Alpine. Écrite ici plutôt
    // qu'inline dans l'attribut : une expression contenant des apostrophes
    // ne survit pas au passage par l'échappement de Blade.
    $expressionBouton = $autoActif ? "erreur !== ''" : 'true';
@endphp

<div class="w-full">

    {{-- Contexte : le lieu d'abord, la date en légende.

         Un nom de domaine s'écrit en toutes lettres — « Direction Technique
         Chargé de l'Ingénierie des Systèmes d'information » fait soixante-neuf
         caractères. Dans une rangée `justify-between`, les deux éléments se
         partageaient la largeur sans pouvoir céder : le nom gonflait la
         pastille en capsule de six lignes et la date se retrouvait tassée sur
         deux ou trois lignes, même pour un nom court. Le lieu prend donc sa
         propre ligne, bornée à deux lignes, et la date redescend dessous :
         l'endroit, puis le moment. `break-words` couvre un libellé d'un seul
         tenant, `title` garde le nom complet pour qui le veut en entier. --}}
    <div class="mb-3 sm:mb-4 flex items-center justify-between gap-2.5">
        <p class="text-[11px] sm:text-xs font-medium capitalize text-[#6e6862] dark:text-[#a89f93]">
            {{ now()->isoFormat('dddd D MMMM YYYY') }}
        </p>

        <span class="inline-flex max-w-[55%] sm:max-w-none items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#dcfce7] dark:bg-[rgba(22,128,74,0.22)] border border-[#15803d]/30 text-[#15803d] dark:text-[#4ade80] shrink-0"
              title="{{ $lieu }}">
            <span class="w-1.5 h-1.5 flex-shrink-0 rounded-full bg-[#15803d] animate-pulse"></span>
            <span class="min-w-0 text-xs sm:text-sm font-semibold leading-tight truncate">{{ $lieu }}</span>
        </span>
    </div>

    <style>
        .carte-pointage-radial {
            background: radial-gradient(ellipse at 85% 20%, rgba(220, 252, 231, 0.7) 0%, rgba(240, 253, 244, 0.35) 45%, #fffefa 75%) !important;
            border-color: #d6ded8 !important;
        }
        .dark .carte-pointage-radial {
            background: radial-gradient(ellipse at 85% 20%, rgba(16, 185, 129, 0.14) 0%, rgba(16, 185, 129, 0.04) 45%, #131519 75%) !important;
            border-color: #202923 !important;
        }
    </style>

    {{-- Carte principale : style 'Mon Stage' avec halo émeraude pastel subtil --}}
    <div class="carte-pointage-radial overflow-hidden rounded-[10px] border shadow-[0_1px_2px_rgba(36,31,27,0.05),0_8px_24px_-16px_rgba(36,31,27,0.12)] dark:shadow-none md:min-h-[460px] lg:min-h-[490px]">
        <div class="flex flex-col md:flex-row md:min-h-[460px] lg:min-h-[490px]">
          {{-- Image à droite sur tablette et PC, en haut sur mobile --}}
          <div class="md:w-1/2 w-full order-1 md:order-2 flex items-center justify-center">
              <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] md:aspect-auto md:h-full min-h-0 md:min-h-[460px] lg:min-h-[490px] p-5 sm:p-7 md:p-8 flex items-center justify-center">
                  <img
                      src="/images/imagepointage1.png"
                      alt=""
                      aria-hidden="true"
                      loading="lazy"
                      class="max-w-full max-h-[300px] sm:max-h-[340px] md:max-h-[410px] w-auto h-auto object-contain object-center select-none pointer-events-none"
                  >
              </div>
          </div>

            {{-- Contenu principal : centré verticalement en face de l'illustration --}}
            <div class="md:w-1/2 w-full p-6 sm:p-8 lg:p-10 order-2 md:order-1 flex flex-col justify-center md:min-h-[460px] lg:min-h-[490px]">
                <div>
                    <p class="text-2xl sm:text-3xl font-bold text-[#241f1b] dark:text-[#f8fafc] text-center">{{ now()->hour < 18 ? 'Bonjour' : 'Bonsoir' }}, <span class="font-extrabold text-[#7e22ce] dark:text-[#c084fc]">{{ $prenom }}</span></p>
                    <p class="mt-4 sm:mt-5 text-4xl sm:text-5xl lg:text-6xl font-light tracking-tight tabular-nums leading-none text-center bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 dark:from-emerald-400 dark:via-teal-300 dark:to-emerald-400"
                       x-data="{
                           time: '',
                           sec: '',
                           tick() {
                               const d = new Date();
                               this.time = d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                               this.sec = String(d.getSeconds()).padStart(2, '0');
                           }
                       }"
                       x-init="tick(); setInterval(() => tick(), 1000)">
                        <span x-text="time">--:--</span><span class="text-2xl sm:text-3xl lg:text-4xl font-thin opacity-70 ml-1" x-text="':' + sec">:00</span>
                    </p>

                    {{-- Timeline compacte --}}
                    <div class="mt-4 sm:mt-8 grid grid-cols-2 gap-4 items-center">
                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Arrivée</p>
                            <p class="mt-1 text-lg font-semibold tabular-nums {{ !$aPointeArrivee ? 'text-[#bfbab2] dark:text-slate-600' : ($arriveeEnRetard ? 'text-[#b45309] dark:text-amber-400' : 'text-[#15803d] dark:text-emerald-400') }}">{{ $day?->first_check_in_at?->format('H:i') ?? '--:--' }}</p>
                            <p class="mt-1 text-xs text-[#6e6862] dark:text-[#a89f93]">@if($arriveeEnRetard && ($day->late_minutes ?? 0) > 0) {{ $day->late_minutes }} min de retard @else prévue {{ $expIn?->format('H:i') ?? '--:--' }} @endif</p>
                        </div>

                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6e6862] dark:text-[#a89f93]">Départ</p>
                            <p class="mt-1 text-lg font-semibold tabular-nums {{ !$aPointeDepart ? 'text-[#bfbab2] dark:text-slate-600' : ($departAnticipe ? 'text-[#b45309] dark:text-amber-400' : 'text-[#15803d] dark:text-emerald-400') }}">{{ $day?->last_check_out_at?->format('H:i') ?? '--:--' }}</p>
                            <p class="mt-1 text-xs text-[#6e6862] dark:text-[#a89f93]">{{ $departAnticipe ? 'départ anticipé' : 'prévu ' . ($expOut?->format('H:i') ?? '--:--') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Action area --}}
                <div class="mt-4 sm:mt-8">
                    @if($verifModal && ! ($etat === 'depart' && ! $departBloqueRapport))
                        {{-- À la porte : un seul bouton, il rappelle le constat
                             au modal. Rien n'est armé, donc rien ne part.

                             Seul le départ encore fermé garde le formulaire : il
                             ne l'empêche pas de pointer, il empêche de partir,
                             et c'est ce formulaire qui permet de demander la
                             permission. Le rapport manquant prime sur tout le
                             reste, même avant l'heure de départ. --}}
                        <button type="button" @click="$dispatch('verif-ouvrir')"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-3 rounded-lg font-semibold text-white text-sm sm:text-base transition {{ $verifModal['ton'] === 'danger' ? 'bg-red-600 hover:bg-red-700' : ($verifModal['ton'] === 'warning' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-slate-600 hover:bg-slate-700') }}">
                            {{ $verifModal['bouton'] }}
                        </button>

                    @elseif(! $isWorkDay)
                        <div class="px-3 py-2 sm:px-4 sm:py-3 rounded-[10px] bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-center">
                            <p class="text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc] leading-tight">Aujourd'hui n'est pas un jour de présence</p>
                            <p class="text-[11px] sm:text-xs text-[#6e6862] dark:text-[#a89f93] mt-0.5 leading-tight">Jours prévus : <span class="font-medium text-[#241f1b] dark:text-[#f8fafc]">{{ $workDaysLabel ?? '—' }}</span></p>
                        </div>

                    @elseif($departBloqueRapport)
                        {{-- Rapport manquant : le bouton reste cliquable et
                             ouvre le constat. Un bouton « disabled » expliquait
                             le blocage dans la zone grise sans jamais permettre
                             d'y répondre ; ici la personne comprend pourquoi le
                             départ est refusé et part directement déposer le
                             rapport, qui est l'action attendue. --}}
                        <button type="button" @click="$dispatch('verif-ouvrir')"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-[8px] font-semibold text-white text-base transition bg-[#b45309] hover:bg-[#92400e]">
                            Pointer mon départ
                        </button>

                    @elseif($arriveeBloque)
                        {{-- Départ oublié à régulariser : rien à faire depuis
                             cette page, l'heure doit être rétablie par un
                             responsable. Le bouton reste donc inerte, et le
                             bandeau plus haut explique pourquoi. --}}
                        <button type="button" disabled class="w-full px-4 py-3 rounded-[8px] bg-[#ecebe3] dark:bg-[#262a30] text-[#6e6862] dark:text-[#a89f93]">Un départ oublié doit être réglé d'abord</button>

                    @elseif($etat === 'termine')
                        <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-[8px] bg-[#dcfce7] dark:bg-[rgba(22,128,74,0.22)] border border-[#15803d]/30 text-[#15803d] dark:text-[#4ade80] font-semibold text-sm">Journée complète — à demain</div>

                    @else
                        <form method="POST" action="{{ $action }}"
                              data-auto-pointage="{{ $autoActif ? '1' : '0' }}"
                              x-data="pointageForm({{ $late ? 'true' : 'false' }}, {{ $departBloque ? 'true' : 'false' }}, {{ $autoActif ? 'true' : 'false' }})"
                              @submit.prevent="submit($el)">
                            @csrf
                            @foreach($champs as $nom => $valeur)
                                <input type="hidden" name="{{ $nom }}" value="{{ $valeur }}">
                            @endforeach
                            <input type="hidden" name="latitude" x-ref="lat">
                            <input type="hidden" name="longitude" x-ref="lng">
                            <input type="hidden" name="accuracy_meters" x-ref="acc">
                            <input type="hidden" name="device_fingerprint" x-ref="fp">
                            <input type="hidden" name="device_uuid" x-ref="uuid">
                            <input type="hidden" name="device_label" x-ref="label">
                            <input type="hidden" name="platform" x-ref="platform">
                            <input type="hidden" name="browser" x-ref="browser">

                            @if($late)
                                @include('presence.partials.retard-modal', ['heurePrevue' => $expIn?->format('H:i')])
                            @endif
                            @if($departBloque)
                                @include('presence.partials.depart-modal', ['heureDepart' => $expOut?->format('H:i')])
                            @endif

                            {{-- En pointage automatique, on dit ce qui se joue pendant
                                 que le téléphone se positionne. Le bouton ne revient
                                 qu'en cas d'échec, pour réessayer sans rescanner. --}}
                            @if($autoActif)
                                <div class="w-full flex items-center justify-center gap-2.5 px-4 py-3 rounded-[8px] bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] text-[#241f1b] dark:text-[#f8fafc] font-medium text-sm" x-cloak x-show="erreur === ''">
                                    <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" stroke="currentColor" stroke-width="4" cx="12" cy="12" r="10"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                    <span x-text="etape || 'Vérification de votre présence…'">Vérification de votre présence…</span>
                                </div>
                            @endif

                            <button type="submit" x-cloak x-show="{!! $expressionBouton !!}" x-bind:disabled="busy" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-[8px] font-semibold text-white text-base transition shadow-sm {{ $etat === 'arrivee' ? 'bg-[#15803d] hover:bg-[#14532d]' : 'bg-[#1c3e96] hover:bg-[#162f73]' }}">
                                <svg x-show="busy" x-cloak class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" stroke="currentColor" stroke-width="4" cx="12" cy="12" r="10"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                <span x-show="!busy">{{ $autoActif ? 'Réessayer' : ($etat === 'arrivee' ? "Pointer mon arrivée" : "Pointer mon départ") }}</span>
                                <span x-show="busy" x-cloak x-text="etape"></span>
                            </button>

                            {{-- Un refus de localisation se corrige sur place : le GPS
                                 se relance sans rescanner. On dit donc ce qui bloque,
                                 ce qu'il reste à faire, et le bouton « Réessayer »
                                 reste juste au-dessus. --}}
                            <div x-show="erreur" x-cloak class="mt-3 px-4 py-3 rounded-[10px] bg-[#fef3c7] dark:bg-[rgba(180,83,9,0.22)] border border-[#b45309]/30">
                                <p class="flex items-start gap-2 text-sm font-semibold text-[#b45309] dark:text-[#fbbf24]">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 4.3L2.6 17.6A2 2 0 004.3 20.6h15.4a2 2 0 001.7-3L13.7 4.3a2 2 0 00-3.4 0z"/></svg>
                                    <span x-text="erreur"></span>
                                </p>
                                <p class="mt-2 pl-6 text-sm text-[#b45309]/90 dark:text-[#fbbf24]/90">
                                    Sortez quelques secondes vers l'extérieur ou approchez-vous d'une baie vitrée, puis relancez la localisation. Rescanner n'est pas nécessaire.
                                </p>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modaux et historique --}}
    @if($verifModal)
        {{-- x-data local : le modal s'ouvre au chargement et se referme au
             clic sur le voile. Le bouton de la carte le rappelle grâce à
             l'événement, les deux étant trop éloignés pour partager un état. --}}
        <div x-data="{ verif: @js(!($autoPointage ?? false)) }"
             @verif-ouvrir.window="verif = true">
            @include('presence.partials.verif-modal', [
                'titre'       => $verifModal['titre'],
                'message'     => $verifModal['message'],
                'ton'         => $verifModal['ton'],
                'icone'       => $verifModal['icone'],
                'actionUrl'   => $verifModal['actionUrl'] ?? null,
                'actionLabel' => $verifModal['actionLabel'] ?? null,
                'bouton'      => $verifModal['bouton'],
            ])
        </div>
    @endif

    @if(($journeeOubliee ?? null) && ($declarationUrl ?? null))
        @include('presence.partials.oubli-depart-modal', [
            'journee'         => $journeeOubliee,
            'declarationUrl'  => $declarationUrl,
        ])
    @endif

    @if($historiqueUrl)
        <div class="mt-4 sm:mt-6 text-center">
            <a href="{{ $historiqueUrl }}" class="font-sans inline-flex items-center gap-2 px-4 py-2 sm:px-6 sm:py-2.5 rounded-[10px] text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] shadow-sm hover:border-[#1c3e96] hover:text-[#1c3e96] transition">Mon historique</a>
        </div>
    @endif
</div>