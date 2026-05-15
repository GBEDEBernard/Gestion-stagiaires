<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('employes.index') }}" class="p-2 bg-gray-100 rounded-xl hover:bg-gray-200">←</a>
                <h1 class="text-3xl font-bold">Nouvel employé</h1>
            </div>
            <p class="text-gray-500 ml-14">Ajoutez un employé. Le compte utilisateur pourra être généré plus tard.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <form action="{{ route('employes.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Nom *</label><input type="text" name="nom" value="{{ old('nom') }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                    <div><label>Prénom *</label><input type="text" name="prenom" value="{{ old('prenom') }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Email *</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                    <div><label>Téléphone</label><input type="text" name="telephone" value="{{ old('telephone') }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Genre</label><select name="genre" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"><option value="">--</option><option>Masculin</option><option>Féminin</option></select></div>
                    <div><label>Date de naissance</label><input type="date" name="date_naissance" value="{{ old('date_naissance') }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Domaine *</label><select name="domaine_id" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl">@foreach($domaines as $d)<option value="{{ $d->id }}">{{ $d->nom }}</option>@endforeach</select></div>
                    <div><label>Site *</label><select name="site_id" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl">@foreach($sites as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Poste</label><input type="text" name="poste" value="{{ old('poste') }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                    <div><label>Matricule *</label><input type="text" name="matricule" value="{{ old('matricule') }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <a href="{{ route('employes.index') }}" class="px-6 py-3 bg-gray-100 rounded-xl">Annuler</a>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>