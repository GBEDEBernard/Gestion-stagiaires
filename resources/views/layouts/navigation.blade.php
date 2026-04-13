@php
$homeRoute = Auth::user()->hasRole('etudiant') ? route('student.stage') : route('dashboard');
@endphp

<nav x-data="{ 
    sidebarOpen: false, 
    stagesOpen: false,
    mobileMenuOpen: false 
}"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 shadow-2xl flex flex-col">

    <!-- Logo et Titre -->
    <div class="flex-shrink-0">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700/50 bg-slate-900/30">
            <a href="{{ $homeRoute }}" class="flex items-center gap-3 group">
                <div class="relative">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG"
                        class="w-12 h-12 rounded-2xl shadow-lg ring-2 ring-blue-500/30 group-hover:ring-blue-500/80 group-hover:scale-110 transition-all duration-300">
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                </div>
                <div class="flex flex-col">
                    <h1 class="text-lg font-bold text-white tracking-wide">Gestion</h1>
                    <p class="text-xs text-slate-400 font-medium">Stagiaires TFG</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Navigation Principale - Zone scrollable -->
    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4">
        <!-- Contenu du menu ici... (inchangé) -->

        @role('etudiant')
        <div class="mb-4 rounded-3xl border border-cyan-500/20 bg-cyan-500/10 p-3 shadow-lg shadow-cyan-950/20">
            <div class="px-3 py-2">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200/80">Espace stagiaire</p>
                <p class="mt-1 text-sm text-slate-300">Les 3 acces utiles pour suivre ton stage sans detour.</p>
            </div>

            <div class="mt-2 space-y-2">
                <a href="{{ route('student.stage') }}"
                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-white transition-all duration-300 hover:bg-white/10">
                    <div class="p-2 rounded-xl bg-cyan-500/20">
                        <svg class="w-5 h-5 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 10l9 4 9-4M3 12l9 4 9-4" />
                        </svg>
                    </div>
                    <span>Mon stage</span>
                </a>

                <a href="{{ route('presence.index') }}"
                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-white transition-all duration-300 hover:bg-white/10">
                    <div class="p-2 rounded-xl bg-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span>Presence</span>
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

        <!-- Menu Présences (Admin + Users) -->
        @can('presence.view')
        <a href="{{ route('presence.index') }}"
            class="flex items-center justify-between px-4 py-3.5 mb-2 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-emerald-600/20 hover:text-emerald-200 transition-all duration-300 group relative overflow-hidden border-l-4 border-emerald-500/30">
            <div class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="p-2 rounded-xl bg-emerald-500/20">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span>Pointage Présence</span>
            </div>
            @if(auth()->user()->can('presence.admin.view') && ($anomaliesCount ?? 0) > 0)
            <span class="px-2.5 py-0.5 text-xs font-bold bg-rose-500 text-white rounded-full shadow-lg animate-pulse">
                {{ $anomaliesCount }}
            </span>
            @endif
        </a>
        @endcan

        <!-- Menu Stages avec Sous-menu (Admin only) -->
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
        @endcan

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
        @endcan

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
    <div class="flex-shrink-0 p-4 border-t border-slate-700/50 bg-slate-900/50 backdrop-blur-sm" x-data="{ userMenuOpen: false }">
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

<!-- Bouton Mobile pour ouvrir sidebar -->
<button @click="sidebarOpen = !sidebarOpen"
    class="lg:hidden fixed top-4 left-4 z-50 p-3 rounded-xl bg-slate-800/90 backdrop-blur-sm text-white shadow-xl hover:bg-slate-700 transition-all duration-300 hover:scale-105">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<!-- Overlay pour mobile -->
<div x-show="sidebarOpen" @click="sidebarOpen = false"
    class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-30 transition-all duration-300"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
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