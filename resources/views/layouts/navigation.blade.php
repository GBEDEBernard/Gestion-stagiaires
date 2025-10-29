<nav x-data="{ open: false, openStages: false }" 
     class="fixed top-0 left-0 w-full bg-white border-b border-gray-200 shadow-md z-50">
    
    <!-- Conteneur principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo + titre -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG"
                         class="w-10 h-10 md:w-14 md:h-14 object-contain rounded-lg shadow-md hover:scale-105 transition-transform duration-300">
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800 hover:text-blue-600 transition-colors">
                        Gestion des Stagiaires
                    </h1>
                </a>
            </div>

            <!-- Liens desktop -->
            <div class="hidden md:flex items-center space-x-6">

                <!-- Menu déroulant Stages -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center font-semibold text-gray-700 hover:text-blue-600 focus:outline-none transition">
                        Stages
                        <svg class="ml-1 h-4 w-4 fill-current transform transition-transform duration-200"
                             :class="{ 'rotate-180': open }" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                                  clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                        <x-nav-link :href="route('stages.index')" class="block px-4 py-2 hover:bg-blue-100">Liste des stages</x-nav-link>
                        <x-nav-link :href="route('type_stages.index')" class="block px-4 py-2 hover:bg-blue-100">Types de stage</x-nav-link>
                        <x-nav-link :href="route('services.index')" class="block px-4 py-2 hover:bg-blue-100">Services</x-nav-link>
                        <x-nav-link :href="route('signataires.index')" class="block px-4 py-2 hover:bg-blue-100">Membres</x-nav-link>
                        <x-nav-link :href="route('jours.index')" class="block px-4 py-2 hover:bg-blue-100">Jours</x-nav-link>
                    </div>
                </div>

                <!-- Autres liens -->
                <x-nav-link :href="route('etudiants.index')" class="hover:text-blue-600">Étudiants</x-nav-link>
                <x-nav-link :href="route('badges.index')" class="hover:text-blue-600">Badges</x-nav-link>
                <x-nav-link :href="route('admin.users.index')" class="hover:text-blue-600">👤 Utilisateurs</x-nav-link>
                <x-nav-link :href="route('corbeille.index')" class="hover:text-red-600">🗑️ Corbeille</x-nav-link>
            </div>

            <!-- Dropdown utilisateur -->
            <div class="hidden md:flex items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:text-gray-800 transition">
                            {{ Auth::user()->name }}
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                                      clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                Déconnexion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Bouton hamburger -->
            <div class="flex md:hidden">
                <button @click="open = !open"
                        class="p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Menu mobile -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden bg-white shadow-md border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stages.index')">Liste des stages</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('type_stages.index')">Types de stage</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('services.index')">Services</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('etudiants.index')">Étudiants</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('badges.index')">Badges</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.users.index')">👤 Utilisateurs</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('corbeille.index')">🗑️ Corbeille</x-responsive-nav-link>
        </div>

        <!-- Profil mobile -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Déconnexion
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
