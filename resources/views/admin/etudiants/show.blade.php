<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('etudiants.index') }}" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Fiche étudiant</h1>
            </div>
            <p class="text-gray-500 ml-14">Informations détaillées du stagiaire.</p>
        </div>

        @php $p = $etudiant->personnel; @endphp

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between border-b pb-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $p->prenom }} {{ $p->nom }}</h2>
                        @if($p->genre)<span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">{{ $p->genre }}</span>@endif
                    </div>
                    @if($p->user)
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm font-medium">✔️ Compte actif</span>
                    @else
                        <form action="{{ route('etudiants.generate-account', $etudiant) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">Générer un compte</button>
                        </form>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><h3 class="text-sm text-gray-500">Email</h3><p class="text-lg">{{ $p->email }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Téléphone</h3><p class="text-lg">{{ $p->telephone ?? '-' }}</p></div>
                    <div><h3 class="text-sm text-gray-500">École</h3><p class="text-lg">{{ $etudiant->ecole ?? '-' }}</p></div>
                    <div><h3 class="text-sm text-gray-500">Niveau</h3><p class="text-lg">{{ $etudiant->niveau ?? '-' }}</p></div>
                </div>

                @if($p->user)
                    <div class="bg-gray-50 rounded-xl p-4 mt-4">
                        <h3 class="font-semibold mb-2">Compte utilisateur</h3>
                        <p>Statut : {{ $p->user->status }}</p>
                        <p>Email vérifié : {{ $p->user->email_verified_at ? 'Oui' : 'Non' }}</p>
                        <p>Doit changer mot de passe : {{ $p->user->must_change_password ? 'Oui' : 'Non' }}</p>
                    </div>
                @endif

                <div class="pt-4 flex gap-3">
                    <a href="{{ route('etudiants.edit', $etudiant) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition">Modifier</a>
                    <form action="{{ route('etudiants.destroy', $etudiant) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>