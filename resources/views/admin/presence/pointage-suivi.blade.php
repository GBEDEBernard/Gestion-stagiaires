<x-app-layout title="Suivi Pointage - Admin">

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-indigo-50 to-sky-50 dark:from-gray-800 dark:to-gray-900 p-5 sm:p-6 rounded-2xl border border-indigo-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                <div>
                    <h1 class="text-xl sm:text-3xl font-bold text-slate-800 dark:text-white tracking-tight leading-tight">Suivi Pointage Temps Réel</h1>
                    <p class="mt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 flex items-center gap-2">
                        <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full animate-pulse shrink-0"></span>
                        <span>Tous les pointages récents • Géolocalisés • Filtrables</span>
                    </p>
                </div>
            </div>
            <div class="shrink-0">
                <a href="{{ route('admin.presence.index') }}"
                   class="inline-flex items-center justify-center gap-2 w-full md:w-auto px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Retour Suivi Pro
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6 sm:space-y-8">

        {{-- Filtres --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md p-4 sm:p-6 transition-all hover:shadow-lg">
            <form method="GET" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-8 gap-3 sm:gap-4">

                {{-- DATE (cachée si période personnalisée) --}}
                <div id="single-date-group" class="col-span-2 sm:col-span-1">
                    <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Date
                    </label>
                    <input type="date" name="date" value="{{ request('date', today()->format('Y-m-d')) }}"
                           class="w-full px-3 sm:px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                {{-- DATE FROM --}}
                <div id="date-from-group" class="hidden col-span-1">
                    <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Du
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom ?? '') }}"
                           class="w-full px-3 sm:px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                {{-- DATE TO --}}
                <div id="date-to-group" class="hidden col-span-1">
                    <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Au
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo ?? '') }}"
                           class="w-full px-3 sm:px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                {{-- PÉRIODE --}}
                <div>
                    <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Période
                    </label>
                    <select name="period" id="period-select" class="w-full px-3 sm:px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Jour</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mois</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                    </select>
                </div>

                {{-- UTILISATEUR --}}
                <div>
                    <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Utilisateur
                    </label>
                    <select name="user_id" class="w-full px-3 sm:px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">Tous</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- SITE --}}
                <div>
                    <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        Site
                    </label>
                    <select name="site_id" class="w-full px-3 sm:px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">Tous</option>
                        @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- ÉCOLE --}}
                <div>
                    <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                        </svg>
                        École
                    </label>
                    <select name="school" class="w-full px-3 sm:px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">Toutes</option>
                        @foreach($schools as $ecole)
                        <option value="{{ $ecole }}" {{ request('school') == $ecole ? 'selected' : '' }}>
                            {{ $ecole }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- BOUTONS --}}
                <div class="col-span-2 sm:col-span-3 lg:col-span-4 xl:col-span-2 flex flex-wrap sm:flex-nowrap items-end gap-2 pt-1 sm:pt-0">
                    <button type="submit"
                            class="flex-1 min-w-[7rem] inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        Filtrer
                    </button>

                    <a href="{{ route('admin.presence.pointage-suivi') }}"
                       class="flex-1 min-w-[6rem] inline-flex items-center justify-center gap-1 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset
                    </a>

                    {{-- BOUTON IMPRIMER (conservé) --}}
                    <button type="button" onclick="printTable()"
                            class="flex-1 min-w-[8rem] inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                            title="Imprimer UNIQUEMENT le tableau">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                        </svg>
                        <span class="hidden sm:inline">Imprimer </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Stats rapides --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
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

        {{-- ── Rapport détaillé par utilisateur (présences + retards + absences) ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-gray-900/50 flex items-center justify-between flex-wrap gap-2">
                <h3 class="font-bold text-base sm:text-lg text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Rapport des pointages ({{ $detail instanceof \Illuminate\Pagination\LengthAwarePaginator ? $detail->total() : $detail->count() }} utilisateur(s))
                </h3>
            </div>

            <div id="print-table" class="p-4 sm:p-6 space-y-5 sm:space-y-6">
                @forelse($detail as $block)
                @php
                $u = $block['user'];
                $t = $block['totals'];
                $workedHours = round($t['worked_minutes'] / 60, 1);
                @endphp
                <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                    {{-- En-tête utilisateur + totaux --}}
                    <div class="px-4 sm:px-5 py-3 bg-slate-50 dark:bg-gray-900/50 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('attendance.tracking.user.historique', $u) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $u->name }}</a>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $block['group'] === 'etudiant' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' }}">
                                {{ $block['group'] === 'etudiant' ? 'Stagiaire' : 'Employé' }}
                            </span>
                            @if($block['school'])
                            <span class="text-xs text-slate-500 dark:text-slate-400">🎓 {{ $block['school'] }}</span>
                            @endif
                            @if($block['site_name'])
                            <span class="text-xs text-slate-500 dark:text-slate-400">📍 {{ $block['site_name'] }}</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-semibold">
                            <span class="text-emerald-600 dark:text-emerald-400">✓ {{ $t['present'] }} présent(s)</span>
                            <span class="text-rose-600 dark:text-rose-400">✗ {{ $t['absent'] }} absence(s)</span>
                            @if($t['corrected'] > 0)
                            <span class="text-slate-500 dark:text-slate-400">◌ {{ $t['corrected'] }} corrigé(s)</span>
                            @endif
                            <span class="text-amber-600 dark:text-amber-400">⏱ Retard cumulé : {{ formatMinutes($t['late_minutes']) }}</span>
                            <span class="text-indigo-600 dark:text-indigo-400">🕒 {{ $workedHours }}h pointées</span>
                        </div>
                    </div>

                    {{-- Jours — tableau scrollable horizontalement sur mobile plutôt que compressé --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-[720px] w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-100 dark:bg-gray-800/80">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide whitespace-nowrap">Journée</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide whitespace-nowrap">Arrivée</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide whitespace-nowrap">Départ</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide whitespace-nowrap">Site</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide whitespace-nowrap">Distance</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide whitespace-nowrap">Retard</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide whitespace-nowrap">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($block['days'] as $day)
                                <tr class="{{ $day['absent'] ? 'bg-rose-50/70 dark:bg-rose-900/10' : '' }} hover:bg-slate-50 dark:hover:bg-gray-900/40 transition-colors">
                                    <td class="px-4 py-2.5 text-sm font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                        {{ $day['date']->locale('fr')->isoFormat('dddd D MMM') }}
                                        <span class="text-xs text-slate-400">{{ $day['date']->format('Y') }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        @if($day['arrival'])
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $day['arrival'] }}</span>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        @if($day['departure'])
                                        <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $day['departure'] }}</span>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $day['site_name'] ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $day['distance'] ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        @if($day['late_minutes'] > 0)
                                        <span class="font-semibold text-rose-600 dark:text-rose-400">-{{ formatMinutes($day['late_minutes']) }}</span>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        @if($day['status'] === 'on_time')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">À l'heure</span>
                                        @elseif($day['status'] === 'late')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">En retard</span>
                                        @elseif($day['status'] === 'absent')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">Absent</span>
                                        @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">Corrigé</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div class="text-center py-14">
                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Aucun pointage ni absence pour ces filtres.</p>
                    <small class="text-xs text-slate-400">Essayez d'étendre la période, ajustez la date ou changez l'utilisateur / l'école.</small>
                </div>
                @endforelse
            </div>

            @if(method_exists($detail, 'hasPages') && $detail->hasPages())
            <div class="px-4 sm:px-6 py-4 bg-slate-50 dark:bg-gray-900/50 border-t border-slate-200 dark:border-slate-700">
                {{ $detail->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
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
                font-size: 11px;
                border-collapse: collapse;
                width: 100%;
            }
            #print-table th, #print-table td {
                border: 1px solid black;
                padding: 6px 8px;
                color: black !important;
                background: white !important;
            }
            #print-table th {
                background: #f0f0f0 !important;
                font-weight: bold;
            }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function printTable() {
            const params = new URLSearchParams(window.location.search);
            const printUrl = "{{ route('admin.presence.print') }}?" + params.toString();
            window.open(printUrl, '_blank', 'width=1000,height=800');
        }

        function toggleDateFields() {
            const sel = document.getElementById('period-select');
            if (!sel) return;
            const isCustom = sel.value === 'custom';
            document.getElementById('single-date-group')?.classList.toggle('hidden', isCustom);
            document.getElementById('date-from-group')?.classList.toggle('hidden', !isCustom);
            document.getElementById('date-to-group')?.classList.toggle('hidden', !isCustom);
        }

        document.addEventListener('DOMContentLoaded', toggleDateFields);
        document.getElementById('period-select')?.addEventListener('change', toggleDateFields);
    </script>
    @endpush

</x-app-layout>