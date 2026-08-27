@php
$homeRoute = Auth::user()->hasRole('etudiant') ? route('student.stage') : route('dashboard');
$hasMonEspace = (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superviseur'))
    && auth()->user()->hasAnyRole(['employe', 'fonctionnaire']);
$navDefault = null;
if (request()->routeIs('attendance.tracking.index') || request()->routeIs('admin.presence.*')
    || request()->routeIs('admin.holidays.*') || request()->routeIs('reports.*')
    || request()->routeIs('admin.tasks.tracking')) {
    $navDefault = 'presence';
} elseif (request()->routeIs('personnels.*') || request()->routeIs('etudiants.*')
    || request()->routeIs('employes.*') || request()->routeIs('badges.*')) {
    $navDefault = 'personnes';
} elseif (request()->routeIs('stages.*') || request()->routeIs('type_stages.*')
    || request()->routeIs('signataires.*') || request()->routeIs('jours.*')
    || request()->routeIs('ecoles.*')) {
    $navDefault = 'stages';
} elseif (request()->routeIs('sites.*') || request()->routeIs('domaines.*')
    || request()->routeIs('tasks.*')) {
    $navDefault = 'organisation';
} elseif (request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')
    || request()->routeIs('admin.logs.*') || request()->routeIs('admin.notifications.urgent.*')) {
    $navDefault = 'access';
} elseif (request()->routeIs('presence.*') || request()->routeIs('permissions.*')) {
    $navDefault = $hasMonEspace ? 'espace' : 'presence';
}
@endphp

{{-- Cette valeur est calculée côté serveur à CHAQUE requête, donc elle reflète toujours
     la page réellement chargée (contrairement à un simple état JS qui se réinitialiserait
     ou resterait figé). Le store Alpine ci-dessous l'utilise comme source de vérité. --}}
<script>
    window.__navDefaultSection = @json($navDefault);
</script>

<div x-data="{ sidebarOpen: false, impersonateOpen: false }">

    <!-- BOUTON MENU MOBILE -->
    <button
        x-show="!sidebarOpen"
        @click="sidebarOpen = true"
        class="lg:hidden fixed top-4 left-2 z-[60] bg-slate-900 p-1 text-white rounded flex mr-2"
        x-transition>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5zM3 3H2v1h1z" />
            <path d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1z" />
            <path fill-rule="evenodd" d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5zM2 7h1v1H2zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm1 .5H2v1h1z" />
        </svg>
        <span class="text-sm font-medium sm:inline">Menu</span>
    </button>

    <nav
        class="fixed inset-y-0 left-0 z-40 w-72 transform transition duration-300 flex flex-col overflow-hidden"
        style="background:#1e2433"
        @click.outside="$store.nav.open = null"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Bouton fermer (mobile) -->
        <button
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="lg:hidden absolute top-8 right-2 z-[50] w-4 text-white bg-blue-700 rounded-full">
            ✕
        </button>

        <!-- Logo et Titre -->
        <div class="flex-shrink-0 border-b border-slate-800/40">
            <a href="{{ $homeRoute }}" class="flex items-center gap-3 px-5 py-5 group hover:bg-slate-800/20 transition">
                <div class="relative">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG" class="w-10 h-10 rounded-xl object-cover transition-transform duration-300 group-hover:scale-105">
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                </div>
                <div class="flex flex-col leading-tight">
                    <h1 class="text-base font-semibold text-white tracking-tight">
                        @if(auth()->user()->hasRole('admin')) Gestions @else Gestions @endif
                    </h1>
                    <p class="text-xs text-slate-400">
                        @if(auth()->user()->hasRole('admin')) Administrative TFG @else Pointage et rapports TFG @endif
                    </p>
                </div>
            </a>
        </div>

        <!-- Zone scrollable -->
        <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4">

            <!-- ================= MENU ÉTUDIANT ================= -->
            @role('etudiant')
            <div class="rounded-3xl bg-gradient-to-br from-cyan-500/5 to-cyan-900/10 p-3 shadow-md shadow-black/5 backdrop-blur-sm">
                <div class="mt-2 space-y-2">
                    {{-- Pointage --}}
                    <a href="{{ route('presence.pointage') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('presence.pointage') ? 'bg-emerald-500/20 text-emerald-100 border-l-2 border-emerald-400 shadow-inner shadow-black/20' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                        <div class="p-2 rounded-xl bg-emerald-500/10"><svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <span>Pointage</span>
                        @if(request()->routeIs('presence.pointage'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                        @endif
                    </a>
                    {{-- Historique --}}
                    <a href="{{ route('presence.historique') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('presence.historique') ? 'bg-blue-500/20 text-blue-100 border-l-2 border-blue-400 shadow-inner shadow-black/20' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                        <div class="p-2 rounded-xl bg-blue-500/10"><svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h6m2 0a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2m2 0v2h4V5" />
                            </svg></div>
                        <span>Historique</span>
                        @if(request()->routeIs('presence.historique'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300 animate-pulse"></span>
                        @endif
                    </a>
                    {{-- Espace de travail (tâches + rapports + discussion) --}}
                    <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('tasks.*') ? 'bg-teal-500/20 text-teal-100 border-l-2 border-teal-400 shadow-inner shadow-black/20' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                        <div class="p-2 rounded-xl bg-teal-500/10">
                            <svg class="w-5 h-5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span>Espace de travail</span>
                        @if(request()->routeIs('tasks.*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-teal-300 animate-pulse"></span>
                        @endif
                    </a>
                    {{-- Mon Stage --}}
                    <a href="{{ route('student.stage') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('student.stage') ? 'bg-indigo-500/20 text-indigo-100 border-l-2 border-indigo-400 shadow-inner shadow-black/20' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                        <div class="p-2 rounded-xl bg-indigo-500/10"><svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7V6a3 3 0 013-3h0a3 3 0 013 3v1m5 0H4a2 2 0 00-2 2v2a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM4 13h16v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5z" />
                            </svg></div>
                        <span>Mon Stage</span>
                        @if(request()->routeIs('student.stage'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-300 animate-pulse"></span>
                        @endif
                    </a>
                    {{-- Demandes de permission --}}
                    <a href="{{ route('permissions.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('permissions.*') ? 'bg-violet-500/20 text-violet-100 border-l-2 border-violet-400 shadow-inner shadow-black/20' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                        <div class="p-2 rounded-xl bg-violet-500/10"><svg class="w-5 h-5 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg></div>
                        <span>Permissions</span>
                        @php $pendingCount = auth()->user()->permissionRequests()->where('status','pending')->count(); @endphp
                        @if($pendingCount > 0)
                        <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-violet-500/30 text-violet-200">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    {{-- Résumés IA --}}
                    <a href="{{ route('summaries.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('summaries.*') ? 'bg-amber-500/20 text-amber-100 border-l-2 border-amber-400 shadow-inner shadow-black/20' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                        <div class="p-2 rounded-xl bg-amber-500/10"><svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg></div>
                        <span>Résumés IA</span>
                        @if(request()->routeIs('summaries.*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                        @endif
                    </a>
                </div>
            </div>
            @endrole

            <!-- ================= MENU NON-ÉTUDIANT (Admin, Superviseur, Employé) ================= -->
            @unlessrole('etudiant')
            <div class="mb-6 rounded-3xl bg-gradient-to-br from-slate-900/60 to-slate-950/40 p-5 shadow-2xl shadow-emerald-950/20 backdrop-blur-sm">

                <!-- 1. Tableau de bord -->
                @can('dashboard.view')
                <div class="mb-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-300 hover:bg-cyan-800/60 hover:text-white transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-cyan-800/60 text-white border-l-2 border-cyan-300 shadow-inner shadow-black/20' : '' }}">
                        <div class="p-2 rounded-xl bg-cyan-500/20">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <span>Tableau de bord</span>
                        @if(request()->routeIs('dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-cyan-300 animate-pulse"></span>
                        @endif
                    </a>
                </div>
                @endcan

                {{-- Mon espace : pointage personnel de l'admin/superviseur employé --}}
                @if($hasMonEspace)
                <div class="mb-4">
                    <button @click="$store.nav.toggle('espace')" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden" :class="$store.nav.open === 'espace' ? 'bg-gradient-to-r from-fuchsia-600 to-purple-700 text-white shadow-lg shadow-fuchsia-600/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm" :class="$store.nav.open === 'espace' ? 'bg-white/20' : 'bg-slate-700/50'">
                                <svg class="w-5 h-5" :class="$store.nav.open === 'espace' ? 'text-white' : 'text-fuchsia-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span>Mon espace</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="$store.nav.open === 'espace' ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>
                    <div x-show="$store.nav.open === 'espace'" x-collapse class="mt-3 ml-4 space-y-2 overflow-hidden">
                        <a href="{{ route('presence.pointage') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('presence.pointage') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            <span>Mon pointage</span>
                            @if(request()->routeIs('presence.pointage'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('presence.historique') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('presence.historique') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                            <span>Historique de mes pointages</span>
                            @if(request()->routeIs('presence.historique'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300 animate-pulse"></span>
                            @endif
                        </a>
                        @if(!auth()->user()->hasRole('admin'))
                        <a href="{{ route('permissions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('permissions.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-violet-500"></div>
                            <span>Mes permissions</span>
                            @php $pendingCount = auth()->user()->permissionRequests()->where('status','pending')->count(); @endphp
                            @if($pendingCount > 0)
                            <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-violet-500/30 text-violet-200">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 2. Suivi & Présence - AVEC PERSISTANCE -->
                <div class="mb-4">
                    <button @click="$store.nav.toggle('presence')" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden" :class="$store.nav.open === 'presence' ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-600/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm" :class="$store.nav.open === 'presence' ? 'bg-white/20' : 'bg-slate-700/50'">
                                <svg class="w-5 h-5" :class="$store.nav.open === 'presence' ? 'text-white' : 'text-emerald-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span>Suivi & Présence</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="$store.nav.open === 'presence' ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>
                    <div x-show="$store.nav.open === 'presence'" x-collapse class="mt-3 ml-4 space-y-2 overflow-hidden">
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superviseur'))
                        @can('presence.view')
                        <a href="{{ route('attendance.tracking.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('attendance.tracking.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 group-hover:bg-blue-400"></div>
                            <span>Pointages journalières</span>
                            @if(request()->routeIs('attendance.tracking.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300 animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('admin.presence.pointage-suivi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.presence.pointage-suivi') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 group-hover:bg-emerald-400"></div>
                            <span>Historique général</span>
                            @if(request()->routeIs('admin.presence.pointage-suivi'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        @can('daily_reports.view')
                        <a href="{{ route('admin.presence.anomalies') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.presence.anomalies') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-rose-500 group-hover:bg-rose-400"></div>
                            <span>Anomalies de présence</span>
                            @if(request()->routeIs('admin.presence.anomalies'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-rose-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        @role('admin|superviseur')
                        <a href="{{ route('admin.permissions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.permissions.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-violet-500 group-hover:bg-violet-400"></div>
                            <span>Permissions</span>
                            @if(request()->routeIs('admin.permissions.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-violet-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endrole

                         @role('admin|superviseur')
                         <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('tasks.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                             <div class="w-1.5 h-1.5 rounded-full bg-teal-500 group-hover:bg-teal-400"></div>
                             <span>Espace de travail</span>
                             @if(request()->routeIs('tasks.*'))
                             <span class="ml-auto w-1.5 h-1.5 rounded-full bg-teal-300 animate-pulse"></span>
                             @endif
                         </a>
                         @endrole
                         @role('admin|superviseur')
                         <a href="{{ route('admin.tasks.tracking') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.tasks.tracking') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                             <div class="w-1.5 h-1.5 rounded-full bg-violet-500 group-hover:bg-violet-400"></div>
                             <span>Suivi des tâches</span>
                             @if(request()->routeIs('admin.tasks.tracking'))
                             <span class="ml-auto w-1.5 h-1.5 rounded-full bg-violet-300 animate-pulse"></span>
                             @endif
                         </a>
                         @endrole
                         @role('admin|superviseur')
                         <a href="{{ route('admin.summaries.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.summaries.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                             <div class="w-1.5 h-1.5 rounded-full bg-amber-500 group-hover:bg-amber-400"></div>
                             <span>Résumés IA</span>
                             @if(request()->routeIs('admin.summaries.*'))
                             <span class="ml-auto w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                             @endif
                         </a>
                         @endrole
                          @role('admin|superviseur')
                         <a href="{{ route('admin.presence.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.presence.index') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                             <div class="w-1.5 h-1.5 rounded-full bg-violet-500 group-hover:bg-violet-400"></div>
                             <span>Statistiques Globales</span>
                             @if(request()->routeIs('admin.presence.index'))
                             <span class="ml-auto w-1.5 h-1.5 rounded-full bg-violet-300 animate-pulse"></span>
                             @endif
                         </a>
                         @endrole
                         @can('holidays.view')
                         <a href="{{ route('admin.holidays.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.holidays.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                             <div class="w-1.5 h-1.5 rounded-full bg-purple-500 group-hover:bg-purple-400"></div>
                             <span>Jours fériés</span>
                             @if(request()->routeIs('admin.holidays.*'))
                             <span class="ml-auto w-1.5 h-1.5 rounded-full bg-purple-300 animate-pulse"></span>
                             @endif
                         </a>
                         @endcan
                        @else
                        {{-- Employé simple --}}
                        <a href="{{ route('presence.pointage') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('presence.pointage') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            <span>Pointage</span>
                            @if(request()->routeIs('presence.pointage'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('presence.historique') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('presence.historique') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                            <span>Historique</span>
                            @if(request()->routeIs('presence.historique'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300 animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('permissions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('permissions.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-violet-500"></div>
                            <span>Permissions</span>
                            @php $pendingCount = auth()->user()->permissionRequests()->where('status','pending')->count(); @endphp
                            @if($pendingCount > 0)
                            <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-violet-500/30 text-violet-200">{{ $pendingCount }}</span>
                            @endif
                        </a>
                      <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 px-2 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('tasks.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="p-1 rounded-xl bg-teal-500/10">
                             <svg class="w-3 h-3 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                             </svg>
                           </div>
                             <span class="relative z-10">Espace de travail</span>
                             @if(request()->routeIs('tasks.*'))
                             <span class="ml-auto w-1.5 h-1.5 rounded-full bg-teal-300 animate-pulse"></span>
                             @endif
                         </a>
                         <a href="{{ route('summaries.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('summaries.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                             <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                             <span>Résumés IA</span>
                             @if(request()->routeIs('summaries.*'))
                             <span class="ml-auto w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                             @endif
                         </a>
                         @endif
                    </div>
                </div>
               <!-- 4. Personnes - AVEC PERSISTANCE -->
                @if(!auth()->user()->hasRole('etudiant'))
                @canany(['etudiants.view', 'employes.view', 'personnels.view', 'badges.view'])
                <div class="mb-4">
                    <button @click="$store.nav.toggle('personnes')" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden" :class="$store.nav.open === 'personnes' ? 'bg-gradient-to-r from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-900/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm" :class="$store.nav.open === 'personnes' ? 'bg-white/20' : 'bg-slate-700/50'">
                                <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                </svg>
                            </div>
                            <span class="relative z-10">Gestions <br>personnels</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="$store.nav.open === 'personnes' ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>
                    <div x-show="$store.nav.open === 'personnes'" x-collapse class="mt-3 ml-4 space-y-2 overflow-hidden">
                        @can('personnels.view')
                        <a href="{{ route('personnels.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('personnels.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                            <span>Personnels</span>
                            @if(request()->routeIs('personnels.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        <!-- @can('etudiants.view')
                        <a href="{{ route('etudiants.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            <span>Stagiaires</span>
                        </a>
                        @endcan
                        @role('admin|superviseur')
                        <a href="{{ route('employes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                            <span>Employés</span>
                        </a>
                        @endrole -->
                        @can('badges.view')
                        <a href="{{ route('badges.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('badges.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                            <span>Badges</span>
                            @if(request()->routeIs('badges.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-purple-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany
                @endif
                <!-- 3. Stages (uniquement pour admin) - AVEC PERSISTANCE -->
                @role('admin')
                @canany(['stages.view', 'type_stages.view', 'signataires.view', 'jour_stage.view', 'ecoles.view'])
                <div class="mb-4">
                    <button @click="$store.nav.toggle('stages')" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden" :class="$store.nav.open === 'stages' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm" :class="$store.nav.open === 'stages' ? 'bg-white/20' : 'bg-slate-700/50'">
                                <svg class="w-5 h-5" :class="$store.nav.open === 'stages' ? 'text-white' : 'text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span>Stages</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="$store.nav.open === 'stages' ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>
                    <div x-show="$store.nav.open === 'stages'" x-collapse class="mt-3 ml-4 space-y-2 overflow-hidden">
                        @can('stages.view')
                        <a href="{{ route('stages.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('stages.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                            <span>Liste des stages</span>
                            @if(request()->routeIs('stages.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        @can('type_stages.view')
                        <a href="{{ route('type_stages.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('type_stages.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                            <span>Types de stage</span>
                            @if(request()->routeIs('type_stages.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-purple-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        @can('ecoles.view')
                        <a href="{{ route('ecoles.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('ecoles.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            <span>Écoles de provenance</span>
                            @if(request()->routeIs('ecoles.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        @can('signataires.view')
                        <a href="{{ route('signataires.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('signataires.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                            <span>Signataires</span>
                            @if(request()->routeIs('signataires.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-orange-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        @can('jour_stage.view')
                        <a href="{{ route('jours.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('jours.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-yellow-500"></div>
                            <span>jours</span>
                            @if(request()->routeIs('jours.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-yellow-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        <!-- @can('stages.view')
                        <a href="{{ route('stages.trash') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-400/80 hover:text-red-300 hover:bg-red-500/10 transition-all duration-200 group hover:translate-x-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                            <span>Corbeille</span>
                        </a>
                        @endcan -->
                    </div>
                </div>
                @endcanany
                @endrole



                <!-- 5. Organisation - AVEC PERSISTANCE -->
                @if(auth()->user()->hasRole('admin') || (!auth()->user()->hasRole('etudiant') && !auth()->user()->hasRole('employe')))
                @canany(['sites.view', 'domaines.view', 'tasks.view'])
                <div class="mb-4">
                    <button @click="$store.nav.toggle('organisation')" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden" :class="$store.nav.open === 'organisation' ? 'bg-gradient-to-r from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-900/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm" :class="$store.nav.open === 'organisation' ? 'bg-white/20' : 'bg-slate-700/50'">
                                <svg class="w-5 h-5 text-cyan-400" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5z" />
                                </svg>
                            </div>
                            <span>Organisation</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="$store.nav.open === 'organisation' ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>
                    <div x-show="$store.nav.open === 'organisation'" x-collapse class="mt-3 ml-4 space-y-2 overflow-hidden">
                        @can('sites.view')
                        <a href="{{ route('sites.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('sites.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-cyan-500"></div>
                            <span>Sites</span>
                            @if(request()->routeIs('sites.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-cyan-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        @can('domaines.view')
                        <a href="{{ route('domaines.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('domaines.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                            <span>Directions/Services</span>
                            @if(request()->routeIs('domaines.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-300 animate-pulse"></span>
                            @endif
                        </a>
                        @endcan
                        <!-- @role('admin')
                        <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group hover:translate-x-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                            <span>Suivi des tâches</span>
                        </a>
                        @endrole -->
                    </div>
                </div>
                @endcanany
                @endif

                <!-- 6Accès & Sécurité (admin uniquement) - AVEC PERSISTANCE -->
                @role('admin')
                <div class="mb-4">
                    <button @click="$store.nav.toggle('access')" class="w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden" :class="$store.nav.open === 'access' ? 'bg-gradient-to-r from-indigo-700 to-indigo-800 text-white shadow-lg shadow-indigo-800/40' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm" :class="$store.nav.open === 'access' ? 'bg-white/20' : 'bg-slate-700/50'">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c2.21 0 4-1.79 4-4S14.21 3 12 3 8 4.79 8 7s1.79 4 4 4zm0 1c-3.31 0-6 2.69-6 6v1h12v-1c0-3.31-2.69-6-6-6z" />
                                </svg>
                            </div>
                            <span>Accès & Sécurité</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform duration-300 relative z-10" :class="$store.nav.open === 'access' ? 'rotate-180 text-white' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>
                    <div x-show="$store.nav.open === 'access'" x-collapse class="mt-3 ml-4 space-y-2 overflow-hidden">
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                            <span>Utilisateurs</span>
                            @if(request()->routeIs('admin.users.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-orange-300 animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.roles.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                            <span>Rôles</span>
                            @if(request()->routeIs('admin.roles.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-purple-300 animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.logs.*') ? 'bg-slate-800/60 text-white border-l-2 border-indigo-400 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                            <span>Logs système</span>
                            @if(request()->routeIs('admin.logs.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                            @endif
                        </a>
                        <a href="{{ route('admin.notifications.urgent.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group {{ request()->routeIs('admin.notifications.urgent.*') ? 'bg-slate-800/60 text-white border-l-2 border-red-500 pl-3 font-semibold shadow-inner shadow-black/20' : '' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                            <span class="text-red-300 font-semibold">Alertes urgentes</span>
                            @if(request()->routeIs('admin.notifications.urgent.*'))
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                            @endif
                        </a>
                    </div>
                </div>
                @endrole

                <!-- 7. Système / Corbeille globale -->
                @can('corbeille.view')
                <div class="mb-4">
                    <a href="{{ route('corbeille.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold text-red-400/80 hover:bg-red-500/10 hover:text-red-300 transition-all duration-300 group {{ request()->routeIs('corbeille.*') ? 'bg-red-500/15 text-red-300 border-l-2 border-red-400 shadow-inner shadow-black/20' : '' }}">
                        <div class="p-2 rounded-xl bg-red-500/20">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <span>Système — Corbeille globale</span>
                        @if(request()->routeIs('corbeille.*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-300 animate-pulse"></span>
                        @endif
                    </a>
                </div>
                @endcan

                {{-- Admin : se connecter en tant qu'employé / stagiaire --}}
                @role('admin')
                <div class="mb-4">
                    <a href="#" @click.prevent="impersonateOpen = true" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold text-fuchsia-400/80 hover:bg-fuchsia-500/10 hover:text-fuchsia-300 transition-all duration-300 group">
                        <div class="p-2 rounded-xl bg-fuchsia-500/20">
                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <span>Se connecter en tant que…</span>
                    </a>
                </div>
                @endrole

            </div>
            @endunlessrole

        </div>

        <!-- Profil Utilisateur — pied de page (popup enrichie) -->
        <div class="flex-shrink-0 px-3 pb-3 pt-2 border-t border-slate-400/10 shadow-[0_-8px_32px_0_rgba(0,0,0,0.32)]"
             x-data="{ open: false }">

            {{-- Overlay transparent pour fermer au clic dehors --}}
            <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 z-50"></div>

            {{-- Popup flottante — au-dessus de l'overlay --}}
            <div
                x-show="open"
                x-cloak
                @click.stop
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                class="fixed bottom-[78px] left-3 w-[268px] z-[60] rounded-2xl overflow-hidden bg-gray-900 border border-white/10 shadow-[0_0_0_1px_rgba(0,0,0,0.5),0_24px_64px_rgba(0,0,0,0.75),0_8px_24px_rgba(0,0,0,0.4),inset_0_1px_0_rgba(255,255,255,0.05)]">

                {{-- Header utilisateur --}}
                <div class="p-4 bg-gradient-to-br from-indigo-500/[0.06] to-transparent border-b border-white/5">
                    <div class="flex items-center gap-3">
                        @if(Auth::user()->avatar)
                        <div class="relative flex-shrink-0">
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                 class="w-[42px] h-[42px] rounded-xl object-cover ring-2 ring-indigo-500/50 shadow-lg shadow-black/40">
                            <span class="absolute -bottom-0.5 -right-0.5 w-[11px] h-[11px] bg-emerald-500 rounded-full border-2 border-gray-900"></span>
                        </div>
                        @else
                        <div class="relative flex-shrink-0">
                            <div class="w-[42px] h-[42px] rounded-xl flex items-center justify-center text-[15px] font-bold text-white bg-gradient-to-br from-indigo-500 to-indigo-800 ring-2 ring-indigo-500/50 shadow-lg shadow-black/40">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-[11px] h-[11px] bg-emerald-500 rounded-full border-2 border-gray-900"></span>
                        </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-semibold text-slate-100 truncate leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-400/60 truncate mt-0.5 leading-tight">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="p-1.5">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] text-[13px] font-medium text-slate-300/90 hover:text-slate-200 hover:bg-indigo-500/[0.13] transition-colors">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-500/[0.14] flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        Paramètres du compte
                    </a>

                    <div class="mx-2 my-1 h-px bg-white/5"></div>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-[10px] text-[13px] font-medium text-red-300/85 hover:text-red-300 hover:bg-red-500/10 transition-colors text-left">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-red-500/[0.12] flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </span>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>

            {{-- ===== BOUTON DÉCLENCHEUR ===== --}}
            <button
                @click="open = !open"
                class="relative z-[1] w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-white/10 bg-white/[0.03] hover:bg-indigo-500/[0.08] hover:border-indigo-500/20 transition-colors">

                @if(Auth::user()->avatar)
                <div class="relative flex-shrink-0">
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                         class="w-[34px] h-[34px] rounded-[9px] object-cover">
                    <span class="absolute -bottom-0.5 -right-0.5 w-[9px] h-[9px] bg-emerald-500 rounded-full border-2 border-[#1e2433]"></span>
                </div>
                @else
                <div class="relative flex-shrink-0">
                    <div class="w-[34px] h-[34px] rounded-[9px] flex items-center justify-center text-[13px] font-bold text-white bg-gradient-to-br from-indigo-500 to-indigo-800">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-[9px] h-[9px] bg-emerald-500 rounded-full border-2 border-[#1e2433]"></span>
                </div>
                @endif

                <div class="flex-1 min-w-0 text-left">
                    <p class="text-[13px] font-semibold text-slate-100 truncate leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-400/55 truncate leading-snug">{{ Auth::user()->email }}</p>
                </div>

                <svg class="w-[15px] h-[15px] flex-shrink-0 text-slate-400/45" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 1.06L8 10.31 3.47 5.78a.75.75 0 0 1 1.06-1.06L8 8.19l3.47-3.47ZM11.47 9.28a.75.75 0 0 0 1.06-1.06L8 3.69 3.47 8.22a.75.75 0 0 0 1.06 1.06L8 5.81l3.47 3.47Z"/>
                </svg>
            </button>
        </div>
    </nav>

    <!-- Overlay mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="lg:hidden fixed inset-0 bg-black/60 z-30" x-transition></div>

    {{-- ── Modale : Se connecter en tant que (admin) ── --}}
    @role('admin')
    <div x-show="impersonateOpen" x-cloak @click.outside="impersonateOpen = false"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="impersonateOpen = false"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden"
             x-data="impersonateSearch()" x-init="search()">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Se connecter en tant que…</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Choisissez un employé ou un stagiaire pour voir son espace.</p>
                </div>
                <button type="button" @click="impersonateOpen = false" class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">✕</button>
            </div>
            <div class="p-4">
                <input type="search" x-model="q" @input.debounce.300ms="search()" placeholder="Rechercher un employé ou stagiaire…"
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-fuchsia-500 outline-none">
                <div class="mt-3 max-h-72 overflow-y-auto custom-scrollbar space-y-1.5">
                    <p x-show="loading" class="text-center text-xs text-gray-400 py-6">Recherche…</p>
                    <p x-show="!loading && results.length === 0" class="text-center text-xs text-gray-400 py-6">Aucun compte trouvé.</p>
                    <template x-for="u in results" :key="u.id">
                        <form :action="'/admin/impersonate/' + u.id" method="POST" class="block">
                            <input type="hidden" name="_token" :value="csrf">
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/60 transition text-left group">
                                <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                     :class="u.type === 'stagiaire' ? 'bg-gradient-to-br from-blue-500 to-cyan-600' : 'bg-gradient-to-br from-amber-500 to-orange-600'">
                                    <span x-text="(u.name || '?').charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="u.name"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="u.email"></p>
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold"
                                          :class="u.type === 'stagiaire' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'"
                                          x-text="u.type === 'stagiaire' ? 'Stagiaire' : 'Employé'"></span>
                                </div>
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>
    @endrole
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('nav', {
        open: null,

        // Appelé automatiquement par Alpine au démarrage.
        // Priorité 1 : la route calculée côté serveur ($navDefault) — c'est la
        // vérité absolue de "où je suis réellement", donc si elle correspond à
        // une section, cette section s'ouvre, point final.
        // Priorité 2 (repli) : la dernière section ouverte manuellement par
        // l'utilisateur, mémorisée en localStorage — utile quand on navigue
        // vers une page qui n'appartient à aucune section (ex: tableau de bord)
        // et qu'on ne veut pas que le menu se referme tout seul.
        init() {
            const serverSection = window.__navDefaultSection ?? null;
            const savedSection = localStorage.getItem('tfg-nav-open-section');
            this.open = serverSection ?? savedSection ?? null;
        },

        toggle(id) {
            this.open = this.open === id ? null : id;
            if (this.open) {
                localStorage.setItem('tfg-nav-open-section', this.open);
            } else {
                localStorage.removeItem('tfg-nav-open-section');
            }
        }
    });
});

function impersonateSearch() {
    return {
        q: '',
        results: [],
        loading: false,
        csrf: document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        search() {
            this.loading = true;
            fetch('/admin/impersonate/options?q=' + encodeURIComponent(this.q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => { this.results = Array.isArray(data) ? data : []; this.loading = false; })
            .catch(() => { this.results = []; this.loading = false; });
        }
    };
}
</script>

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