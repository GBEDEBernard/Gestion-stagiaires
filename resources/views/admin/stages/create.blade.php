<x-app-layout>
    <div class="max-w-4xl mx-auto">

        {{-- Modal de création rapide (après création d’un étudiant) --}}
        <div id="stageModal" class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 p-4"
            style="display: {{ ($showModal ?? false) ? 'flex' : 'none' }};">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Créer un stage pour l'étudiant</h2>
                    <button onclick="closeModal()" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="stageFormModal" action="{{ route('stages.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="etudiant_id_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Étudiant <span class="text-red-500">*</span>
                            </label>
                            <select name="etudiant_id" id="etudiant_id_modal" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner un étudiant</option>
                                @foreach($etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}"
                                    {{ ($preselectedEtudiantId ?? request('etudiant_id') ?? old('etudiant_id')) == $etudiant->id ? 'selected' : '' }}>
                                    {{ $etudiant->personnel->nom ?? '' }} {{ $etudiant->personnel->prenom ?? '' }}
                                </option>
                                @endforeach
                            </select>
                            @error('etudiant_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="typestage_id_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de stage</label>
                                <select name="typestage_id" id="typestage_id_modal" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                    <option value="">Sélectionner</option>
                                    @foreach($typestages as $type)
                                    <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="badge_id_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Badge</label>
                                <select name="badge_id" id="badge_id_modal" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                    <option value="">Sélectionner</option>
                                    @foreach($badges as $badge)
                                    <option value="{{ $badge->id }}">{{ $badge->badge }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="domaine_id_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Domaine</label>
                                <select name="domaine_id" id="domaine_id_modal" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                    <option value="">Sélectionner</option>
                                    @foreach($domaines as $domaine)
                                    <option value="{{ $domaine->id }}">{{ $domaine->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="site_id_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site de présence</label>
                                <select name="site_id" id="site_id_modal" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                    <option value="">Sélectionner</option>
                                    @foreach($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}{{ $site->city ? ' - ' . $site->city : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="supervisor_id_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Superviseur</label>
                            <select name="supervisor_id" id="supervisor_id_modal" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->personnel->nom ?? '' }} {{ $supervisor->personnel->prenom ?? '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="theme_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thème du stage</label>
                            <input type="text" name="theme" id="theme_modal"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl"
                                placeholder="Ex: Développement web...">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="date_debut_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Date de début <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date_debut" id="date_debut_modal" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                            </div>
                            <div>
                                <label for="date_fin_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Date de fin <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date_fin" id="date_fin_modal" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Jours de présence</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($jours as $jour)
                                <label class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}" class="w-4 h-4 text-blue-600 rounded">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $jour->jour }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('jours_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p id="joursErrorModal" class="mt-2 text-sm text-red-500" style="display:none">Veuillez sélectionner au moins un jour de présence.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition font-medium">
                            Ignorer
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-600/20">
                            Créer le stage
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Formulaire principal --}}
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('stages.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nouveau Stage</h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 ml-14">Créez un nouveau stage avec son lieu de présence et son responsable de suivi.</p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl">{{ session('success') }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form id="stageForm" action="{{ route('stages.store') }}" method="POST" class="p-6 space-y-8">
                @csrf
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        Informations du stage
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="etudiant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Étudiant <span class="text-red-500">*</span>
                            </label>
                            <select name="etudiant_id" id="etudiant_id" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner un étudiant</option>
                                @foreach($etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}"
                                    {{ (request('etudiant_id') ?? old('etudiant_id')) == $etudiant->id ? 'selected' : '' }}>
                                    {{ $etudiant->personnel->nom ?? '' }} {{ $etudiant->personnel->prenom ?? '' }}
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
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner un type</option>
                                @foreach($typestages as $type)
                                <option value="{{ $type->id }}" {{ old('typestage_id') == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="badge_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Badge</label>
                            <select name="badge_id" id="badge_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner un badge</option>
                                @foreach($badges as $badge)
                                <option value="{{ $badge->id }}" {{ old('badge_id') == $badge->id ? 'selected' : '' }}>{{ $badge->badge }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="domaine_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Services</label>
                            <select name="domaine_id" id="domaine_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner un domaine de service</option>
                                @foreach($domaines as $domaine)
                                <option value="{{ $domaine->id }}" {{ old('domaine_id') == $domaine->id ? 'selected' : '' }}>{{ $domaine->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="site_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site de présence</label>
                            <select name="site_id" id="site_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner un site</option>
                                @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}{{ $site->city ? ' - ' . $site->city : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="supervisor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Superviseur</label>
                            <select name="supervisor_id" id="supervisor_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="">Sélectionner un superviseur</option>
                                @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->personnel->nom ?? '' }} {{ $supervisor->personnel->prenom ?? '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thème du stage</label>
                            <input type="text" name="theme" id="theme" value="{{ old('theme') }}"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl"
                                placeholder="Ex: Développement web, marketing digital...">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Période du stage</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de début *</label>
                            <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                        </div>
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de fin *</label>
                            <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Jours de présence</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($jours as $jour)
                        <label class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" name="jours_id[]" value="{{ $jour->id }}"
                                {{ in_array($jour->id, old('jours_id', [])) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $jour->jour }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('jours_id')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p id="joursError" class="mt-2 text-sm text-red-500" style="display:none">Veuillez sélectionner au moins un jour de présence.</p>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('stages.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition font-medium">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-600/20">
                        Créer le stage
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function closeModal() {
            const modal = document.getElementById('stageModal');
            if (modal) {
                modal.style.display = 'none';
                const url = new URL(window.location.href);
                url.searchParams.delete('show_modal');
                url.searchParams.delete('show');
                url.searchParams.delete('showModal');
                url.searchParams.delete('etudiant_id');
                url.searchParams.delete('preselected_etudiant_id');
                window.history.replaceState({}, '', url.toString());
            }
        }

        function openModal(etudiantId = null) {
            const modal = document.getElementById('stageModal');
            if (!modal) return;
            modal.style.display = 'flex';
            if (etudiantId) {
                const sel = document.getElementById('etudiant_id_modal');
                if (sel) {
                    sel.value = etudiantId;
                    sel.dispatchEvent(new Event('change'));
                }
            }
        }

        function handleEtudiantChange(selectElement) {
            const etudiantId = selectElement.value;
            if (!etudiantId) {
                return;
            }

            fetch(`/admin/etudiants/${etudiantId}/domaines`)
                .then(res => res.json())
                .then(data => {
                    const domaineSelects = [
                        document.getElementById('domaine_id'),
                        document.getElementById('domaine_id_modal')
                    ];
                    domaineSelects.forEach(domaineSelect => {
                        if (!domaineSelect) return;
                        domaineSelect.innerHTML = '<option value="">Sélectionner un domaine</option>';
                        data.forEach(domaine => {
                            const opt = document.createElement('option');
                            opt.value = domaine.id;
                            opt.text = domaine.nom;
                            domaineSelect.appendChild(opt);
                        });
                    });
                })
                .catch(err => console.error('Erreur lors du chargement des domaines :', err));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const etudiantSelect = document.getElementById('etudiant_id');
            const etudiantSelectModal = document.getElementById('etudiant_id_modal');

            if (etudiantSelect) {
                etudiantSelect.addEventListener('change', function() {
                    handleEtudiantChange(this);
                });
            }

            if (etudiantSelectModal) {
                etudiantSelectModal.addEventListener('change', function() {
                    handleEtudiantChange(this);
                });
                if (etudiantSelectModal.value) {
                    handleEtudiantChange(etudiantSelectModal);
                }
            }

            try {
                const params = new URLSearchParams(window.location.search);
                const show = params.get('show_modal') || params.get('show') || params.get('showModal');
                const etuId = params.get('etudiant_id') || params.get('preselected_etudiant_id');
                if (show && ['1', 'true', 'yes'].includes(String(show).toLowerCase())) {
                    openModal(etuId);
                }
            } catch (e) {
                console.debug('Erreur de lecture des query params pour le modal :', e);
            }
        });

        document.addEventListener('click', function(e) {
            const modal = document.getElementById('stageModal');
            if (modal && e.target === modal) {
                closeModal();
            }
        });
    </script>

    <script>
        function validateJours(form, errorElementId) {
            const checkboxes = form.querySelectorAll('input[name="jours_id[]"]');
            let found = false;
            checkboxes.forEach(cb => {
                if (cb.checked) found = true;
            });
            const errEl = document.getElementById(errorElementId);
            if (!found) {
                if (errEl) errEl.style.display = 'block';
                return false;
            }
            if (errEl) errEl.style.display = 'none';
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const mainForm = document.getElementById('stageForm');
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    if (!validateJours(this, 'joursError')) {
                        e.preventDefault();
                        const firstCb = this.querySelector('input[name="jours_id[]"]');
                        if (firstCb) firstCb.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });
            }

            const modalForm = document.getElementById('stageFormModal');
            if (modalForm) {
                modalForm.addEventListener('submit', function(e) {
                    if (!validateJours(this, 'joursErrorModal')) {
                        e.preventDefault();
                        const firstCb = this.querySelector('input[name="jours_id[]"]');
                        if (firstCb) firstCb.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });
            }

            const editForm = document.getElementById('stageFormEdit');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    if (!validateJours(this, 'joursErrorEdit')) {
                        e.preventDefault();
                        const firstCb = this.querySelector('input[name="jours_id[]"]');
                        if (firstCb) firstCb.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });
            }
        });
    </script>
</x-app-layout>