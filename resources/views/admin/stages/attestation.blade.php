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
        padding: 20px; 
        font-size: 18px;
    }
    .a4-container { 
        width: 210mm; 
        height: 297mm; 
        background: #ffffff; 
        padding: 15mm; 
        position: relative; 
        font-family: "Times New Roman", Times, serif; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        box-sizing: border-box;
    }
    .header { display: flex; align-items: center; border-bottom: 4px solid #b90912da; padding-bottom: 8px; margin-bottom: 15px; }
    .header img { width: 70px; height: 70px; margin-right: 15px; border-radius: 12px; }
    .text-header { flex: 1; text-align: center; }
    .text-header h1 { font-size: 20px; color: #3c57a1; margin: 6px 0; font-family: Arial, Helvetica, sans-serif; }
    .text-header .i { font-size: 14px; color: #ff0303; font-weight: 600; opacity: 0.75; display: block; margin-bottom: 6px; margin-top: 0; }
    .text-header .i span { font-size: 10px; vertical-align: text-bottom; line-height: 1; position: relative; bottom: -2px; }
    .text-header .p1 { font-size: 14px; font-weight: 600; margin: 2px 0; }
    .rcf { margin: 8px 0 15px 0; font-weight: 600; font-size: 16px; }
    .title { font-size: 30px; font-weight: 900; color: #000; text-align: center; margin: 15px 0; }
    .title::after { content: "*_*_*_*_*_*_*"; display: block; margin: 5px auto 0; font-size: 14px; letter-spacing: 2px; }
    .content { text-align: justify; line-height: 1.8; font-size: 18px; }
    .content p { margin: 10px 0; }
    .content b { color: #000; }
    .signatures { display: flex; justify-content: center; margin-top: 120px; flex-wrap: wrap; }
    .sign { width: 48%; }
    .sign.student { text-align: left; margin-top: 80px; }
    .sign.director { text-align: center; }
    .company { position: absolute; bottom: 20px; left: 15mm; right: 15mm; border-top: 4px solid #030303d3; text-align: justify; font-size: 12px; padding-top: 5px; font-weight: 600; }
    .buttons-container { position: absolute; top: 50%; right: 15mm; transform: translateY(-50%); display: flex; flex-direction: column; gap: 15px; z-index: 1000; }
    .buttons-container a, .buttons-container button { padding: 10px 25px; font-size: 16px; font-weight: 600; border-radius: 6px; cursor: pointer; border: none; color: white; transition: transform 0.2s; text-align: center; width: 200px; }
    .buttons-container a:hover, .buttons-container button:hover { transform: translateY(-2px); }
    .back { background: #6b7280; }
    .print { background: #2563eb; }
    @media print { .buttons-container { display: none !important; } }
    @media print { @page { size: A4; margin: 15mm; } .a4-container { box-shadow: none; padding: 15mm; margin: 0; width: 210mm; height: 297mm; } body { padding: 0; margin: 0; } }
</style>
</head>
<body>

@php
use Carbon\Carbon;

// Dates en français
$dateDebut = Carbon::parse($stage->date_debut)->locale('fr');
$dateFin = Carbon::parse($stage->date_fin)->locale('fr');
$now = Carbon::now()->locale('fr');

// Calcul de la durée
$diffDays = $dateDebut->diffInDays($dateFin) + 1;
$duréeTexte = '';
$formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);

if($diffDays < 30){
    if($diffDays < 7){
        $joursLettre = $formatter->format($diffDays);
        $duréeTexte = "$joursLettre (" . str_pad($diffDays,2,'0',STR_PAD_LEFT) . ") jour" . ($diffDays > 1 ? 's' : '');
    } else {
        $semaines = round($diffDays / 7);
        $semainesLettre = $formatter->format($semaines);
        $duréeTexte = "$semainesLettre (" . str_pad($semaines,2,'0',STR_PAD_LEFT) . ") semaine" . ($semaines > 1 ? 's' : '');
    }
} else {
    $mois = floor($diffDays / 30);
    $joursRestants = $diffDays % 30;

    if($joursRestants >= 15){
        $mois += 1;
        $joursRestants = 0;
    } elseif($joursRestants > 0 && $joursRestants <= 4){
        $joursRestants = 0;
    }

    $moisLettre = $formatter->format($mois);
    $duréeTexte = "$moisLettre (" . str_pad($mois,2,'0',STR_PAD_LEFT) . ") mois";

    if($joursRestants > 4 && $joursRestants < 15){
        $joursLettre = $formatter->format($joursRestants);
        $duréeTexte .= " et $joursLettre (" . str_pad($joursRestants,2,'0',STR_PAD_LEFT) . ") jour" . ($joursRestants > 1 ? 's' : '');
    }
}

// Année académique
$year = $now->year;
$academicYear = ($now->month >= 9) ? "$year-" . ($year + 1) : ($year - 1) . "-$year";

// Genre et pronoms
$genre = strtolower($stage->etudiant->genre ?? 'masculin');
$pronomSujet = $genre === 'féminin' ? 'Elle' : 'Il';
$pronomObjet = $genre === 'féminin' ? 'la' : 'le';
$pronomPossessif = $genre === 'féminin' ? 'sa' : 'son';

// Thème stage
$themePropre = ucfirst(mb_strtolower(trim($stage->theme), 'UTF-8'));
$textePro = " a effectué des travaux : $themePropre.";
$texteAcad = "$pronomSujet a travaillé sur : $themePropre";

@endphp

<div class="a4-container">
    <div class="header">
      <img src="{{ url('images/TFGLOGO.png') }}" alt="Logo">
        <div class="text-header">
            <h1>TECHNOLOGY FOREVER GROUP SARL</h1>
            <p class="i"><span>***</span> La Technologie au service du développement <span>***</span></p>
            <p class="p1">Informatique – Télécommunications – BTP – Energétique – Electricité – Formations Commerce Général – Fourniture de matériels – Import - Export & Divers</p>
        </div>
    </div>

    <div class="rcf">Réf : {{ $reference }}</div>
    <h1 class="title">ATTESTATION DE STAGE</h1>

    <div class="content">
        @if($stage->typestage->code === '003')
            <p>Je soussigné <b>Appolinaire KONNON</b>, Directeur Général de la société <b>Technology Forever Group SARL (TFG SARL)</b>, atteste que Mme/Mr <b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b>, a effectué un
         <b>stage professionnel</b> de {{ $duréeTexte }} dans notre entreprise au sein de la direction de : <b>{{ $stage->service->nom ?? '—' }}</b>, durant la période du <b>{{ $dateDebut->isoFormat('D MMMM YYYY') }}</b> au <b>{{ $dateFin->isoFormat('D MMMM YYYY') }}</b>.</p>

            <p>Durant cette période, il/elle {{ $textePro }}</p>
        @elseif($stage->typestage->code === '004')
            <p>Je soussigné <b>Appolinaire KONNON</b>, Directeur Général de la société <b>Technology Forever Group SARL</b>, atteste que Mme/Mr <b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b>, a effectué un
         <b>stage académique</b> de {{ $duréeTexte }} dans notre entreprise  au sein de la direction de : <b>{{ $stage->service->nom ?? '—' }}</b>, durant la période du <b>{{ $dateDebut->isoFormat('D MMMM YYYY') }}</b> au <b>{{ $dateFin->isoFormat('D MMMM YYYY') }}</b> pour le compte de l’année académique <b>{{ $academicYear }}</b>.</p>

            <p>Durant cette période, {{ $texteAcad }}</p>
        @endif

        <p>En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.</p>
    </div>

    <div class="signatures">
        @foreach($signataires as $signataire)
        <div class="sign director">
            @php
                $pivot = $signataire->pivot;
                $parOrdre = $pivot?->par_ordre ?? false;
            @endphp

            <p><b>Fait à Cotonou, le {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}</b></p>

            @if($signataire->isDG())
                <p style="margin-top:15px;"><b>{{ $signataire->poste }}</b></p>
                <p style="margin-top:50px; "> <u><b>{{ $signataire->nom }}</b></u>  </p>
            @elseif($parOrdre)
                <p><b>Le Directeur Général et P.O</b></p>
                <p><b>{{ $signataire->poste }}</b></p>
                <p style="margin-top:50px;"><u><b>{{ $signataire->nom }}</b></u></p>
            @else
                <p><b>{{ $signataire->poste }}</b></p>
                <p style="margin-top:50px; "><u><b>{{ $signataire->nom }}</b></u></p>
            @endif
        </div>
        @endforeach
    </div>

    <div class="company">
        <p>TFG SARL : Capital de 1.000.000 FCFA - RCCM : RB/ABT/18 B 2111 - N°IFU : 3201810222368
        Siège : M/ GAUTHE Gabriel - Allègléta | Godomey-Togoudo (Abomey-Calavi) - Site Web : www.tfgbusiness.com
        Tél : (+229) 0165103959 / 0169580603 - 09 BP 791 (St-Michel | Cotonou)</p>
    </div>
</div>

<div class="buttons-container">
    <a href="{{ route('stages.show', $stage->id) }}">
        <button type="button" class="back">Retour</button>
    </a>
    <button type="button" class="print" onclick="window.print()">Imprimer</button>
</div>

</body>
</html>
