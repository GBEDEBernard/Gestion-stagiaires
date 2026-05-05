<x-app-layout>

    {{-- ════════════════════════════════════════════════════════
         BANNER
    ════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-700 to-indigo-800 rounded-2xl mb-6">
        <div class="absolute inset-0 opacity-20"
            style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
        </div>
        <div class="relative px-5 sm:px-8 py-7 sm:py-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-1">Tableau de Bord</h1>
                    <p class="text-violet-200 text-sm sm:text-base">Bienvenue ! Voici l'état de votre plateforme</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-3 py-2 border border-white/10 self-start">
                    <div class="flex items-center gap-2 text-white">
                        <svg class="w-4 h-4 text-violet-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium text-sm">{{ now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 60" fill="none" class="w-full h-8">
                <path d="M0 60C240 60 480 60 720 45C960 30 1200 30 1440 45L1440 60H0Z"
                    fill="rgb(249,250,251)" class="dark:fill-gray-900"/>
            </svg>
        </div>
    </div>

    <div class="space-y-5 overflow-x-hidden">

        {{-- ── NOTIFICATIONS ────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="truncate">Notifications</span>
                </h3>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($notificationCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 whitespace-nowrap">{{ $notificationCount }} non lu(s)</span>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 whitespace-nowrap">
                        Voir tout
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="max-h-64 sm:max-h-80 overflow-y-auto">
                @forelse($notifications as $notification)
                <a href="{{ $notification->url }}"
                    onclick="event.preventDefault(); document.getElementById('dash-notif-form-{{ $notification->id }}').submit();"
                    class="flex items-start gap-3 px-4 sm:px-6 py-3 sm:py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <form id="dash-notif-form-{{ $notification->id }}" action="{{ route('notifications.markRead', $notification->id) }}" method="GET" style="display:none;"></form>
                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center mt-0.5
                        @if($notification->color === 'blue') bg-blue-100 text-blue-600
                        @elseif($notification->color === 'amber') bg-amber-100 text-amber-600
                        @else bg-green-100 text-green-600 @endif">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $notification->title }}</p>
                            @if(!$notification->read_at)
                            <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1 sm:line-clamp-2">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </a>
                @empty
                <div class="px-6 py-8 sm:py-12 text-center">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-gray-500 font-medium text-sm">Aucune notification</p>
                    <p class="text-xs text-gray-400 mt-1">Vous êtes à jour !</p>
                </div>
                @endforelse
            </div>
            @if($notificationCount > 0)
            <div class="px-4 sm:px-6 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between gap-3">
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs sm:text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Tout marquer comme lu
                    </button>
                </form>
                <a href="{{ route('notifications.index') }}" class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 font-medium flex items-center gap-1">
                    Voir plus
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endif
        </div>

        {{-- ── KPI ROW 1 ──────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            {{-- Total Stages --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2 sm:p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[10px] sm:text-xs font-semibold bg-green-100 text-green-700">+12%</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $totalStages }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Total Stages</p>
            </div>

            {{-- Stages en cours --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2 sm:p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[10px] sm:text-xs font-semibold bg-emerald-100 text-emerald-700">Actifs</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $enCoursGlobal }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Stages en cours</p>
            </div>

            {{-- Étudiants --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2 sm:p-2.5 bg-violet-100 dark:bg-violet-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[10px] sm:text-xs font-semibold bg-violet-100 text-violet-700">+8%</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $totalEtudiants }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Étudiants inscrits</p>
            </div>

            {{-- Attestations --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="p-2 sm:p-2.5 bg-amber-100 dark:bg-amber-900/30 rounded-xl w-fit mb-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $totalAttestations }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Attestations délivrées</p>
            </div>
        </div>

        {{-- ── KPI ROW 2 ──────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach([
                ['bg-purple-100 dark:bg-purple-900/30','text-purple-600',$terminesGlobal,'Stages terminés'],
                ['bg-orange-100 dark:bg-orange-900/30','text-orange-600',$inscritsGlobal,'Stages à venir'],
                ['bg-cyan-100 dark:bg-cyan-900/30','text-cyan-600',$dureeMoyenne.' j','Durée moyenne'],
                ['bg-rose-100 dark:bg-rose-900/30','text-rose-600',$etudiantsSansStage,'Sans stage'],
            ] as [$bg,$ic,$val,$lbl])
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="p-2 sm:p-2.5 {{ $bg }} rounded-xl w-fit mb-3">
                    <svg class="w-5 h-5 {{ $ic }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $val }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>

        {{-- ── SUIVI DES POINTAGES ──────────── --}}
        @can('presence.view')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-cyan-50 dark:from-gray-800 dark:to-gray-800 flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Suivi des Pointages
                </h3>
                <a href="{{ route('attendance.tracking.index') }}"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs sm:text-sm transition-all whitespace-nowrap">
                    Voir le détail
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 sm:p-6">
                @foreach([
                    ['from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/30','border-emerald-200 dark:border-emerald-700/50','bg-emerald-600','text-emerald-700 dark:text-emerald-300','text-emerald-900 dark:text-emerald-100','text-emerald-600 dark:text-emerald-400',$todayAttendance,'Aujourd\'hui','pointages'],
                    ['from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/30','border-blue-200 dark:border-blue-700/50','bg-blue-600','text-blue-700 dark:text-blue-300','text-blue-900 dark:text-blue-100','text-blue-600 dark:text-blue-400',$todayPresent,'Présents','aujourd\'hui'],
                    ['from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-900/30','border-amber-200 dark:border-amber-700/50','bg-amber-600','text-amber-700 dark:text-amber-300','text-amber-900 dark:text-amber-100','text-amber-600 dark:text-amber-400',$todayLate,'En retard','aujourd\'hui'],
                    ['from-rose-50 to-rose-100 dark:from-rose-900/20 dark:to-rose-900/30','border-rose-200 dark:border-rose-700/50','bg-rose-600','text-rose-700 dark:text-rose-300','text-rose-900 dark:text-rose-100','text-rose-600 dark:text-rose-400',$weekLateMinutes,'Retard cumulé','min cette semaine'],
                ] as [$grad,$border,$iconbg,$label,$val,$sub,$data,$title,$subtitle])
                <div class="bg-gradient-to-br {{ $grad }} rounded-xl p-3 sm:p-4 border {{ $border }}">
                    <p class="text-[10px] sm:text-xs font-semibold {{ $label }} uppercase tracking-wide mb-1">{{ $title }}</p>
                    <p class="text-xl sm:text-2xl font-bold {{ $val }}">{{ $data }}</p>
                    <p class="text-[10px] sm:text-xs {{ $sub }} mt-0.5">{{ $subtitle }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endcan

        {{-- ── CHARTS ROW 1 ──────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            {{-- Évolution inscriptions --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col xs:flex-row xs:items-center xs:justify-between gap-3">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Évolution des inscriptions
                    </h3>
                    <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg self-start xs:self-auto">
                        <button id="btn-jour" onclick="switchPeriod('jour')"
                            class="px-2.5 sm:px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-500 text-white transition-all">Jour</button>
                        <button id="btn-semaine" onclick="switchPeriod('semaine')"
                            class="px-2.5 sm:px-3 py-1.5 text-xs font-semibold rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">Sem.</button>
                        <button id="btn-mois" onclick="switchPeriod('mois')"
                            class="px-2.5 sm:px-3 py-1.5 text-xs font-semibold rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">Mois</button>
                    </div>
                </div>
                <div class="p-4 sm:p-6" style="position:relative;height:220px;sm:height:300px">
                    <canvas id="chart-inscriptions"></canvas>
                </div>
            </div>

            {{-- Par type --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-violet-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        </svg>
                        Par type de stage
                    </h3>
                </div>
                <div class="p-4 sm:p-6" style="position:relative;height:220px">
                    <canvas id="chart-types"></canvas>
                </div>
            </div>
        </div>

        {{-- ── CHARTS ROW 2 ────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            {{-- Stages/mois --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Stages (12 derniers mois)
                    </h3>
                </div>
                <div class="p-4 sm:p-6" style="position:relative;height:220px">
                    <canvas id="chart-stages-mois"></canvas>
                </div>
            </div>

            {{-- Top services --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Top Services
                    </h3>
                </div>
                <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                    @forelse($topServices as $idx => $svc)
                    @php $max = $topServices->max('stages_count') ?: 1; @endphp
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0
                            {{ $idx===0 ? 'bg-yellow-100 text-yellow-700' : ($idx===1 ? 'bg-gray-200 text-gray-700' : ($idx===2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-50 text-blue-600')) }}">
                            {{ $idx+1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $svc->nom }}</span>
                                <span class="text-xs sm:text-sm font-bold text-gray-600 dark:text-gray-400 ml-2 flex-shrink-0">{{ $svc->stages_count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 sm:h-2">
                                <div class="h-1.5 sm:h-2 rounded-full bg-gradient-to-r from-violet-500 to-purple-500"
                                    style="width:{{ round($svc->stages_count/$max*100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 text-sm py-6">Aucun service</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── CHART 3 : Répartition services ─────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-cyan-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Répartition par service
                </h3>
            </div>
            <div class="p-4 sm:p-6" style="position:relative;height:220px sm:height:280px">
                <canvas id="chart-services"></canvas>
            </div>
        </div>

        {{-- ── Activités + Indicateurs ─────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            {{-- Activités récentes --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activités récentes
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-72 sm:max-h-80 overflow-y-auto">
                    @forelse($dernieresActivites as $activity)
                    <div class="px-4 sm:px-6 py-3 sm:py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex items-start gap-3">
                            <div class="h-7 w-7 sm:h-8 sm:w-8 rounded-full bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-1">{{ $activity->action }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $activity->description }}</p>
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0 ml-1">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">Aucune activité récente</div>
                    @endforelse
                </div>
            </div>

            {{-- Indicateurs clés --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Indicateurs clés
                    </h3>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    @foreach([
                        ['Taux de réussite',$tauxReussite,'from-green-400 to-green-500','text-green-600'],
                        ['Étudiants actifs',$tauxEtudiantsActifs,'from-blue-400 to-blue-500','text-blue-600'],
                        ['Conversion',$tauxConversion,'from-violet-400 to-violet-500','text-violet-600']
                    ] as [$lbl,$val,$grad,$cls])
                    <div>
                        <div class="flex justify-between mb-1.5">
                            <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">{{ $lbl }}</span>
                            <span class="text-xs sm:text-sm font-bold {{ $cls }}">{{ $val }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r {{ $grad }} h-2 rounded-full transition-all duration-700" style="width:{{ $val }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-3 text-center">
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                            <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $evolutionInscriptionsMois >= 0 ? '+' : '' }}{{ $evolutionInscriptionsMois }}%</p>
                            <p class="text-xs text-gray-500 mt-0.5">vs mois dernier</p>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $evolutionStages >= 0 ? '+' : '' }}{{ $evolutionStages }}%</p>
                            <p class="text-xs text-gray-500 mt-0.5">stages actifs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CORBEILLE ──────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Corbeille
                </h3>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $totalTrash }} éléments</span>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    @foreach([
                        ['Stages',$stagesTrash->count(),'text-purple-600','bg-purple-50 dark:bg-purple-900/20'],
                        ['Étudiants',$etudiantsTrash->count(),'text-blue-600','bg-blue-50 dark:bg-blue-900/20'],
                        ['Badges',$badgesTrash->count(),'text-amber-600','bg-amber-50 dark:bg-amber-900/20'],
                        ['Services',$servicesTrash->count(),'text-rose-600','bg-rose-50 dark:bg-rose-900/20'],
                    ] as [$lbl,$cnt,$cls,$bg])
                    <div class="{{ $bg }} rounded-xl p-3 sm:p-4 text-center hover:opacity-80 transition">
                        <p class="text-2xl sm:text-3xl font-bold {{ $cls }}">{{ $cnt }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $lbl }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>{{-- /space-y-5 --}}

    {{-- ════════ DONNÉES PHP → JS ════════ --}}
    <script>
        window.__DASHBOARD__ = {
            labelsJour:      {!! Js::from($labelsJour) !!},
            evolutionJour:   {!! Js::from($evolutionJour) !!},
            labelsSemaine:   {!! Js::from($labelsSemaine) !!},
            evolutionSemaine:{!! Js::from($evolutionSemaine) !!},
            labelsMois:      {!! Js::from($labelsMois) !!},
            evolutionMois:   {!! Js::from($evolutionMois) !!},
            typesLabels:     {!! Js::from($typesLabels) !!},
            typesData:       {!! Js::from($typesData) !!},
            stagesMoisLabels:{!! Js::from($labelsMoisAnnee) !!},
            stagesMoisData:  {!! Js::from($stagesParMois) !!},
            svcLabels:       {!! Js::from($servicesStats->pluck('service')->values()) !!},
            svcEnCours:      {!! Js::from($servicesStats->pluck('enCours')->values()) !!},
            svcTermines:     {!! Js::from($servicesStats->pluck('termines')->values()) !!},
            svcInscrits:     {!! Js::from($servicesStats->pluck('inscrits')->values()) !!},
        };
    </script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const D   = window.__DASHBOARD__ || {};
        const safe = (arr, fb=[]) => Array.isArray(arr) && arr.length ? arr : fb;

        const isDark  = document.documentElement.classList.contains('dark');
        const TXT     = isDark ? '#e5e7eb' : '#374151';
        const GRID    = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';

        Chart.defaults.font.family = "'Figtree', sans-serif";
        Chart.defaults.font.size   = 11;

        function xyScales() {
            return {
                x: { ticks:{ color:TXT, maxRotation:30 }, grid:{ display:false } },
                y: { beginAtZero:true, ticks:{ color:TXT }, grid:{ color:GRID } }
            };
        }
        function tip() {
            return { backgroundColor:'rgba(15,23,42,.9)', titleColor:'#fff', bodyColor:'#ddd', padding:8, cornerRadius:6 };
        }

        /* ── 1. INSCRIPTIONS ── */
        let inscChart;
        function buildInscriptions(labels, data) {
            const canvas = document.getElementById('chart-inscriptions');
            if (!canvas) return;
            if (inscChart) inscChart.destroy();
            inscChart = new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: safe(labels, ['—']),
                    datasets: [{ label:'Inscriptions', data:safe(data,[0]), borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,0.1)', fill:true, tension:0.4 }]
                },
                options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:tip() }, scales:xyScales() }
            });
        }

        window.switchPeriod = function(period) {
            const map = {
                jour:    [D.labelsJour,    D.evolutionJour],
                semaine: [D.labelsSemaine, D.evolutionSemaine],
                mois:    [D.labelsMois,    D.evolutionMois],
            };
            document.querySelectorAll('[id^="btn-"]').forEach(b => {
                b.classList.remove('bg-blue-500','text-white');
                b.classList.add('text-gray-600');
            });
            const btn = document.getElementById('btn-'+period);
            if (btn) { btn.classList.add('bg-blue-500','text-white'); btn.classList.remove('text-gray-600'); }
            buildInscriptions(...map[period]);
        };
        buildInscriptions(D.labelsJour, D.evolutionJour);

        /* ── 2. TYPES ── */
        new Chart(document.getElementById('chart-types')?.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: safe(D.typesLabels,['Vide']),
                datasets: [{ data:safe(D.typesData,[1]), backgroundColor:['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'] }]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{color:TXT, boxWidth:10, padding:8} }, tooltip:tip() } }
        });

        /* ── 3. STAGES/MOIS ── */
        new Chart(document.getElementById('chart-stages-mois')?.getContext('2d'), {
            type: 'bar',
            data: {
                labels: safe(D.stagesMoisLabels,[]),
                datasets: [{ label:'Stages', data:safe(D.stagesMoisData,[0]), backgroundColor:'#8b5cf6', borderRadius:4 }]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:tip() }, scales:xyScales() }
        });

        /* ── 4. SERVICES ── */
        new Chart(document.getElementById('chart-services')?.getContext('2d'), {
            type: 'bar',
            data: {
                labels: safe(D.svcLabels,['Vide']),
                datasets: [
                    { label:'En cours',  data:safe(D.svcEnCours,[0]),  backgroundColor:'#22c55e', borderRadius:3 },
                    { label:'Terminés',  data:safe(D.svcTermines,[0]), backgroundColor:'#a855f7', borderRadius:3 },
                    { label:'À venir',   data:safe(D.svcInscrits,[0]), backgroundColor:'#f97316', borderRadius:3 },
                ]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{color:TXT, boxWidth:10, padding:8} }, tooltip:tip() }, scales:xyScales() }
        });

    });
    </script>
    @endpush

</x-app-layout>