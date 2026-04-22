<x-app-layout>
<<<<<<< HEAD
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
=======
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
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                    </div>

                </div>

                {{-- Actions --}}
<<<<<<< HEAD
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-200">
                    <form id="pointageForm" method="POST" action="{{ route('presence.confirm') }}" class="flex-1">
=======
                <div class="space-y-3">
                    <form id="pointageForm" method="POST" action="{{ route('presence.confirm') }}">
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                        @csrf
                        @foreach($form_data ?? [] as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit" id="submitBtn"
<<<<<<< HEAD
                            class="w-full bg-emerald-600 hover:bg-emerald-700 focus:bg-emerald-700 text-white font-bold py-4 px-6 rounded-xl text-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-3 group relative overflow-hidden">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
=======
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-md transition-colors duration-200 flex items-center justify-center relative">
                            <span id="buttonText" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Valider le pointage
                            </span>
<<<<<<< HEAD
                            <div id="spinner" class="absolute inset-0 flex items-center justify-center opacity-0 invisible transition-all duration-300">
                                <svg class="w-6 h-6 animate-spin text-white" fill="none" viewBox="0 0 24 24">
=======
                            <div id="spinner" class="absolute inset-0 flex items-center justify-center opacity-0">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </form>

<<<<<<< HEAD
                    <a href="{{ route('presence.pointage') }}" class="flex-1 sm:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-4 px-6 rounded-xl text-lg text-center shadow-sm hover:shadow-md transition-all">
=======
                    <a href="{{ route('presence.pointage') }}"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-md transition-colors duration-200 text-center block">
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                        Annuler & Modifier
                    </a>
                </div>

                {{-- Optimistic preview --}}
<<<<<<< HEAD
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
=======
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
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
                        </div>
                    </div>
                </div>

<<<<<<< HEAD
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
=======
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pointageForm');
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('buttonText');
            const optimisticPreview = document.getElementById('optimisticPreview');

            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                buttonText.style.opacity = '0';
                spinner.classList.remove('opacity-0');

                setTimeout(() => {
                    optimisticPreview.classList.remove('hidden');
                }, 300);
            });
        });
    </script>
>>>>>>> 7f86b0b18054b451357562162fff94988eac643a
</x-app-layout>