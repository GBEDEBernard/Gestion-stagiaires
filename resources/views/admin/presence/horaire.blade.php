<x-app-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Horaire de référence</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
            L'horaire de l'entreprise. Il s'applique aux employés et à tout stage qui ne déclare
            pas le sien. L'heure d'arrivée détermine le retard : une minute après, le pointage
            est compté en retard, sans tolérance.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-sm text-emerald-800 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-sm text-red-800 dark:text-red-300">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden max-w-3xl">
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Horaire de l'entreprise</h3>
        </div>

        <form method="POST" action="{{ route('admin.presence.horaire.update') }}" class="p-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Arrivée attendue</label>
                    <input type="time" name="start_time" required
                        value="{{ old('start_time', substr($setting->start_time, 0, 5)) }}"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Départ attendu</label>
                    <input type="time" name="end_time" required
                        value="{{ old('end_time', substr($setting->end_time, 0, 5)) }}"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Pause <span class="font-normal text-gray-400">en minutes</span>
                    </label>
                    <input type="number" name="break_minutes" min="0" max="480" step="15"
                        value="{{ old('break_minutes', $setting->break_minutes) }}"
                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Déduite du temps de présence.</p>
                </div>
            </div>

            <div class="mt-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50">
                <p class="text-sm text-amber-800 dark:text-amber-300">
                    Ce changement ne touche pas les pointages déjà enregistrés : il s'applique
                    aux prochains. Un stage qui déclare son propre horaire garde le sien, et une
                    demi-journée se règle jour par jour sur la fiche du stage.
                </p>
            </div>

            @if($setting->exists && $setting->editor)
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    Dernière modification par {{ $setting->editor->name }}
                    le {{ $setting->updated_at?->format('d/m/Y à H:i') }}.
                </p>
            @endif

            <div class="mt-6">
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition shadow-sm">
                    Enregistrer l'horaire
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
