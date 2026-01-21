<nav x-data="{ open: false, mobileMenuOpen: false }"
    class="sticky top-0 z-50 backdrop-blur-xl bg-white/80 dark:bg-slate-900/80 border-b border-gray-200 dark:border-slate-700 shadow-sm transition-all duration-300">

    <style>
        [data-theme="dark"] {
            color-scheme: dark;
        }
    </style>

    <!-- Conteneur principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo + Brand -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                    <div class="relative">
                        <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG"
                            class="w-10 h-10 rounded-xl shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-300 ring-2 ring-blue-500/20">
                    </div>
                    <div class="hidden sm:flex flex-col">
                        <h1 class="text-sm font-bold text-gray-900 dark:text-white">Gestion Stagiaires</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">TFG SARL</p>
                    </div>
                </a>
            </div>

            <!-- Menu Desktop -->
            <div class="hidden lg:flex items-center gap-1">

                <!-- Menu Stages Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6M6 12a6 6 0 1112 0 6 6 0 01-12 0z" />
                        </svg>
                        Stages
                        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute left-0 mt-2 w-56 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl z-50 overflow-hidden">

                        <a href="{{ route('stages.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Liste des stages
                        </a>
                        <a href="{{ route('type_stages.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a4 4 0 014-4z" />
                            </svg>
                            Types de stage
                        </a>
                        <a href="{{ route('services.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Services
                        </a>
                        <a href="{{ route('signataires.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Signataires
                        </a>
                        <a href="{{ route('jours.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Jours
                        </a>
                    </div>
                </div>

                <!-- Autres liens -->
                <a href="{{ route('etudiants.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
                    </svg>
                    Étudiants
                </a>

                <a href="{{ route('badges.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m0 0c-1.745-2.772-2.747-6.054-2.747-9.571m5.5 0c0 3.517 1.009 6.799 2.753 9.571m0 0c1.745-2.772 2.747-6.054 2.747-9.571M12 11a9 9 0 00-9 9m0 0a9 9 0 018.354 5.646M12 11a9 9 0 019 9m-9-9a9 9 0 018.354 5.646" />
                    </svg>
                    Badges
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 12H9m4.354-11.646l2.121 2.121a4 4 0 01-5.656 5.656l-2.121-2.121a4 4 0 015.656-5.656z" />
                    </svg>
                    Utilisateurs
                </a>

                <a href="{{ route('corbeille.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Corbeille
                </a>
            </div>

            <!-- Actions droite -->
            <div class="flex items-center gap-3">

                <!-- Dropdown Utilisateur -->
                <div class="hidden lg:flex" x-data="{ userOpen: false }">
                    <button @click="userOpen = !userOpen"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="text-gray-900 dark:text-white font-semibold">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': userOpen }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" />
                        </svg>
                    </button>

                    <div x-show="userOpen" @click.away="userOpen = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 mt-12 w-48 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl z-50 overflow-hidden">

                        <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            Paramètres
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bouton Hamburger Mobile -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-6 h-6" :class="{ 'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="w-6 h-6" :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Menu Mobile -->
    <div :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" class="hidden lg:hidden border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-lg">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Dashboard
            </a>
            <a href="{{ route('stages.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Stages
            </a>
            <a href="{{ route('etudiants.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Étudiants
            </a>
            <a href="{{ route('badges.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Badges
            </a>
            <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Utilisateurs
            </a>
            <a href="{{ route('corbeille.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                Corbeille
            </a>
        </div>

        <!-- Profil Mobile -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                Profil
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</nav>