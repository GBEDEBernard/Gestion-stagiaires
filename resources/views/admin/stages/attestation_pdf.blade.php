<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de stage</title>
    <style>
        @page { margin: 0; size: A4; }
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, serif;
            color: #111827;
        }
        .page {
            width: 210mm;
            height: 297mm;
            box-sizing: border-box;
            padding: 18mm 15mm 15mm;
            position: relative;
        }
        .page::before {
            content: "";
            position: absolute;
            inset: 0;
            background: center / 420px no-repeat url("data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/TFGLOGO.png'))) }}");
            opacity: 0.08;
        }
        .page > * {
            position: relative;
            z-index: 1;
        }
        .header {
            display: flex;
            gap: 16px;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 4px solid #b91c1c;
        }
        .header img {
            width: 70px;
            height: 70px;
            border-radius: 12px;
        }
        .header-text {
            flex: 1;
            text-align: center;
        }
        .header-text h1 {
            margin: 0;
            font-size: 20px;
            color: #1d4ed8;
        }
        .header-text p {
            margin: 4px 0 0;
            font-size: 13px;
        }
        .reference {
            margin-top: 28px;
            font-size: 15px;
            font-weight: 600;
        }
        .title {
            margin: 30px 0 10px;
            text-align: center;
            font-size: 28px;
            font-weight: 800;
        }
        .title::after {
            content: "*_*_*_*_*_*_*";
            display: block;
            margin-top: 6px;
            font-size: 12px;
            letter-spacing: 2px;
        }
        .content {
            margin-top: 38px;
            font-size: 17px;
            line-height: 1.7;
            text-align: justify;
        }
        .signatures {
            margin-top: 70px;
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
        }
        .signature {
            width: 70%;
            text-align: center;
        }
        .footer {
            position: absolute;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            padding-top: 8px;
            border-top: 3px solid #111827;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;

        $dateDebut = Carbon::parse($stage->date_debut)->locale('fr');
        $dateFin = Carbon::parse($stage->date_fin)->locale('fr');
        $now = Carbon::now()->locale('fr');
        $diffDays = $dateDebut->diffInDays($dateFin) + 1;

        $numberToFrench = function ($num) {
            $ones = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
            $teens = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
            $tens = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];

            if ($num < 10) return $ones[$num];
            if ($num < 20) return $teens[$num - 10];
            if ($num < 100) {
                $ten = floor($num / 10);
                $one = $num % 10;
                return $tens[$ten] . ($one > 0 ? '-' . $ones[$one] : '');
            }

            return (string) $num;
        };

        if ($diffDays < 30) {
            if ($diffDays < 7) {
                $dureeTexte = ucfirst($numberToFrench($diffDays)) . " ($diffDays) jour" . ($diffDays > 1 ? 's' : '');
            } else {
                $semaines = round($diffDays / 7);
                $dureeTexte = ucfirst($numberToFrench($semaines)) . " ($semaines) semaine" . ($semaines > 1 ? 's' : '');
            }
        } else {
            $mois = floor($diffDays / 30);
            $dureeTexte = $numberToFrench($mois) . " ($mois) mois";
        }

        $year = $now->year;
        $academicYear = ($now->month >= 9) ? "$year-" . ($year + 1) : ($year - 1) . "-$year";
        $genre = strtolower($stage->etudiant->genre ?? 'masculin');
        $civilite = $genre === 'feminin' ? 'Madame' : 'Monsieur';
        $pronom = $genre === 'feminin' ? 'elle' : 'il';
        $serviceNom = $stage->service->nom ?? 'ce service';
        $firstChar = mb_substr($serviceNom, 0, 1);
        $prepositionService = in_array($firstChar, ['a', 'e', 'i', 'o', 'u', 'y', 'h', 'A', 'E', 'I', 'O', 'U', 'Y', 'H'], true) ? "d'" : 'de ';
    @endphp

    <div class="page">
        <div class="header">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/TFGLOGO.png'))) }}" alt="Logo TFG">
            <div class="header-text">
                <h1>TECHNOLOGY FOREVER GROUP SARL</h1>
                <p>La Technologie au service du developpement</p>
                <p>Informatique · Telecommunications · BTP · Energie · Formations · Commerce General</p>
            </div>
        </div>

        <div class="reference">Ref : {{ $reference }}</div>
        <div class="title">ATTESTATION DE STAGE</div>

        <div class="content">
            @if($stage->typestage?->code === '003')
                <p>
                    Je soussigne <strong>Appolinaire KONNON</strong>, Directeur General de la societe
                    <strong>Technology Forever Group (TFG) SARL</strong>, atteste que {{ $civilite }}
                    <strong>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</strong> a effectue un
                    <strong>stage professionnel</strong> de {{ $dureeTexte }} au sein {{ $prepositionService }}<strong>{{ $serviceNom }}</strong>,
                    du <strong>{{ $dateDebut->isoFormat('D MMMM YYYY') }}</strong> au
                    <strong>{{ $dateFin->isoFormat('D MMMM YYYY') }}</strong>.
                </p>
            @else
                <p>
                    Je soussigne <strong>Appolinaire KONNON</strong>, Directeur General de la societe
                    <strong>Technology Forever Group (TFG) SARL</strong>, atteste que {{ $civilite }}
                    <strong>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</strong> a effectue un
                    <strong>stage academique</strong> de {{ $dureeTexte }} au sein {{ $prepositionService }}<strong>{{ $serviceNom }}</strong>,
                    du <strong>{{ $dateDebut->isoFormat('D MMMM YYYY') }}</strong> au
                    <strong>{{ $dateFin->isoFormat('D MMMM YYYY') }}</strong>, pour l'annee academique
                    <strong>{{ $academicYear }}</strong>.
                </p>
            @endif

            @if($stage->theme)
                <p>Durant cette periode, {{ $pronom }} a notamment travaille sur le theme suivant : <strong>{{ $stage->theme }}</strong>.</p>
            @endif

            <p>En foi de quoi, la presente attestation lui est delivree pour servir et valoir ce que de droit.</p>
        </div>

        <div class="signatures">
            @foreach($signataires as $signataire)
                <div class="signature">
                    <p><strong>Fait a Cotonou, le {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</strong></p>
                    @if($signataire->pivot->par_ordre ?? false)
                        <p><strong>Le Directeur General et P.O</strong></p>
                    @endif
                    <p><strong>{{ $signataire->poste }}</strong></p>
                    <p style="margin-top: 72px;"><u><strong>{{ $signataire->nom }}</strong></u></p>
                </div>
            @endforeach
        </div>

        <div class="footer">
            TFG SARL · Capital de 1.000.000 FCFA · RCCM : RB/ABT/18 B 2111 · IFU : 3201810222368
            · Togoudo, Abomey-Calavi · www.tfgbusiness.com
        </div>
    </div>
</body>
</html>
