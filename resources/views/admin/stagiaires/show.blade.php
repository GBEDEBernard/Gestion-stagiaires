<x-app-layout>
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-6">

            {{-- Breadcrumbs / entête --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Profil stagiaire</h1>
                    <p class="text-sm text-gray-500">
                        <a href="{{ route('stagiaires.index') }}" class="hover:underline">Stagiaires</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-700">{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('stagiaires.edit', $stagiaire->id) }}"
                       class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700" data-confirm-edit>
                        Modifier
                    </a>
                    <form action="{{ route('stagiaires.destroy', $stagiaire->id) }}" method="POST" data-confirm-delete>
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                            Supprimer
                        </button>
                    </form>
                   
                    <a href="{{ route('stagiaires.badge', $stagiaire->id) }}" target="_blank"
                        class="px-4 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300">
                        Voir / Imprimer le badge
                    </a>
                </div>
            </div>

            {{-- Carte de profil --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start gap-6">
                    
                    {{-- ✅ Logo intégré --}}
                    <div class="h-20 w-20 rounded-full overflow-hidden shadow">
                        <img src="{{ asset('images/TGFpdf.jpg') }}" 
                             alt="Logo entreprise" 
                             class="h-full w-full object-cover">
                    </div>

                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">
                                    {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                                </h2>
                                <p class="text-gray-500">{{ $stagiaire->ecole }}</p>
                            </div>

                            <div class="mt-3 sm:mt-0">
                                @if($statutEnCours)
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-green-100 text-green-700">
                                        ● En cours
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-red-100 text-red-700">
                                        ● Terminé
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-xs uppercase text-gray-500">Email</p>
                                <p class="text-gray-800 font-medium">{{ $stagiaire->email }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-xs uppercase text-gray-500">Téléphone</p>
                                <p class="text-gray-800 font-medium">{{ $stagiaire->telephone }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-xs uppercase text-gray-500">Type de stage</p>
                                <p class="text-gray-800 font-medium">{{ $stagiaire->typestage->libelle ?? '—' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-xs uppercase text-gray-500">Badge</p>
                                <p class="text-gray-800 font-medium">{{ $stagiaire->badge->badge ?? '—' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-xs uppercase text-gray-500">Thème</p>
                                <p class="text-gray-800 font-medium">{{ $stagiaire->theme }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-4">
                                <p class="text-xs uppercase text-gray-500">Période</p>
                                <p class="text-gray-800 font-medium">
                                    {{ \Illuminate\Support\Carbon::parse($stagiaire->date_debut)->format('d/m/Y') }}
                                    —
                                    {{ \Illuminate\Support\Carbon::parse($stagiaire->date_fin)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Jours --}}
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Jours de présence</h3>
                            @if($stagiaire->jours->count())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($stagiaire->jours as $jour)
                                        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-sm">
                                            {{ $jour->jour }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">Aucun jour associé.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- CTA bas de page --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('stagiaires.index') }}" class="text-gray-600 hover:underline">
                    ← Retour à la liste
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('stagiaires.edit', $stagiaire->id) }}"
                       class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                        Modifier le profil
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
