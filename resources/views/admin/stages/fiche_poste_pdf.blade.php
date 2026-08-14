<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de poste – {{ $stage->etudiant->personnel->nom ?? 'Stagiaire' }}</title>
    <style>
        @if(isset($isPdf))
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @elseif(empty($embedded))
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @else
        .sheet, .sheet * { margin: 0; padding: 0; box-sizing: border-box; }
        @endif

        /* ===== FORMAT A4 ===== */
        @if(!isset($isPdf))
        @page {
            margin: 0;
            size: A4;
        }
        @endif

        @if(!isset($isPdf))
        @if(empty($embedded))
        html, body {
            background: #e2e8f0;
            display: flex;
            justify-content: center;
            padding: 24px 0;
        }
        @endif
        .sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        }
        .sheet-inner { padding: 15mm; }
        @else
        .sheet { width: 100%; }
        .sheet-inner { padding: 0; }
        @endif

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', sans-serif;
            color: #1e293b;
            font-size: 11.5px;
            line-height: 1.4;
        }

        /* ---- En-tête ---- */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 4px solid #b90912da;
            padding-bottom: 6px;
            margin-bottom: 4px;
        }
        .header img {
            width: 46px;
            height: 46px;
            margin-right: 14px;
            border-radius: 12px;
        }
        .text-header {
            flex: 1;
            text-align: center;
        }
        .text-header h1 {
            font-size: 18px;
            color: #3c57a1;
            margin: 2px;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-weight: 700;
        }
        .text-header .i {
            font-size: 10.5px;
            color: #ff0303;
            font-weight: 600;
            opacity: 0.75;
            display: block;
            margin-bottom: 3px;
            margin-top: 3px;
        }
        .text-header .p1 {
            font-size: 9px;
            font-weight: 600;
            margin: 2px 0;
        }
        .header-contact {
            text-align: center;
            font-size: 8px;
            color: #475569;
            margin-top: 3px;
            margin-bottom: 3px;
            font-weight: 600;
        }

        h2.titre-principal {
            text-align: center;
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #003c6a;
        }
        .sous-titre {
            text-align: center;
            font-size: 11.5px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .sous-titre2 {
            text-align: center;
            font-size: 10px;
            color: #475569;
            margin-bottom: 4px;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9px;
        }
        table.info td {
            padding: 1px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        table.info tr {
            page-break-inside: avoid;
        }
        table.info td:first-child {
            width: 32%;
            font-weight: 600;
            background: #f8fafc;
        }
        table.info td:first-child strong {
            color: #003c6a;
        }

        .section {
            margin: 5px 0 7px 0;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #003c6a;
            margin-bottom: 2px;
            page-break-after: avoid;
        }
        .section-hr {
            border: none;
            border-top: 2.5px solid #003c6a;
            margin: 2px 0 4px 0;
            opacity: 0.85;
        }
        .content {
            font-size: 10.5px;
            line-height: 1.35;
        }
        .content ul {
            padding-left: 18px;
            margin: 2px 0;
        }
        .content ul li {
            margin-bottom: 1px;
        }
        .content p {
            margin: 2px 0;
        }

        .footer {
            margin-top: 18px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10px;
            color: #171718;
            page-break-inside: avoid;
        }
        .footer-left {
            font-size: 10px;
            color: #475569;
        }
        .footer-right {
            text-align: center;
            font-size: 11px;
            font-weight: 600;
        }
        .footer-right .fr-line {
            display: inline-block;
            min-width: 130px;
            border-bottom: 1px solid #334155;
            margin-left: 4px;
        }

        /* ---- Impression ---- */
        @if(!isset($isPdf))
        @media print {
            body {
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .sheet {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                box-shadow: none !important;
                border: none !important;
                background: #ffffff;
            }
            .sheet-inner {
                padding: 15mm !important;
            }
            /* Masquer le chrome du layout admin à l'impression */
            nav, header, #loader-overlay {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
            main {
                padding: 0 !important;
            }
            /* Supprimer tout filigrane éventuel */
            .tfg-bg, .tfg-watermark, .watermark, .a4-container::before {
                display: none !important;
                opacity: 0 !important;
            }
        }
        @endif
    </style>
</head>
<body>

    @php
    // Gestion du logo en base64 pour le PDF
    if (!isset($logoDataUri)) {
        $logoPath = public_path('images/TFGLOGO.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : '';
    }
    @endphp

    <div class="sheet">
        <div class="sheet-inner">

            <div class="header">
                <img src="{{ $logoDataUri }}" alt="Logo">
                <div class="text-header">
                    <h1>TECHNOLOGY FOREVER GROUP SARL</h1>
                    <p class="i"><span>***</span> La Technologie au service du développement <span>***</span></p>
                    <p class="p1">
                        Informatique – Télécommunications – BTP – Énergie – Électricité – Formations – Commerce Général – Fournitures – Import-Export & Divers
                    </p>
                </div>
            </div>
            <p class="header-contact">
                {{ $stage->site->name ?? '' }}@if($stage->site?->address) – {{ $stage->site->address }}@endif
                @if($stage->site?->phone) – Tél : {{ $stage->site->phone }}@endif
                – Email : {{ config('mail.from.address') }}
            </p>

            <h2 class="titre-principal">FICHE DE POSTE</h2>
            <p class="sous-titre">{{ $stage->intitule_poste ?? \App\Models\Stage::DEFAULT_INTITULE_POSTE }}</p>
            <p class="sous-titre2">(Stage de fin de formation – avec dépôt et soutenance de rapport)</p>

            <table class="info">
                <tr><td><strong>Intitulé du poste</strong></td><td>{{ $stage->intitule_poste ?? \App\Models\Stage::DEFAULT_INTITULE_POSTE }}</td></tr>
                <tr><td><strong>Type de stage</strong></td><td>{{ $stage->fichePosteTypeStage() }}</td></tr>
                <tr><td><strong>Établissement d'origine</strong></td><td>{{ $stage->etudiant->ecole ?? '[Nom de l\'université / institut]' }}</td></tr>
                <tr><td><strong>Filière / Niveau d'étude</strong></td><td>{{ trim(($stage->filiere ?? '') . ' ' . ($stage->niveau_etude ?? '')) ?: '[Ex : Licence 3 / Master]' }}</td></tr>
                <tr><td><strong>Service / Département d'accueil</strong></td><td>{{ $stage->domaine->nom ?? 'Service informatique / Développement' }}</td></tr>
                <tr><td><strong>Lieu de travail</strong></td><td>{{ $stage->site->name ?? 'TFG SARL' }}@if($stage->site?->city) – {{ $stage->site->city }}@endif</td></tr>
                <tr><td><strong>Durée du stage</strong></td><td>Du {{ $stage->date_debut?->format('d/m/Y') ?? 'jj/mm/aaaa' }} au {{ $stage->date_fin?->format('d/m/Y') ?? 'jj/mm/aaaa' }}</td></tr>
                <tr><td><strong>Maître de stage (entreprise)</strong></td><td>{{ trim(($stage->supervisor?->personnel?->nom ?? '') . ' ' . ($stage->supervisor?->personnel?->prenom ?? '')) }}{{ $stage->supervisor?->fonction ? ' – ' . $stage->supervisor->fonction : '' }}</td></tr>
                <tr><td><strong>Tuteur académique</strong></td><td>{{ $stage->tuteur_academique ?? '[Nom Prénom – Établissement]' }}</td></tr>
                <tr><td><strong>Thème du stage</strong></td><td>{{ $stage->theme ?? '[Intitulé du sujet]' }}</td></tr>
                <tr><td><strong>Livrable attendu</strong></td><td>{{ !empty($stage->livrables) ? implode(' + ', $stage->livrables) : 'Rapport de stage à déposer + soutenance orale devant jury' }}</td></tr>
                <tr><td><strong>Indemnité de stage</strong></td><td>{{ $stage->indemnite ?? \App\Models\Stage::DEFAULT_INDEMNITE }}</td></tr>
            </table>

            <div class="section">
                <div class="section-title">1. Mission du poste</div>
                <hr class="section-hr">
                <div class="content">
                    <p>Le/la stagiaire académique intègre l'équipe de développement afin de mettre en pratique les connaissances acquises durant sa formation. Le stage doit lui permettre de mener une mission concrète en entreprise, d'acquérir une expérience professionnelle significative, et de rédiger un rapport de stage répondant aux exigences académiques de son établissement, en vue d'une soutenance devant jury.</p>
                </div>
            </div>

            <div class="section">
                <div class="section-title">2. Activités et responsabilités principales</div>
                <hr class="section-hr">
                <div class="content">
                    <ul>
                        <li>Participer au développement de fonctionnalités front-end et/ou back-end sur les projets en cours</li>
                        <li>Contribuer à la conception, aux tests et à la correction d'anomalies (bugs)</li>
                        <li>Documenter les travaux réalisés au fil du stage (pour alimenter le futur rapport)</li>
                        <li>Participer aux réunions d'équipe et rendre compte régulièrement de son avancement</li>
                        <li>Mener, en lien avec le thème retenu, une analyse ou une réalisation technique servant de base au rapport de stage</li>
                        <li>Préparer la soutenance : synthèse du travail effectué, résultats obtenus, difficultés rencontrées et solutions apportées</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <div class="section-title">3. Profil recherché</div>
                <hr class="section-hr">
                <div class="content">
                    <p><strong>Formation :</strong></p>
                    <ul>
                        <li>Étudiant(e) inscrit(e) dans un établissement académique, filière informatique / développement web ou équivalent</li>
                        <li>Stage devant obligatoirement se conclure par le dépôt d'un rapport et une soutenance</li>
                    </ul>
                    <p><strong>Compétences techniques :</strong></p>
                    <ul>
                        <li>Notions de HTML, CSS, JavaScript</li>
                        <li>Connaissance d'au moins un langage back-end (PHP, Python, Node.js…) appréciée</li>
                        <li>Bases de Git et du travail collaboratif en versionnage de code</li>
                    </ul>
                    <p><strong>Qualités personnelles :</strong></p>
                    <ul>
                        <li>Curiosité, autonomie, capacité de synthèse et de rédaction</li>
                        <li>Rigueur dans le suivi et la documentation de son travail</li>
                        <li>Esprit d'équipe et bonne communication</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <div class="section-title">4. Encadrement et suivi académique</div>
                <hr class="section-hr">
                <div class="content">
                    <ul>
                        <li>Un maître de stage est désigné au sein de l'entreprise pour l'encadrement quotidien du/de la stagiaire</li>
                        <li>Un tuteur académique, rattaché à l'établissement d'origine, assure le suivi pédagogique et valide le sujet du rapport</li>
                        <li>Des points d'étape réguliers sont organisés pour évaluer l'avancement de la mission et du rapport</li>
                        <li>L'entreprise fournit au/à la stagiaire les informations nécessaires à la rédaction du rapport, dans le respect de la confidentialité</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <div class="section-title">5. Conditions de travail</div>
                <hr class="section-hr">
                <div class="content">
                    <ul>
                        <li>Horaires : Temps plein, du lundi au vendredi</li>
                        <li>Outils / équipements : ordinateur (SE : Linux), accès aux outils de développement et à l'environnement de test</li>
                        <li>Le stagiaire doit marquer sa présence et faire connaître ses activités via la plateforme de suivi qu'offre la structure. Les paramètres pour l'accès à cette plateforme sont communiqués au stagiaire à sa présentation et sont automatiquement désactivés à la fin de son stage.</li>
                        <li>Temps dédié à la rédaction du rapport aménagé en fin de stage, en accord avec le maître de stage</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <div class="section-title">6. Modalités de fin de stage</div>
                <hr class="section-hr">
                <div class="content">
                    <ul>
                        <li>Dépôt du rapport de stage sur la plateforme de présence et de suivi de {{ config('app.name', 'TFG SARL') }} et auprès de l'établissement académique, selon le calendrier fixé par celui-ci</li>
                        <li>Soutenance orale devant un jury académique (et, le cas échéant, en présence du maître de stage. Elle est facultative)</li>
                        <li>Un rapport de stage issu de la plateforme est transmis à son école de provenance</li>
                        <li>Remise d'une attestation de stage et, sur demande, d'une fiche d'évaluation du/de la stagiaire par l'entreprise</li>
                    </ul>
                </div>
            </div>

            <div class="footer">
                <div class="footer-left">Date de mise à jour de la fiche : {{ now()->format('d/m/Y') }}</div>
                <div class="footer-right">
                    <div>Date de signature</div>
                    <div class="fr-line"></div>
                    <div>Signature</div>
                    <div>Lu et approuvé</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================================================================
         SUPPRESSION DES BOUTONS FLOTTANTS (Retour / Imprimer)
         Ils sont déjà présents dans le layout parent (x-app-layout).
         ================================================================ --}}

</body>
</html>