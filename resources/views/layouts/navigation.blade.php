@php
$homeRoute = Auth::user()->hasRole('etudiant') ? route('student.stage') : route('dashboard');
@endphp

<div x-data="{ sidebarOpen: false }">

    <!-- BOUTON MENU -->
    <button
        x-show="!sidebarOpen"
        @click="sidebarOpen = true"
        class="lg:hidden fixed top-4 left-4 z-[60] p-3 bg-slate-900 text-white rounded-xl"
        x-transition>
        ☰
    </button>

    <nav
        class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-900
        transform transition duration-300 flex flex-col"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Bouton fermer -->
        <button
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="lg:hidden absolute top-8 right-2 z-[60] p-1 text-white bg-blue-700 rounded-full"
            x-transition>
            ✕
        </button>
        <!-- Logo et Titre -->
        <div class="flex-shrink-0">
            <div class="flex items-center gap-3 px-6 py-6 bg-gradient-to-b from-slate-900/95 to-slate-950/70 backdrop-blur-md shadow-2xl">
                <a href="{{ $homeRoute }}" class="flex items-center gap-3 group">
                    <div class="relative">
                        <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG"
                            class="w-12 h-12 rounded-3xl shadow-2xl ring-2 ring-emerald-400/60 group-hover:ring-emerald-300/90 group-hover:scale-110 transition-all duration-400">
                        <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full animate-ping shadow-lg"></div>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-xl font-black text-white tracking-tight bg-gradient-to-r from-white to-slate-100 bg-clip-text">Gestion Pro</h1>
                        <p class="text-sm text-emerald-300 font-bold tracking-wide">Administrative TFG</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Navigation Principale - Zone scrollable -->
        <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4">
            @role('etudiant')

            <div class="rounded-3xl bg-gradient-to-br from-cyan-500/10 to-cyan-900/20 p-3 shadow-2xl shadow-cyan-950/30 backdrop-blur-sm">
                <div class="px-3 py-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200/80">Espace stagiaire</p>
                    <p class="mt-1 text-sm text-slate-300">Les 3 acces utiles pour suivre ton stage sans detour.</p>
                </div>

                <div class="mt-2 space-y-2">
                    <a href="{{ route('presence.pointage') }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-white transition-all duration-300 hover:bg-white/10 {{ request()->routeIs('presence.pointage') ? 'bg-emerald-500/30 ring-2 ring-emerald-400/50 !text-emerald-100 scale-105' : '' }}" aria-current="page">
                        <div class="p-2 rounded-xl bg-emerald-500/20">
                            <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span>Pointage</span>
                    </a>

                    <a href="{{ route('presence.historique') }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-white transition-all duration-300 hover:bg-white/10">
                        <div class="p-2 rounded-xl bg-blue-500/20">
                            <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span>Historique</span>
                    </a>

                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-white transition-all duration-300 hover:bg-white/10">
                        <div class="p-2 rounded-xl bg-amber-500/20">
                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span>Rapport journalier</span>
                    </a>
                </div>
            </div>
            @endrole

            @unless(auth()->user()->hasRole('etudiant'))
            <div class="mb-6 rounded-3xl bg-gradient-to-br from-slate-900/60 to-slate-950/40 p-5 shadow-2xl shadow-emerald-950/20 backdrop-blur-sm">

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superviseur'))
                <div class="space-y-1.5">
                    @can('presence.admin.view')
                    <a href="{{ route('admin.presence.index') }}"
                        class="flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-bold text-emerald-200 hover:bg-emerald-500/20 hover:text-emerald-100 hover:shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 bg-slate-800/30">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-emerald-500/30 ring-1 ring-emerald-500/40">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span>Pointage Admin</span>
                        </div>
                        @if(($anomaliesCount ?? 0) > 0)
                        <span class="px-2.5 py-1 text-xs font-bold bg-gradient-to-r from-rose-500 to-red-500 text-white rounded-full shadow-lg animate-pulse ring-1 ring-rose-400/50">{{ $anomaliesCount }}</span>
                        @endif
                    </a>
                    @endcan

                    {{-- NEW PRÉSENCE DROPDOWN --}}
                    @can('presence.admin.view')
                    <div x-data="{ presenceOpen: false }" class="space-y-1">
                        <button @click="presenceOpen = !presenceOpen" type="button"
                            class="group w-full flex items-center justify-between px-5 py-4 rounded-2xl text-sm font-bold text-amber-200 hover:bg-gradient-to-r from-amber-500/20 to-orange-500/20 hover:text-amber-100 hover:shadow-xl hover:shadow-amber-500/30 transition-all duration-400 bg-slate-800/40 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-400/10 to-orange-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-400 -skew-x-12"></div>
                            <div class="flex items-center gap-4 relative z-10">
                                <div class="p-2.5 rounded-xl bg-gradient-to-br from-amber-400/30 to-orange-400/30 ring-1 ring-amber-500/50">
                                    <svg class="w-5 h-5 text-amber-500 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 012 2h-4a2 2 0 012-2z" />
                                    </svg>
                                </div>
                                <span class="text-white text-xl font-bold">Présence</span>
                                <span class="px-3 py-1 text-xs bg-amber-500/30 text-gray-200 font-bold rounded-full ring-1 ring-amber-800/50">Admin</span>
                            </div>
                            <svg x-bind:class="presenceOpen ? 'rotate-180' : 'rotate-0'" class="w-5 h-5 text-amber-300 transform transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="presenceOpen" x-cloak="presenceOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="ml-4 space-y-1.5 pl-3 border-l-2 border-amber-500/30 bg-slate-900/50 rounded-2xl p-3">
                            <a href="{{ route('admin.presence.anomalies') }}"
                                class="block pl-3 pr-4 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:bg-rose-600/20 hover:text-rose-200 hover:translate-x-1 transition-all duration-300 relative bg-slate-800/20 before:absolute before:left-2 before:top-1/2 before:-translate-y-1/2 before:w-1.5 before:h-1.5 before:rounded-full before:bg-rose-500/50">
                                🚨 Tableau Anomalies
                                @if(($anomaliesCount ?? 0) > 0)
                                <span class="ml-2 inline-flex px-2 py-1 text-xs font-bold bg-gradient-to-r from-rose-500 to-red-500 text-white rounded-full shadow-lg ml-auto float-right">{{ $anomaliesCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.presence.pointage-suivi') ?? route('admin.presence.index') }}"
                                class="block pl-3 pr-4 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:bg-emerald-600/20 hover:text-emerald-200 hover:translate-x-1 transition-all duration-300 relative bg-slate-800/20 before:absolute before:left-2 before:top-1/2 before:-translate-y-1/2 before:w-1.5 before:h-1.5 before:rounded-full before:bg-emerald-500/50">
                                📊 Suivi Pointages
                            </a>
                        </div>
                    </div>
                    @endcan

                    <div x-data="{ open: false }" class="space-y-1">
                        <button @click="open = !open" type="button"
                            class="group w-full flex items-center justify-between px-5 py-4 rounded-2xl text-sm font-bold text-slate-200 hover:bg-gradient-to-r from-emerald-600/20 to-blue-600/20 hover:text-emerald-100 hover:shadow-xl hover:shadow-emerald-500/30 transition-all duration-400 bg-slate-800/40 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-400 -skew-x-12"></div>
                            <div class="flex items-center gap-4 relative z-10">
                                <div class="p-2.5 rounded-xl bg-gradient-to-br from-emerald-400/30 to-blue-400/30 ring-1 ring-emerald-500/50">
                                    <svg class="w-5 h-5 text-emerald-500 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                                    </svg>
                                </div>
                                <span class="text-white text-xl font-bold">Suivi</span>
                                <span class="px-3 py-1 text-xs bg-emerald-500/30 text-gray-200 font-bold rounded-full ring-1 ring-emerald-800/50">Pro</span>
                            </div>
                            <svg x-bind:class="open ? 'rotate-180' : 'rotate-0'" class="w-5 h-5 text-emerald-300 transform transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                       <div x-show="open" x-cloak="open" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 -translate-y-2" 
     x-transition:enter-end="opacity-100 translate-y-0" 
     class="ml-4 space-y-1.5 pl-3 border-l-2 border-emerald-500/30 bg-slate-900/50 rounded-2xl p-3">

    @can('presence.view')
    <a href="{{ route('attendance.tracking.index') }}"
       class="group flex items-center gap-3 pl-3 pr-4 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:bg-blue-600/20 hover:text-blue-200 hover:translate-x-1 transition-all duration-300 relative overflow-hidden">
        <div class="w-7 h-7 flex items-center justify-center bg-blue-500/10 text-blue-400 rounded-lg group-hover:bg-blue-500/20 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276L20 10V18H4V6L8.553 3.724 12 6 15.447 3.724 20 6" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 18l3-3m0 0l3 3m-3-3l3-3" />
            </svg>
        </div>
        <span>Suivi Pointages</span>
    </a>
    @endcan

    @can('daily_reports.view')
    <a href="{{ route('admin.reports.index') }}"
       class="group flex items-center gap-3 pl-3 pr-4 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:bg-amber-600/20 hover:text-amber-200 hover:translate-x-1 transition-all duration-300 relative overflow-hidden">
        <div class="w-7 h-7 flex items-center justify-center bg-amber-500/10 text-amber-400 rounded-lg group-hover:bg-amber-500/20 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-9 9-4-4-6 6" />
            </svg>
        </div>
        <span>Suivi Rapports</span>
    </a>
    @endcan

    @can('daily_reports.view')
    <a href="{{ route('admin.presence.anomalies') }}"
       class="group flex items-center gap-3 pl-3 pr-4 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:bg-rose-600/20 hover:text-rose-200 hover:translate-x-1 transition-all duration-300 relative overflow-hidden">
        <div class="w-7 h-7 flex items-center justify-center bg-rose-500/10 text-rose-400 rounded-lg group-hover:bg-rose-500/20 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <span>Anomalies de Présence</span>
    </a>
    @endcan

    @can('daily_reports.view')
    <a href="{{ route('admin.presence.index') }}"
       class="group flex items-center gap-3 pl-3 pr-4 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:bg-emerald-600/20 hover:text-emerald-200 hover:translate-x-1 transition-all duration-300 relative overflow-hidden">
        <div class="w-7 h-7 flex items-center justify-center bg-emerald-500/10 text-emerald-400 rounded-lg group-hover:bg-emerald-500/20 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1v-5m10-10l2 2m-2-2v10a1 1 0 01-1 1v-5m-6 0a1 1 0 001-1v5" />
            </svg>
        </div>
        <span>Statistiques Globales</span>
    </a>
    @endcan

</div>
                </div>
                @else
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" type="button"
                        class="group w-full flex items-center justify-between px-5 py-4 mb-3 rounded-2xl text-sm font-bold text-slate-200 hover:bg-gradient-to-r from-emerald-600/20 to-blue-600/20 hover:text-emerald-100 hover:shadow-xl hover:shadow-emerald-500/30 transition-all duration-400 border border-slate-700/30 bg-slate-800/40 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-400 -skew-x-12"></div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="p-2.5 rounded-xl bg-gradient-to-br from-emerald-400/30 to-blue-400/30 ring-1 ring-emerald-500/50">
                                <svg class="w-5 h-5 text-emerald-300 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                                </svg>
                            </div>
                            <span>Suivi</span>
                            <span class="px-3 py-1 text-xs bg-emerald-500/30 text-emerald-200 font-bold rounded-full ring-1 ring-emerald-400/50">Pro</span>
                        </div>
                        <svg x-bind:class="open ? 'rotate-180' : 'rotate-0'" class="w-5 h-5 text-emerald-300 transform transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="ml-4 space-y-1.5 pl-3 border-l-2 border-emerald-500/30 bg-slate-900/50 rounded-2xl p-3">
                        @can('presence.view')
                        <a href="{{ route('presence.pointage') }}"
                            class="flex items-center gap-3 pl-3 pr-4 py-3.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-emerald-600/20 hover:text-emerald-200 hover:translate-x-1 transition-all duration-300 border border-slate-700/20 bg-slate-800/20 shadow-sm hover:shadow-emerald-500/30">
                            <div class="p-2 rounded-xl bg-emerald-500/30 ring-1 ring-emerald-500/40">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m-6 0h2m-2 0v2" />
                                </svg>
                            </div>
                            Pointage Présence
                        </a>
                        @endcan

                        @can('daily_reports.view')
                        <a href="{{ route('reports.index') }}"
                            class="flex items-center gap-3 pl-3 pr-4 py-3.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-amber-600/20 hover:text-amber-200 hover:translate-x-1 transition-all duration-300 border border-slate-700/20 bg-slate-800/20 shadow-sm hover:shadow-amber-500/30">
                            <div class="p-2 rounded-xl bg-amber-500/30 ring-1 ring-amber-500/40">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            Rapports de travail
                        </a>
                        @endcan

                        @can('presence.view')
                        <a href="{{ route('presence.historique') }}"
                            class="flex items-center gap-3 pl-3 pr-4 py-3.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-blue-600/20 hover:text-blue-200 hover:translate-x-1 transition-all duration-300 border border-slate-700/20 bg-slate-800/20 shadow-sm hover:shadow-blue-500/30">
                            <div class="p-2 rounded-xl bg-blue-500/30 ring-1 ring-blue-500/40">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span>Historique</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif
            </div>
            @endunless

            <!-- Menu Stages - ADMIN SEULEMENT -->
            @role('admin')
            @canany(['stages.view', 'type_stages.view', 'services.view', 'sites.view', 'tasks.view', 'signataires.view', 'jour_stage.view'])
            <div class="mb-3" x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden"
                    :class="open ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                    <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm"
                            :class="open ? 'bg-white/20' : 'bg-slate-700/50'">
                            <svg class="w-5 h-5" :class="open ? 'text-white' : 'text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span>Stages</span>
                        @if($stagesCount > 0)
                        <span class="px-2.5 py-0.5 text-xs font-bold bg-blue-500 text-white rounded-full shadow-lg animate-pulse">
                            {{ $stagesCount }}
                        </span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="open ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                    </svg>
                </button>

                <!-- Sous-menu Stages -->
                <div x-show="open" x-collapse @click.outside="open = false"
                    class="mt-3 ml-4 space-y-2 overflow-hidden">
                    @can('stages.view')
                    <a href="{{ route('stages.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 group-hover:bg-blue-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>Liste des stages</span>
                    </a>
                    @endcan
                    @can('type_stages.view')
                    <a href="{{ route('type_stages.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500 group-hover:bg-purple-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Types de stage</span>
                    </a>
                    @endcan
                    @can('services.view')
                    <a href="{{ route('services.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 group-hover:bg-green-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-green-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Services</span>
                    </a>
                    @endcan

                    @can('signataires.view')
                    <a href="{{ route('signataires.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-orange-500 group-hover:bg-orange-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Signataires</span>
                    </a>
                    @endcan
                    @can('jour_stage.view')
                    <a href="{{ route('jours.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-yellow-500 group-hover:bg-yellow-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Jours</span>
                    </a>
                    @endcan
                    @can('stages.view')
                    <a href="{{ route('stages.trash') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-400/80 hover:text-red-300 hover:bg-red-500/10 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 group-hover:bg-red-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-red-500/50 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Corbeille</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany
            @endrole

            <!-- Employés avec Sous-menu -->
            @canany(['domaines.view', 'users.view'])
            <div class="mb-3" x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden"
                    :class="open ? 'bg-gradient-to-r from-cyan-600 to-cyan-700 text-white shadow-lg shadow-cyan-600/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                    <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm"
                            :class="open ? 'bg-white/20' : 'bg-slate-700/50'">
                            <svg class="w-5 h-5" :class="open ? 'text-white' : 'text-cyan-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span>Employés</span>
                    </div>
                    <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="open ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                    </svg>
                </button>

                <!-- Sous-menu Employés -->
                <div x-show="open" x-collapse @click.outside="open = false"
                    class="mt-3 ml-4 space-y-2 overflow-hidden">
                    @can('domaines.view')
                    <a href="{{ route('domaines.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-cyan-500 group-hover:bg-cyan-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Gérer Domaines</span>
                    </a>
                    @endcan
                    @foreach($domaines as $domaine)
                    <a href="{{ route('employes.by_domaine', $domaine) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 group-hover:bg-blue-400 transition-colors"></div>
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ $domaine->nom }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endcanany
            <!-- Étudiants -->
            @can('etudiants.view')
            @unlessrole('etudiant')
            <a href="{{ route('etudiants.index') }}"
                class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="p-2 rounded-xl bg-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
                        </svg>
                    </div>
                    <span>Étudiants</span>
                </div>
                @if($etudiantsCount > 0)
                <span class="px-2.5 py-0.5 text-xs font-bold bg-emerald-500/20 text-emerald-400 rounded-full border border-emerald-500/30">
                    {{ $etudiantsCount }}
                </span>
                @endif
            </a>
            @endunlessrole
            @endcan
            <!-- Badges -->
            @can('badges.view')
            @unlessrole('etudiant')
            <a href="{{ route('badges.index') }}"
                class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="p-2 rounded-xl bg-purple-500/20">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m0 0c-1.745-2.772-2.747-6.054-2.747-9.571m5.5 0c0 3.517 1.009 6.799 2.753 9.571m0 0c1.745-2.772 2.747-6.054 2.747-9.571M12 11a9 9 0 00-9 9m0 0a9 9 0 018.354 5.646M12 11a9 9 0 019 9m-9-9a9 9 0 018.354 5.646" />
                        </svg>
                    </div>
                    <span>Badges</span>
                </div>
                @if($badgesCount > 0)
                <span class="px-2.5 py-0.5 text-xs font-bold bg-purple-500/20 text-purple-400 rounded-full border border-purple-500/30">
                    {{ $badgesCount }}
                </span>
                @endif
            </a>
            @endunlessrole
            @endcan

            <!-- Utilisateurs (Admin only) -->
            @role('admin')
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="p-2 rounded-xl bg-orange-500/20">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span>Utilisateurs</span>
                </div>
                @if($usersCount > 0)
                <span class="px-2.5 py-0.5 text-xs font-bold bg-orange-500/20 text-orange-400 rounded-full border border-orange-500/30">
                    {{ $usersCount }}
                </span>
                @endif
            </a>
            @endrole

            <!-- Rôles (Admin only) -->
            @role('admin')
            <a href="{{ route('admin.roles.index') }}"
                class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="p-2 rounded-xl bg-indigo-500/20">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span>Rôles</span>
                </div>
                @if($rolesCount > 0)
                <span class="px-2.5 py-0.5 text-xs font-bold bg-indigo-500/20 text-indigo-400 rounded-full border border-indigo-500/30">
                    {{ $rolesCount }}
                </span>
                @endif
            </a>
            @endrole

            <!-- Corbeille Globale (Admin only) -->
            @can('corbeille.view')
            <a href="{{ route('corbeille.index') }}"
                class="flex items-center justify-between px-4 py-3.5 mb-4 rounded-2xl text-sm font-semibold text-red-400/80 hover:bg-red-500/10 hover:text-red-300 transition-all duration-300 group relative overflow-hidden">
                <div class="absolute inset-0 bg-red-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="p-2 rounded-xl bg-red-500/20">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <span>Corbeille</span>
                </div>
                @if($trashCount > 0)
                <span class="px-2.5 py-0.5 text-xs font-bold bg-red-500/20 text-red-400 rounded-full border border-red-500/30 animate-pulse">
                    {{ $trashCount }}
                </span>
                @endif
            </a>
            @endcan
            @can('sites.view')
            <a href="{{ route('sites.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                <div class="w-1.5 h-1.5 rounded-full bg-cyan-500 group-hover:bg-cyan-400 transition-colors"></div>
                <svg class="w-4 h-4 text-slate-500 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Sites</span>
            </a>
            @endcan
            @can('tasks.view')
            <a href="{{ route('tasks.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                <div class="w-1.5 h-1.5 rounded-full bg-amber-500 group-hover:bg-amber-400 transition-colors"></div>
                <svg class="w-4 h-4 text-slate-500 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V9m-7 6h.01M12 3l7 7m-7-7v4a3 3 0 003 3h4" />
                </svg>
                <span>Taches</span>
            </a>
            @endcan
            <!-- Divider -->
            @can('dashboard.view')
            @unlessrole('etudiant')
            <div class="border-t border-slate-700/50 my-3"></div>

            <!-- Lien Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="p-2 rounded-xl bg-cyan-500/20">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <span>Dashboard</span>
                </div>
                <svg class="w-4 h-4 text-slate-500 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            @endunlessrole
            @endcan
        </div>

        <!-- Profil Utilisateur en bas du Sidebar -->
        <div class="flex-shrink-0 p-5 bg-gradient-to-t from-slate-900/90 to-transparent backdrop-blur-xl" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen"
                class="w-full flex items-center gap-3 px-3 py-3 rounded-2xl hover:bg-slate-800/60 transition-all duration-300 group">
                @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                    class="w-10 h-10 rounded-2xl object-cover shadow-lg group-hover:scale-110 transition-transform duration-300">
                @else
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white text-sm font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                @endif
                <div class="flex-1 text-left">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 transform transition-transform duration-300" :class="userMenuOpen ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                </svg>
            </button>

            <!-- Menu Utilisateur -->
            <div x-show="userMenuOpen" x-collapse @click.outside="userMenuOpen = false"
                class="mt-3 py-2 bg-slate-800/80 backdrop-blur-sm rounded-2xl overflow-hidden shadow-xl">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 transition-all duration-200 group">
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    <span>Paramètres</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-200 group">
                        <svg class="w-4 h-4 text-red-500 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <!-- 🔥 OVERLAY ICI EXACTEMENT -->
    <div x-show="sidebarOpen"
        @click="sidebarOpen = false"
        class="lg:hidden fixed inset-0 bg-black/60 z-30"
        x-transition>
    </div>

</div>


<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
    }
</style>