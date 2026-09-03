<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur serveur — {{ config('app.name', 'TFG') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @if(file_exists(public_path('android-chrome-512x512.png')))
    <link rel="icon" href="{{ asset('android-chrome-512x512.png') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/auth-tfg.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="tfg-page">
        <div class="tfg-bg" aria-hidden="true"></div>

        <div class="tfg-card" style="max-width: 440px;">
            <div class="tfg-card-header" style="background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 100%);">
                <div class="tfg-logo-ring" style="background: linear-gradient(135deg, #6d28d9, #8b5cf6); box-shadow: 0 8px 24px rgba(139, 92, 246, 0.25);">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG">
                </div>
                <span class="tfg-card-eyebrow"><span class="tfg-live-dot" style="background: #c4b5fd;"></span><span style="color: rgba(255,255,255,0.55);">Erreur serveur</span></span>
                <h2 class="tfg-card-title">TFG Sarl</h2>
            </div>

            <div class="tfg-card-body" style="text-align: center; padding: 32px 24px 36px;">
                <div style="font-size: 4rem; font-weight: 800; color: #c4b5fd; line-height: 1; margin-bottom: 8px; font-family: 'Space Grotesk', sans-serif;">
                    500
                </div>
                <h3 style="margin: 0 0 10px; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                    Erreur interne du serveur
                </h3>
                <p style="margin: 0 0 28px; color: #64748b; font-size: 0.85rem; line-height: 1.65; max-width: 320px; margin-left: auto; margin-right: auto;">
                    Une erreur inattendue s'est produite. Notre équipe technique a été notifiée. Veuillez réessayer dans quelques instants.
                </p>

                <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
                    <a href="{{ url('/') }}" class="tfg-btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; max-width: 260px;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Réessayer
                    </a>

                    @auth
                    <a href="{{ url('/dashboard') }}" style="background: none; border: none; color: #0EA5A0; font-size: 0.8rem; font-weight: 500; cursor: pointer; padding: 6px 12px; text-decoration: none;">
                        Retour au tableau de bord
                    </a>
                    @endauth

                    @guest
                    <a href="{{ route('login') }}" style="background: none; border: none; color: #0EA5A0; font-size: 0.8rem; font-weight: 500; cursor: pointer; padding: 6px 12px; text-decoration: none;">
                        Retour à la connexion
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</body>
</html>
