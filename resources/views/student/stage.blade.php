<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
        <div class="rounded-[2rem] bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-6 py-7 text-white shadow-xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm font-medium uppercase tracking-[0.28em] text-cyan-200/80">Espace stagiaire</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight">Mon stage</h1>
                    <p class="mt-3 text-sm text-slate-300">
                        Une vue claire pour suivre la presence, les taches du jour et l'etat du rapport sans passer par tout le back-office.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('presence.pointage') }}" class="inline-flex items-center rounded-2xl bg-white px-4 py-2.5 text-sm font-medium text-slate-900 shadow-sm hover:bg-slate-100">
                        Pointer ma presence
                    </a>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-2xl border border-white/20 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10">
                        Ouvrir le rapport du jour
                    </a>
                </div>
            </div>
        </div>

        @if (! $activeStage)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-5 text-amber-800">
            Aucun stage actif n'est rattache a ton compte pour aujourd'hui. Verifie avec l'administration si le stage n'a pas encore ete affecte ou active.
        </div>
        @else
        <div class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <p class="text-sm text-slate-500">Theme du stage</p>
                <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $activeStage->theme ?: 'Stage sans theme' }}</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Site</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $activeStage->site?->name ?: 'Site non defini' }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $activeStage->site?->city ?: 'Ville non definie' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Superviseur</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $activeStage->supervisor?->name ?: 'Aucun superviseur' }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $activeStage->supervisor?->email ?: 'Email non defini' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Presence du jour</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $attendanceDay?->day_status ?: 'pending' }}</p>
                <p class="mt-3 text-sm text-slate-500">
                    Arrivee: <span class="font-medium text-slate-800">{{ $attendanceDay?->first_check_in_at?->format('H:i') ?: '--:--' }}</span>
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Depart: <span class="font-medium text-slate-800">{{ $attendanceDay?->last_check_out_at?->format('H:i') ?: '--:--' }}</span>
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Rapport du jour</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $todayReport?->status ?: 'draft' }}</p>
                <p class="mt-3 text-sm text-slate-500">
                    Mise a jour:
                    <span class="font-medium text-slate-800">{{ $todayReport?->updated_at?->format('d/m/Y H:i') ?: 'Aucune' }}</span>
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Progression:
                    <span class="font-medium text-slate-800">{{ $todayReport?->completion_rate ?? 0 }}%</span>
                </p>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-[1.6fr_1fr]">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Taches du stage</h2>
                        <p class="text-sm text-slate-500">Suivi rapide de ce qui est en cours et de ce qui est deja boucle.</p>
                    </div>

                    <div class="flex items-center gap-2 text-sm">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 font-medium text-emerald-700">{{ $completedTasksCount }} terminee(s)</span>
                        <span class="rounded-full bg-amber-50 px-3 py-1 font-medium text-amber-700">{{ $openTasksCount }} a suivre</span>
                    </div>
                </div>

                @if ($tasks->isEmpty())
                <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-5 text-sm text-slate-600">
                    Aucune tache n'est encore rattachee a ce stage. Tu peux deja utiliser le rapport journalier pour decrire clairement ce que tu fais chaque jour.
                </div>
                @else
                <div class="mt-5 space-y-4">
                    @foreach ($tasks as $task)
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div class="max-w-2xl">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-semibold text-slate-900">{{ $task->title }}</h3>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium
                                                    {{ $task->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $task->status }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-slate-500">{{ $task->description ?: 'Aucune description fournie.' }}</p>
                            </div>

                            <div class="grid gap-2 text-sm text-slate-500 sm:grid-cols-2 md:text-right">
                                <p>Priorite: <span class="font-medium text-slate-800">{{ $task->priority }}</span></p>
                                <p>Echeance: <span class="font-medium text-slate-800">{{ $task->due_date?->format('d/m/Y') ?: 'Non definie' }}</span></p>
                                <p>Progression: <span class="font-medium text-slate-800">{{ $task->last_progress_percent }}%</span></p>
                                <p>Demarre: <span class="font-medium text-slate-800">{{ $task->started_at?->format('d/m/Y') ?: '--' }}</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Cadre du stage</h2>
                    <dl class="mt-4 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between gap-3">
                            <dt>Periode</dt>
                            <dd class="font-medium text-slate-900">
                                {{ $activeStage->date_debut?->format('d/m/Y') }} - {{ $activeStage->date_fin?->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt>Service</dt>
                            <dd class="font-medium text-slate-900">{{ $activeStage->service?->nom_service ?: 'Non defini' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt>Type</dt>
                            <dd class="font-medium text-slate-900">{{ $activeStage->typestage?->nom_type_stage ?: 'Non defini' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt>Horaire attendu</dt>
                            <dd class="font-medium text-slate-900">
                                {{ $activeStage->expected_check_in_time ?: '--:--' }} - {{ $activeStage->expected_check_out_time ?: '--:--' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt>Mode presence</dt>
                            <dd class="font-medium text-slate-900">{{ $activeStage->presence_mode ?: 'geolocalisee' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Rappels utiles</h2>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        {{-- jb -> Cette carte garde l'espace stagiaire simple:
                            une consigne rapide, puis un appel a l'action direct. --}}
                        <p>Pointe d'abord ta presence, puis remplis ton rapport avec les taches travaillees et les blocages du jour.</p>
                        <p>Si ton site ou ton superviseur n'est pas correct, signale-le a l'administration avant de continuer.</p>
                    </div>

                    <div class="mt-5 grid gap-3">
                        <a href="{{ route('presence.pointage') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-medium text-white hover:bg-slate-800">
                            Aller au pointage
                        </a>
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Aller au rapport journalier
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>