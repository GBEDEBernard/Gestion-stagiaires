<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de stage</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            padding: 10px;
        }

        .a4-container {
            width: 210mm;
            height: 297mm;
            background: #fff;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        /* Header avec logo */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 4px solid #db040f;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            height: 80px;
            margin-right: 20px;
            border-radius: 50%;
        }
        .text-header {
            flex: 1;
        }
        .text-header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: #0c43db;
            font-weight: bold;
            text-align: center;
        }
        .text-header p i {
            font-size: 14px;
            color: #4fa0a0;
            font-weight: bold;
            text-align: center;
            display: block;
        }
        .text-header .p1 {
            margin: 2px 0;
            font-size: 13px;
            color: #101111;
            font-weight: bold;
            text-align: center;
        }

        /* RCF en bas du header */
        .rcf {
            
            margin: 10px 0 20px 0;
            font-size: 14px;
            font-style: italic;
        }

        /* Titre avec étoiles */
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 24px;
            margin: 30px 0;
            color: #B91C1C;
            font-family: serif;
            position: relative;
        }
        .title::after {
            content: "✯✯✯✯✯✯✯✯✯✯✯✯✯✯✯";
            display: block;
            margin: 8px auto 0 auto;
            color: #0f0f0f;
            font-size: 18px;
            letter-spacing: 4px;
        }

        /* Contenu */
        .content {
            text-align: justify;
            line-height: 1.6;
            font-size: 14px;
        }
        .content b {
            color: #000000;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .sign {
            width: 100%;
        }
        .sign.student {
            text-align: left;
            margin-top: 44px
        }
        .sign.director {
            text-align: center;
            margin-right: 50px;
        }
        .sign p {
            margin: 6px 0;
            
        }
       .sign p .b1{
          font-size: 13px;
          font-weight: normal;
            
        }
 

        /* Footer pro */
        .company {
            position: absolute;
            bottom: 100px;
            left: 40px;
            right: 40px;
            border-top: 4px solid #db040f;
            color: #000000;
            line-height: 1.2;
            font-size: 11px;
            box-sizing: border-box;
            padding-top: 6px;
        }
        .company p {
            text-align: center;
            margin: 4px 0;
        }

        /* Impression */
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .a4-container { box-shadow: none; margin: 0; width: 210mm; height: 297mm; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            #actions { display: none; }
        }

        /* Boutons */
        #actions { margin-bottom: 20px; text-align: center; }
        #actions a, #actions button {
            margin: 0 6px;
            padding: 10px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-back { background: #6B7280; color: #fff; }
        .btn-back:hover { background: #4B5563; }
        .btn-print { background: #1E3A8A; color: #fff; }
        .btn-print:hover { background: #162F6A; }
        .btn-download { background: #B91C1C; color: #fff; }
        .btn-download:hover { background: #991B1B; }
    </style>
</head>
<body>

<div>
    <!-- Boutons -->
    <div id="actions">
        <a href="{{ route('stages.show', $stage->id) }}" class="btn-back">⬅ Retour</a>
        <button onclick="window.print()" class="btn-print">🖨️ Imprimer</button>
        <a href="{{ route('stages.attestation.download', $stage->id) }}" class="btn-download">⬇ Télécharger PDF</a>
    </div>

    <!-- Contenu A4 -->
    <div class="a4-container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('images/TGFpdf.jpg') }}" alt="Logo TFG SARL">
            <div class="text-header">
                <h1>TECHNOLOGY FOREVER GROUP (TFG SARL)</h1>
                <p><i>---La Technologie au service du développement ---</i></p>
                <p class="p1">Ingénierie – Électricité – Électronique – Informatique – Énergies renouvelables – Commerce général – Import Export divers</p>
            </div>
        </div>

        <!-- RCF -->
        <div class="rcf">Réf : ATS 03_25/ TFG/ DG / DT-ISI / SD</div>

        <!-- Titre -->
        <div class="title">ATTESTATION DE STAGE</div>

        <!-- Contenu -->
        <div class="content">
            Je soussigné <b>Appolinaire KONNON</b>, Directeur Général de la société <b>Technology Forever Group SARL</b>,
            atteste que <b>{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b>,
            étudiant(e) à <b>{{ $stage->etudiant->ecole }}</b>,
            a effectué un stage de type <b>{{ $stage->typestage->libelle ?? '—' }}</b> dans notre entreprise,
            au sein du service <b>{{ $stage->service->nom ?? '—' }}</b>,
            durant la période du <b>{{ $stage->date_debut?->format('d/m/Y') }}</b>
            au <b>{{ $stage->date_fin?->format('d/m/Y') }}</b>.

            <br><br>
            Durant ce stage, il/elle a travaillé sur : <b>{{ $stage->theme }}</b>.
            <br><br>
            En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <!-- Etudiant -->
            <div class="sign student">
                <p><b class="b1">Reçu le ..</b></p>
                <p style="margin-top:60px;"><b class="b2">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</b></p>
            </div>

            <!-- Directeur -->
            <div class="sign director">
                <p><b>Fait à Abomey-Calavi, le {{ now()->format('d/m/Y') }}</b></p>
                <p><b>Le Directeur Général et P.O</b></p>
                <p><b>Le Directeur Technique DT</b></p>
                <p style="margin-top:60px;"><b>Gamaliel GBETIE</b></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="company">
            <p>TFG SARL : Capital de 1.000.000 FCFA - RCCM : RB/ABT/18 B 2111 - N°IFU : 3201810222368</p>
            <p>Siège : M/ GAUTHE Gabriel - Allègléta | Godomey-Togoudo (Abomey-Calavi) - Site Web : www.tfgbusiness.com</p>
            <p>Tél : (+229) 0165103959 / 0169580603 - 09 BP 791 (St-Michel | Cotonou)</p>
        </div>

    </div>
</div>

</body>
</html>
