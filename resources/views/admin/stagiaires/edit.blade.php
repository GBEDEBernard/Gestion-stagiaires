<x-app-layout>
<div class="bg-blue-900 hover:opacity-90 min-h-screen py-10">
    <div class="container max-w-3xl mx-auto p-6 bg-white shadow-md rounded-md">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Modifier un Stagiaire</h1>

        <form action="{{ route('stagiaires.update', $stagiaire->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <!-- Nom -->
                <div class="mb-2">
                    <label for="nom" class="block text-xl font-medium text-black">Nom</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $stagiaire->nom) }}" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nom') border-red-500 @enderror">
                    @error('nom') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Prénom -->
                <div class="mb-2">
                    <label for="prenom" class="block text-xl font-medium text-black">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $stagiaire->prenom) }}" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-500 @error('prenom') border-red-500 @enderror">
                    @error('prenom') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div class="mb-2">
                    <label for="email" class="block text-xl font-medium text-black">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $stagiaire->email) }}" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Téléphone -->
                <div class="mb-2">
                    <label for="telephone" class="block text-xl font-medium text-black">Téléphone</label>
                    <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $stagiaire->telephone) }}" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-500 @error('telephone') border-red-500 @enderror">
                    @error('telephone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Type de stage -->
                <div class="mb-2">
                    <label for="typestage_id" class="block text-xl font-medium text-black">Type de stage</label>
                    <select id="typestage_id" name="typestage_id" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <optgroup label="Type actuel">
                            <option value="{{ $stagiaire->typestage_id }}" selected>
                                {{ $stagiaire->typestage->libelle }}
                            </option>
                        </optgroup>
                        <optgroup label="Autres types de stage">
                            @foreach ($typeStages as $type)
                                @if ($type->id != $stagiaire->typestage_id)
                                    <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                    @error('typestage_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Badge -->
                <div class="mb-2">
                    <label for="badge_id" class="block text-xl font-medium text-black">Badge</label>
                    <select id="badge_id" name="badge_id" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Sélectionner un badge</option>
                        @foreach ($badges as $badge)
                            <option value="{{ $badge->id }}" {{ $badge->id == $stagiaire->badge_id ? 'selected' : '' }}>
                                {{ $badge->badge }}
                            </option>
                        @endforeach
                    </select>
                    @error('badge_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Thème -->
                <div class="mb-2">
                    <label for="theme" class="block text-xl font-medium text-black">Thème</label>
                    <input type="text" id="theme" name="theme" value="{{ old('theme', $stagiaire->theme) }}"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-500 @error('theme') border-red-500 @enderror">
                    @error('theme') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Ecole -->
                <div class="mb-2">
                    <label for="ecole" class="block text-xl font-medium text-black">École</label>
                    <input type="text" id="ecole" name="ecole" value="{{ old('ecole', $stagiaire->ecole) }}"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-blue-500 @error('ecole') border-red-500 @enderror">
                    @error('ecole') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Date de début -->
            <div class="mb-2">
                <label for="date_debut" class="block text-xl font-medium text-black">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" 
                    value="{{ old('date_debut', $stagiaire->date_debut ? \Carbon\Carbon::parse($stagiaire->date_debut)->format('Y-m-d') : '') }}" 
                    required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                    focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_debut') border-red-500 @enderror">
                @error('date_debut') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Date de fin -->
            <div class="mb-2">
                <label for="date_fin" class="block text-xl font-medium text-black">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin" 
                    value="{{ old('date_fin', $stagiaire->date_fin ? \Carbon\Carbon::parse($stagiaire->date_fin)->format('Y-m-d') : '') }}" 
                    required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                    focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_fin') border-red-500 @enderror">
                @error('date_fin') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>


            <!-- Jours -->
            <div class="mb-4">
                <label class="block text-xl font-medium text-black">Jours</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-2">
                    @foreach ($jours as $jour)
                        <div class="flex items-center">
                            <input type="checkbox" id="jour_{{ $jour->id }}" name="jours_id[]" value="{{ $jour->id }}"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                @if(in_array($jour->id, old('jours_id', $stagiaire->jours->pluck('id')->toArray()))) checked @endif>
                            <label for="jour_{{ $jour->id }}" class="ml-2 text-sm text-gray-700">{{ $jour->jour }}</label>
                        </div>
                    @endforeach
                </div>
                @error('jours_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-500 font-bold text-white rounded-md hover:bg-blue-700 transition">
                Mettre à jour
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('stagiaires.index') }}" class="text-indigo-600 hover:text-blue-800">Retour à la liste des stagiaires</a>
        </div>
    </div>
</div>
</x-app-layout>
