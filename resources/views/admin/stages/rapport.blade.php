@php
    $c   = $report['counts'];
    $r   = $report['ratios'];
    $w   = $report['window'];
    $an  = $report['anomalies'];
    $etu = $stage->etudiant;
    $p   = $etu?->personnel;

    /** Rend une fraction : la valeur brute d'abord, le pourcentage seulement s'il veut dire quelque chose. */
    $fmt = function (?array $ratio) {
        if (!$ratio) return null;
        $pct = $ratio['rate'] !== null ? number_format($ratio['rate'] * 100, 1, ',', ' ') . ' %' : null;
        return $ratio + ['pct' => $pct];
    };

    $cards = [
        ['Assiduité',          $fmt($r['assiduite']),          'jours attendus'],
        ['Ponctualité',        $fmt($r['ponctualite']),        'jours pointés'],
        ['Journées complètes', $fmt($r['journees_completes']), 'jours pointés'],
        ['Tenue de poste',     $fmt($r['tenue_poste']),        'jours pointés'],
        ['Comptes rendus',     $fmt($r['comptes_rendus']),     'jours pointés'],
        ['Incidents ouverts',  $fmt($r['incidents']),          'jours pointés'],
    ];
@endphp

<x-app-layout>
    {{-- En-tête --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ encrypted_route('stages.show', $stage) }}"
                class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rapport de stage</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $p ? trim(($p->prenom ?? '') . ' ' . ($p->nom ?? '')) : 'Stagiaire' }}
                    &middot;
                    {{ \Carbon\Carbon::parse($w['from'])->isoFormat('Do MMMM YYYY') }}
                    au
                    {{ \Carbon\Carbon::parse($w['to'])->isoFormat('Do MMMM YYYY') }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ encrypted_route('stages.rapport.print', $stage) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimer
            </a>
            <a href="{{ encrypted_route('stages.rapport.pdf', $stage) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Télécharger le PDF
            </a>
        </div>

        @if($w['is_ongoing'])
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Stage en cours — arrêté au {{ \Carbon\Carbon::parse($w['effective_to'])->isoFormat('Do MMMM') }}
            </span>
        @endif
    </div>

    {{-- Thème du stage --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Thème de stage</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-900 dark:text-white">{{ $stage->theme ?: 'Aucun thème renseigné.' }}</p>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="block text-gray-500 dark:text-gray-400">Site</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $stage->site?->name ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 dark:text-gray-400">Domaine</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $stage->domaine?->nom ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 dark:text-gray-400">Type</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $stage->typestage?->libelle ?? '—' }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 dark:text-gray-400">Jours de présence</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $stage->workDaysLabel() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Ratios --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($cards as [$label, $ratio, $denomLabel])
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5">
                <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                    {{ $label }}
                </span>
                <div class="flex items-baseline gap-1 tabular-nums">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $ratio['numerator'] }}</span>
                    <span class="text-xl text-gray-400">/</span>
                    <span class="text-xl text-gray-600 dark:text-gray-300">{{ $ratio['denominator'] }}</span>
                </div>
                @if($ratio['pct'])
                    <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">{{ $ratio['pct'] }}</p>
                @else
                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                        Trop peu de {{ $denomLabel }} pour un pourcentage
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Détail chiffré --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Détail</h3>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-y-5 gap-x-4 text-sm">
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Jours attendus</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $c['expected_days'] }}</span>
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Jours présents</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $c['present_days'] }}</span>
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Jours absents</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $c['absent_days'] }}</span>
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Jours en retard</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $c['late_days'] }}</span>
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Retard cumulé</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $c['late_minutes'] }} min</span>
                @if(($c['avg_late_minutes'] ?? 0) > 0)
                    <span class="block text-xs text-amber-600 dark:text-amber-400">
                        {{ $c['avg_late_minutes'] }} min en moyenne
                    </span>
                @endif
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Heures travaillées</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $c['worked_hours'] }} h</span>
                <span class="block text-xs text-gray-400">dans l'horaire prévu</span>
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Heures supplémentaires</span>
                <span class="text-lg font-semibold tabular-nums {{ ($c['overtime_hours'] ?? 0) > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }}">
                    {{ $c['overtime_hours'] ?? 0 }} h
                </span>
                <span class="block text-xs text-gray-400">hors horaire, non comptées dans le ratio</span>
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Moyenne / jour</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $c['avg_daily_hours'] }} h</span>
            </div>
            <div>
                <span class="block text-gray-500 dark:text-gray-400">Jours sur permission</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $report['permissions']['days_covered'] }}</span>
            </div>
        </div>

        @if($report['permissions']['days_covered'] > 0)
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4">
                    {{ $report['permissions']['days_covered'] }} jour(s) couvert(s) par une permission approuvée
                    ont été retirés des jours attendus. Ils ne comptent ni comme présence ni comme absence.
                </p>
            </div>
        @endif
    </div>

    {{-- Anomalies --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                Anomalies
                <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                    {{ $an['total'] }} sur la période, dont {{ $an['open'] }} non résolue(s)
                </span>
            </h3>

            <div class="flex items-center gap-1 text-xs">
                @foreach(['count' => 'Compté', 'grouped' => 'Ventilé', 'detailed' => 'Détaillé'] as $key => $label)
                    <a href="{{ request()->fullUrlWithQuery(['anomalies' => $key]) }}"
                       class="px-3 py-1.5 rounded-lg font-medium transition {{ $disclosure === $key
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="p-6">
            @if($an['total'] === 0)
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune anomalie sur la période.</p>

            @elseif($disclosure === 'count')
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    {{ $an['total'] }} anomalie(s) détectée(s), {{ $an['resolved'] }} traitée(s),
                    {{ $an['open'] }} encore ouverte(s).
                    Choisissez « Ventilé » ou « Détaillé » pour en voir la nature.
                </p>

            @elseif($disclosure === 'grouped')
                <div class="space-y-2">
                    @foreach($an['by_type'] as $type => $count)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ (new \App\Models\AttendanceAnomaly(['anomaly_type' => $type]))->type_label }}
                            </span>
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-300 tabular-nums">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>

            @else
                <div class="mb-4 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50">
                    <p class="text-sm text-amber-800 dark:text-amber-300">
                        Ce niveau nomme des faits précis, parfois mis en cause. Vérifiez-les avant de
                        les reprendre dans un document remis à une école.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                <th class="pb-3 pr-4">Date</th>
                                <th class="pb-3 pr-4">Type</th>
                                <th class="pb-3 pr-4">Gravité</th>
                                <th class="pb-3 pr-4">Statut</th>
                                <th class="pb-3">Note de résolution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($an['items'] as $a)
                                <tr class="border-b border-gray-100 dark:border-gray-700/60 last:border-0">
                                    <td class="py-3 pr-4 whitespace-nowrap text-gray-600 dark:text-gray-300 tabular-nums">
                                        {{ \Carbon\Carbon::parse($a->detected_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 pr-4 text-gray-900 dark:text-white">{{ $a->type_label }}</td>
                                    <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $a->severity }}</td>
                                    <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $a->status }}</td>
                                    <td class="py-3 text-gray-500 dark:text-gray-400">{{ $a->resolution_note ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
