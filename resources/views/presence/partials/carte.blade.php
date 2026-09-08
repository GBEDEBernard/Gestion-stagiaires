{{--
    pointage.blade.php — contenu de la page, pas de layout.
    La sidebar et le header ("Présence - Pointage") viennent du layout
    parent : ce fichier ne doit rendre QUE le contenu de la zone principale.

    Attendus :
      $lieu, $prenom, $day, $expIn, $expOut, $etat, $late, $departBloque,
      $action, $champs, $isWorkDay, $workDaysLabel, $historiqueUrl,
      $journeeOubliee, $declarationUrl, $arriveeBloquee, $rapportSoumis
--}}
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
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6">

    {{-- Contexte: date + lieu --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-base sm:text-xl font-semibold text-slate-700 dark:text-slate-200 capitalize">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 dark:bg-emerald-900/20 ring-1 ring-emerald-200 dark:ring-emerald-700/40 text-sm sm:text-base font-bold text-emerald-700 dark:text-emerald-300">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            {{ $lieu }}
        </span>
    </div>

    {{-- Alerte compacte (ne montre que le bloc d'arrivée bloquée) --}}
    @if($arriveeBloque)
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-red-200 dark:border-red-700/40 bg-red-50 dark:bg-red-900/15 px-4 py-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-500 text-white flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-red-800 dark:text-red-300">Pointage d'arrivée bloqué</p>
                <p class="text-sm text-red-700/90 dark:text-red-400/90">Un départ oublié doit être réglé avec votre responsable.</p>
            </div>
        </div>
    @endif

    {{-- Carte principale : layout split (image + contenu) --}}
    <div class="overflow-hidden rounded-2xl shadow-lg bg-gradient-to-b {{ $heroTint }} ring-1 ring-slate-200 dark:ring-slate-800">
        <div class="flex flex-col lg:flex-row">
          {{-- Image à droite sur grand écran, en haut sur mobile --}}
<div class="lg:w-1/2 w-full order-1 lg:order-2">
    <div class="relative w-full aspect-[16/10] sm:aspect-[21/9] lg:aspect-auto lg:h-full overflow-hidden">
        <img
            src="/images/imagepointage.jpeg"
            alt=""
            aria-hidden="true"
            loading="lazy"
            class="absolute inset-0 w-full h-full object-cover object-[center_25%]"
        >
    </div>
</div>

            {{-- Contenu principal --}}
            <div class="lg:w-1/2 w-full p-6 sm:p-8 order-2 lg:order-1 flex flex-col justify-between">
                <div>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-600 dark:text-emerald-300 text-center">{{ now()->hour < 18 ? 'Bonjour' : 'Bonsoir' }}, <span class="font-extrabold text-violet-700 dark:text-violet-300">{{ $prenom }}</span></p>
                    <p class="mt-3 text-4xl sm:text-5xl font-extralight tracking-tight tabular-nums leading-none text-center bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 dark:from-emerald-400 dark:via-teal-300 dark:to-emerald-400"
                       x-data="{ h: '' }" x-init="h = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'}); setInterval(() => h = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'}), 10000)"
                       x-text="h">--:--</p>

                    {{-- Timeline compacte --}}
                    <div class="mt-6 grid grid-cols-2 gap-4 items-center">
                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase text-slate-400">Arrivée</p>
                            <p class="mt-1 text-lg font-semibold tabular-nums {{ !$aPointeArrivee ? 'text-slate-300 dark:text-slate-600' : ($arriveeEnRetard ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400') }}">{{ $day?->first_check_in_at?->format('H:i') ?? '--:--' }}</p>
                            <p class="mt-1 text-xs text-slate-400">@if($arriveeEnRetard && ($day->late_minutes ?? 0) > 0) {{ $day->late_minutes }} min de retard @else prévue {{ $expIn?->format('H:i') ?? '--:--' }} @endif</p>
                        </div>

                        <div class="text-center">
                            <p class="text-xs font-semibold uppercase text-slate-400">Départ</p>
                            <p class="mt-1 text-lg font-semibold tabular-nums {{ !$aPointeDepart ? 'text-slate-300 dark:text-slate-600' : ($departAnticipe ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400') }}">{{ $day?->last_check_out_at?->format('H:i') ?? '--:--' }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $departAnticipe ? 'départ anticipé' : 'prévu ' . ($expOut?->format('H:i') ?? '--:--') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Action area --}}
                <div class="mt-6">
                    @if(!$isWorkDay)
                        <p class="text-sm text-slate-600 dark:text-slate-300">Aujourd'hui n'est pas un jour de présence. <span class="block text-slate-400">Jours prévus : {{ $workDaysLabel ?? '—' }}</span></p>

                    @elseif($etat === 'termine')
                        <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-emerald-100/70 dark:bg-emerald-900/25 text-emerald-700 dark:text-emerald-300 font-medium text-sm">Journée complète — à demain</div>

                    @elseif($arriveeBloque || $departBloqueRapport)
                        <button type="button" disabled class="w-full px-4 py-3 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400">Complétez d'abord l'action requise ci-dessus</button>

                    @else
                        <form method="POST" action="{{ $action }}" x-data="pointageForm({{ $late ? 'true' : 'false' }}, {{ $departBloque ? 'true' : 'false' }})" @submit.prevent="submit($el)">
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

                            <button type="submit" x-bind:disabled="busy" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-semibold text-white text-base transition {{ $etat === 'arrivee' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                                <svg x-show="busy" x-cloak class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                <span x-show="!busy">{{ $etat === 'arrivee' ? "Pointer mon arrivée" : "Pointer mon départ" }}</span>
                                <span x-show="busy" x-cloak x-text="etape"></span>
                            </button>

                            <p x-show="erreur" x-cloak class="mt-3 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm text-red-800 dark:text-red-300" x-text="erreur"></p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modaux et historique --}}
    @if(($journeeOubliee ?? null) && ($declarationUrl ?? null))
        @include('presence.partials.oubli-depart-modal', [
            'journee'         => $journeeOubliee,
            'declarationUrl'  => $declarationUrl,
        ])
    @endif

    @if($historiqueUrl)
        <div class="mt-6 text-center">
            <a href="{{ $historiqueUrl }}" class="font-sans inline-flex items-center gap-2.5 px-6 py-3 rounded-xl text-base font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 ring-1 ring-slate-200 dark:ring-slate-700 shadow-sm hover:text-violet-700 dark:hover:text-violet-300 transition">Mon historiques</a>
        </div>
    @endif
</div>