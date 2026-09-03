<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée — {{ config('app.name', 'TFG') }}</title>
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
            <div class="tfg-card-header" style="background: linear-gradient(135deg, #92400e 0%, #b45309 100%);">
                <div class="tfg-logo-ring" style="background: linear-gradient(135deg, #b45309, #d97706); box-shadow: 0 8px 24px rgba(217, 119, 6, 0.25);">
                    <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG">
                </div>
                <span class="tfg-card-eyebrow"><span class="tfg-live-dot" style="background: #fcd34d;"></span><span style="color: rgba(255,255,255,0.55);">Session expirée</span></span>
                <h2 class="tfg-card-title">TFG Sarl</h2>
            </div>

            <div class="tfg-card-body" style="text-align: center; padding: 32px 24px 36px;">
                <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: #fef3c7; border-radius: 999px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 32px; height: 32px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 style="margin: 0 0 10px; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                    Votre session a expiré
                </h3>
                <p style="margin: 0 0 28px; color: #64748b; font-size: 0.85rem; line-height: 1.65; max-width: 320px; margin-left: auto; margin-right: auto;">
                    Pour des raisons de sécurité, votre session a été déconnectée automatiquement. Veuillez vous reconnecter pour continuer.
                </p>

                <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
                    <a href="{{ route('login') }}" class="tfg-btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; max-width: 260px;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Se reconnecter
                    </a>

                    <button onclick="history.back()" style="background: none; border: none; color: #0EA5A0; font-size: 0.8rem; font-weight: 500; cursor: pointer; padding: 6px 12px; margin-top: 4px;">
                        ← Retour à la page précédente
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
