<x-app-layout>
    {{-- En-tête --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ encrypted_route('stages.show', $stage) }}"
                class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Fiche de poste
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $stage->etudiant->personnel->nom ?? '' }} {{ $stage->etudiant->personnel->prenom ?? '' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2.5">
            {{-- Bouton Modifier les informations --}}
            <a href="{{ encrypted_route('stages.fiche-poste.edit', $stage) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-medium hover:bg-amber-200 dark:hover:bg-amber-900/50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier les informations
            </a>

            

            {{-- Bouton Imprimer : ouvre directement la boîte de dialogue
                 d'impression du navigateur. Le @media print de la fiche ne
                 garde que la feuille A4 (sans la barre du haut ni le menu). --}}
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimer
            </button>
        </div>
    </div>

    {{-- Aperçu : la fiche est embarquée dans le layout admin (mode "embedded") :
         le partial n'applique alors ni reset global ni styles html/body, pour ne
         pas casser la structure du layout. La feuille A4 garde son ombre et son
         fond blanc, et devient scrollable en largeur si l'écran est étroit. --}}
    <div class="overflow-x-auto pb-4">
        <div class="min-w-[210mm]">
            @include('admin.stages.fiche_poste_pdf', ['stage' => $stage, 'embedded' => true])
        </div>
    </div>
</x-app-layout>