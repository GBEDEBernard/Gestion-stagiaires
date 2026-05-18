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
                if (id === defaultDomaineId) {
                    option.selected = true;
                }
                domaineSelect.appendChild(option);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('select[name="site_id"]').addEventListener('change', updateDomaineOptions);
            toggleTypeFields();
            updateDomaineOptions();
        });
    </script>
</x-app-layout>