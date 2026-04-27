<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de permission</title>
</head>
<body style="margin:0;padding:32px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:24px;padding:32px;border:1px solid #e2e8f0;">
        <p style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#0f9d83;margin:0 0 14px;">Transmission officielle</p>
        <h1 style="font-size:28px;line-height:1.2;margin:0 0 12px;">Demande de permission à examiner</h1>
        <p style="font-size:15px;line-height:1.7;color:#475569;margin:0 0 24px;">
            Bonjour {{ $signataire->nom }}, le dossier de permission de <strong>{{ $permissionRequest->requester->name }}</strong> vous est transmis en pièce jointe au format PDF.
        </p>

        <div style="border:1px solid #e2e8f0;border-radius:18px;padding:20px;background:#f8fafc;">
            <p style="margin:0 0 8px;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.14em;">Résumé</p>
            <p style="margin:0 0 8px;font-size:15px;"><strong>Type :</strong> {{ $permissionRequest->type_label }}</p>
            <p style="margin:0 0 8px;font-size:15px;"><strong>Date :</strong> {{ $permissionRequest->request_date?->format('d/m/Y') }}</p>
            <p style="margin:0;font-size:15px;"><strong>Motif :</strong> {{ $permissionRequest->reason }}</p>
        </div>

        <p style="font-size:14px;line-height:1.7;color:#475569;margin:24px 0 0;">
            Le fichier PDF joint reprend le contexte, le validateur métier, les horaires concernés et les éléments de suivi utiles pour votre traitement.
        </p>
    </div>
</body>
</html>
