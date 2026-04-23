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
        [x-cloak] {
            display: none !important;
        }

        #loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
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
            background: rgba(17, 24, 39, 0.7);
        }

        /* Gestion de la sidebar (w-72 = 18rem) */
        .main-content {
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 1024px) {
            .main-content {
                margin-left: 18rem;
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">

    <div id="loader-overlay">
        <div class="spinner"></div>
    </div>

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
                                <svg x-show="!isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                </svg>
                                <svg x-show="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0z" />
                                </svg>
                            </button>

                            <div class="relative">
                                <button @click="notifyOpen = !notifyOpen" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main content --}}
            <main class="p-6">
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
            }
        });
    </script>
    @stack('scripts')
</body>

</html>