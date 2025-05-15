<x-app-layout>

<div class="bg-blue-900 hover:opacity-90  min-h-screen py-10">
    <div class="container max-w-4xl mx-auto p-8 bg-white shadow-2xl rounded-2xl border border-blue-200">
        <h1 class="text-3xl font-bold text-blue-700 mb-8">Ajouter un Stagiaire</h1>

        <form action="{{ route('stagiaires.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- NOM -->
                <div>
                    <label for="nom" class="block text-sm font-semibold text-blue-600">Nom</label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('nom') border-red-500 @enderror"
                        required>
                    @error('nom')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PRENOM -->
                <div>
                    <label for="prenom" class="block text-sm font-semibold text-blue-700">Prénom</label>
                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('prenom') border-red-500 @enderror"
                        required>
                    @error('prenom')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- EMAIL -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-blue-600">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                        required>
                    @error('email')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- TELEPHONE -->
                <div>
                    <label for="telephone" class="block text-sm font-semibold text-blue-700">Téléphone</label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('telephone') border-red-500 @enderror"
                        required>
                    @error('telephone')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- TYPE STAGE -->
                <div>
                    <label for="typestage_id" class="block text-sm font-semibold text-blue-600">Type de stage</label>
                    <select name="typestage_id" id="typestage_id"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('typestage_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Sélectionner --</option>
                        @foreach ($typestages as $type)
                            <option value="{{ $type->id }}" {{ old('typestage_id') == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                        @endforeach
                    </select>
                    @error('typestage_id')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- BADGE -->
                <div>
                    <label for="badge_id" class="block text-sm font-medium text-blue-600">Badge</label>
                    <select name="badge_id" id="badge_id" class="mt-1 block w-full border border-gray-300 rounded-lg p-2" required>
                        <option value="">Sélectionner un badge</option>
                        @foreach ($badges as $badge)
                            <option value="{{ $badge->id }}">{{ $badge->badge }}</option>
                        @endforeach
                    </select>
                    @error('badge_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- THEME -->
                <div>
                    <label for="theme" class="block text-sm font-semibold text-blue-600">Thème</label>
                    <input type="text" name="theme" id="theme" value="{{ old('theme') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('theme') border-red-500 @enderror">
                    @error('theme')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                 <!-- Ecole -->
                 <div>
                    <label for="ecole" class="block text-sm font-semibold text-blue-600">Ecole</label>
                    <input type="text" name="ecole" id="ecole" value="{{ old('ecole') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('ecole') border-red-500 @enderror">
                    @error('ecole')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DATE DEBUT -->
                <div>
                    <label for="date_debut" class="block text-sm font-semibold text-blue-700">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('date_debut') border-red-500 @enderror"
                        required>
                    @error('date_debut')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DATE FIN -->
                <div>
                    <label for="date_fin" class="block text-sm font-semibold text-blue-600">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('date_fin') border-red-500 @enderror"
                        required>
                    @error('date_fin')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- JOURS -->
          <!-- Jours -->
            <div class="mb-4 sm:col-span-2">
                <label for="jours_id" class="block text-sm font-semibold text-blue-600 mb-2">Jours de présence</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($jours as $jour)
                        <label class="inline-flex items-center bg-gray-50 p-2 rounded-md shadow-sm hover:bg-gray-100">
                            <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}"
                                class="form-checkbox text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                {{ in_array($jour->id, old('jours_id', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $jour->jour }}</span>
                        </label>
                    @endforeach
                </div>
                @error('jours_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
          

           <div class="">
            <button type="submit" class="mt-2 ml-[200px] w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition duration-300">
                Enregistrer
            </button>
           </div>
        </form>
    </div>
</div>
</x-app-layout>