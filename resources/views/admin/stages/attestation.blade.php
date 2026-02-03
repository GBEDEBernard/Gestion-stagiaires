<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Attestation de stage</title>
    <style>
        body {
            font-family: DejaVu Sans, serif;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0;
            font-size: 18px;
        }

        .a4-container {
            width: 210mm;
            height: 297mm;
            background: #ffffff;
            padding: 15mm;
            position: relative;
            font-family: "Times New Roman", Times, serif;
            box-sizing: border-box;
            border: 1px solid rgba(218, 218, 218, 1);
            overflow: hidden;
        }

        /* 🎨 Logo en filigrane centré et stylé */
        .a4-container::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background-image: url('{{ url('images/TFGLOGO.png') }}');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: contain;
            opacity: 0.40;
            z-index: 0;
            pointer-events: none;
        }

        .a4-container > * {
            position: relative;
            z-index: 1;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 4px solid #b90912da;
            padding-bottom: 8px;
            margin-bottom: 40px;
        }

        .header img {
            width: 70px;
            height: 70px;
            margin-right: 15px;
            border-radius: 12px;
        }

        .text-header {
            flex: 1;
            text-align: center;
        }

        .text-header h1 {
            font-size: 20px;
            color: #3c57a1;
            margin: 2px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .text-header .i {
            font-size: 14px;
            color: #ff0303;
            font-weight: 600;
            opacity: 0.75;
            display: block;
            margin-bottom: 4px;
            margin-top: 4px;
        }

        .text-header .i span {
            font-size: 10px;
            vertical-align: text-bottom;
            line-height: 1;
            position: relative;
            bottom: -1px;
        }

        .text-header .p1 {
            font-size: 14px;
            font-weight: 600;
            margin: 2px 0;
        }

        .rcf {
            margin: 8px 0 35px 0;
            font-weight: 600;
            font-size: 16px;
        }

        .title {
            font-size: 30px;
            font-weight: 900;
            color: #000;
            text-align: center;
            margin: 15px 0 15px 0;
        }

        .title::after {
            content: "*_*_*_*_*_*_*";
            display: block;
            margin: 5px auto 0;
            font-size: 14px;
            letter-spacing: 2px;
        }

        .content {
            text-align: justify;
            line-height: 1.6;
            font-size: 18px;
            margin-top: 50px;
        }

        .signatures {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        .sign {
            margin: 90px;
            width: 60%;
            line-height: 0.5;
        }

        .sign.director {
            text-align: center;
        }

        .company {
            position: absolute;
            bottom: 20px;
            left: 15mm;
            right: 15mm;
            border-top: 4px solid #030303d3;
            text-align: justify;
            font-size: 12px;
            padding-top: 5px;
            font-weight: 600;
        }

        .buttons-container {
            position: fixed;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 1000;
        }

        .buttons-container a,
        .buttons-container button {
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            color: white;
            transition: all 0.3s ease;
            text-align: center;
            width: 200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: block;
        }

        .buttons-container a:hover,
        .buttons-container button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .print {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        /* ✅ Impression parfaite avec filigrane */
        @media print {
            body {
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .buttons-container {
                display: none !important;
            }

            .a4-container {
                margin: 0 auto;
                box-shadow: none !important;
                width: 210mm;
                height: 297mm;
                padding: 15mm;
                page-break-after: avoid;
                border: none;
            }

            /* Filigrane visible à l'impression */
            .a4-container::before {
                opacity: 0.25 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            * {
                overflow: visible !important;
            }
        }

        @page {
            margin: 0;
            size: A4;
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

    // Helper function to convert numbers to French text
    $numberToFrench = function($num) {
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
        return (string)$num;
    };

    if($diffDays < 30){
        if($diffDays < 7){
            $duréeTexte = ucfirst($numberToFrench($diffDays)) . " ($diffDays) jour" . ($diffDays > 1 ? 's' : '');
        } else {
            $semaines = round($diffDays / 7);
            $duréeTexte = ucfirst($numberToFrench($semaines)) . " ($semaines) semaine" . ($semaines > 1 ? 's' : '');
        }
    } else {
        $mois = floor($diffDays / 30);
        $joursRestants = $diffDays % 30;
        $moisLettre = ucfirst($numberToFrench($mois));
        $duréeTexte = "$moisLettre ($mois) mois";
    }

    $year = $now->year;
    $academicYear = ($now->month >= 9) ? "$year-" . ($year + 1) : ($year - 1) . "-$year";

    $genre = strtolower($stage->etudiant->genre ?? 'masculin');
    $textePro = " a effectué des travaux : " . ucfirst($stage->theme) . ".";
    $texteAcad = " a travaillé sur : " . ucfirst($stage->theme) . ".";
    @endphp

    @php
    $serviceNom = $stage->service->nom ?? '—';

    // Vérifier si le service commence par une voyelle
    $voyelles = ['a','e','i','o','u','y','A','E','I','O','U','Y','H','h'];
    $firstChar = mb_substr($serviceNom, 0, 1);

    if($serviceNom === '—') {
        $prepositionService = 'de';
    } elseif(in_array($firstChar, $voyelles)) {
        $prepositionService = "d'";
    } else {
        $prepositionService = "de";
    }
    @endphp

    <div class="a4-container">

        <div class="header">
            <img src="{{ url('images/TFGLOGO.png') }}" alt="Logo">
            <div class="text-header">
                <h1>TECHNOLOGY FOREVER GROUP SARL</h1>
                <p class="i"><span>***</span> La Technologie au service du développement <span>***</span></p>
                <p class="p1">
                    Informatique – Télécommunications – BTP – Énergie – Électricité – Formations – Commerce Général – Fournitures – Import-Export & Divers
                </p>
            </div>
        </div>

        <div class="rcf">Réf : {{ $reference }}</div>
        <h1 class="title">ATTESTATION DE STAGE</h1>

        <div class="content">
            @if($stage->typestage->code === '003')
            <p>Je soussigné <b>Appolinaire KONNON</b>, Directeur Général de <b>Technology Forever Group SARL</b>, atteste que Mme/Mr <b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b> a effectué un <b>stage professionnel</b> de {{ $duréeTexte }} au sein du service {{ $prepositionService }} <b>{{ $stage->service->nom ?? '—' }}</b>, du <b>{{ $dateDebut->isoFormat('D MMMM YYYY') }}</b> au <b>{{ $dateFin->isoFormat('D MMMM YYYY') }}</b>.</p>
            <p>Durant cette période, il/elle {{ $textePro }}</p>
            @else
            <p>Je soussigné <b>Appolinaire KONNON</b>, Directeur Général de <b>Technology Forever Group SARL</b>, atteste que Mme/Mr <b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b> a effectué un <b>stage académique</b> de {{ $duréeTexte }} au sein du service {{ $prepositionService }} <b>{{ $stage->service->nom ?? '—' }}</b>, du <b>{{ $dateDebut->isoFormat('D MMMM YYYY') }}</b> au <b>{{ $dateFin->isoFormat('D MMMM YYYY') }}</b>, pour l'année académique <b>{{ $academicYear }}</b>.</p>
            <p>Durant cette période, il/elle {{ $texteAcad }}</p>
            @endif

            <p>En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.</p>
        </div>

        <div class="signatures">
            @foreach($signataires as $signataire)
            <div class="sign director">
                <p><b>Fait à Cotonou, le {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</b></p>
                @php
                $parOrdre = $signataire->pivot->par_ordre ?? false;
                @endphp
                @if($parOrdre)
                <p style="margin-top:8px;"><b>Le Directeur Général et P.O</b></p>
                @endif
                <p style="margin-top:{{ $parOrdre ? '8px' : '8px' }};"><b>{{ $signataire->poste }}</b></p>
                <p style="margin-top:90px;"><u><b>{{ $signataire->nom }}</b></u></p>
            </div>
            @endforeach
        </div>

        <div class="company">
            <p>TFG SARL : Capital de 1.000.000 FCFA - RCCM : RB/ABT/18 B 2111 - N°IFU : 3201810222368
                Siège : M/ GAUTHE Gabriel - Allègléta | Godomey-Togoudo (Abomey-Calavi)
                Site Web : www.tfgbusiness.com
                Tél : (+229) 01 65 10 39 59 / 01 69 58 06 03 - 09 BP 791 (St-Michel | Cotonou)</p>
        </div>
    </div>

    <div class="buttons-container">
        <a href="{{ route('stages.show', $stage->id) }}" class="back">Retour</a>
        <button type="button" class="print" onclick="window.print()">Imprimer</button>
    </div>

</body>

</html>