{{-- resources/views/emails/attestation-signer-notification.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Signature d'attestation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
        }
        .header img {
            max-width: 80px;
            margin-bottom: 15px;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .info-box .label {
            font-weight: bold;
            color: #667eea;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .signature-preview {
            background: #fff9e6;
            border: 1px solid #ffe6b3;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/TFGLOGO.png') }}" alt="TFG SARL Logo" style="background: white; border-radius: 50%; padding: 10px;">
            <h1>Demande de signature</h1>
        </div>

        <div class="content">
            <h2>Bonjour {{ $signer->name }},</h2>

            <p>Une demande de signature pour une attestation de stage vous a été soumise.</p>

            <div class="info-box">
                <p><span class="label">📌 Stagiaire :</span> {{ $stage->etudiant->personnel->nom ?? '' }} {{ $stage->etudiant->personnel->prenom ?? '' }}</p>
                <p><span class="label">🎓 École :</span> {{ $stage->etudiant->ecole ?? 'Non renseignée' }}</p>
                <p><span class="label">📅 Période :</span> {{ $stage->date_debut->format('d/m/Y') }} → {{ $stage->date_fin->format('d/m/Y') }}</p>
                <p><span class="label">🏢 Domaine :</span> {{ $stage->domaine->nom ?? 'Non défini' }}</p>
                <p><span class="label">📝 Thème :</span> {{ $stage->theme ?? 'Non défini' }}</p>
            </div>

            <div class="signature-preview">
                <p><strong>📄 Aperçu de l'attestation :</strong></p>
                <p style="font-size: 13px;">Je soussigné Appolinaire KONNON, Directeur Général de la société Technology Forever SARL (TFG SARL), atteste que <strong>{{ $stage->etudiant->personnel->prenom ?? '' }} {{ $stage->etudiant->personnel->nom ?? '' }}</strong> a effectué un stage d'une durée de <strong>{{ ceil($stage->date_debut->diffInDays($stage->date_fin) / 30) }} mois</strong> dans notre entreprise...</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $signatureUrl }}" class="button">✍️ Signer l'attestation</a>
            </div>

            <p style="font-size: 14px; color: #666;">
                Ce lien vous permettra de consulter et de signer électroniquement l'attestation. 
                Si vous avez des questions, veuillez contacter l'administrateur.
            </p>

            <p style="font-size: 13px; margin-top: 20px;">
                Cordialement,<br>
                <strong>TFG SARL - Direction Générale</strong>
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Technology Forever SARL (TFG SARL) - Tous droits réservés.</p>
            <p>Si vous ne devriez pas recevoir cet email, veuillez l'ignorer.</p>
        </div>
    </div>
</body>
</html>