<x-app-layout>
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-6">

            {{-- Breadcrumbs --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Profil de l'étudiant</h1>
                    <p class="text-sm text-gray-500">
                        <a href="{{ route('stages.index') }}" class="hover:underline">Stages</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-700">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</span>
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('stages.index') }}" 
                       class="px-4 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">
                        ← Retour
                    </a>

                   

                    @if($stage->badge)
                        <a href="{{ route('admin.stages.badge.show', $stage->id) }}" 
                           class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">
                            🎫 Voir badge
                        </a>
                    @endif
                   <!-- Bouton pour attestation -->
                    <button onclick="document.getElementById('modalAttestation').classList.remove('hidden')"
                            class="px-4 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700">
                        Générer attestation
                    </button>

                    <!-- Modal  pour attestation-->
                    <div id="modalAttestation" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                        <div class="bg-white p-6 rounded-lg w-1/2">
                            <h2 class="text-lg font-bold mb-4">Choisir les signataires</h2>

                            <form method="POST" action="{{ route('stages.attestation.store', $stage->id) }}">
                                @csrf
                       @foreach($signataires as $signataire)
    <div class="flex items-center mb-2">
        <input type="checkbox"
            name="signataires[{{ $signataire->id }}][selected]"
            value="1"
            class="mr-2 signataire-checkbox"
            id="sign_{{ $signataire->id }}"
            data-ordre="ordre_{{ $signataire->id }}"
            data-parordre="parordre_{{ $signataire->id }}">

        <label for="sign_{{ $signataire->id }}" class="mr-4">
            {{ $signataire->nom }} ({{ $signataire->poste }})
        </label>

        {{-- L’ordre ne s’affiche pas pour le DG --}}
        @if(!$signataire->isDG() && $signataire->peut_par_ordre)
            <input type="number"
                name="signataires[{{ $signataire->id }}][ordre]"
                min="1" max="10"
                placeholder="Ordre"
                class="border px-2 py-1 w-16 ordre-input"
                id="ordre_{{ $signataire->id }}"
                disabled>

            <!-- Case à cocher pour dire "Par ordre du DG" -->
            <input type="checkbox"
                name="signataires[{{ $signataire->id }}][par_ordre]"
                value="1"
                id="parordre_{{ $signataire->id }}"
                class="ml-2"
                disabled>
            <span class="ml-2 text-sm text-gray-500">Signer P.O DG</span>
        @endif
    </div>
@endforeach


                                <div class="mt-4 text-right">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Valider</button>
                                    <button type="button" onclick="document.getElementById('modalAttestation').classList.add('hidden')"
                                            class="ml-2 px-4 py-2 border rounded">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>
            </div>

            {{-- Profil complet --}}
            <div class="bg-white rounded-lg shadow p-6">
                {{-- En-tête --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 border-b border-gray-200 pb-6">
                    <div class="h-28 w-28 rounded-full overflow-hidden shadow flex-shrink-0">
                        <img src="{{ asset('images/TFGLOGO.png') }}" alt="Photo étudiant" class="h-full w-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</h2>
                        <p class="text-gray-500">{{ $stage->etudiant->ecole }}</p>
                        <p class="text-gray-500">{{ $stage->etudiant->adresse ?? 'Adresse non renseignée' }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        @if($statutEnCours == 'En cours')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-green-100 text-green-700">● En cours</span>
                        @elseif($statutEnCours == 'À venir')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-700">● À venir</span>
                        @else
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-red-100 text-red-700">● Terminé</span>
                        @endif
                    </div>
                </div>

                {{-- Infos principales --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Email</p>
                        <p class="text-gray-800 font-medium">{{ $stage->etudiant->email }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Téléphone</p>
                        <p class="text-gray-800 font-medium">{{ $stage->etudiant->telephone }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Type de stage</p>
                        <p class="text-gray-800 font-medium">{{ $stage->typestage->libelle ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Badge</p>
                        <p class="text-gray-800 font-medium">{{ $stage->badge->badge ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Thème</p>
                        <p class="text-gray-800 font-medium">{{ $stage->theme }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Période</p>
                        <p class="text-gray-800 font-medium">{{ $stage->date_debut?->format('d/m/Y') ?? '—' }} à {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Service</p>
                        <p class="text-gray-800 font-medium">{{ $stage->service->nom ?? 'Aucun' }}</p>
                        <p class="text-sm text-gray-600">Responsable : {{ $stage->service->responsable ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-xs uppercase text-gray-500">Notes / Observations</p>
                        <p class="text-gray-800 font-medium">{{ $stage->etudiant->notes ?? '—' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    document.querySelectorAll('.signataire-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const ordreInputId = this.dataset.ordre;
        const parOrdreId = this.dataset.parordre;

        const ordreInput = document.getElementById(ordreInputId);
        const parOrdreInput = document.getElementById(parOrdreId);

        if (ordreInput) {
            ordreInput.disabled = !this.checked;
        }
        if (parOrdreInput) {
            parOrdreInput.disabled = !this.checked;
        }
    });
});

</script>
</x-app-layout>
