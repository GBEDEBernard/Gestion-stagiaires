<x-app-layout title="Historique de présence - Employé">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Historique de présence - Employé</h1>
                <p class="mt-2 text-xl text-slate-600">Tous tes pointages par période</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('employee.presence.pointage') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z">
                    </svg>
                    Nouveau pointage
                </a>
            </div>
        </div>

        {{-- Period Tabs --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-1 shadow-sm">
            <nav class="-mb-px flex space-x-8">
                <a href="?period=week" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'week' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'week' ? '📅 Semaine' : 'Semaine' }}
                </a>
                <a href="?period=month" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'month' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'month' ? '📊 Mois' : 'Mois' }}
                </a>
                <a href="?period=year" class="group inline-flex items-center px-4 py-3 border-b-2 {{ request('period') === 'year' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700' }} text-sm transition-colors">
                    {{ request('period') === 'year' ? '📈 Année' : 'Année' }}
                </a>
            </nav>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-emerald-100">
                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Jours pointés</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceDays->flatten()->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Heures travaillées</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceDays->flatten()->sum('worked_minutes') / 60 }}h</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-amber-100">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600">Anomalies</p>
                        <p class="text-3xl font-bold text-slate-900">{{ $attendanceDays->flatten()->sum('anomaly_count') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Days --}}
        @if($attendanceDays->isNotEmpty())
        <div class="space-y-6">
            @foreach($attendanceDays as $weekKey => $days)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">
                        Semaine du {{ \Carbon\Carbon::createFromFormat('Y-W', $weekKey . '-1')->startOfWeek()->format('d/m/Y') }}
                    </h3>
                </div>
                <div class="divide-y divide-slate-200">
                    @foreach($days as $day)
                    <div class="p-6 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $day->attendance_date->format('l d/m/Y') }}</p>
                                    <p class="text-sm text-slate-500">
                                        @if($day->first_check_in_at && $day->last_check_out_at)
                                        {{ $day->first_check_in_at->format('H:i') }} - {{ $day->last_check_out_at->format('H:i') }}
                                        ({{ number_format($day->worked_minutes / 60, 1) }}h)
                                        @else
                                        Pointage incomplet
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($day->anomalies->count() > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    {{ $day->anomalies->count() }} anomalie(s)
                                </span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($day->day_status === 'late') bg-amber-100 text-amber-800
                                    @elseif($day->day_status === 'incomplete') bg-red-100 text-red-800
                                    @else bg-green-100 text-green-800 @endif">
                                    @if($day->day_status === 'late') Retard
                                    @elseif($day->day_status === 'incomplete') Incomplet
                                    @else À l'heure @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-slate-900">Aucun pointage trouvé</h3>
            <p class="mt-1 text-sm text-slate-500">Commence par pointer ta présence pour voir ton historique.</p>
        </div>
        @endif
    </div>
</x-app-layout>