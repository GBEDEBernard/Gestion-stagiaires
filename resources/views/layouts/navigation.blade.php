<nav x-data="{ open: false }" class="fixed top-0 left-0 bg-white border-b border-gray-200 shadow-md mb-8 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo + Titre -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo Stage TFG"
                         class="w-12 h-12 sm:w-16 sm:h-14 object-contain rounded-lg shadow-md hover:scale-105 transition-transform">
                </a>
                <a href="{{ route('dashboard') }}">
                    <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 hover:text-blue-600 transition">
                        Gestion des stagiaires
                    </h1>
                </a>
            </div>

            <!-- Desktop Links -->
            <div class="hidden sm:flex sm:items-center space-x-6">

                <!-- Stages Dropdown -->
                <div x-data="{ openStages: false }" class="relative">
                    <button @click="openStages = !openStages"
                            class="flex items-center px-3 py-2 font-semibold text-gray-700 hover:text-blue-600 focus:outline-none transition">
                        Stages
                        <svg class="ml-1 h-4 w-4 fill-current transform" :class="{'rotate-180': openStages}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="openStages" @click.away="openStages = false"
                         class="absolute mt-2 w-36 bg-white border rounded-lg shadow-lg z-50 transition ease-in-out duration-200">
                        <x-nav-link :href="route('stages.index')" :active="request()->routeIs('stages.index')" class="block px-4 py-2 hover:bg-blue-100 hover:text-blue-700 transition">
                            Liste des Stages
                        </x-nav-link>
                        <x-nav-link :href="route('type_stages.index')" :active="request()->routeIs('type_stages.index')" class="block px-4 py-2 hover:bg-blue-100 hover:text-blue-700 transition">
                            Type de Stage
                        </x-nav-link>
                        <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.index')" class="block px-4 py-2 hover:bg-blue-100 hover:text-blue-700 transition">
                            Services
                        </x-nav-link>
                        <x-nav-link :href="route('signataires.index')" :active="request()->routeIs('signataires.index')" class="block px-4 py-2 hover:bg-blue-100 hover:text-blue-700 transition">
                            Membres
                        </x-nav-link>
                        <x-nav-link :href="route('jours.index')" :active="request()->routeIs('jours.index')" class="block px-4 py-2 hover:bg-blue-100 hover:text-blue-700 transition">
                            Jours
                        </x-nav-link>
                    </div>
                </div>

                <!-- Autres liens -->
                <x-nav-link :href="route('etudiants.index')" :active="request()->routeIs('etudiants.index')" class="hover:text-blue-600 transition">
                    Étudiants
                </x-nav-link>
                <x-nav-link :href="route('badges.index')" :active="request()->routeIs('badges.index')" class="hover:text-blue-600 transition">
                    Numéro des Badges
                </x-nav-link>
                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')" class="hover:text-blue-600 transition">
                    👤 Utilisateurs
                </x-nav-link>
                <x-nav-link :href="route('corbeille.index')" :active="request()->routeIs('corbeille.index')" class="hover:text-red-600 transition">
                    🗑️ Corbeille
                </x-nav-link>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 transition">
                            {{ Auth::user()->name }}
                            <svg class="ml-1 h-4 w-4 fill-current transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                Déconnexion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white shadow-md border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('etudiants.index')" :active="request()->routeIs('etudiants.index')">
                Étudiants
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('badges.index')" :active="request()->routeIs('badges.index')">
                Badges
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
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
