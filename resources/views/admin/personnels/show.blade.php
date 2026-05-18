<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Fiche du personnel</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Détails centralisés du personnel et des informations liées à son type.</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('personnels.index') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition">Retour à la liste</a>
                <a href="{{ route('personnels.edit', $personnel) }}" class="px-4 py-2.5 bg-yellow-100 text-yellow-700 rounded-xl hover:bg-yellow-200 transition">Modifier</a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-xl">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Profil</h2>
                <div class="mt-6 space-y-4 text-sm text-gray-700 dark:text-gray-300">
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Nom</h3>
                        <p>{{ $personnel->prenom }} {{ $personnel->nom }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Email</h3>
                        <p>{{ $personnel->email }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Téléphone</h3>
                        <p>{{ $personnel->telephone ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Genre</h3>
                        <p>{{ $personnel->genre ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Type</h3>
                        <p>{{ $personnel->type_label }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Compte</h3>
                        <p>{{ $personnel->user ? 'Compte actif' : 'Aucun compte associé' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:col-span-2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Détails</h2>
                <div class="mt-6 grid gap-6 md:grid-cols-2 text-sm text-gray-700 dark:text-gray-300">
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Date de naissance</h3>
                        <p>{{ $personnel->date_naissance ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Adresse</h3>
                        <p>{{ $personnel->adresse ?? '-' }}</p>
                    </div>
                    @if($personnel->personnable_type === App\Models\Etudiant::class)
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">École</h3>
                        <p>{{ $personnel->personnable->ecole ?? '-' }}</p>
                    </div>
                    @elseif($personnel->personnable_type === App\Models\Employe::class)
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Poste</h3>
                        <p>{{ $personnel->personnable->poste ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Matricule</h3>
                        <p>{{ $personnel->personnable->matricule ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Domaine</h3>
                        <p>{{ optional($personnel->personnable->domaine)->nom ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Site</h3>
                        <p>{{ optional($personnel->personnable->site)->name ?? '-' }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if(!$personnel->user)
                    <form action="{{ route('personnels.generate-account', $personnel) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-3 bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition">Générer un compte</button>
                    </form>
                    @endif
                    <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" onsubmit="return confirm('Supprimer ce personnel ?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-3 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 transition">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>