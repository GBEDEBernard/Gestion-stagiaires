<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier d'Activité & Rapports – {{ $report->user?->personnel?->nom ?? $report->user?->name ?? 'Collaborateur' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 14mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 9.5px;
            line-height: 1.45;
            background: #ffffff;
        }

        /* ---- En-tête Institutionnel ---- */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-logo {
            width: 60px;
            text-align: left;
        }
        .header-logo img {
            width: 48px;
            height: auto;
        }
        .header-text {
            text-align: center;
        }
        .header-text h1 {
            font-size: 13px;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .header-text .slogan {
            font-size: 8px;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .header-text .subtext {
            font-size: 7px;
            color: #64748b;
            line-height: 1.2;
        }

        /* ---- Titre du Document ---- */
        .doc-title-panel {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 8px 12px;
            margin-bottom: 12px;
            border-left: 4px solid #0f172a;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .doc-subtitle {
            font-size: 8.5px;
            color: #475569;
            margin-top: 2px;
        }

        /* ---- Badges & Étiquettes sobres ---- */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #94a3b8;
            background: #ffffff;
            color: #0f172a;
        }
        .badge-filled {
            background: #0f172a;
            color: #ffffff;
            border: 1px solid #0f172a;
        }

        /* ---- Sections ---- */
        .section {
            margin-bottom: 14px;
            page-break-inside: auto;
        }
        .section-header {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 2px;
            margin-bottom: 6px;
        }

        /* ---- Tableaux de Données ---- */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 6px;
        }
        table.data-table td, table.data-table th {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        table.data-table th {
            background: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
        }
        table.data-table td.label-col {
            background: #f8fafc;
            font-weight: bold;
            color: #334155;
            width: 25%;
        }
        table.data-table td.value-col {
            color: #0f172a;
            width: 25%;
        }

        /* ---- Cartes des Tâches ---- */
        .task-container {
            border: 1px solid #94a3b8;
            margin-bottom: 12px;
            background: #ffffff;
            page-break-inside: avoid;
        }
        .task-header-bar {
            background: #f1f5f9;
            border-bottom: 1px solid #94a3b8;
            padding: 6px 8px;
        }
        .task-title-text {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .task-body {
            padding: 8px;
        }

        /* ---- Blocs Rapports Journaliers ---- */
        .report-entry {
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0f172a;
            background: #fbfcfd;
            padding: 6px 8px;
            margin-top: 6px;
            margin-bottom: 6px;
            page-break-inside: avoid;
        }
        .report-entry-header {
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 3px;
            margin-bottom: 4px;
        }
        .report-field {
            margin-top: 4px;
            font-size: 8.5px;
            line-height: 1.4;
        }
        .report-field-title {
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 8px;
            display: block;
            margin-bottom: 1px;
        }

        /* ---- Avis & Commentaires ---- */
        .review-box {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 5px 7px;
            margin-bottom: 5px;
            page-break-inside: avoid;
            font-size: 8.5px;
        }

        /* ---- Signatures ---- */
        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        .sig-frame {
            border: 1px solid #94a3b8;
            padding: 8px;
            min-height: 75px;
            text-align: center;
            background: #ffffff;
        }
        .sig-heading {
            font-weight: bold;
            font-size: 8.5px;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .sig-space {
            height: 40px;
        }
        .sig-caption {
            font-size: 8px;
            color: #475569;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
        }

        /* ---- Footer ---- */
        .doc-footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #cbd5e1;
            font-size: 7.5px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>

    @php
        if (!isset($logoDataUri)) {
            $logoPath = public_path('images/TFGLOGO.png');
            $logoDataUri = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : '';
        }

        $authorPersonnel = $report->user?->personnel ?? $report->etudiant?->personnel;
        $authorFullName  = trim(($authorPersonnel?->prenom ?? '') . ' ' . ($authorPersonnel?->nom ?? $report->user?->name ?? 'Collaborateur'));
        $isStudent       = !is_null($report->etudiant_id);
        $tasksList       = $relatedTasks ?? collect([$report->task])->filter();
        $totalHoursSum   = $tasksList->sum(fn($t) => $t->dailyReports->sum('hours_declared'));
    @endphp

    {{-- ── En-tête officiel TFG ── --}}
    <table class="header-table">
        <tr>
            @if($logoDataUri)
            <td class="header-logo">
                <img src="{{ $logoDataUri }}" alt="Logo TFG">
            </td>
            @endif
            <td class="header-text">
                <h1>TECHNOLOGY FOREVER GROUP SARL</h1>
                <div class="slogan">La Technologie au service du développement</div>
                <div class="subtext">
                    Informatique – Télécommunications – BTP – Énergie – Formations – Commerce Général<br>
                    Direction des Ressources Humaines & Encadrement Technique
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Titre officiel du dossier ── --}}
    <div class="doc-title-panel">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td>
                    <div class="doc-title">Dossier d'Activité & Suivi des Rapports Journaliers</div>
                    <div class="doc-subtitle">
                        Séance de référence : <strong>{{ $report->report_date->isoFormat('dddd D MMMM YYYY') }}</strong> • Document réf. #{{ $report->id }}
                    </div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <span class="badge badge-filled">
                        {{ $report->status === 'submitted' ? 'Soumis' : ($report->status === 'reviewed' ? 'Validé' : 'Brouillon') }}
                    </span>
                    @if($report->hours_declared > 0)
                        <div style="font-size: 8px; font-weight: bold; color: #0f172a; margin-top: 2px;">
                            {{ $report->hours_declared }} h déclarée(s)
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ── SECTION 1 : IDENTIFICATION DU COLLABORATEUR & CADRE DU TRAVAIL ── --}}
    <div class="section">
        <div class="section-header">1. Identification du Collaborateur & Cadre du Stage / Mission</div>
        <table class="data-table">
            <tr>
                <td class="label-col">Nom & Prénom(s)</td>
                <td class="value-col"><strong>{{ $authorFullName }}</strong></td>
                <td class="label-col">Statut / Rôle</td>
                <td class="value-col">{{ $isStudent ? 'Stagiaire Académique' : 'Personnel / Employé' }}</td>
            </tr>
            <tr>
                <td class="label-col">Adresse email</td>
                <td class="value-col">{{ $report->user?->email ?? $report->etudiant?->user?->email ?? 'N/A' }}</td>
                <td class="label-col">Contact téléphonique</td>
                <td class="value-col">{{ $authorPersonnel?->telephone ?? $report->etudiant?->telephone ?? 'N/A' }}</td>
            </tr>
            @if($isStudent && $report->stage)
            <tr>
                <td class="label-col">Établissement & Filière</td>
                <td class="value-col">
                    {{ $report->stage->etudiant?->ecole ?? 'N/A' }}
                    @if($report->stage->filiere) ({{ $report->stage->filiere }}) @endif
                </td>
                <td class="label-col">Période du stage</td>
                <td class="value-col">
                    Du {{ $report->stage->date_debut?->format('d/m/Y') ?? 'N/A' }} au {{ $report->stage->date_fin?->format('d/m/Y') ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td class="label-col">Thème de stage</td>
                <td colspan="3" class="value-col" style="width: 75%;">
                    <strong>{{ $report->stage->theme ?? 'Non spécifié' }}</strong>
                </td>
            </tr>
            <tr>
                <td class="label-col">Encadrant / Superviseur</td>
                <td class="value-col">
                    {{ trim(($report->stage->supervisor?->personnel?->prenom ?? '') . ' ' . ($report->stage->supervisor?->personnel?->nom ?? $report->stage->supervisor?->name ?? 'Non assigné')) }}
                </td>
                <td class="label-col">Site d'affectation</td>
                <td class="value-col">
                    {{ $report->stage->site?->name ?? 'TFG SARL' }} @if($report->stage->site?->city)({{ $report->stage->site->city }})@endif
                </td>
            </tr>
            @elseif(!$isStudent)
            <tr>
                <td class="label-col">Département / Domaine</td>
                <td class="value-col">{{ $report->user?->domaine?->nom ?? 'Service technique' }}</td>
                <td class="label-col">Fonction</td>
                <td class="value-col">{{ $authorPersonnel?->fonction ?? 'Personnel' }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ── SECTION 2 : VUE CONSOLIDÉE DE TOUTES LES TÂCHES ET DE LEURS RAPPORTS ── --}}
    <div class="section">
        <div class="section-header">2. État d'Avancement des Tâches & Tous les Rapports Associés</div>

        @if($tasksList->isEmpty())
            <p style="font-size: 8.5px; color: #64748b; font-style: italic; padding: 4px 0;">
                Aucune tâche enregistrée dans ce dossier.
            </p>
        @else
            @foreach($tasksList as $index => $taskItem)
                @php
                    $taskReports = $taskItem->dailyReports ?? collect();
                    $isFocusTask = ($report->task_id === $taskItem->id);
                @endphp
                <div class="task-container">
                    {{-- En-tête de la tâche --}}
                    <div class="task-header-bar">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td>
                                    <span class="task-title-text">
                                        Mission {{ $index + 1 }} : {{ $taskItem->title }}
                                    </span>
                                    @if($isFocusTask)
                                        <span style="font-size: 7.5px; font-weight: bold; color: #0f172a; margin-left: 4px;">
                                            (★ Rapport de session rattaché)
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    <span class="badge">Priorité : {{ ['high'=>'Haute','low'=>'Basse','medium'=>'Moyenne','urgent'=>'Urgente'][$taskItem->priority] ?? ucfirst($taskItem->priority ?? 'Normale') }}</span>
                                    &nbsp;
                                    <span class="badge badge-filled">{{ ['completed'=>'Terminée','in_progress'=>'En cours','blocked'=>'Bloquée','pending'=>'En attente','todo'=>'À faire','cancelled'=>'Annulée'][$taskItem->status] ?? 'En cours' }} ({{ $taskItem->last_progress_percent ?? 0 }}%)</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="task-body">
                        @if($taskItem->description)
                            <div style="font-size: 8.5px; color: #334155; margin-bottom: 5px;">
                                <strong>Description :</strong> {{ $taskItem->description }}
                            </div>
                        @endif

                        <table class="data-table" style="margin-bottom: 6px;">
                            <tr>
                                <td class="label-col" style="width: 25%;">Échéance fixée</td>
                                <td class="value-col" style="width: 25%;">{{ $taskItem->due_date ? $taskItem->due_date->format('d/m/Y') : 'Non spécifiée' }}</td>
                                <td class="label-col" style="width: 25%;">Total rapports tâche</td>
                                <td class="value-col" style="width: 25%;">{{ $taskReports->count() }} compte(s)-rendu(s)</td>
                            </tr>
                        </table>

                        {{-- Liste exhaustive de tous les rapports déposés sur cette tâche --}}
                        <div style="font-size: 8px; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-top: 4px; margin-bottom: 2px;">
                            Comptes-rendus d'activité enregistrés pour cette mission ({{ $taskReports->count() }}) :
                        </div>

                        @if($taskReports->isEmpty())
                            <div style="font-size: 8px; color: #94a3b8; font-style: italic; padding: 2px 0;">
                                Aucun rapport n'a encore été déposé pour cette tâche.
                            </div>
                        @else
                            @foreach($taskReports as $tReport)
                                <div class="report-entry">
                                    <div class="report-entry-header">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="font-weight: bold; font-size: 8.5px; color: #0f172a;">
                                                    Compte-rendu du {{ $tReport->report_date->format('d/m/Y') }}
                                                    @if($tReport->hours_declared > 0)
                                                        <span style="color: #475569; font-weight: normal;">• {{ $tReport->hours_declared }} heure(s)</span>
                                                    @endif
                                                </td>
                                                <td style="text-align: right;">
                                                    <span class="badge">
                                                        {{ $tReport->status === 'submitted' ? 'Soumis' : ($tReport->status === 'reviewed' ? 'Validé' : 'Brouillon') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    @if($tReport->introduction)
                                        <div class="report-field">
                                            <span class="report-field-title">Contexte & Objectifs :</span>
                                            <div>{{ nl2br(e($tReport->introduction)) }}</div>
                                        </div>
                                    @endif

                                    <div class="report-field">
                                        <span class="report-field-title">Travaux Réalisés :</span>
                                        <div>{{ nl2br(e($tReport->summary)) }}</div>
                                    </div>

                                    @if($tReport->blockers)
                                        <div class="report-field" style="color: #991b1b;">
                                            <span class="report-field-title" style="color: #991b1b;">Difficultés & Blocages :</span>
                                            <div>{{ nl2br(e($tReport->blockers)) }}</div>
                                        </div>
                                    @endif

                                    @if($tReport->next_steps)
                                        <div class="report-field" style="color: #065f46;">
                                            <span class="report-field-title" style="color: #065f46;">Prochaines Étapes :</span>
                                            <div>{{ nl2br(e($tReport->next_steps)) }}</div>
                                        </div>
                                    @endif

                                    @if($tReport->latitude && $tReport->longitude)
                                        <div style="font-size: 7.5px; color: #64748b; margin-top: 4px; padding-top: 2px; border-top: 1px dotted #cbd5e1;">
                                            Pointage GPS : {{ number_format($tReport->latitude, 4) }}, {{ number_format($tReport->longitude, 4) }}
                                            @if($tReport->distance_to_site_meters !== null)
                                                • Distance au site : {{ $tReport->distance_to_site_meters }} m
                                            @endif
                                            • Enregistré le {{ $tReport->created_at->format('d/m/Y à H:i') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ── SECTION 3 : ÉVALUATIONS & INSTRUCTIONS DE L'ENCADREMENT ── --}}
    @if($report->reviews && $report->reviews->count() > 0)
    <div class="section">
        <div class="section-header">3. Avis, Évaluations & Recommandations de l'Encadrement</div>
        @foreach($report->reviews as $review)
            @php
                $reviewerPersonnel = $review->reviewer?->personnel;
                $reviewerName = trim(($reviewerPersonnel?->prenom ?? '') . ' ' . ($reviewerPersonnel?->nom ?? $review->reviewer?->name ?? 'Superviseur'));
            @endphp
            <div class="review-box">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
                    <tr>
                        <td style="font-weight: bold; color: #0f172a; font-size: 8px;">
                            Avis émis par : {{ $reviewerName }}
                        </td>
                        <td style="text-align: right; color: #64748b; font-size: 7.5px;">
                            {{ $review->created_at->format('d/m/Y à H:i') }}
                        </td>
                    </tr>
                </table>
                <div style="color: #334155;">{{ nl2br(e($review->comment)) }}</div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- ── SECTION 4 : SIGNATURES ET VISAS OFFICIELS ── --}}
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-frame">
                    <div class="sig-heading">Signature du Collaborateur</div>
                    <div class="sig-space"></div>
                    <div class="sig-caption">{{ $authorFullName }}</div>
                </div>
            </td>
            <td>
                <div class="sig-frame">
                    <div class="sig-heading">Visa & Validation de l'Encadrement</div>
                    <div class="sig-space"></div>
                    <div class="sig-caption">Direction Technique / Ressources Humaines TFG SARL</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── PIED DE PAGE ── --}}
    <div class="doc-footer">
        TECHNOLOGY FOREVER GROUP SARL • Document de suivi d'activité généré le {{ now()->format('d/m/Y à H:i') }} • Réf. #{{ $report->id }}
    </div>

</body>
</html>
