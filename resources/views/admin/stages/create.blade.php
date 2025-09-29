<x-app-layout>
<div class=" min-h-screen py-10">
    <div class="container max-w-4xl mx-auto p-8 bg-white shadow-2xl rounded-2xl border border-blue-200 hover:opacity-85">
        <h1 class="text-3xl font-bold text-blue-700 mb-8">Ajouter un Stage</h1>

        <form action="{{ route('stages.store') }}" method="POST" >
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Étudiant -->
                <div>
                    <label for="etudiant_id" class="block text-sm font-semibold text-blue-600">Étudiant</label>
                    <select name="etudiant_id" id="etudiant_id" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('etudiant_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner --</option>
                        @foreach($etudiants as $etudiant)
                            <option value="{{ $etudiant->id }}" {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                {{ $etudiant->nom }} {{ $etudiant->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('etudiant_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Type de stage -->
                <div>
                    <label for="typestage_id" class="block text-sm font-semibold text-blue-600">Type de stage</label>
                    <select name="typestage_id" id="typestage_id" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('typestage_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner --</option>
                        @foreach($typestages as $type)
                            <option value="{{ $type->id }}" {{ old('typestage_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('typestage_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Badge -->
                <div>
                    <label for="badge_id" class="block text-sm font-semibold text-blue-700">Badge</label>
                    <select name="badge_id" id="badge_id"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('badge_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner --</option>
                        @foreach($badges as $badge)
                            <option value="{{ $badge->id }}" {{ old('badge_id') == $badge->id ? 'selected' : '' }}>
                                {{ $badge->badge }}
                            </option>
                        @endforeach
                    </select>
                    @error('badge_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Service -->
                <div>
                    <label for="service_id" class="block text-sm font-semibold text-blue-700">Service</label>
                    <select name="service_id" id="service_id"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('service_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Thème -->
                <div>
                    <label for="theme" class="block text-sm font-semibold text-blue-700">Thème</label>
                    <input type="text" name="theme" id="theme" value="{{ old('theme') }}"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('theme') border-red-500 @enderror">
                    @error('theme') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Date début -->
                <div>
                    <label for="date_debut" class="block text-sm font-semibold text-blue-600">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('date_debut') border-red-500 @enderror">
                    @error('date_debut') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Date fin -->
                <div>
                    <label for="date_fin" class="block text-sm font-semibold text-blue-700">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('date_fin') border-red-500 @enderror">
                    @error('date_fin') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <!-- Jours (checkboxes) -->
            <div class="mt-6">
                <span class="block text-sm font-semibold text-blue-600 mb-2">Jours de présence</span>
                <div class="flex flex-wrap gap-4">
                    @foreach($jours as $jour)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}" 
                                {{ in_array($jour->id, old('jours_id', [])) ? 'checked' : '' }}
                                class="form-checkbox h-5 w-5 text-blue-600">
                            <span class="text-gray-700">{{ $jour->jour }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 text-right">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('etudiant_id').addEventListener('change', function() {
    let etudiantId = this.value;
    fetch(`/etudiants/${etudiantId}/services`)
        .then(res => res.json())
        .then(data => {
            let serviceSelect = document.getElementById('service_id');
            serviceSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
            data.forEach(service => {
                let opt = document.createElement('option');
                opt.value = service.id;
                opt.text = service.nom;
                serviceSelect.appendChild(opt);
            });
        });
});

</script>
</x-app-layout>
