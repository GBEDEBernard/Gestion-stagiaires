<x-app-layout>
<div class="bg-gray-100 min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-6">

        {{-- Breadcrumbs --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Profil de l'étudiant</h1>
                <p class="text-sm text-gray-500">
                    <a href="{{ route('stages.index') }}" class="hover:underline">Stages</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</span>
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('stages.index') }}" class="px-4 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">← Retour</a>
                <a href="{{ route('stages.edit', $stage->id) }}" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Modifier</a>
                <form action="{{ route('stages.destroy', $stage->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce stage ?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Supprimer</button>
                </form>
                @if($stage->badge)
                    <button onclick="document.getElementById('badgeModal').classList.remove('hidden')"
                            class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">
                        Voir badge
                    </button>
                @endif
            </div>
        </div>

        {{-- Profil complet --}}
        <div class="bg-white rounded-lg shadow p-6">
            {{-- En-tête --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-6 border-b border-gray-200 pb-6">
                <div class="h-28 w-28 rounded-full overflow-hidden shadow flex-shrink-0">
                    <img src="{{ asset('images/TGFpdf.jpg') }}" alt="Photo étudiant" class="h-full w-full object-cover">
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $stage->etudiant->nom }} {{ $stage->etudiant->prenom }}</h2>
                    <p class="text-gray-500">{{ $stage->etudiant->ecole }}</p>
                    <p class="text-gray-500">{{ $stage->etudiant->adresse ?? 'Adresse non renseignée' }}</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    @if($statutEnCours == 'En cours')
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-green-100 text-green-700">● En cours</span>
                    @elseif($statutEnCours == 'À venir')
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-700">● À venir</span>
                    @else
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-red-100 text-red-700">● Terminé</span>
                    @endif
                </div>
            </div>

            {{-- Infos principales --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Email</p><p class="text-gray-800 font-medium">{{ $stage->etudiant->email }}</p></div>
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Téléphone</p><p class="text-gray-800 font-medium">{{ $stage->etudiant->telephone }}</p></div>
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Type de stage</p><p class="text-gray-800 font-medium">{{ $stage->typestage->libelle ?? '—' }}</p></div>
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Badge</p><p class="text-gray-800 font-medium">{{ $stage->badge->badge ?? '—' }}</p></div>
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Thème</p><p class="text-gray-800 font-medium">{{ $stage->theme }}</p></div>
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Période</p><p class="text-gray-800 font-medium">{{ $stage->date_debut?->format('d/m/Y') ?? '—' }} à {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}</p></div>
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Service</p><p class="text-gray-800 font-medium">{{ $stage->service->nom ?? 'Aucun' }}</p><p class="text-sm text-gray-600">Responsable : {{ $stage->service->responsable ?? '—' }}</p></div>
                <div class="bg-gray-50 rounded p-4"><p class="text-xs uppercase text-gray-500">Notes / Observations</p><p class="text-gray-800 font-medium">{{ $stage->etudiant->notes ?? '—' }}</p></div>
            </div>
        </div>

        {{-- Modal Badge --}}
        <div id="badgeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-sm relative">
                <div id="badge" class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">

                    {{-- Header --}}
                    <div class="text-center mb-2 bg-blue-200 py-3 border-b border-gray-400">
                        <h1 class="text-lg font-bold text-blue-700">TECHNOLOGY FOREVER GROUP (TFG)</h1>
                    </div>

                    {{-- Logo --}}
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/TGFpdf.jpg') }}" alt="TFG Logo"
                             class="mx-auto w-24 h-24 rounded-full object-cover -mt-4">
                    </div>

                    {{-- Infos stagiaire --}}
                    <div class="mb-4 text-center px-3">
                        <h2 class="text-xl font-bold text-gray-800 mb-2 border-b pb-1">Stagiaire</h2>
                        <div class="text-gray-800 text-sm space-y-1 mt-2">
                            <span class="block text-xl font-extrabold text-red-700">
                                {{ str_pad($stage->badge->badge ?? '000000', 6, '0', STR_PAD_LEFT) }}
                            </span>
                            <p><b>Nom :</b> {{ $stage->etudiant->nom ?? '—' }} {{ $stage->etudiant->prenom ?? '—' }}</p>
                            <p><b>École :</b> {{ $stage->etudiant->ecole ?? '—' }}</p>
                            <p><b>Type :</b> {{ $stage->typestage->libelle ?? '—' }}</p>
                            <p><b>Tél :</b> {{ $stage->etudiant->telephone ?? '—' }}</p>
                            <p><b>Dates :</b> {{ $stage->date_debut?->format('d/m/Y') ?? '—' }} - {{ $stage->date_fin?->format('d/m/Y') ?? '—' }}</p>
                            <p><b>Email :</b> {{ $stage->etudiant->email ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-4 text-white font-bold p-3 bg-gradient-to-r from-blue-900 to-red-700 border-t-2 border-blue-900 text-xs">
                        <div class="flex flex-col items-center space-y-1">
                            <div class="flex items-center gap-1">📍 <span>Abomey-Calavi (Togoudo)</span></div>
                            <div class="flex items-center gap-1">📞 <span>0166439030 / 0169580603</span></div>
                            <div class="flex items-center gap-1">🌐 <span>www.tfgbusiness.com</span></div>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="mt-4 flex justify-end gap-2">
                    <button onclick="window.print()" class="px-3 py-2 rounded-md bg-blue-600 text-white font-bold hover:bg-blue-700">Imprimer</button>
                    <button onclick="document.getElementById('badgeModal').classList.add('hidden')" class="px-3 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">Fermer</button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #badge, #badge * { visibility: visible; }
    #badge {
        position: fixed;
        left: 50%;
        top: 20px;
        transform: translateX(-50%);
        width: 320px;
        page-break-inside: avoid;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    #badgeModal, #badgeModal * { visibility: hidden; }
    #badgeModal #badge, #badgeModal #badge * { visibility: visible; }
    @page { margin: 10mm; }
}
</style>
</x-app-layout>
