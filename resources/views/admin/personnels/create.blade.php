<x-app-layout>
    <div class="mb-8 ml-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau personnel</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Créez un personnel, choisissez le type et enregistrez-le dans les deux tableaux.</p>
            </div>
            <a href="{{ route('personnels.index') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition">Retour à la liste</a>
        </div>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl">
            <ul class="space-y-1 text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('personnels.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Prénom *</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                        @error('prenom')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom *</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                        @error('nom')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                        @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                        @error('telephone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Genre</label>
                        <select name="genre" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                            <option value="">Sélectionner</option>
                            <option value="Homme" {{ old('genre') == 'Homme' ? 'selected' : '' }}>Homme</option>
                            <option value="Femme" {{ old('genre') == 'Femme' ? 'selected' : '' }}>Femme</option>
                            <option value="Autre" {{ old('genre') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('genre')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                        @error('date_naissance')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Adresse</label>
                        <textarea name="adresse" rows="3" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">{{ old('adresse') }}</textarea>
                        @error('adresse')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 p-5 rounded-3xl border border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Type de personnel</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choisissez un type pour enregistrer les informations spécialisées.</p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <label class="cursor-pointer rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 bg-white dark:bg-gray-800 hover:border-sky-400">
                            <input type="radio" name="type" value="etudiant" {{ old('type', 'etudiant') === 'etudiant' ? 'checked' : '' }} class="h-4 w-4 text-sky-600" onchange="toggleTypeFields()" />
                            <span>Étudiant</span>
                        </label>
                        <label class="cursor-pointer rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 bg-white dark:bg-gray-800 hover:border-sky-400">
                            <input type="radio" name="type" value="employe" {{ old('type') === 'employe' ? 'checked' : '' }} class="h-4 w-4 text-sky-600" onchange="toggleTypeFields()" />
                            <span>Employé</span>
                        </label>
                    </div>
                    @error('type')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="etudiant-fields" class="space-y-6 {{ old('type', 'etudiant') !== 'etudiant' ? 'hidden' : '' }}">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Informations étudiant</h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">École *</label>
                            <input type="text" name="ecole" value="{{ old('ecole') }}" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                            @error('ecole')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div id="employe-fields" class="space-y-6 {{ old('type') !== 'employe' ? 'hidden' : '' }}">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Informations employé</h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Domaine *</label>
                            <select name="domaine_id" id="domaine_id" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" disabled>
                                <option value="">D'abord choisir un site</option>
                            </select>
                            @error('domaine_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Site *</label>
                            <select name="site_id" id="site_id" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner</option>
                                @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                @endforeach
                            </select>
                            @error('site_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Poste</label>
                            <input type="text" name="poste" value="{{ old('poste') }}" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                            @error('poste')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Matricule *</label>
                            <input type="text" name="matricule" value="{{ old('matricule') }}" class="mt-2 w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl" />
                            @error('matricule')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition">Enregistrer</button>
                    <a href="{{ route('personnels.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 transition">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Stage (après création étudiant) --}}
    @if(session('open_stage_modal'))
    <div id="stageModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 dark:bg-black/75 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Créer le stage</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pour <span class="font-semibold text-blue-600">{{ session('new_etudiant_nom') }}</span></p>
                    </div>
                </div>
                <button onclick="document.getElementById('stageModal').remove()"
                    class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Formulaire --}}
            <form action="{{ route('stages.store') }}" method="POST" class="px-6 py-6 space-y-5">
                @csrf
                <input type="hidden" name="etudiant_id" value="{{ session('new_etudiant_id') }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type de stage</label>
                        <select name="typestage_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">— Sélectionner —</option>
                            @foreach($typestages as $type)
                            <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Badge</label>
                        <select name="badge_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">— Sélectionner —</option>
                            @foreach($badges as $badge)
                            <option value="{{ $badge->id }}">{{ $badge->badge }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Service</label>
                        <select name="service_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">— Sélectionner —</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Site de présence</label>
                        <select name="site_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">— Sélectionner —</option>
                            @foreach($stageSites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }}{{ $site->city ? ' — ' . $site->city : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Superviseur</label>
                    <select name="supervisor_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">— Sélectionner —</option>
                        @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}">{{ $supervisor->personnel->nom ?? $supervisor->name ?? '' }} {{ $supervisor->personnel->prenom ?? '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Thème du stage</label>
                    <input type="text" name="theme"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Ex: Développement web, Marketing digital...">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date de début <span class="text-red-500">*</span></label>
                        <input type="date" name="date_debut" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date de fin <span class="text-red-500">*</span></label>
                        <input type="date" name="date_fin" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jours de présence <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($jours as $jour)
                        <label class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30">
                            <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}" class="w-4 h-4 text-blue-600 rounded accent-blue-600">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $jour->jour }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-5 mt-2">
                    <button type="button" onclick="document.getElementById('stageModal').remove()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Ignorer pour l'instant
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 active:scale-95 transition shadow-lg shadow-blue-600/25">
                        Créer le stage
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        const domainesParSite = @json($domainesParSite);
        const defaultSiteId = @json(old('site_id', ''));
        const defaultDomaineId = @json(old('domaine_id', ''));

        function toggleTypeFields() {
            const type = document.querySelector('input[name="type"]:checked').value;
            const etudiantDiv = document.getElementById('etudiant-fields');
            const employeDiv = document.getElementById('employe-fields');
            const isEtudiant = type === 'etudiant';

            etudiantDiv.classList.toggle('hidden', !isEtudiant);
            employeDiv.classList.toggle('hidden', isEtudiant);

            etudiantDiv.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = !isEtudiant;
            });
            employeDiv.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = isEtudiant;
            });
        }

        function updateDomaineOptions() {
            const siteSelect = document.querySelector('select[name="site_id"]');
            const domaineSelect = document.querySelector('select[name="domaine_id"]');
            const siteId = siteSelect.value;
            const domaines = domainesParSite[siteId] || {};

            domaineSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = siteId ? 'Sélectionnez un domaine' : 'D\'abord choisir un site';
            domaineSelect.appendChild(placeholder);

            if (!siteId || Object.keys(domaines).length === 0) {
                domaineSelect.disabled = true;
                return;
            }

            domaineSelect.disabled = false;
            Object.entries(domaines).forEach(([id, nom]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = nom;
                if (id == defaultDomaineId) {
                    option.selected = true;
                }
                domaineSelect.appendChild(option);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const siteSelect = document.querySelector('select[name="site_id"]');
            if (siteSelect) {
                siteSelect.addEventListener('change', updateDomaineOptions);
            }
            toggleTypeFields();
            updateDomaineOptions();
        });
    </script>
</x-app-layout>