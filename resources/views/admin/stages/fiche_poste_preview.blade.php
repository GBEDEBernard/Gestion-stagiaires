<x-app-layout>

    {{-- ============================================================
         EN-TÊTE DE LA PAGE
    ============================================================= --}}

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">

        {{-- Titre + retour --}}
        <div class="flex items-center gap-4">

            {{-- Retour --}}
            <a href="{{ encrypted_route('stages.show', $stage) }}"
                class="p-2.5 bg-gray-100 dark:bg-gray-700
                       text-gray-600 dark:text-gray-300
                       rounded-xl
                       hover:bg-gray-200 dark:hover:bg-gray-600
                       transition shadow-sm"
                title="Retour">

                <svg class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

            </a>


            {{-- Informations --}}
            <div>

                <h1 class="text-2xl lg:text-3xl font-bold
                           text-gray-900 dark:text-white">
                    Fiche de poste
                </h1>

                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">

                    {{ $stage->etudiant->personnel->nom ?? '' }}
                    {{ $stage->etudiant->personnel->prenom ?? '' }}

                </p>

            </div>

        </div>


        {{-- ========================================================
             BOUTONS D'ACTION
        ========================================================= --}}

        <div class="flex flex-wrap gap-2.5">


            {{-- ----------------------------------------------------
                 MODIFIER
            ----------------------------------------------------- --}}

            <a href="{{ encrypted_route('stages.fiche-poste.edit', $stage) }}"
                class="inline-flex items-center gap-2
                       px-4 py-2.5
                       rounded-xl
                       bg-amber-100 dark:bg-amber-900/30
                       text-amber-700 dark:text-amber-400
                       font-medium
                       hover:bg-amber-200 dark:hover:bg-amber-900/50
                       transition shadow-sm">

                <svg class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />

                </svg>

                Modifier les informations

            </a>


            {{-- ----------------------------------------------------
                 TÉLÉCHARGER PDF
                 
                 IMPORTANT :
                 On utilise window.print() afin de conserver
                 exactement le rendu A4 défini dans
                 fiche_poste_pdf.blade.php.
            ----------------------------------------------------- --}}

            <button
                type="button"
                onclick="downloadFichePostePDF()"
                class="inline-flex items-center gap-2
                       px-4 py-2.5
                       rounded-xl
                       bg-emerald-100 dark:bg-emerald-900/30
                       text-emerald-700 dark:text-emerald-400
                       font-medium
                       hover:bg-emerald-200 dark:hover:bg-emerald-900/50
                       transition shadow-sm"
                title="Télécharger la fiche au format PDF">

                {{-- Icône téléchargement --}}
                <svg class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14" />

                </svg>

                Télécharger PDF

            </button>


            {{-- ----------------------------------------------------
                 IMPRIMER
            ----------------------------------------------------- --}}

            <button
                type="button"
                onclick="printFichePoste()"
                class="inline-flex items-center gap-2
                       px-4 py-2.5
                       rounded-xl
                       bg-blue-100 dark:bg-blue-900/30
                       text-blue-700 dark:text-blue-400
                       font-medium
                       hover:bg-blue-200 dark:hover:bg-blue-900/50
                       transition shadow-sm"
                title="Imprimer la fiche">

                <svg class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4h2m2 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm6-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />

                </svg>

                Imprimer

            </button>

        </div>

    </div>


    {{-- ============================================================
         APERÇU DE LA FICHE
    ============================================================= --}}

    <div class="overflow-x-auto pb-4">

        <div class="min-w-[210mm]">

            @include(
                'admin.stages.fiche_poste_pdf',
                [
                    'stage' => $stage,
                    'embedded' => true
                ]
            )

        </div>

    </div>


    {{-- ============================================================
         SCRIPT IMPRESSION / PDF
    ============================================================= --}}

    <script>

        /**
         * ----------------------------------------------------------
         * IMPRESSION
         * ----------------------------------------------------------
         *
         * Cette fonction utilise les règles @media print
         * déjà présentes dans fiche_poste_pdf.blade.php.
         *
         * Cela permet de conserver :
         * - le format A4
         * - les marges
         * - les sauts de page
         * - les tableaux
         * - les espacements
         * - le header
         * - le footer
         */
        function printFichePoste() {

            window.print();

        }


        /**
         * ----------------------------------------------------------
         * TÉLÉCHARGEMENT PDF
         * ----------------------------------------------------------
         *
         * On ouvre la boîte d'impression du navigateur.
         *
         * Dans Chrome :
         *
         * Destination
         *      → Fichier PDF
         *
         * puis :
         *
         *      → Enregistrer
         *
         * Le PDF généré utilise exactement le rendu de la fiche
         * d'impression.
         */
        function downloadFichePostePDF() {

            window.print();

        }

    </script>


</x-app-layout>