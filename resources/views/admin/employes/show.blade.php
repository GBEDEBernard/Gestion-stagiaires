<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('employes.index') }}" class="p-2 bg-gray-100 rounded-xl">←</a>
                <h1 class="text-3xl font-bold">Fiche employé</h1>
            </div>
            <p class="text-gray-500 ml-14">Informations professionnelles et personnelles.</p>
        </div>

        @php $p = $employe->personnel; @endphp

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <div class="p-6 space-y-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $p->prenom }} {{ $p->nom }}</h2>
                        <span class="text-sm text-gray-500">{{ $employe->matricule }}</span>
                    </div>
                    @if($p->user)
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm">✔️ Compte actif</span>
                    @else
                        <form action="{{ route('employes.generate-account', $employe) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl">Générer un compte</button>
                        </form>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><h3 class="text-sm text-gray-500">Email</h3><p>{{ $p->email }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Téléphone</h3><p>{{ $p->telephone ?? '-' }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Genre</h3><p>{{ $p->genre ?? '-' }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Date de naissance</h3><p>{{ $p->date_naissance ?? '-' }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Domaine</h3><p>{{ $employe->domaine->nom }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Site</h3><p>{{ $employe->site->name }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Poste</h3><p>{{ $employe->poste ?? '-' }}</p></div>
                </div>

                @if($p->user)
                    <div class="bg-gray-50 rounded-xl p-4 mt-4">
                        <h3 class="font-semibold">Compte utilisateur</h3>
                        <p>Statut : {{ $p->user->status }}</p>
                        <p>Email vérifié : {{ $p->user->email_verified_at ? 'Oui' : 'Non' }}</p>
                    </div>
                @endif

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('employes.edit', $employe) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-xl">Modifier</a>
                    <form action="{{ route('employes.destroy', $employe) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-xl">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>