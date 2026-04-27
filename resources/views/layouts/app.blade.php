<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? "{$title} | " . config('app.name', 'Gestion Pro') : config('app.name', 'Gestion Pro') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script>
        (() => {
            try {
                const savedTheme = localStorage.getItem('theme');
                const resolvedTheme = savedTheme === 'dark' || savedTheme === 'light'
                    ? savedTheme
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

                document.documentElement.classList.toggle('dark', resolvedTheme === 'dark');
                document.documentElement.dataset.theme = resolvedTheme;
            } catch (error) {
                document.documentElement.classList.remove('dark');
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/lucide@latest"></script>
    @stack('styles')

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

        .loader-spinner {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            border: 3px solid rgba(148, 163, 184, 0.3);
            border-top-color: #4154f1;
            animation: loader-spin 0.9s linear infinite;
        }

        @keyframes loader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .main-content {
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 1024px) {
            .main-content {
                margin-left: 18rem;
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    @php
        $hasHeader = isset($header) && trim((string) $header) !== '';
        $pageHeading = Route::currentRouteName()
            ? str_replace(['admin.', '.', '_'], ' ', Route::currentRouteName())
            : 'Tableau de bord';
    @endphp

    <div
        x-data="{
            sidebarOpen: false,
            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
            toggleTheme() {
                this.theme = window.setAppTheme(this.theme === 'dark' ? 'light' : 'dark');
            }
        }"
        class="min-h-screen flex"
    >
        <div id="loader-overlay">
            <div class="loader-spinner"></div>
        </div>

        @include('layouts.navigation')

        <div class="flex-1 flex flex-col main-content">
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800 transition-all duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center min-h-16 py-3 gap-4">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <button
                                type="button"
                                @click="sidebarOpen = true"
                                class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                aria-label="Ouvrir le menu"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div class="min-w-0 flex-1">
                                @if($hasHeader)
                                    {{ $header }}
                                @else
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 capitalize truncate">
                                        {{ $pageHeading }}
                                    </h2>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 ml-auto">
                            <button
                                type="button"
                                @click="toggleTheme()"
                                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                                :aria-label="theme === 'dark' ? 'Activer le mode clair' : 'Activer le mode sombre'"
                            >
                                <svg x-show="theme !== 'dark'" x-cloak class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                </svg>
                                <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-5.657-2.343a1 1 0 011.414 0l.707.707A1 1 0 015.05 15.78l-.707-.707a1 1 0 010-1.414zm1.414-8.486a1 1 0 010 1.414l-.707.707A1 1 0 013.636 5.88l.707-.707a1 1 0 011.414 0zM4 10a1 1 0 110 2H3a1 1 0 110-2h1zm13 0a1 1 0 110 2h-1a1 1 0 110-2h1z" />
                                </svg>
                            </button>

                            <a
                                href="{{ route('notifications.index') }}"
                                class="relative p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                                aria-label="Notifications"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if(($notificationCount ?? 0) > 0)
                                    <span class="absolute top-1.5 right-1.5 inline-flex h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('submit', function (event) {
            if (!event.target.matches('[data-confirm-delete]')) {
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Cette action est irréversible !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });

        document.addEventListener('theme:changed', () => {
            window.refreshLucideIcons();
        });
    </script>

    @stack('scripts')
</body>

</html>
