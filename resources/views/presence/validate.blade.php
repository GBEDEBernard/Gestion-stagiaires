<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">

        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <strong>Erreur de validation:</strong>
            <ul class="mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold mb-2">Confirmer votre pointage</h1>
                <p class="text-sm opacity-90">{{ ucfirst($type) }} • {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            <div class="p-6 space-y-6">

                {{-- Détails du pointage --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <h3 class="font-semibold text-emerald-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ isset($user_name) ? 'Employé' : 'Étudiant' }}
                        </h3>
                        <p class="text-lg font-bold text-slate-900">{{ $user_name ?? $etudiant_name }}</p>
                    </div>

                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <h3 class="font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-1-4-4 4h14l-4-4"></path>
                            </svg>
                            {{ isset($user_name) ? 'Domaine / Site' : 'Entreprise / Site' }}
                        </h3>
                        <p class="text-lg font-bold text-slate-900">{{ $site_name }}</p>
                        @if(isset($domaine_name))
                        <p class="text-sm text-slate-600 mt-1">{{ $domaine_name }}</p>
                        @elseif(isset($theme))
                        <p class="text-sm text-slate-600 mt-1">{{ $theme }}</p>
                        @endif
                    </div>

                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100 md:col-span-2">
                        <h3 class="font-semibold text-indigo-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Position GPS
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-slate-500">Latitude:</span><br>
                                <span class="font-mono bg-slate-100 px-2 py-1 rounded text-slate-900">{{ $latitude }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Longitude:</span><br>
                                <span class="font-mono bg-slate-100 px-2 py-1 rounded text-slate-900">{{ $longitude }}</span>
                            </div>
                        </div>
                        @if(isset($distance))
                        <p class="mt-2 text-sm font-semibold text-green-700 bg-green-50 px-3 py-1 rounded-full inline-flex items-center gap-1">
                            ✅ Distance du site: <span class="font-mono">{{ $distance }}m</span>
                        </p>
                        @endif
                        <p class="text-xs text-slate-500 mt-1">Précision: {{ $accuracy ?? 'N/A' }} mètres</p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 md:col-span-2">
                        <h3 class="font-semibold text-slate-800 mb-2">Heure de pointage</h3>
                        <p class="text-2xl font-bold text-slate-900">{{ $pointage_time }}</p>
                        <p class="text-sm text-slate-500">{{ now()->diffForHumans() }}</p>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-200">
                    <form id="pointageForm" method="POST" action="{{ route('presence.confirm') }}" class="flex-1">
                        @csrf
                        @foreach($form_data ?? [] as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit" id="submitBtn"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 focus:bg-emerald-700 text-white font-bold py-4 px-6 rounded-xl text-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-3 group relative overflow-hidden">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Valider le pointage
                            </span>
                            <div id="spinner" class="absolute inset-0 flex items-center justify-center opacity-0 invisible transition-all duration-300">
                                <svg class="w-6 h-6 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </form>

                    <a href="{{ route('presence.pointage') }}" class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-4 px-6 rounded-xl text-lg text-center shadow-sm hover:shadow-md transition-all">
                        Annuler & Modifier
                    </a>
                </div>

                {{-- Optimistic preview --}}
                <div id="optimisticPreview" class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl opacity-0 scale-95 transition-all duration-300 hidden">
                    <div class="flex items-center gap-3 text-emerald-800">
                        <div class="w-10 h-10 bg-emerald-500 text-white rounded-2xl flex items-center justify-center animate-bounce">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">✅ Pointage en cours de validation...</h3>
                            <p class="text-sm">Vous serez redirigé vers l'historique dans quelques instants !</p>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-slate-500 text-center">
                    Après validation, vous serez redirigé vers l'historique de vos présences.
                </div>

            </div>
            {{-- JS pour loading + optimistic --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('pointageForm');
                    const submitBtn = document.getElementById('submitBtn');
                    const spinner = document.getElementById('spinner');
                    const textSpan = submitBtn.querySelector('span');
                    const optimisticPreview = document.getElementById('optimisticPreview');

                    form.addEventListener('submit', function(e) {
                        // Disable button + show spinner
                        submitBtn.disabled = true;
                        textSpan.style.opacity = '0';
                        spinner.classList.remove('opacity-0', 'invisible');

                        // Show optimistic preview after short delay
                        setTimeout(() => {
                            optimisticPreview.classList.remove('opacity-0', 'scale-95', 'hidden');
                            optimisticPreview.classList.add('opacity-100', 'scale-100');
                        }, 300);
                    });
                });
            </script>
        </div>
    </div>
    </div>
</x-app-layout>