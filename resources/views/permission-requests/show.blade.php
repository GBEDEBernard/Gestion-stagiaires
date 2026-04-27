<x-app-layout title="Dossier de permission">
    @php
        $statusClasses = [
            'slate' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
            'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-200',
            'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-200',
            'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-200',
            'teal' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/15 dark:text-teal-200',
            'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-200',
        ];
    @endphp

    <div class="space-y-6">
        <section class="app-panel-strong overflow-hidden">
            <div class="flex flex-col gap-5 border-b border-[var(--app-border)] px-6 py-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="app-section-title">Dossier #{{ $permissionRequest->id }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-[var(--app-text)]">{{ $permissionRequest->reason }}</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-[var(--app-text-muted)]">
                        {{ $permissionRequest->details ?: "Le dossier ne contient pas encore de details complementaires." }}
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                    <a href="{{ route('permission-requests.index') }}" class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-3 text-center text-sm font-semibold text-[var(--app-text)]">
                        Retour a la liste
                    </a>
                    <a href="{{ route('permission-requests.pdf', $permissionRequest) }}" class="rounded-2xl bg-[var(--app-text)] px-4 py-3 text-center text-sm font-semibold text-white">
                        Telecharger le PDF
                    </a>
                </div>
            </div>

            <div class="grid gap-4 px-6 py-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="app-metric-card">
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Statut</p>
                    <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$permissionRequest->status_tone] ?? $statusClasses['slate'] }}">
                        {{ $permissionRequest->status_label }}
                    </span>
                </div>
                <div class="app-metric-card">
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Type</p>
                    <p class="mt-3 text-lg font-semibold text-[var(--app-text)]">{{ $permissionRequest->type_label }}</p>
                </div>
                <div class="app-metric-card">
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Validateur metier</p>
                    <p class="mt-3 text-lg font-semibold text-[var(--app-text)]">{{ $permissionRequest->firstApprover?->name ?? 'Diffusion directe' }}</p>
                </div>
                <div class="app-metric-card">
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Etat du PDF</p>
                    <p class="mt-3 text-lg font-semibold text-[var(--app-text)]">{{ $permissionRequest->pdf_generated_at ? 'Genere' : 'A generer' }}</p>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
            <section class="app-panel overflow-hidden">
                <div class="border-b border-[var(--app-border)] px-6 py-5">
                    <h3 class="text-lg font-semibold text-[var(--app-text)]">Chronologie de validation</h3>
                </div>

                <div class="space-y-5 px-6 py-6">
                    <div class="flex gap-4">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-teal-500/15 text-teal-500">
                            <i data-lucide="file-pen-line" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--app-text)]">Creation du dossier</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ $permissionRequest->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-500/15 text-blue-500">
                            <i data-lucide="send" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--app-text)]">Soumission</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ $permissionRequest->submitted_at?->format('d/m/Y H:i') ?? 'Brouillon non envoye' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-500">
                            <i data-lucide="user-check" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--app-text)]">Validation metier</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                                {{ $permissionRequest->first_reviewed_at?->format('d/m/Y H:i') ?? 'En attente de traitement' }}
                            </p>
                            @if($permissionRequest->first_review_notes)
                                <p class="mt-2 rounded-2xl bg-[var(--app-surface)] px-4 py-3 text-sm text-[var(--app-text-soft)]">
                                    {{ $permissionRequest->first_review_notes }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-500">
                            <i data-lucide="mail-check" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--app-text)]">Diffusion officielle</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ $permissionRequest->sent_at?->format('d/m/Y H:i') ?? 'Non encore diffusee' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <section class="app-panel p-6">
                    <h3 class="text-lg font-semibold text-[var(--app-text)]">Informations cles</h3>
                    <dl class="mt-5 space-y-4">
                        <div>
                            <dt class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Date concernee</dt>
                            <dd class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->request_date?->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Debut</dt>
                            <dd class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->starts_at?->format('d/m/Y H:i') ?? 'Non precise' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Fin</dt>
                            <dd class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->ends_at?->format('d/m/Y H:i') ?? 'Non precisee' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Contexte</dt>
                            <dd class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->stage?->theme ?? $permissionRequest->domaine?->nom ?? 'Non defini' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="app-panel p-6">
                    <h3 class="text-lg font-semibold text-[var(--app-text)]">Signataires notifies</h3>
                    <div class="mt-4 space-y-3">
                        @forelse(($permissionRequest->signataires_snapshot ?? []) as $signataire)
                            <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-3">
                                <p class="text-sm font-semibold text-[var(--app-text)]">{{ $signataire['nom'] }}</p>
                                <p class="mt-1 text-xs text-[var(--app-text-muted)]">{{ $signataire['poste'] }} · {{ $signataire['email'] }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-[var(--app-text-muted)]">La liste apparaitra des que la demande sera diffusee.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
