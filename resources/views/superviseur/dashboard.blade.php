<x-app-layout title="Dashboard superviseur">
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-600">Supervision</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Dashboard superviseur</h1>
                <p class="mt-1 text-slate-500">Suivi des stages actifs, des pointages du jour et des rapports a relire.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('presence.pointage') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Mon pointage
                </a>
                <a href="{{ route('admin.reports.index') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Rapports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Stages actifs</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $summary['active_stages'] }}</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Arrivees du jour</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $summary['checked_in_today'] }}</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Jours termines</p>
                <p class="mt-3 text-3xl font-bold text-blue-600">{{ $summary['completed_days'] }}</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm uppercase text-slate-500">Rapports a relire</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $summary['pending_reviews'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1.5fr,1fr]">
            <section class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Stages supervises aujourd'hui</h2>
                    <p class="mt-1 text-sm text-slate-500">Etat du pointage et du rapport quotidien pour chaque stagiaire actif.</p>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse($supervisedStages as $stage)
                        @php
                            $day = $stage->attendanceDays->first();
                            $report = $stage->dailyReports->first();
                        @endphp

                        <div class="px-6 py-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">
                                        {{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $stage->service?->nom ?? 'Service non assigne' }} · {{ $stage->site?->name ?? 'Site non assigne' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Stage jusqu'au {{ $stage->date_fin?->format('d/m/Y') ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                        <div class="text-slate-500">Arrivee</div>
                                        <div class="mt-1 font-semibold text-slate-900">{{ $day?->first_check_in_at?->format('H:i') ?? 'En attente' }}</div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                        <div class="text-slate-500">Depart</div>
                                        <div class="mt-1 font-semibold text-slate-900">{{ $day?->last_check_out_at?->format('H:i') ?? 'En cours' }}</div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                        <div class="text-slate-500">Rapport</div>
                                        <div class="mt-1 font-semibold {{ $report?->status === 'submitted' ? 'text-amber-600' : ($report?->status === 'approved' ? 'text-emerald-600' : 'text-slate-900') }}">
                                            {{ $report?->status ? ucfirst(str_replace('_', ' ', $report->status)) : 'Aucun' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <a
                                    href="{{ encrypted_route('stages.show', $stage) }}"
                                    class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    Ouvrir le stage
                                </a>
                                <a
                                    href="{{ route('admin.reports.index', ['period' => 'daily', 'date' => now()->format('Y-m-d')]) }}"
                                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                                >
                                    Voir les rapports
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-slate-500">
                            Aucun stage actif ne vous est actuellement assigne.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                        <h2 class="text-lg font-semibold text-slate-900">Rapports en attente</h2>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse($pendingReviews as $report)
                            <div class="px-6 py-5 space-y-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $report->stage?->etudiant?->nom }} {{ $report->stage?->etudiant?->prenom }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $report->report_date?->format('d/m/Y') }} · {{ $report->stage?->theme ?? 'Rapport journalier' }}
                                    </p>
                                </div>

                                <p class="text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($report->summary, 120) }}</p>

                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.reports.review', $report) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approved">
                                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                            Approuver
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.reports.review', $report) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="changes_requested">
                                        <button type="submit" class="rounded-xl border border-amber-300 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-50">
                                            Demander une reprise
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-slate-500">
                                Aucun rapport en attente aujourd'hui.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Raccourcis utiles</h2>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('presence.historique') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Consulter mon historique de presence
                        </a>
                        <a href="{{ route('reports.index') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Ouvrir mes rapports journaliers
                        </a>
                        <a href="{{ route('stages.index') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Parcourir les stages
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
