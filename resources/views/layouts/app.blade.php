<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .main-content { transition: margin-left 0.3s ease; }
        @media (min-width: 1024px) { .main-content { margin-left: 18rem; } }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.4); border-radius: 10px; }
        .badge-pulse { animation: pulse-ring 1.8s infinite; }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(239,68,68,.6); }
            70%  { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
            100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        }
        #loader-overlay {
            position: fixed; inset: 0; background: rgba(255,255,255,0.7);
            backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
            z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        #loader-overlay.active { opacity: 1; pointer-events: all; }
        .dark #loader-overlay { background: rgba(17,24,39,0.7); }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 overflow-x-hidden">

    <div id="loader-overlay"><div class="spinner"></div></div>

    {{-- Mobile modal (profil / notifs / thème) --}}
    <div x-data="mobilePanel()" @open-mobile-panel.window="open = true; tab = $event.detail?.tab ?? 'notifs'">
        <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9998] lg:hidden"></div>
        <div x-show="open" x-transition.duration.300ms class="fixed bottom-0 left-0 right-0 z-[9999] lg:hidden bg-white dark:bg-gray-900 rounded-t-3xl shadow-2xl max-h-[88vh] flex flex-col">
            <div class="flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div></div>
            <div class="px-5 pt-2 pb-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/'.Auth::user()->avatar) }}" class="w-10 h-10 rounded-2xl object-cover ring-2 ring-indigo-500/30">
                    @else
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @endif
                    <div><p class="font-semibold text-gray-900 dark:text-white text-sm">{{ Auth::user()->name }}</p><p class="text-xs text-gray-400">{{ Auth::user()->email }}</p></div>
                </div>
                <button @click="open = false" class="p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex gap-1 px-5 pt-3 pb-2">
                <button @click="tab='notifs'; fetchNotifs()" :class="tab==='notifs' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'" class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-semibold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifs <span x-show="notifCount > 0" x-text="notifCount" class="min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"></span>
                </button>
                <button @click="tab='profil'" :class="tab==='profil' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'" class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </button>
                <button @click="tab='theme'" :class="tab==='theme' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'" class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Thème
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar px-5 pb-6">
                {{-- Notifications --}}
                <div x-show="tab==='notifs'" class="pt-2 space-y-2">
                    <div x-show="notifCount > 0" class="flex justify-end mb-1">
                        <button @click="markAllRead()" class="text-xs text-indigo-600 font-semibold hover:underline">Tout marquer comme lu</button>
                    </div>
                    <template x-if="loadingNotifs">
                        <div class="space-y-2 pt-2"><template x-for="i in 3"><div class="p-3 rounded-xl bg-gray-100 dark:bg-gray-800 animate-pulse h-16"></div></template></div>
                    </template>
                    <template x-if="!loadingNotifs && notifications.length === 0">
                        <div class="py-12 text-center"><div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3"><svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div><p class="font-semibold text-gray-700 dark:text-gray-300">Tout est à jour !</p></div>
                    </template>
                    <template x-if="!loadingNotifs && notifications.length > 0">
                        <div class="space-y-2">
                            <template x-for="notif in notifications" :key="notif.id">
                                <a :href="notif.url" @click="markRead(notif.id)" class="flex items-start gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-indigo-300">
                                    <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center" :class="{'bg-blue-100 text-blue-600': notif.color==='blue', 'bg-amber-100 text-amber-600': notif.color==='amber', 'bg-green-100 text-green-600': true}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="flex-1"><p class="text-sm font-semibold text-gray-800 dark:text-white truncate" x-text="notif.title"></p><p class="text-xs text-gray-500 mt-0.5" x-text="notif.message"></p><p class="text-xs text-gray-400 mt-1" x-text="notif.created_at"></p></div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Profil --}}
                <div x-show="tab==='profil'" class="pt-3 space-y-3">
                    <a href="{{ route('profile.edit') }}" @click="open=false" class="flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        <div><p class="font-semibold text-gray-900 dark:text-white text-sm">Paramètres du profil</p><p class="text-xs text-gray-500">Avatar, mot de passe, infos</p></div>
                        <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800 border space-y-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Informations</p>
                        <div class="flex gap-2"><span class="text-xs text-gray-400">Nom :</span><span class="text-sm font-medium">{{ Auth::user()->name }}</span></div>
                        <div class="flex gap-2"><span class="text-xs text-gray-400">Email :</span><span class="text-sm font-medium truncate">{{ Auth::user()->email }}</span></div>
                        <div class="flex gap-2"><span class="text-xs text-gray-400">Membre depuis :</span><span class="text-sm font-medium">{{ Auth::user()->created_at->format('d/m/Y') }}</span></div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 font-semibold text-sm hover:bg-red-100">Se déconnecter</button></form>
                </div>

                {{-- Thème --}}
                <div x-show="tab==='theme'" class="pt-3">
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="setTheme('light')" :class="!isDark ? 'ring-2 ring-amber-400 bg-amber-50' : 'bg-gray-50 dark:bg-gray-800'" class="p-5 rounded-2xl border flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-300 to-orange-400 flex items-center justify-center text-white text-2xl">☀️</div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Mode Clair</p>
                            <div class="w-4 h-4 rounded-full border-2 mt-1" :class="!isDark ? 'bg-amber-400 border-amber-400' : 'border-gray-300'"></div>
                        </button>
                        <button @click="setTheme('dark')" :class="isDark ? 'ring-2 ring-indigo-400 bg-indigo-50' : 'bg-gray-50 dark:bg-gray-800'" class="p-5 rounded-2xl border flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-slate-800 flex items-center justify-center text-white text-2xl">🌙</div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Mode Sombre</p>
                            <div class="w-4 h-4 rounded-full border-2 mt-1" :class="isDark ? 'bg-indigo-500 border-indigo-500' : 'border-gray-300'"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Layout principal --}}
    <div class="min-h-screen flex overflow-x-hidden w-full max-w-full">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col main-content min-w-0 overflow-x-hidden">
            <header class="sticky top-0 z-30 bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center h-14 sm:h-16 gap-3">
                        <div class="lg:hidden w-5 mr-8 flex-shrink-0"></div>
                        <h2 class="flex-1 text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate capitalize">
                            {{ Route::currentRouteName() ? str_replace(['admin.','.','_'], ' ', Route::currentRouteName()) : 'Tableau de bord' }}
                        </h2>

                        {{-- Actions desktop --}}
                        <div class="hidden lg:flex items-center gap-2" x-data="{ isDark: document.documentElement.classList.contains('dark'), toggleTheme() { this.isDark = !this.isDark; document.documentElement.classList.toggle('dark', this.isDark); localStorage.setItem('theme', this.isDark ? 'dark' : 'light'); } }">
                            <button @click="toggleTheme()" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg x-show="!isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                                <svg x-show="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0z"/></svg>
                            </button>

                            {{-- Dropdown notifications desktop --}}
                            <div class="relative" x-data="{ open: false, count: {{ $notificationCount ?? 0 }} }">
                                <button @click="open = !open" class="relative p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <span x-show="count > 0" x-text="count > 9 ? '9+' : count" class="badge-pulse absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold"></span>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                                    <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">Notifications</span>
                                        <a href="{{ route('notifications.index') }}" @click="open = false" class="text-xs text-indigo-600 hover:underline">Tout voir</a>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        @php
                                            $latestNotifs = \App\Models\AppNotification::where('user_id', Auth::id())->latest()->limit(5)->get();
                                        @endphp
                                        @forelse($latestNotifs as $notif)
                                            <a href="{{ route('notifications.markRead', $notif->id) }}" @click="open = false"
                                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0 {{ !$notif->read_at ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $notif->title }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notif->message }}</p>
                                                <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                            </a>
                                        @empty
                                            <div class="px-4 py-6 text-center text-gray-500">Aucune notification</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bouton mobile --}}
                        <button onclick="window.dispatchEvent(new CustomEvent('open-mobile-panel', { detail: { tab: 'notifs' } }))" class="lg:hidden relative p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            @if(isset($notificationCount) && $notificationCount > 0)
                                <span class="badge-pulse absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                            @endif
                        </button>
                    </div>
                </div>
            </header>

            <main class="p-3 sm:p-4 md:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function mobilePanel() {
            return {
                open: false,
                tab: 'notifs',
                isDark: document.documentElement.classList.contains('dark'),
                notifications: [],
                notifCount: {{ $notificationCount ?? 0 }},
                loadingNotifs: false,

                async fetchNotifs() {
                    this.loadingNotifs = true;
                    try {
                        const r = await fetch('/notifications/unread-json');
                        const d = await r.json();
                        this.notifications = d.notifications;
                        this.notifCount = d.count;
                    } catch(e) { console.error(e); }
                    finally { this.loadingNotifs = false; }
                },

                async markRead(id) {
                    await fetch('/notifications/mark-read/' + id, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content }
                    });
                    this.notifications = this.notifications.filter(n => n.id != id);
                    this.notifCount = Math.max(0, this.notifCount - 1);
                },

                async markAllRead() {
                    await fetch('/notifications/mark-all-read-api', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content }
                    });
                    this.notifications = [];
                    this.notifCount = 0;
                },

                setTheme(theme) {
                    const isDark = theme === 'dark';
                    this.isDark = isDark;
                    document.documentElement.classList.toggle('dark', isDark);
                    localStorage.setItem('theme', theme);
                }
            }
        }

        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();

        document.addEventListener('submit', function(e) {
            if (e.target.matches('[data-confirm-delete]')) {
                e.preventDefault();
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: 'Cette action est irréversible !',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then(r => { if (r.isConfirmed) e.target.submit(); });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>