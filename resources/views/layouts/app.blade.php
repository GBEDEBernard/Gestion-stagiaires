<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'gestion des personnels') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600&display=swap" rel="stylesheet" />
   
    @vite(['resources/css/app.css', 'resources/js/app.js'])
   
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
            if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 1024) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();

        window.toggleSidebar = function() {
            if (window.innerWidth < 1024) {
                var isOpen = document.documentElement.classList.toggle('sidebar-mobile-open');
                window.dispatchEvent(new CustomEvent('toggle-sidebar-mobile', { detail: { open: isOpen } }));
            } else {
                var isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
                if (window.Alpine && window.Alpine.store('sidebar')) {
                    window.Alpine.store('sidebar').collapsed = isCollapsed;
                }
            }
        };

        window.closeSidebarMobile = function() {
            document.documentElement.classList.remove('sidebar-mobile-open');
            window.dispatchEvent(new CustomEvent('close-sidebar'));
        };

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024 && document.documentElement.classList.contains('sidebar-mobile-open')) {
                window.closeSidebarMobile();
            }
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            height: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #f1f0ed;
        }

        html.dark {
            background-color: #1f232d !important;
        }

        body {
            height: 100%;
            min-height: 100%;
            max-height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden !important;
            background-color: #f1f0ed;
        }

        html.dark body, body.dark {
            background-color: #1f232d !important;
        }

        .main-content {
            margin: 0.5rem;
            height: calc(100vh - 1rem);
            height: calc(100dvh - 1rem);
            max-height: calc(100vh - 1rem);
            max-height: calc(100dvh - 1rem);
            border-radius: 10px;
            border: 1px solid #e3e0da;
            background-color: #ffffff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: margin 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .main-content {
            background-color: #131519;
            border-color: #222630;
        }

        .sidebar {
            width: 250px;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f1f0ed;
        }

        html.dark .sidebar, body.dark .sidebar {
            background-color: #1f232d !important;
        }

        @media (max-width: 1023px) {
            .sidebar {
                z-index: 50 !important;
                transform: translateX(-100%);
            }
            html.sidebar-mobile-open .sidebar {
                transform: translateX(0) !important;
            }
            .sidebar-overlay-backdrop {
                display: none;
            }
            html.sidebar-mobile-open .sidebar-overlay-backdrop {
                display: block !important;
                opacity: 1 !important;
                pointer-events: auto !important;
            }
        }

        @media (min-width: 1024px) {
            .main-content {
                margin: 0.5rem 0.5rem 0.5rem calc(250px + 0.5rem);
                height: calc(100vh - 1rem);
                height: calc(100dvh - 1rem);
                max-height: calc(100vh - 1rem);
                max-height: calc(100dvh - 1rem);
                border-radius: 10px;
                border: 1px solid #e3e0da;
                transition: margin 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .dark .main-content {
                border-color: #222630;
            }

            html.sidebar-collapsed .sidebar {
                width: 4.5rem !important;
            }

            html.sidebar-collapsed .main-content {
                margin: 0.5rem 0.5rem 0.5rem calc(4.5rem + 0.5rem) !important;
            }

            html.sidebar-collapsed .sidebar .sidebar-logo-text,
            html.sidebar-collapsed .sidebar .sidebar-profile-text,
            html.sidebar-collapsed .sidebar .sidebar-profile-chevron,
            html.sidebar-collapsed .sidebar [x-collapse],
            html.sidebar-collapsed .sidebar button svg.rotate-90,
            html.sidebar-collapsed .sidebar button svg.transform,
            html.sidebar-collapsed .sidebar span.truncate {
                display: none !important;
            }

            html.sidebar-collapsed .sidebar a.group,
            html.sidebar-collapsed .sidebar button.group,
            html.sidebar-collapsed .sidebar a,
            html.sidebar-collapsed .sidebar button {
                justify-content: center !important;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            html.sidebar-collapsed .sidebar .sidebar-logo-container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                display: flex !important;
                justify-content: center !important;
            }

            html.sidebar-collapsed .sidebar .sidebar-logo-container a {
                justify-content: center !important;
                gap: 0 !important;
            }

            html.sidebar-collapsed .sidebar .sidebar-logo-container img {
                width: 2.5rem !important;
                height: 2.5rem !important;
            }

            html.sidebar-collapsed .sidebar .sidebar-menu-header {
                display: none !important;
            }

            html.sidebar-collapsed .sidebar .flex.items-center.gap-3.min-w-0 {
                gap: 0 !important;
                justify-content: center !important;
                width: 100% !important;
            }
        }

        .main-content-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            -webkit-overflow-scrolling: touch;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.35) transparent;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.35);
            border-radius: 9999px;
            transition: background 0.2s ease;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.55);
        }

        .dark .custom-scrollbar {
            scrollbar-color: rgba(148, 163, 184, 0.25) transparent;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.22);
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.45);
        }

        .badge-pulse {
            animation: pulse-ring 1.8s infinite;
        }

        @keyframes urgentTextShimmer {
            0% {
                background-position: 200% center;
                opacity: 0.92;
                transform: scale(1);
            }
            50% {
                opacity: 1;
                transform: scale(1.03);
            }
            100% {
                background-position: -200% center;
                opacity: 0.92;
                transform: scale(1);
            }
        }

        .urgent-text-animated {
            background: linear-gradient(90deg, #fef08a 0%, #ffffff 25%, #fde047 50%, #ffffff 75%, #fef08a 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: urgentTextShimmer 3s ease-in-out infinite;
            filter: drop-shadow(0 1px 3px rgba(185, 28, 28, 0.8));
            display: inline-block;
            font-weight: 900;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        @keyframes urgentBeacon {
            0%, 100% { transform: scale(1) rotate(0deg); }
            15% { transform: scale(1.18) rotate(-8deg); }
            30% { transform: scale(1.18) rotate(8deg); }
            45% { transform: scale(1.1) rotate(-4deg); }
            60% { transform: scale(1) rotate(0deg); }
        }

        .urgent-beacon-icon {
            animation: urgentBeacon 2.5s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, .6);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Animation Shimmer Skeleton Mon-Stage */
        @keyframes ks-sk-shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .ks-sk-shimmer {
            position: relative;
            overflow: hidden;
        }

        .ks-sk-shimmer::after {
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.45) 45%,
                rgba(255, 255, 255, 0.75) 50%,
                rgba(255, 255, 255, 0.45) 55%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: ks-sk-shimmer 1.8s infinite;
            content: '';
        }

        .dark .ks-sk-shimmer::after {
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.04) 45%,
                rgba(255, 255, 255, 0.09) 50%,
                rgba(255, 255, 255, 0.04) 55%,
                rgba(255, 255, 255, 0) 100%
            );
        }

        .ks-sk-block {
            background-color: #e4e1d9;
            border-radius: 6px;
        }
        .dark .ks-sk-block {
            background-color: #1e222c;
        }
    </style>
