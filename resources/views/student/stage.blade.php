<x-app-layout>
    @php $user = auth()->user(); @endphp
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

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- En-tête / Banner Premium -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-slate-900 px-6 sm:px-8 py-8 sm:py-10 text-white shadow-xl ring-1 ring-white/10">
            <!-- Éléments décoratifs -->
            <div class="absolute -right-20 -top-20 h-96 w-96 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-200/90">Espace stagiaire</p>
                    <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight">Mon stage</h1>
                    <p class="mt-2 max-w-2xl text-sm text-indigo-100/80">
                        Une vue claire pour suivre ma présence, mes tâches du jour et l'état du rapport sans passer par tout le back-office.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('presence.pointage') }}" class="inline-flex items-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-lg shadow-indigo-900/20 hover:bg-slate-100 transition-all">
                        Pointer ma présence
                    </a>
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-5 py-2.5 text-sm font-medium text-white backdrop-blur-sm hover:bg-white/20 transition-all">
                        Ouvrir le rapport du jour
                    </a>
                </div>
            </div>
        </div>

        @if (! $activeStage)
        <div class="rounded-2xl border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-950/40 p-5 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800/60 shadow-sm">
            <p class="font-bold text-base">Aucun stage actif</p>
            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">Aucun stage actif n'est rattaché à ton compte pour aujourd'hui. Vérifie avec l'administration.</p>
        </div>
        @else
        @if(session('success'))
        <div class="rounded-2xl border-l-4 border-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 p-4 text-sm text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800/60 shadow-sm">
            {{ session('success') }}
        </div>
        @endif

        <!-- Grille 1 : Informations générales -->
        <div class="grid gap-6 lg:grid-cols-4">
            <!-- Carte 1 : Thème, Site & Superviseur -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-slate-200/80 dark:border-gray-700 lg:col-span-2"
                 x-data="{ editing: false, theme: @js($activeStage->theme ?? '') }">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700/80 pb-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400">Thème du stage</p>
                    <button @click="editing = true" x-show="!editing" class="btn-modifier inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition-all">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        Modifier le thème
                    </button>
                </div>
                
                <template x-if="!editing">
                    <h2 class="mt-4 text-xl font-bold text-slate-900 dark:text-white leading-snug break-words" x-text="theme || 'Stage sans thème'"></h2>
                </template>
                
                <template x-if="editing">
                    <form method="POST" action="{{ route('student.theme.update') }}" class="mt-4">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col gap-3">
                            <input type="text" name="theme" x-model="theme"
                                class="w-full rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-base font-medium text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-shadow"
                                placeholder="Ex: Développement web, marketing digital...">
                            <div class="flex gap-2">
                                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                                    Enregistrer
                                </button>
                                <button type="button" @click="editing = false; theme = @js($activeStage->theme ?? '')"
                                    class="rounded-xl border border-slate-200 dark:border-gray-700 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </form>
                </template>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 dark:bg-gray-900/60 p-4 border border-slate-100 dark:border-gray-700/60">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-400">Site d'affectation</p>
                        <p class="mt-1.5 text-sm font-bold text-slate-900 dark:text-white truncate">{{ $activeStage->site?->name ?: 'Site non défini' }}</p>
                        <p class="text-xs text-slate-500 dark:text-gray-400 truncate">{{ $activeStage->site?->city ?: 'Ville non définie' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-gray-900/60 p-4 border border-slate-100 dark:border-gray-700/60">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-gray-400">Superviseur</p>
                        <p class="mt-1.5 text-sm font-bold text-slate-900 dark:text-white truncate">{{ $activeStage->supervisor?->name ?: 'Aucun superviseur' }}</p>
                        <p class="text-xs text-slate-500 dark:text-gray-400 truncate">{{ $activeStage->supervisor?->email ?: 'Email non défini' }}</p>
                    </div>
                </div>
            </div>

            <!-- Carte 2 : Présence du jour -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400">Présence du jour</p>
                    @php
                        $statusMap = [
                            'late' => ['En retard', 'text-amber-600 dark:text-amber-400'],
                            'present' => ['Présent', 'text-emerald-600 dark:text-emerald-400'],
                            'absent' => ['Absent', 'text-rose-600 dark:text-rose-400'],
                            'incomplete' => ['Incomplet', 'text-amber-600 dark:text-amber-400'],
                            'pending' => ['En attente', 'text-blue-600 dark:text-blue-400'],
                            'completed' => ['Terminé', 'text-emerald-600 dark:text-emerald-400'],
                        ];
                        $statusKey = $attendanceDay?->day_status;
                        $statusInfo = $statusMap[$statusKey] ?? [$statusKey ? ucfirst($statusKey) : 'Non pointé', 'text-slate-700 dark:text-gray-300'];
                    @endphp
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</p>
                    <div class="mt-4 flex items-center gap-6 text-sm">
                        <div>
                            <span class="block text-[11px] uppercase tracking-wider font-semibold text-slate-400 dark:text-gray-500">Arrivée</span>
                            <span class="font-bold text-slate-800 dark:text-gray-200">{{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] uppercase tracking-wider font-semibold text-slate-400 dark:text-gray-500">Départ</span>
                            <span class="font-bold text-slate-800 dark:text-gray-200">{{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 dark:border-gray-700/80 pt-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400">Présence semaine</span>
                        <span class="font-bold text-slate-800 dark:text-gray-200">{{ $presenceSemaine }}%</span>
                    </div>
                    <div class="mt-2 h-2 w-full rounded-full bg-slate-100 dark:bg-gray-700 overflow-hidden">
                        <div class="h-2 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-700" style="width: {{ $presenceSemaine }}%"></div>
                    </div>
                    <p class="mt-1.5 text-[11px] text-slate-500 dark:text-gray-400">{{ $joursPresentSemaine }} jour(s) pointé(s) sur {{ $joursTrackesSemaine }}</p>
                </div>
            </div>

            <!-- Carte 3 : Rapport du jour -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-slate-200/80 dark:border-gray-700 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400">Rapport du jour</p>
                    @php
                        $reportStatusMap = [
                            'submitted' => ['Soumis', 'text-blue-600 dark:text-blue-400'],
                            'reviewed' => ['Validé', 'text-emerald-600 dark:text-emerald-400'],
                            'approved' => ['Approuvé', 'text-emerald-600 dark:text-emerald-400'],
                            'draft' => ['Brouillon', 'text-slate-600 dark:text-gray-300'],
                            'pending' => ['En attente', 'text-amber-600 dark:text-amber-400'],
                            'rejected' => ['Rejeté', 'text-rose-600 dark:text-rose-400'],
                        ];
                        $todayReportStatus = $todayReport?->status ? ($reportStatusMap[$todayReport->status] ?? [ucfirst($todayReport->status), 'text-slate-800 dark:text-gray-200']) : ['En attente', 'text-amber-600 dark:text-amber-400'];
                    @endphp
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold {{ $todayReportStatus[1] }}">{{ $todayReportStatus[0] }}</p>
                    <div class="mt-4 space-y-2 text-xs text-slate-500 dark:text-gray-400">
                        <div class="flex justify-between">
                            <span>Dernière mise à jour :</span>
                            <span class="font-bold text-slate-800 dark:text-gray-200">{{ $todayReport?->updated_at?->format('d/m/Y H:i') ?: 'Aucune' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Progression déclarée :</span>
                            <span class="font-bold text-slate-800 dark:text-gray-200">{{ $todayReport?->completion_rate ?? 0 }}%</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 dark:border-gray-700/80 pt-4">
                    <a href="{{ route('tasks.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 px-3 py-2 text-xs font-bold text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors">
                        Consulter mon rapport
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Grille 2 : Tâches & Cadre -->
        <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
            
            <!-- Section Tâches du stage -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-slate-200/80 dark:border-gray-700">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 dark:border-gray-700/80 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tâches du stage</h2>
                        <p class="text-xs text-slate-500 dark:text-gray-400">Suivi rapide de ce qui est en cours et de ce qui est déjà bouclé.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20">
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $completedTasksCount }} terminée(s)
                        </span>
                        <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20">
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $openTasksCount }} à suivre
                        </span>
                    </div>
                </div>

                @if ($tasks->isEmpty())
                <div class="mt-6 rounded-2xl border-2 border-dashed border-slate-200 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-900/40 p-8 text-center">
                    <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="font-bold text-slate-800 dark:text-gray-200 text-sm">Aucune tâche n'est encore rattachée à ce stage.</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400 max-w-sm mx-auto">Tu peux utiliser le rapport journalier pour décrire clairement ton avancement au quotidien.</p>
                </div>
                @else
                <div class="mt-5 space-y-3.5">
                    @foreach ($tasks as $task)
                    @php
                        $myReports = $task->dailyReports->where('user_id', $user->id)->take(2);
                        $statusMeta = match ($task->status) {
                            'completed' => ['Terminée', 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 ring-emerald-600/20'],
                            'awaiting_validation' => ['En attente validation', 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 ring-purple-600/20'],
                            'blocked' => ['Bloquée', 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 ring-rose-600/20'],
                            'in_progress' => ['En cours', 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 ring-blue-600/20'],
                            'changes_requested' => ['Corrections demandées', 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 ring-amber-600/20'],
                            default => ['À faire', 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-200 ring-slate-600/20'],
                        };
                        $priorityMeta = match ($task->priority) {
                            'urgent' => ['Urgente', 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 ring-rose-600/20'],
                            'high' => ['Haute', 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 ring-amber-600/20'],
                            'low' => ['Basse', 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-gray-300 ring-slate-400/20'],
                            default => ['Normale', 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-indigo-500/20'],
                        };
                        $progressPercent = min(100, max(0, $task->last_progress_percent ?? $task->base_progress_percent ?? 0));
                    @endphp

                    {{-- CARTE DE TÂCHE : Disposition exacte selon la capture --}}
                    <div class="group rounded-2xl border border-slate-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6 shadow-sm hover:shadow-md transition-all">
                        <a href="{{ encrypted_route('tasks.show', $task) }}" class="block">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                
                                {{-- Colonne Gauche : Titre, Badges, Description, Progression, Rapports --}}
                                <div class="flex-1 min-w-0 md:pr-4">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-snug">
                                        {{ $task->title }}
                                    </h3>

                                    <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusMeta[1] }}">
                                            {{ $statusMeta[0] }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium ring-1 ring-inset {{ $priorityMeta[1] }}">
                                            {{ $priorityMeta[0] }}
                                        </span>
                                        @if ($task->owner_id === $user->id)
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-950/40 px-3 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300 ring-1 ring-inset ring-indigo-500/20">
                                            Créée par toi
                                        </span>
                                        @else
                                        <span class="inline-flex items-center rounded-full bg-sky-50 dark:bg-sky-950/40 px-3 py-0.5 text-xs font-medium text-sky-700 dark:text-sky-300 ring-1 ring-inset ring-sky-500/20">
                                            Assignée à toi
                                        </span>
                                        @endif
                                    </div>

                                    <p class="mt-2 text-sm text-slate-500 dark:text-gray-400 line-clamp-2 break-words leading-relaxed">
                                        {{ $task->description ?: 'Aucune description fournie.' }}
                                    </p>

                                    {{-- Barre de progression --}}
                                    <div class="mt-3 flex max-w-xs items-center gap-2.5">
                                        <div class="h-1.5 w-full rounded-full bg-slate-200 dark:bg-gray-700 overflow-hidden">
                                            <div class="h-1.5 rounded-full {{ $task->status === 'completed' ? 'bg-emerald-500' : 'bg-indigo-600' }}"
                                                 style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-slate-600 dark:text-gray-400">{{ $progressPercent }}%</span>
                                    </div>

                                    {{-- Rapports récents déposés par l'étudiant --}}
                                    @if ($myReports->isNotEmpty())
                                    <div class="mt-4 pt-1">
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-500">Tes rapports déposés</p>
                                        <div class="mt-2 space-y-1.5">
                                            @foreach ($myReports as $report)
                                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-gray-400">
                                                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-medium text-slate-700 dark:text-gray-200">{{ $report->report_date->isoFormat('DD MMM') }}</span>
                                                <span>·</span>
                                                <span class="font-medium">{{ $report->task_progress_percent ?? 0 }}%</span>
                                                <span>·</span>
                                                <span class="truncate max-w-sm text-slate-500 dark:text-gray-400">{{ $report->summary ?: 'Rapport déposé' }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Colonne Droite : Priorité, Démarrage, Date de fin, Lien --}}
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs text-slate-500 dark:text-gray-400 sm:text-right shrink-0 mt-2 md:mt-0">
                                    <div>Priorité : <span class="font-medium text-slate-800 dark:text-gray-200">{{ ['high'=>'Haute','low'=>'Basse','medium'=>'Normale','urgent'=>'Urgente'][$task->priority] ?? ucfirst($task->priority ?? 'Normale') }}</span></div>
                                    <div>Démarré le : <span class="font-medium text-slate-800 dark:text-gray-200">{{ $task->started_at?->format('d/m/Y') ?: ($task->created_at?->format('d/m/Y') ?: '--') }}</span></div>
                                    <div class="col-span-2">date de fin : <span class="font-medium text-slate-800 dark:text-gray-200">{{ $task->due_date?->format('d/m/Y') ?: 'Non définie' }}</span></div>
                                    <div class="col-span-2 mt-2 inline-flex items-center gap-1 font-semibold text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-700 dark:group-hover:text-indigo-300 sm:justify-end transition-colors">
                                        Ouvrir l'espace de travail
                                        <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </div>
                                </div>

                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Colonne Droite : Cadre, Rapport PDF & Rappels -->
            <div class="space-y-6">
                
                {{-- Cadre du stage --}}
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-slate-200/80 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Cadre du stage</h2>
                    <dl class="mt-4 space-y-2 text-sm divide-y divide-slate-100 dark:divide-gray-700/60">
                        <div class="flex items-center justify-between py-2.5">
                            <dt class="text-slate-500 dark:text-gray-400">Période</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">
                                {{ $activeStage->date_debut?->format('d/m/Y') }} - {{ $activeStage->date_fin?->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt class="text-slate-500 dark:text-gray-400">Domaine</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $activeStage->domaine?->nom ?: 'Non défini' }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt class="text-slate-500 dark:text-gray-400">Type de stage</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $activeStage->typestage?->libelle ?: 'Non défini' }}</dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt class="text-slate-500 dark:text-gray-400">Horaire attendu</dt>
                            <dd class="font-bold text-slate-900 dark:text-white">
                                {{ $activeStage->expected_check_in_time ?: '08:00' }} 
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2.5">
                            <dt class="text-slate-500 dark:text-gray-400">Mode présence</dt>
                            @php
                                $presenceModeMap = [
                                    'geolocation_only' => 'Géolocalisée',
                                    'geolocation' => 'Géolocalisée',
                                    'badge_only' => 'Badge uniquement',
                                    'badge' => 'Badge',
                                    'qr_code' => 'Code QR',
                                    'manual' => 'Manuel',
                                    'both' => 'Géolocalisation & Badge',
                                ];
                                $presenceModeText = $presenceModeMap[$activeStage->presence_mode] ?? ($activeStage->presence_mode ? ucfirst(str_replace('_', ' ', $activeStage->presence_mode)) : 'Géolocalisée');
                            @endphp
                            <dd class="font-bold text-slate-900 dark:text-white">{{ $presenceModeText }}</dd>
                        </div>
                        @php
                            $encoreDansPeriode = $activeStage->date_debut?->isPast() && !$activeStage->date_fin?->isPast();
                        @endphp
                        <div class="flex items-center justify-between py-2.5">
                            <dt class="text-slate-500 dark:text-gray-400">Jours restants</dt>
                            <dd class="font-bold {{ $joursRestants > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-gray-400' }}">
                                @if ($joursRestants > 0)
                                    {{ $joursRestants }} jour{{ $joursRestants > 1 ? 's' : '' }}
                                @elseif ($encoreDansPeriode)
                                    Dernier jour
                                @else
                                    Terminé
                                @endif
                            </dd>
                        </div>
                    </dl>
                    
                    <div class="mt-4 border-t border-slate-100 dark:border-gray-700/80 pt-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400">Progression globale</span>
                            <span class="font-bold text-slate-800 dark:text-gray-200">{{ $progressionStage }}%</span>
                        </div>
                        <div class="mt-2 h-2 w-full rounded-full bg-slate-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-700 transition-all duration-700" style="width: {{ $progressionStage }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500 dark:text-gray-400">
                            @if ($dureeTotale > 0)
                                Jour {{ min($joursEcoules + 1, $dureeTotale + 1) }} sur {{ $dureeTotale + 1 }} · {{ $progressionStage }}%
                            @else
                                En attente des dates de stage
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Rapport de fin de stage PDF --}}
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-slate-200/80 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Rapport de fin de stage</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">Dépose ici ton rapport de fin de stage final au format PDF.</p>

                    @if($activeStage->final_report_path)
                        <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 p-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex-shrink-0 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-emerald-600 dark:text-emerald-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs sm:text-sm font-bold text-emerald-800 dark:text-emerald-200">{{ $activeStage->final_report_name }}</p>
                                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400">
                                        Déposé le {{ $activeStage->final_report_uploaded_at?->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                                <a href="{{ $activeStage->final_report_url }}" target="_blank" class="inline-flex flex-shrink-0 items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition-colors">
                                    Voir
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/40 p-3.5">
                            <p class="text-xs font-bold text-amber-800 dark:text-amber-200">Aucun rapport déposé pour le moment</p>
                            <p class="mt-0.5 text-[11px] text-amber-700 dark:text-amber-300">Ton rapport final n'est pas encore transmis à l'administration.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.final_report.upload') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-900/50 px-4 py-5 text-center transition-colors hover:border-indigo-400 dark:hover:border-indigo-500">
                            <svg class="w-7 h-7 text-slate-400 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-xs font-semibold text-slate-600 dark:text-gray-300">Choisir un fichier PDF…</span>
                            <input type="file" name="final_report" accept=".pdf,application/pdf" class="sr-only" @change="this.previousElementSibling.textContent = this.files[0] ? this.files[0].name : 'Choisir un fichier PDF…'">
                        </label>
                        @error('final_report')
                            <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            {{ $activeStage->final_report_path ? 'Remplacer le rapport PDF' : 'Déposer le rapport PDF' }}
                        </button>
                    </form>
                </div>

                {{-- Rappels utiles --}}
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-slate-200/80 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Rappels utiles</h2>
                    <ul class="mt-3 space-y-2.5 text-xs text-slate-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500 shrink-0"></span>
                            <span>Pointe d'abord ta présence le matin, puis remplis ton rapport en fin de journée.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500 shrink-0"></span>
                            <span>Si ton site ou superviseur est incorrect, signale-le immédiatement à l'administration.</span>
                        </li>
                    </ul>

                    <div class="mt-6 grid gap-2.5">
                        <a href="{{ route('presence.pointage') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            Aller au pointage
                        </a>
                        <a href="{{ route('tasks.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-gray-200 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors">
                            Aller au rapport journalier
                        </a>
                    </div>
                </div>

            </div>
        </div>
        @endif
    </div>
</x-app-layout>