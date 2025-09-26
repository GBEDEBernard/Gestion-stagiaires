<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 mb-8 relative">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo + Titre -->
                <div class="flex items-center justify-center space-y-1 sm:items-start sm:justify-start sm:me-6">
                    <a href="{{ route('dashboard') }}" class="ml-[70px]">
                        <img 
                            src="{{ asset('images/TFGLOGO.png') }}" 
                            alt="Logo Stage TFG"
                            class="w-12 h-12 sm:w-16 sm:h-14 object-contain rounded shadow-md"
                        >
                    </a>
                    <a href="{{ route('dashboard') }}">
                        <h1 class="text-sm sm:text-base mt-4 ml-2 md:text-xl font-semibold text-gray-800 text-center sm:text-left">
                            Gestion des stagiaires
                        </h1>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ms-10 space-x-6 items-center">
                    <!-- Stages Dropdown -->
                    <div x-data="{ openStages: false }" class="relative">
                        <button @click="openStages = !openStages" 
                            class="inline-flex items-center px-3 py-2 font-semibold text-gray-700 hover:text-blue-600 focus:outline-none">
                            Stages
                            <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Dropdown Links -->
                        <div x-show="openStages" @click.away="openStages = false"
                             class="absolute mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                            <x-nav-link :href="route('stages.index')" :active="request()->routeIs('stages.index')" class="block px-4 py-2 hover:bg-blue-100">
                                Liste des Stages
                            </x-nav-link>
                            <x-nav-link :href="route('type_stages.index')" :active="request()->routeIs('type_stages.index')" class="block px-4 py-2 hover:bg-blue-100">
                                Type de Stage
                            </x-nav-link>
                            <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.index')" class="block px-4 py-2 hover:bg-blue-100">
                                Services
                            </x-nav-link>
                            
                             <x-nav-link :href="route('signataires.index')" :active="request()->routeIs('signataires.index')" class="block px-4 py-2 hover:bg-blue-100">
                                Les menbres
                            </x-nav-link>
                            <x-nav-link :href="route('jours.index')" :active="request()->routeIs('jours.index')" class="block px-4 py-2 hover:bg-blue-100">
                                Les Jours
                            </x-nav-link>
                        </div>
                    </div>

                    <!-- Etudiants -->
                    <x-nav-link :href="route('etudiants.index')" :active="request()->routeIs('etudiants.index')">
                        {{ __('Etudiants') }}
                    </x-nav-link>

                   
                    <!-- Badges -->
                    <x-nav-link :href="route('badges.index')" :active="request()->routeIs('badges.index')">
                        {{ __('Numéro des Badges') }}
                    </x-nav-link>
                    {{-- les corbeilles --}}
                    <x-nav-link :href="route('corbeille.index')" :active="request()->routeIs('corbeille.index')">
                        🗑️ <span class="ml-2 text-red-600">Corbeille</span>
                    </x-nav-link>

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
