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
    .text-header .i { font-size: 14px; color: #ff0303; font-weight: 600; opacity: 0.75; display: block; margin-bottom: 6px; }
   .text-header .i span {
    font-size: 10px;      /* taille plus petite */
    vertical-align: text-bottom; /* aligne le span en bas de la ligne */
    line-height: 1;       /* pour éviter un décalage vertical bizarre */
      position: relative;
    bottom: -2px; /* ajuste selon ton visuel */
}

    .text-header .p1 { font-size: 14px; font-weight: 600; margin: 2px 0; }
    .rcf { margin: 8px 0 15px 0; font-weight: 600; font-size: 16px; }
    .title { font-size: 30px; font-weight: 900; color: #000; text-align: center; margin: 15px 0; }
    .title::after { content: "*_*_*_*_*_*_*"; display: block; margin: 5px auto 0; font-size: 14px; letter-spacing: 2px; }
.content { 
    text-align: justify; 
    line-height: 1.8; 
    font-size: 18px; 
}
    .content p { margin: 10px 0; }
    .content b { color: #000; }
    .signatures { display: flex; justify-content: space-between; margin-top: 120px; flex-wrap: wrap; }
    .sign { width: 48%; }
    .sign.student { text-align: left; margin-top: 80px; }
    .sign.director { text-align: center; }
    .company { position: absolute; bottom: 20px; left: 15mm; right: 15mm; border-top: 4px solid #ac0810d3; text-align: justify; font-size: 12px; padding-top: 5px; font-weight: 600; }
  
    /* Container général des boutons : à droite et verticalement centrés */
.buttons-container {
    position: absolute;
    top: 50%;
    right: 15mm; /* décalage depuis le bord droit */
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 15px;
    z-index: 1000; /* pour rester au-dessus de la page */
}

/* Style des boutons */
.buttons-container a,
.buttons-container button {
    padding: 10px 25px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    border: none;
    color: white;
    transition: transform 0.2s;
    text-align: center;
    width: 200px;
}

.buttons-container a:hover,
.buttons-container button:hover {
    transform: translateY(-2px);
}

.back { background: #6b7280; }       /* gris */
.print { background: #2563eb; }      /* bleu */
.download { background: #636363; }   /* vert */

/* Masquer les boutons à l'impression */
@media print {
    .buttons-container { display: none !important; }
}

   
@media print {
        @page { size: A4; margin: 15mm; }
        .buttons-container { display: none !important; }
        .a4-container { box-shadow: none; padding: 15mm; margin: 0; width: 210mm; height: 297mm; }
        body { padding: 0; margin: 0; }
    }
</style>
</head>
<body>
    @php
use Carbon\Carbon;

// Mettre Carbon en français
Carbon::setLocale('fr');

// Récupère la date actuelle
$now = Carbon::now();
$year = $now->year;

// Calcul de l'année académique
if ($now->month >= 9) {
    $academicYear = $year . '-' . ($year + 1);
} else {
    $academicYear = ($year - 1) . '-' . $year;
}
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
        <p>Je soussigné <b>Appolinaire KONNON</b>, Directeur Général de la société <b>Technology Forever Group SARL</b>,
        atteste que <b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b>,
        étudiant(e) à <b>{{ $stage->etudiant->ecole }}</b>,
        a effectué un stage <b>{{ $stage->typestage->libelle ?? '—' }}</b> dans notre entreprise,
        académique de trois (03) mois dans notre entreprise au sein de
la Direction <b>{{ $stage->service->nom ?? '—' }}</b>,
        du <b>{{ $stage->date_debut?->isoFormat('D MMMM YYYY') }}</b>


au <b>{{ $stage->date_fin?->isoFormat('D MMMM YYYY') }}</b> pour le compte de l’année académique <b>{{ $academicYear }}</b>

        
        <p>Durant ce stage, il/elle a travaillé sur un logiciel  {{ $stage->theme }}.</p>
        <p>En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.</p>
    </div>

    <div class="signatures">
        <div class="sign student">
            <p>Reçu le ..</p>
            <p style="margin-top:40px;"><b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b></p>
        </div>

        @foreach($signataires as $signataire)
        <div class="sign director">
            @php
                $pivot = $signataire->pivot;
                $parOrdre = $pivot?->par_ordre ?? false; // Sécurisation si pivot null
            @endphp

            <p><b>Fait à Cotonou, le {{ now()->isoFormat('D MMMM YYYY') }}</b></p>

            @if($signataire->isDG())
                <p style="margin-top:15px;"><b>{{ $signataire->poste }}</b></p>
                <p style="margin-top:40px;"><b>{{ $signataire->nom }}</b></p>
            @elseif($parOrdre)
                <p><b>Le Directeur Général et P.O</b></p>
                <p><b>{{ $signataire->poste }}</b></p>
                <p style="margin-top:40px;"><b>{{ $signataire->nom }}</b></p>
            @else
                <p><b>{{ $signataire->poste }}</b></p>
                <p style="margin-top:40px;"><b>{{ $signataire->nom }}</b></p>
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
    <a href="{{ route('stages.index') }}">
        <button type="button" class="back">Retour</button>
    </a>
    <button type="button" class="print" onclick="window.print()">Imprimer</button>
    <a href="{{ route('stages.attestation.download', $stage->id) }}">
        <button type="button" class="download">Télécharger PDF</button>
    </a>
</div>
</body>
</html>
