@php
$homeRoute = Auth::user()->hasRole('etudiant') ? route('student.stage') : route('dashboard');
$navDefault = null;
if (request()->routeIs('type_stages.*') || request()->routeIs('ecoles.*') || request()->routeIs('signataires.*') || request()->routeIs('jours.*')) {
    $navDefault = 'stages_config';
}
@endphp

<script>
    window.__navDefaultSection = @json($navDefault);
</script>

<div x-data="{
        sidebarOpen: false,
        impersonateOpen: false,
        searchQuery: '',
        init() {
            this.sidebarOpen = document.documentElement.classList.contains('sidebar-mobile-open');
        }
     }"
     @toggle-sidebar-mobile.window="sidebarOpen = ($event.detail && typeof $event.detail.open === 'boolean') ? $event.detail.open : !sidebarOpen; document.documentElement.classList.toggle('sidebar-mobile-open', sidebarOpen)"
     @open-sidebar.window="sidebarOpen = true; document.documentElement.classList.add('sidebar-mobile-open')"
     @close-sidebar.window="sidebarOpen = false; document.documentElement.classList.remove('sidebar-mobile-open')">

    <nav
        class="sidebar fixed inset-y-0 left-0 z-50 w-[250px] -translate-x-full lg:translate-x-0 transform transition duration-300 flex flex-col overflow-hidden h-[100dvh] bg-[#f1f0ed] dark:bg-[#1f232d]"
        @click.outside="if (window.Alpine && Alpine.store('nav')) Alpine.store('nav').open = null"
        :class="sidebarOpen ? '!translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Bouton fermer (mobile & tablette) -->
        <button
            type="button"
            x-show="sidebarOpen"
            @click="sidebarOpen = false; if (window.closeSidebarMobile) window.closeSidebarMobile();"
            onclick="if (window.closeSidebarMobile) window.closeSidebarMobile();"
            aria-label="Fermer le menu"
            class="lg:hidden absolute top-3.5 right-3 z-50 p-2 text-[#57544f] hover:text-[#0f0e0d] rounded-xl hover:bg-black/5 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/10 transition-colors cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Logo et Titre (Option 4 : Espace épuré avec point de rupture) -->
        <div class="sidebar-logo-container flex-shrink-0 pl-[26px] pr-4 pt-4 pb-3 border-b border-[#dcdad2] dark:border-white/10">
            <a href="{{ $homeRoute }}" class="flex items-end justify-start gap-2 group transition">
                <div class="relative flex-shrink-0">
                    <img src="{{ asset('images/logo_TFG_fond_transparent.png') }}?v={{ filemtime(public_path('images/logo_TFG_fond_transparent.png')) }}" alt="Logo TFG" class="w-[50px] h-[50px] object-contain transition-transform duration-300 group-hover:scale-105">
                </div>
                <div class="sidebar-logo-text min-w-0 pb-1">
                    <span class="text-[16px] font-semibold text-[#241f1b] dark:text-slate-100 tracking-wider leading-none truncate block">
                        GESTION T.F.G
                    </span>
                </div>
            </a>
        </div>

        <!-- Zone scrollable : corps central uniquement -->
        <div class="sidebar-body flex-1 overflow-y-auto custom-scrollbar px-3 pt-3 pb-3 space-y-1.5">

            <!-- En-tête de section Menu (aligné sur la même verticale que les icônes des onglets : px-3.5) -->
            <div class="sidebar-menu-header px-3.5 pt-1 pb-1">
                <span class="text-xs font-semibold tracking-wider uppercase text-[#6e6862] dark:text-slate-400">Menu</span>
            </div>

            <!-- ================= MENU ÉTUDIANT ================= -->
            @if(auth()->user()->hasRole('etudiant'))
            <div class="space-y-1">
                {{-- Pointage --}}
                <a href="{{ route('presence.pointage') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('presence.pointage') ? 'bg-emerald-500/20 text-emerald-800 font-semibold dark:bg-emerald-500/25 dark:text-emerald-300' : 'text-[#57544f] hover:text-emerald-700 hover:bg-emerald-500/10 dark:text-slate-300 dark:hover:text-emerald-300 dark:hover:bg-emerald-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('presence.pointage') ? 'text-emerald-700 dark:text-emerald-300' : 'text-[#15803d] dark:text-emerald-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="truncate">Pointage</span>
                    </div>
                </a>

                {{-- Historique --}}
                <a href="{{ route('presence.historique') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('presence.historique') ? 'bg-sky-500/20 text-sky-800 font-semibold dark:bg-sky-500/25 dark:text-sky-300' : 'text-[#57544f] hover:text-sky-700 hover:bg-sky-500/10 dark:text-slate-300 dark:hover:text-sky-300 dark:hover:bg-sky-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('presence.historique') ? 'text-sky-700 dark:text-sky-300' : 'text-[#0284c7] dark:text-sky-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h6m2 0a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2m2 0v2h4V5" />
                        </svg>
                        <span class="truncate">Historique</span>
                    </div>
                </a>

                {{-- Espace de travail --}}
                <a href="{{ route('tasks.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('tasks.*') ? 'bg-teal-500/20 text-teal-800 font-semibold dark:bg-teal-500/25 dark:text-teal-300' : 'text-[#57544f] hover:text-teal-700 hover:bg-teal-500/10 dark:text-slate-300 dark:hover:text-teal-300 dark:hover:bg-teal-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('tasks.*') ? 'text-teal-700 dark:text-teal-300' : 'text-[#0d9488] dark:text-teal-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span class="truncate">Espace de travail</span>
                    </div>
                </a>

                {{-- Mon Stage --}}
                <a href="{{ route('student.stage') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('student.stage') ? 'bg-[#1c3e96]/20 text-[#1c3e96] font-semibold dark:bg-[#1c3e96]/30 dark:text-indigo-200' : 'text-[#57544f] hover:text-[#1c3e96] hover:bg-[#1c3e96]/10 dark:text-slate-300 dark:hover:text-indigo-300 dark:hover:bg-[#1c3e96]/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('student.stage') ? 'text-[#1c3e96] dark:text-indigo-300' : 'text-[#1c3e96] dark:text-indigo-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7V6a3 3 0 013-3h0a3 3 0 013 3v1m5 0H4a2 2 0 00-2 2v2a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM4 13h16v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5z" />
                        </svg>
                        <span class="truncate">Mon Stage</span>
                    </div>
                </a>

                {{-- Permissions --}}
                <a href="{{ route('permissions.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('permissions.*') ? 'bg-purple-500/20 text-purple-800 font-semibold dark:bg-purple-500/25 dark:text-purple-300' : 'text-[#57544f] hover:text-purple-700 hover:bg-purple-500/10 dark:text-slate-300 dark:hover:text-purple-300 dark:hover:bg-purple-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('permissions.*') ? 'text-purple-700 dark:text-purple-300' : 'text-[#7e22ce] dark:text-purple-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="truncate">Permissions</span>
                    </div>
                    @php $pendingCount = auth()->user()->permissionRequests()->where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('permissions.*') ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-700 dark:bg-purple-500/30 dark:text-purple-200' }}">{{ $pendingCount }}</span>
                    @endif
                </a>

                {{-- Résumés IA --}}
                <a href="{{ route('summaries.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('summaries.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('summaries.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#b45309] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="truncate">Résumés IA</span>
                    </div>
                </a>
            </div>

            <!-- ================= MENU COLLABORATEUR / EMPLOYÉ ================= -->
            @elseif(auth()->user()->hasRole('employe') && !auth()->user()->hasAnyRole(['admin', 'superviseur']))
            <div class="space-y-1">
                {{-- 1. Tableau de bord --}}
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('dashboard') ? 'bg-cyan-500/20 text-cyan-800 font-semibold dark:bg-cyan-500/25 dark:text-cyan-300' : 'text-[#57544f] hover:text-cyan-700 hover:bg-cyan-500/10 dark:text-slate-300 dark:hover:text-cyan-300 dark:hover:bg-cyan-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-cyan-700 dark:text-cyan-300' : 'text-[#0891b2] dark:text-cyan-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="truncate">Tableau de bord</span>
                    </div>
                </a>

                {{-- 2. Pointage --}}
                <a href="{{ route('presence.pointage') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('presence.pointage') ? 'bg-emerald-500/20 text-emerald-800 font-semibold dark:bg-emerald-500/25 dark:text-emerald-300' : 'text-[#57544f] hover:text-emerald-700 hover:bg-emerald-500/10 dark:text-slate-300 dark:hover:text-emerald-300 dark:hover:bg-emerald-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('presence.pointage') ? 'text-emerald-700 dark:text-emerald-300' : 'text-[#15803d] dark:text-emerald-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="truncate">Pointage</span>
                    </div>
                </a>

                {{-- 3. Historique --}}
                <a href="{{ route('presence.historique') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('presence.historique') ? 'bg-sky-500/20 text-sky-800 font-semibold dark:bg-sky-500/25 dark:text-sky-300' : 'text-[#57544f] hover:text-sky-700 hover:bg-sky-500/10 dark:text-slate-300 dark:hover:text-sky-300 dark:hover:bg-sky-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('presence.historique') ? 'text-sky-700 dark:text-sky-300' : 'text-[#0284c7] dark:text-sky-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h6m2 0a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2m2 0v2h4V5" />
                        </svg>
                        <span class="truncate">Historique</span>
                    </div>
                </a>

                {{-- 4. Espace de travail & Tâches --}}
                <a href="{{ route('tasks.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('tasks.*') ? 'bg-teal-500/20 text-teal-800 font-semibold dark:bg-teal-500/25 dark:text-teal-300' : 'text-[#57544f] hover:text-teal-700 hover:bg-teal-500/10 dark:text-slate-300 dark:hover:text-teal-300 dark:hover:bg-teal-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('tasks.*') ? 'text-teal-700 dark:text-teal-300' : 'text-[#0d9488] dark:text-teal-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span class="truncate">Espace de travail</span>
                    </div>
                </a>

                {{-- 5. Rapports d'activité --}}
                @can('daily_reports.view')
                <a href="{{ route('reports.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('reports.*') ? 'bg-[#1c3e96]/20 text-[#1c3e96] font-semibold dark:bg-[#1c3e96]/30 dark:text-indigo-200' : 'text-[#57544f] hover:text-[#1c3e96] hover:bg-[#1c3e96]/10 dark:text-slate-300 dark:hover:text-indigo-300 dark:hover:bg-[#1c3e96]/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('reports.*') ? 'text-[#1c3e96] dark:text-indigo-300' : 'text-[#1c3e96] dark:text-indigo-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="truncate">Rapports d'activité</span>
                    </div>
                </a>
                @endcan

                {{-- 6. Mes permissions --}}
                @can('permissions.view')
                <a href="{{ route('permissions.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('permissions.*') ? 'bg-purple-500/20 text-purple-800 font-semibold dark:bg-purple-500/25 dark:text-purple-300' : 'text-[#57544f] hover:text-purple-700 hover:bg-purple-500/10 dark:text-slate-300 dark:hover:text-purple-300 dark:hover:bg-purple-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('permissions.*') ? 'text-purple-700 dark:text-purple-300' : 'text-[#7e22ce] dark:text-purple-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="truncate">Mes permissions</span>
                    </div>
                    @php $pendingCount = auth()->user()->permissionRequests()->where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('permissions.*') ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-700 dark:bg-purple-500/30 dark:text-purple-200' }}">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endcan

                {{-- 7. Résumés IA --}}
                <a href="{{ route('summaries.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('summaries.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('summaries.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#b45309] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="truncate">Résumés IA</span>
                    </div>
                </a>

                {{-- 8. Jours fériés (si autorisé) --}}
                @can('holidays.view')
                <a href="{{ route('admin.holidays.index') }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('admin.holidays.*') ? 'bg-orange-500/20 text-orange-800 font-semibold dark:bg-orange-500/25 dark:text-orange-300' : 'text-[#57544f] hover:text-orange-700 hover:bg-orange-500/10 dark:text-slate-300 dark:hover:text-orange-300 dark:hover:bg-orange-500/15 font-medium' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.holidays.*') ? 'text-orange-700 dark:text-orange-300' : 'text-orange-600 dark:text-orange-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate">Jours fériés</span>
                    </div>
                </a>
                @endcan
            </div>

            <!-- ================= MENU ADMIN / SUPERVISEUR (OPTION A : ONGLETS DIRECTS) ================= -->
            @else
            <div class="space-y-0.5">

                <!-- 1. Tableau de bord -->
                @can('dashboard.view')
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-cyan-500/20 text-cyan-800 font-semibold dark:bg-cyan-500/25 dark:text-cyan-300' : 'text-[#57544f] hover:text-cyan-700 hover:bg-cyan-500/10 dark:text-slate-300 dark:hover:text-cyan-300 dark:hover:bg-cyan-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-cyan-700 dark:text-cyan-300' : 'text-[#0891b2] dark:text-cyan-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="truncate">Tableau de bord</span>
                    </div>
                </a>
                @endcan

                <!-- Catégorie : PRÉSENCES -->
                @canany(['presence.view', 'daily_reports.view', 'holidays.view'])
                <div class="px-3 pt-3 pb-1 border-t border-[#dcdad2]/60 dark:border-white/5">
                    <span class="text-[10px] font-bold tracking-wider uppercase text-[#7a756d] dark:text-slate-400">Présences</span>
                </div>

                @can('presence.view')
                <a href="{{ route('attendance.tracking.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('attendance.tracking.*') ? 'bg-emerald-500/20 text-emerald-800 font-semibold dark:bg-emerald-500/25 dark:text-emerald-300' : 'text-[#57544f] hover:text-emerald-700 hover:bg-emerald-500/10 dark:text-slate-300 dark:hover:text-emerald-300 dark:hover:bg-emerald-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('attendance.tracking.*') ? 'text-emerald-700 dark:text-emerald-300' : 'text-emerald-600 dark:text-emerald-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate">Pointages du jour</span>
                    </div>
                </a>

                <a href="{{ route('admin.presence.pointage-suivi') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.presence.pointage-suivi') ? 'bg-emerald-500/20 text-emerald-800 font-semibold dark:bg-emerald-500/25 dark:text-emerald-300' : 'text-[#57544f] hover:text-emerald-700 hover:bg-emerald-500/10 dark:text-slate-300 dark:hover:text-emerald-300 dark:hover:bg-emerald-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.presence.pointage-suivi') ? 'text-emerald-700 dark:text-emerald-300' : 'text-emerald-600 dark:text-emerald-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="truncate">Historique</span>
                    </div>
                </a>
                @endcan

                @canany(['daily_reports.view', 'presence.view'])
                <a href="{{ route('admin.presence.anomalies') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.presence.anomalies') ? 'bg-rose-500/20 text-rose-800 font-semibold dark:bg-rose-500/25 dark:text-rose-300' : 'text-[#57544f] hover:text-rose-700 hover:bg-rose-500/10 dark:text-slate-300 dark:hover:text-rose-300 dark:hover:bg-rose-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.presence.anomalies') ? 'text-rose-700 dark:text-rose-300' : 'text-rose-600 dark:text-rose-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="truncate">Anomalies</span>
                    </div>
                </a>
                @endcanany

                @role('admin|superviseur')
                <a href="{{ route('admin.presence.corrections') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.presence.corrections') ? 'bg-emerald-500/20 text-emerald-800 font-semibold dark:bg-emerald-500/25 dark:text-emerald-300' : 'text-[#57544f] hover:text-emerald-700 hover:bg-emerald-500/10 dark:text-slate-300 dark:hover:text-emerald-300 dark:hover:bg-emerald-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.presence.corrections') ? 'text-emerald-700 dark:text-emerald-300' : 'text-emerald-600 dark:text-emerald-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="truncate">Corrections</span>
                    </div>
                </a>

                <a href="{{ route('admin.presence.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.presence.index') || request()->routeIs('admin.presence.stats') || request()->routeIs('admin.presence.chart-detail') ? 'bg-emerald-500/20 text-emerald-800 font-semibold dark:bg-emerald-500/25 dark:text-emerald-300' : 'text-[#57544f] hover:text-emerald-700 hover:bg-emerald-500/10 dark:text-slate-300 dark:hover:text-emerald-300 dark:hover:bg-emerald-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.presence.index') || request()->routeIs('admin.presence.stats') || request()->routeIs('admin.presence.chart-detail') ? 'text-emerald-700 dark:text-emerald-300' : 'text-emerald-600 dark:text-emerald-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="truncate">Statistiques</span>
                    </div>
                </a>
                @endrole

                @role('admin')
                <a href="{{ route('admin.presence.horaire.edit') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.presence.horaire.*') ? 'bg-emerald-500/20 text-emerald-800 font-semibold dark:bg-emerald-500/25 dark:text-emerald-300' : 'text-[#57544f] hover:text-emerald-700 hover:bg-emerald-500/10 dark:text-slate-300 dark:hover:text-emerald-300 dark:hover:bg-emerald-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.presence.horaire.*') ? 'text-emerald-700 dark:text-emerald-300' : 'text-emerald-600 dark:text-emerald-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <span class="truncate">Horaires</span>
                    </div>
                </a>
                @endrole

                @can('holidays.view')
                <a href="{{ route('admin.holidays.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.holidays.*') ? 'bg-orange-500/20 text-orange-800 font-semibold dark:bg-orange-500/25 dark:text-orange-300' : 'text-[#57544f] hover:text-orange-700 hover:bg-orange-500/10 dark:text-slate-300 dark:hover:text-orange-300 dark:hover:bg-orange-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.holidays.*') ? 'text-orange-700 dark:text-orange-300' : 'text-orange-600 dark:text-orange-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span class="truncate">Jours fériés</span>
                    </div>
                </a>
                @endcan
                @endcanany

                <!-- Catégorie : ACTIVITÉS & TÂCHES -->
                @canany(['tasks.view', 'daily_reports.view'])
                <div class="px-3 pt-3 pb-1 border-t border-[#dcdad2]/60 dark:border-white/5">
                    <span class="text-[10px] font-bold tracking-wider uppercase text-[#7a756d] dark:text-slate-400">Activités & Tâches</span>
                </div>

                @can('tasks.view')
                <a href="{{ route('tasks.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('tasks.*') && !request()->routeIs('admin.tasks.tracking') ? 'bg-[#1c3e96]/15 text-[#1c3e96] font-semibold dark:bg-[#1c3e96]/25 dark:text-indigo-300' : 'text-[#57544f] hover:text-[#1c3e96] hover:bg-[#1c3e96]/10 dark:text-slate-300 dark:hover:text-indigo-300 dark:hover:bg-[#1c3e96]/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('tasks.*') && !request()->routeIs('admin.tasks.tracking') ? 'text-[#1c3e96] dark:text-indigo-300' : 'text-[#1c3e96] dark:text-indigo-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="truncate">Espace de travail</span>
                    </div>
                </a>
                @endcan

                @role('admin|superviseur')
                <a href="{{ route('admin.tasks.tracking') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.tasks.tracking') ? 'bg-[#1c3e96]/15 text-[#1c3e96] font-semibold dark:bg-[#1c3e96]/25 dark:text-indigo-300' : 'text-[#57544f] hover:text-[#1c3e96] hover:bg-[#1c3e96]/10 dark:text-slate-300 dark:hover:text-indigo-300 dark:hover:bg-[#1c3e96]/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.tasks.tracking') ? 'text-[#1c3e96] dark:text-indigo-300' : 'text-[#1c3e96] dark:text-indigo-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="truncate">Suivi tâches</span>
                    </div>
                </a>
                @endrole

                @can('daily_reports.view')
                <a href="{{ route('admin.reports.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'bg-[#1c3e96]/15 text-[#1c3e96] font-semibold dark:bg-[#1c3e96]/25 dark:text-indigo-300' : 'text-[#57544f] hover:text-[#1c3e96] hover:bg-[#1c3e96]/10 dark:text-slate-300 dark:hover:text-indigo-300 dark:hover:bg-[#1c3e96]/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') ? 'text-[#1c3e96] dark:text-indigo-300' : 'text-[#1c3e96] dark:text-indigo-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="truncate">Rapports d'activité</span>
                    </div>
                </a>
                @endcan

                @role('admin|superviseur')
                <a href="{{ route('admin.summaries.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.summaries.*') || request()->routeIs('summaries.*') ? 'bg-[#1c3e96]/15 text-[#1c3e96] font-semibold dark:bg-[#1c3e96]/25 dark:text-indigo-300' : 'text-[#57544f] hover:text-[#1c3e96] hover:bg-[#1c3e96]/10 dark:text-slate-300 dark:hover:text-indigo-300 dark:hover:bg-[#1c3e96]/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.summaries.*') || request()->routeIs('summaries.*') ? 'text-[#1c3e96] dark:text-indigo-300' : 'text-[#1c3e96] dark:text-indigo-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="truncate">Résumés IA</span>
                    </div>
                </a>

                <a href="{{ route('admin.permissions.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.permissions.*') ? 'bg-[#1c3e96]/15 text-[#1c3e96] font-semibold dark:bg-[#1c3e96]/25 dark:text-indigo-300' : 'text-[#57544f] hover:text-[#1c3e96] hover:bg-[#1c3e96]/10 dark:text-slate-300 dark:hover:text-indigo-300 dark:hover:bg-[#1c3e96]/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.permissions.*') ? 'text-[#1c3e96] dark:text-indigo-300' : 'text-[#1c3e96] dark:text-indigo-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span class="truncate">Permissions</span>
                    </div>
                    @php $pendingAdminPerms = \App\Models\PermissionRequest::where('status', 'pending')->count(); @endphp
                    @if($pendingAdminPerms > 0)
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('admin.permissions.*') ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/30 dark:text-indigo-200' }}">{{ $pendingAdminPerms }}</span>
                    @endif
                </a>
                @endrole
                @endcanany

                <!-- Catégorie : RESSOURCES HUMAINES -->
                @canany(['employes.view', 'etudiants.view', 'personnels.view', 'badges.view'])
                <div class="px-3 pt-3 pb-1 border-t border-[#dcdad2]/60 dark:border-white/5">
                    <span class="text-[10px] font-bold tracking-wider uppercase text-[#7a756d] dark:text-slate-400">Ressources Humaines</span>
                </div>

                @can('employes.view')
                <a href="{{ route('employes.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('employes.*') ? 'bg-fuchsia-500/20 text-fuchsia-800 font-semibold dark:bg-fuchsia-500/25 dark:text-fuchsia-300' : 'text-[#57544f] hover:text-fuchsia-700 hover:bg-fuchsia-500/10 dark:text-slate-300 dark:hover:text-fuchsia-300 dark:hover:bg-fuchsia-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('employes.*') ? 'text-fuchsia-700 dark:text-fuchsia-300' : 'text-[#c026d3] dark:text-fuchsia-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="truncate">Employés</span>
                    </div>
                </a>
                @endcan

                @can('etudiants.view')
                <a href="{{ route('etudiants.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('etudiants.*') ? 'bg-fuchsia-500/20 text-fuchsia-800 font-semibold dark:bg-fuchsia-500/25 dark:text-fuchsia-300' : 'text-[#57544f] hover:text-fuchsia-700 hover:bg-fuchsia-500/10 dark:text-slate-300 dark:hover:text-fuchsia-300 dark:hover:bg-fuchsia-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('etudiants.*') ? 'text-fuchsia-700 dark:text-fuchsia-300' : 'text-[#c026d3] dark:text-fuchsia-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                        <span class="truncate">Stagiaires</span>
                    </div>
                </a>
                @endcan

                @can('personnels.view')
                <a href="{{ route('personnels.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('personnels.*') ? 'bg-fuchsia-500/20 text-fuchsia-800 font-semibold dark:bg-fuchsia-500/25 dark:text-fuchsia-300' : 'text-[#57544f] hover:text-fuchsia-700 hover:bg-fuchsia-500/10 dark:text-slate-300 dark:hover:text-fuchsia-300 dark:hover:bg-fuchsia-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('personnels.*') ? 'text-fuchsia-700 dark:text-fuchsia-300' : 'text-[#c026d3] dark:text-fuchsia-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate">Personnels</span>
                    </div>
                </a>
                @endcan

                @can('badges.view')
                <a href="{{ route('badges.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('badges.*') ? 'bg-fuchsia-500/20 text-fuchsia-800 font-semibold dark:bg-fuchsia-500/25 dark:text-fuchsia-300' : 'text-[#57544f] hover:text-fuchsia-700 hover:bg-fuchsia-500/10 dark:text-slate-300 dark:hover:text-fuchsia-300 dark:hover:bg-fuchsia-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('badges.*') ? 'text-fuchsia-700 dark:text-fuchsia-300' : 'text-[#c026d3] dark:text-fuchsia-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2H9.17A3.001 3.001 0 0112 14z" />
                        </svg>
                        <span class="truncate">Badges</span>
                    </div>
                </a>
                @endcan
                @endcanany

                <!-- Catégorie : STAGES -->
                @canany(['stages.view', 'type_stages.view', 'ecoles.view', 'signataires.view', 'jour_stage.view'])
                <div class="px-3 pt-3 pb-1 border-t border-[#dcdad2]/60 dark:border-white/5">
                    <span class="text-[10px] font-bold tracking-wider uppercase text-[#7a756d] dark:text-slate-400">Stages</span>
                </div>

                @can('stages.view')
                <a href="{{ route('stages.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('stages.*') ? 'bg-blue-500/20 text-blue-800 font-semibold dark:bg-blue-500/25 dark:text-blue-300' : 'text-[#57544f] hover:text-blue-700 hover:bg-blue-500/10 dark:text-slate-300 dark:hover:text-blue-300 dark:hover:bg-blue-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('stages.*') ? 'text-blue-700 dark:text-blue-300' : 'text-blue-600 dark:text-blue-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span class="truncate">Liste des stages</span>
                    </div>
                </a>
                @endcan

                @role('admin')
                <a href="{{ route('admin.evaluations.criteres.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.evaluations.criteres.*') ? 'bg-blue-500/20 text-blue-800 font-semibold dark:bg-blue-500/25 dark:text-blue-300' : 'text-[#57544f] hover:text-blue-700 hover:bg-blue-500/10 dark:text-slate-300 dark:hover:text-blue-300 dark:hover:bg-blue-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.evaluations.criteres.*') ? 'text-blue-700 dark:text-blue-300' : 'text-blue-600 dark:text-blue-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        <span class="truncate">Évaluations</span>
                    </div>
                </a>
                @endrole

                {{-- Sous-menu discret pour les tables de paramétrage de stage --}}
                @canany(['type_stages.view', 'ecoles.view', 'signataires.view', 'jour_stage.view'])
                <div class="space-y-0.5">
                    <button @click="$store.nav.toggle('stages_config')"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all text-[#57544f] hover:text-blue-700 hover:bg-blue-500/10 dark:text-slate-300 dark:hover:text-blue-300 dark:hover:bg-blue-500/15 group"
                            :class="$store.nav.open === 'stages_config' ? 'bg-blue-500/20 text-blue-800 font-semibold dark:bg-blue-500/25 dark:text-blue-300' : ''">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-[18px] h-[18px] flex-shrink-0 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">Configuration stages</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-[#6e6862] group-hover:text-blue-700 dark:text-slate-500 dark:group-hover:text-slate-300 transform transition-transform duration-200 flex-shrink-0"
                             :class="$store.nav.open === 'stages_config' ? 'rotate-90 text-blue-700 dark:text-white' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="$store.nav.open === 'stages_config'" x-collapse class="mt-1 ml-3 pl-3 border-l border-[#dcdad2] dark:border-white/10 space-y-0.5">
                        @can('type_stages.view')
                        <a href="{{ route('type_stages.index') }}"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12.5px] transition-all {{ request()->routeIs('type_stages.*') ? 'bg-blue-500/20 text-blue-800 font-semibold dark:bg-blue-500/25 dark:text-blue-300' : 'text-[#6e6862] hover:text-blue-700 hover:bg-blue-500/10 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/[0.05]' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 {{ request()->routeIs('type_stages.*') ? 'text-blue-700 dark:text-blue-300' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span class="truncate">Types</span>
                        </a>
                        @endcan
                        @can('ecoles.view')
                        <a href="{{ route('ecoles.index') }}"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12.5px] transition-all {{ request()->routeIs('ecoles.*') ? 'bg-blue-500/20 text-blue-800 font-semibold dark:bg-blue-500/25 dark:text-blue-300' : 'text-[#6e6862] hover:text-blue-700 hover:bg-blue-500/10 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/[0.05]' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 {{ request()->routeIs('ecoles.*') ? 'text-blue-700 dark:text-blue-300' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">Écoles</span>
                        </a>
                        @endcan
                        @can('signataires.view')
                        <a href="{{ route('signataires.index') }}"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12.5px] transition-all {{ request()->routeIs('signataires.*') ? 'bg-blue-500/20 text-blue-800 font-semibold dark:bg-blue-500/25 dark:text-blue-300' : 'text-[#6e6862] hover:text-blue-700 hover:bg-blue-500/10 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/[0.05]' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 {{ request()->routeIs('signataires.*') ? 'text-blue-700 dark:text-blue-300' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span class="truncate">Signataires</span>
                        </a>
                        @endcan
                        @can('jour_stage.view')
                        <a href="{{ route('jours.index') }}"
                           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12.5px] transition-all {{ request()->routeIs('jours.*') ? 'bg-blue-500/20 text-blue-800 font-semibold dark:bg-blue-500/25 dark:text-blue-300' : 'text-[#6e6862] hover:text-blue-700 hover:bg-blue-500/10 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/[0.05]' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 {{ request()->routeIs('jours.*') ? 'text-blue-700 dark:text-blue-300' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">Jours de stage</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany
                @endcanany

                <!-- Catégorie : ADMINISTRATION -->
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superviseur'))
                <div class="px-3 pt-3 pb-1 border-t border-[#dcdad2]/60 dark:border-white/5">
                    <span class="text-[10px] font-bold tracking-wider uppercase text-[#7a756d] dark:text-slate-400">Administration</span>
                </div>

                @can('sites.view')
                <a href="{{ route('sites.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('sites.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('sites.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#d97706] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate">Sites</span>
                    </div>
                </a>
                @endcan

                @can('domaines.view')
                <a href="{{ route('domaines.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('domaines.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('domaines.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#d97706] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="truncate">Directions</span>
                    </div>
                </a>
                @endcan

                @can('users.view')
                <a href="{{ route('admin.users.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#d97706] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="truncate">Utilisateurs</span>
                    </div>
                </a>
                @endcan

                @can('roles.view')
                <a href="{{ route('admin.roles.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.roles.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.roles.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#d97706] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="truncate">Rôles</span>
                    </div>
                </a>
                @endcan

                @role('admin')
                <a href="{{ route('admin.notifications.urgent.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.notifications.urgent.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.notifications.urgent.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#d97706] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        <span class="truncate">Alertes urgentes</span>
                    </div>
                </a>

                <a href="{{ route('admin.logs.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('admin.logs.*') ? 'bg-amber-500/20 text-amber-900 font-semibold dark:bg-amber-500/25 dark:text-amber-300' : 'text-[#57544f] hover:text-amber-800 hover:bg-amber-500/10 dark:text-slate-300 dark:hover:text-amber-300 dark:hover:bg-amber-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('admin.logs.*') ? 'text-amber-700 dark:text-amber-300' : 'text-[#d97706] dark:text-amber-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate">Logs</span>
                    </div>
                </a>
                @endrole

                @can('corbeille.view')
                <a href="{{ route('corbeille.index') }}"
                   class="group flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition-all {{ request()->routeIs('corbeille.*') ? 'bg-rose-500/20 text-rose-800 font-semibold dark:bg-rose-500/25 dark:text-rose-300' : 'text-[#57544f] hover:text-rose-700 hover:bg-rose-500/10 dark:text-slate-300 dark:hover:text-rose-300 dark:hover:bg-rose-500/15' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs('corbeille.*') ? 'text-rose-700 dark:text-rose-300' : 'text-rose-600 dark:text-rose-400' }} group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="truncate">Corbeille</span>
                    </div>
                </a>
                @endcan
                @endif

                {{-- Admin : se connecter en tant qu'employé / stagiaire --}}
                @role('admin')
                <div class="pt-2 border-t border-[#dcdad2]/60 dark:border-white/5">
                    <a href="#" @click.prevent="impersonateOpen = true"
                       class="group flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all text-[#7e22ce] hover:text-[#581c87] hover:bg-purple-500/10 dark:text-fuchsia-300 dark:hover:text-white dark:hover:bg-fuchsia-500/15">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#a21caf] dark:text-fuchsia-400 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span class="truncate">Se connecter en tant que…</span>
                    </a>
                </div>
                @endrole

            </div>
            @endif

        </div>

        <!-- Profil Utilisateur — pied de page fixe (sans point de rupture) -->
        <div class="flex-shrink-0 px-3 pb-3 pt-2 bg-[#f1f0ed] dark:bg-[#1f232d]"
             x-data="{ open: false }">

            {{-- Overlay transparent pour fermer au clic dehors --}}
            <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 z-50"></div>

            {{-- Popup flottante compacte (style carte mon-stage & Kaisss) --}}
            <div
                x-show="open"
                x-cloak
                @click.stop
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="fixed bottom-[68px] left-3 w-[228px] z-[60] rounded-xl overflow-hidden bg-[#fffefa] border border-[#dcdad2] shadow-xl dark:bg-[#131519] dark:border-[#222630] dark:shadow-2xl">

                @php
                    $roleLibelle = auth()->user()->roles->first()?->name ?? 'Utilisateur';
                    $roleTraduit = match($roleLibelle) {
                        'admin' => 'Administrateur',
                        'superviseur' => 'Superviseur',
                        'etudiant' => 'Stagiaire',
                        'employe' => 'Employé',
                        default => ucfirst($roleLibelle)
                    };
                @endphp

                {{-- Header utilisateur compact --}}
                <div class="p-3 border-b border-[#ecebe3] bg-[#faf9f6] dark:border-[#222630] dark:bg-[#1f232d]">
                    <div class="flex items-center gap-2.5">
                        @if(Auth::user()->avatar)
                        <div class="relative flex-shrink-0">
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                 class="w-9 h-9 rounded-full object-cover ring-1 ring-[#dcdad2] dark:ring-white/10">
                            <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-emerald-500 rounded-full border border-white dark:border-[#1f232d]"></span>
                        </div>
                        @else
                        <div class="relative flex-shrink-0">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold bg-[#eef1fb] text-[#1c3e96] border border-[#dcdad2] dark:bg-gradient-to-br dark:from-indigo-500 dark:to-indigo-800 dark:text-white dark:border-none">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-emerald-500 rounded-full border border-white dark:border-[#1f232d]"></span>
                        </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-[#241f1b] dark:text-white truncate leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[10.5px] text-[#6e6862] dark:text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                            <span class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[9.5px] font-semibold bg-[#eef1fb] text-[#1c3e96] border border-[#dcdad2] dark:bg-indigo-950/60 dark:text-indigo-400 dark:border-indigo-800/40">
                                {{ $roleTraduit }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Actions compactes --}}
                <div class="p-1 space-y-0.5">
                    {{-- Paramètres / Mon Profil --}}
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium text-[#241f1b] hover:bg-[#f1f0ed] hover:text-[#1c3e96] dark:text-slate-200 dark:hover:bg-white/[0.08] dark:hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Paramètres</span>
                    </a>

                    {{-- Mode d'affichage (Clair / Sombre) comme Kaisss --}}
                    <button type="button"
                            @click="const isDark = document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', isDark ? 'dark' : 'light'); window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: isDark } }));"
                            class="flex items-center justify-between w-full px-2.5 py-1.5 rounded-lg text-xs font-medium text-[#241f1b] hover:bg-[#f1f0ed] hover:text-[#1c3e96] dark:text-slate-200 dark:hover:bg-white/[0.08] dark:hover:text-white transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-[#6e6862] dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <span>Mode d'affichage</span>
                        </div>
                        <span class="text-[9.5px] font-semibold text-[#6e6862] dark:text-slate-400 bg-[#e8e6e1] dark:bg-white/10 px-1.5 py-0.5 rounded">
                            <span class="dark:hidden">Clair</span>
                            <span class="hidden dark:inline">Sombre</span>
                        </span>
                    </button>

                    {{-- Séparateur --}}
                    <div class="my-1 border-t border-[#ecebe3] dark:border-[#222630]"></div>

                    {{-- Déconnexion --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 w-full px-2.5 py-1.5 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition-colors text-left">
                            <svg class="w-3.5 h-3.5 text-red-500 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Bouton profil déclencheur --}}
            <button
                @click="open = !open"
                class="relative z-[1] w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl border border-[#dcdad2] bg-[#fffefa] hover:bg-white hover:border-[#bfbab2] shadow-sm dark:border-white/5 dark:bg-white/[0.03] dark:hover:bg-white/[0.08] transition-all">

                @if(Auth::user()->avatar)
                <div class="relative flex-shrink-0">
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                         class="w-8 h-8 rounded-full object-cover ring-1 ring-[#dcdad2] dark:ring-white/10">
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#fffefa] dark:border-[#1f232d]"></span>
                </div>
                @else
                <div class="relative flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-[#eef1fb] text-[#1c3e96] border border-[#dcdad2] dark:bg-gradient-to-br dark:from-indigo-500 dark:to-indigo-800 dark:text-white dark:border-none">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#fffefa] dark:border-[#1f232d]"></span>
                </div>
                @endif

                <div class="flex-1 min-w-0 text-left sidebar-profile-text">
                    <p class="text-xs font-semibold text-[#241f1b] dark:text-slate-100 truncate leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-[#6e6862] dark:text-slate-400 truncate leading-tight mt-0.5">{{ Auth::user()->email }}</p>
                </div>

                <div class="text-[#6e6862] dark:text-slate-500 sidebar-profile-chevron flex items-center">
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
            </button>
        </div>
    </nav>

    <!-- Overlay mobile & tablette -->
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false; if (window.closeSidebarMobile) window.closeSidebarMobile();"
         onclick="if (window.closeSidebarMobile) window.closeSidebarMobile();"
         class="sidebar-overlay-backdrop lg:hidden fixed inset-0 bg-black/25 backdrop-blur-[1px] z-40 transition-opacity"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Modale : Se connecter en tant que (admin) --}}
    @role('admin')
    <div x-show="impersonateOpen" x-cloak @click.outside="impersonateOpen = false"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-[1px]" @click="impersonateOpen = false"></div>
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
function initAlpineNav() {
    if (!window.Alpine) return;

    if (!Alpine.store('sidebar')) {
        Alpine.store('sidebar', {
            collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
            toggle() {
                window.toggleSidebar();
            }
        });
    }

    if (!Alpine.store('nav')) {
        Alpine.store('nav', {
            open: null,

            init() {
                const serverSection = window.__navDefaultSection ?? null;
                const savedSection = localStorage.getItem('tfg-nav-open-section');
                this.open = serverSection ?? savedSection ?? null;
            },

            toggle(id) {
                if (document.documentElement.classList.contains('sidebar-collapsed')) {
                    window.toggleSidebar();
                }
                this.open = this.open === id ? null : id;
                if (this.open) {
                    localStorage.setItem('tfg-nav-open-section', this.open);
                } else {
                    localStorage.removeItem('tfg-nav-open-section');
                }
            }
        });
    }
}

document.addEventListener('alpine:init', initAlpineNav);
if (window.Alpine) {
    initAlpineNav();
}

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
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.25);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.45);
    }
</style>