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
<<<<<<< HEAD
        #loader-overlay {
            position: fixed; inset: 0;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            z-index: 9999; opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }

        /* 🔥 Animations notifications parfaites */
        @keyframes pulse-infinite {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .animate-pulse-infinite {
            animation: pulse-infinite 2s infinite;
        }

        @keyframes bounce-subtle {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-2px);
            }
        }

        .animate-bounce-subtle {
            animation: bounce-subtle 0.6s ease-in-out;
        }

        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-slide-down {
            animation: slide-down 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @media (max-width: 640px) {
            [x-text][class*='notificationCount'] {
                min-width: 1.25rem;
                font-size: 0.6875rem;
            }
        }

        #loader-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .dark #loader-overlay {
            background: rgba(0, 0, 0, 0.5);
        }

        .spinner {
            border: 6px solid #f3f3f3; border-top: 6px solid #3b82f6;
            border-radius: 50%; width: 50px; height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg)
            }

            100% {
                transform: rotate(360deg)
            }
=======
        [x-cloak] { display: none !important; }
        
        #loader-overlay {
            position: fixed; inset: 0; background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
            z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
>>>>>>> 007f7e8bd1873fbd9920094cb70cf1592744c13e
        }
        #loader-overlay.active { opacity: 1; pointer-events: all; }
        .dark #loader-overlay { background: rgba(17, 24, 39, 0.7); }

        /* Gestion de la sidebar (w-72 = 18rem) */
        .main-content { transition: margin-left 0.3s ease; }
        @media (min-width: 1024px) { .main-content { margin-left: 18rem; } }

<<<<<<< HEAD
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 16rem;
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }

        [x-cloak] {
            display: none !important;
        }
=======
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
>>>>>>> 007f7e8bd1873fbd9920094cb70cf1592744c13e
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">

<<<<<<< HEAD
    <div id="loader-overlay">
        <div class="spinner"></div>
    </div>

    <div class="min-h-screen flex flex-col relative">
=======
    <div id="loader-overlay"><div class="spinner"></div></div>
>>>>>>> 007f7e8bd1873fbd9920094cb70cf1592744c13e

    <div class="min-h-screen flex">
        {{-- Navigation Sidebar --}}
        @include('layouts.navigation')

        {{-- Zone de contenu principale --}}
        <div class="flex-1 flex flex-col main-content">
            
            {{-- Header --}}
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800 transition-all duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">

                        <div class="flex items-center gap-4">
                            <button onclick="window.dispatchEvent(new CustomEvent('toggle-menu'))"
                                class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 ml-4 capitalize">
                                {{ Route::currentRouteName() ? str_replace(['admin.','.','_'], ' ', Route::currentRouteName()) : 'Tableau de bord' }}
                            </h2>
                        </div>

<<<<<<< HEAD
                    <div class="flex items-center gap-3">
                        <!-- Avatar/Nom mobile — ouvre le modal -->
                        <button onclick="window.dispatchEvent(new CustomEvent('toggle-menu'))"
                            type="button"
                            class="flex items-center gap-2 lg:hidden p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                        </button>

                        <!-- Actions Desktop -->
                        <div class="hidden lg:flex items-center gap-3">
                            <!-- Thème -->
                            <div class="relative" x-data="{ themeOpen: false }">
                                <button @click="themeOpen = !themeOpen"
                                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                    <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                                    </svg>
                                    <svg class="w-5 h-5 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                    </svg>
                                </button>
                                <div x-show="themeOpen" @click.away="themeOpen = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden z-50">
                                    <button @click="document.documentElement.classList.remove('dark'); localStorage.setItem('theme','light'); themeOpen=false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                        ☀️ Clair
                                    </button>
                                    <button @click="document.documentElement.classList.add('dark'); localStorage.setItem('theme','dark'); themeOpen=false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                        🌙 Sombre
                                    </button>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div class="relative" x-data="{
                                notifyOpen: false, 
                                notificationCount: {{ $notificationCount ?? 0 }}, 
                                lastCount: {{ $notificationCount ?? 0 }},
                                polling: null, 
                                init() {
                                    app(NotificationService).generateNotifications();
                                    
                                    this.polling = setInterval(async () => {
                                        try {
                                            const res = await fetch('/notifications/unread-json');
                                            const data = await res.json();
                                            if (data.count > this.notificationCount) {
                                                this.lastCount = this.notificationCount;
                                                this.notificationCount = data.count;
                                            } else {
                                                this.notificationCount = data.count;
                                            }
                                        } catch(e) {}
                                    }, {{ $notificationCount > 0 ? '5000' : '15000' }});
                                }
                            }">

                                <button @click="notifyOpen = !notifyOpen"
                                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition relative group"
                                    :aria-label="'Notifications (' + notificationCount + ')'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <template x-if="notificationCount > 0">
                                        <span class="absolute -top-1 -right-1 w-5.5 h-5.5 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-bold rounded-full flex items-center justify-center ring-2 ring-white/80 shadow-lg 
                                            animate-pulse-infinite scale-100 group-hover:scale-105 transition-all duration-300
                                            dark:ring-gray-900/80 dark:shadow-2xl"
                                            :class="notificationCount > lastCount ? 'animate-bounce-subtle bg-red-600 shadow-red-500/50' : ''"
                                            x-text="notificationCount > 9 ? '9+' : notificationCount"></span>
                                    </template>
                                </button>

                                <!-- Dropdown amélioré -->
                                <div x-show="notifyOpen"
                                    @click.away="notifyOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                    class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden z-[60]"
                                    style="min-width: 320px;">

                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifications</h3>
                                        @if($notificationCount > 0)
                                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="text-xs">
                                            @csrf
                                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:text-blue-700">Tout marquer lu</button>
                                        </form>
                                        @endif
                                    </div>

                                    <div class="max-h-80 overflow-y-auto custom-scrollbar">
                                        @forelse($menuNotifications ?? [] as $notification)
                                        <a href="{{ route('notifications.index') }}"
                                            class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                                    @if($notification->color === 'blue') bg-blue-100 dark:bg-blue-900/30 text-blue-600
                                                    @elseif($notification->color === 'amber') bg-amber-100 dark:bg-amber-900/30 text-amber-600
                                                    @else bg-green-100 dark:bg-green-900/30 text-green-600 @endif">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $notification->title }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $notification->message }}</p>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                        @empty
                                        <div class="px-4 py-12 text-center">
                                            <div class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Aucune notification</p>
                                            <p class="text-xs text-gray-400">Vous êtes à jour !</p>
                                        </div>
                                        @endforelse
                                    </div>

                                    @if($notificationCount > 0)
                                    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                        <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 dark:text-blue-400 font-medium flex items-center gap-1">
                                            Voir toutes les notifications →
                                        </a>
                                    </div>
                                    @endif
                                </div>
