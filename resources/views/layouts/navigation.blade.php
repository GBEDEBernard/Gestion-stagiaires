@php
    $user = auth()->user();
    $homeRoute = route($user->homeRouteName());

    $navSections = [];

    if ($user->hasRole('etudiant') || $user->hasRole('employe')) {
        $navSections['Mon espace'][] = [
            'label' => 'Pointage',
            'route' => route('presence.pointage'),
            'active' => 'presence.pointage',
            'icon' => 'clock-3',
        ];
        $navSections['Mon espace'][] = [
            'label' => 'Historique',
            'route' => route('presence.historique'),
            'active' => 'presence.historique',
            'icon' => 'history',
        ];
        $navSections['Mon espace'][] = [
            'label' => 'Rapports',
            'route' => route('reports.index'),
            'active' => 'reports.*',
            'icon' => 'file-text',
        ];
        $navSections['Mon espace'][] = [
            'label' => 'Permissions',
            'route' => route('permission-requests.index'),
            'active' => 'permission-requests.index|permission-requests.show',
            'icon' => 'file-signature',
        ];
    }

    if ($user->hasRole('etudiant')) {
        $navSections['Mon espace'][] = [
            'label' => 'Mon Stage',
            'route' => route('student.stage'),
            'active' => 'student.stage',
            'icon' => 'briefcase-business',
        ];
    }

    if ($user->hasRole('admin')) {
        $navSections['Pilotage'][] = [
            'label' => 'Dashboard',
            'route' => route('dashboard'),
            'active' => 'dashboard',
            'icon' => 'layout-dashboard',
        ];
    }

    if ($user->hasRole('superviseur')) {
        $navSections['Pilotage'][] = [
            'label' => 'Dashboard',
            'route' => route('superviseur.dashboard'),
            'active' => 'superviseur.dashboard',
            'icon' => 'layout-dashboard',
        ];
    }

    if ($user->can('accessAdminPresence')) {
        $navSections['Suivi'][] = [
            'label' => 'Pointage Admin',
            'route' => route('admin.presence.index'),
            'active' => 'admin.presence.index',
            'icon' => 'scan-face',
        ];
        $navSections['Suivi'][] = [
            'label' => 'Suivi Pointages',
            'route' => route('admin.presence.pointage-suivi'),
            'active' => 'admin.presence.pointage-suivi',
            'icon' => 'map-pinned',
        ];
        $navSections['Suivi'][] = [
            'label' => 'Anomalies',
            'route' => route('admin.presence.anomalies'),
            'active' => 'admin.presence.anomalies',
            'icon' => 'shield-alert',
            'badge' => $anomaliesCount ?? 0,
        ];
    }

    if (!$user->hasRole('etudiant') && $user->can('daily_reports.view')) {
        $navSections['Suivi'][] = [
            'label' => 'Rapports Admin',
            'route' => route('admin.reports.index'),
            'active' => 'admin.reports.*',
            'icon' => 'clipboard-check',
        ];
    }

    if ($user->can('reviewPermissionRequests') || $user->hasRole('admin') || $user->hasRole('superviseur')) {
        $navSections['Suivi'][] = [
            'label' => 'Validation permissions',
            'route' => route('permission-requests.review.index'),
            'active' => 'permission-requests.review.*',
            'icon' => 'stamp',
        ];
    }

    if ($user->can('stages.view')) {
        $navSections['Administration'][] = [
            'label' => 'Stages',
            'route' => route('stages.index'),
            'active' => 'stages.*',
            'icon' => 'folder-kanban',
            'badge' => $stagesCount ?? null,
        ];
    }

    if ($user->can('users.view')) {
        $navSections['Administration'][] = [
            'label' => 'Utilisateurs',
            'route' => route('admin.users.index'),
            'active' => 'admin.users.*',
            'icon' => 'users',
            'badge' => $usersCount ?? null,
        ];
    }

    if ($user->can('domaines.view')) {
        $navSections['Administration'][] = [
            'label' => 'Domaines',
            'route' => route('domaines.index'),
            'active' => 'domaines.*',
            'icon' => 'building-2',
        ];
    }

    if ($user->can('signataires.view')) {
        $navSections['Administration'][] = [
            'label' => 'Signataires',
            'route' => route('signataires.index'),
            'active' => 'signataires.*',
            'icon' => 'signature',
        ];
    }

    if ($user->can('roles.view')) {
        $navSections['Administration'][] = [
            'label' => 'Roles',
            'route' => route('admin.roles.index'),
            'active' => 'admin.roles.*',
            'icon' => 'shield-check',
            'badge' => $rolesCount ?? null,
        ];
    }

    if ($user->can('sites.view')) {
        $navSections['Administration'][] = [
            'label' => 'Sites',
            'route' => route('sites.index'),
            'active' => 'sites.*',
            'icon' => 'map-pin',
        ];
    }

    if ($user->can('badges.view') && ! $user->hasRole('etudiant')) {
        $navSections['Administration'][] = [
            'label' => 'Badges',
            'route' => route('badges.index'),
            'active' => 'badges.*',
            'icon' => 'badge-check',
            'badge' => $badgesCount ?? null,
        ];
    }

    if ($user->can('etudiants.view') && ! $user->hasRole('etudiant')) {
        $navSections['Administration'][] = [
            'label' => 'Etudiants',
            'route' => route('etudiants.index'),
            'active' => 'etudiants.*',
            'icon' => 'graduation-cap',
            'badge' => $etudiantsCount ?? null,
        ];
    }

    if ($user->can('corbeille.view')) {
        $navSections['Administration'][] = [
            'label' => 'Corbeille',
            'route' => route('corbeille.index'),
            'active' => 'corbeille.*',
            'icon' => 'trash-2',
            'badge' => $trashCount ?? null,
            'danger' => true,
        ];
    }

    if ($user->hasRole('admin')) {
        $navSections['Administration'][] = [
            'label' => 'Taches',
            'route' => route('tasks.index'),
            'active' => 'tasks.*',
            'icon' => 'list-todo',
        ];
    }
