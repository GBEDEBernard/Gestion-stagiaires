<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <div class="relative overflow-hidden bg-gradient-to-br from-sky-600 via-cyan-600 to-indigo-700 rounded-2xl">
            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.25),_transparent_40%)]"></div>
            <div class="relative px-8 py-10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">Tableau de bord Employé</h1>
                        <p class="text-cyan-100 text-lg">Vos rapports et votre pointage en un coup d'œil.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/10 self-start">
                        <div class="flex items-center gap-2 text-white">
                            <svg class="w-5 h-5 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">{{ now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pointage aujourd'hui</h2>
                <p class="mt-4 text-3xl font-bold text-slate-900 dark:text-white">{{ $todayAttendance ? 'Enregistré' : 'Aucun pointage' }}</p>
                @if($todayAttendance)
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Arrivée : {{ $todayAttendance->first_check_in_at?->format('H:i') ?? '--' }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Départ : {{ $todayAttendance->last_check_out_at?->format('H:i') ?? '--' }}</p>
                @else
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Votre pointage n'a pas encore été enregistré aujourd'hui.</p>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Rapports</h2>
                <p class="mt-4 text-3xl font-bold text-slate-900 dark:text-white">{{ $attendanceEventsThisWeek }}</p>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Événements de pointage cette semaine</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Suivi hebdomadaire</h2>
                <p class="mt-4 text-3xl font-bold text-slate-900 dark:text-white">{{ $daysPresentThisWeek }}/{{ $daysTrackedThisWeek }}</p>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Jours où le pointage a été validé cette semaine</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Votre domaine</h3>
                <p class="mt-3 text-slate-600 dark:text-slate-300">{{ $user->domaine?->nom ?? 'Non défini' }}</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Utilisez ce tableau de bord pour accéder rapidement à votre pointage et à votre présence.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Actions rapides</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('presence.pointage') }}" class="block rounded-2xl bg-slate-900 text-white px-4 py-3 hover:bg-slate-800 transition">Voir le pointage</a>
                    <a href="{{ route('presence.historique') }}" class="block rounded-2xl border border-slate-200 dark:border-gray-700 text-slate-900 dark:text-white px-4 py-3 hover:bg-slate-50 dark:hover:bg-gray-900 transition">Historique des pointages</a>
                </div>
            </div>
        </div>

        {{-- ==================== MES TÂCHES ==================== --}}
        <div class="mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Mes tâches</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Les tâches que vous avez créées ou qui vous ont été assignées, avec vos rapports déposés.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300 ring-1 ring-inset ring-indigo-600/20">
                            {{ $myTasksCreated }} créée(s)
                        </span>
                        <span class="inline-flex items-center rounded-full bg-sky-50 dark:bg-sky-900/30 px-3 py-1 text-xs font-medium text-sky-700 dark:text-sky-300 ring-1 ring-inset ring-sky-600/20">
                            {{ $myTasksAssigned }} assignée(s)
                        </span>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20">
                            {{ $myTasksCompleted }} terminée(s)
                        </span>
                    </div>
                </div>

                @if ($myTasks->isEmpty())
                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 dark:border-gray-600 bg-slate-50/50 dark:bg-gray-900/40 px-6 py-10 text-center">
                    <p class="text-sm text-slate-600 dark:text-slate-300">Aucune tâche pour le moment.</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Créez une tâche ou consultez l'espace de travail pour commencer.</p>
                    <a href="{{ route('tasks.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-5 py-2.5 text-sm font-medium hover:bg-slate-800 dark:hover:bg-slate-100 transition">
                        Ouvrir l'espace de travail
                    </a>
                </div>
                @else
                <div class="mt-6 space-y-3">
                    @foreach ($myTasks as $task)
                    @php
                        $myReports = $task->dailyReports->where('user_id', $user->id)->take(3);
                        $statusMeta = match ($task->status) {
                            'completed' => ['Terminée', 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 ring-emerald-600/20'],
                            'awaiting_validation' => ['En attente validation', 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 ring-amber-600/20'],
                            'blocked' => ['Bloquée', 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 ring-red-600/20'],
                            'in_progress' => ['En cours', 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ring-blue-600/20'],
                            'changes_requested' => ['Corrections demandées', 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 ring-amber-600/20'],
                            default => ['À faire', 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 ring-slate-600/20'],
                        };
                        $priorityMeta = match ($task->priority) {
                            'urgent' => ['Urgente', '#dc2626'],
                            'high' => ['Haute', '#d97706'],
                            'low' => ['Basse', '#64748b'],
                            default => ['Normale', '#6366f1'],
                        };
                    @endphp
                    <a href="{{ encrypted_route('tasks.show', $task) }}"
                       class="group block rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-900/40 p-5 hover:bg-white dark:hover:bg-gray-800 hover:border-slate-300 dark:hover:border-gray-600 transition-all">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $task->title }}</h3>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background:{{ $priorityMeta[1] }}15; color:{{ $priorityMeta[1] }};">{{ $priorityMeta[0] }}</span>
                                    @if ($task->owner_id === $user->id)
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300">Créée par moi</span>
                                    @else
                                    <span class="inline-flex items-center rounded-full bg-sky-50 dark:bg-sky-900/30 px-2.5 py-0.5 text-xs font-medium text-sky-700 dark:text-sky-300">Assignée à moi</span>
                                    @endif
                                </div>

                                @if ($task->description)
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 line-clamp-2">{{ $task->description }}</p>
                                @endif

                                <div class="mt-3 flex max-w-md items-center gap-2">
                                    <div class="h-1.5 flex-1 rounded-full bg-slate-200 dark:bg-gray-700">
                                        <div class="h-1.5 rounded-full {{ $task->status === 'completed' ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $task->last_progress_percent }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $task->last_progress_percent }}%</span>
                                </div>

                                @if ($myReports->isNotEmpty())
                                <div class="mt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Vos rapports déposés</p>
                                    <div class="mt-2 space-y-1.5">
                                        @foreach ($myReports as $report)
                                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                            <svg class="h-3.5 w-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $report->report_date->isoFormat('DD MMM') }}</span>
                                            <span>·</span>
                                            <span>{{ $report->task_progress_percent ?? 0 }}%</span>
                                            <span>·</span>
                                            <span>{{ str()->limit($report->summary ?: 'Rapport déposé', 60) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="flex flex-col gap-1 text-xs text-slate-500 dark:text-slate-400 lg:text-right shrink-0">
                                <div>Échéance : <span class="font-medium text-slate-800 dark:text-slate-200">{{ $task->due_date?->format('d/m/Y') ?: 'Non définie' }}</span></div>
                                <div>Démarré le : <span class="font-medium text-slate-800 dark:text-slate-200">{{ $task->started_at?->format('d/m/Y') ?: '--' }}</span></div>
                                <div class="mt-2 inline-flex items-center gap-1 font-medium text-indigo-600 dark:text-indigo-400">
                                    Ouvrir dans l'espace de travail
                                    <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
</x-app-layout>