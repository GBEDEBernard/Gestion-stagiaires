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

    <style>
        /* Loader overlay pour mon loading */
                #loader-overlay {
                position: fixed;
                inset: 0;
                background: rgba(255, 255, 255,);
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
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(6px);
            }
            .spinner {
                border: 6px solid #f3f3f3;
                border-top: 6px solid #3498db;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }


        /* Responsive adjustments */
        @media (max-width: 768px) {
            main {
                padding-top: 5rem !important;
            }
        }

        @media (max-width: 480px) {
            .spinner {
                width: 40px;
                height: 40px;
                border-width: 4px;
            }
        }
    </style>
</head>

<body class="font-sans antialiased transition-colors duration-300 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <!-- Loader -->
    <div id="loader-overlay">
        <div class="spinner"></div>
    </div>

    <div class="min-h-screen flex flex-col relative">

        <!-- Navigation -->
        <header class="fixed top-0 left-0 w-full z-50 bg-white/70 dark:bg-gray-900/80 backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @include('layouts.navigation')
            </div>
        </header>

        <!-- Bouton de thème (caché sur mobile) -->
        <div class="fixed top-4 right-6 z-50 hidden sm:block" x-data="{ open: false }">
            <button @click="open = !open"
                    class="px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition flex items-center space-x-1 text-sm sm:text-base">
                🌞 / 🌙
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Menu du thème -->
            <div x-show="open" @click.away="open = false"
                 class="mt-2 w-32 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden transition-all">
                <button @click="document.documentElement.classList.remove('dark'); localStorage.setItem('theme','light'); open=false"
                        class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm">
                    Mode clair ☀️
                </button>
                <button @click="document.documentElement.classList.add('dark'); localStorage.setItem('theme','dark'); open=false"
                        class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm">
                    Mode sombre 🌙
                </button>
            </div>
        </div>

        <!-- Page Content -->
        <main class="flex-1 pt-24 sm:pt-20 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("loader-overlay");
            const html = document.documentElement;

            // Loader sur navigation
            document.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", function () {
                    const href = this.getAttribute("href");
                    if (href && !href.startsWith("#") && !href.startsWith("javascript") &&
                        !this.hasAttribute("data-confirm-edit") && !this.hasAttribute("data-confirm-delete")) {
                        loader.classList.add("active");
                    }
                });
            });

            // Loader sur formulaire
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", function () {
                    if (!form.hasAttribute("data-confirm-delete")) loader.classList.add("active");
                });
            });

            // Confirmation suppression
            document.querySelectorAll("form[data-confirm-delete]").forEach(form => {
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Êtes-vous sûr ?",
                        text: "⚠️ Cette action est irréversible.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#dd0f08ff",
                        cancelButtonColor: "#4a4c4eff",
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
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    const url = this.getAttribute("href");
                    Swal.fire({
                        title: "Modifier cet élément ?",
                        text: "Vous allez passer en mode édition.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#147ee0ff",
                        cancelButtonColor: "#18191aff",
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
            if(localStorage.getItem('theme') === 'dark'){
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
