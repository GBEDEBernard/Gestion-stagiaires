<x-app-layout>
    <style>
        @keyframes clignote {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.02); }
        }
        .btn-modifier {
            animation: clignote 1.2s ease-in-out infinite;
        }
        .btn-modifier:hover {
            animation: none;
            opacity: 1;
            transform: scale(1.05);
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 bg-slate-50/50 min-h-screen">
        
        <!-- En-tête / Banner Premium -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-slate-900 px-8 py-10 text-white shadow-xl ring-1 ring-white/10">
            <!-- Éléments décoratifs -->
            <div class="absolute -right-20 -top-20 h-96 w-96 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-medium uppercase tracking-[0.3em] text-indigo-200/90">Espace stagiaire</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight">Mon stage</h1>
                    <p class="mt-3 max-w-2xl text-sm text-indigo-100/80">
                        Une vue claire pour suivre la présence, les tâches du jour et l'état du rapport sans passer par tout le back-office.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('presence.pointage') }}" class="inline-flex items-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-lg shadow-indigo-900/20 hover:bg-slate-50 transition-colors">
                        Pointer ma présence
                    </a>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-xl border border-white/20 bg-white/5 px-5 py-2.5 text-sm font-medium text-white backdrop-blur-sm hover:bg-white/10 transition-colors">
                        Ouvrir le rapport du jour
                    </a>
                </div>
            </div>
        </div>

        @if (! $activeStage)
        <div class="rounded-xl border-l-4 border-amber-400 bg-amber-50/80 px-5 py-5 text-amber-800 backdrop-blur-sm">
            <p class="font-medium">Aucun stage actif</p>
            <p class="text-sm">Aucun stage actif n'est rattaché à ton compte pour aujourd'hui. Vérifie avec l'administration.</p>
        </div>
        @else
        @if(session('success'))
        <div class="rounded-xl border-l-4 border-emerald-400 bg-emerald-50/80 px-5 py-4 text-sm text-emerald-800 backdrop-blur-sm shadow-sm">
            {{ session('success') }}
        </div>
        @endif

        <!-- Grille 1 : Informations générales -->
        <div class="grid gap-6 lg:grid-cols-4">
            <!-- Carte 1 : Thème, Site & Superviseur -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5 lg:col-span-2"
                 x-data="{ editing: false, theme: @js($activeStage->theme ?? '') }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <p class="text-sm font-medium text-slate-500">Thème du stage</p>
                    <button @click="editing = true" x-show="!editing" class="btn-modifier inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        Modifier le thème
                    </button>
                </div>
                
                <template x-if="!editing">
                    <h2 class="mt-4 text-xl font-bold text-slate-900" x-text="theme || 'Stage sans thème'"></h2>
                </template>
                
                <template x-if="editing">
                    <form method="POST" action="{{ route('student.theme.update') }}" class="mt-4">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col gap-3">
                            <input type="text" name="theme" x-model="theme"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-lg font-medium text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-shadow"
                                placeholder="Ex: Développement web, marketing digital...">
                            <div class="flex gap-3">
                                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition-colors">
                                    Enregistrer
                                </button>
                                <button type="button" @click="editing = false; theme = @js($activeStage->theme ?? '')"
                                    class="rounded-lg border border-slate-200 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </form>
                </template>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50/50 p-4 ring-1 ring-slate-100">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Site</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $activeStage->site?->name ?: 'Site non défini' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $activeStage->site?->city ?: 'Ville non définie' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50/50 p-4 ring-1 ring-slate-100">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Superviseur</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $activeStage->supervisor?->name ?: 'Aucun superviseur' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $activeStage->supervisor?->email ?: 'Email non défini' }}</p>
                    </div>
                </div>
            </div>

            <!-- Carte 2 : Présence du jour -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                <p class="text-sm font-medium text-slate-500">Présence du jour</p>
                @php
                    $statusMap = [
                        'late' => 'En retard',
                        'present' => 'Présent',
                        'absent' => 'Absent',
                        'incomplete' => 'Incomplet',
                        'pending' => 'En attente',
                        'completed' => 'Terminé',
                    ];
                    $statusKey = $attendanceDay?->day_status;
                    $statusFr = $statusMap[$statusKey] ?? ($statusKey ? ucfirst($statusKey) : '--');
                @endphp
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $statusFr }}</p>
                <div class="mt-4 flex items-center gap-6 text-sm text-slate-500">
                    <div>
                        <span class="block text-xs uppercase text-slate-400">Arrivée</span>
                        <span class="font-medium text-slate-800">{{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase text-slate-400">Départ</span>
                        <span class="font-medium text-slate-800">{{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }}</span>
                    </div>
                </div>
            </div>

            <!-- Carte 3 : Rapport du jour -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                <p class="text-sm font-medium text-slate-500">Rapport du jour</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $todayReport?->status ?: 'En attente' }}</p>
                <div class="mt-4 space-y-2 text-sm text-slate-500">
                    <div class="flex justify-between">
                        <span>Mise à jour :</span>
                        <span class="font-medium text-slate-800">{{ $todayReport?->updated_at?->format('d/m/Y H:i') ?: 'Aucune' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Progression :</span>
                        <span class="font-medium text-slate-800">{{ $todayReport?->completion_rate ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grille 2 : Tâches & Cadre -->
        <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
            
            <!-- Section Tâches -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Tâches du stage</h2>
                        <p class="text-sm text-slate-500">Suivi rapide de ce qui est en cours et de ce qui est déjà bouclé.</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $completedTasksCount }} terminée(s)
                        </span>
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $openTasksCount }} à suivre
                        </span>
                    </div>
                </div>

                @if ($tasks->isEmpty())
                <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50/50 px-6 py-8 text-center text-sm text-slate-600">
                    <p>Aucune tâche n'est encore rattachée à ce stage.</p>
                    <p class="mt-1 text-xs">Tu peux déjà utiliser le rapport journalier pour décrire clairement ton avancement.</p>
                </div>
                @else
                <div class="mt-6 space-y-2">
                    @foreach ($tasks as $task)
                    <div class="group rounded-xl border border-slate-100 bg-slate-50/30 p-5 hover:bg-white hover:shadow-md transition-all">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-semibold text-slate-900">{{ $task->title }}</h3>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                                                    {{ $task->status === 'completed' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-700 ring-slate-600/20' }}">
                                        {{ $task->status }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $task->description ?: 'Aucune description fournie.' }}</p>
                                
                                <!-- Barre de progression visuelle ajoutée -->
                                @if(isset($task->last_progress_percent))
                                <div class="mt-2 flex max-w-xs items-center gap-2">
                                    <div class="h-1.5 w-full rounded-full bg-slate-200">
                                        <div class="h-1.5 rounded-full bg-indigo-500" style="width: {{ $task->last_progress_percent }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-slate-600">{{ $task->last_progress_percent }}%</span>
                                </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-slate-500 sm:text-right">
                                <div>Priorité : <span class="font-medium text-slate-800">{{ ucfirst($task->priority) }}</span></div>
                                <div>Échéance : <span class="font-medium text-slate-800">{{ $task->due_date?->format('d/m/Y') ?: 'Non définie' }}</span></div>
                                <div class="col-span-2">Démarré le : <span class="font-medium text-slate-800">{{ $task->started_at?->format('d/m/Y') ?: '--' }}</span></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Colonne Droite : Cadre & Rappels -->
            <div class="space-y-6">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                    <h2 class="text-lg font-semibold text-slate-900">Cadre du stage</h2>
                    <dl class="mt-4 space-y-2 text-sm text-slate-600 divide-y divide-slate-100">
                        <div class="flex items-center justify-between py-2.5">
                            <dt>Période</dt>
                            <dd class="font-medium text-slate-900">
                                {{ $activeStage->date_debut?->format('d/m/Y') }} - {{ $activeStage->date_fin?->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt>Domaine</dt>
                            <dd class="font-medium text-slate-900">{{ $activeStage->domaine?->nom ?: 'Non défini' }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt>Type</dt>
                            <dd class="font-medium text-slate-900">{{ $activeStage->typestage?->nom_type_stage ?: 'Non défini' }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt>Horaire attendu</dt>
                            <dd class="font-medium text-slate-900">
                                {{ $activeStage->expected_check_in_time ?: '--:--' }} - {{ $activeStage->expected_check_out_time ?: '--:--' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt>Mode présence</dt>
                            <dd class="font-medium text-slate-900">{{ $activeStage->presence_mode ?: 'Géolocalisée' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5">
                    <h2 class="text-lg font-semibold text-slate-900">Rappels utiles</h2>
                    <ul class="mt-3 space-y-2 text-sm text-slate-500">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 h-2 w-2 rounded-full bg-indigo-500"></span>
                            <span>Pointe d'abord ta présence, puis remplis ton rapport avec les tâches travaillées.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 h-2 w-2 rounded-full bg-indigo-500"></span>
                            <span>Si ton site ou superviseur est incorrect, signale-le à l'administration.</span>
                        </li>
                    </ul>

                    <div class="mt-6 grid gap-3">
                        <a href="{{ route('presence.pointage') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            Aller au pointage
                        </a>
                        <a href="{{ route('reports.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Aller au rapport journalier
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>