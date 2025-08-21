<x-app-layout>
<div class="bg-gray-100 min-h-screen flex items-center justify-center p-6 space-x-6 font-serif">

    <!-- Badge container -->
    <div id="badge" class="bg-white shadow-xl rounded-2xl p-6 w-80 print:w-80 print:shadow-none border border-gray-200">

        <!-- Header avec couleur légère (sans logo) -->
        <div class="text-center mb-2 bg-blue-200 rounded-t-2xl py-4 border-b border-gray-400">
            <h1 class="text-xl font-bold text-blue-700">TECHNOLOGY FOREVER GROUP (TFG)</h1>
        </div>

        <!-- Logo en bas du header -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/TGFpdf.jpg') }}" alt="TFG Logo"
                 class="mx-auto w-32 h-32 rounded-full object-cover -mt-6">
        </div>

        <!-- Informations stagiaire -->
        <div class="mb-4 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-2 border-b-2 border-gray-200 pb-1">Stagiaire</h2>
            <div class="text-gray-800 space-y-1 mt-2">

                <div class="inline-block  rounded px-4 py-2 mt-2">
                    <span class="text-2xl font-extrabold text-gray-800">
                        {{ str_pad($stagiaire->badge->badge, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p><span class="font-semibold ">Nom :</span> {{ $stagiaire->nom }} {{ $stagiaire->prenom }}</p>
                <p><span class="font-semibold ">École :</span> {{ $stagiaire->ecole }}</p>
                <p><span class="font-semibold">Type de stage :</span> {{ $stagiaire->typestage->libelle }}</p>
                <p><span class="font-semibold">Téléphone :</span> {{ $stagiaire->telephone }}</p>
                <p><span class="font-semibold">Dates :</span> {{ \Carbon\Carbon::parse($stagiaire->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($stagiaire->date_fin)->format('d/m/Y') }}</p>
                <p><span class="font-semibold">Email :</span> {{ $stagiaire->email }}</p>
            </div>
        </div>

        <!-- Footer coloré avec SVG -->
        <div class="mt-6 rounded-b-2xl text-white font-bold p-4" style="background: linear-gradient(to right, #0033a0, #d50000); border-top: 2px solid #0033a0;">
            <div class="flex flex-col items-center space-y-2 text-sm">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zM12 11.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/>
                    </svg>
                    <span>Abomey-Calavi (Togoudo)</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21c1.21.48 2.53.74 3.88.74a1 1 0 011 1v3.5a1 1 0 01-1 1C10.07 22 2 13.93 2 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.26 2.67.74 3.88a1 1 0 01-.21 1.11l-2.2 2.2z"/>
                    </svg>
                    <span>0166439030 / 0169580603</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4a8 8 0 100 16 8 8 0 000-16zm-1 14.93A6.001 6.001 0 016.07 13H11v5.93zM13 19.93V13h4.93a6.001 6.001 0 01-4.93 6.93zM11 11V6.07A6.001 6.001 0 0117.93 11H11z"/>
                    </svg>
                    <span>www.tfgbusiness.com</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Bouton Imprimer à côté -->
    <div class="self-start mt-10">
        <button onclick="window.print()"
            class="px-6 py-3 rounded-md bg-blue-500 text-white font-bold hover:bg-blue-600 transition">
            Imprimer le badge
        </button>
    </div>

</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #badge, #badge * {
        visibility: visible;
    }
    #badge {
        position: absolute;
        left: 50%;
        top: 20px;
        transform: translateX(-50%);
        width: 350px; /* taille fixe pour plastification */
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    @page {
        margin: 0;
    }
}

</style>
</x-app-layout>
