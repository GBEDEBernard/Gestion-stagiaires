
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature d'attestation - TFG SARL</title>

    <style>
        body{
            margin:0;
            padding:0;
            background:#f4f7fb;
            font-family:Arial, Helvetica, sans-serif;
            color:#1e293b;
        }

        table{
            border-spacing:0;
            width:100%;
        }

        td{
            padding:0;
        }

        img{
            border:0;
            display:block;
        }

        .wrapper{
            width:100%;
            table-layout:fixed;
            background:#f4f7fb;
            padding:30px 10px;
        }

        .main{
            background:#ffffff;
            margin:0 auto;
            width:100%;
            max-width:620px;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 5px 25px rgba(0,0,0,0.06);
        }

        .header{
            background:linear-gradient(135deg,#0f172a,#2563eb);
            padding:35px 20px;
            text-align:center;
        }

        .logo{
            width:90px;
            height:90px;
            margin:0 auto 15px;
            border-radius:50%;
            background:#fff;
            padding:10px;
        }

        .title{
            color:#ffffff;
            font-size:28px;
            font-weight:bold;
            margin-bottom:10px;
        }

        .subtitle{
            color:rgba(255,255,255,0.85);
            font-size:15px;
        }

        .content{
            padding:35px 28px;
        }

        .hello{
            font-size:22px;
            font-weight:bold;
            margin-bottom:18px;
            color:#0f172a;
        }

        .text{
            font-size:15px;
            line-height:1.8;
            color:#475569;
            margin-bottom:25px;
        }

        .card{
            background:#f8fafc;
            border:1px solid #e2e8f0;
            border-radius:18px;
            padding:22px;
            margin-top:20px;
        }

        .card-title{
            font-size:17px;
            font-weight:bold;
            margin-bottom:20px;
            color:#0f172a;
        }

        .info{
            margin-bottom:14px;
        }

        .label{
            font-size:13px;
            color:#64748b;
            margin-bottom:5px;
            font-weight:bold;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }

        .value{
            font-size:15px;
            color:#0f172a;
            line-height:1.6;
        }

        .signature-box{
            margin-top:30px;
            background:#eff6ff;
            border-left:4px solid #2563eb;
            border-radius:12px;
            padding:18px;
        }

        .signature-box p{
            margin:0;
            font-size:14px;
            line-height:1.7;
            color:#1e40af;
        }

        .footer{
            background:#f8fafc;
            padding:25px 20px;
            text-align:center;
            border-top:1px solid #e2e8f0;
        }

        .footer-company{
            font-size:15px;
            font-weight:bold;
            color:#0f172a;
            margin-bottom:10px;
        }

        .footer-text{
            font-size:13px;
            color:#64748b;
            line-height:1.8;
        }

        .link-btn{
            display:inline-block;
            margin-top:18px;
            background:#2563eb;
            color:#ffffff !important;
            text-decoration:none;
            padding:14px 28px;
            border-radius:10px;
            font-size:15px;
            font-weight:bold;
        }

        @media screen and (max-width:600px){

            .content{
                padding:25px 18px !important;
            }

            .title{
                font-size:23px !important;
            }

            .hello{
                font-size:20px !important;
            }

            .card{
                padding:18px !important;
            }

            .link-btn{
                display:block !important;
                width:100% !important;
                text-align:center !important;
                box-sizing:border-box;
            }
        }

    </style>
</head>

<body>

    <div class="wrapper">

        <div class="main">

            <!-- HEADER -->
            <div class="header">

                <img
                    src="{{ asset('images/logo-tfg.png') }}"
                    alt="TFG SARL"
                    class="logo"
                >

                <div class="title">
                    Signature d'attestation
                </div>

                <div class="subtitle">
                    Technology Forever SARL
                </div>

            </div>

            <!-- CONTENT -->
            <div class="content">

                <div class="hello">
                    Bonjour {{ $signer->name }},
                </div>

                <div class="text">
                    Une attestation de stage nécessite votre signature.

                    Veuillez consulter les informations ci-dessous avant de procéder à la signature officielle du document.
                </div>

                <!-- CARD -->
                <div class="card">

                    <div class="card-title">
                        Informations du stagiaire
                    </div>

                    <div class="info">
                        <div class="label">Stagiaire</div>
                        <div class="value">
                            {{ $stage->etudiant->personnel->nom ?? '' }}
                            {{ $stage->etudiant->personnel->prenom ?? '' }}
                        </div>
                    </div>

                    <div class="info">
                        <div class="label">Établissement</div>
                        <div class="value">
                            {{ $stage->etudiant->ecole ?? 'Non renseigné' }}
                        </div>
                    </div>

                    <div class="info">
                        <div class="label">Période de stage</div>
                        <div class="value">
                            {{ $stage->date_debut->format('d/m/Y') }}
                            au
                            {{ $stage->date_fin->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="info">
                        <div class="label"> Domaine</div>
                        <div class="value">
                            {{ $stage->domaine->nom ?? 'Non défini' }}
                        </div>
                    </div>

                    <div class="info">
                        <div class="label">Thème</div>
                        <div class="value">
                            {{ $stage->theme ?? 'Non défini' }}
                        </div>
                    </div>

                    @if($stage->typestage)

                    <div class="info">
                        <div class="label">Type de stage</div>
                        <div class="value">
                            {{ $stage->typestage->libelle ?? '' }}
                        </div>
                    </div>

                    @endif

                </div>

              

            </div>

            <!-- FOOTER -->
            <div class="footer">

                <div class="footer-company">
                    Technology Forever SARL (TFG SARL)
                </div>

                <div class="footer-text">
                    M/ GAUTHE Gabriel - Allègléta | Godomey-Togoudo (Abomey-Calavi)
                    <br>
                    (+229) 01 65 10 39 59 / 01 69 58 06 03
                    <br>
                    contact@tfg.bj
                    <br><br>

                    Ce lien expire dans 72 heures pour des raisons de sécurité.
                    <br>

                    © {{ date('Y') }} TFG SARL - Tous droits réservés.
                </div>

            </div>

        </div>

    </div>

</body>
</html>