=======
                        <div class="flex items-center gap-3 ml-auto" x-data="{
                            isDark: document.documentElement.classList.contains('dark'),
                            notifyOpen: false,
                            notificationCount: {{ $notificationCount ?? 0 }},
                            toggleTheme() {
                                this.isDark = !this.isDark;
                                document.documentElement.classList.toggle('dark');
                                localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                            }
                        }">
                            <div class="hidden md:flex items-center mr-2">
                                {{-- Barre de recherche --}}
                            </div>

                            <button @click="toggleTheme()" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg x-show="!isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" /></svg>
                                <svg x-show="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0z" /></svg>
                            </button>

                            <div class="relative">
                                <button @click="notifyOpen = !notifyOpen" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                </button>
>>>>>>> 007f7e8bd1873fbd9920094cb70cf1592744c13e
                            </div>
                        </div>
                    </div>
                </div>
            </header>

<<<<<<< HEAD
        {{-- 🔥 AVATAR FLOTTANT UNIVERSEL (Fix Admin Desktop) --}}
        <button onclick="window.dispatchEvent(new CustomEvent('toggle-menu'))"
            type="button"
            class="fixed bottom-6 right-6 lg:bottom-8 lg:right-8 z-[10000] p-3 bg-gradient-to-br from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-3xl shadow-2xl ring-2 ring-white/30 hover:ring-white/50 hover:scale-110 active:scale-95 transition-all duration-300 lg:shadow-emerald-500/20 backdrop-blur-sm border border-white/20"
            title="Menu rapide">
            @if(Auth::user()->avatar)
            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                class="w-8 h-8 rounded-2xl object-cover ring-2 ring-white/50">
            @else
            <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white font-bold text-sm ring-2 ring-white/50">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            @endif
        </button>

        @include('layouts.mobile-menu')

        <main class="main-content flex-1 px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-7xl mx-auto">
=======
            {{-- Main content --}}
            <main class="p-6">
>>>>>>> 007f7e8bd1873fbd9920094cb70cf1592744c13e
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('submit', function(e) {
            if (e.target.matches('[data-confirm-delete]')) {
                e.preventDefault();
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: "Cette action est irréversible !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.target.submit();
                    }
                });
<<<<<<< HEAD
            });

            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", function () {
                    if (!form.hasAttribute("data-confirm-delete")) loader.classList.add("active");
                });
            });

            document.querySelectorAll("form[data-confirm-delete]").forEach(form => {
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Êtes-vous sûr ?",
                        text: "⚠️ Cette action est irréversible.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#dc2626",
                        cancelButtonColor: "#6b7280",
                        confirmButtonText: "Oui, supprimer",
                        cancelButtonText: "Annuler"
                    }).then(result => {
                        if (result.isConfirmed) {
                            loader.classList.add("active");
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll("a[data-confirm-edit], button[data-confirm-edit]").forEach(btn => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    const url = this.getAttribute("href");
                    Swal.fire({
                        title: "Modifier cet élément ?",
                        text: "Vous allez passer en mode édition.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#3b82f6",
                        cancelButtonColor: "#6b7280",
                        confirmButtonText: "Oui, modifier",
                        cancelButtonText: "Annuler"
                    }).then(result => {
                        if (result.isConfirmed && url) {
                            loader.classList.add("active");
                            window.location.href = url;
                        }
                    });
                });
            });

            const INACTIVITY_LIMIT = 90 * 1000;

            function resetTimer() {
                localStorage.setItem("lastActivity", Date.now());
            }

            function checkInactivity() {
                const saved = localStorage.getItem("lastActivity") || Date.now();
                if (Date.now() - saved > INACTIVITY_LIMIT) window.location.href = "{{ route('login') }}";
            }
            window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeydown = resetTimer;
            document.onscroll = resetTimer;
            document.onclick = resetTimer;
            setInterval(checkInactivity, 10000);
=======
            }
>>>>>>> 007f7e8bd1873fbd9920094cb70cf1592744c13e
        });
    </script>
</body>
</html>