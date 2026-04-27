<x-app-layout title="Demande de permission">
    @php
        $approver = $permissionContext['approver'] ?? null;
        $stage = $permissionContext['stage'] ?? null;
        $domaine = $permissionContext['domaine'] ?? null;
        $isStudent = auth()->user()->hasRole('etudiant');
        $statusClasses = [
            'slate' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
            'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-200',
            'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-200',
            'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200',
            'teal' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/15 dark:text-teal-200',
            'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-200',
        ];
        $contextName = $stage?->theme ?? $domaine?->nom ?? 'Contexte a confirmer';
        $locationName = $isStudent
            ? ($stage?->site?->name ?? 'Site de stage')
            : ($domaine?->nom ?? 'Domaine employe');
    @endphp

    <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <section class="app-panel-strong overflow-hidden">
            <div class="border-b border-[var(--app-border)] px-6 py-6 sm:px-8">
                <p class="app-section-title">Demande de permission</p>
                <div class="mt-3 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl font-semibold tracking-tight text-[var(--app-text)]">Formulaire rapide</h2>
                        <p class="mt-2 text-sm leading-6 text-[var(--app-text-muted)]">
                            Renseignez l'essentiel. La demande partira ensuite au superviseur et restera visible par l'administration.
                        </p>
                    </div>

                    <div class="inline-flex rounded-full border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--app-text-muted)]">
                        {{ $isStudent ? 'Stagiaire' : 'Employe' }}
                    </div>
                </div>
            </div>

            <div class="border-b border-[var(--app-border)] px-6 py-5 sm:px-8">
                <div class="grid gap-4 text-sm sm:grid-cols-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Contexte</p>
                        <p class="mt-2 font-semibold text-[var(--app-text)]">{{ $contextName }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Lieu</p>
                        <p class="mt-2 font-semibold text-[var(--app-text)]">{{ $locationName }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Validateur</p>
                        <p class="mt-2 font-semibold text-[var(--app-text)]">{{ $approver?->name ?? 'Administration' }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('permission-requests.store') }}" method="POST" class="px-6 py-6 sm:px-8">
                @csrf

                <div class="grid gap-x-5 gap-y-5 xl:grid-cols-2">
                    <div>
                        <label for="type" class="app-form-label">Type</label>
                        <select id="type" name="type" class="app-form-control" required>
                            <option value="">Choisir un type</option>
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="request_date" class="app-form-label">Date</label>
                        <input
                            id="request_date"
                            type="date"
                            name="request_date"
                            value="{{ old('request_date', now()->toDateString()) }}"
                            class="app-form-control"
                            required
                        >
                        @error('request_date')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_time" class="app-form-label">Debut</label>
                        <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" class="app-form-control">
                        @error('start_time')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_time" class="app-form-label">Fin</label>
                        <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" class="app-form-control">
                        @error('end_time')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="xl:col-span-2">
                        <label for="reason" class="app-form-label">Motif</label>
                        <input
                            id="reason"
                            type="text"
                            name="reason"
                            value="{{ old('reason') }}"
                            class="app-form-control"
                            placeholder="Ex: Consultation medicale, urgence familiale, rendez-vous administratif"
                            required
                        >
                        @error('reason')
                            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="xl:col-span-2">
                        <details class="group border-t border-[var(--app-border)] pt-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-[var(--app-text)]">
                                <span>Ajouter un detail</span>
                                <span class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)] transition group-open:opacity-0">Optionnel</span>
                            </summary>

                            <div class="mt-4">
                                <textarea
                                    id="details"
                                    name="details"
                                    rows="3"
                                    class="app-form-control min-h-[110px] resize-y"
                                    placeholder="Informations complementaires utiles a la validation."
                                >{{ old('details') }}</textarea>
                                @error('details')
                                    <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </details>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3 border-t border-[var(--app-border)] pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-[var(--app-text-muted)]">
                        Brouillon prive ou envoi direct au superviseur.
                    </p>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="submit"
                            name="intent"
                            value="draft"
                            class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface)] px-5 py-3 text-sm font-semibold text-[var(--app-text)] transition hover:border-[var(--app-border-strong)]"
                        >
                            Enregistrer
                        </button>
                        <button
                            type="submit"
                            name="intent"
                            value="submit"
                            class="rounded-2xl bg-[var(--app-text)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                        >
                            Soumettre
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <aside class="app-panel overflow-hidden">
            <div class="border-b border-[var(--app-border)] px-6 py-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="app-section-title">Suivi</p>
                        <h3 class="mt-3 text-2xl font-semibold text-[var(--app-text)]">Mes demandes</h3>
                    </div>

                    <div class="text-right">
                        <p class="text-2xl font-semibold text-[var(--app-text)]">{{ $permissionRequests->total() }}</p>
                        <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">dossiers</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4">
                @forelse($permissionRequests as $permissionRequest)
                    <article class="@if(!$loop->last) border-b border-[var(--app-border)] pb-4 @endif @if(!$loop->first) pt-4 @endif">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$permissionRequest->status_tone] ?? $statusClasses['slate'] }}">
                                        {{ $permissionRequest->status_label }}
                                    </span>
                                    <span class="text-xs font-medium text-[var(--app-text-muted)]">{{ $permissionRequest->request_date?->format('d/m/Y') }}</span>
                                </div>

                                <h4 class="mt-3 truncate text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->reason }}</h4>
                                <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ $permissionRequest->type_label }}</p>
                            </div>

                            <a href="{{ route('permission-requests.show', $permissionRequest) }}" class="shrink-0 text-sm font-semibold text-[var(--app-text)] underline-offset-4 hover:underline">
                                Ouvrir
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-base font-semibold text-[var(--app-text)]">Aucune demande</p>
                        <p class="mt-2 text-sm text-[var(--app-text-muted)]">Vos prochains dossiers apparaitront ici.</p>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-[var(--app-border)] px-6 py-4">
                {{ $permissionRequests->links() }}
            </div>
        </aside>
    </div>
</x-app-layout>
