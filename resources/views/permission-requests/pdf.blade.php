<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de permission</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.5;
            margin: 32px;
        }

        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .subtitle {
            color: #475569;
            margin-top: 6px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .grid td {
            width: 50%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #64748b;
            margin-bottom: 4px;
        }

        .value {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        .section {
            margin-top: 22px;
        }

        .section h2 {
            font-size: 14px;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f172a;
        }

        .box {
            border: 1px solid #cbd5e1;
            padding: 14px;
            border-radius: 10px;
            background: #f8fafc;
        }

        .signatures {
            margin-top: 28px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 16px 0;
        }

        .signatures td {
            width: 33.33%;
            border: 1px dashed #94a3b8;
            height: 120px;
            vertical-align: bottom;
            padding: 12px;
        }

        .muted {
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Demande de permission</p>
        <p class="subtitle">
            Dossier n° {{ $permissionRequest->id }} · genere le {{ now()->format('d/m/Y à H:i') }}
        </p>
    </div>

    <table class="grid">
        <tr>
            <td>
                <div class="label">Demandeur</div>
                <div class="value">{{ $permissionRequest->requester->name }}</div>
            </td>
            <td>
                <div class="label">Type</div>
                <div class="value">{{ $permissionRequest->type_label }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Date concernee</div>
                <div class="value">{{ $permissionRequest->request_date?->format('d/m/Y') }}</div>
            </td>
            <td>
                <div class="label">Contexte</div>
                <div class="value">{{ $permissionRequest->stage?->theme ?? $permissionRequest->domaine?->nom ?? 'Non defini' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Debut</div>
                <div class="value">{{ $permissionRequest->starts_at?->format('d/m/Y H:i') ?? 'Non precise' }}</div>
            </td>
            <td>
                <div class="label">Fin</div>
                <div class="value">{{ $permissionRequest->ends_at?->format('d/m/Y H:i') ?? 'Non precisee' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Premier validateur</div>
                <div class="value">{{ $permissionRequest->firstApprover?->name ?? 'Diffusion directe vers les signataires' }}</div>
            </td>
            <td>
                <div class="label">Statut du dossier</div>
                <div class="value">{{ $permissionRequest->status_label }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2>Motif</h2>
        <div class="box">{{ $permissionRequest->reason }}</div>
    </div>

    <div class="section">
        <h2>Details complementaires</h2>
        <div class="box">{{ $permissionRequest->details ?: 'Aucun detail complementaire fourni.' }}</div>
    </div>

    <div class="section">
        <h2>Validation</h2>
        <div class="box">
            <strong>Note de validation :</strong>
            <span class="muted">{{ $permissionRequest->first_review_notes ?: 'Aucune note enregistree.' }}</span><br>
            <strong>Revu le :</strong>
            <span class="muted">{{ $permissionRequest->first_reviewed_at?->format('d/m/Y H:i') ?? 'En attente' }}</span>
        </div>
    </div>

    <div class="section">
        <h2>Signatures attendues</h2>
        <table class="signatures">
            <tr>
                @php
                    $signataires = collect($permissionRequest->signataires_snapshot ?? [])->take(3);
                @endphp
                @forelse($signataires as $signataire)
                    <td>
                        <strong>{{ $signataire['nom'] }}</strong><br>
                        <span class="muted">{{ $signataire['poste'] }}</span>
                    </td>
                @empty
                    <td>
                        <strong>Signature officielle</strong><br>
                        <span class="muted">Le circuit de diffusion renseignera les signataires.</span>
                    </td>
                    <td></td>
                    <td></td>
                @endforelse
            </tr>
        </table>
    </div>
</body>
</html>
