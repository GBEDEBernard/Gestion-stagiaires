<x-app-layout title="Rapports de travail">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Rapports de travail</h1>
                <p class="text-sm text-slate-500 mt-1">Gérez vos rapports quotidiens, hebdomadaires et mensuels</p>
            </div>

            <a href="{{ route('presence.pointage') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Revenir au pointage
            </a>
        </div>

        {{-- Period Tabs --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-1 shadow-sm mb-6">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('reports.index', ['period' => 'daily']) }}" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period', 'daily') === 'daily' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    📅 Quotidien
                </a>
                <a href="{{ route('reports.index', ['period' => 'weekly']) }}" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period', 'daily') === 'weekly' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    📊 Hebdomadaire
                </a>
                <a href="{{ route('reports.index', ['period' => 'monthly']) }}" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period', 'daily') === 'monthly' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    📈 Mensuel
                </a>
            </nav>
        </div>

        @if(request('period', 'daily') === 'daily')
        @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-700">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700">
            <p class="font-medium">Le rapport n'a pas pu etre enregistre.</p>
            <ul class="mt-2 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (! $activeStage)
        <div class="rounded-2xl bg-amber-50 border border-amber-200 px-5 py-5 text-amber-800">
            Aucun stage actif n'est disponible aujourd'hui pour remplir un rapport.
        </div>
        @else
        <div class="grid gap-4 lg:grid-cols-3 mb-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Stage actif</p>
                <p class="mt-2 text-lg font-semibold text-slate-900">{{ $activeStage->theme ?: 'Stage sans theme' }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $activeStage->site?->name ?: 'Site non defini' }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Presence du jour</p>
                <p class="mt-2 text-lg font-semibold text-slate-900">{{ $attendanceDay?->day_status ?: 'pending' }}</p>
                <p class="mt-1 text-sm text-slate-600">
                    Arrivee: {{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}
                    |
                    Depart: {{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Etat du rapport</p>
                <p class="mt-2 text-lg font-semibold text-slate-900">{{ $todayReport?->status ?: 'draft' }}</p>
                <p class="mt-1 text-sm text-slate-600">
                    Derniere mise a jour:
                    {{ $todayReport?->updated_at?->format('d/m/Y H:i') ?: 'Aucune encore' }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Synthese de la journee</h2>
                    <p class="text-sm text-slate-500 mt-1">Commence par le plus important, puis detaille ce qui merite un suivi.</p>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="lg:col-span-2">
                        <label for="summary" class="block text-sm font-medium text-slate-700 mb-2">Resume de la journee</label>
                        <textarea id="summary" name="summary" rows="5" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" placeholder="Resumer clairement ce qui a ete realise aujourd'hui..." required>{{ old('summary', $todayReport?->summary) }}</textarea>
                    </div>

                    <div>
                        <label for="blockers" class="block text-sm font-medium text-slate-700 mb-2">Blocages ou risques</label>
                        <textarea id="blockers" name="blockers" rows="4" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" placeholder="Indique les points qui ralentissent le travail...">{{ old('blockers', $todayReport?->blockers) }}</textarea>
                    </div>

                    <div>
                        <label for="next_steps" class="block text-sm font-medium text-slate-700 mb-2">Prochaines actions</label>
                        <textarea id="next_steps" name="next_steps" rows="4" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" placeholder="Ce que tu comptes faire ensuite...">{{ old('next_steps', $todayReport?->next_steps) }}</textarea>
                    </div>

                    <div>
                        <label for="hours_declared" class="block text-sm font-medium text-slate-700 mb-2">Heures declarees</label>
                        <input id="hours_declared" name="hours_declared" type="number" min="0" max="24" step="0.25" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" value="{{ old('hours_declared', $todayReport?->hours_declared ?: 0) }}">
                    </div>

                    <div>
                        <label for="completion_rate" class="block text-sm font-medium text-slate-700 mb-2">Progression globale (%)</label>
                        <input id="completion_rate" name="completion_rate" type="number" min="0" max="100" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" value="{{ old('completion_rate', $todayReport?->completion_rate) }}">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                <div class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold text-slate-900">Taches du stage</h2>
                    <p class="text-sm text-slate-500">Chaque mise a jour alimente le rapport du jour et l'historique d'avancement.</p>
                </div>

                @if ($activeStage->tasks->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                    Aucune tache n'est encore rattachee a ce stage. Tu peux quand meme remplir les activites complementaires plus bas.
                </div>
                @else
                <div class="space-y-4">
                    @foreach ($activeStage->tasks as $task)
                    @php
                    $taskItem = $taskItems->get($task->id);
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="text-base font-semibold text-slate-900">{{ $task->title }}</p>
                                <p class="text-sm text-slate-500 mt-1">{{ $task->description ?: 'Aucune description fournie.' }}</p>
                            </div>

                            <div class="text-sm text-slate-500">
                                <p>Statut: <span class="font-medium text-slate-700">{{ $task->status }}</span></p>
                                <p>Progression actuelle: <span class="font-medium text-slate-700">{{ $task->last_progress_percent }}%</span></p>
                            </div>
                        </div>

                        <input type="hidden" name="items[{{ $loop->index }}][task_id]" value="{{ $task->id }}">
                        <input type="hidden" name="items[{{ $loop->index }}][work_type]" value="task_update">

                        <div class="mt-4 grid gap-4 lg:grid-cols-3">
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Travail realise sur cette tache</label>
                                <textarea name="items[{{ $loop->index }}][description]" rows="3" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" placeholder="Decris ce qui a avance aujourd'hui...">{{ old("items.{$loop->index}.description", $taskItem?->description) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Temps passe (minutes)</label>
                                <input name="items[{{ $loop->index }}][duration_minutes]" type="number" min="0" max="1440" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" value="{{ old("items.{$loop->index}.duration_minutes", $taskItem?->duration_minutes) }}">
                            </div>

                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Resultat / remarque</label>
                                <textarea name="items[{{ $loop->index }}][outcome]" rows="3" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" placeholder="Resultat obtenu, decision prise, difficulte rencontree...">{{ old("items.{$loop->index}.outcome", $taskItem?->outcome) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Nouvelle progression (%)</label>
                                <input name="items[{{ $loop->index }}][progress_percent]" type="number" min="0" max="100" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" value="{{ old("items.{$loop->index}.progress_percent", $taskItem?->progress_percent ?? $task->last_progress_percent) }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                <div class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold text-slate-900">Activites complementaires</h2>
                    <p class="text-sm text-slate-500">Pour garder un rapport complet, tu peux aussi declarer les actions qui ne sont pas directement liees a une tache assignee.</p>
                </div>

                {{-- jb -> On reserve volontairement quelques lignes libres pour
                    garder un rapport vivant meme quand la journee a depasse le cadre
                    strict des taches planifiees. --}}
                <div class="space-y-4">
                    @for ($freeIndex = 0; $freeIndex < 3; $freeIndex++)
                        @php
                        $itemIndex=$activeStage->tasks->count() + $freeIndex;
                        $freeItem = $freeItems->get($freeIndex);
                        @endphp
                        <div class="rounded-2xl border border-dashed border-slate-300 p-5">
                            <input type="hidden" name="items[{{ $itemIndex }}][work_type]" value="free_entry">

                            <div class="grid gap-4 lg:grid-cols-3">
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Activite</label>
                                    <textarea name="items[{{ $itemIndex }}][description]" rows="3" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" placeholder="Reunion, assistance, recherche, documentation...">{{ old("items.{$itemIndex}.description", $freeItem?->description) }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Temps passe (minutes)</label>
                                    <input name="items[{{ $itemIndex }}][duration_minutes]" type="number" min="0" max="1440" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" value="{{ old("items.{$itemIndex}.duration_minutes", $freeItem?->duration_minutes) }}">
                                </div>

                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Resultat / valeur apporte</label>
                                    <textarea name="items[{{ $itemIndex }}][outcome]" rows="3" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" placeholder="Ce que cette activite a permis de produire ou de clarifier...">{{ old("items.{$itemIndex}.outcome", $freeItem?->outcome) }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Progression (%)</label>
                                    <input name="items[{{ $itemIndex }}][progress_percent]" type="number" min="0" max="100" class="w-full rounded-2xl border-slate-300 focus:border-slate-900 focus:ring-slate-900" value="{{ old("items.{$itemIndex}.progress_percent", $freeItem?->progress_percent) }}">
                                </div>
                            </div>
                        </div>
                        @endfor
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button type="submit" name="status_action" value="draft" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Enregistrer le brouillon
                </button>

                <button type="submit" name="status_action" value="submit" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white hover:bg-slate-800">
                    Soumettre le rapport du jour
                </button>
            </div>
        </form>
        @endif
        @else
        {{-- Weekly/Monthly Reports History --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    Rapports {{ $period === 'weekly' ? 'hebdomadaires' : 'mensuels' }}
                    <span class="px-2 py-1 bg-slate-200 text-slate-800 text-xs font-semibold rounded-full">
                        {{ $reports->count() }} rapports
                    </span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Résumé</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Heures</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Progression</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($reports as $report)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $report->report_date->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $report->report_date->translatedFormat('l') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 max-w-xs truncate">
                                {{ Str::limit($report->summary, 50) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold">
                                {{ $report->hours_declared }}h
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold">
                                {{ $report->completion_rate }}%
                            </td>
                            <td class="px-6 py-4">
                                @if($report->status === 'submitted')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">
                                    Soumis
                                </span>
                                @elseif($report->status === 'approved')
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    Approuvé
                                </span>
                                @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-800 text-xs font-semibold rounded-full">
                                    {{ ucfirst($report->status) }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-16 w-16 mb-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">Aucun rapport trouvé</h3>
                                <p class="text-slate-500">Vos rapports apparaîtront ici une fois soumis.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>