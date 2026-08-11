<x-app-layout title="Suivi Pointage - Admin">

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-indigo-50 to-sky-50 dark:from-gray-800 dark:to-gray-900 p-6 rounded-2xl border border-indigo-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3">
                {{-- Icône SVG (localisation) --}}
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Suivi Pointage Temps Réel</h1>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300 flex items-center gap-2">
                        <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        Tous les pointages récents • Géolocalisés • Filtrables
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.presence.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Retour Suivi Pro
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Filtres --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md p-6 transition-all hover:shadow-lg">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-8 gap-4">

                {{-- DATE (cachée si période personnalisée) --}}
                <div id="single-date-group">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Date
                    </label>
                    <input type="date" name="date" value="{{ request('date', today()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                {{-- DATE FROM --}}
                <div id="date-from-group" class="hidden">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Du
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom ?? '') }}"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                {{-- DATE TO --}}
                <div id="date-to-group" class="hidden">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Au
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo ?? '') }}"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                {{-- PÉRIODE --}}
                <div>
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Période
                    </label>
                    <select name="period" id="period-select" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Jour</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mois</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                    </select>
                </div>

                {{-- UTILISATEUR --}}
                <div>
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Utilisateur
                    </label>
                    <select name="user_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
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
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        Site
                    </label>
                    <select name="site_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
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
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                        </svg>
                        École
                    </label>
                    <select name="school" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">Toutes</option>
                        @foreach($schools as $ecole)
                        <option value="{{ $ecole }}" {{ request('school') == $ecole ? 'selected' : '' }}>
                            {{ $ecole }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- BOUTONS --}}
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        Filtrer
                    </button>

                    <a href="{{ route('admin.presence.pointage-suivi') }}"
                       class="inline-flex items-center justify-center gap-1 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset
                    </a>

                    {{-- BOUTON IMPRIMER (conservé) --}}
                    <button onclick="printTable()"
                            class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                            title="Imprimer UNIQUEMENT le tableau">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                        </svg>
                        Imprimer Tableau
                    </button>
                </div>
            </form>
        </div>

        {{-- Stats rapides --}}
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

        {{-- Tableau pointages --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-gray-900/50 flex items-center justify-between flex-wrap gap-2">
                <h3 class="font-bold text-lg text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Historique Récent ({{ $days->total() }} résultats)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Heure</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Distance</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Actions</th>
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
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">{{ $userName }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    Journée
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
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
                                        $statutPonctualite = 'À l\'heure';
                                        $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300';
                                    } else {
                                        $minutesRetard = (int) $heureArrivee->diffInMinutes($heureReference);
                                        $statutPonctualite = "En retard (-" . formatMinutes($minutesRetard) . ")";
                                        $badgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300';
                                    }
                                } else {
                                    $statutPonctualite = 'Non pointé';
                                    $badgeClass = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                                }
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $statutPonctualite }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if($day->anomalies->count() > 0)
                                    <span class="text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                        {{ $day->anomalies->count() }}
                                    </span>
                                    @endif
                                    @php
                                    $targetUser = $day->etudiant?->user ?? $day->user;
                                    @endphp
                                    @if($targetUser)
                                    <a href="{{ route('attendance.tracking.user.historique', $targetUser) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-300 text-xs font-semibold rounded-full transition-colors">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Voir
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
                                <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0 -3.332.477 -4.5 1.253" />
                                </svg>
                                Aucun pointage trouvé pour ces filtres.<br><small class="text-xs">Essayez d'étendre la période ou ajustez la date.</small>
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

        {{-- ── Absences ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-gray-900/50 flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    Absences ({{ $absences->total() }} résultats)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($absences as $absence)
                        @php
                        $userName = $absence['user']?->name ?? 'N/A';
                        @endphp
                        <tr class="hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">{{ $userName }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $absence['date']->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs font-semibold rounded-full">
                                    {{ $absence['stage']?->site?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Absent
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                Aucune absence pour cette période.
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
        /* Animation du point de statut */
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