<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de stage</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            background: #ffffff; 
            display: flex; 
            flex-direction: row;
            justify-content: center;
            align-items: flex-start;
            padding: 20px; 
            font-size: 18px;
            gap: 20px;
        }

        @media (max-width: 1200px) {
            body {
                flex-direction: column;
                align-items: center;
            }
            .buttons-container {
                margin-top: 20px;
                margin-left: 0;
            }
        }

        /* 🔹 Container PDF */
        .a4-container { 
            width: 210mm; 
            height: 297mm; 
            background: #ffffff; 
            padding: 15mm; 
            position: relative; 
            font-family: "Times New Roman", Times, serif; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            font-size: 18px;
            box-sizing: border-box;
        }

        /* Header */
        .header { display: flex; align-items: center; border-bottom: 4px solid #b90912da; padding-bottom: 8px; margin-bottom: 15px; }
        .header img { width: 70px; height: 70px; margin-right: 15px; border-radius: 12px; }
        .text-header { flex: 1; text-align: center; }
        .text-header h1 { font-size: 20px; color: #3c57a1; margin-top: 6px; margin-bottom: 0; font-family: Arial, Helvetica, sans-serif; }
        .text-header .i { font-family: "Times New Roman", Times, serif; font-size: 14px; color: #ff0303; font-weight: 600; opacity: 0.75; display: block; margin-bottom: 6px; margin-top: 0; }
        .text-header .i span { position: relative; top: 2px; font-size: 9px; }
        .text-header .p1 { font-size: 14px; margin: 2px 0; font-weight: 600; }

        /* Référence */
        .rcf { margin: 8px 0 15px 0; font-weight: 600; font-size: 16px; font-family: Arial, sans-serif; }

        /* Titre */
        .title { font-family: Arial, sans-serif; font-size: 28px; font-weight: 900; color: #000000; text-align: center; margin: 15px 0; }
        .title::after { content: "*_*_*_*_*_*_*"; font-weight: bold; display: block; margin: 5px auto 0 auto; color: #0f0f0f; font-size: 20px; letter-spacing: 2px; }

        /* Contenu */
        .content { text-align: justify; line-height: 1.5; font-size: 16px; }
        .content p { font-size: 16px; margin: 8px 0; }
        .content b { color: #000000; }

        /* Signatures */
        .signatures { display: flex; justify-content: space-between; margin-top: 80px; flex-wrap: wrap; }
        .sign { width: 48%; }
        .sign.student { text-align: left; margin-top: 20px; }
        .sign.student p, .sign.director p { font-size: 18px; margin: 8px 0; }
        .sign.director { text-align: center; }

        /* Footer */
        .company { position: absolute; bottom: 15mm; left: 15mm; right: 15mm; border-top: 4px solid #ac0810d3; text-align: justify; font-size: 11px; padding-top: 5px; font-weight: 600; }
        .company p { font-size: 11px; margin: 0; }

        /* 🔹 Boutons flexibles hors PDF */
        .buttons-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

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
            width: 200px;
            text-align: center;
        }

        .buttons-container a:hover,
        .buttons-container button:hover {
            transform: translateY(-2px);
        }

        .back { background: #6b7280; }       /* gris */
        .print { background: #2563eb; }      /* bleu */
        .download { background: #16a34a; }   /* vert */

        /* 🔹 Masquer les boutons à l'impression / PDF */
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            .buttons-container {
                display: none !important;
            }
            .a4-container {
                box-shadow: none;
                padding: 15mm;
                margin: 0;
                width: 210mm;
                height: 297mm;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- 🔹 Container PDF -->
    <div class="a4-container">
        <div class="header">
            <img src="{{ asset('images/TFGLOGO.png') }}" alt="Logo TFG SARL">
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
            a effectué un stage de type <b>{{ $stage->typestage->libelle ?? '—' }}</b> dans notre entreprise,
            au sein du service <b>{{ $stage->service->nom ?? '—' }}</b>,
            durant la période du <b>{{ $stage->date_debut?->isoFormat('D MMMM YYYY') }}</b>
            au <b>{{ $stage->date_fin?->isoFormat('D MMMM YYYY') }}</b>.</p>

            <p>Durant ce stage, il/elle a travaillé sur : <b>{{ $stage->theme }}</b>.</p>
            <p>En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.</p>
        </div>

        <div class="signatures">
            <div class="sign student">
                <p>Reçu le ..</p>
                <p style="margin-top:30px;"><b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b></p>
            </div>
            @foreach($stage->signataires as $signataire)
            <div class="sign director">
                @php $pivot = $signataire->pivot; @endphp
                <p><b>Fait à Cotonou, le {{ now()->isoFormat('D MMMM YYYY') }}</b></p>
                @if($signataire->isDG())
                    <p style="margin-top:10px;"><b>{{ $signataire->poste }}</b></p>
                    <p style="margin-top:30px;"><b>{{ $signataire->nom }}</b></p>
                @elseif($pivot->par_ordre)
                    <p><b>Le Directeur Général et P.O</b></p>
                    <p><b>{{ $signataire->poste }}</b></p>
                    <p style="margin-top:30px;"><b>{{ $signataire->nom }}</b></p>
                @else
                    <p><b>{{ $signataire->poste }}</b></p>
                    <p style="margin-top:30px;"><b>{{ $signataire->nom }}</b></p>
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

    <!-- 🔹 Boutons hors PDF -->
    <div class="buttons-container">
        <a href="{{ route('stages.index') }}"><button type="button" class="back">Retour</button></a>
        <button onclick="window.print()" class="print">Imprimer</button>
        <a href="{{ route('stages.attestation.download', $stage->id) }}"><button type="button" class="download">Télécharger PDF</button></a>
    </div>
</body>
</html>