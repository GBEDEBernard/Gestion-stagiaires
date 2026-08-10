<x-app-layout title="Suivi Pointage - Admin">

    {{-- ===== HEADER ===== --}}
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-indigo-50 to-sky-50 dark:from-gray-800 dark:to-gray-900 p-6 rounded-2xl border border-indigo-100 dark:border-gray-700 shadow-sm">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="bg-indigo-500 text-white p-2 rounded-xl">📍</span>
                    Suivi Pointage Temps Réel
                </h1>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300 flex items-center gap-2">
                    <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Dernière mise à jour : {{ now()->format('H:i:s') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.presence.index') }}"
                   class="inline-flex items-center gap-1 px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm">
                    ← Retour
                </a>
                <button onclick="printTable()"
                        class="inline-flex items-center gap-1 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm">
                    🖨️ Imprimer
                </button>
            </div>
        </div>
    </x-slot>

    {{-- ===== CONTENU ===== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- ===== FILTRES ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md p-6 transition-all hover:shadow-lg">
            <form method="GET" class="space-y-4">

                {{-- 1ère ligne : Période + Dates --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    {{-- Période --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Période</label>
                        <select name="period" id="period-select"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Jour</option>
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Semaine</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mois</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                        </select>
                    </div>

                    {{-- Date simple --}}
                    <div id="single-date-group">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Date</label>
                        <input type="date" name="date" value="{{ request('date', today()->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition">
                    </div>

                    {{-- Date du --}}
                    <div id="date-from-group" class="hidden">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Du</label>
                        <input type="date" name="date_from" value="{{ request('date_from', $dateFrom ?? '') }}"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition">
                    </div>

                    {{-- Date au --}}
                    <div id="date-to-group" class="hidden">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Au</label>
                        <input type="date" name="date_to" value="{{ request('date_to', $dateTo ?? '') }}"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                </div>

                {{-- 2ème ligne : Utilisateur, Site, École + Boutons --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Utilisateur</label>
                        <select name="user_id"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="">Tous</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Site</label>
                        <select name="site_id"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="">Tous</option>
                            @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">École</label>
                        <select name="school"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-gray-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="">Toutes</option>
                            @foreach($schools as $ecole)
                            <option value="{{ $ecole }}" {{ request('school') == $ecole ? 'selected' : '' }}>
                                {{ $ecole }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition flex items-center justify-center gap-1">
                            🔍 Filtrer
                        </button>
                        <a href="{{ route('admin.presence.pointage-suivi') }}"
                           class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium transition">
                            ✕
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===== STATS ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-stats-card title="Pointages Aujourd'hui" value="{{ $todayCount ?? 0 }}" icon="map-pin" color="emerald">
                <x-slot:subtitle>{{ $checkinsToday ?? 0 }} entrées / {{ $checkoutsToday ?? 0 }} sorties</x-slot:subtitle>
            </x-stats-card>
            <x-stats-card title="Anomalies Détectées" value="{{ $recentAnomalies ?? 0 }}" icon="exclamation-triangle" color="rose">
                <x-slot:subtitle>Sur {{ $periodDays ?? 7 }} jours filtrés</x-slot:subtitle>
            </x-stats-card>
            <x-stats-card title="Précision Moyenne" value="{{ number_format($avgAccuracy ?? 0, 0) }}m" icon="target" color="blue">
                <x-slot:subtitle>GPS précision localisation</x-slot:subtitle>
            </x-stats-card>
        </div>

        {{-- ===== TABLEAU DES POINTAGES ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-gray-900/50 flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    📋 Historique Récent
                    <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2.5 py-0.5 rounded-full">
                        {{ $days->total() }}
                    </span>
                </h3>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span>🕒 {{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-100 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Distance</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($days ?? [] as $day)
                        @php
                            $userName = $day->user?->name ?? $day->etudiant?->user?->name ?? 'N/A';
                            $checkIn = $day->checkInEvent;
                            $checkOut = $day->checkOutEvent;
                            $arrivalTime = $checkIn?->occurred_at ?? $day->first_check_in_at;
                            $departureTime = $checkOut?->occurred_at ?? $day->last_check_out_at;
                            $distance = $checkIn?->distance_to_site_meters ?? null;
                            $precision = $checkIn?->accuracy_meters ?? null;
                            $distanceDisplay = $distance !== null ? round($distance).' m' : ($precision !== null ? round($precision).' m' : '—');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-900/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100">{{ $userName }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <span>📅</span> Journée
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-700 dark:text-slate-200">
                                <div class="flex flex-col gap-0.5">
                                    @if($arrivalTime)
                                    <span class="text-emerald-600 dark:text-emerald-400">Arrivée : {{ Carbon\Carbon::parse($arrivalTime)->format('d/m H:i') }}</span>
                                    @else
                                    <span class="text-slate-400">Arrivée : —</span>
                                    @endif
                                    @if($departureTime)
                                    <span class="text-blue-600 dark:text-blue-400">Départ : {{ Carbon\Carbon::parse($departureTime)->format('d/m H:i') }}</span>
                                    @else
                                    <span class="text-slate-400">Départ : —</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($day->resolved_site_name)
                                <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 text-xs font-semibold rounded-full">
                                    {{ $day->resolved_site_name }}
                                </span>
                                @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs font-semibold rounded-full">
                                    À distance
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300 text-xs font-semibold rounded-full">
                                    {{ $distanceDisplay }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statutPonctualite = '';
                                    $badgeClass = '';
                                    if ($arrivalTime) {
                                        $heureArrivee = Carbon\Carbon::parse($arrivalTime);
                                        $heureReference = $heureArrivee->copy()->setTime(8, 0, 0);
                                        if ($heureArrivee <= $heureReference) {
                                            $statutPonctualite = '✅ À l\'heure';
                                            $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300';
                                        } else {
                                            $minutesRetard = (int) $heureArrivee->diffInMinutes($heureReference);
                                            $statutPonctualite = "⚠️ En retard (-".formatMinutes($minutesRetard).")";
                                            $badgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300';
                                        }
                                    } else {
                                        $statutPonctualite = '⛔ Non pointé';
                                        $badgeClass = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $statutPonctualite }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($day->anomalies->count() > 0)
                                    <span class="text-rose-600 dark:text-rose-400 font-semibold text-sm flex items-center gap-0.5">
                                        <span>🚨</span> {{ $day->anomalies->count() }}
                                    </span>
                                    @endif
                                    @php
                                        $targetUser = $day->etudiant?->user ?? $day->user;
                                    @endphp
                                    @if($targetUser)
                                    <a href="{{ route('attendance.tracking.user.historique', $targetUser) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-300 text-xs font-semibold rounded-full transition">
                                        👁️ Voir
                                    </a>
                                    @else
                                    <span class="text-slate-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0 -3.332.477 -4.5 1.253" />
                                </svg>
                                Aucun pointage trouvé pour ces filtres.<br>
                                <small class="text-xs">Essayez d'étendre la période ou ajustez les critères.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($days->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 border-t border-slate-200 dark:border-slate-700">
                {{ $days->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- ===== TABLEAU DES ABSENCES ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-gray-900/50 flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    ❌ Absences
                    <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2.5 py-0.5 rounded-full">
                        {{ $absences->total() }}
                    </span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-100 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($absences as $absence)
                        @php
                            $userName = $absence['user']?->name ?? 'N/A';
                        @endphp
                        <tr class="hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-100">{{ $userName }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $absence['date']->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs font-semibold rounded-full">
                                    {{ $absence['stage']?->site?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                    🚫 Absent
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                Aucune absence pour cette période. 👍
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($absences->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 border-t border-slate-200 dark:border-slate-700">
                {{ $absences->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

    </div>

    {{-- ===== STYLES & SCRIPTS ===== --}}
    @push('styles')
    <style>
        /* Animation douce pour le pulse du statut live */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Impression */
        @media print {
            body * { visibility: hidden; }
            #print-table, #print-table * { visibility: visible; }
            #print-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            nav, header, footer, button, .no-print { display: none !important; }
            #print-table table {
                font-size: 10px;
                border-collapse: collapse;
                width: 100%;
            }
            #print-table th, #print-table td {
                border: 1px solid #000;
                padding: 4px 6px;
                color: #000 !important;
                background: #fff !important;
            }
            #print-table th {
                background: #eee !important;
                font-weight: bold;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function toggleDateFields() {
            const sel = document.getElementById('period-select');
            const isCustom = sel.value === 'custom';
            document.getElementById('single-date-group')?.classList.toggle('hidden', isCustom);
            document.getElementById('date-from-group')?.classList.toggle('hidden', !isCustom);
            document.getElementById('date-to-group')?.classList.toggle('hidden', !isCustom);
        }

        document.addEventListener('DOMContentLoaded', toggleDateFields);
        document.getElementById('period-select')?.addEventListener('change', toggleDateFields);

        function printTable() {
            const params = new URLSearchParams(window.location.search);
            const printUrl = "{{ route('admin.presence.print') }}?" + params.toString();
            window.open(printUrl, '_blank', 'width=1100,height=900');
        }
    </script>
    @endpush

</x-app-layout>