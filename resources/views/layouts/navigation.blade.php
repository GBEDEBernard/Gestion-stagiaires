<nav x-data="{ 
    sidebarOpen: false, 
    stagesOpen: false,
    mobileMenuOpen: false 
}"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-slate-900 to-slate-800 dark:from-slate-900 dark:to-slate-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <!-- Logo et Titre -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700/50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="relative">
                <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG"
                    class="w-10 h-10 rounded-xl shadow-lg ring-2 ring-blue-500/30 group-hover:ring-blue-500/60 transition-all duration-300">
            </div>
            <div class="flex flex-col">
                <h1 class="text-sm font-bold text-white tracking-wide">Gestion</h1>
                <p class="text-xs text-slate-400">Stagiaires TFG</p>
            </div>
        </a>
    </div>

    <!-- Navigation Principale -->
    <div class="flex flex-col h-full px-3 py-4 overflow-y-auto">

        <!-- Menu Stages avec Sous-menu -->
        <div class="mb-2" x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group"
                :class="open ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" :class="open ? 'text-white' : 'text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Stages</span>
                </div>
                <svg class="w-4 h-4 transform transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                </svg>
            </button>

            <!-- Sous-menu Stages -->
            <div x-show="open" x-collapse @click.outside="open = false"
                class="mt-2 ml-4 space-y-1 overflow-hidden">
                <a href="{{ route('stages.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Liste des stages
                </a>
                <a href="{{ route('type_stages.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a4 4 0 014-4z" />
                    </svg>
                    Types de stage
                </a>
                <a href="{{ route('services.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Services
                </a>
                <a href="{{ route('signataires.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Signataires
                </a>
                <a href="{{ route('jours.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Jours
                </a>
            </div>
        </div>

        <!-- Étudiants -->
        <a href="{{ route('etudiants.index') }}"
            class="flex items-center gap-3 px-4 py-3 mb-1 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-700/50 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
            </svg>
            Étudiants
        </a>

        <!-- Badges -->
        <a href="{{ route('badges.index') }}"
            class="flex items-center gap-3 px-4 py-3 mb-1 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-700/50 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m0 0c-1.745-2.772-2.747-6.054-2.747-9.571m5.5 0c0 3.517 1.009 6.799 2.753 9.571m0 0c1.745-2.772 2.747-6.054 2.747-9.571M12 11a9 9 0 00-9 9m0 0a9 9 0 018.354 5.646M12 11a9 9 0 019 9m-9-9a9 9 0 018.354 5.646" />
            </svg>
            Badges
        </a>

        <!-- Utilisateurs -->
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-3 px-4 py-3 mb-1 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-700/50 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Utilisateurs
        </a>

        <!-- Corbeille -->
        <a href="{{ route('corbeille.index') }}"
            class="flex items-center gap-3 px-4 py-3 mb-4 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Corbeille
        </a>

        <!-- Divider -->
        <div class="border-t border-slate-700/50 my-2"></div>

        <!-- Lien Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 mb-2 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-700/50 hover:text-white transition-colors">
            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Dashboard
        </a>
    </div>

    <!-- Profil Utilisateur en bas du Sidebar -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700/50 bg-slate-800/50" x-data="{ userMenuOpen: false }">
        <button @click="userMenuOpen = !userMenuOpen"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-700/50 transition-colors">
            <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="flex-1 text-left">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>
            <svg class="w-4 h-4 text-slate-400 transform transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
            </svg>
        </button>

        <!-- Menu Utilisateur -->
        <div x-show="userMenuOpen" x-collapse @click.outside="userMenuOpen = false"
            class="mt-2 py-2 bg-slate-700/50 rounded-xl overflow-hidden">
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:text-white hover:bg-slate-600/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                Paramètres
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Bouton Mobile pour ouvrir sidebar -->
<button @click="sidebarOpen = !sidebarOpen"
    class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-slate-800 text-white shadow-lg">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<!-- Overlay pour mobile -->
<div x-show="sidebarOpen" @click="sidebarOpen = false"
    class="lg:hidden fixed inset-0 bg-black/50 z-30 transition-opacity"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
</div>