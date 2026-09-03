@php
    /** @var \App\Models\Stage|null $stage */
    $stage = $stage ?? null;

    // L'horaire de référence vit en base, plus en configuration.
    $reference = \App\Models\WorkScheduleSetting::current();
    $defaults  = [
        'start'         => substr($reference->start_time, 0, 5),
        'end'           => substr($reference->end_time, 0, 5),
        'break_minutes' => (int) $reference->break_minutes,
    ];

    $selected = old('jours_id', $stage?->jours->pluck('id')->toArray() ?? []);

    $stageStart = old('expected_check_in_time', $stage?->expected_check_in_time ? substr($stage->expected_check_in_time, 0, 5) : $defaults['start']);
    $stageEnd   = old('expected_check_out_time', $stage?->expected_check_out_time ? substr($stage->expected_check_out_time, 0, 5) : $defaults['end']);

    // La pause se saisit en heures. Elle reste stockée en minutes.
    $pauseMinutes = $stage?->break_minutes ?? $defaults['break_minutes'];
    $stageBreak   = old('break_hours', rtrim(rtrim(number_format($pauseMinutes / 60, 2, '.', ''), '0'), '.') ?: '0');
@endphp

<div class="border-t border-gray-100 dark:border-gray-700 pt-6">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Horaires</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 max-w-3xl">
        Cet horaire s'applique à tous les jours de présence du stage.
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
                Pause <span class="font-normal text-gray-400">en heures</span>
            </label>
            <input type="number" name="break_hours" value="{{ $stageBreak }}" min="0" max="8" step="0.5"
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">2 pour deux heures, 0.5 pour trente minutes.</p>
            @error('break_hours')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
        </div>
    </div>

    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Jours de présence</h4>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        Les jours où le stagiaire est attendu. Tous suivent l'horaire ci-dessus.
    </p>

    <div class="flex flex-wrap gap-2.5">
        @foreach($jours as $jour)
            <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition
                          border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900
                          hover:bg-gray-100 dark:hover:bg-gray-800
                          has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400
                          dark:has-[:checked]:bg-blue-900/20 dark:has-[:checked]:border-blue-700">
                <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}"
                    @checked(in_array($jour->id, $selected))
                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $jour->jour }}</span>
            </label>
        @endforeach
    </div>

    @error('jours_id')<p class="mt-2 text-sm text-red-500">{{ $message }}</p>@enderror
</div>
