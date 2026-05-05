<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            {{-- Header --}}
            <div class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="mt-4 text-xl font-semibold text-gray-900 text-center">Confirmer le pointage</h1>
                <p class="mt-1 text-sm text-gray-500 text-center">{{ ucfirst($type) }} • {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <div class="text-red-800 text-sm">
                    <strong>Erreurs de validation :</strong>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="px-6 py-6 space-y-6">

                {{-- Détails --}}
                <div class="space-y-4">

                    {{-- Site --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-1-4-4 4h14l-4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ isset($user_name) ? 'Domaine / Site' : 'Entreprise / Site' }}</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $site_name }}</p>
                            @if(isset($domaine_name))
                            <p class="text-sm text-gray-500">{{ $domaine_name }}</p>
                            @elseif(isset($theme))
                            <p class="text-sm text-gray-500">{{ $theme }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- GPS --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Position GPS</p>
                            <div class="mt-1 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-500">Latitude:</span>
                                    <span class="font-mono text-gray-900">{{ $latitude }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Longitude:</span>
                                    <span class="font-mono text-gray-900">{{ $longitude }}</span>
                                </div>
                            </div>
                            @if(isset($distance))
                            <p class="mt-2 text-sm text-green-600">
                                Distance du site: {{ $distance }}m
                            </p>
                            @endif
                            <p class="text-xs text-gray-500">Précision: {{ $accuracy ?? 'N/A' }} mètres</p>
                        </div>
                    </div>

                    {{-- Heure --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Heure de pointage</p>
                            <p class="text-xl font-bold text-gray-900">{{ $pointage_time }}</p>
                            <p class="text-sm text-gray-500">{{ now()->diffForHumans() }}</p>
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="space-y-3">
                    <form id="pointageForm" method="POST" action="{{ route('presence.confirm') }}">
                        @csrf
                        @foreach($form_data ?? [] as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        {{-- Champ observation masqué (rempli par la modale si retard) --}}
                        <textarea 
                            id="observation_message" 
                            name="observation_message" 
                            rows="3" 
                            class="hidden"
                            placeholder="Observation (obligatoire en cas de retard)"></textarea>

                        <button type="submit" id="submitBtn"
                            class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-md transition-colors duration-200 flex items-center justify-center relative"
                            {{ isset($is_late) && $is_late ? 'disabled' : '' }}>
                            <span id="buttonText" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ isset($is_late) && $is_late ? 'Remplissez l\'observation pour continuer' : 'Valider le pointage' }}
                            </span>
                            <div id="spinner" class="absolute inset-0 flex items-center justify-center opacity-0">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </form>

                    <a href="{{ route('presence.pointage') }}"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-md transition-colors duration-200 text-center block">
                        Annuler & Modifier
                    </a>
                </div>

                {{-- Optimistic preview --}}
                <div id="optimisticPreview" class="hidden p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Pointage en cours de validation...</p>
                            <p class="text-sm text-green-700">Redirection vers l'historique.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODALE POUR OBSERVATION EN CAS DE RETARD --}}
    @if(isset($is_late) && $is_late)
    <div id="observationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm transition-all duration-300" style="display: flex;">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Arrivée en retard</h3>
                </div>
                <p class="text-slate-600 dark:text-slate-300 mb-4">
                    Vous avez pointé après 8h00. Merci d’indiquer brièvement la raison (10 à 500 caractères).
                </p>
                <div class="mb-5">
                    <textarea id="modalObservation"
                              rows="4"
                              class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-slate-900 dark:text-white resize-none"
                              placeholder="Ex. : Embouteillage sur l’A7, problème de RER, rendez-vous médical…"></textarea>
                    <div id="charCount" class="text-right text-xs text-slate-400 mt-1">0 / 500</div>
                    <div id="errorMin" class="text-red-500 text-xs mt-1 hidden">⚠️ Minimum 10 caractères requis.</div>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="cancelObservation" class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Annuler</button>
                    <button type="button" id="confirmObservation" class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold transition">Enregistrer & continuer</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pointageForm');
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('buttonText');
            const optimisticPreview = document.getElementById('optimisticPreview');
            const observationField = document.getElementById('observation_message');
            const isLate = {{ isset($is_late) && $is_late ? 'true' : 'false' }};

            // Fonction de validation de l'observation (pour le bouton)
            function validateObservation() {
                if (!isLate || !observationField) return true;
                const message = observationField.value.trim();
                const isValid = message.length >= 10; // Doit correspondre au min:10 du contrôleur
                submitBtn.disabled = !isValid;
                if (!isValid) {
                    submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    buttonText.innerHTML = 'Remplissez l\'observation pour continuer';
                } else {
                    submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    buttonText.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Valider avec observation';
                }
                return isValid;
            }

            // Écouter les modifications sur le champ observation (rempli par la modale)
            if (observationField) {
                observationField.addEventListener('input', validateObservation);
                validateObservation(); // état initial
            }

            // --- Gestion de la modale (si retard) ---
            @if(isset($is_late) && $is_late)
            const modal = document.getElementById('observationModal');
            const modalTextarea = document.getElementById('modalObservation');
            const charCount = document.getElementById('charCount');
            const errorMin = document.getElementById('errorMin');
            const confirmBtn = document.getElementById('confirmObservation');
            const cancelBtn = document.getElementById('cancelObservation');

            // Compteur de caractères
            function updateCounter() {
                const len = modalTextarea.value.length;
                charCount.innerText = `${len} / 500`;
                if (len > 500) {
                    modalTextarea.value = modalTextarea.value.substring(0, 500);
                    updateCounter();
                }
            }
            modalTextarea.addEventListener('input', updateCounter);
            updateCounter();

            // Validation et fermeture de la modale
            function validateAndClose() {
                const val = modalTextarea.value.trim();
                const minLength = 10; // Doit correspondre à la règle de validation
                if (val.length < minLength) {
                    errorMin.classList.remove('hidden');
                    return;
                }
                errorMin.classList.add('hidden');
                // Copier vers le champ réel du formulaire
                observationField.value = val;
                // Déclencher l'événement input pour activer le bouton de validation
                const inputEvent = new Event('input', { bubbles: true });
                observationField.dispatchEvent(inputEvent);
                // Fermer la modale
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            confirmBtn.addEventListener('click', validateAndClose);

            cancelBtn.addEventListener('click', () => {
                // Vider le champ réel et le désactiver
                observationField.value = '';
                observationField.dispatchEvent(new Event('input', { bubbles: true }));
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                alert('Vous devez fournir une observation pour valider votre arrivée en retard.');
            });

            // Empêcher la fermeture en cliquant sur l'arrière-plan
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    e.stopPropagation();
                }
            });

            // Bloquer le scroll de la page tant que la modale est ouverte
            document.body.style.overflow = 'hidden';
            @endif

            // --- Soumission du formulaire ---
            form.addEventListener('submit', function(e) {
                // Vérification finale (si retard et observation vide)
                if (isLate && (!observationField || observationField.value.trim().length < 10)) {
                    e.preventDefault();
                    alert('Veuillez saisir une observation d\'au moins 10 caractères pour votre retard.');
                    return false;
                }

                // Activer l'état de chargement
                submitBtn.disabled = true;
                buttonText.style.opacity = '0';
                spinner.classList.remove('opacity-0');

                // Afficher l'aperçu optimiste après un court délai
                setTimeout(() => {
                    optimisticPreview.classList.remove('hidden');
                }, 300);
            });
        });
    </script>
</x-app-layout>