<x-app-layout title="Modifier le rapport">

    <div class="max-w-2xl mx-auto px-6 py-12">

        <!-- HEADER -->
        <div class="mb-8">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour aux rapports
            </a>

            <div>
                <h1 class="text-3xl font-bold text-slate-900">Modifier le rapport</h1>
                <p class="text-lg text-slate-500 mt-2">
                    Date: <span class="font-semibold text-slate-900">{{ $report->report_date->format('j F Y') }}</span>
                </p>
            </div>
        </div>

        <!-- STATUS BADGE -->
        <div class="mb-8 flex items-center gap-3">
            @if($report->status === 'submitted')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Soumis
            </span>
            @elseif($report->status === 'reviewed')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Révisé
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Brouillon
            </span>
            @endif
        </div>

        <!-- FORM -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">

            <form method="POST" action="{{ route('reports.update', $report->id) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Summary -->
                <div>
                    <label for="summary" class="block text-sm font-semibold text-slate-900 mb-3">Résumé</label>
                    <textarea
                        id="summary"
                        name="summary"
                        rows="5"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-transparent text-base transition-all duration-200 @error('summary') border-red-500 focus:ring-red-500 @enderror"
                        placeholder="Décrivez vos activités du jour...">{{ old('summary', $report->summary) }}</textarea>
                    @error('summary')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Blockers -->
                <div>
                    <label for="blockers" class="block text-sm font-semibold text-slate-900 mb-3">Blocages rencontrés</label>
                    <textarea
                        id="blockers"
                        name="blockers"
                        rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-transparent text-base transition-all duration-200 @error('blockers') border-red-500 focus:ring-red-500 @enderror"
                        placeholder="Listez les obstacles rencontrés...">{{ old('blockers', $report->blockers) }}</textarea>
                    @error('blockers')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Next Steps -->
                <div>
                    <label for="next_steps" class="block text-sm font-semibold text-slate-900 mb-3">Prochaines étapes</label>
                    <textarea
                        id="next_steps"
                        name="next_steps"
                        rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-transparent text-base transition-all duration-200 @error('next_steps') border-red-500 focus:ring-red-500 @enderror"
                        placeholder="Décrivez vos prochaines actions...">{{ old('next_steps', $report->next_steps) }}</textarea>
                    @error('next_steps')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hours and Date -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="hours_declared" class="block text-sm font-semibold text-slate-900 mb-3">Heures déclarées</label>
                        <input
                            type="number"
                            id="hours_declared"
                            name="hours_declared"
                            min="0"
                            max="24"
                            step="0.5"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-transparent text-base transition-all duration-200 @error('hours_declared') border-red-500 focus:ring-red-500 @enderror"
                            value="{{ old('hours_declared', $report->hours_declared) }}">
                        @error('hours_declared')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="report_date" class="block text-sm font-semibold text-slate-900 mb-3">Date du rapport</label>
                        <input
                            type="date"
                            id="report_date"
                            name="report_date"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-slate-500 focus:border-transparent text-base transition-all duration-200 @error('report_date') border-red-500 focus:ring-red-500 @enderror"
                            value="{{ old('report_date', $report->report_date->toDateString()) }}">
                        @error('report_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- FORM ACTIONS -->
                <div class="flex items-center gap-4 pt-6 border-t border-slate-200">
                    <a href="{{ route('reports.index') }}"
                        class="px-6 py-3 text-sm font-medium text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Enregistrer les modifications
                    </button>
                </div>

            </form>

        </div>

    </div>

</x-app-layout>