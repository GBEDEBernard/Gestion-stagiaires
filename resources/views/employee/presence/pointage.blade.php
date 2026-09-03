@php
    $day        = $attendanceDay ?? null;

    // Couleur portée par l'état réel, pas par le simple fait d'avoir pointé :
    // une arrivée en retard doit se voir.
    $arriveeEnRetard = $day?->arrival_status === 'late';
    $tonArrivee = !($hasCheckedIn ?? false)
        ? 'text-gray-300 dark:text-gray-600'
        : ($arriveeEnRetard ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400');
    $tonDepart  = ($hasCheckedOut ?? false)
        ? (($day?->early_departure_minutes ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400')
        : 'text-gray-300 dark:text-gray-600';
    $checkedIn  = $hasCheckedIn ?? false;
    $checkedOut = $hasCheckedOut ?? false;
    $expIn      = $expectedIn  ?? null;
    $expOut     = $expectedOut ?? null;
    $late       = ($isLateNow ?? false) && !$checkedIn;

    $etat = !$checkedIn ? 'arrivee' : (!$checkedOut ? 'depart' : 'termine');

    $prenom = $user?->personnel?->prenom ?: ($user?->prenom ?: $user?->name);
    $salut  = now()->hour < 18 ? 'Bonjour' : 'Bonsoir';
@endphp

<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">

        @foreach(['success' => 'emerald', 'info' => 'blue', 'error' => 'red'] as $cle => $ton)
            @if(session($cle))
                <div class="mb-5 px-4 py-3 rounded-xl border
                            bg-{{ $ton }}-50 dark:bg-{{ $ton }}-900/20
                            border-{{ $ton }}-200 dark:border-{{ $ton }}-800/50
                            text-sm text-{{ $ton }}-800 dark:text-{{ $ton }}-300">
                    {{ session($cle) }}
                </div>
            @endif
        @endforeach

        @if($errors->any())
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm text-red-800 dark:text-red-300">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Trois situations distinctes derrière un même jour férié : dire
             seulement « c'est férié » laisserait la personne sans savoir si
             elle peut pointer ou non. --}}
        @if($todayHoliday ?? null)
            @php
                $ouvert = ($canBypassHoliday ?? false) || ($isEmergencyExempted ?? false);
                $ton    = $ouvert ? 'amber' : 'violet';
            @endphp
            <div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-xl
                        bg-{{ $ton }}-50 dark:bg-{{ $ton }}-900/20
                        border border-{{ $ton }}-200 dark:border-{{ $ton }}-800/50">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-{{ $ton }}-600 dark:text-{{ $ton }}-400"
                     fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-{{ $ton }}-800 dark:text-{{ $ton }}-300">
                        Jour férié — {{ $todayHoliday->label }}
                    </p>
                    <p class="mt-0.5 text-sm text-{{ $ton }}-700/85 dark:text-{{ $ton }}-400/85">
                        @if($isEmergencyExempted ?? false)
                            Vous avez été appelé en urgence : vous pouvez pointer normalement.
                        @elseif($canBypassHoliday ?? false)
                            Vous disposez d'une autorisation de pointer aujourd'hui.
                        @else
                            Le pointage est désactivé. En cas d'urgence, votre responsable peut vous contacter.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">

            <div class="px-6 pt-6 pb-5 text-center border-b border-gray-100 dark:border-gray-700">
                {{-- Le lieu en pastille, la salutation en tête : on sait à qui
                     s'adresse l'écran avant de lire l'heure. --}}
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                             bg-gray-100 dark:bg-gray-700/60 text-xs font-medium text-gray-600 dark:text-gray-300">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ $user->domaine?->nom ?? 'Poste' }}
                </span>

                <p class="mt-3.5 text-xl text-gray-500 dark:text-gray-400">
                    {{ $salut }}, <span class="font-semibold text-gray-900 dark:text-white">{{ $prenom }}</span>
                </p>

                <p class="mt-4 text-5xl font-semibold tabular-nums text-gray-900 dark:text-white"
                   x-data="{ h: '' }" x-init="h = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
                                              setInterval(() => h = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'}), 10000)"
                   x-text="h">--:--</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ now()->isoFormat('dddd Do MMMM') }}</p>
            </div>

            <div class="grid grid-cols-2 divide-x divide-gray-100 dark:divide-gray-700 border-b border-gray-100 dark:border-gray-700">
                <div class="px-6 py-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Arrivée</p>
                    <p class="text-3xl font-semibold tabular-nums {{ $tonArrivee }}">
                        {{ $day?->first_check_in_at?->format('H:i') ?? '--:--' }}
                    </p>
                    @if($arriveeEnRetard && ($day->late_minutes ?? 0) > 0)
                        <p class="mt-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                            {{ $day->late_minutes }} min de retard
                        </p>
                    @else
                        <p class="mt-1 text-xs text-gray-400">prévue {{ $expIn?->format('H:i') ?? '--:--' }}</p>
                    @endif
                </div>

                <div class="px-6 py-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Départ</p>
                    <p class="text-3xl font-semibold tabular-nums {{ $tonDepart }}">
                        {{ $day?->last_check_out_at?->format('H:i') ?? '--:--' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">prévu {{ $expOut?->format('H:i') ?? '--:--' }}</p>
                </div>
            </div>

            <div class="p-6">
                @if($etat === 'termine')
                    <div class="text-center">
                        <p class="font-medium text-gray-900 dark:text-white">Journée complète</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Vos deux pointages sont enregistrés. À demain.</p>
                    </div>
                @else
                    <form method="POST"
                          action="{{ $etat === 'arrivee' ? route('presence.checkin') : route('presence.checkout') }}"
                          x-data="pointageForm({{ $late ? 'true' : 'false' }})" @submit.prevent="submit($el)">
                        @csrf
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

                        @if($etat === 'depart' && !($canCheckOutNow ?? true))
                            <div class="mb-5 px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300">
                                La journée n'est pas terminée. Votre départ est prévu à {{ $expOut?->format('H:i') }}.
                            </div>
                        @endif

                        <button type="submit" x-bind:disabled="busy"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-medium text-white
                                   {{ $etat === 'arrivee' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700' }}
                                   disabled:opacity-60 transition">
                            <span x-show="!busy">{{ $etat === 'arrivee' ? "Pointer mon arrivée" : "Pointer mon départ" }}</span>
                            <span x-show="busy" x-cloak x-text="etape"></span>
                        </button>

                        <p x-show="erreur" x-cloak class="mt-4 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm text-red-800 dark:text-red-300" x-text="erreur"></p>
                    </form>
                @endif
            </div>
        </div>

        <p class="mt-4 text-center text-xs text-gray-400">
            Votre position sert uniquement à confirmer votre présence sur le site.
        </p>
    </div>

    @include('presence.partials.badge-popup')
    @include('presence.partials.pointage-script')
</x-app-layout>
