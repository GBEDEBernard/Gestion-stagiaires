<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js pour les collapses -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Loader overlay */
        #loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        #loader-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .dark #loader-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(6px);
        }

        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3b82f6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Transition du contenu principal */
        .main-content {
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 1024px) {
            .main-content {
                margin-left: 16rem;
                /* w-64 */
            }
        }

        /* Scrollbar personnalisée pour le sidebar */
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
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    <!-- Loader -->
    <div id="loader-overlay">
        <div class="spinner"></div>
    </div>

    <div class="min-h-screen flex flex-col relative">

        <!-- Sidebar (Navigation gauche) -->
        @include('layouts.navigation')

        <!-- Header Propre -->
        <header class="sticky top-0 z-30 main-content bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700 shadow-sm transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">

                    <!-- Titre de la page / Breadcrumb -->
                    <div class="flex items-center gap-4">
                        <!-- Bouton Menu Mobile -->
                        <button @click="$dispatch('toggle-sidebar')"
                            class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Titre dynamique -->
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 ml-4">
                                {{ Route::currentRouteName() ? ucfirst(str_replace(['admin.', '.', '_'], ' ', Route::currentRouteName())) : 'Dashboard' }}
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block ml-4">
                                {{ now()->format('d/m/Y') }} • Gestion des Stagiaires TFG
                            </p>
                        </div>
                    </div>

                    <!-- Actions droites -->
                    <div class="flex items-center gap-3">

                        <!-- Theme Toggle -->
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

                            <!-- Menu Theme -->
                            <div x-show="themeOpen" @click.away="themeOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden">
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
                        <div class="relative" x-data="{ notifyOpen: false }">
                            <button @click="notifyOpen = !notifyOpen"
                                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition relative">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($notificationCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                    {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                                </span>
                                @endif
                            </button>

                            <!-- Dropdown Notifications -->
                            <div x-show="notifyOpen" @click.away="notifyOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifications</h3>
                                    @if($notificationCount > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="text-xs">
                                        @csrf
                                        <button type="submit" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                            Tout marquer lu
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                <div class="max-h-80 overflow-y-auto">
                                    @forelse($notifications as $notification)
                                    <a href="{{ $notification->url }}"
                                        onclick="event.preventDefault(); document.getElementById('mark-read-form-{{ $notification->id }}').submit();"
                                        class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <form id="mark-read-form-{{ $notification->id }}" action="{{ route('notifications.markRead', $notification->id) }}" method="GET" style="display: none;">
                                        </form>
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center 
                                                @if($notification->color === 'blue') bg-blue-100 dark:bg-blue-900/30 text-blue-600
                                                @elseif($notification->color === 'amber') bg-amber-100 dark:bg-amber-900/30 text-amber-600
                                                @else bg-green-100 dark:bg-green-900/30 text-green-600
                                                @endif">
                                                @if($notification->icon === 'user-plus')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                </svg>
                                                @elseif($notification->icon === 'clock')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                                    {{ $notification->title }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $notification->message }}
                                                </p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune notification</p>
                                    </div>
                                    @endforelse
                                </div>

                                @if($notificationCount > 0)
                                <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                    <a href="#" class="text-xs text-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Voir toutes les notifications →
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Nom Utilisateur (Mobile) -->
                        <div class="flex items-center gap-2 lg:hidden">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu Principal -->
        <main class="main-content flex-1 px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById("loader-overlay");
            const html = document.documentElement;

            // Loader sur navigation
            document.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", function() {
                    const href = this.getAttribute("href");
                    if (href && !href.startsWith("#") && !href.startsWith("javascript") &&
                        !this.hasAttribute("data-confirm-edit") && !this.hasAttribute("data-confirm-delete")) {
                        loader.classList.add("active");
                    }
                });
            });

            // Loader sur formulaire
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", function() {
                    if (!form.hasAttribute("data-confirm-delete")) loader.classList.add("active");
                });
            });

            // Confirmation suppression
            document.querySelectorAll("form[data-confirm-delete]").forEach(form => {
                form.addEventListener("submit", function(e) {
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
                    }).then((result) => {
                        if (result.isConfirmed) {
                            loader.classList.add("active");
                            form.submit();
                        }
                    });
                });
            });

            // Confirmation modification
            document.querySelectorAll("a[data-confirm-edit], button[data-confirm-edit]").forEach(btn => {
                btn.addEventListener("click", function(e) {
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
                    }).then((result) => {
                        if (result.isConfirmed && url) {
                            loader.classList.add("active");
                            window.location.href = url;
                        }
                    });
                });
            });

            // Thème sauvegardé
            if (localStorage.getItem('theme') === 'dark') {
                html.classList.add('dark');
            }

            // Inactivité → déconnexion
            const INACTIVITY_LIMIT = 90 * 1000;
            let lastActivity = Date.now();

            function resetTimer() {
                lastActivity = Date.now();
                localStorage.setItem("lastActivity", lastActivity);
            }

            function checkInactivity() {
                const saved = localStorage.getItem("lastActivity") || Date.now();
                const now = Date.now();
                if (now - saved > INACTIVITY_LIMIT) {
                    window.location.href = "{{ route('login') }}";
                }
            }

            window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeydown = resetTimer;
            document.onscroll = resetTimer;
            document.onclick = resetTimer;
            setInterval(checkInactivity, 10000);
        });
    </script>

    @stack('scripts')
</body>

</html>