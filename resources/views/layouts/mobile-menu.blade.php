<div x-data="{
        mobileMenuOpen: false,
        activeTab: 'profil',
        notifications: [],
        notificationCount: 0,
        loading: false,
        polling: null,
        lastCount: 0,
        async init() {
            await this.fetchNotifications();
            this.polling = setInterval(() => this.fetchNotifications(), 10000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.fetchNotifications();
            });
            $watch('mobileMenuOpen', val => {
                document.body.style.overflow = val ? 'hidden' : '';
                if (val) this.fetchNotifications();
            });
        },
        showNewToast: false,
        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/notifications/unread-json');
                const data = await response.json();
                this.notifications = data.notifications;
                this.notificationCount = data.count;
                if (data.count > this.lastCount) {
                    this.showNewToast = true;
                    setTimeout(() => this.showNewToast = false, 3000);
                }
                this.lastCount = data.count;
            } catch (error) {
                console.error('Notifications fetch error:', error);
            } finally {
                this.loading = false;
            }
        },
        async markAsRead(id) {
            try {
                await fetch(`/notifications/mark-read/${id}`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content, 'Content-Type': 'application/json'}
                });
                this.notifications = this.notifications.filter(n => n.id != id);
                this.notificationCount--;
            } catch (error) {
                Swal.fire('Erreur', 'Impossible de marquer comme lu', 'error');
            }
        },
        async markAllAsRead() {
            const result = await Swal.fire({
                title: 'Tout marquer lu ?',
                text: `Confirmer pour ${this.notificationCount} notification(s)`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            });
            if (result.isConfirmed) {
                try {
                    await fetch('/notifications/mark-all-read-api', {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content}
                    });
                    this.notifications = [];
                    this.notificationCount = 0;
                    this.lastCount = 0;
                } catch (error) {
                    Swal.fire('Erreur', 'Impossible de tout marquer', 'error');
                }
            }
        }
    }"
    @toggle-menu.window="mobileMenuOpen = !mobileMenuOpen"
    @close-menu.window="mobileMenuOpen = false">

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-xs">Profil</span>
                </button>

                <button type="button" @click="activeTab = 'notifications'"
                    :class="activeTab === 'notifications' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 bg-white dark:bg-gray-800 ring-1 ring-blue-500/30' : 'text-gray-500 dark:text-gray-400'"
                    class="py-3 flex flex-col items-center gap-1 transition-all duration-200 relative group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="text-xs font-medium">Notifs</span>
                    <template x-if="notificationCount > 0">
                        <span :class="notificationCount > lastCount ? 'animate-ping-badge bg-red-500/90 shadow-lg shadow-red-500/25' : 'bg-red-500 shadow-md shadow-red-400/30'"
                            class="absolute -top-0.5 -right-0.5 w-5 h-5 text-white text-xs font-bold rounded-full flex items-center justify-center ring-2 ring-white/50 drop-shadow-lg transition-all duration-300"
                            x-text="notificationCount > 9 ? '9+' : notificationCount"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="scale-75 opacity-0"
                            x-transition:enter-end="scale-100 opacity-100"></span>
                    </template>
                </button>

                <button type="button" @click="activeTab = 'mode'"
                    :class="activeTab === 'mode' ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'"
                    class="py-3 flex flex-col items-center gap-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-xs">Thème</span>
                </button>

                <button type="button" @click="activeTab = 'deconnexion'"
                    :class="activeTab === 'deconnexion' ? 'text-red-600 dark:text-red-400 border-b-2 border-red-600 bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'"
                    class="py-3 flex flex-col items-center gap-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Modifier mon profil</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Informations personnelles, avatar</p>
                            </div>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Préférences</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Langue, notifications, confidentialité</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- NOTIFICATIONS DYNAMIQUES 🔥 -->
                <div x-show="activeTab === 'notifications'" class="space-y-3 p-1">
                    <!-- Header avec Mark All -->
                    <div class="flex items-center justify-between mb-4 pt-1 pb-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Notifications <span x-text="notificationCount" class="font-black text-blue-600 dark:text-blue-400"></span>
                        </h3>
                        <template x-if="notificationCount > 0">
                            <button @click="markAllAsRead()" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                                Tout lu
                            </button>
                        </template>
                    </div>

                    <!-- Toast nouvelle notif -->
                    <template x-if="showNewToast">
                        <div x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="translate-x-full opacity-0"
                            x-transition:enter-end="translate-x-0 opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="translate-x-0 opacity-100"
                            x-transition:leave-end="translate-x-full opacity-0"
                            class="fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-2xl shadow-2xl ring-2 ring-green-400/50 z-[10001] animate-bounce">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Nouvelles notifications !</span>
                            </div>
                        </div>
                    </template>

                    <!-- Loading -->
                    <template x-if="loading">
                        <div class="space-y-2">
                            <template x-for="i in 3" :key="i">
                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 animate-pulse border-l-4 border-blue-500/30">
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-4/5 animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- List -->
                    <template x-if="!loading && notifications.length === 0">
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Aucune notification</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Vous êtes à jour ! 🟢</p>
                        </div>
                    </template>

                    <template x-if="!loading && notifications.length > 0">
                        <div class="space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                            <template x-for="notif in notifications" :key="notif.id">
                                <div class="group/notif relative p-3 rounded-xl bg-gradient-to-r from-gray-50 to-blue-50/30 dark:from-gray-800 dark:to-gray-900/70 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800/50 transition-all duration-300 cursor-pointer overflow-hidden"
                                    @click="notif.url ? window.open(notif.url, '_blank') : null"
                                    x-on:swipe.left.window.debounce.500="markAsRead(notif.id)"
                                    style="touch-action: pan-y;">
                                    <!-- Icon color -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1.5"
                                        :style="'background: linear-gradient(to bottom, var(--color-start), var(--color-end)'"
                                        x-bind:style="`--color-start: ${notif.read_at ? '#e5e7eb' : (notif.color === 'blue' ? '#3b82f6' : notif.color === 'amber' ? '#f59e0b' : '#10b981')}; --color-end: var(--color-start)`">
                                    </div>
                                    <div class="flex items-start gap-3 relative z-10">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center ring-1 ring-gray-200 dark:ring-gray-700"
                                            :class="notif.color === 'blue' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600' : notif.color === 'amber' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' : 'bg-green-100 dark:bg-green-900/30 text-green-600'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between mb-1">
                                                <p :class="notif.read_at ? 'text-gray-800 dark:text-gray-200 font-medium' : 'text-gray-900 dark:text-white font-bold bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent'"
                                                    class="text-sm truncate pr-8" x-text="notif.title"></p>
                                                <template x-if="!notif.read_at">
                                                    <button @click.stop="markAsRead(notif.id)" class="ml-2 p-1 rounded-full hover:bg-blue-500/20 transition-colors flex-shrink-0" title="Marquer lu">
                                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </template>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-1" x-text="notif.message"></p>
                                            <p class="text-xs text-gray-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span x-text="notif.created_at"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Swipe indicator -->
                                    <template x-if="!notif.read_at">
                                        <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-emerald-500 to-green-500 opacity-0 group-hover/notif:opacity-100 transition-opacity flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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

        <!-- Styles animations 🔥 -->
        <style>
            @keyframes ping-badge {

                0%,
                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
                }

                50% {
                    transform: scale(1.1);
                    box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
                }
            }

            .animate-ping-badge {
                animation: ping-badge 1.5s infinite;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(148, 163, 184, 0.3);
                border-radius: 2px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(148, 163, 184, 0.5);
            }

            @media (prefers-reduced-motion: reduce) {
                .animate-ping-badge {
                    animation: none;
                }
            }
        </style>
    </div>
</div>