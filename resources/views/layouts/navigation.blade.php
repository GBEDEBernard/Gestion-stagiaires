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
        <div class="flex-shrink-0 border-b border-slate-800/40">

            <a href="{{ $homeRoute }}"
                class="flex items-center gap-3 px-5 py-5 group hover:bg-slate-800/20 transition">

                <!-- Logo -->
                <div class="relative">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG"
                        class="w-10 h-10 rounded-xl object-cover transition-transform duration-300 group-hover:scale-105">

                    <!-- petit statut discret -->
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                </div>

                <!-- Texte -->
                <div class="flex flex-col leading-tight">
                    <h1 class="text-base font-semibold text-white tracking-tight">
                        @if(auth()->user()->hasRole('admin'))
                        Gestions
                        @else
                        Gestions
                        @endif
                    </h1>
                    <p class="text-xs text-slate-400">
                        @if(auth()->user()->hasRole('admin'))
                        Administrative TFG
                        @else
                        Pointage et rapports TFG
                        @endif
                    </p>
                </div>

            </a>
        </div>

        <!-- Navigation Principale - Zone scrollable -->
        <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4">
            @role('etudiant')

            <div class="rounded-3xl bg-gradient-to-br from-cyan-500/5 to-cyan-900/10 p-3 shadow-md shadow-black/5 backdrop-blur-sm">

                <div class="mt-2 space-y-2">

                    {{-- Pointage --}}
                    <a href="{{ route('presence.pointage') }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200
                {{ request()->routeIs('presence.pointage') 
                    ? 'bg-emerald-500/20 text-emerald-100' 
                    : 'text-white/80 hover:bg-white/5 hover:text-white' }}">

                        <div class="p-2 rounded-xl bg-emerald-500/10">
                            <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span>Pointage</span>
                    </a>

                    {{-- Historique --}}
                    <a href="{{ route('presence.historique') }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200
                {{ request()->routeIs('presence.historique') 
                    ? 'bg-blue-500/20 text-blue-100' 
                    : 'text-white/80 hover:bg-white/5 hover:text-white' }}">

                        <div class="p-2 rounded-xl bg-blue-500/10">
                            <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 5h6m2 0a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2m2 0v2h4V5" />
                            </svg>
                        </div>
                        <span>Historique</span>
                    </a>

                    {{-- Rapport --}}
                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200
                {{ request()->routeIs('reports.*') 
                    ? 'bg-amber-500/20 text-amber-100' 
                    : 'text-white/80 hover:bg-white/5 hover:text-white' }}">

                        <div class="p-2 rounded-xl bg-amber-500/10">
                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14l-5-3-5 3V6a2 2 0 012-2z" />
                            </svg>
                        </div>
                        <span>Rapport journalier</span>
                    </a>

                    {{-- Mon Stage --}}
                    <a href="{{ route('student.stage') }}"
                        class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200
                {{ request()->routeIs('student.stage') 
                    ? 'bg-indigo-500/20 text-indigo-100' 
                    : 'text-white/80 hover:bg-white/5 hover:text-white' }}">

                        <div class="p-2 rounded-xl bg-indigo-500/10">
                            <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 7V6a3 3 0 013-3h0a3 3 0 013 3v1m5 0H4a2 2 0 00-2 2v2a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM4 13h16v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5z" />
                            </svg>
                        </div>
                        <span>Mon Stage</span>
                    </a>

                </div>
            </div>

            @endrole

            @unless(auth()->user()->hasRole('etudiant'))
            <div class="mb-6 rounded-3xl bg-gradient-to-br from-slate-900/60 to-slate-950/40 p-5 shadow-2xl shadow-emerald-950/20 backdrop-blur-sm">

                @if(auth()->user()->hasRole('superviseur') || auth()->user()->hasRole('admin'))
                <div class="space-y-1.5">
                    <h2 class="text-sm font-bold text-center text-slate-400 uppercase tracking-wide">Administration</h2>
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

                <!-- Gestion Personnel avec Sous-menu -->
                @canany(['domaines.view', 'users.view', 'sites.view', 'tasks.view'])
                <div class="mb-3" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden"
                        :class="open ? 'bg-gradient-to-r from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-900/40' : 'text-slate-300 hover:bg-blue-800/60 hover:text-white'">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm"
                                :class="open ? 'bg-white/20' : 'bg-slate-700/50'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-blue-400 bi bi-people" viewBox="0 0 16 16">
                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                                </svg>

                            </div>

                            <span>Gestion des <br>Personnels</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="open ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>

                    <!-- Sous-menu Gestion Personnel -->
                    <div x-show="open" x-collapse @click.outside="open = false"
                        class="mt-3 ml-4 space-y-2 overflow-hidden">
                        @can('domaines.view')
                        <a href="{{ route('domaines.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-500 group-hover:bg-slate-400 transition-colors"></div>

                            <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <div class="flex flex-col">
                                <span>Domaines</span>
                                <p class="text-xs text-slate-500 group-hover:text-slate-400">Groupes employés</p>
                            </div>
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
                        @role('admin')
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500 group-hover:bg-orange-400 transition-colors"></div>

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                            <div class="flex flex-col">
                                <span>Utilisateurs</span>
                                <p class="text-xs text-slate-500 group-hover:text-slate-400">Gestion comptes</p>
                            </div>

                        </a>
                        @endrole
                        @can('sites.view')
                        <a href="{{ route('sites.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-cyan-500 group-hover:bg-cyan-400 transition-colors"></div>

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-europe-africa-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0m0 1a6.97 6.97 0 0 0-4.335 1.505l-.285.641a.847.847 0 0 0 1.48.816l.244-.368a.81.81 0 0 1 1.035-.275.81.81 0 0 0 .722 0l.262-.13a1 1 0 0 1 .775-.05l.984.34q.118.04.243.054c.784.093.855.377.694.801a.84.84 0 0 1-1.035.487l-.01-.003C8.273 4.663 7.747 4.5 6 4.5 4.8 4.5 3.5 5.62 3.5 7c0 3 1.935 1.89 3 3 1.146 1.194-1 4 2 4 1.75 0 3-3.5 3-4.5 0-.704 1.5-1 1-2.5-.097-.291-.396-.568-.642-.756-.173-.133-.206-.396-.051-.55a.334.334 0 0 1 .42-.043l1.085.724a.276.276 0 0 0 .348-.035c.15-.15.414-.083.488.117.16.428.445 1.046.847 1.354A7 7 0 0 0 8 1" />
                            </svg>

                            <div class="flex flex-col">
                                <span>Sites</span>
                                <p class="text-xs text-slate-500 group-hover:text-slate-400">Lieux & géofencing</p>
                            </div>
                        </a>
                        @endcan
                        @role('admin')
                        <a href="{{ route('tasks.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500 group-hover:bg-amber-400 transition-colors"></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                            </svg>
                            <div class="flex flex-col">
                                <span>Tâches</span>
                                <p class="text-xs text-slate-500 group-hover:text-slate-400">Système & automation</p>
                            </div>
                        </a>
                        @endrole
                    </div>
                </div>
                @endcanany
                <!-- Étudiants -->
                @can('etudiants.view')
                @unlessrole('etudiant')
                <a href="{{ route('etudiants.index') }}"
                    class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-emerald-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="p-2 rounded-xl bg-emerald-500/20">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-walking" viewBox="0 0 16 16">
                                <path d="M9.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0M6.44 3.752A.75.75 0 0 1 7 3.5h1.445c.742 0 1.32.643 1.243 1.38l-.43 4.083a1.8 1.8 0 0 1-.088.395l-.318.906.213.242a.8.8 0 0 1 .114.175l2 4.25a.75.75 0 1 1-1.357.638l-1.956-4.154-1.68-1.921A.75.75 0 0 1 6 8.96l.138-2.613-.435.489-.464 2.786a.75.75 0 1 1-1.48-.246l.5-3a.75.75 0 0 1 .18-.375l2-2.25Z" />
                                <path d="M6.25 11.745v-1.418l1.204 1.375.261.524a.8.8 0 0 1-.12.231l-2.5 3.25a.75.75 0 1 1-1.19-.914zm4.22-4.215-.494-.494.205-1.843.006-.067 1.124 1.124h1.44a.75.75 0 0 1 0 1.5H11a.75.75 0 0 1-.531-.22Z" />
                            </svg>
                        </div>
                        <span>Étudiants</span>
                    </div>

                </a>
                @endunlessrole
                @endcan
                <!-- Badges -->
                @can('badges.view')
                @unlessrole('etudiant')
                <a href="{{ route('badges.index') }}"
                    class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-purple-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="p-2 rounded-xl bg-purple-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge text-purple-400" viewBox="0 0 16 16">
                                <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z" />
                            </svg>

                        </div>
                        <span>Badges</span>
                    </div>

                </a>
                @endunlessrole
                @endcan

                <!-- Rôles (Admin only) -->
                @role('admin')
                <a href="{{ route('admin.roles.index') }}"
                    class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-indigo-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="p-2 rounded-xl bg-indigo-500/20">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span>Rôles</span>
                    </div>

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

                </a>
                @endcan
                <!-- Divider -->
                @can('dashboard.view')
                @unlessrole('etudiant')
                <div class="border-t border-slate-700/50 my-3"></div>

                <!-- Lien Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-cyan-800/60 hover:text-white transition-all duration-300 group relative overflow-hidden">
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

            <!-- Profil Utilisateur -->
            <div class="p-4 border-t border-slate-800/40" x-data="{ userMenuOpen: false }">

                <!-- Bouton profil -->
                <button @click="userMenuOpen = !userMenuOpen"
                    class="w-full flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-slate-800/30 transition">

                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                        class="w-9 h-9 rounded-full object-cover">
                    @else
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-white text-sm font-medium">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    @endif

                    <div class="flex-1 text-left min-w-0">
                        <p class="text-sm text-white truncate">
                            {{ Auth::user()->name }}
                        </p>

                    </div>

                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-200"
                        :class="userMenuOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Menu -->
                <div x-show="userMenuOpen"
                    x-transition
                    @click.outside="userMenuOpen = false"
                    class="mt-2 rounded-xl bg-slate-900/60 border border-slate-800/40 overflow-hidden">

                    <!-- Profil -->
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800/40 transition">
                        Paramètres
                    </a>

                    <!-- Déconnexion -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition">
                            Déconnexion
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