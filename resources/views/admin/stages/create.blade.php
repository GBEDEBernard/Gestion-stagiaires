<x-app-layout>
    <div class="max-w-4xl mx-auto">
<<<<<<< HEAD
        <!-- En-tête -->
=======
>>>>>>> e9635ab
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('stages.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau Stage</h1>
            </div>
<<<<<<< HEAD
            <p class="text-gray-500 dark:text-gray-400 ml-14">Créez un nouveau stage pour un stagiaire</p>
        </div>

        <!-- Formulaire -->
=======
            <p class="text-gray-500 dark:text-gray-400 ml-14">Cree un nouveau stage avec son lieu de presence et son responsable de suivi.</p>
        </div>

>>>>>>> e9635ab
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('stages.store') }}" method="POST" class="p-6 space-y-8">
                @csrf

<<<<<<< HEAD
                <!-- Informations du stage -->
=======
>>>>>>> e9635ab
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        Informations du stage
                    </h3>
<<<<<<< HEAD
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Étudiant -->
                        <div>
                            <label for="etudiant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Étudiant <span class="text-red-500">*</span></label>
                            <select name="etudiant_id" id="etudiant_id" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                                <option value="">Sélectionner un étudiant</option>
                                @foreach($etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}" {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                    {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                </option>
                                @endforeach
                            </select>
                            @error('etudiant_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type de stage -->
                        <div>
                            <label for="typestage_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de stage <span class="text-red-500">*</span></label>
                            <select name="typestage_id" id="typestage_id" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                                <option value="">Sélectionner un type</option>
                                @foreach($typestages as $type)
                                <option value="{{ $type->id }}" {{ old('typestage_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->libelle }}
                                </option>
                                @endforeach
                            </select>
                            @error('typestage_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Badge -->
=======

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="etudiant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Etudiant <span class="text-red-500">*</span></label>
                            <select name="etudiant_id" id="etudiant_id" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                                <option value="">Selectionner un etudiant</option>
                                @foreach($etudiants as $etudiant)
                                    <option value="{{ $etudiant->id }}" {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                        {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('etudiant_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="typestage_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de stage</label>
                            <select name="typestage_id" id="typestage_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                                <option value="">Selectionner un type</option>
                                @foreach($typestages as $type)
                                    <option value="{{ $type->id }}" {{ old('typestage_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->libelle }}
                                    </option>
                                @endforeach
                            </select>
                            @error('typestage_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

>>>>>>> e9635ab
                        <div>
                            <label for="badge_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Badge</label>
                            <select name="badge_id" id="badge_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
<<<<<<< HEAD
                                <option value="">Sélectionner un badge</option>
                                @foreach($badges as $badge)
                                <option value="{{ $badge->id }}" {{ old('badge_id') == $badge->id ? 'selected' : '' }}>
                                    {{ $badge->badge }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service -->
=======
                                <option value="">Selectionner un badge</option>
                                @foreach($badges as $badge)
                                    <option value="{{ $badge->id }}" {{ old('badge_id') == $badge->id ? 'selected' : '' }}>
                                        {{ $badge->badge }}
                                    </option>
                                @endforeach
                            </select>
                            @error('badge_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

>>>>>>> e9635ab
                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service</label>
                            <select name="service_id" id="service_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
<<<<<<< HEAD
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->nom }}
                                </option>
=======
                                <option value="">Selectionner un service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->nom }}
                                    </option>
>>>>>>> e9635ab
                                @endforeach
                            </select>
                        </div>

<<<<<<< HEAD
                        <!-- Thème -->
                        <div class="md:col-span-2">
                            <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thème du stage</label>
                            <input type="text" name="theme" id="theme" value="{{ old('theme') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="Ex: Développement web, Marketing digital...">
=======
                        <div>
                            <label for="site_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site de presence</label>
                            <select name="site_id" id="site_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                                <option value="">Selectionner un site</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}{{ $site->city ? ' - ' . $site->city : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="supervisor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Superviseur</label>
                            <select name="supervisor_id" id="supervisor_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                                <option value="">Selectionner un superviseur</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->name }} - {{ $supervisor->getRoleNames()->implode(', ') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Theme du stage</label>
                            <input type="text" name="theme" id="theme" value="{{ old('theme') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="Ex: Developpement web, marketing digital...">
>>>>>>> e9635ab
                        </div>
                    </div>
                </div>

<<<<<<< HEAD
                <!-- Période -->
=======
>>>>>>> e9635ab
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
<<<<<<< HEAD
                        Période du stage
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date début -->
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de début <span class="text-red-500">*</span></label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                            @error('date_debut')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date fin -->
=======
                        Periode du stage
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de debut <span class="text-red-500">*</span></label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                            @error('date_debut')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

>>>>>>> e9635ab
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de fin <span class="text-red-500">*</span></label>
                            <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-900 dark:text-white">
                            @error('date_fin')
<<<<<<< HEAD
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
=======
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
>>>>>>> e9635ab
                            @enderror
                        </div>
                    </div>
                </div>

<<<<<<< HEAD
                <!-- Jours de présence -->
=======
>>>>>>> e9635ab
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
<<<<<<< HEAD
                        Jours de présence
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($jours as $jour)
                        <label class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}"
                                {{ in_array($jour->id, old('jours_id', [])) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $jour->jour }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Boutons -->
=======
                        Jours de presence
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($jours as $jour)
                            <label class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}"
                                    {{ in_array($jour->id, old('jours_id', [])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $jour->jour }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('jours_id')
                        <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

>>>>>>> e9635ab
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('stages.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-600/20 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6" />
                        </svg>
<<<<<<< HEAD
                        Créer le stage
=======
                        Creer le stage
>>>>>>> e9635ab
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('etudiant_id').addEventListener('change', function() {
            let etudiantId = this.value;
            if (etudiantId) {
                fetch(`/admin/etudiants/${etudiantId}/services`)
                    .then(res => res.json())
                    .then(data => {
                        let serviceSelect = document.getElementById('service_id');
<<<<<<< HEAD
                        serviceSelect.innerHTML = '<option value="">Sélectionner un service</option>';
=======
                        serviceSelect.innerHTML = '<option value="">Selectionner un service</option>';
>>>>>>> e9635ab
                        data.forEach(service => {
                            let opt = document.createElement('option');
                            opt.value = service.id;
                            opt.text = service.nom;
                            serviceSelect.appendChild(opt);
                        });
                    })
                    .catch(err => console.error('Erreur:', err));
            }
        });
    </script>
<<<<<<< HEAD
</x-app-layout>
=======
</x-app-layout>
>>>>>>> e9635ab
