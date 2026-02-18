<x-app-layout>
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-6">

            {{-- Header + Breadcrumbs --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Profil de l'étudiant</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        <a href="{{ route('stages.index') }}" class="text-blue-600 hover:underline">Stages</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-800 font-medium">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</span>
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('stages.index') }}" 
                       class="px-4 py-2 rounded-lg bg-gray-600 text-white font-medium shadow hover:bg-gray-700 transition">
                        ← Retour
                    </a>

                    @if($stage->badge)
                        <a href="{{ encrypted_route('admin.stages.badge.show', $stage) }}" 
                           class="px-4 py-2 rounded-lg bg-green-600 text-white font-medium shadow hover:bg-green-700 transition">
                            🎫 Voir badge
                        </a>
                    @endif

                    <button onclick="document.getElementById('modalAttestation').classList.remove('hidden')"
                            class="px-4 py-2 rounded-lg bg-purple-600 text-white font-medium shadow hover:bg-purple-700 transition">
                        Générer attestation
                    </button>
                </div>
            </div>

            {{-- Modal Attestation --}}
            <div id="modalAttestation" 
                 class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Choisir les signataires</h2>

                    <form method="POST" action="{{ encrypted_route('stages.attestation.store', $stage) }}" class="space-y-4">
                        @csrf

                        @foreach($signataires as $signataire)
                            <div class="flex items-center gap-3 border-b border-gray-100 pb-2">
                                <input type="checkbox"
                                    name="signataires[{{ $signataire->id }}][selected]"
                                    value="1"
                                    class="signataire-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    id="sign_{{ $signataire->id }}"
                                    data-ordre="ordre_{{ $signataire->id }}"
                                    data-parordre="parordre_{{ $signataire->id }}">

                                <label for="sign_{{ $signataire->id }}" class="flex-1 text-gray-700">
                                    {{ $signataire->nom }} <span class="text-sm text-gray-500">({{ $signataire->poste }})</span>
                                </label>

                                {{-- Ordre sauf DG --}}
                                @if(!$signataire->isDG() && $signataire->peut_par_ordre)
                                    <input type="number"
                                        name="signataires[{{ $signataire->id }}][ordre]"
                                        min="1" max="2"
                                        placeholder="Ordre"
                                        class="border px-2 py-1 w-16 rounded-md text-sm text-black focus:ring focus:ring-blue-200"
                                        id="ordre_{{ $signataire->id }}"
                                        disabled>

                                    <div class="flex items-center text-sm text-gray-600 ml-2">
                                        <input type="checkbox"
                                            name="signataires[{{ $signataire->id }}][par_ordre]"
                                            value="1"
                                            id="parordre_{{ $signataire->id }}"
                                            class="ml-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            disabled>
                                        <span class="ml-1">P.O DG</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" 
                                    onclick="document.getElementById('modalAttestation').classList.add('hidden')"
                                    class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100 transition">
                                Annuler
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium shadow hover:bg-blue-700 transition">
                                Valider
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Profil complet --}}
            <div class="bg-white rounded-xl shadow p-6">
                {{-- En-tête --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 border-b border-gray-200 pb-6">
                    <div class="h-28 w-28 rounded-full overflow-hidden shadow-md flex-shrink-0 ring-2 ring-gray-200">
                        <img src="{{ asset('images/TFGLOGO.png') }}" alt="Photo étudiant" class="h-full w-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</h2>
                        <p class="text-gray-600">{{ $stage->etudiant->ecole }}</p>
                        <p class="text-gray-500">{{ $stage->etudiant->adresse ?? 'Adresse non renseignée' }}</p>
                    </div>
                    <div>
                        @if($statutEnCours == 'En cours')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold bg-green-100 text-green-700">● En cours</span>
                        @elseif($statutEnCours == 'À venir')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold bg-yellow-100 text-yellow-700">● À venir</span>
                        @else
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold bg-red-100 text-red-700">● Terminé</span>
                        @endif
                    </div>
                </div>

                {{-- Infos principales --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Email</p>
                        <p class="text-gray-800 font-medium">{{ $stage->etudiant->email }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Téléphone</p>
                        <p class="text-gray-800 font-medium">{{ $stage->etudiant->telephone }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Type de stage</p>
                        <p class="text-gray-800 font-medium">{{ $stage->typestage->libelle ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Badge</p>
                        <p class="text-gray-800 font-medium">{{ $stage->badge->badge ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Thème</p>
                        <p class="text-gray-800 font-medium">{{ $stage->theme }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Période</p>
                        <p class="text-gray-800 font-medium">{{ $stage->date_debut?->format('d/m/Y') ?? '—' }} à {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Service</p>
                        <p class="text-gray-800 font-medium">{{ $stage->service->nom ?? 'Aucun' }}</p>
                        <p class="text-sm text-gray-600">Responsable : {{ $stage->service->responsable ?? 'TFG sarl' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs uppercase text-gray-500">Notes / Observations</p>
                        <p class="text-gray-800 font-medium">{{ $stage->etudiant->notes ?? '10' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Script gestion des signataires --}}
    <script>
        document.querySelectorAll('.signataire-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const ordreInputId = this.dataset.ordre;
                const parOrdreId = this.dataset.parordre;

                const ordreInput = document.getElementById(ordreInputId);
                const parOrdreInput = document.getElementById(parOrdreId);

                if (ordreInput) ordreInput.disabled = !this.checked;
                if (parOrdreInput) parOrdreInput.disabled = !this.checked;
            });
        });
    </script>
</x-app-layout>
