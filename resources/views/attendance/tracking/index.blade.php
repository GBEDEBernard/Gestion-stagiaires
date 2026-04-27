<x-app-layout title="Suivi des Pointages - Présence">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 space-y-4 sm:space-y-6 text-slate-900 dark:text-slate-100">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">Suivi des Pointages</h1>
                <p class="mt-1 sm:mt-2 text-base sm:text-lg text-slate-600 dark:text-slate-300">Gestion complète des présences et absences</p>
            </div>
        </div>

        {{-- Period Selector --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:gap-4">
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                    <a href="?period=day{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-sm sm:text-base {{ request('period', 'day') === 'day' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📅 Jour
                    </a>
                    <a href="?period=week{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-sm sm:text-base {{ request('period', 'day') === 'week' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📊 Semaine
                    </a>
                    <a href="?period=month{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-sm sm:text-base {{ request('period', 'day') === 'month' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📈 Mois
                    </a>
                    <a href="?period=year{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-sm sm:text-base {{ request('period', 'day') === 'year' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📊 Année
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-stretch sm:items-center">
                    <select id="userFilter" name="user_id"
                        class="px-3 sm:px-4 py-2 text-sm sm:text-base dark:bg-slate-800 border border-slate-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($allUsers as $user)
                        <option value="{{ $user['id'] }}" {{ request('user_id') == $user['id'] ? 'selected' : '' }}>
                            {{ $user['name'] }}
                        </option>
                        @endforeach
                    </select>
                    <input type="date" id="dateFilter" name="date" value="{{ $filterDate->format('Y-m-d') }}"
                        class="px-3 sm:px-4 py-2 text-sm sm:text-base dark:bg-slate-800 border border-slate-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <a href="{{ route('attendance.tracking.export') }}?period={{ request('period', 'day') }}&date={{ $filterDate->format('Y-m-d') }}{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}"
                        class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg sm:rounded-xl shadow-sm transition-all text-center text-sm sm:text-base">
                        📥 Exporter CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Daily View --}}
        @if(request('period', 'day') === 'day')
        <div class="space-y-4 sm:space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-4 sm:p-5 shadow-sm">
                    <h3 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Étudiants</h3>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">Suivi des présences des stagiaires.</p>
                    <div class="mt-4 sm:mt-5 grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-3 sm:p-4">
                            <p class="text-[10px] sm:text-xs uppercase tracking-[0.24em] text-slate-500">Total</p>
                            <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">{{ $summary['student_total'] }}</p>
                        </div>
                        <div class="rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-3 sm:p-4">
                            <p class="text-[10px] sm:text-xs uppercase tracking-[0.24em] text-slate-500">Présents</p>
                            <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-emerald-600">{{ $summary['student_present'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-4 sm:p-5 shadow-sm">
                    <h3 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white">Employés</h3>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">Suivi des présences du personnel.</p>
                    <div class="mt-4 sm:mt-5 grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-3 sm:p-4">
                            <p class="text-[10px] sm:text-xs uppercase tracking-[0.24em] text-slate-500">Total</p>
                            <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">{{ $summary['employee_total'] }}</p>
                        </div>
                        <div class="rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-3 sm:p-4">
                            <p class="text-[10px] sm:text-xs uppercase tracking-[0.24em] text-slate-500">Présents</p>
                            <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-emerald-600">{{ $summary['employee_present'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tableau Étudiants - Version responsive --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Pointages du {{ $displayDate }} — Étudiants</h3>
                </div>

                {{-- Version mobile : cartes --}}
                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($attendanceStudents as $day)
                    <div class="p-4 space-y-2 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <div class="flex justify-between items-start">
                            <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $day->etudiant?->user?->name ?? 'N/A' }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass ?? 'bg-gray-100 text-gray-800' }}">{{ $statusText ?? 'Absent' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-slate-500">Arrivée:</span>
                                <span class="font-semibold ml-1">{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Départ:</span>
                                <span class="font-semibold ml-1">{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Position:</span>
                                <span class="ml-1">{{ $day->stage?->site ? 'TFG SARL' : 'À distance' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Retard:</span>
                                <span class="ml-1 {{ $day->late_minutes > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $day->late_minutes > 0 ? $day->late_minutes . ' min' : '0 min' }}</span>
                            </div>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('attendance.tracking.user.historique', $day->etudiant->user) }}"
                                class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
                                👁️ Voir
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucun pointage étudiant pour cette date.</div>
                    @endforelse
                </div>

                {{-- Version desktop : tableau --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Arrivée</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Départ</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Position</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Statut</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($attendanceStudents as $day)
                            @php
                            $hour = $day->first_check_in_at?->hour ?? null;
                            $minute = $day->first_check_in_at?->minute ?? null;
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $statusText = 'Absent';
                            if ($hour !== null) {
                            if ($hour < 8 || ($hour==7 && $minute <=45)) {
                                $statusClass='bg-emerald-100 text-emerald-800' ;
                                $statusText='👍 À l\' heure';
                                } elseif ($hour==7 && $minute> 45 && $minute < 60) {
                                    $statusClass='bg-amber-100 text-amber-800' ;
                                    $statusText='⚠️ Tard' ;
                                    } else {
                                    $statusClass='bg-red-100 text-red-800' ;
                                    $statusText='👎 Retard' ;
                                    }
                                    }
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $day->etudiant?->user?->name ?? 'N/A' }}</td>
                                    <td class="px-4 lg:px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                        @if($day->first_check_in_at)
                                        <span class="font-semibold">{{ $day->first_check_in_at->format('H:i') }}</span>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                        @if($day->last_check_out_at)
                                        <span class="font-semibold">{{ $day->last_check_out_at->format('H:i') }}</span>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm">
                                        @if($day->stage?->site)
                                        <span class="px-2 lg:px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">TFG SARL</span>
                                        @else
                                        <span class="px-2 lg:px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">À distance</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm">
                                        <span class="px-2 lg:px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm font-semibold">
                                        @if($day->late_minutes > 0)
                                        <span class="text-red-600">{{ $day->late_minutes }} min</span>
                                        @else
                                        <span class="text-emerald-600">0 min</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm">
                                        <a href="{{ route('attendance.tracking.user.historique', $day->etudiant->user) }}"
                                            class="px-2 lg:px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
                                            👁️ Voir
                                        </a>
                                    </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-500">Aucun pointage étudiant pour cette date.</td>
                                    </tr>
                                    @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tableau Employés - Version responsive --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Pointages du {{ $displayDate }} — Employés</h3>
                </div>

                {{-- Version mobile : cartes --}}
                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($attendanceEmployees as $day)
                    <div class="p-4 space-y-2 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <div class="flex justify-between items-start">
                            <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $day->user?->name ?? 'N/A' }}</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass ?? 'bg-gray-100 text-gray-800' }}">{{ $statusText ?? 'Absent' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-slate-500">Arrivée:</span>
                                <span class="font-semibold ml-1">{{ $day->first_check_in_at?->format('H:i') ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Départ:</span>
                                <span class="font-semibold ml-1">{{ $day->last_check_out_at?->format('H:i') ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Position:</span>
                                <span class="ml-1">{{ $day->stage?->site ? 'TFG SARL' : 'À distance' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Retard:</span>
                                <span class="ml-1 {{ $day->late_minutes > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $day->late_minutes > 0 ? $day->late_minutes . ' min' : '0 min' }}</span>
                            </div>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('attendance.tracking.user.historique', $day->user) }}"
                                class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
                                👁️ Voir
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucun pointage employé pour cette date.</div>
                    @endforelse
                </div>

                {{-- Version desktop : tableau --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Arrivée</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Départ</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Position</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Statut</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($attendanceEmployees as $day)
                            @php
                            $hour = $day->first_check_in_at?->hour ?? null;
                            $minute = $day->first_check_in_at?->minute ?? null;
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $statusText = 'Absent';
                            if ($hour !== null) {
                            if ($hour < 8 || ($hour==7 && $minute <=45)) {
                                $statusClass='bg-emerald-100 text-emerald-800' ;
                                $statusText='👍 À l\' heure';
                                } elseif ($hour==7 && $minute> 45 && $minute < 60) {
                                    $statusClass='bg-amber-100 text-amber-800' ;
                                    $statusText='⚠️ Tard' ;
                                    } else {
                                    $statusClass='bg-red-100 text-red-800' ;
                                    $statusText='👎 Retard' ;
                                    }
                                    }
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $day->user?->name ?? 'N/A' }}</td>
                                    <td class="px-4 lg:px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                        @if($day->first_check_in_at)
                                        <span class="font-semibold">{{ $day->first_check_in_at->format('H:i') }}</span>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                        @if($day->last_check_out_at)
                                        <span class="font-semibold">{{ $day->last_check_out_at->format('H:i') }}</span>
                                        @else
                                        <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm">
                                        @if($day->stage?->site)
                                        <span class="px-2 lg:px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">TFG SARL</span>
                                        @else
                                        <span class="px-2 lg:px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">À distance</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm">
                                        <span class="px-2 lg:px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm font-semibold">
                                        @if($day->late_minutes > 0)
                                        <span class="text-red-600">{{ $day->late_minutes }} min</span>
                                        @else
                                        <span class="text-emerald-600">0 min</span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm">
                                        <a href="{{ route('attendance.tracking.user.historique', $day->user) }}"
                                            class="px-2 lg:px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
                                            👁️ Voir
                                        </a>
                                    </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-500">Aucun pointage employé pour cette date.</td>
                                    </tr>
                                    @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Weekly View - Version responsive --}}
        @if(request('period', 'day') === 'week')
        <div class="space-y-4 sm:space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi hebdomadaire — Étudiants</h3>
                </div>

                {{-- Version mobile --}}
                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($studentWeekData as $data)
                    <div class="p-4 space-y-2">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $data['owner']?->user?->name ?? 'N/A' }}</div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Jours présents:</span>
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full text-sm">{{ $data['present_days'] }}/5 jours</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Retard total:</span>
                            <span class="font-semibold">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</span>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('attendance.tracking.user.historique', $data['owner']->user) }}"
                                class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                👁️ Voir
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucune donnée étudiant pour cette semaine.</div>
                    @endforelse
                </div>

                {{-- Version desktop --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard total</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($studentWeekData as $data)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $data['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $data['present_days'] }}/5 jours</span></td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    <a href="{{ route('attendance.tracking.user.historique', $data['owner']->user) }}"
                                        class="px-2 lg:px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
                                        👁️ Voir
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucune donnée étudiant pour cette semaine.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi hebdomadaire — Employés</h3>
                </div>

                {{-- Version mobile --}}
                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($employeeWeekData as $data)
                    <div class="p-4 space-y-2">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $data['owner']?->name ?? 'N/A' }}</div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Jours présents:</span>
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full text-sm">{{ $data['present_days'] }}/5 jours</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Retard total:</span>
                            <span class="font-semibold">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</span>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('attendance.tracking.user.historique', $data['owner']) }}"
                                class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                👁️ Voir
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucune donnée employé pour cette semaine.</div>
                    @endforelse
                </div>

                {{-- Version desktop --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard total</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($employeeWeekData as $data)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $data['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $data['present_days'] }}/5 jours</span></td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    <a href="{{ route('attendance.tracking.user.historique', $data['owner']) }}"
                                        class="px-2 lg:px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
                                        👁️ Voir
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucune donnée employé pour cette semaine.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Monthly View - Version responsive --}}
        @if(request('period', 'day') === 'month')
        <div class="space-y-4 sm:space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi mensuel — Étudiants</h3>
                </div>

                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($studentMonthlySummary as $summary)
                    <div class="p-4 space-y-2">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->user?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-slate-500">Jours présents:</span> <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></div>
                            <div><span class="text-slate-500">Heures travaillées:</span> <span class="font-semibold">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-slate-500">Retard cumulé:</span> <span class="font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucune donnée étudiant pour ce mois.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($studentMonthlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucune donnée étudiant pour ce mois.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi mensuel — Employés</h3>
                </div>

                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($employeeMonthlySummary as $summary)
                    <div class="p-4 space-y-2">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-slate-500">Jours présents:</span> <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></div>
                            <div><span class="text-slate-500">Heures travaillées:</span> <span class="font-semibold">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-slate-500">Retard cumulé:</span> <span class="font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucune donnée employé pour ce mois.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($employeeMonthlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucune donnée employé pour ce mois.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Yearly View - Version responsive --}}
        @if(request('period', 'day') === 'year')
        <div class="space-y-4 sm:space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi annuel — Étudiants</h3>
                </div>

                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($studentYearlySummary as $summary)
                    <div class="p-4 space-y-2">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->user?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-slate-500">Jours présents:</span> <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></div>
                            <div><span class="text-slate-500">Heures:</span> <span class="font-semibold">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-slate-500">Retard:</span> <span class="font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                            <div><span class="text-slate-500">Anomalies:</span> <span class="px-2 py-1 rounded-full {{ $summary['anomalies_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $summary['anomalies_count'] }}</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucune donnée étudiant pour cette année.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Anomalies</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($studentYearlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 rounded-full {{ $summary['anomalies_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $summary['anomalies_count'] }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucune donnée étudiant pour cette année.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi annuel — Employés</h3>
                </div>

                <div class="block sm:hidden divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($employeeYearlySummary as $summary)
                    <div class="p-4 space-y-2">
                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->name ?? 'N/A' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-slate-500">Jours présents:</span> <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></div>
                            <div><span class="text-slate-500">Heures:</span> <span class="font-semibold">{{ $summary['total_worked_hours'] }}h</span></div>
                            <div><span class="text-slate-500">Retard:</span> <span class="font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</span></div>
                            <div><span class="text-slate-500">Anomalies:</span> <span class="px-2 py-1 rounded-full {{ $summary['anomalies_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $summary['anomalies_count'] }}</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">Aucune donnée employé pour cette année.</div>
                    @endforelse
                </div>

                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Anomalies</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($employeeYearlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm"><span class="px-2 lg:px-3 py-1 rounded-full {{ $summary['anomalies_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $summary['anomalies_count'] }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucune donnée employé pour cette année.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Graphiques - Version responsive --}}
        @if($userStats && $userStats['chart_data'])
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                <h3 class="text-base sm:text-xl font-semibold text-slate-900 dark:text-slate-100">
                    📊 Évolution des statistiques — {{ $allUsers->where('id', $selectedUserId)->first()['name'] ?? 'Utilisateur' }}
                </h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">Période: {{ ucfirst($period) }}</p>
            </div>
            <div class="p-3 sm:p-6">
                <div class="relative h-64 sm:h-80 md:h-96">
                    <canvas id="presenceChart"
                        aria-label="Graphique d'évolution des heures travaillées et des minutes de retard"
                        class="w-full h-full">
                    </canvas>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.getElementById('dateFilter').addEventListener('change', function() {
            const period = new URLSearchParams(window.location.search).get('period') || 'day';
            const userId = new URLSearchParams(window.location.search).get('user_id') || '';
            window.location.href = `?period=${period}&date=${this.value}${userId ? '&user_id=' + userId : ''}`;
        });

        document.getElementById('userFilter').addEventListener('change', function() {
            const period = new URLSearchParams(window.location.search).get('period') || 'day';
            const date = document.getElementById('dateFilter').value;
            window.location.href = `?period=${period}&date=${date}&user_id=${this.value}`;
        });

        @if($userStats && $userStats['chart_data'])
        var chartData = @json($userStats['chart_data'] ?? []);
        var labels = Array.isArray(chartData.labels) ? chartData.labels : [];
        var present = Array.isArray(chartData.present) ? chartData.present : [];
        var onTime = Array.isArray(chartData.on_time) ? chartData.on_time : [];
        var lateDays = Array.isArray(chartData.late_days) ? chartData.late_days : [];
        var absences = Array.isArray(chartData.absences) ? chartData.absences : [];
        var workedHours = Array.isArray(chartData.worked_hours) ? chartData.worked_hours : [];
        var lateMinutes = Array.isArray(chartData.late_minutes) ? chartData.late_minutes : [];

        var canvas = document.getElementById('presenceChart');
        if (canvas && labels.length > 0) {
            var isMobile = window.innerWidth < 640;
            var ctx = canvas.getContext('2d');
            var grad = (c1, c2) => {
                var g = ctx.createLinearGradient(0, 0, 0, 400);
                g.addColorStop(0, c1);
                g.addColorStop(1, c2);
                return g;
            };

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: '✅ Présence',
                            data: present,
                            borderColor: '#10b981',
                            backgroundColor: grad('rgba(16,185,129,0.25)', 'rgba(16,185,129,0.02)'),
                            fill: true,
                            stepped: 'before',
                            tension: 0,
                            borderWidth: 2.5,
                            pointRadius: present.map(v => v > 0 ? (isMobile ? 5 : 6) : 0),
                            pointHoverRadius: isMobile ? 6 : 8,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'yBinary'
                        },
                        {
                            label: "🟢 À l'heure",
                            data: onTime,
                            borderColor: '#3b82f6',
                            backgroundColor: grad('rgba(59,130,246,0.18)', 'rgba(59,130,246,0.02)'),
                            fill: true,
                            stepped: 'before',
                            tension: 0,
                            borderWidth: 2.5,
                            pointRadius: onTime.map(v => v > 0 ? (isMobile ? 5 : 6) : 0),
                            pointHoverRadius: isMobile ? 6 : 8,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'yBinary'
                        },
                        {
                            label: '⚠️ Jours retard',
                            data: lateDays,
                            borderColor: '#f59e0b',
                            backgroundColor: grad('rgba(245,158,11,0.18)', 'rgba(245,158,11,0.02)'),
                            fill: true,
                            stepped: 'before',
                            tension: 0,
                            borderWidth: 2.5,
                            pointRadius: lateDays.map(v => v > 0 ? (isMobile ? 5 : 6) : 0),
                            pointHoverRadius: isMobile ? 6 : 8,
                            pointBackgroundColor: '#f59e0b',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'yBinary'
                        },
                        {
                            label: '🔴 Absences',
                            data: absences,
                            borderColor: '#f43f5e',
                            backgroundColor: grad('rgba(244,63,94,0.18)', 'rgba(244,63,94,0.02)'),
                            fill: true,
                            stepped: 'before',
                            tension: 0,
                            borderWidth: 2.5,
                            pointRadius: absences.map(v => v > 0 ? (isMobile ? 5 : 6) : 0),
                            pointHoverRadius: isMobile ? 6 : 8,
                            pointBackgroundColor: '#f43f5e',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'yBinary'
                        },
                        {
                            label: '⏱️ Minutes retard',
                            data: lateMinutes,
                            borderColor: '#f97316',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.35,
                            borderWidth: 2,
                            borderDash: [6, 4],
                            pointRadius: lateMinutes.map(v => v > 0 ? (isMobile ? 4 : 5) : 0),
                            pointHoverRadius: isMobile ? 5 : 7,
                            pointBackgroundColor: '#f97316',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'yMinutes'
                        },
                        {
                            label: '💼 Heures travaillées',
                            data: workedHours,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: workedHours.map(v => v > 0 ? (isMobile ? 3 : 4) : 0),
                            pointHoverRadius: isMobile ? 4 : 6,
                            pointBackgroundColor: '#8b5cf6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'yMinutes'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 900,
                        easing: 'easeOutQuart'
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        title: {
                            display: false
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 8,
                                padding: isMobile ? 6 : 10,
                                font: {
                                    size: isMobile ? 9 : 11,
                                    weight: '600'
                                },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            titleColor: '#fff',
                            bodyColor: '#d1d5db',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    var val = context.parsed.y;
                                    var label = context.dataset.label;
                                    if (label.includes('Minutes')) return label + ': ' + val + ' min';
                                    if (label.includes('Heures')) return label + ': ' + val + 'h';
                                    if (val === 1) return label + ': OUI';
                                    if (val === 0) return label + ': non';
                                    return label + ': ' + val;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: isMobile ? 9 : 11
                                },
                                autoSkip: true,
                                maxTicksLimit: isMobile ? 5 : 10
                            },
                            grid: {
                                color: 'rgba(148,163,184,0.1)'
                            }
                        },
                        yBinary: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            min: 0,
                            max: 1.25,
                            title: {
                                display: true,
                                text: 'État (0/1)',
                                font: {
                                    size: isMobile ? 9 : 11
                                }
                            },
                            ticks: {
                                stepSize: 1,
                                callback: function(v) {
                                    if (v === 0) return '0';
                                    if (v === 1) return '1 ▲';
                                    return '';
                                },
                                font: {
                                    size: isMobile ? 9 : 11,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(148,163,184,0.1)'
                            }
                        },
                        yMinutes: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Min / Heures',
                                font: {
                                    size: isMobile ? 9 : 11,
                                    weight: '600'
                                },
                                color: '#f97316'
                            },
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                font: {
                                    size: isMobile ? 9 : 11
                                },
                                color: '#f97316'
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: isMobile ? 3 : 4
                        },
                        line: {
                            borderWidth: isMobile ? 1.5 : 2
                        }
                    }
                }
            });
        }
        @endif
    </script>
</x-app-layout>