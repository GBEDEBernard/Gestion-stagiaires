<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de poste – {{ $stage->etudiant->personnel->nom ?? 'Stagiaire' }}</title>
    <style>
        /* ===== COMPACT : tient sur 2 pages A4 ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', sans-serif;
            margin: 15px 20px;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.4;
            padding: 10px;
        }

        .header {
            text-align: left;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;  
            margin: 0;
            color: #003c6a;
             
        }
        .header p {
            font-size: 11px;
            color: #475569;
            margin: 2px 0;
        }

        h2.titre-principal {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #003c6a;
        }
        .sous-titre {
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .sous-titre2 {
            text-align: center;
            font-size: 12px;
            color: #475569;
            margin-bottom: 12px;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 11.5px;
        }
        table.info td {
            padding: 3px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
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
            margin: 8px 0;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #003c6a;
            margin-bottom: 2px;
        }
        /* ---- HR plus épais et visible ---- */
        .section-hr {
            border: none;
            border-top: 2.5px solid #003c6a;   /* ← plus épais */
            margin: 4px 0 8px 0;
            opacity: 0.85;                      /* ← un peu plus opaque */
        }
        .content {
            font-size: 11.5px;
            line-height: 1.4;
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
            margin-top: 15px;
            font-size: 12px;
            color: #171718;
            text-align: left;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name', 'TFG SARL') }}</h1>
        <p>{{ $stage->site->address ?? '' }} – Tél : {{ $stage->site->phone ?? '' }} – Email : {{ config('mail.from.address') }}</p>
    </div>

    <h2 class="titre-principal">FICHE DE POSTE</h2>
    <p class="sous-titre">Stagiaire Académique en Développement Web</p>
    <p class="sous-titre2">(Stage de fin de formation – avec dépôt et soutenance de rapport)</p>

    <table class="info">
        <tr><td><strong>Intitulé du poste</strong></td><td>Stagiaire académique en développement web</td></tr>
        <tr><td><strong>Type de stage</strong></td><td>Stage académique de fin de formation (obligatoire pour la validation du diplôme)</td></tr>
        <tr><td><strong>Établissement d'origine</strong></td><td>{{ $stage->etudiant->ecole ?? '[Nom de l\'université / institut]' }}</td></tr>
        <tr><td><strong>Filière / Niveau d'étude</strong></td><td>{{ $stage->filiere ?? '[Ex : Licence 3 / Master]' }}</td></tr>
        <tr><td><strong>Service / Département d'accueil</strong></td><td>Service informatique / Développement</td></tr>
        <tr><td><strong>Lieu de travail</strong></td><td>{{ $stage->site->city ?? 'Ville' }} – {{ $stage->site->address ?? 'Adresse' }}</td></tr>
        <tr><td><strong>Durée du stage</strong></td><td>Du {{ $stage->date_debut?->format('d/m/Y') ?? 'jj/mm/aaaa' }} au {{ $stage->date_fin?->format('d/m/Y') ?? 'jj/mm/aaaa' }}</td></tr>
        <tr><td><strong>Maître de stage (entreprise)</strong></td><td>{{ $stage->supervisor->personnel->nom ?? '' }} {{ $stage->supervisor->personnel->prenom ?? '' }} – {{ $stage->supervisor->fonction ?? 'Fonction' }}</td></tr>
        <tr><td><strong>Tuteur académique</strong></td><td>{{ $stage->tuteur_nom ?? '[Nom Prénom – Établissement]' }}</td></tr>
        <tr><td><strong>Thème du stage</strong></td><td>{{ $stage->theme ?? '[Intitulé du sujet]' }}</td></tr>
        <tr><td><strong>Livrable attendu</strong></td><td>Rapport de stage à déposer + soutenance orale devant jury</td></tr>
        <tr><td><strong>Indemnité de stage</strong></td><td>{{ $stage->indemnite ?? '[Montant selon la réglementation]' }}</td></tr>
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
                <li>Horaires : {{ $stage->horaires ?? 'Temps plein, du lundi au vendredi' }}</li>
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
        Date de mise à jour de la fiche : {{ now()->format('d/m/Y') }}
    </div>

</body>
</html>