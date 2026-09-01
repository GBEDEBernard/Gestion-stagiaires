<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Affiche de pointage — {{ $site->name }}</title>
    <style>
        :root {
            --ink:        #111111;
            --ink-soft:   #4a4a46;
            --ink-faint:  #86847d;
            --rule:       #dcdad2;
            --paper:      #ffffff;
            --desk:       #f2f1eb;
            --brand-blue: #3c57a1;
            --brand-red:  #b90912;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 32px 16px;
            background: var(--desk);
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: "Times New Roman", Times, serif;
            color: var(--ink);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        /* ---------- Barre d'actions (écran uniquement) ---------- */

        .toolbar {
            width: 210mm;
            max-width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            font-family: ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
        }

        .toolbar a,
        .toolbar button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            font: inherit;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid var(--rule);
            background: var(--paper);
            color: var(--ink);
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(17, 17, 17, .04);
            transition: background-color 120ms ease, box-shadow 120ms ease;
        }
        .toolbar a:hover,
        .toolbar button:hover {
            background: #fbfaf7;
            box-shadow: 0 1px 3px rgba(17, 17, 17, .07);
        }
        .toolbar button {
            background: var(--ink);
            border-color: var(--ink);
            color: #fff;
        }
        .toolbar button:hover { background: #2e2e2b; }
        .toolbar svg { width: 15px; height: 15px; stroke-width: 1.75; }

        /* ---------- Feuille A4 ---------- */

        .sheet {
            width: 210mm;
            min-height: 297mm;
            max-width: 100%;
            background: var(--paper);
            padding: 15mm;
            position: relative;
            border: 1px solid var(--rule);
            box-shadow: 0 1px 3px rgba(17, 17, 17, .05), 0 18px 48px -24px rgba(17, 17, 17, .22);
            display: flex;
            flex-direction: column;
        }

        /* Filigrane, comme sur l'attestation */
        .sheet::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 460px;
            height: 460px;
            background-image: url('{{ asset("images/TFGLOGO.png") }}');
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            opacity: .05;
            z-index: 0;
            pointer-events: none;
        }
        .sheet > * { position: relative; z-index: 1; }

        /* ---------- En-tête TFG ---------- */

        .letterhead {
            display: flex;
            align-items: center;
            border-bottom: 4px solid var(--brand-red);
            padding-bottom: 8px;
        }

        .letterhead img {
            width: 70px;
            height: 70px;
            margin-right: 15px;
            flex: none;
        }

        .letterhead-text {
            flex: 1;
            text-align: center;
        }

        .letterhead-text h1 {
            margin: 2px;
            font-size: 20px;
            color: var(--brand-blue);
            font-family: Arial, Helvetica, sans-serif;
        }

        .letterhead-text .tagline {
            display: block;
            margin: 4px 0;
            font-size: 14px;
            font-weight: 600;
            color: #ff0303;
            opacity: .75;
        }
        .letterhead-text .tagline span {
            font-size: 10px;
            vertical-align: text-bottom;
        }

        .letterhead-text .activities {
            margin: 2px 0;
            font-size: 14px;
            font-weight: 600;
        }

        /* ---------- Titre du document ---------- */

        .doc-title {
            margin: 34px 0 4px;
            font-size: 30px;
            font-weight: 900;
            text-align: center;
            letter-spacing: .01em;
        }

        .doc-site {
            margin: 0;
            text-align: center;
            font-size: 17px;
            color: var(--ink-soft);
        }
        .doc-site strong { color: var(--ink); }

        /* ---------- QR ---------- */

        .qr-block {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 0;
        }

        .qr-frame {
            padding: 14px;
            border: 1px solid var(--rule);
            background: var(--paper);
            box-shadow: 0 1px 2px rgba(17, 17, 17, .04), 0 10px 28px -18px rgba(17, 17, 17, .25);
            line-height: 0;
        }
        .qr-frame svg,
        .qr-frame img { display: block; width: 330px; height: 330px; }

        .qr-caption {
            margin: 14px 0 0;
            font-family: ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace;
            font-size: 10px;
            color: var(--ink-faint);
            letter-spacing: .02em;
            word-break: break-all;
            text-align: center;
            max-width: 120mm;
        }

        /* ---------- Instructions ---------- */

        .steps {
            border-top: 1px solid var(--rule);
            border-bottom: 1px solid var(--rule);
            padding: 18px 0;
            display: flex;
            gap: 28px;
        }

        .step { flex: 1; }

        .step-num {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin: 0 0 5px;
        }

        .step h3 {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 700;
        }

        .step p {
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
            color: var(--ink-soft);
        }

        .note {
            margin: 16px 0 0;
            font-size: 13px;
            line-height: 1.6;
            color: var(--ink-soft);
            text-align: center;
        }

        /* ---------- Pied de page société ---------- */

        .company {
            margin-top: 22px;
            border-top: 4px solid #030303d3;
            padding-top: 3px;
            font-size: 10px;
            font-weight: 700;
            font-style: italic;
            text-align: justify;
        }
        .company p { margin: 0; }

        .generated {
            margin: 6px 0 0;
            font-size: 9px;
            font-style: normal;
            font-weight: 400;
            color: var(--ink-faint);
            text-align: right;
        }

        /* ---------- Impression ---------- */

        @media print {
            body { padding: 0; background: var(--paper); }
            .no-print { display: none !important; }
            .sheet {
                width: 100%;
                min-height: 100vh;
                border: none;
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar no-print">
        <a href="{{ route('sites.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
            </svg>
            <span>Retour aux sites</span>
        </a>
        <button type="button" onclick="window.print()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V3h12v6"/>
                <path d="M6 18H4a2 2 0 01-2-2v-4a2 2 0 012-2h16a2 2 0 012 2v4a2 2 0 01-2 2h-2"/>
                <path d="M6 14h12v7H6z"/>
            </svg>
            <span>Imprimer</span>
        </button>
    </div>

    <div class="sheet">

        <div class="letterhead">
            <img src="{{ asset('images/TFGLOGO3.png') }}" alt="Logo TFG">
            <div class="letterhead-text">
                <h1>TECHNOLOGY FOREVER GROUP SARL</h1>
                <p class="tagline"><span>***</span> La Technologie au service du développement <span>***</span></p>
                <p class="activities">
                    Informatique – Télécommunications – BTP – Énergie – Électricité – Formations – Commerce Général – Fournitures – Import-Export &amp; Divers
                </p>
            </div>
        </div>

        <h2 class="doc-title">POINTAGE DES PRÉSENCES</h2>
        <p class="doc-site">
            Site : <strong>{{ $site->name }}</strong>@if($site->code) — {{ $site->code }}@endif
        </p>

        <div class="qr-block">
            <div class="qr-frame">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(330)->margin(0)->generate($pointageUrl) !!}
            </div>
            <p class="qr-caption">{{ $pointageUrl }}</p>
        </div>

        <div class="steps">
            <div class="step">
                <p class="step-num">Étape 1</p>
                <h3>Scannez</h3>
                <p>Ouvrez l'appareil photo de votre téléphone et visez le code ci-dessus.</p>
            </div>
            <div class="step">
                <p class="step-num">Étape 2</p>
                <h3>Autorisez</h3>
                <p>Acceptez l'accès à votre position : elle confirme que vous êtes sur le site.</p>
            </div>
            <div class="step">
                <p class="step-num">Étape 3</p>
                <h3>C'est fait</h3>
                <p>Votre arrivée ou votre départ est enregistré, l'écran vous le confirme.</p>
            </div>
        </div>

        <p class="note">
            Le pointage n'est possible qu'à proximité immédiate de l'entrée.
            En cas de difficulté, adressez-vous à votre responsable de site.
        </p>

        <div class="company">
            <p>TFG SARL : Capital de 1.000.000 FCFA - RCCM : RB/ABT/18 B 2111 - N°IFU : 3201810222368
                Siège : M/ GAUTHE Gabriel - Allègléta | Godomey-Togoudo (Abomey-Calavi)
                Site Web : www.tfgbusiness.com
                Tél : (+229) 01 65 10 39 59 / 01 69 58 06 03 - 09 BP 791 (St-Michel | Cotonou)</p>
            <p class="generated">Affiche générée le {{ now()->format('d/m/Y') }}</p>
        </div>

    </div>

</body>

</html>
