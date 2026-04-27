<x-app-layout title="Mon stage">
    <div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
        <div class="rounded-[2rem] bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-6 py-7 text-white shadow-xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm font-medium uppercase tracking-[0.28em] text-cyan-200/80">Espace stagiaire</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight">Mon stage</h1>
                    <p class="mt-3 text-sm text-slate-300">
                        Une vue claire pour suivre la présence, les tâches du jour et l'état du rapport sans passer par tout le back-office.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('presence.pointage') }}" class="inline-flex items-center rounded-2xl bg-white px-4 py-2.5 text-sm font-medium text-slate-900 shadow-sm hover:bg-slate-100">
                        Pointer ma présence
                    </a>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-2xl border border-white/20 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10">
                        Ouvrir le rapport du jour
                    </a>
                </div>
            </div>
        </div>

        @if (! $activeStage)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-5 text-amber-800">
                Aucun stage actif n'est rattaché à ton compte pour aujourd'hui. Vérifie avec l'administration si le stage n'a pas encore été affecté ou activé.
            </div>
        @else
            <div class="grid gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2 dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Thème du stage</p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $activeStage->theme ?: 'Stage sans thème' }}</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900/60">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Site</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $activeStage->site?->name ?: 'Site non défini' }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $activeStage->site?->city ?: 'Ville non définie' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900/60">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Superviseur</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $activeStage->supervisor?->name ?: 'Aucun superviseur' }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $activeStage->supervisor?->email ?: 'Email non défini' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Présence du jour</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $attendanceDay?->day_status ?: 'pending' }}</p>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        Arrivée: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}</span>
                    </p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Départ: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }}</span>
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Rapport du jour</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $todayReport?->status ?: 'draft' }}</p>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        Mise à jour:
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $todayReport?->updated_at?->format('d/m/Y H:i') ?: 'Aucune' }}</span>
                    </p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Progression:
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $todayReport?->completion_rate ?? 0 }}%</span>
                    </p>
                </div>
            </div>

            <div class="grid gap-4 xl:grid-cols-[1.6fr_1fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Tâches du stage</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Suivi rapide de ce qui est en cours et de ce qui est déjà bouclé.</p>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <span class="rounded-full bg-emerald-50 px-3 py-1 font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $completedTasksCount }} terminée(s)</span>
                            <span class="rounded-full bg-amber-50 px-3 py-1 font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">{{ $openTasksCount }} à suivre</span>
                        </div>
                    </div>

                    @if ($tasks->isEmpty())
                        <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-5 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                            Aucune tâche n'est encore rattachée à ce stage. Tu peux déjà utiliser le rapport journalier pour décrire clairement ce que tu fais chaque jour.
                        </div>
                    @else
                        <div class="mt-5 space-y-4">
                            @foreach ($tasks as $task)
                                <div class="rounded-2xl border border-slate-200 p-5 dark:border-slate-700">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                        <div class="max-w-2xl">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $task->title }}</h3>
                                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $task->status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-300' }}">
                                                    {{ $task->status }}
                                                </span>
                                            </div>
                                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $task->description ?: 'Aucune description fournie.' }}</p>
                                        </div>

                                        <div class="grid gap-2 text-sm text-slate-500 dark:text-slate-400 sm:grid-cols-2 md:text-right">
                                            <p>Priorité: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $task->priority }}</span></p>
                                            <p>Échéance: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $task->due_date?->format('d/m/Y') ?: 'Non définie' }}</span></p>
                                            <p>Progression: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $task->last_progress_percent }}%</span></p>
                                            <p>Démarré: <span class="font-medium text-slate-800 dark:text-slate-100">{{ $task->started_at?->format('d/m/Y') ?: '--' }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Cadre du stage</h2>
                        <dl class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center justify-between gap-3">
                                <dt>Période</dt>
                                <dd class="font-medium text-slate-900 dark:text-white">
                                    {{ $activeStage->date_debut?->format('d/m/Y') }} - {{ $activeStage->date_fin?->format('d/m/Y') }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Service</dt>
                                <dd class="font-medium text-slate-900 dark:text-white">{{ $activeStage->service?->nom_service ?: 'Non défini' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Type</dt>
                                <dd class="font-medium text-slate-900 dark:text-white">{{ $activeStage->typestage?->nom_type_stage ?: 'Non défini' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Horaire attendu</dt>
                                <dd class="font-medium text-slate-900 dark:text-white">
                                    {{ $activeStage->expected_check_in_time ?: '--:--' }} - {{ $activeStage->expected_check_out_time ?: '--:--' }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Mode présence</dt>
                                <dd class="font-medium text-slate-900 dark:text-white">{{ $activeStage->presence_mode ?: 'geolocalisee' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Rappels utiles</h2>
                        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <p>Pointe d'abord ta présence, puis remplis ton rapport avec les tâches travaillées et les blocages du jour.</p>
                            <p>Si ton site ou ton superviseur n'est pas correct, signale-le à l'administration avant de continuer.</p>
                        </div>

                        <div class="mt-5 grid gap-3">
                            <a href="{{ route('presence.pointage') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-medium text-white hover:bg-slate-800">
                                Aller au pointage
                            </a>
                            <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900">
                                Aller au rapport journalier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
