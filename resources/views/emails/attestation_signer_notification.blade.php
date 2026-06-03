<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle attestation à signer</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h1 style="color: #5f3dc4;">Nouvelle demande de signature</h1>

    <p>Bonjour {{ $signataire->personnel->full_name ?? $signataire->name }},</p>

    <p>Une attestation de stage a été générée pour <strong>{{ $stage->etudiant->personnel->full_name ?? ($stage->etudiant->personnel->nom . ' ' . $stage->etudiant->personnel->prenom) }}</strong>.</p>

    <p>Référence de l'attestation : <strong>{{ $attestation->reference }}</strong></p>
    <p>Thème du stage : <strong>{{ $stage->theme ?? 'Non renseigné' }}</strong></p>
    <p>Du <strong>{{ $stage->date_debut?->format('d/m/Y') ?? '—' }}</strong> au <strong>{{ $stage->date_fin?->format('d/m/Y') ?? '—' }}</strong>.</p>

    <p>Merci de vous connecter à l'application pour finaliser la signature ou prendre connaissance de la demande.</p>

    <p style="margin-top: 24px;">Cordialement,<br>TFG SARL</p>
</body>
</html>
