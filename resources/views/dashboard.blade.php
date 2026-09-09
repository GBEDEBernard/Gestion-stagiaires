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
                <div class="flex flex-wrap items-center gap-3 self-start sm:self-auto">
                    @role('admin')
                    <a href="{{ route('admin.notifications.urgent.index') }}"
                       class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all duration-300 transform hover:-translate-y-0.5 group border border-white/20">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                        </span>
                        <svg class="w-4 h-4 text-white group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span>Alertes urgentes</span>
                    </a>
                    @endrole
                    @role('admin|superviseur')
                    @if(($pendingCorrectionsCount ?? 0) > 0)
                    <a href="{{ route('admin.presence.corrections') }}"
                       class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 transition-all duration-300 transform hover:-translate-y-0.5 group border border-white/20">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                        </span>
                        <svg class="w-4 h-4 text-white group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        <span>Correction de pointage</span>
                        <span class="inline-flex items-center justify-center min-w-[1.375rem] h-5 px-1.5 rounded-full bg-white/25 text-[11px] font-bold tabular-nums">{{ $pendingCorrectionsCount }}</span>
                    </a>
                    @endif
                    @endrole
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-3.5 py-2.5 border border-white/10">
                        <div class="flex items-center gap-2 text-white">
                            <svg class="w-4 h-4 text-violet-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium text-sm">{{ now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</span>
                        </div>
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
        {{-- ── KPI ROW 1 ──────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            {{-- Total Stages --}}
            <a href="{{ route('stages.index', ['annee_academique' => 'all']) }}" class="block bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2 sm:p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[10px] sm:text-xs font-semibold {{ $evolutionStages30j >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $evolutionStages30j >= 0 ? '+' : '' }}{{ $evolutionStages30j }}%</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $totalStages }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Total Stages</p>
            </a>

            {{-- Stages en cours --}}
            <a href="{{ route('stages.index', ['statut' => 'En cours', 'annee_academique' => 'all']) }}" class="block bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
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
            </a>

            {{-- Stagiaires --}}
            <a href="{{ route('etudiants.index') }}" class="block bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2 sm:p-2.5 bg-violet-100 dark:bg-violet-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[10px] sm:text-xs font-semibold {{ $evolutionEtudiants30j >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $evolutionEtudiants30j >= 0 ? '+' : '' }}{{ $evolutionEtudiants30j }}%</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $totalEtudiants }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Stagiaires inscrits</p>
            </a>

            {{-- Attestations --}}
            <a href="{{ route('stages.index') }}" class="block bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="p-2 sm:p-2.5 bg-amber-100 dark:bg-amber-900/30 rounded-xl w-fit mb-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $totalAttestations }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Attestations délivrées</p>
            </a>
        </div>

        {{-- ── KPI ROW 2 ──────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach([
                ['bg-purple-100 dark:bg-purple-900/30','text-purple-600',$terminesGlobal,'Stages terminés',route('stages.index',['statut'=>'Termine','annee_academique'=>'all'])],
                ['bg-orange-100 dark:bg-orange-900/30','text-orange-600',$inscritsGlobal,'Stages à venir',route('stages.index',['statut'=>'A venir','annee_academique'=>'all'])],
                ['bg-cyan-100 dark:bg-cyan-900/30','text-cyan-600',$dureeMoyenne.' j','Durée moyenne',null],
                ['bg-rose-100 dark:bg-rose-900/30','text-rose-600',$etudiantsSansStage,'Sans stage',route('etudiants.index',['stage_status'=>'none'])],
            ] as [$bg,$ic,$val,$lbl,$link])
            @if($link)
            <a href="{{ $link }}" class="block bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
            @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            @endif
                <div class="p-2 sm:p-2.5 {{ $bg }} rounded-xl w-fit mb-3">
                    <svg class="w-5 h-5 {{ $ic }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $val }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $lbl }}</p>
            @if($link)
            </a>
            @else
            </div>
            @endif
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

        {{-- ── DEMANDES DE PERMISSION (cliquables) ─────────────────────── --}}
        @role('admin|superviseur')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach([
                ['Demandes en attente',$permissionsPending,'from-amber-500 to-orange-500','bg-amber-100 dark:bg-amber-900/30','text-amber-600','Urgent à traiter',route('admin.permissions.index',['status'=>'pending']),'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Approuvées',$permissionsApproved,'from-emerald-500 to-teal-500','bg-emerald-100 dark:bg-emerald-900/30','text-emerald-600','validées',route('admin.permissions.index',['status'=>'approved']),'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Refusées',$permissionsRejected,'from-rose-500 to-red-500','bg-rose-100 dark:bg-rose-900/30','text-rose-600','rejetées',route('admin.permissions.index',['status'=>'rejected']),'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as [$lbl,$val,$grad,$bg,$txt,$sub,$link,$icon])
            <a href="{{ $link }}" class="group block bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-0.5">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2 sm:p-2.5 {{ $bg }} rounded-xl">
                        <svg class="w-5 h-5 {{ $txt }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[10px] sm:text-xs font-semibold bg-gradient-to-r {{ $grad }} text-white">{{ $sub }}</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $val }}</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5">
                    {{ $lbl }}
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-violet-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </p>
            </a>
            @endforeach
        </div>
        @endrole

        {{-- ── SUIVI DES TÂCHES (courbes) ─────────────────────────────── --}}
        @role('admin|superviseur')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-violet-50 to-fuchsia-50 dark:from-gray-800 dark:to-gray-800 flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-violet-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Suivi des Tâches
                </h3>
                <a href="{{ route('tasks.index') }}"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 bg-violet-600 hover:bg-violet-700 text-white font-semibold rounded-xl text-xs sm:text-sm transition-all whitespace-nowrap">
                    Voir les tâches
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 p-4 sm:p-6">
                @foreach([
                    ['Créées',$tasksCreated,'from-violet-500 to-purple-500','text-violet-700 dark:text-violet-300'],
                    ['En cours',$tasksInProgress,'from-blue-500 to-blue-600','text-blue-700 dark:text-blue-300'],
                    ['Terminées',$tasksCompleted,'from-emerald-500 to-teal-500','text-emerald-700 dark:text-emerald-300'],
                    ['En attente val.',$tasksAwaiting,'from-amber-500 to-orange-500','text-amber-700 dark:text-amber-300'],
                ] as [$lbl,$val,$grad,$txt])
                <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-4 h-4 bg-gradient-to-r {{ $grad }} rounded p-[1px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-[10px] sm:text-xs font-semibold {{ $txt }} uppercase tracking-wide">{{ $lbl }}</span>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $val }}</p>
                </div>
                @endforeach
            </div>
            <div class="px-4 sm:px-6 pb-4 sm:pb-6" style="position:relative;height:260px">
                <canvas id="chart-tasks"></canvas>
            </div>
            <div class="px-4 sm:px-6 pb-4 text-center">
                <p class="inline-flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                    </svg>
                    Cliquez sur un point de la courbe pour voir le détail des tâches du mois.
                </p>
            </div>
        </div>
        @endrole

        {{-- ── CERCLES STATISTIQUES UTILISATEURS ──────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            {{-- Cercle 1 : Répartition des Utilisateurs (Cliquable -> /admin/stages?per_page=10) --}}
            <a href="{{ url('/admin/stages?per_page=10') }}"
               class="group block bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 transition-all">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50/60 via-indigo-50/40 to-white dark:from-gray-800 dark:to-gray-800 flex items-center justify-between">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Répartition des Utilisateurs
                    </h3>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400 group-hover:translate-x-0.5 transition-transform bg-blue-100/60 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">
                        Voir les stages
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
                <div class="p-4 sm:p-6" style="position:relative;height:260px">
                    <canvas id="chart-users-roles"></canvas>
                </div>
            </a>

            {{-- Cercle 2 : Statut des Comptes (Actifs / Inactifs) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Statut des Comptes
                    </h3>
                </div>
                <div class="p-4 sm:p-6" style="position:relative;height:260px">
                    <canvas id="chart-users-status"></canvas>
                </div>
            </div>
        </div>

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
                <div class="px-4 sm:px-6 pb-4 text-center">
                    <p class="inline-flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                        Cliquez sur un point de la courbe pour voir le détail des inscriptions de la période.
                    </p>
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



        {{-- ── RÉPARTITION PAR DOMAINE (graphique empilé + détail) ───────────── --}}
        @php
            $repartitionDomaines = $domainesStats->sortByDesc('total')->values();
            $nbDomainesRep       = $repartitionDomaines->count();
            $totalStagesRep      = (int) $repartitionDomaines->sum('total');
            $sEnCoursRep         = (int) $repartitionDomaines->sum('enCours');
            $sTerminesRep        = (int) $repartitionDomaines->sum('termines');
            $sInscritsRep        = (int) $repartitionDomaines->sum('inscrits');
            $paletteRep          = ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#6366f1', '#14b8a6', '#f97316', '#ef4444', '#a855f7', '#22d3ee'];
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-cyan-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Répartition par domaine
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 ml-1">
                        {{ $nbDomainesRep }} domaine{{ $nbDomainesRep > 1 ? 's' : '' }}
                    </span>
                </h3>
            </div>
            <div class="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-5 gap-5 sm:gap-6">

                {{-- Graphique : donut par domaine (total de stages) --}}
                <div class="lg:col-span-3 min-w-0">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <p class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 min-w-0 truncate">
                            Nombre total de stages par domaine
                        </p>
                        @if($nbDomainesRep)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 whitespace-nowrap flex-shrink-0">
                            {{ $totalStagesRep }} stage{{ $totalStagesRep > 1 ? 's' : '' }}
                        </span>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                        <div class="relative w-full max-w-[15rem] aspect-square shrink-0">
                            <canvas id="chart-domaines"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl sm:text-3xl font-extrabold text-gray-800 dark:text-gray-100 tabular-nums">{{ $totalStagesRep }}</span>
                                <span class="text-[10px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">stages</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 w-full space-y-2">
                            @forelse($repartitionDomaines as $idxPie => $domPie)
                            @php
                                $colorPie = $paletteRep[$idxPie % count($paletteRep)];
                                $pctPie   = $totalStagesRep ? round($domPie['total'] / $totalStagesRep * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $colorPie }}"></span>
                                <span class="flex-1 min-w-0 text-xs font-medium text-gray-700 dark:text-gray-300 truncate" title="{{ $domPie['domaine'] }}">{{ $domPie['domaine'] }}</span>
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 flex-shrink-0 tabular-nums">{{ $pctPie }}%</span>
                                <span class="w-6 text-right text-xs font-bold text-gray-700 dark:text-gray-300 flex-shrink-0 tabular-nums">{{ $domPie['total'] }}</span>
                            </div>
                            @empty
                            <p class="text-center text-gray-500 text-sm py-6">Aucun domaine enregistré</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Détail par domaine --}}
                <div class="lg:col-span-2 min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">Détail par domaine</p>
                    <div class="space-y-2.5">
                        @forelse($repartitionDomaines as $idxRep => $domRep)
                        @php
                            $totRep = $domRep['total'] ?: 1;
                            $rings  = [
                                ['En cours', $domRep['enCours'],  round($domRep['enCours']  / $totRep * 100), 'stroke-emerald-500', 'text-emerald-600 dark:text-emerald-400'],
                                ['Terminés', $domRep['termines'], round($domRep['termines'] / $totRep * 100), 'stroke-violet-500',  'text-violet-600 dark:text-violet-400'],
                                ['À venir',  $domRep['inscrits'], round($domRep['inscrits'] / $totRep * 100), 'stroke-orange-500',  'text-orange-600 dark:text-orange-400'],
                            ];
                        @endphp
                        <div class="group rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/50 px-3 py-2.5 transition hover:bg-white dark:hover:bg-gray-700/40 hover:border-gray-200 dark:hover:border-gray-600">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0
                                    {{ $idxRep === 0 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400' : ($idxRep === 1 ? 'bg-gray-200 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300' : ($idxRep === 2 ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400' : 'bg-cyan-50 text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-300')) }}">
                                    {{ $idxRep + 1 }}
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 truncate" title="{{ $domRep['domaine'] }}">{{ $domRep['domaine'] }}</span>
                                </span>
                                <span class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 flex-shrink-0">{{ $domRep['total'] }}</span>
                            </div>

                            <div class="mt-3 ml-9 flex items-start justify-between gap-2">
                                @foreach($rings as [$ringLbl, $ringCnt, $ringPct, $ringBar, $ringTxt])
                                <div class="flex flex-col items-center gap-1 min-w-0">
                                    <div class="relative w-9 h-9 flex-shrink-0">
                                        <svg viewBox="0 0 36 36" class="w-9 h-9 -rotate-90">
                                            <circle cx="18" cy="18" r="15.915" fill="none" class="stroke-gray-200 dark:stroke-gray-700" stroke-width="3.5"></circle>
                                            <circle cx="18" cy="18" r="15.915" fill="none" class="{{ $ringBar }}" stroke-width="3.5"
                                                stroke-linecap="round" stroke-dasharray="{{ $ringPct }} 100"></circle>
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold {{ $ringTxt }}">{{ $ringPct }}%</span>
                                    </div>
                                    <span class="text-[9px] font-medium text-gray-400 dark:text-gray-500 leading-none">{{ $ringLbl }}</span>
                                    <span class="text-[9px] font-semibold text-gray-500 dark:text-gray-400 leading-none -mt-0.5">{{ $ringCnt }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 text-sm py-6">Aucun domaine enregistré</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($nbDomainesRep)
            <div class="px-4 sm:px-6 py-3 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>{{ $sEnCoursRep }} en cours
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-violet-500"></span>{{ $sTerminesRep }} terminés
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>{{ $sInscritsRep }} à venir
                </span>
                <span class="ml-auto inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-500"></span>{{ $totalStagesRep }} au total
                </span>
            </div>
            @endif
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
                        ['Stagiaires actifs',$tauxEtudiantsActifs,'from-blue-400 to-blue-500','text-blue-600'],
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
                        ['Stages',$stagesTrash->count(),'text-purple-600','bg-purple-50 dark:bg-purple-900/20',route('stages.trash')],
                        ['Stagiaires',$etudiantsTrash->count(),'text-blue-600','bg-blue-50 dark:bg-blue-900/20',route('etudiants.trash')],
                        ['Badges',$badgesTrash->count(),'text-amber-600','bg-amber-50 dark:bg-amber-900/20',route('stages.trash')],
                        ['Total',$totalTrash,'text-rose-600','bg-rose-50 dark:bg-rose-900/20',route('stages.trash')],
                    ] as [$lbl,$cnt,$cls,$bg,$link])
                    <a href="{{ $link }}" class="{{ $bg }} rounded-xl p-3 sm:p-4 text-center hover:opacity-80 transition block">
                        <p class="text-2xl sm:text-3xl font-bold {{ $cls }}">{{ $cnt }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $lbl }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- {{-- ─ NOTIFICATIONS (EN BAS) ─ --}}
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
        </div> -->

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
            rangesJour:      {!! Js::from($rangesJour) !!},
            rangesSemaine:   {!! Js::from($rangesSemaine) !!},
            rangesMois:      {!! Js::from($rangesMois) !!},
            typesLabels:     {!! Js::from($typesLabels) !!},
            typesData:       {!! Js::from($typesData) !!},
            stagesMoisLabels:{!! Js::from($labelsMoisAnnee) !!},
            stagesMoisData:  {!! Js::from($stagesParMois) !!},
            domLabels:       {!! Js::from($repartitionDomaines->pluck('domaine')->values()) !!},
            domTotaux:       {!! Js::from($repartitionDomaines->pluck('total')->values()) !!},
            domPalette:      {!! Js::from($paletteRep) !!},
            tasksMoisLabels: {!! Js::from($tasksMoisLabels) !!},
            tasksCreated:    {!! Js::from($tasksCreatedByMonth) !!},
            tasksInProgress: {!! Js::from($tasksInProgressByMonth) !!},
            tasksCompleted:  {!! Js::from($tasksCompletedByMonth) !!},
            tasksRanges:     {!! Js::from($rangesMois) !!},
        };
    </script>

    {{-- ════════ MODALE : DÉTAIL DES INSCRIPTIONS (courbe cliquable) ════════ --}}
    <div id="registrations-modal" x-data="registrationsApp()" x-show="open" x-cloak
        class="fixed inset-0 z-[10000] flex items-start justify-center p-3 sm:p-6 overflow-y-auto">
        <div x-show="open" x-transition.opacity @click="open=false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div x-show="open" x-transition.duration.200ms class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-6xl z-10 my-4 border border-gray-100 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-5 sm:px-7 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white truncate">
                            Inscriptions — <span x-text="periodLabel"></span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <span x-text="total"></span> stagiaire(s) inscrit(s) du
                            <span x-text="fromLabel"></span> au <span x-text="toLabel"></span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button @click="exportCsv()" :disabled="csvLoading"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition disabled:opacity-50">
                        <svg x-show="!csvLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <svg x-show="csvLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Export CSV
                    </button>
                    <button @click="open=false" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-5 sm:px-7 py-4" @keydown.escape.window="open=false">
                <div x-show="loading" class="py-16 text-center">
                    <div class="w-10 h-10 mx-auto rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Chargement des inscriptions…</p>
                </div>

                <div x-show="!loading && error" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Impossible de charger les données.</p>
                    <button @click="loadPage(page)" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                        Réessayer
                    </button>
                </div>

                <div x-show="!loading && !error && rows.length === 0" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aucune inscription sur cette période.</p>
                </div>

                <div x-show="!loading && !error && rows.length > 0" class="overflow-x-auto max-h-[55vh] overflow-y-auto rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="w-full text-sm min-w-[860px]">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Identité</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">École</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Compte</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Inscription</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stages & Domaines</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="r in rows" :key="r.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="font-semibold text-gray-900 dark:text-white" x-text="r.full_name"></div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            <span x-show="r.genre" x-text="r.genre" class="inline-block px-1.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 uppercase"></span>
                                            <span x-show="r.niveau" x-text="'Niv : ' + r.niveau" class="ml-1 text-gray-500 dark:text-gray-400"></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="text-gray-900 dark:text-white" x-text="r.email || '—'"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="r.telephone || '—'"></div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="r.ecole || '—'"></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="accountBadgeClass(r)">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="accountDotClass(r)"></span>
                                            <span x-text="r.account?.label || '—'"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300" x-text="r.created_at || '—'"></td>
                                    <td class="px-5 py-3">
                                        <template x-if="r.stages && r.stages.length">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="(s, si) in r.stages" :key="s.theme + si">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300"
                                                        :title="`${s.theme} — ${s.domaine || 'Sans domaine'} (${s.statut || ''})`">
                                                        <span x-text="s.theme"></span>
                                                        <span x-show="s.domaine" class="text-violet-400" x-text="'· ' + s.domaine"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <span x-show="!r.stages || !r.stages.length" class="text-xs text-gray-400 italic">Aucun stage</span>
                                    </td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <a :href="routeShow(r)" class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Voir la fiche">
                                            <svg class="w-4.5 h-4.5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div x-show="!loading && !error && rows.length > 0" class="mt-4 flex items-center justify-between gap-3 flex-wrap">
                    <p class="text-xs text-gray-500 dark:text-gray-400"
                        x-text="`Page ${page} / ${lastPage} — ${total} stagiaire(s) au total`"></p>
                    <div class="flex gap-2">
                        <button @click="loadPage(page - 1)" :disabled="page <= 1"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            ‹ Précédent
                        </button>
                        <button @click="loadPage(page + 1)" :disabled="page >= lastPage"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Suivant ›
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════ MODALE : DÉTAIL DES TÂCHES (courbe cliquable) ════════ --}}
    <div id="tasks-modal" x-data="tasksApp()" x-show="open" x-cloak
        class="fixed inset-0 z-[10000] flex items-start justify-center p-3 sm:p-6 overflow-y-auto">
        <div x-show="open" x-transition.opacity @click="open=false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div x-show="open" x-transition.duration.200ms class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-7xl z-10 my-4 border border-gray-100 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600">
                <div class="absolute inset-0 opacity-20"
                    style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;)">
                </div>
                <div class="relative px-5 sm:px-7 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center text-white flex-shrink-0 border border-white/20 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-white truncate flex items-center gap-2">
                                Tâches <span x-text="typeLabel()"></span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white border border-white/20" x-text="periodLabel"></span>
                            </h3>
                            <p class="text-xs text-violet-200 mt-0.5">
                                <span x-text="total"></span> tâche(s) · du
                                <span x-text="fromLabel"></span> au <span x-text="toLabel"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0 self-start sm:self-auto">
                        <a :href="trackingUrl()" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 backdrop-blur-sm transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Suivi des tâches
                        </a>
                        <button @click="open=false" class="w-10 h-10 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 backdrop-blur-sm transition flex items-center justify-center" title="Fermer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-4 sm:px-6 py-4" @keydown.escape.window="open=false">
                <div x-show="loading" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Chargement des tâches…</p>
                </div>

                <div x-show="!loading && error" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Impossible de charger les données.</p>
                    <button @click="loadPage(page)" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-violet-600 text-white hover:bg-violet-700 transition">
                        Réessayer
                    </button>
                </div>

                <div x-show="!loading && !error && rows.length === 0" class="py-16 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aucune tâche sur cette période.</p>
                    <p class="mt-1 text-xs text-gray-400">Essayez une autre période en cliquant sur la courbe.</p>
                </div>

                <template x-if="!loading && !error && rows.length > 0">
                    <div>
                        {{-- Search --}}
                        <div class="mb-4 relative">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m1.7-4.3a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                            </svg>
                            <input type="text" x-model="q" placeholder="Rechercher une tâche, un propriétaire, un stage…"
                                class="w-full h-10 pl-10 pr-9 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500 transition">
                            <button x-show="q.length > 0" @click="q = ''"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div x-show="filteredRows().length === 0" class="py-12 text-center">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m1.7-4.3a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Aucun résultat</p>
                            <p class="mt-1 text-xs text-gray-400">Aucune tâche ne correspond à « <span class="font-medium" x-text="q"></span> ».</p>
                        </div>

                        {{-- Cards grid --}}
                        <div x-show="filteredRows().length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5 max-h-[55vh] overflow-y-auto pr-1">
                            <template x-for="r in filteredRows()" :key="r.id">
                                <a :href="r.url"
                                    class="group relative block bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-0.5 hover:border-violet-300 dark:hover:border-violet-600 transition-all duration-300">
                                    {{-- Status color bar --}}
                                    <div class="absolute left-0 top-0 bottom-0 w-1" :class="statusBarClass(r)"></div>

                                    <div class="p-4 pl-5">
                                        {{-- Top row : priority dot + status + arrow --}}
                                        <div class="flex items-center justify-between gap-2 mb-2.5">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full flex-shrink-0" :class="priorityDotClass(r)"></span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium flex items-center gap-1.5"
                                                    :class="statusBadgeClass(r)">
                                                    <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(r)"></span>
                                                    <span x-text="r.status_label"></span>
                                                </span>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-violet-500 group-hover:translate-x-0.5 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>

                                        {{-- Title + owner --}}
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 leading-snug mb-2.5 group-hover:text-violet-700 dark:group-hover:text-violet-300 transition">
                                            <span x-text="r.title"></span>
                                        </p>
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" :style="`background:${avatarColor(r.owner || '—')}`" x-text="initials(r.owner)"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="r.owner || '—'"></span>
                                        </div>

                                        {{-- Meta : stage + date --}}
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <span x-show="r.stage_theme" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 truncate max-w-[60%]"
                                                :title="`${r.stage_theme}${r.etudiant ? ' — ' + r.etudiant : ''}`">
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span x-text="r.stage_theme" class="truncate"></span>
                                            </span>
                                            <span x-show="!r.stage_theme" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                                Sans stage
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[11px] text-gray-400 ml-auto">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span x-text="r.created_at || '—'"></span>
                                            </span>
                                        </div>

                                        {{-- Progress --}}
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500" :class="progressBarClass(r)" :style="`width:${Math.min(100,(r.progress || 0))}%`"></div>
                                            </div>
                                            <span class="text-[11px] font-bold text-gray-600 dark:text-gray-300 font-mono" x-text="(r.progress || 0) + '%'"></span>
                                        </div>

                                        {{-- Assignees --}}
                                        <template x-if="r.assignees && r.assignees.length">
                                            <div class="flex items-center pt-3 border-t border-gray-100 dark:border-gray-700/60">
                                                <div class="flex -space-x-2">
                                                    <template x-for="(a, ai) in r.assignees.slice(0, 3)" :key="a + ai">
                                                        <span class="w-6 h-6 rounded-full ring-2 ring-white dark:ring-gray-800 flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0"
                                                            :style="`background:${avatarColor(a)}`" :title="a" x-text="initials(a)"></span>
                                                    </template>
                                                    <span x-show="r.assignees.length > 3"
                                                        class="w-6 h-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 flex items-center justify-center text-[9px] font-bold flex-shrink-0"
                                                        x-text="'+' + (r.assignees.length - 3)"></span>
                                                </div>
                                                <div class="ml-2 flex flex-wrap gap-1 items-center w-full">
                                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 truncate" x-text="r.assignees.slice(0, 3).map((n, i) => (i === 0 ? n : ' · ' + n)).join('') + (r.assignees.length > 3 ? '…' : '')"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </a>
                            </template>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                x-text="`Page ${page} / ${lastPage} — ${total} tâche(s) au total`"></p>
                            <div class="flex gap-2">
                                <button @click="loadPage(page - 1)" :disabled="page <= 1"
                                    class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Précédent
                                </button>
                                <button @click="loadPage(page + 1)" :disabled="page >= lastPage"
                                    class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    Suivant
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function registrationsApp() {
            return {
                open: false,
                loading: false,
                error: false,
                rows: [],
                total: 0,
                from: null,
                to: null,
                fromLabel: '',
                toLabel: '',
                periodLabel: '',
                page: 1,
                lastPage: 1,
                perPage: 10,
                csvLoading: false,

                init() {},

                async openDetail(from, to, label) {
                    this.from = from;
                    this.to = to;
                    this.periodLabel = label || '';
                    this.page = 1;
                    this.rows = [];
                    this.total = 0;
                    this.lastPage = 1;
                    this.error = false;
                    this.open = true;
                    await this.loadPage(1);
                },

                async loadPage(page) {
                    if (page < 1) return;
                    this.loading = true;
                    this.error = false;
                    try {
                        const url = `/admin/dashboard/inscriptions/details?from=${encodeURIComponent(this.from)}&to=${encodeURIComponent(this.to)}&page=${page}&per_page=${this.perPage}`;
                        const resp = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const data = await resp.json();
                        this.rows = data.data || [];
                        this.total = data.total || 0;
                        this.page = data.pagination?.current_page || page;
                        this.lastPage = data.pagination?.last_page || 1;
                        this.setLabels(data.from, data.to);
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },

                setLabels(from, to) {
                    const fmt = (iso) => {
                        if (!iso) return '';
                        const [y, m, d] = iso.split('-');
                        return `${d}/${m}/${y}`;
                    };
                    this.fromLabel = fmt(from);
                    this.toLabel = fmt(to);
                },

                accountBadgeClass(r) {
                    const map = {
                        active: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                        pending: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                        none: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                    };
                    return map[r.account?.status] || map.none;
                },

                accountDotClass(r) {
                    const map = { active: 'bg-emerald-500', pending: 'bg-amber-500', none: 'bg-gray-400' };
                    return map[r.account?.status] || map.none;
                },

                routeShow(r) {
                    return `/admin/etudiants/${r.id}`;
                },

                async exportCsv() {
                    if (this.csvLoading || !this.from || !this.to) return;
                    this.csvLoading = true;
                    try {
                        const url = `/admin/dashboard/inscriptions/details?from=${encodeURIComponent(this.from)}&to=${encodeURIComponent(this.to)}&page=1&per_page=1000`;
                        const resp = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const data = await resp.json();
                        const rows = data.data || [];
                        const header = ['Identité', 'Email', 'Téléphone', 'Genre', 'École', 'Niveau', 'Inscrit le', 'Compte', 'Stages'];
                        const esc = (v) => '"' + String(v ?? '').replace(/"/g, '""') + '"';
                        const csv = [
                            header.join(';'),
                            ...rows.map(r => [
                                r.full_name,
                                r.email,
                                r.telephone,
                                r.genre,
                                r.ecole,
                                r.niveau,
                                r.created_at,
                                r.account?.label,
                                (r.stages || []).map(s => s.theme + (s.domaine ? ' — ' + s.domaine : '') + (s.statut ? ' (' + s.statut + ')' : '')).join(' | '),
                            ].map(esc).join(';')),
                        ].join('\n');
                        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = `inscriptions_${this.from}_au_${this.to}.csv`;
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        URL.revokeObjectURL(link.href);
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.csvLoading = false;
                    }
                },
            };
        }

        window.openRegistrationsDetails = function (from, to, label) {
            const el = document.getElementById('registrations-modal');
            if (!el) return;
            let data = null;

            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try { data = window.Alpine.$data(el); } catch (e) { data = null; }
            }
            if (!data && el.__x && el.__x.$data) {
                data = el.__x.$data;
            }
            if (!data) {
                console.warn('[Registrations] Alpine composant modale non initialisé.');
                return;
            }
            if (typeof data.openDetail === 'function') {
                data.openDetail(from, to, label);
            }
        };
    </script>

    <script>
        function tasksApp() {
            return {
                open: false,
                loading: false,
                error: false,
                rows: [],
                total: 0,
                from: null,
                to: null,
                fromLabel: '',
                toLabel: '',
                periodLabel: '',
                type: 'created',
                page: 1,
                lastPage: 1,
                perPage: 10,
                q: '',

                typeLabel() {
                    const map = {
                        created: 'créées',
                        in_progress: 'en cours',
                        completed: 'terminées',
                    };
                    return map[this.type] || this.type;
                },

                async openDetail(from, to, label, type) {
                    this.from = from;
                    this.to = to;
                    this.periodLabel = label || '';
                    this.type = type || 'created';
                    this.page = 1;
                    this.rows = [];
                    this.total = 0;
                    this.lastPage = 1;
                    this.error = false;
                    this.q = '';
                    this.open = true;
                    await this.loadPage(1);
                },

                async loadPage(page) {
                    if (page < 1) return;
                    this.loading = true;
                    this.error = false;
                    try {
                        const url = `/admin/dashboard/tasks-details?from=${encodeURIComponent(this.from)}&to=${encodeURIComponent(this.to)}&type=${encodeURIComponent(this.type)}&page=${page}&per_page=${this.perPage}`;
                        const resp = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const data = await resp.json();
                        this.rows = data.data || [];
                        this.total = data.total || 0;
                        this.page = data.pagination?.current_page || page;
                        this.lastPage = data.pagination?.last_page || 1;
                        this.setLabels(data.from, data.to);
                    } catch (e) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },

                setLabels(from, to) {
                    const fmt = (iso) => {
                        if (!iso) return '';
                        const [y, m, d] = iso.split('-');
                        return `${d}/${m}/${y}`;
                    };
                    this.fromLabel = fmt(from);
                    this.toLabel = fmt(to);
                },

                filteredRows() {
                    const q = (this.q || '').trim().toLowerCase();
                    if (!q) return this.rows || [];
                    return (this.rows || []).filter((r) => {
                        const hay = [
                            r.title,
                            r.owner,
                            r.stage_theme,
                            r.etudiant,
                            r.status_label,
                            ...(r.assignees || []),
                        ].filter(Boolean).join(' ').toLowerCase();
                        return hay.includes(q);
                    });
                },

                initials(name) {
                    name = (name || '').trim();
                    if (!name) return '?';
                    const parts = name.split(/\s+/).filter(Boolean);
                    if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
                    return name.slice(0, 2).toUpperCase();
                },

                avatarColor(n) {
                    n = n || '?';
                    let h = 0;
                    for (let i = 0; i < n.length; i++) h = (h * 31 + n.charCodeAt(i)) % 360;
                    return 'hsl(' + h + ' 55% 48%)';
                },

                priorityDotClass(r) {
                    const map = {
                        urgent: 'bg-red-500',
                        high: 'bg-orange-500',
                        normal: 'bg-blue-500',
                        low: 'bg-gray-400',
                    };
                    return map[r.priority] || map.normal;
                },

                statusBarClass(r) {
                    const map = {
                        slate: 'bg-slate-400',
                        blue: 'bg-blue-500',
                        red: 'bg-red-500',
                        amber: 'bg-amber-500',
                        violet: 'bg-violet-500',
                        emerald: 'bg-emerald-500',
                    };
                    return map[r.status_color] || map.slate;
                },

                statusBadgeClass(r) {
                    const map = {
                        slate: 'bg-slate-100 dark:bg-slate-700/40 text-slate-600 dark:text-slate-300',
                        blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                        red: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                        amber: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                        violet: 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300',
                        emerald: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                    };
                    return map[r.status_color] || map.slate;
                },

                statusDotClass(r) {
                    const map = {
                        slate: 'bg-slate-400',
                        blue: 'bg-blue-500',
                        red: 'bg-red-500',
                        amber: 'bg-amber-500',
                        violet: 'bg-violet-500',
                        emerald: 'bg-emerald-500',
                    };
                    return map[r.status_color] || map.slate;
                },

                progressBarClass(r) {
                    const p = r.progress || 0;
                    if (p >= 100) return 'bg-emerald-500';
                    if (p >= 50) return 'bg-blue-500';
                    return 'bg-amber-500';
                },

                trackingUrl() {
                    return `/admin/tasks-tracking?status=${encodeURIComponent(this.statusForTracking())}`;
                },

                statusForTracking() {
                    const map = {
                        created: '',
                        in_progress: 'in_progress',
                        completed: 'completed',
                    };
                    return map[this.type] || '';
                },
            };
        }

        window.openTasksDetails = function (from, to, label, type) {
            const el = document.getElementById('tasks-modal');
            if (!el) return;
            let data = null;

            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try { data = window.Alpine.$data(el); } catch (e) { data = null; }
            }
            if (!data && el.__x && el.__x.$data) {
                data = el.__x.$data;
            }
            if (!data) {
                console.warn('[Tasks] Alpine composant modale non initialisé.');
                return;
            }
            if (typeof data.openDetail === 'function') {
                data.openDetail(from, to, label, type);
            }
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
        let currentPeriod = 'jour';

        function buildInscriptions(labels, data, ranges) {
            const canvas = document.getElementById('chart-inscriptions');
            if (!canvas) return;
            if (inscChart) inscChart.destroy();

            const clickPoint = (evt, items) => {
                let idx = (items && items.length) ? items[0].index : null;

                if (idx === null && inscChart) {
                    const xScale = inscChart.scales.x;
                    const estimated = Math.round(xScale.getValueForPixel(evt.x));
                    if (Number.isFinite(estimated)) idx = estimated;
                }

                if (idx === null || idx < 0) return;
                const range = ranges && ranges[idx];
                if (!range) return;
                if (typeof window.openRegistrationsDetails === 'function') {
                    window.openRegistrationsDetails(range[0], range[1], labels[idx]);
                }
            };

            inscChart = new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: safe(labels, ['—']),
                    datasets: [{
                        label: 'Inscriptions',
                        data: safe(data, [0]),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHitRadius: 15,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: true, mode: 'nearest' },
                    plugins: {
                        legend: { display: false },
                        tooltip: tip()
                    },
                    scales: xyScales(),
                    onClick: clickPoint,
                    onHover: (evt, item) => {
                        evt.native.target.style.cursor = item[0] ? 'pointer' : 'default';
                    }
                }
            });
        }

        window.switchPeriod = function(period) {
            const map = {
                jour:    [D.labelsJour,    D.evolutionJour],
                semaine: [D.labelsSemaine, D.evolutionSemaine],
                mois:    [D.labelsMois,    D.evolutionMois],
            };
            const ranges = {
                jour:    D.rangesJour,
                semaine: D.rangesSemaine,
                mois:    D.rangesMois,
            };
            document.querySelectorAll('[id^="btn-"]').forEach(b => {
                b.classList.remove('bg-blue-500','text-white');
                b.classList.add('text-gray-600');
            });
            const btn = document.getElementById('btn-'+period);
            if (btn) { btn.classList.add('bg-blue-500','text-white'); btn.classList.remove('text-gray-600'); }
            currentPeriod = period;
            buildInscriptions(map[period][0], map[period][1], ranges[period]);
        };
        buildInscriptions(D.labelsJour, D.evolutionJour, D.rangesJour);

        /* ── 2. TYPES ── */
        new Chart(document.getElementById('chart-types')?.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: safe(D.typesLabels,['Vide']),
                datasets: [{ data:safe(D.typesData,[1]), backgroundColor:['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'] }]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{color:TXT, boxWidth:10, padding:8} }, tooltip:tip() } }
        });

        /* ── 3. DOMAINES (donut par domaine) ── */
        new Chart(document.getElementById('chart-domaines')?.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: safe(D.domLabels, ['Vide']),
                datasets: [{
                    data: safe(D.domTotaux, [1]),
                    backgroundColor: (D.domPalette && D.domPalette.length) ? D.domPalette : ['#06b6d4'],
                    borderColor: isDark ? '#1f2937' : '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: Object.assign(tip(), {
                        callbacks: {
                            label: (ctx) => {
                                let total = 0;
                                (ctx.dataset.data || []).forEach(v => total += v);
                                const share = total ? Math.round(ctx.raw / total * 100) : 0;
                                return ' ' + ctx.label + ' : ' + ctx.raw + ' (' + share + '%)';
                            }
                        }
                    })
                }
            }
        });

        /* ── 4. TÂCHES (courbes 12 mois) ── */
        (function() {
            const el = document.getElementById('chart-tasks');
            if (!el) return;

            const typesByDs = ['created', 'in_progress', 'completed'];
            let tasksChart;

            const clickPoint = (evt, items) => {
                let idx = (items && items.length) ? items[0].index : null;
                let ds  = (items && items.length) ? items[0].datasetIndex : null;

                if (idx === null && tasksChart) {
                    const xScale = tasksChart.scales.x;
                    const estimated = Math.round(xScale.getValueForPixel(evt.x));
                    if (Number.isFinite(estimated)) idx = estimated;
                }
                if (ds === null && items && items.length) ds = items[0].datasetIndex;

                if (idx === null || idx < 0) return;
                const range = D.tasksRanges && D.tasksRanges[idx];
                if (!range) return;
                const type = typesByDs[ds] || 'created';
                const label = (D.tasksMoisLabels && D.tasksMoisLabels[idx]) || '';
                if (typeof window.openTasksDetails === 'function') {
                    window.openTasksDetails(range[0], range[1], label, type);
                }
            };

            tasksChart = new Chart(el.getContext('2d'), {
                type: 'line',
                data: {
                    labels: safe(D.tasksMoisLabels, []),
                    datasets: [
                        { label: 'Créées',  data: safe(D.tasksCreated, [0]),    borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.08)', fill: true, tension: 0.4, pointRadius: 4, pointHitRadius: 15, pointHoverRadius: 7, pointBackgroundColor: '#8b5cf6', pointBorderColor: '#ffffff', pointBorderWidth: 1.5 },
                        { label: 'En cours', data: safe(D.tasksInProgress, [0]), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.06)',  fill: false, tension: 0.4, pointRadius: 4, pointHitRadius: 15, pointHoverRadius: 7, pointBackgroundColor: '#3b82f6', pointBorderColor: '#ffffff', pointBorderWidth: 1.5 },
                        { label: 'Terminées', data: safe(D.tasksCompleted, [0]), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.06)',  fill: false, tension: 0.4, pointRadius: 4, pointHitRadius: 15, pointHoverRadius: 7, pointBackgroundColor: '#10b981', pointBorderColor: '#ffffff', pointBorderWidth: 1.5 },
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, interaction: { intersect: true, mode: 'nearest' }, onClick: clickPoint, onHover: (evt, item) => { evt.native.target.style.cursor = item[0] ? 'pointer' : 'default'; }, plugins: { legend: { position: 'bottom', labels: { color: TXT, boxWidth: 10, padding: 8 } }, tooltip: tip() }, scales: xyScales() }
            });
        })();

        /* ── CERCLE 1 : RÉPARTITION UTILISATEURS ── */
        (function() {
            const el = document.getElementById('chart-users-roles');
            if (!el) return;
            const totalUsers = {{ $totalUsers }};
            const dataRoles = [{{ $usersStagiaires }}, {{ $usersEmployes }}, {{ $usersAdmins }} @if($usersAutres > 0), {{ $usersAutres }} @endif];
            const labelsRoles = ['Stagiaires', 'Employés', 'Admins / Superviseurs' @if($usersAutres > 0), 'Autres' @endif];
            const colorsRoles = ['#06b6d4', '#8b5cf6', '#f59e0b', '#9ca3af'];

            const drawCenterText = (ctx, cx, cy, mainTxt, subTxt) => {
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 26px sans-serif';
                ctx.fillStyle = isDark ? '#f9fafb' : '#111827';
                ctx.fillText(mainTxt, cx, cy - 8);
                ctx.font = '11px sans-serif';
                ctx.fillStyle = isDark ? '#9ca3af' : '#6b7280';
                ctx.fillText(subTxt, cx, cy + 14);
                ctx.restore();
            };

            const drawSlicePct = (ctx, chart, dsData) => {
                const total = dsData.reduce((a, b) => a + (b || 0), 0);
                if (!total) return;
                chart.getDatasetMeta(0).data.forEach((arc, i) => {
                    const val = dsData[i];
                    if (!val) return;
                    const pct = val / total * 100;
                    if (pct < 5) return;
                    const pos = arc.tooltipPosition();
                    ctx.save();
                    ctx.font = 'bold 11px sans-serif';
                    ctx.fillStyle = '#fff';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.shadowColor = 'rgba(0,0,0,0.5)';
                    ctx.shadowBlur = 3;
                    ctx.fillText(pct.toFixed(1) + '%', pos.x, pos.y);
                    ctx.restore();
                });
            };

            const pluginRoles = {
                id: 'pluginRoles',
                afterDraw(chart) {
                    const { ctx, chartArea } = chart;
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;
                    drawCenterText(ctx, cx, cy, totalUsers, 'Utilisateurs');
                    drawSlicePct(ctx, chart, dataRoles);
                }
            };

            new Chart(el.getContext('2d'), {
                type: 'doughnut',
                plugins: [pluginRoles],
                data: {
                    labels: labelsRoles,
                    datasets: [{ data: dataRoles, backgroundColor: colorsRoles, borderWidth: 2, borderColor: isDark ? '#1f2937' : '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { color: TXT, boxWidth: 10, padding: 8 } }, tooltip: tip() } }
            });
        })();

        /* ── CERCLE 2 : STATUT COMPTES ── */
        (function() {
            const el = document.getElementById('chart-users-status');
            if (!el) return;
            const totalUsers = {{ $totalUsers }};
            const dataStatus = [{{ $usersActifs }}, {{ $usersInactifs }}];
            const labelsStatus = ['Actifs', 'Inactifs'];
            const colorsStatus = ['#10b981', '#ef4444'];

            const pluginStatus = {
                id: 'pluginStatus',
                afterDraw(chart) {
                    const { ctx, chartArea } = chart;
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;
                    // Centre
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = 'bold 26px sans-serif';
                    ctx.fillStyle = isDark ? '#f9fafb' : '#111827';
                    ctx.fillText(totalUsers, cx, cy - 8);
                    ctx.font = '11px sans-serif';
                    ctx.fillStyle = isDark ? '#9ca3af' : '#6b7280';
                    ctx.fillText('Comptes', cx, cy + 14);
                    ctx.restore();
                    // Pourcentages sur tranches
                    const total = dataStatus.reduce((a, b) => a + (b || 0), 0);
                    if (!total) return;
                    chart.getDatasetMeta(0).data.forEach((arc, i) => {
                        const val = dataStatus[i];
                        if (!val) return;
                        const pct = val / total * 100;
                        if (pct < 5) return;
                        const pos = arc.tooltipPosition();
                        ctx.save();
                        ctx.font = 'bold 11px sans-serif';
                        ctx.fillStyle = '#fff';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.shadowColor = 'rgba(0,0,0,0.5)';
                        ctx.shadowBlur = 3;
                        ctx.fillText(pct.toFixed(1) + '%', pos.x, pos.y);
                        ctx.restore();
                    });
                }
            };

            new Chart(el.getContext('2d'), {
                type: 'doughnut',
                plugins: [pluginStatus],
                data: {
                    labels: labelsStatus,
                    datasets: [{ data: dataStatus, backgroundColor: colorsStatus, borderWidth: 2, borderColor: isDark ? '#1f2937' : '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { color: TXT, boxWidth: 10, padding: 8 } }, tooltip: tip() } }
            });
        })();

    });
    </script>
    @endpush

</x-app-layout>