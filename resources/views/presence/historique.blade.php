<x-app-layout title="Historique de présence">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 space-y-4 sm:space-y-6 dark:bg-gray-900/50 min-h-screen pb-20 sm:pb-24">

        {{-- SUCCESS BANNER --}}
        @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)"
            x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
            class="group bg-gradient-to-r from-emerald-500 to-green-600 text-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl border-4 border-white/20 backdrop-blur-sm animate-pulse mb-6 sm:mb-8 dark:border-white/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl sm:rounded-2xl flex items-center justify-center animate-bounce">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black drop-shadow-lg animate-pulse">{{ session('success') }}</h2>
                        <p class="text-xs sm:text-sm opacity-90 font-medium">Redirection vers ton historique ✨</p>
                    </div>
                </div>
                <button @click="show = false" class="p-2 -m-2 rounded-2xl hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-gray-100 tracking-tight">
                     @isset($user)
                        Pointages de {{ $user->name }}
                     @else
                        Tous tes pointages
                     @endisset
                </h1>
                <p class="mt-1 sm:mt-2 text-base sm:text-xl text-slate-600 dark:text-indigo-300">
                    {{ isset($user) && $user->id !== auth()->id() ? 'Pointages de ' . $user->name : 'Tous tes pointages' }} par période
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white rounded-lg sm:rounded-xl font-medium shadow-sm transition-all text-sm sm:text-base">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        {{-- Period Tabs - Version responsive --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-gray-700 p-1 shadow-sm">
            <nav class="flex space-x-4 sm:space-x-8 overflow-x-auto">
                <a href="?period=week" class="group inline-flex items-center px-3 sm:px-4 py-2 sm:py-3 border-b-2 whitespace-nowrap text-xs sm:text-sm {{ request('period') === 'week' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' }} transition-colors">
                    📅 Semaine
                </a>
                <a href="?period=month" class="group inline-flex items-center px-3 sm:px-4 py-2 sm:py-3 border-b-2 whitespace-nowrap text-xs sm:text-sm {{ request('period') === 'month' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' }} transition-colors">
                    📊 Mois
                </a>
                <a href="?period=year" class="group inline-flex items-center px-3 sm:px-4 py-2 sm:py-3 border-b-2 whitespace-nowrap text-xs sm:text-sm {{ request('period') === 'year' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-gray-400 dark:hover:text-gray-200' }} transition-colors">
                    📈 Année
                </a>
            </nav>
        </div>

        {{-- Stats Cards - Version responsive --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
            <x-stats-card title="Jours Pointés" value="{{ $attendanceDays->count() }}" icon="calendar-days" color="slate">
                <x-slot:subtitle>{{ $userStats['present_days'] ?? $attendanceDays->whereNotNull('first_check_in_at')->count() }} présents</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card title="Taux Présence" value="{{ round(($userStats['present_days'] ?? $attendanceDays->whereNotNull('first_check_in_at')->count()) / max(1, $attendanceDays->count()) * 100, 1) }}%" icon="check-circle" color="emerald">
                <x-slot:subtitle>+{{ $userStats['late_days'] ?? $attendanceDays->where('arrival_status', 'late')->count() }} retards</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card title="Heures Totales" value="{{ round($attendanceDays->sum('worked_minutes') / 60, 1) }}h" icon="briefcase" color="blue">
                <x-slot:subtitle>{{ round($attendanceDays->avg('worked_minutes') / 60, 1) }}h/jour moyenne</x-slot:subtitle>
            </x-stats-card>

            <x-stats-card title="Retards Cumulés" value="{{ $attendanceDays->sum('late_minutes') }}min" icon="clock" color="amber">
                <x-slot:subtitle>{{ $attendanceDays->where('late_minutes', '>', 0)->count() }} jours retard</x-slot:subtitle>
            </x-stats-card>
        </div>

        {{-- Graphiques - Version responsive --}}
        @if(isset($userStats))
        <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-gray-100 mb-2 flex items-center gap-2">
                📊 Évolution · Présence & Retards
            </h3>

            {{-- Légende manuelle --}}
            <div class="flex flex-wrap gap-3 sm:gap-4 mb-4">
                <span class="flex items-center gap-2 text-xs sm:text-sm text-slate-600 dark:text-gray-300">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#3b82f6;"></span>
                    Heures travaillées
                </span>
                <span class="flex items-center gap-2 text-xs sm:text-sm text-slate-600 dark:text-gray-300">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f97316;"></span>
                    Minutes de retard
                </span>
            </div>

            {{-- Wrapper avec hauteur fixe --}}
            <div style="position: relative; width: 100%; height: 250px; min-height: 250px;">
                <canvas id="presenceChart"
                    role="img"
                    aria-label="Graphique d'évolution des heures travaillées et des minutes de retard">
                    Aucune donnée à afficher.
                </canvas>
            </div>
        </div>
        @endif

        {{-- Tableau principal - Version responsive --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900/50">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-gray-100 flex items-center gap-2">
                    Pointages récents
                    <span class="px-2 py-1 bg-slate-200 dark:bg-gray-700 text-slate-800 dark:text-gray-200 text-xs font-semibold rounded-full">
                        {{ $attendanceDays->flatten()->count() }} jours
                    </span>
                </h3>
            </div>

            {{-- Version mobile : cartes --}}
            <div class="block sm:hidden divide-y divide-slate-200 dark:divide-gray-700">
                @forelse($attendanceDays->flatten()->sortByDesc('attendance_date') as $index => $day)
                <div class="p-4 space-y-2 hover:bg-slate-50 dark:hover:bg-gray-800 transition-all duration-300
                           {{ $day->attendance_date->isToday() ? 'bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-400 dark:border-emerald-500' : '' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-gray-100 text-sm">{{ $day->attendance_date->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                            <div class="text-xs text-slate-500 dark:text-gray-400 capitalize">{{ $day->attendance_date->locale('fr')->isoFormat('dddd') }}</div>
                        </div>
                        @php
                            $arrivalTime = $day->first_check_in_at;
                            if ($arrivalTime) {
                                $timeInMinutes = $arrivalTime->hour * 60 + $arrivalTime->minute;
                                if ($timeInMinutes < 8 * 60) {
                                    $status = 'À l\'heure';
                                    $color = 'emerald';
                                    $icon = '👍';
                                } else {
                                    $status = 'En retard';
                                    $color = 'rose';
                                    $icon = '👎';
                                }
                            } else {
                                $status = 'Absent';
                                $color = 'gray';
                                $icon = '❌';
                            }
                        @endphp
                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                            {{ $icon }} {{ $status }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
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
                            <span class="ml-1">{{ $day->stage && $day->stage->site ? 'TFG SARL' : 'À distance' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500">Retard:</span>
                            <span class="ml-1 font-semibold">{{ $day->late_minutes }} min</span>
                        </div>
                    </div>
                    @php $report = $day->dailyReports->first(); @endphp
                    @if($report)
                    <div class="pt-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($report->status === 'submitted') bg-emerald-100 text-emerald-800
                            @elseif($report->status === 'draft') bg-amber-100 text-amber-800
                            @elseif($report->status === 'approved') bg-green-100 text-green-800
                            @else bg-slate-100 text-slate-800 @endif">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>
                    @endif
                </div>
                @empty
                <div class="p-8 text-center text-slate-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 mb-4 text-slate-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-gray-100 mb-2">Aucun pointage trouvé</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400">Tes premiers pointages apparaîtront ici.</p>
                    <div class="mt-4 sm:mt-6">
                        <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 rounded-xl sm:rounded-2xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold shadow-lg transition-all text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Faire mon premier pointage
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Version desktop : tableau --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Heure d'arrivée</th>
                            <th class="px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Heure de départ</th>
                            <th class="px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Position</th>
                            <th class="px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Minutes avant 8h</th>
                            <th class="px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                            <th class="px-4 lg:px-6 py-3 sm:py-4 text-left text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Rapport</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                        @forelse($attendanceDays->flatten()->sortByDesc('attendance_date') as $index => $day)
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-800 transition-all duration-300
                                   {{ $day->attendance_date->isToday() ? 'bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-400 dark:border-emerald-500' : '' }}">
                            <td class="px-4 lg:px-6 py-3 sm:py-4">
                                <div class="font-semibold text-slate-900 dark:text-gray-100 text-sm">{{ $day->attendance_date->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
                                <div class="text-xs text-slate-500 dark:text-gray-400 capitalize">{{ $day->attendance_date->locale('fr')->isoFormat('dddd') }}</div>
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4">
                                <div class="text-sm font-semibold text-slate-900 dark:text-gray-100">
                                    {{ $day->first_check_in_at?->format('H:i') ?? '—' }}
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4 text-sm font-semibold text-slate-900 dark:text-gray-100">
                                {{ $day->last_check_out_at?->format('H:i') ?? '—' }}
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4 text-sm text-slate-700 dark:text-gray-300">
                                @if($day->stage && $day->stage->site)
                                <span class="px-2 lg:px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 text-xs font-semibold rounded-full">
                                    TFG SARL
                                </span>
                                @else
                                <span class="px-2 lg:px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-semibold rounded-full">
                                    À distance
                                </span>
                                @endif
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4 text-sm text-slate-700 dark:text-gray-300">
                                @php
                                    $arrivalTime = $day->first_check_in_at;
                                    $minutesBefore = 0;
                                    if ($arrivalTime) {
                                        $arrivalMinutes = $arrivalTime->hour * 60 + $arrivalTime->minute;
                                        $eightAM = 8 * 60;
                                        if ($arrivalMinutes < $eightAM) {
                                            $minutesBefore = $eightAM - $arrivalMinutes;
                                        }
                                    }
                                @endphp
                                {{ $minutesBefore > 0 ? $minutesBefore . ' min' : '—' }}
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4">
                                @php
                                    $arrivalTime = $day->first_check_in_at;
                                    $status = 'inconnu';
                                    $color = 'gray';
                                    $icon = 'question';
                                    if ($arrivalTime) {
                                        $timeInMinutes = $arrivalTime->hour * 60 + $arrivalTime->minute;
                                        if ($timeInMinutes < 8 * 60) {
                                            $status = 'À l\'heure';
                                            $color = 'emerald';
                                            $icon = 'thumbs-up';
                                        } else {
                                            $status = 'En retard';
                                            $color = 'rose';
                                            $icon = 'thumbs-down';
                                        }
                                    }
                                @endphp
                                @if($status !== 'inconnu')
                                <span class="px-3 sm:px-4 py-1 sm:py-2 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-{{ $color }}-400 to-{{ $color }}-500 text-white shadow-lg dark:from-{{ $color }}-500 dark:to-{{ $color }}-600">
                                    @if($icon === 'thumbs-up') 👍 @elseif($icon === 'thumbs-down') 👎 @endif
                                    {{ $status }}
                                </span>
                                @else
                                <span class="px-3 sm:px-4 py-1 sm:py-2 inline-flex text-xs font-bold rounded-full bg-slate-200 dark:bg-gray-700 text-slate-800 dark:text-gray-200">
                                    Inconnu
                                </span>
                                @endif
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4">
                                @php $report = $day->dailyReports->first(); @endphp
                                @if($report)
                                    @if($report->status === 'submitted')
                                    <span class="px-2 lg:px-3 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-xs font-semibold rounded-full">Soumis</span>
                                    @elseif($report->status === 'draft')
                                    <span class="px-2 lg:px-3 py-1 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 text-xs font-semibold rounded-full">Brouillon</span>
                                    @elseif($report->status === 'approved')
                                    <span class="px-2 lg:px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 text-xs font-semibold rounded-full">Approuvé</span>
                                    @else
                                    <span class="px-2 lg:px-3 py-1 bg-slate-100 dark:bg-gray-800 text-slate-800 dark:text-gray-200 text-xs font-semibold rounded-full">{{ ucfirst($report->status) }}</span>
                                    @endif
                                @else
                                <span class="px-2 lg:px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-xs font-semibold rounded-full">Aucun</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-gray-400">
                                <svg class="mx-auto h-16 w-16 mb-4 text-slate-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-gray-100 mb-2">Aucun pointage trouvé</h3>
                                <p class="text-slate-500 dark:text-gray-400">Tes premiers pointages apparaîtront ici.</p>
                                <div class="mt-6">
                                    <a href="{{ route('presence.pointage') }}" class="inline-flex items-center px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-semibold shadow-lg transition-all">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Faire mon premier pointage
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        @if(isset($userStats))

        var chartData     = @json($userStats['chart_data'] ?? []);
        var labels        = Array.isArray(chartData.labels)       ? chartData.labels       : [];
        var workedHours   = Array.isArray(chartData.worked_hours) ? chartData.worked_hours : [];
        var lateMinutes   = Array.isArray(chartData.late_minutes) ? chartData.late_minutes : [];

        workedHours = workedHours.map(function(v) { return parseFloat(v) || 0; });
        lateMinutes = lateMinutes.map(function(v) { return parseInt(v)   || 0; });

        var canvas = document.getElementById('presenceChart');
        if (!canvas) return;

        if (labels.length === 0) {
            var ctx2d = canvas.getContext('2d');
            ctx2d.font = '12px sans-serif';
            ctx2d.fillStyle = '#94a3b8';
            ctx2d.textAlign = 'center';
            ctx2d.fillText('Aucune donnée disponible pour cette période.', canvas.offsetWidth / 2, 120);
            return;
        }

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Heures travaillées',
                        data: workedHours,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.08)',
                        borderWidth: window.innerWidth < 640 ? 1.5 : 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        pointRadius: window.innerWidth < 640 ? 3 : 5,
                        pointHoverRadius: window.innerWidth < 640 ? 5 : 7,
                        yAxisID: 'yHeures'
                    },
                    {
                        label: 'Minutes de retard',
                        data: lateMinutes,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.08)',
                        borderWidth: window.innerWidth < 640 ? 1.5 : 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        pointRadius: window.innerWidth < 640 ? 3 : 5,
                        pointHoverRadius: window.innerWidth < 640 ? 5 : 7,
                        yAxisID: 'yMinutes'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 600 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        titleFont: { size: window.innerWidth < 640 ? 10 : 12 },
                        bodyFont: { size: window.innerWidth < 640 ? 9 : 11 },
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return ' ' + context.parsed.y.toFixed(1) + 'h travaillées';
                                }
                                return ' ' + context.parsed.y + ' min de retard';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: window.innerWidth < 640 ? 5 : 12,
                            color: '#64748b',
                            font: { size: window.innerWidth < 640 ? 9 : 11 }
                        },
                        grid: { color: 'rgba(148, 163, 184, 0.15)' }
                    },
                    yHeures: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        title: { display: true, text: 'Heures', color: '#3b82f6', font: { size: window.innerWidth < 640 ? 9 : 11 } },
                        ticks: { color: '#3b82f6', font: { size: window.innerWidth < 640 ? 9 : 11 } },
                        grid: { color: 'rgba(148, 163, 184, 0.15)' }
                    },
                    yMinutes: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        title: { display: true, text: 'Minutes', color: '#f97316', font: { size: window.innerWidth < 640 ? 9 : 11 } },
                        ticks: { color: '#f97316', font: { size: window.innerWidth < 640 ? 9 : 11 } },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });

        @endif
    });
    </script>
    @endpush
</x-app-layout>