<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modifier le personnel</h1>
                <p class="text-gray-500 dark:text-gray-300 mt-1">Mettez à jour les informations du personnel et son type associé.</p>
            </div>
            <a href="{{ route('personnels.index') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                Retour à la liste
            </a>
        </div>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-xl border border-red-200 dark:border-red-800">
            <ul class="space-y-1 text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('personnels.update', $personnel) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="{{ $type }}" />

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Prénom *</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $personnel->prenom) }}" required
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                      text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom *</label>
                        <input type="text" name="nom" value="{{ old('nom', $personnel->nom) }}" required
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                      text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $personnel->email) }}" required
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                      text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $personnel->telephone) }}"
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                      text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Genre</label>
                        <select name="genre"
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                       text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="">Sélectionner</option>
                            <option value="Homme" {{ old('genre', $personnel->genre) == 'Homme' ? 'selected' : '' }}>Homme</option>
                            <option value="Femme" {{ old('genre', $personnel->genre) == 'Femme' ? 'selected' : '' }}>Femme</option>
                            <option value="Autre" {{ old('genre', $personnel->genre) == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance', $personnel->date_naissance) }}"
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                      text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Adresse</label>
                        <textarea name="adresse" rows="3"
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                         text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">{{ old('adresse', $personnel->adresse) }}</textarea>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900/50 p-5 rounded-3xl border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Type de personnel</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">
                        Ce personnel est enregistré comme
                        <strong class="text-gray-700 dark:text-sky-400">
                            {{ $type === 'employe' ? 'Employé' : ($type === 'etudiant' ? 'Étudiant' : 'Inconnu') }}
                        </strong>.
                    </p>
                </div>

                <div id="etudiant-fields" class="space-y-6 {{ $type !== 'etudiant' ? 'hidden' : '' }}">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Informations étudiant</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">École</label>
                        <input type="text" name="ecole" value="{{ old('ecole', optional($personnel->personnable)->ecole) }}"
                            class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                      text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                    </div>
                </div>

                <div id="employe-fields" class="space-y-6 {{ $type !== 'employe' ? 'hidden' : '' }}">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Informations employé</h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Domaine</label>
                            <select name="domaine_id"
                                class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                           text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                @foreach($domaines as $domaine)
                                <option value="{{ $domaine->id }}" {{ old('domaine_id', optional($personnel->personnable)->domaine_id) == $domaine->id ? 'selected' : '' }}>{{ $domaine->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Site *</label>
                            <select name="site_id"
                                class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                           text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('site_id', optional($personnel->personnable)->site_id) == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Poste</label>
                            <input type="text" name="poste" value="{{ old('poste', optional($personnel->personnable)->poste) }}"
                                class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                          text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Matricule</label>
                            <input type="text" name="matricule" value="{{ old('matricule', optional($personnel->personnable)->matricule) }}"
                                class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl
                                          text-gray-900 dark:text-white focus:ring-2 focus:ring-sky-500 focus:border-transparent" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-xl hover:from-sky-600 hover:to-blue-700 transition shadow-md font-medium">
                        Mettre à jour
                    </button>
                    <a href="{{ route('personnels.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>