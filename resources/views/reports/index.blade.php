<x-app-layout title="Rapports de travail">
    <div class="max-w-5xl mx-auto px-4 py-8 space-y-8">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Rapports de travail</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Crée et gère tes rapports quotidiens
                </p>
            </div>

            <a href="{{ route('presence.pointage') }}" class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-slate-200">
                Retour au pointage
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 space-y-5">
            <h2 class="font-semibold text-slate-900 dark:text-white">
                {{ isset($editReport) ? 'Modifier le rapport' : 'Nouveau rapport' }}
            </h2>

            <form method="POST" action="{{ isset($editReport) ? route('reports.update', $editReport->id) : route('reports.store') }}">
                @csrf
                @isset($editReport)
                    @method('PUT')
                @endisset

                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Résumé</label>
                    <textarea name="summary" rows="4" class="w-full mt-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" required>{{ old('summary', $editReport->summary ?? '') }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Blocages</label>
                    <textarea name="blockers" rows="3" class="w-full mt-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('blockers', $editReport->blockers ?? '') }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Prochaines actions</label>
                    <textarea name="next_steps" rows="3" class="w-full mt-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ old('next_steps', $editReport->next_steps ?? '') }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">Heures travaillées</label>
                    <input type="number" name="hours_declared" class="w-full mt-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" value="{{ old('hours_declared', $editReport->hours_declared ?? 0) }}">
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="submit" name="status_action" value="draft" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-900 dark:text-slate-200">
                        Brouillon
                    </button>

                    <button type="submit" name="status_action" value="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 font-semibold text-slate-900 dark:text-white">
                Historique des rapports
            </div>

            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($reports as $report)
                    <div class="p-4 flex justify-between items-center gap-4">
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">
                                {{ $report->report_date->format('d M Y') }}
                            </p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ \Illuminate\Support\Str::limit($report->summary, 60) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-sm text-slate-600 dark:text-slate-300">
                                {{ $report->hours_declared }}h
                            </span>

                            <a href="{{ route('reports.index', ['period' => 'daily', 'edit' => $report->id]) }}" class="text-sm px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-900 dark:text-slate-200">
                                Modifier
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 dark:text-slate-400">
                        Aucun rapport
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
