<x-app-layout title="Validation des permissions">
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
            <div class="grid gap-4 px-6 py-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <div>
                    <p class="app-section-title">Validation metier</p>
                    <h2 class="mt-3 text-3xl font-semibold text-[var(--app-text)]">Demandes de permission en attente</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-[var(--app-text-muted)]">
                        Cette file centralise les demandes a approuver ou refuser avant la diffusion du PDF vers les signataires institutionnels.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="rounded-[1.35rem] border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Dossiers affiches</p>
                        <p class="mt-2 text-2xl font-semibold text-[var(--app-text)]">{{ $permissionRequests->total() }}</p>
                    </div>
                    <div class="rounded-[1.35rem] border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Signataires actifs</p>
                        <p class="mt-2 text-2xl font-semibold text-[var(--app-text)]">{{ $activeSignatairesCount }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            @forelse($permissionRequests as $permissionRequest)
                <article class="app-panel overflow-hidden">
                    <div class="flex flex-col gap-5 border-b border-[var(--app-border)] px-6 py-5 xl:flex-row xl:items-start xl:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$permissionRequest->status_tone] ?? $statusClasses['slate'] }}">
                                    {{ $permissionRequest->status_label }}
                                </span>
                                <span class="app-chip">{{ $permissionRequest->type_label }}</span>
                                <span class="app-chip">{{ $permissionRequest->request_date?->format('d/m/Y') }}</span>
                            </div>

                            <div>
                                <h3 class="text-xl font-semibold text-[var(--app-text)]">{{ $permissionRequest->requester->name }}</h3>
                                <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                                    {{ $permissionRequest->requester->email }} · {{ $permissionRequest->requester->domaine?->nom ?? 'Sans domaine' }}
                                </p>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Motif</p>
                                    <p class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->reason }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Contexte</p>
                                    <p class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->stage?->theme ?? $permissionRequest->domaine?->nom ?? 'Non defini' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Debut</p>
                                    <p class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->starts_at?->format('d/m/Y H:i') ?? 'Non precise' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Fin</p>
                                    <p class="mt-1 text-sm font-semibold text-[var(--app-text)]">{{ $permissionRequest->ends_at?->format('d/m/Y H:i') ?? 'Non precisee' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row xl:flex-col">
                            <a href="{{ route('permission-requests.show', $permissionRequest) }}" class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-3 text-center text-sm font-semibold text-[var(--app-text)]">
                                Ouvrir le dossier
                            </a>
                            <a href="{{ route('permission-requests.pdf', $permissionRequest) }}" class="rounded-2xl bg-[var(--app-text)] px-4 py-3 text-center text-sm font-semibold text-white">
                                PDF
                            </a>
                        </div>
                    </div>

                    <div class="grid gap-5 px-6 py-5 lg:grid-cols-[minmax(0,1fr)_23rem]">
                        <div>
                            <p class="text-sm leading-6 text-[var(--app-text-soft)]">
                                {{ $permissionRequest->details ?: "Aucun detail complementaire n'a ete renseigne pour ce dossier." }}
                            </p>

                            @if($permissionRequest->first_review_notes)
                                <div class="mt-4 rounded-[1.25rem] border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Derniere note</p>
                                    <p class="mt-2 text-sm text-[var(--app-text-soft)]">{{ $permissionRequest->first_review_notes }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            @can('actOnPermissionRequest', $permissionRequest)
                                <form action="{{ route('permission-requests.review.approve', $permissionRequest) }}" method="POST" class="rounded-[1.35rem] border border-emerald-200/40 bg-emerald-500/5 p-4 dark:border-emerald-400/20">
                                    @csrf
                                    <label class="mb-2 block text-sm font-semibold text-[var(--app-text)]" for="approve-notes-{{ $permissionRequest->id }}">Note de validation</label>
                                    <textarea id="approve-notes-{{ $permissionRequest->id }}" name="notes" rows="3" class="w-full rounded-2xl border px-4 py-3" placeholder="Message optionnel a conserver dans le dossier."></textarea>
                                    <button type="submit" class="mt-3 w-full rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 px-4 py-3 text-sm font-semibold text-white">
                                        Approuver et diffuser
                                    </button>
                                </form>

                                <form action="{{ route('permission-requests.review.reject', $permissionRequest) }}" method="POST" class="rounded-[1.35rem] border border-rose-200/40 bg-rose-500/5 p-4 dark:border-rose-400/20">
                                    @csrf
                                    <label class="mb-2 block text-sm font-semibold text-[var(--app-text)]" for="reject-notes-{{ $permissionRequest->id }}">Motif de refus</label>
                                    <textarea id="reject-notes-{{ $permissionRequest->id }}" name="notes" rows="3" class="w-full rounded-2xl border px-4 py-3" placeholder="Expliquez brievement la raison du refus."></textarea>
                                    <button type="submit" class="mt-3 w-full rounded-2xl bg-gradient-to-r from-rose-500 to-orange-500 px-4 py-3 text-sm font-semibold text-white">
                                        Refuser la demande
                                    </button>
                                </form>
                            @else
                                <div class="rounded-[1.35rem] border border-[var(--app-border)] bg-[var(--app-surface)] px-4 py-4">
                                    @php
                                        $isWaitingForSupervisor = $permissionRequest->status === 'under_review'
                                            && !$permissionRequest->reviewed_by_id
                                            && $permissionRequest->firstApprover;
                                    @endphp

                                    <p class="text-sm font-semibold text-[var(--app-text)]">
                                        {{ $isWaitingForSupervisor ? 'Consultation uniquement' : 'Dossier deja traite' }}
                                    </p>
                                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">
                                        {{ $isWaitingForSupervisor
                                            ? "La validation de premier niveau est reservee a {$permissionRequest->firstApprover->name}."
                                            : ($permissionRequest->reviewer?->name ? "Traite par {$permissionRequest->reviewer->name}." : "Ce dossier n'est plus actionnable.") }}
                                    </p>
                                </div>
                            @endcan
                        </div>
                    </div>
                </article>
            @empty
                <div class="app-panel px-6 py-12 text-center">
                    <p class="text-lg font-semibold text-[var(--app-text)]">Aucune demande a traiter</p>
                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">Les nouvelles demandes apparaitront ici des leur soumission.</p>
                </div>
            @endforelse
        </section>

        <div class="app-panel px-6 py-4">
            {{ $permissionRequests->links() }}
        </div>
    </div>
</x-app-layout>
