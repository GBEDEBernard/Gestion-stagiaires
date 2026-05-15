<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('etudiants.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Modifier l'étudiant</h1>
            </div>
            <p class="text-gray-500 ml-14">Mettez à jour les informations du stagiaire.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('etudiants.update', $etudiant) }}" method="POST" class="p-6 space-y-5">
                @csrf @method('PUT')
                @php $p = $etudiant->personnel; @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $p->nom) }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
                        @error('nom') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $p->prenom) }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
                        @error('prenom') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="genre" class="block text-sm font-medium text-gray-700 mb-2">Genre</label>
                        <select name="genre" id="genre" class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
                            <option value="" disabled>Sélectionner</option>
                            <option value="Masculin" {{ old('genre', $p->genre) == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                            <option value="Féminin" {{ old('genre', $p->genre) == 'Féminin' ? 'selected' : '' }}>Féminin</option>
                        </select>
                        @error('genre') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $p->email) }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
                        @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $p->telephone) }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
                        @error('telephone') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="ecole" class="block text-sm font-medium text-gray-700 mb-2">École</label>
                        <input type="text" name="ecole" id="ecole" value="{{ old('ecole', $etudiant->ecole) }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl">
                        @error('ecole') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if($p->user)
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
                        ⚠️ Un compte utilisateur existe déjà. La modification de l’email ne sera pas répercutée sur le compte. Si vous changez l’email, pensez à informer l’utilisateur.
                    </div>
                @endif

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('etudiants.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">Annuler</a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>