<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande de permission</title>
</head>
<body style="margin:0;padding:32px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:24px;padding:32px;border:1px solid #e2e8f0;">
        <p style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#4154f1;margin:0 0 14px;">Soumission de permission</p>
        <h1 style="font-size:28px;line-height:1.2;margin:0 0 12px;">Nouvelle demande à consulter</h1>
        <p style="font-size:15px;line-height:1.7;color:#475569;margin:0 0 24px;">
            Bonjour {{ $recipient->name }}, une demande de permission a été soumise par
            <strong>{{ $permissionRequest->requester->name }}</strong>.
            Vous recevez ce message en tant que {{ $recipientRoleLabel }}.
        </p>

        <div style="border:1px solid #e2e8f0;border-radius:18px;padding:20px;background:#f8fafc;">
            <p style="margin:0 0 8px;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.14em;">Résumé</p>
            <p style="margin:0 0 8px;font-size:15px;"><strong>Type :</strong> {{ $permissionRequest->type_label }}</p>
            <p style="margin:0 0 8px;font-size:15px;"><strong>Date :</strong> {{ $permissionRequest->request_date?->format('d/m/Y') }}</p>
            <p style="margin:0 0 8px;font-size:15px;"><strong>Motif :</strong> {{ $permissionRequest->reason }}</p>
            <p style="margin:0;font-size:15px;"><strong>Statut :</strong> {{ $permissionRequest->status_label }}</p>
        </div>

        <p style="font-size:14px;line-height:1.7;color:#475569;margin:24px 0 0;">
            Le PDF joint reprend tous les détails de la demande pour consultation et traitement dans l'application.
        </p>
    </div>
</body>
</html>
