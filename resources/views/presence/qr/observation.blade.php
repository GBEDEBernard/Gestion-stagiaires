<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f5f4ee">
    <title>Motif du retard — {{ $site->name }}</title>
    @include('presence.qr.partials.theme')
    <style>
        textarea {
            width: 100%;
            min-height: 96px;
            padding: 11px 12px;
            font: inherit;
            font-size: 15px;
            line-height: 1.5;
            color: var(--text);
            background: var(--surface);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-sm);
            resize: vertical;
        }
        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(193, 95, 60, .12);
        }
        .field-label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 500;
        }
        .hint {
            margin: 7px 0 0;
            font-size: 13px;
            color: var(--text-tertiary);
        }
        .hint--error { color: var(--danger); }
    </style>
</head>
<body>

    <main class="card">

        <p class="eyebrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>{{ $site->name }}</span>
        </p>

        <div class="badge badge--accent">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3 2"/>
            </svg>
        </div>

        <h1 class="title">Vous arrivez après {{ $expected }}</h1>
        <p class="subtitle">
            {{ $user->prenom ?? $user->name }}, il est {{ now()->format('H:i') }}.
            Expliquez brièvement ce qui s'est passé : votre pointage sera enregistré aussitôt.
        </p>

        <form method="POST" action="{{ route('presence.qr.process', ['site_token' => $site->qr_token]) }}">
            @csrf

            {{-- Position et appareil déjà capturés : on les reporte pour ne pas
                 redemander la géolocalisation après la saisie du motif. --}}
            @foreach($payload as $key => $value)
                @if($value !== null && $key !== 'observation_message')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <label class="field-label" for="observation">Motif du retard</label>
            <textarea id="observation" name="observation_message" required minlength="10" maxlength="500"
                placeholder="Ex. : embouteillage sur la voie de Godomey, parti à 07h15."
                autofocus></textarea>

            @if($tooShort)
                <p class="hint hint--error">
                    Merci de détailler un peu : au moins dix caractères, pour que votre responsable
                    puisse apprécier le motif.
                </p>
            @else
                <p class="hint">Dix caractères minimum. Ce motif sera lu par votre responsable.</p>
            @endif

            <div style="margin-top: 18px;">
                <button type="submit" class="btn btn--primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12.5l4.5 4.5L19 7"/>
                    </svg>
                    <span>Enregistrer mon arrivée</span>
                </button>
            </div>
        </form>

        <p class="footnote">
            Le retard est constaté par rapport à l'heure d'arrivée prévue pour votre poste.
            Votre motif est enregistré avec le pointage, il n'efface pas le retard mais l'explique.
        </p>

    </main>

</body>
</html>
