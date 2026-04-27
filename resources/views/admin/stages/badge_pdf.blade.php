<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Badge stagiaire</title>
    <style>
        @page { margin: 0; size: 105mm 148mm; }
        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            background: #ffffff;
        }
        .badge {
            width: 105mm;
            height: 148mm;
            border: 2px solid #dbe3f0;
            border-radius: 20px;
            overflow: hidden;
            box-sizing: border-box;
        }
        .header {
            background: #dbeafe;
            padding: 12px 10px;
            text-align: center;
            border-bottom: 2px solid #dbe3f0;
        }
        .header h1 {
            margin: 0;
            font-size: 17px;
            color: #1d4ed8;
        }
        .content {
            padding: 14px 12px 0;
            text-align: center;
        }
        .logo {
            width: 90px;
            height: 90px;
            border-radius: 999px;
            object-fit: cover;
        }
        .role {
            margin-top: 12px;
            font-size: 22px;
            font-weight: 700;
            color: #111827;
        }
        .badge-number {
            margin-top: 8px;
            font-size: 28px;
            font-weight: 800;
            color: #b91c1c;
        }
        .infos {
            margin-top: 12px;
            text-align: left;
            font-size: 13px;
            line-height: 1.6;
            color: #111827;
        }
        .footer {
            margin-top: 14px;
            background: linear-gradient(90deg, #1e3a8a, #b91c1c);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
        }
        .company {
            font-size: 11px;
            line-height: 1.5;
        }
        .qr {
            width: 68px;
            height: 68px;
            border-radius: 8px;
            background: white;
            padding: 4px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="badge">
        <div class="header">
            <h1>TECHNOLOGY FOREVER GROUP (TFG)</h1>
        </div>

        <div class="content">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/TFGLOGO.png'))) }}" class="logo" alt="Logo TFG">
            <div class="role">Stagiaire</div>
            <div class="badge-number">{{ str_pad($stage->badge->badge ?? '000000', 6, '0', STR_PAD_LEFT) }}</div>

            <div class="infos">
                <div><strong>Nom :</strong> {{ $stage->etudiant->nom ?? '—' }} {{ $stage->etudiant->prenom ?? '—' }}</div>
                <div><strong>Ecole :</strong> {{ $stage->etudiant->ecole ?? '—' }}</div>
                <div><strong>Type :</strong> {{ $stage->typestage->libelle ?? '—' }}</div>
                <div><strong>Tel :</strong> {{ $stage->etudiant->telephone ?? '—' }}</div>
                <div><strong>Dates :</strong> {{ $stage->date_debut?->format('d/m/Y') ?? '—' }} - {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}</div>
                <div><strong>Email :</strong> {{ $stage->etudiant->email ?? '—' }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="company">
                <div>Togoudo · Abomey-Calavi</div>
                <div>0166439030 / 0169580603</div>
                <div>www.tfgbusiness.com</div>
            </div>
            <div class="qr">
                {!! QrCode::size(60)->generate('https://www.tfgbusiness.com') !!}
            </div>
        </div>
    </div>
</body>
</html>
