@php
    $day        = $attendanceDay ?? null;
    $checkedIn  = $hasCheckedIn ?? false;
    $checkedOut = $hasCheckedOut ?? false;
    $expIn      = $expectedIn  ?? null;
    $expOut     = $expectedOut ?? null;
    $late       = ($isLateNow ?? false) && !$checkedIn;

    $etat = !$checkedIn ? 'arrivee' : (!$checkedOut ? 'depart' : 'termine');
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

        @if($todayHoliday ?? null)
            <div class="mb-5 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 text-sm text-amber-800 dark:text-amber-300">
                {{ $todayHoliday->label }} — jour férié.
                @if($canBypassHoliday || $isEmergencyExempted) Le pointage vous reste ouvert. @endif
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">

            <div class="px-6 pt-6 pb-5 text-center border-b border-gray-100 dark:border-gray-700">
                <p class="text-xs uppercase tracking-wide text-gray-400">{{ $user->domaine?->nom ?? 'Poste' }}</p>
                <p class="mt-3 text-5xl font-semibold tabular-nums text-gray-900 dark:text-white"
                   x-data="{ h: '' }" x-init="h = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
                                              setInterval(() => h = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'}), 10000)"
                   x-text="h">--:--</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ now()->isoFormat('dddd Do MMMM') }}</p>
            </div>

            <div class="grid grid-cols-2 divide-x divide-gray-100 dark:divide-gray-700 border-b border-gray-100 dark:border-gray-700">
                <div class="px-6 py-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Arrivée</p>
                    <p class="text-3xl font-semibold tabular-nums {{ $checkedIn ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-300 dark:text-gray-600' }}">
                        {{ $day?->first_check_in_at?->format('H:i') ?? '--:--' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">prévue {{ $expIn?->format('H:i') ?? '--:--' }}</p>
                </div>

                <div class="px-6 py-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-gray-400 mb-2">Départ</p>
                    <p class="text-3xl font-semibold tabular-nums {{ $checkedOut ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-300 dark:text-gray-600' }}">
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
                          x-data="pointageForm()" @submit.prevent="submit($el)">
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
                            <div class="mb-5">
                                <label for="observation_message" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                                    Vous arrivez après {{ $expIn?->format('H:i') }} — motif du retard
                                </label>
                                <textarea id="observation_message" name="observation_message" rows="3" required minlength="10" maxlength="500"
                                    placeholder="Ex. : embouteillage sur la voie de Godomey."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm text-sm"></textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dix caractères minimum. Il sera lu par votre responsable.</p>
                            </div>
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