@endphp

<nav
    class="fixed inset-y-0 left-0 z-40 w-72 bg-slate-900 transform transition duration-300 flex flex-col"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <div class="flex-shrink-0 border-b border-slate-800/40">
        <a href="{{ $homeRoute }}" class="flex items-center gap-3 px-5 py-5 group hover:bg-slate-800/20 transition">
            <div class="relative">
                <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG" class="w-10 h-10 rounded-xl object-cover transition-transform duration-300 group-hover:scale-105">
                <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>

            <div class="flex flex-col leading-tight min-w-0">
                <h1 class="text-base font-semibold text-white tracking-tight truncate">Gestion Pro</h1>
                <p class="text-xs text-slate-400 truncate">Administrative TFG</p>
            </div>
        </a>

        <button
            type="button"
            @click="sidebarOpen = false"
            class="lg:hidden absolute top-5 right-4 z-[60] p-2 text-white bg-blue-700 rounded-full"
            aria-label="Fermer le menu"
        >
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4">
        @foreach($navSections as $sectionTitle => $items)
            <div class="mb-6 rounded-3xl bg-gradient-to-br from-slate-900/60 to-slate-950/40 p-4 shadow-xl shadow-black/10 backdrop-blur-sm">
                <p class="px-1 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $sectionTitle }}</p>

                <div class="space-y-2">
                    @foreach($items as $item)
                        @php
                            $isActive = collect(explode('|', $item['active']))->contains(fn ($pattern) => request()->routeIs($pattern));
                            $baseClasses = 'text-white/80 hover:bg-white/5 hover:text-white';
                            $activeClasses = !empty($item['danger'])
                                ? 'bg-red-500/20 text-red-100'
                                : 'bg-emerald-500/20 text-emerald-100';
                        @endphp
                        <a
                            href="{{ $item['route'] }}"
                            class="flex items-center justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ $isActive ? $activeClasses : $baseClasses }}"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="p-2 rounded-xl {{ $isActive ? (!empty($item['danger']) ? 'bg-red-500/20 text-red-300' : 'bg-emerald-500/10 text-emerald-300') : 'bg-white/5 text-slate-300' }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                                </div>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </div>

                            @if(isset($item['badge']) && $item['badge'])
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ !empty($item['danger']) ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-white/10 text-white border border-white/10' }}">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="p-4 border-t border-slate-800/40" x-data="{ userMenuOpen: false }">
        <button
            type="button"
            @click="userMenuOpen = !userMenuOpen"
            class="w-full flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-slate-800/30 transition"
        >
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-9 h-9 rounded-full object-cover" alt="Avatar">
            @else
                <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-white text-sm font-medium">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif

            <div class="flex-1 text-left min-w-0">
                <p class="text-sm text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>

            <svg
                class="w-4 h-4 text-slate-500 transition-transform duration-200"
                :class="userMenuOpen ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            x-show="userMenuOpen"
            x-transition
            x-cloak
            @click.outside="userMenuOpen = false"
            class="mt-2 rounded-xl bg-slate-900/60 border border-slate-800/40 overflow-hidden"
        >
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800/40 transition">
                Parametres
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition">
                    Deconnexion
                </button>
            </form>
        </div>
    </div>
</nav>

<div
    x-show="sidebarOpen"
    x-cloak
    @click="sidebarOpen = false"
    class="lg:hidden fixed inset-0 bg-black/60 z-30"
    x-transition
></div>
