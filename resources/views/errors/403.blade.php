<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accès refusé — {{ config('app.name', 'TFG') }}</title>
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
            <div class="tfg-card-header" style="background: linear-gradient(135deg, #7c2d12 0%, #991b1b 100%);">
                <div class="tfg-logo-ring" style="background: linear-gradient(135deg, #991b1b, #dc2626); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.25);">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG">
                </div>
                <span class="tfg-card-eyebrow"><span class="tfg-live-dot" style="background: #fca5a5;"></span><span style="color: rgba(255,255,255,0.55);">Accès refusé</span></span>
                <h2 class="tfg-card-title">TFG Sarl</h2>
            </div>

            <div class="tfg-card-body" style="text-align: center; padding: 32px 24px 36px;">
                <div style="font-size: 4rem; font-weight: 800; color: #fca5a5; line-height: 1; margin-bottom: 8px; font-family: 'Space Grotesk', sans-serif;">
                    403
                </div>
                <h3 style="margin: 0 0 10px; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                    Accès non autorisé
                </h3>
                <p style="margin: 0 0 28px; color: #64748b; font-size: 0.85rem; line-height: 1.65; max-width: 320px; margin-left: auto; margin-right: auto;">
                    Vous n'avez pas les permissions nécessaires pour accéder à cette page. Si vous pensez qu'il s'agit d'une erreur, veuillez contacter votre administrateur.
                </p>

                <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="tfg-btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; max-width: 260px;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Retour au tableau de bord
                    </a>
                    @endauth

                    @guest
                    <a href="{{ route('login') }}" class="tfg-btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; max-width: 260px;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Retour à la connexion
                    </a>
                    @endguest

                    <button onclick="history.back()" style="background: none; border: none; color: #0EA5A0; font-size: 0.8rem; font-weight: 500; cursor: pointer; padding: 6px 12px; margin-top: 4px;">
                        ← Retour à la page précédente
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
