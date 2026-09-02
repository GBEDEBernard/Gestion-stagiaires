@php
    /** @var \App\Models\Stage|null $stage */
    $stage = $stage ?? null;

    $defaults   = config('presence.default_schedule');
    $selected   = old('jours_id', $stage?->jours->pluck('id')->toArray() ?? []);
    $pivots     = $stage?->jours->keyBy('id') ?? collect();
    $oldPerDay  = old('jour_schedule', []);

    $stageStart = old('expected_check_in_time', $stage?->expected_check_in_time ? substr($stage->expected_check_in_time, 0, 5) : $defaults['start']);
    $stageEnd   = old('expected_check_out_time', $stage?->expected_check_out_time ? substr($stage->expected_check_out_time, 0, 5) : $defaults['end']);
    $stageBreak = old('break_minutes', $stage?->break_minutes ?? $defaults['break_minutes']);
@endphp

<div class="border-t border-gray-100 dark:border-gray-700 pt-6">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Horaires</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 max-w-3xl">
        L'heure d'arrivée détermine le retard : une minute après, le pointage est compté en retard.
        La pause est déduite du temps de présence pour obtenir le temps de travail.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Arrivée attendue</label>
            <input type="time" name="expected_check_in_time" value="{{ $stageStart }}" required
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
            @error('expected_check_in_time')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Départ attendu</label>
            <input type="time" name="expected_check_out_time" value="{{ $stageEnd }}" required
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
            @error('expected_check_out_time')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Pause <span class="font-normal text-gray-400">en minutes</span>
            </label>
            <input type="number" name="break_minutes" value="{{ $stageBreak }}" min="0" max="480" step="15"
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">120 pour deux heures. 0 pour ne rien déduire.</p>
            @error('break_minutes')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>
    </div>

    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Jours de présence</h4>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        Laissez les heures d'un jour vides pour qu'il suive l'horaire ci-dessus.
        Renseignez-les pour une demi-journée ou un jour à horaire particulier.
    </p>

    <div class="space-y-3">
        @foreach($jours as $jour)
            @php
                $isChecked = in_array($jour->id, $selected);
                $pivot     = $pivots->get($jour->id)?->pivot;
                $dStart    = $oldPerDay[$jour->id]['start_time']    ?? ($pivot?->start_time ? substr($pivot->start_time, 0, 5) : '');
                $dEnd      = $oldPerDay[$jour->id]['end_time']      ?? ($pivot?->end_time ? substr($pivot->end_time, 0, 5) : '');
                $dBreak    = $oldPerDay[$jour->id]['break_minutes'] ?? ($pivot?->break_minutes ?? '');
            @endphp

            <div x-data="{ on: {{ $isChecked ? 'true' : 'false' }} }"
                 class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 transition"
                 :class="on ? 'bg-blue-50/40 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800/50' : ''">

                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}" x-model="on"
                        @checked($isChecked)
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $jour->jour }}</span>
                </label>

                <div x-show="on" x-cloak class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Arrivée ce jour-là</label>
                        <input type="time" name="jour_schedule[{{ $jour->id }}][start_time]" value="{{ $dStart }}"
                            placeholder="{{ $stageStart }}"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Départ ce jour-là</label>
                        <input type="time" name="jour_schedule[{{ $jour->id }}][end_time]" value="{{ $dEnd }}"
                            placeholder="{{ $stageEnd }}"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Pause ce jour-là</label>
                        <input type="number" name="jour_schedule[{{ $jour->id }}][break_minutes]" value="{{ $dBreak }}"
                            min="0" max="480" step="15" placeholder="{{ $stageBreak }}"
                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @error('jours_id')<p class="mt-2 text-sm text-red-500">{{ $message }}</p>@enderror
    @error('jour_schedule')<p class="mt-2 text-sm text-red-500">{{ $message }}</p>@enderror
</div>
