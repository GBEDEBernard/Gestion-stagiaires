<x-app-layout title="Suivi des Pointages - Présence">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-900 dark:text-slate-100">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">Suivi des Pointages</h1>
                <p class="mt-2 text-lg text-slate-600 dark:text-slate-300">Gestion complète des présences et absences</p>
            </div>
        </div>

        {{-- Period Selector --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
            <div class="flex flex-col gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="?period=day{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}" class="px-4 py-2 rounded-xl {{ request('period', 'day') === 'day' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📅 Jour
                    </a>
                    <a href="?period=week{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}" class="px-4 py-2 rounded-xl {{ request('period', 'day') === 'week' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📊 Semaine
                    </a>
                    <a href="?period=month{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}" class="px-4 py-2 rounded-xl {{ request('period', 'day') === 'month' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📈 Mois
                    </a>
                    <a href="?period=year{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}" class="px-4 py-2 rounded-xl {{ request('period', 'day') === 'year' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-all">
                        📊 Année
                    </a>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    <select id="userFilter" name="user_id" class="px-4 py-2 dark:bg-slate-800 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($allUsers as $user)
                        <option value="{{ $user['id'] }}" {{ request('user_id') == $user['id'] ? 'selected' : '' }}>
                            {{ $user['name'] }}
                        </option>
                        @endforeach
                    </select>
                    <input type="date" id="dateFilter" name="date" value="{{ $filterDate->format('Y-m-d') }}" class="px-4 py-2 dark:bg-slate-800 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <a href="{{ route('attendance.tracking.export') }}?period={{ request('period', 'day') }}&date={{ $filterDate->format('Y-m-d') }}{{ request('user_id') ? '&user_id=' . request('user_id') : '' }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm transition-all">
                        📥 Exporter CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Daily View --}}
        @if(request('period', 'day') === 'day')
        <div class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Étudiants</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Suivi des présences des stagiaires.</p>
                    <div class="mt-5 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-4">
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Total</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $summary['student_total'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-4">
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Présents</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $summary['student_present'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Employés</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Suivi des présences du personnel.</p>
                    <div class="mt-5 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-4">
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Total</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $summary['employee_total'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 dark:bg-slate-950/60 p-4">
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Présents</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $summary['employee_present'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Pointages du {{ $displayDate }} — Étudiants</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Arrivée</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Départ</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($attendanceStudents as $day)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $day->etudiant?->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                    @if($day->first_check_in_at)
                                    <span class="font-semibold">{{ $day->first_check_in_at->format('H:i') }}</span>
                                    @else
                                    <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                    @if($day->last_check_out_at)
                                    <span class="font-semibold">{{ $day->last_check_out_at->format('H:i') }}</span>
                                    @else
                                    <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    @if($day->stage?->site)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">TFG SARL</span>
                                    @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">À distance</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm">
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
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm font-semibold">
                                    @if($day->late_minutes > 0)
                                    <span class="text-red-600">{{ $day->late_minutes }} min</span>
                                    @else
                                    <span class="text-emerald-600">0 min</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <a href="{{ route('attendance.tracking.user.historique', $day->etudiant->user) }}"
                                        class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
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

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Pointages du {{ $displayDate }} — Employés</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Arrivée</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Départ</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($attendanceEmployees as $day)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $day->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                    @if($day->first_check_in_at)
                                    <span class="font-semibold">{{ $day->first_check_in_at->format('H:i') }}</span>
                                    @else
                                    <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300">
                                    @if($day->last_check_out_at)
                                    <span class="font-semibold">{{ $day->last_check_out_at->format('H:i') }}</span>
                                    @else
                                    <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    @if($day->stage?->site)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">TFG SARL</span>
                                    @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">À distance</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm">
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
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm font-semibold">
                                    @if($day->late_minutes > 0)
                                    <span class="text-red-600">{{ $day->late_minutes }} min</span>
                                    @else
                                    <span class="text-emerald-600">0 min</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <a href="{{ route('attendance.tracking.user.historique', $day->user) }}"
                                        class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
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

        {{-- Weekly View --}}
        @if(request('period', 'day') === 'week')
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi hebdomadaire — Étudiants</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard total</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($studentWeekData as $data)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $data['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $data['present_days'] }}/5 jours</span></td>
                                <td class="px-6 py-3 text-sm font-semibold">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <a href="{{ route('attendance.tracking.user.historique', $data['owner']->user) }}"
                                        class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
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

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi hebdomadaire — Employés</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard total</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($employeeWeekData as $data)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $data['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $data['present_days'] }}/5 jours</span></td>
                                <td class="px-6 py-3 text-sm font-semibold">{{ $data['total_late_minutes'] > 0 ? $data['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <a href="{{ route('attendance.tracking.user.historique', $data['owner']) }}"
                                        class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full hover:bg-blue-200 transition-colors">
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

        {{-- Monthly View --}}
        @if(request('period', 'day') === 'month')
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi mensuel — Étudiants</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($studentMonthlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
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

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi mensuel — Employés</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($employeeMonthlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
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

        {{-- Yearly View --}}
        @if(request('period', 'day') === 'year')
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi annuel — Étudiants</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Anomalies</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($studentYearlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 rounded-full {{ $summary['anomalies_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $summary['anomalies_count'] }}</span></td>
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

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Suivi annuel — Employés</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Jours présents</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Heures travaillées</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Retard cumulé</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-700 dark:text-slate-300">Anomalies</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($employeeYearlySummary as $summary)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['owner']?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">{{ $summary['present_days'] }} jours</span></td>
                                <td class="px-6 py-3 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $summary['total_worked_hours'] }}h</td>
                                <td class="px-6 py-3 text-sm font-semibold">{{ $summary['total_late_minutes'] > 0 ? $summary['total_late_minutes'] . ' min' : '0 min' }}</td>
                                <td class="px-6 py-3 text-sm"><span class="px-3 py-1 rounded-full {{ $summary['anomalies_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $summary['anomalies_count'] }}</span></td>
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

        {{-- Graphiques --}}
        @if($userStats && $userStats['chart_data'])
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                    📊 Évolution des statistiques — {{ $allUsers->where('id', $selectedUserId)->first()['name'] ?? 'Utilisateur' }}
                </h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Période: {{ ucfirst($period) }}</p>
            </div>
            <div class="p-6">
                <canvas id="presenceChart" width="400" height="200"
                    aria-label="Graphique d'évolution des heures travaillées et des minutes de retard">
                </canvas>
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
        var workedHours = Array.isArray(chartData.worked_hours) ? chartData.worked_hours : [];
        var lateMinutes = Array.isArray(chartData.late_minutes) ? chartData.late_minutes : [];

        var canvas = document.getElementById('presenceChart');
        if (canvas) {
            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Heures travaillées',
                            data: workedHours,
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            yAxisID: 'y',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Minutes de retard',
                            data: lateMinutes,
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            yAxisID: 'y1',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Évolution des présences et retards'
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Heures travaillées'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Minutes de retard'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }
        @endif
    </script>
</x-app-layout>