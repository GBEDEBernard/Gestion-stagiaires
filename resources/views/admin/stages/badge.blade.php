<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Badge Stagiaire</title>
<style>
/* Taille A6 exacte pour impression */
@page { margin: 0; size: 105mm 148mm; }

/* Général */
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh; /* Centrer le badge sur la page */
}

/* Badge container */
.badge-container {
    width: 105mm;
    height: 148mm;
    background-color: #ffffff;
    border: 2px solid #d6dbe9;
    border-radius: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: center;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

/* Header */
.header {
    background-color: #b1c7e4;
    padding: 8px 0;
    border-bottom: 2px solid #ebebeb;
    height: 50px;
}
.header h1 {
    margin: 2;
    font-size: 18px; /* Augmenté */
    font-weight: bold;
    color: #0c42d8;
}

/* Logo */
.logo {
    display: block;
    width: 95px;
    height: 95px;
    border-radius: 50%;
    object-fit: cover;
    margin: 12px auto;
    
}

/* Infos stagiaire */
.infos {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 0 20px;
    text-align: center;
}
.infos h2 {
    font-size: 20px; /* Augmenté */
    font-weight: bold;
    margin-bottom: 6px;
    color: #1F2937;
    text-align: center;
}
.badge-number {
    font-size: 24px; /* Augmenté */
    font-weight: 800;
    color: #B91C1C;
    margin-bottom: 8px;
    text-align: center;
}
.infos p {
    font-size: 14px; /* Augmenté */
    margin: 3px 0;
    color: #1F2937;
}
.infos p b {
    font-weight: 600;
}

/* Footer */
.footer {
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: linear-gradient(to right, #1E3A8A, #B91C1C);
    color: white;
    padding: 8px 10px;
    font-size: 11px; /* Augmenté */
    border-top: 2px solid #d6dbe9;
    border-radius: 0 0 20px 20px;
}

/* QR */
.footer .qr {
    background: white;
    padding: 4px;
    border-radius: 8px;
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Infos entreprise bien alignées */
.footer .company-info {
    text-align: left;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 4px;
    font-size: 14px;
}

/* Impression : garder les couleurs */
@media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body { background: #ffffff; height: auto; }
    #print-btn, #back-btn { display: none; } /* Masquer boutons */
}

/* Boutons */
button {
    margin: 10px 5px;
    padding: 10px 16px;
    border: none;
    font-size: 14px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    color: white;
}
#print-btn {
    background: #1E3A8A; /* Bleu */
}
#print-btn:hover {
    background: #2563eb;
}
#back-btn {
    background: #B91C1C; /* Rouge */
}
#back-btn:hover {
    background: #dc2626;
}
</style>
</head>
<body>

<div>
    <!-- Badge -->
    <div class="badge-container">
        <!-- Header -->
        <div class="header">
            <h1>TECHNOLOGY FOREVER GROUP (TFG)</h1>
        </div>

        <!-- Logo -->
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/TFGLOGO.png'))) }}" class="logo" alt="TFG Logo">

        <!-- Infos stagiaire -->
        <div class="infos">
            <h2>Stagiaire</h2>
            <div class="badge-number">{{ str_pad($stage->badge->badge ?? '000000', 6, '0', STR_PAD_LEFT) }}</div>
            <p><b>Nom :</b> {{ $stage->etudiant->nom ?? '—' }}</p>
            <p><b>Prénom :</b> {{ $stage->etudiant->prenom ?? '—' }}</p>
            <p><b>École :</b> {{ $stage->etudiant->ecole ?? '—' }}</p>
            <p><b>Type :</b> {{ $stage->typestage->libelle ?? '—' }}</p>
            <p><b>Tél :</b> {{ $stage->etudiant->telephone ?? '—' }}</p>
            <p><b>Dates :</b> {{ $stage->date_debut?->format('d/m/Y') ?? '—' }} - {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}</p>
            <p><b>Email :</b> {{ $stage->etudiant->email ?? '—' }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="company-info">
                <div>📍 Abomey-Calavi (Togoudo)</div>
                <div>📞 0166439030 / 0169580603</div>
                <div>🌐 www.tfgbusiness.com</div>
            </div>
            <div class="qr">
                {!! QrCode::size(70)->generate('https://www.tfgbusiness.com') !!}
            </div>
        </div>
    </div>

    <!-- Boutons -->
    <div style="text-align:center;">
        <button id="back-btn" onclick="window.history.back()">⬅ Retour</button>
        <button id="print-btn" onclick="window.print()">🖨️ Imprimer</button>
    </div>
</div>

</body>
</html>
