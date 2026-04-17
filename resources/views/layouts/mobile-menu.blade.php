<div x-data="{
        mobileMenuOpen: false,
        activeTab: 'profil'
    }"
    @toggle-menu.window="mobileMenuOpen = !mobileMenuOpen"
    @close-menu.window="mobileMenuOpen = false"
    x-init="$watch('mobileMenuOpen', val => document.body.style.overflow = val ? 'hidden' : '')">
    
    <div x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="mobileMenuOpen = false"
        class="fixed inset-0 z-[9999] lg:hidden">
        <!-- Overlay cliquable -->
        <div @click="mobileMenuOpen = false"
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <!-- Panel glissant -->
      <div class="relative w-full h-full bg-white dark:bg-gray-900 flex flex-col shadow-2xl
            lg:max-w-md lg:ml-auto">

            <!-- HEADER -->
            <div class="p-5 bg-gradient-to-r from-blue-500 to-purple-600 text-white flex-shrink-0">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                class="w-14 h-14 rounded-2xl object-cover shadow-lg ring-2 ring-white/50">
                        @else
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-white text-2xl font-bold ring-2 ring-white/50">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-bold text-lg">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-blue-100">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <button @click="mobileMenuOpen = false" type="button"
                        class="p-2 hover:bg-white/20 rounded-full transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- TABS -->
            <div class="grid grid-cols-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex-shrink-0">
                <button type="button" @click="activeTab = 'profil'"
                    :class="activeTab === 'profil' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'"
                    class="py-3 flex flex-col items-center gap-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs">Profil</span>
                </button>

                <button type="button" @click="activeTab = 'notifications'"
                    :class="activeTab === 'notifications' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'"
                    class="py-3 flex flex-col items-center gap-1 transition-all duration-200 relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="text-xs">Notifs</span>
                    @if($notificationCount > 0)
                    <span class="absolute top-1 right-3 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                        {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                    </span>
                    @endif
                </button>

                <button type="button" @click="activeTab = 'mode'"
                    :class="activeTab === 'mode' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'"
                    class="py-3 flex flex-col items-center gap-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="text-xs">Thème</span>
                </button>

                <button type="button" @click="activeTab = 'deconnexion'"
                    :class="activeTab === 'deconnexion' ? 'text-red-600 dark:text-red-400 border-b-2 border-red-600 bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'"
                    class="py-3 flex flex-col items-center gap-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-xs">Sortie</span>
                </button>
            </div>

            <!-- CONTENT -->
            <div class="flex-1 overflow-y-auto p-4">

                <!-- PROFIL -->
                <div x-show="activeTab === 'profil'" class="space-y-4">
                    <div class="text-center py-4">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mb-3">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Membre depuis {{ Auth::user()->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                        <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Modifier mon profil</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Informations personnelles, avatar</p>
                            </div>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Préférences</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Langue, notifications, confidentialité</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- NOTIFICATIONS -->
                <div x-show="activeTab === 'notifications'" class="space-y-2">
                    @forelse($notifications as $notification)
                    <a href="{{ route('notifications.index') }}" @click="mobileMenuOpen = false"
                        class="block p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition border-l-4 {{ !$notification->read_at ? 'border-blue-500' : 'border-transparent' }}">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                @if($notification->color === 'blue') bg-blue-100 dark:bg-blue-900/30 text-blue-600
                                @elseif($notification->color === 'amber') bg-amber-100 dark:bg-amber-900/30 text-amber-600
                                @else bg-green-100 dark:bg-green-900/30 text-green-600 @endif">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $notification->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->read_at)
                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></span>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Aucune notification</p>
                        <p class="text-xs text-gray-400 mt-1">Vous êtes à jour !</p>
                    </div>
                    @endforelse
                </div>

                <!-- THÈME -->
                <div x-show="activeTab === 'mode'" class="space-y-3">
                    <button type="button"
                        @click="document.documentElement.classList.remove('dark'); localStorage.setItem('theme','light'); mobileMenuOpen = false"
                        class="w-full p-4 bg-gray-100 dark:bg-gray-800 rounded-xl text-left flex items-center justify-between hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">☀️</span>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Mode clair</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Interface lumineuse et épurée</p>
                            </div>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2"
                            :class="!document.documentElement.classList.contains('dark') ? 'bg-blue-500 border-blue-500' : 'border-gray-400'"></div>
                    </button>
                    <button type="button"
                        @click="document.documentElement.classList.add('dark'); localStorage.setItem('theme','dark'); mobileMenuOpen = false"
                        class="w-full p-4 bg-gray-100 dark:bg-gray-800 rounded-xl text-left flex items-center justify-between hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🌙</span>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Mode sombre</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Confortable pour la nuit</p>
                            </div>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2"
                            :class="document.documentElement.classList.contains('dark') ? 'bg-blue-500 border-blue-500' : 'border-gray-400'"></div>
                    </button>
                </div>

                <!-- DÉCONNEXION -->
                <div x-show="activeTab === 'deconnexion'" class="text-center space-y-6 py-8">
                    <svg class="w-20 h-20 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <div>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">Déconnexion</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Êtes-vous sûr de vouloir quitter ?</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="mobileMenuOpen = false"
                            class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                            Annuler
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition">
                                Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>