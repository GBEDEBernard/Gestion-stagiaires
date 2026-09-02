@php
    $c   = $report['counts'];
    $r   = $report['ratios'];
    $w   = $report['window'];
    $an  = $report['anomalies'];
    $p   = $stage->etudiant?->personnel;
    $max = config('evaluation.max_score', 20);

    $nom = $p ? trim(($p->prenom ?? '') . ' ' . ($p->nom ?? '')) : 'Stagiaire';

    $pct = function (?array $ratio) {
        if (!$ratio) return '—';
        $frac = $ratio['numerator'] . ' / ' . $ratio['denominator'];
        return $ratio['rate'] !== null
            ? $frac . '  (' . number_format($ratio['rate'] * 100, 1, ',', ' ') . ' %)'
            : $frac;
    };

    $lignes = [
        ['Assiduité',          $r['assiduite'],          'jours attendus'],
        ['Ponctualité',        $r['ponctualite'],        'jours pointés'],
        ['Journées complètes', $r['journees_completes'], 'jours pointés'],
        ['Tenue de poste',     $r['tenue_poste'],        'jours pointés'],
        ['Comptes rendus',     $r['comptes_rendus'],     'jours pointés'],
        ['Incidents ouverts',  $r['incidents'],          'jours pointés'],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; color: #000; }

        .letterhead { width: 100%; border-bottom: 3px solid #b90912; padding-bottom: 6px; margin-bottom: 18px; }
        .letterhead td { vertical-align: middle; }
        .lh-text { text-align: center; }
        .lh-text h1 { font-family: Arial, Helvetica, sans-serif; font-size: 14pt; color: #3c57a1; margin: 0 0 3px; }
        .lh-text .tag { font-size: 9pt; font-weight: bold; color: #ff0303; margin: 0 0 3px; }
        .lh-text .act { font-size: 8.5pt; font-weight: bold; margin: 0; }

        h2.doc { text-align: center; font-size: 17pt; font-weight: bold; margin: 4px 0 2px; letter-spacing: .5px; }
        p.sub  { text-align: center; font-size: 10pt; margin: 0 0 16px; }

        h3 { font-size: 11.5pt; margin: 18px 0 6px; border-bottom: 1px solid #999; padding-bottom: 3px; }

        table.data { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table.data th { text-align: left; font-size: 8.5pt; text-transform: uppercase; letter-spacing: .5px;
                        color: #555; border-bottom: 1.5px solid #333; padding: 0 6px 4px 0; }
        table.data td { padding: 5px 6px 5px 0; border-bottom: 0.5px solid #ddd; }
        td.num { text-align: right; white-space: nowrap; }

        table.kv { width: 100%; font-size: 10pt; }
        table.kv td { padding: 3px 0; }
        table.kv td.lbl { color: #444; width: 42%; }

        /* Récapitulatif sur quatre colonnes : largeurs explicites, sinon les
           paires libellé/valeur se télescopent et « 42 » colle au libellé suivant. */
        table.kv4 { width: 100%; font-size: 10pt; border-collapse: collapse; }
        table.kv4 td { padding: 4px 0; border-bottom: 0.5px solid #eee; }
        table.kv4 td.l1 { color: #444; width: 30%; }
        table.kv4 td.v1 { width: 18%; text-align: right; padding-right: 22px; white-space: nowrap; }
        table.kv4 td.l2 { color: #444; width: 30%; }
        table.kv4 td.v2 { width: 18%; text-align: right; white-space: nowrap; }

        .note-box { border: 1px solid #333; padding: 10px 12px; margin-top: 6px; }
        .note-box .score { font-size: 20pt; font-weight: bold; }
        .note-box .mention { font-size: 10pt; }

        .muted { color: #555; font-size: 9pt; }
        .warn  { border-left: 3px solid #b90912; padding-left: 8px; font-size: 9pt; color: #7a1a1a; }

        .company { border-top: 3px solid #030303; padding-top: 3px; margin-top: 22px;
                   font-size: 7.5pt; font-weight: bold; font-style: italic; text-align: justify; }
        .gen { font-size: 7pt; font-weight: normal; font-style: normal; color: #666; text-align: right; margin-top: 3px; }
    </style>
</head>
<body>

    <table class="letterhead">
        <tr>
            {{-- Les dimensions sont posées en attributs HTML : mPDF ignore le
                 width/height CSS d'une image en cellule de tableau et retombe
                 sur la taille native du fichier — 1254 px, qui fait exploser
                 la mise en page. --}}
            <td width="80" style="width: 80px;">
                @if($logoDataUri)<img src="{{ $logoDataUri }}" width="62" height="62" alt="">@endif
            </td>
            {{-- Couleurs en style en ligne : mPDF ne fait pas descendre la
                 cascade CSS à travers une cellule de tableau, le bleu et le rouge
                 déclarés en classe restaient sans effet. --}}
            <td class="lh-text" align="center">
                <h1 style="font-family: Arial, Helvetica, sans-serif; font-size: 14pt; color: #3c57a1; margin: 0 0 3px;">
                    TECHNOLOGY FOREVER GROUP SARL
                </h1>
                <p style="font-size: 9pt; font-weight: bold; color: #ff0303; margin: 0 0 3px;">
                    *** La Technologie au service du développement ***
                </p>
                <p style="font-size: 8.5pt; font-weight: bold; margin: 0;">
                    Informatique – Télécommunications – BTP – Énergie – Électricité – Formations –
                    Commerce Général – Fournitures – Import-Export &amp; Divers
                </p>
            </td>
        </tr>
    </table>

    <h2 class="doc">RAPPORT DE STAGE</h2>
    <p class="sub">
        {{ $nom }}
        &nbsp;|&nbsp;
        du {{ \Carbon\Carbon::parse($w['from'])->isoFormat('Do MMMM YYYY') }}
        au {{ \Carbon\Carbon::parse($w['to'])->isoFormat('Do MMMM YYYY') }}
    </p>

    @if(!$report['is_frozen'])
        <p class="warn">
            Document provisoire — l'évaluation n'est pas finalisée. Les chiffres présentés
            peuvent encore évoluer et n'engagent pas l'entreprise.
        </p>
    @endif

    <h3>Le stage</h3>
    <table class="kv">
        <tr><td class="lbl">Thème</td><td>{{ $stage->theme ?: '—' }}</td></tr>
        <tr><td class="lbl">Type</td><td>{{ $stage->typestage?->libelle ?? '—' }}</td></tr>
        <tr><td class="lbl">Site</td><td>{{ $stage->site?->name ?? '—' }}</td></tr>
        <tr><td class="lbl">Domaine</td><td>{{ $stage->domaine?->nom ?? '—' }}</td></tr>
        <tr><td class="lbl">Jours de présence</td><td>{{ $stage->workDaysLabel() }}</td></tr>
    </table>

    <h3>Assiduité</h3>
    <table class="data">
        <thead>
            <tr><th>Indicateur</th><th>Résultat</th><th>Base de calcul</th></tr>
        </thead>
        <tbody>
            @foreach($lignes as [$label, $ratio, $base])
                <tr>
                    <td>{{ $label }}</td>
                    <td class="num">{{ $pct($ratio) }}</td>
                    <td class="muted">{{ $base }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="kv4" style="margin-top: 12px;">
        <tr>
            <td class="l1">Jours attendus</td><td class="v1">{{ $c['expected_days'] }}</td>
            <td class="l2">Jours présents</td><td class="v2">{{ $c['present_days'] }}</td>
        </tr>
        <tr>
            <td class="l1">Jours absents</td><td class="v1">{{ $c['absent_days'] }}</td>
            <td class="l2">Jours en retard</td><td class="v2">{{ $c['late_days'] }}</td>
        </tr>
        <tr>
            <td class="l1">Retard cumulé</td>
            <td class="v1">{{ number_format($c['late_minutes'], 0, ',', ' ') }} min</td>
            <td class="l2">Heures travaillées</td>
            <td class="v2">{{ number_format((float) $c['worked_hours'], 1, ',', ' ') }} h</td>
        </tr>
        @if(($report['permissions']['days_covered'] ?? 0) > 0)
            <tr>
                <td class="l1">Jours sur permission</td>
                <td class="v1">{{ $report['permissions']['days_covered'] }}</td>
                <td class="l2" colspan="2">
                    <span class="muted">retirés des jours attendus, ni présence ni absence</span>
                </td>
            </tr>
        @endif
    </table>

    <h3>Anomalies</h3>
    @if($an['total'] === 0)
        <p class="muted">Aucune anomalie relevée sur la période.</p>
    @elseif($disclosure === 'count')
        <p>{{ $an['total'] }} anomalie(s) relevée(s) sur la période, dont {{ $an['open'] }} non résolue(s).</p>
    @elseif($disclosure === 'grouped')
        <table class="data">
            <thead><tr><th>Nature</th><th>Nombre</th></tr></thead>
            <tbody>
                @foreach($an['by_type'] as $type => $count)
                    <tr>
                        <td>{{ (new \App\Models\AttendanceAnomaly(['anomaly_type' => $type]))->type_label }}</td>
                        <td class="num">{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table class="data">
            <thead><tr><th>Date</th><th>Nature</th><th>Gravité</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($an['items'] as $a)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($a->detected_at)->format('d/m/Y') }}</td>
                        <td>{{ $a->type_label }}</td>
                        <td>{{ $a->severity }}</td>
                        <td>{{ $a->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($evaluation && $evaluation->isFinalized())
        <h3>Évaluation</h3>
        <table class="data">
            <thead><tr><th>Critère</th><th>Coef.</th><th>Note</th></tr></thead>
            <tbody>
                @foreach($evaluation->scores as $row)
                    <tr>
                        <td>
                            {{ $row->label_snapshot }}
                            @if($row->isOverridden())
                                <br><span class="muted">Note calculée {{ number_format((float) $row->computed_score, 2, ',', ' ') }} —
                                    retenue {{ number_format((float) $row->score, 2, ',', ' ') }} : {{ $row->comment }}</span>
                            @elseif($row->comment)
                                <br><span class="muted">{{ $row->comment }}</span>
                            @endif
                        </td>
                        <td class="num">{{ $row->weight_snapshot }}</td>
                        <td class="num">{{ number_format((float) $row->score, 2, ',', ' ') }} / {{ $max }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="note-box">
            <table style="width:100%">
                <tr>
                    <td>Note finale</td>
                    <td style="text-align:right">
                        <span class="score">{{ number_format((float) $evaluation->final_score, 2, ',', ' ') }} / {{ $max }}</span>
                        <br><span class="mention">{{ $evaluation->mention() }}</span>
                    </td>
                </tr>
            </table>
        </div>

        @if($evaluation->general_comment)
            <h3>Appréciation générale</h3>
            <p>{{ $evaluation->general_comment }}</p>
        @endif

        <p class="muted" style="margin-top: 14px;">
            Évaluation finalisée le {{ $evaluation->finalized_at?->format('d/m/Y') }}
            @if($evaluation->evaluator) par {{ $evaluation->evaluator->name }} @endif.
            Les chiffres d'assiduité de ce document sont ceux constatés à cette date.
        </p>
    @endif

    <div class="company">
        {{-- Ni capital, ni RCCM, ni IFU, ni adresse personnelle du gérant : ce
             rapport circule hors de l'entreprise, jusqu'aux écoles. --}}
        <p style="margin:0">TECHNOLOGY FOREVER GROUP SARL
            &nbsp;·&nbsp; www.tfgbusiness.com
            &nbsp;·&nbsp; Tél : (+229) 01 65 10 39 59 / 01 69 58 06 03
            &nbsp;·&nbsp; 09 BP 791 (St-Michel | Cotonou)</p>
        <p class="gen">Document généré le {{ now()->format('d/m/Y') }}</p>
    </div>

</body>
</html>
