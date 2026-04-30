<x-app-layout title="Suivi Pointage - Admin">

    <x-slot name="header">
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">📍 Suivi Pointage Temps Réel</h1>
                <p class="mt-1 text-xl text-slate-600">Tous les pointages récents • Géolocalisés • Filtrables</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.presence.index') }}"
                    class="px-6 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                    ← Retour Suivi Pro
                </a>
                <a href="{{ route('admin.presence.export-pointages') }}"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                    📥 Exporter CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Filtres --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 shadow-sm p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-7 gap-4">

                {{-- DATE --}}
                <div>
                    <label class="text-sm font-semibold font-serif dark:text-white dark:font-serif dark:font-bold dark:text-xl">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                        class="w-full px-4 py-2 border rounded-xl">
                </div>

                {{-- PERIOD --}}
                <div>
                    <label class="text-sm font-semibold font-serif dark:text-white dark:font-serif dark:font-bold dark:text-xl">Période</label>
                    <select name="period" class="w-full px-4 py-2 border rounded-xl">
                        <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Jour</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mois</option>
                    </select>
                </div>

                {{-- USER --}}
                <div>
                    <label class="text-sm font-semibold font-serif dark:text-white dark:font-serif dark:font-bold dark:text-xl">Utilisateur</label>
                    <select name="user_id" class="w-full px-4 py-2 border rounded-xl">
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
                    <label class="text-sm font-semibold dark:text-white dark:font-serif dark:font-bold dark:text-xl">Site</label>
                    <select name="site_id" class="w-full px-4 py-2 border rounded-xl">
                        <option value="">Tous</option>
                        @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- ECOLE --}}
                <div>
                    <label class="text-sm font-semibold dark:text-white dark:font-serif dark:font-bold dark:text-xl">École</label>
                    <select name="school" class="w-full px-4 py-2 border rounded-xl">
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
                        class="flex-1 bg-emerald-600 text-white p-2 rounded-xl text-sm font-semibold hover:bg-emerald-700 transition">
                        🔍 Filtrer
                    </button>

                    <a href="{{ route('admin.presence.pointage-suivi') }}"
                        class="bg-gray-300 px-3 py-2 rounded-xl text-sm text-gray-700 hover:bg-gray-400 transition">
                        Reset
                    </a>
                    <button onclick="printTable()" class="p-1 text-sm bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all" title="Imprimer UNIQUEMENT le tableau">
                        🖨️ Imprimer Tableau
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 dark:bg-gray-700">
                <h3 class="font-bold text-lg text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    Historique Récent ({{ $days->total() }} résultats)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Heure</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Site</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Précision</th>
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
                        $statusBadge = $day->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800';
                        $statusText = ucfirst($day->status ?? 'pending');
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-900 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">
                                {{ $userName }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                    Journée
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                <div class="flex flex-col gap-0.5">
                                    @if($checkIn)
                                    <span class="text-emerald-600">Arrivée : {{ $checkIn->occurred_at->format('d/m H:i') }}</span>
                                    @else
                                    <span class="text-gray-400">Arrivée : —</span>
                                    @endif
                                    @if($checkOut)
                                    <span class="text-blue-600">Départ : {{ $checkOut->occurred_at->format('d/m H:i') }}</span>
                                    @else
                                    <span class="text-gray-400">Départ : —</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($day->resolved_site_name)
                                <span class="px-2 lg:px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full dark:bg-green-900 dark:text-green-200">
                                    {{ $day->resolved_site_name }}
                                </span>
                                @else
                                <span class="px-2 lg:px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full dark:bg-gray-800 dark:text-gray-200">
                                    À distance
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php $precision = $checkIn?->accuracy_meters ?? $checkOut?->accuracy_meters ?? 0; @endphp
                                <span class="px-2 py-1 bg-cyan-100 text-cyan-800 text-xs font-semibold rounded-full dark:bg-cyan-900 dark:text-cyan-200">
                                    {{ number_format($precision, 0) }}m
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2 justify-end">
                                    @if($day->anomalies->count() > 0)
                                    <span class="text-rose-600 dark:text-rose-400 font-semibold">{{ $day->anomalies->count() }} anomalie{{ $day->anomalies->count() > 1 ? 's' : '' }}</span>
                                    @endif
                            <td class="px-4 lg:px-6 py-3 text-sm">
                                @php
                                $targetUser = $day->etudiant?->user ?? $day->user;
                                @endphp
                                @if($targetUser)
                                <a href="{{ route('attendance.tracking.user.historique', $targetUser) }}"
                                    class="px-2 lg:px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
                                    👁️ Voir
                                </a>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>
            </div>
            </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0 -3.332.477 -4.5 1.253" />
                    </svg>
                    Aucun pointage trouvé pour ces filtres.<br><small>Essayez d'étendre la période ou ajustez la date.</small>
                </td>
            </tr>
            @endforelse
            </tbody>
            </table>
        </div>
        @if($days->hasPages())
        <div class="px-6 py-4 bg-slate-50 dark:bg-gray-700 border-t border-slate-200 dark:border-slate-600">
            {{ $days->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
    </div>

    @push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #print-table,
            #print-table * {
                visibility: visible;
            }

            #print-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            nav,
            header,
            footer,
            button,
            .no-print {
                display: none !important;
            }

            #print-table table {
                font-size: 11px;
                border-collapse: collapse;
                width: 100%;
            }

            #print-table th,
            #print-table td {
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
    </style>
    @endpush


    @push('scripts')
    <script>
        function printTable() {
            // Récupérer tous les paramètres de filtres actuels
            const params = new URLSearchParams(window.location.search);
            // Ouvrir la route d'impression avec les mêmes filtres
            const printUrl = "{{ route('admin.presence.print') }}?" + params.toString();
            window.open(printUrl, '_blank', 'width=1000,height=800');
        }
    </script>
    @endpush>

</x-app-layout>