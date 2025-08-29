<x-app-layout>
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold text-blue-600 mb-4">Modifier l’étudiant</h1>

        <form action="{{ route('etudiants.update', $etudiant) }}" method="POST" class="space-y-4 bg-white p-6 shadow rounded-xl">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-red-600">Nom</label>
                <input type="text" name="nom" value="{{ old('nom', $etudiant->nom) }}" class="w-full border rounded-xl p-2" required>
            </div>

            <div>
                <label class="block text-red-600">Prénom</label>
                <input type="text" name="prenom" value="{{ old('prenom', $etudiant->prenom) }}" class="w-full border rounded-xl p-2" required>
            </div>

            <div>
                <label class="block text-red-600">Email</label>
                <input type="email" name="email" value="{{ old('email', $etudiant->email) }}" class="w-full border rounded-xl p-2" required>
            </div>

            <div>
                <label class="block text-red-600">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone', $etudiant->telephone) }}" class="w-full border rounded-xl p-2" required>
            </div>

            <div>
                <label class="block text-red-600">École</label>
                <input type="text" name="ecole" value="{{ old('ecole', $etudiant->ecole) }}" class="w-full border rounded-xl p-2">
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded-xl shadow hover:bg-blue-700">Mettre à jour</button>
        </form>
    </div>
</x-app-layout>