</head>

<body class="font-sans antialiased bg-[#f1f0ed] dark:bg-[#1f232d] overflow-hidden m-0 p-0">

    {{-- Volet mobile : Notifications uniquement (Palette et style Mon Stage) --}}
    <div x-data="mobilePanel()" x-cloak @open-mobile-panel.window="isOpen = true; fetchNotifs()">
        {{-- Backdrop léger et subtil --}}
        <div x-show="isOpen"
             x-cloak
             x-transition.opacity.duration.250ms
             @click="isOpen = false"
             class="fixed inset-0 bg-black/25 backdrop-blur-[1px] z-[9998] lg:hidden"
             style="display: none;"></div>

        {{-- Tiroir inférieur mobile --}}
        <div x-show="isOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 z-[9999] lg:hidden bg-[#fffefa] dark:bg-[#131519] border-t border-[#dcdad2] dark:border-[#222630] rounded-t-[20px] shadow-2xl max-h-[85vh] flex flex-col overflow-hidden"
             style="display: none;">

            {{-- Poignée supérieure --}}
            <div class="flex justify-center pt-2.5 pb-1">
                <div class="w-10 h-1 bg-[#dcdad2] dark:bg-[#262a34] rounded-full"></div>
            </div>

            {{-- En-tête : Notifications & Actions --}}
            <div class="px-4 py-3 flex items-center justify-between border-b border-[#ecebe3] dark:border-[#222630]">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-[8px] bg-[#eef1fb] dark:bg-[rgba(28,62,150,0.25)] border border-[#1c3e96]/20 text-[#1c3e96] dark:text-[#60a5fa] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-[15px] text-[#241f1b] dark:text-[#f8fafc]">Notifications</h3>
                        <span x-show="notifCount > 0"
                              x-text="notifCount"
                              class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#1c3e96] text-white shadow-sm"></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button x-show="notifCount > 0"
                            @click="markAllRead()"
                            type="button"
                            class="text-xs text-[#1c3e96] dark:text-[#60a5fa] font-semibold hover:underline">
                        Tout marquer comme lu
                    </button>
                    <button @click="isOpen = false"
                            type="button"
                            aria-label="Fermer"
                            class="h-8 w-8 rounded-[8px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#262a34] text-[#6e6862] dark:text-[#94a3b8] hover:text-[#241f1b] dark:hover:text-[#f8fafc] flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Liste scrollable des notifications --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar px-4 py-3 space-y-2">
                {{-- Chargement skeleton --}}
                <template x-if="loadingNotifs">
                    <div class="space-y-2 pt-1">
                        <template x-for="i in 3">
                            <div class="p-3 rounded-[10px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#222630] animate-pulse h-16"></div>
                        </template>
                    </div>
                </template>

                {{-- État vide --}}
                <template x-if="!loadingNotifs && notifications.length === 0">
                    <div class="py-12 text-center flex flex-col items-center justify-center">
                        <div class="w-14 h-14 rounded-[12px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#262a34] flex items-center justify-center mb-3 text-[#6e6862] dark:text-[#94a3b8]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </div>
                        <p class="font-semibold text-sm text-[#241f1b] dark:text-[#f8fafc]">Tout est à jour !</p>
                        <p class="text-xs text-[#6e6862] dark:text-[#a89f93] mt-1">Vous n'avez aucune notification en attente.</p>
                    </div>
                </template>

                {{-- Liste des notifications --}}
                <template x-if="!loadingNotifs && notifications.length > 0">
                    <div class="space-y-2">
                        <template x-for="notif in notifications" :key="notif.id">
                            <a :href="notif.url"
                               @click="markRead(notif.id)"
                               class="flex items-start gap-3 p-3 rounded-[10px] bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] hover:border-[#1c3e96]/40 dark:hover:border-[#60a5fa]/40 hover:bg-[#f8f7f4] dark:hover:bg-[#181b22] transition-colors group">
                                <div class="shrink-0 w-8 h-8 rounded-[8px] flex items-center justify-center"
                                     :class="{
                                         'bg-[#dcfce7] text-[#15803d] border border-[#15803d]/30': notif.color==='green',
                                         'bg-[#eef1fb] text-[#1c3e96] border border-[#1c3e96]/30': notif.color==='blue',
                                         'bg-[#fef3c7] text-[#b45309] border border-[#b45309]/30': notif.color==='amber',
                                         'bg-[#fee2e2] text-[#b91c1c] border border-[#b91c1c]/30': notif.color==='red'
                                     }">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc] truncate" x-text="notif.title"></p>
                                    <p class="text-xs text-[#6e6862] dark:text-[#a89f93] mt-0.5" x-text="notif.message"></p>
                                    <p class="text-[11px] text-[#bfbab2] dark:text-[#64748b] mt-1" x-text="notif.created_at"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Pied de page / Lien historique --}}
            <div class="px-4 py-3 border-t border-[#ecebe3] dark:border-[#222630] bg-[#f8f7f4] dark:bg-[#181b22]">
                <a href="{{ route('notifications.index') }}"
                   @click="isOpen = false"
                   class="w-full flex items-center justify-center gap-2 py-2.5 rounded-[8px] bg-white dark:bg-[#131519] border border-[#dcdad2] dark:border-[#262a34] text-xs sm:text-sm font-semibold text-[#241f1b] dark:text-[#f8fafc] hover:border-[#1c3e96] hover:text-[#1c3e96] dark:hover:border-[#60a5fa] dark:hover:text-[#60a5fa] transition-colors">
                    <span>Consulter l'historique des notifications</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Layout principal --}}
    <div class="h-full h-screen h-[100dvh] flex overflow-hidden w-full max-w-full bg-[#f1f0ed] dark:bg-[#1f232d]">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col main-content min-w-0">
            <div class="main-content-scroll custom-scrollbar">
            @if(session()->has('impersonate_user_id'))
            @php $impersonated = \App\Models\User::find(session('impersonate_user_id')); @endphp
            @if($impersonated)
            <div class="flex items-center justify-between gap-3 px-4 sm:px-6 py-2 bg-gradient-to-r from-fuchsia-600 to-purple-700 text-white text-sm">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    <span class="truncate">Vous consultez le compte de <strong>{{ $impersonated->name }}</strong> ({{ $impersonated->hasRole('etudiant') ? 'Stagiaire' : 'Employé' }})</span>
                </div>
                <form method="POST" action="{{ route('impersonate.leave') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/15 hover:bg-white/25 transition text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Revenir à mon espace
                    </button>
                </form>
            </div>
            @endif
            @endif
            <header class="sticky top-0 z-30 bg-white/90 dark:bg-[#131519] backdrop-blur-xl border-none shadow-none">
                <div class="px-3 sm:px-6 lg:px-8">
                    <div class="flex items-center h-14 gap-2 sm:gap-3">
                        {{-- Bouton Toggle Sidebar (à gauche comme sur l'image) --}}
                        <button onclick="window.toggleSidebar()"
                                type="button"
                                title="Basculer le menu latéral"
                                class="h-8 w-8 sm:h-9 sm:w-9 rounded-[8px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#262a34] text-[#6e6862] dark:text-[#94a3b8] hover:text-[#241f1b] dark:hover:text-[#f8fafc] hover:border-[#bfbab2] dark:hover:border-[#383e4d] transition-all flex items-center justify-center shrink-0 cursor-pointer">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="3" />
                                <path d="M9 3v18" />
                                <path d="M4.75 8h1.5" />
                                <path d="M4.75 12h1.5" />
                                <path d="M4.75 16h1.5" />
                            </svg>
                        </button>

                        <nav aria-label="Fil d'Ariane" class="flex-1 min-w-0 flex items-center justify-start">
                            @php
                                $routeName = Route::currentRouteName() ?? '';
                                
                                $translations = [
                                    'dashboard'      => 'Tableau de bord',
                                    'stages'         => 'Stages',
                                    'stage'          => 'Mon Stage',
                                    'type_stages'    => 'Types de stages',
                                    'ecoles'         => 'Écoles de provenance',
                                    'badges'         => 'Badges',
                                    'users'          => 'Utilisateurs',
                                    'roles'          => 'Rôles',
                                    'etudiants'      => 'Stagiaires',
                                    'employes'       => 'Employés',
                                    'personnels'     => 'Personnels',
                                    'sites'          => 'Sites',
                                    'domaines'       => 'Domaines',
                                    'presence'       => 'Présence',
                                    'pointage'       => 'Pointage',
                                    'historique'     => 'Historique',
                                    'anomalies'      => 'Anomalies',
                                    'attestation'    => 'Attestations',
                                    'attestations'   => 'Attestations',
                                    'rapports'       => 'Rapports',
                                    'reports'        => 'Rapports',
                                    'tasks'          => 'Tâches',
                                    'taches'         => 'Tâches',
                                    'notifications'  => 'Notifications',
                                    'profile'        => 'Profil',
                                    'corbeille'      => 'Corbeille',
                                    'trash'          => 'Corbeille',
                                    'holidays'       => 'Jours fériés',
                                    'jours'          => 'Jours de présence',
                                    'settings'       => 'Paramètres',
                                    'permissions'    => 'Permissions',
                                    'logs'           => 'Journaux',
                                    'statistics'     => 'Statistiques',
                                    'statistiques'   => 'Statistiques',
                                    'evaluations'    => 'Évaluations',
                                    'criteria'       => 'Critères',
                                    'work-schedules' => 'Horaires',
                                    'work_schedules' => 'Horaires',
                                    'create'         => 'Nouveau',
                                    'edit'           => 'Modifier',
                                    'show'           => 'Détails',
                                    'detail'         => 'Détails',
                                    'details'        => 'Détails',
                                    'scan'           => 'Scan QR',
                                ];

                                // Nettoyer les préfixes
                                $cleaned = preg_replace('/^(admin|web|app|student|employee)\./', '', $routeName);
                                $parts = array_values(array_filter(explode('.', $cleaned), fn($p) => $p !== 'index'));

                                if (count($parts) > 1 && $parts[0] === 'dashboard') {
                                    array_shift($parts);
                                }

                                $breadcrumbs = [];

                                if (empty($parts) || $cleaned === 'dashboard') {
                                    $breadcrumbs[] = [
                                        'label' => 'Tableau de bord',
                                        'url'   => null,
                                    ];
                                } else {
                                    foreach ($parts as $idx => $part) {
                                        $isLast = ($idx === count($parts) - 1);
                                        $label = $translations[$part] ?? ucfirst(str_replace(['_', '-'], ' ', $part));

                                        $targetUrl = null;
                                        if (!$isLast) {
                                            if (Route::has($part . '.index')) {
                                                $targetUrl = route($part . '.index');
                                            } elseif (Route::has('admin.' . $part . '.index')) {
                                                $targetUrl = route('admin.' . $part . '.index');
                                            } elseif (Route::has($part)) {
                                                $targetUrl = route($part);
                                            }
                                        }

                                        $breadcrumbs[] = [
                                            'label' => $label,
                                            'url'   => $targetUrl,
                                        ];
                                    }
                                }
                            @endphp

                            <div class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-[8px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#262a34] max-w-full overflow-hidden text-xs sm:text-sm">
                                <a href="{{ route('dashboard') }}" class="text-[#6e6862] dark:text-[#94a3b8] hover:text-[#1c3e96] dark:hover:text-[#60a5fa] transition-colors flex items-center shrink-0" title="Tableau de bord">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                </a>

                                @foreach($breadcrumbs as $index => $crumb)
                                    <svg class="w-3 h-3 text-[#bfbab2] dark:text-[#4b5563] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>

                                    @if(!empty($crumb['url']) && !$loop->last)
                                        <a href="{{ $crumb['url'] }}" class="text-[#6e6862] dark:text-[#94a3b8] hover:text-[#241f1b] dark:hover:text-[#f8fafc] transition-colors truncate max-w-[110px] sm:max-w-[180px]">
                                            {{ $crumb['label'] }}
                                        </a>
                                    @else
                                        <span class="{{ $loop->last ? 'text-[#241f1b] dark:text-[#f8fafc] font-semibold' : 'text-[#6e6862] dark:text-[#94a3b8]' }} truncate max-w-[130px] sm:max-w-[240px]">
                                            {{ $crumb['label'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </nav>

                        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0"
                             x-data="{ isDark: document.documentElement.classList.contains('dark'), toggleTheme() { this.isDark = !this.isDark; document.documentElement.classList.toggle('dark', this.isDark); localStorage.setItem('theme', this.isDark ? 'dark' : 'light'); window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.isDark } })); } }"
                             @theme-changed.window="isDark = $event.detail.dark">
                            {{-- Dropdown d'Alerte Urgente dans le Header --}}
                            <x-urgent-alert />

                            {{-- Bouton changement de mode (clair / sombre) --}}
                            <button @click="toggleTheme()"
                                    type="button"
                                    title="Changer de thème"
                                    class="h-8 w-8 sm:h-9 sm:w-9 rounded-[8px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#262a34] text-[#6e6862] dark:text-[#94a3b8] hover:text-[#241f1b] dark:hover:text-[#f8fafc] hover:border-[#bfbab2] dark:hover:border-[#383e4d] transition-colors flex items-center justify-center shrink-0">
                                <svg x-show="!isDark" class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                                <svg x-show="isDark" x-cloak class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                            </button>

                            {{-- Dropdown notifications desktop --}}
                            <div class="hidden lg:block relative" x-data="{ open: false, count: {{ $notificationCount ?? 0 }} }">
                                <button @click="open = !open"
                                        type="button"
                                        title="Notifications"
                                        class="relative h-8 w-8 sm:h-9 sm:w-9 rounded-[8px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#262a34] text-[#6e6862] dark:text-[#94a3b8] hover:text-[#241f1b] dark:hover:text-[#f8fafc] hover:border-[#bfbab2] dark:hover:border-[#383e4d] transition-colors flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    <span x-show="count > 0" x-text="count > 9 ? '9+' : count" class="badge-pulse absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-red-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold"></span>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-[#fffefa] dark:bg-[#131519] rounded-[10px] shadow-xl border border-[#dcdad2] dark:border-[#222630] overflow-hidden z-50">
                                    <div class="p-3 border-b border-[#ecebe3] dark:border-[#222630] flex justify-between items-center bg-[#f8f7f4] dark:bg-[#181b22]">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-sm text-[#241f1b] dark:text-[#f8fafc]">Notifications</span>
                                            <span x-show="count > 0" x-text="count" class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#1c3e96] text-white"></span>
                                        </div>
                                        <a href="{{ route('notifications.index') }}" @click="open = false" class="text-xs text-[#1c3e96] dark:text-[#60a5fa] font-semibold hover:underline">Tout voir</a>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                        @php
                                        $latestNotifs = \App\Models\AppNotification::where('user_id', Auth::id())->latest()->limit(5)->get();
                                        @endphp
                                        @forelse($latestNotifs as $notif)
                                        <a href="{{ route('notifications.markRead', $notif->id) }}" @click="open = false"
                                            class="block px-4 py-3 hover:bg-[#f8f7f4] dark:hover:bg-[#181b22] border-b border-[#ecebe3] dark:border-[#222630] last:border-0 transition-colors {{ !$notif->read_at ? 'bg-[#eef1fb]/40 dark:bg-[rgba(28,62,150,0.15)]' : '' }}">
                                            <p class="text-sm font-medium text-[#241f1b] dark:text-[#f8fafc] truncate">{{ $notif->title }}</p>
                                            <p class="text-xs text-[#6e6862] dark:text-[#a89f93] mt-1">{{ $notif->message }}</p>
                                            <p class="text-[11px] text-[#bfbab2] dark:text-[#64748b] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                        </a>
                                        @empty
                                        <div class="px-4 py-6 text-center text-xs text-[#6e6862] dark:text-[#a89f93]">Aucune notification</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Bouton notifications mobile --}}
                            <button onclick="window.dispatchEvent(new CustomEvent('open-mobile-panel'))"
                                    type="button"
                                    title="Notifications"
                                    class="lg:hidden relative h-8 w-8 rounded-[8px] bg-[#f8f7f4] dark:bg-[#181b22] border border-[#dcdad2] dark:border-[#262a34] text-[#6e6862] dark:text-[#94a3b8] hover:text-[#241f1b] dark:hover:text-[#f8fafc] hover:border-[#bfbab2] dark:hover:border-[#383e4d] transition-colors flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                @if(isset($notificationCount) && $notificationCount > 0)
                                <span class="badge-pulse absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-red-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-3 sm:px-4 lg:px-5 pt-2.5 sm:pt-3.5 pb-6 flex-1 flex flex-col min-w-0 relative">
                <x-page-skeleton />
                <div id="real-page-content" class="w-full flex-1 flex flex-col min-w-0" style="display: none; opacity: 0;">
                    {{ $slot }}
                </div>
            </main>
            </div>
        </div>
    </div>

    <script>
        function mobilePanel() {
            return {
                isOpen: false,
                notifications: [],
                notifCount: @json($notificationCount ?? 0),
                loadingNotifs: false,

                async fetchNotifs() {
                    this.loadingNotifs = true;
                    try {
                        const r = await fetch('/notifications/unread-json');
                        const d = await r.json();
                        this.notifications = Array.isArray(d.notifications) ? d.notifications : [];
                        this.notifCount = d.count ?? 0;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loadingNotifs = false;
                    }
                },

                async markRead(id) {
                    await fetch('/notifications/mark-read/' + id, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content
                        }
                    });
                    this.notifications = this.notifications.filter(n => n.id != id);
                    this.notifCount = Math.max(0, this.notifCount - 1);
                },

                async markAllRead() {
                    await fetch('/notifications/mark-all-read-api', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content
                        }
                    });
                    this.notifications = [];
                    this.notifCount = 0;
                }
            }
        }

        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();

        document.addEventListener('click', function(e) {
            // === CONFIRMATION GÉNÉRIQUE ===
            // Les deux gestionnaires ci-dessous portent des textes figés. Celui-ci
            // laisse chaque écran écrire le sien, sans retomber sur le confirm()
            // natif du navigateur qui jure avec le reste de l'interface.
            const swalForm = e.target.closest('form[data-swal-text]');
            if (swalForm && e.target.closest('button[type="submit"]')) {
                e.preventDefault();
                e.stopPropagation();
                Swal.fire({
                    title: swalForm.dataset.swalTitle || 'Confirmer',
                    text: swalForm.dataset.swalText,
                    icon: swalForm.dataset.swalIcon || 'question',
                    showCancelButton: true,
                    confirmButtonColor: swalForm.dataset.swalColor || '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: swalForm.dataset.swalConfirm || 'Confirmer',
                    cancelButtonText: 'Annuler'
                }).then(r => {
                    if (r.isConfirmed) {
                        swalForm.removeAttribute('data-swal-text');
                        swalForm.submit();
                    }
                });
                return;
            }

            // === HANDLE DELETE CONFIRMATION ===
            const deleteForm = e.target.closest('form[data-confirm-delete]');
            if (deleteForm) {
                const submitBtn = e.target.closest('button[type="submit"]');
                if (submitBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: 'Cette action est irréversible !',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Oui, supprimer !',
                        cancelButtonText: 'Annuler'
                    }).then(r => {
                        if (r.isConfirmed) {
                            deleteForm.submit();
                        }
                    });
                    return;
                }
            }

            // === HANDLE EDIT CONFIRMATION ===
            const editTarget = e.target.closest('[data-confirm-edit]');
            if (editTarget) {
                e.preventDefault();
                e.stopPropagation();
                Swal.fire({
                    title: 'Confirmer la modification',
                    text: 'Voulez-vous vraiment modifier cet élément ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Oui, modifier !',
                    cancelButtonText: 'Annuler'
                }).then(r => {
                    if (!r.isConfirmed) return;
                    if (editTarget.tagName === 'A' && editTarget.href) {
                        window.location.href = editTarget.href;
                        return;
                    }
                    const form = editTarget.closest('form');
                    if (form) {
                        form.submit();
                    }
                });
            }
        });

        // === GESTION DU SKELETON RÉEL MON-STAGE ===
        (function() {
            const skeleton = document.getElementById('page-skeleton-overlay');
            const content = document.getElementById('real-page-content');
            if (!skeleton || !content) return;

            let revealed = false;
            function revealContent() {
                if (revealed) return;
                revealed = true;
                skeleton.style.transition = 'opacity 0.18s ease-out';
                skeleton.style.opacity = '0';
                content.style.display = 'flex';
                requestAnimationFrame(function() {
                    content.style.transition = 'opacity 0.2s ease-in';
                    content.style.opacity = '1';
                    setTimeout(function() {
                        skeleton.style.display = 'none';
                    }, 180);
                });
            }

            // Écouter Alpine ou load
            if (window.Alpine) {
                document.addEventListener('alpine:initialized', function() {
                    setTimeout(revealContent, 80);
                });
            }
            if (document.readyState === 'complete') {
                setTimeout(revealContent, 80);
            } else {
                window.addEventListener('load', function() {
                    setTimeout(revealContent, 80);
                });
            }
            // Sécurité anti-blocage (maximum 300ms)
            setTimeout(revealContent, 300);

            // Gestion du retour arrière navigateur (bfcache)
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    skeleton.style.display = 'none';
                    content.style.display = 'flex';
                    content.style.opacity = '1';
                }
            });

            // Bascule instantanée du skeleton réel lors des clics sur les liens
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a[href]');
                if (!link || link.target === '_blank' || link.hasAttribute('download') || e.ctrlKey || e.metaKey || e.shiftKey) return;
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

                let tplId = null;
                if (href.includes('/mon-stage')) tplId = 'sk-tpl-mon-stage';
                else if (href.includes('/attendance-tracking') || href.includes('/tracking')) tplId = 'sk-tpl-attendance-tracking';
                else if (href.includes('/anomalies')) tplId = 'sk-tpl-anomalies';
                else if (href.includes('/corrections-depart') || href.includes('/corrections')) tplId = 'sk-tpl-corrections-depart';
                else if (href.includes('/pointage-suivi')) tplId = 'sk-tpl-pointage-suivi';
                else if (href.includes('/horaire')) tplId = 'sk-tpl-horaire';
                else if (href.includes('/admin/presence')) tplId = 'sk-tpl-admin-presence';
                else if (href.includes('/holidays')) tplId = 'sk-tpl-holidays';
                else if (href.includes('/presence/pointage') || href.includes('/pointage')) tplId = 'sk-tpl-pointage';
                else if (href.includes('/presence/historique') || href.includes('/historique')) tplId = 'sk-tpl-historique';
                else if (href.includes('/tasks-tracking')) tplId = 'sk-tpl-tasks-tracking';
                else if (href.includes('/tasks')) tplId = 'sk-tpl-tasks';
                else if (href.includes('/permissions')) tplId = 'sk-tpl-permissions';
                else if (href.includes('/admin/reports')) tplId = 'sk-tpl-admin-reports';
                else if (href.includes('/reports')) tplId = 'sk-tpl-reports';
                else if (href.includes('/summaries')) tplId = 'sk-tpl-summaries';
                else if (href.includes('/profile')) tplId = 'sk-tpl-profile';
                else if (href.includes('/notifications')) tplId = 'sk-tpl-notifications';
                else if (href.includes('/admin/employes') || href.includes('/employes')) tplId = 'sk-tpl-employes';
                else if (href.includes('/admin/etudiants') || href.includes('/etudiants')) tplId = 'sk-tpl-etudiants';
                else if (href.includes('/admin/personnels') || href.includes('/personnels')) tplId = 'sk-tpl-personnels';
                else if (href.includes('/admin/badges') || href.includes('/badges')) tplId = 'sk-tpl-badges';
                else if (href.includes('/dashboard')) tplId = 'sk-tpl-dashboard';

                if (tplId) {
                    const tpl = document.getElementById(tplId);
                    const skContent = document.getElementById('page-skeleton-content');
                    if (tpl && skContent) {
                        skContent.innerHTML = tpl.innerHTML;
                        skeleton.style.display = 'block';
                        skeleton.style.opacity = '1';
                        content.style.opacity = '0';
                        content.style.display = 'none';
                    }
                }
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>