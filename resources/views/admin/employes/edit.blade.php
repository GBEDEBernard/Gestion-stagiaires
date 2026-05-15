<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('employes.index') }}" class="p-2 bg-gray-100 rounded-xl">←</a>
                <h1 class="text-3xl font-bold">Modifier l'employé</h1>
            </div>
            <p class="text-gray-500 ml-14">Mettez à jour les informations de l'employé.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <form action="{{ route('employes.update', $employe) }}" method="POST" class="p-6 space-y-5">
                @csrf @method('PUT')
                @php $p = $employe->personnel; @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Nom *</label><input type="text" name="nom" value="{{ old('nom', $p->nom) }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                    <div><label>Prénom *</label><input type="text" name="prenom" value="{{ old('prenom', $p->prenom) }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Email *</label><input type="email" name="email" value="{{ old('email', $p->email) }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                    <div><label>Téléphone</label><input type="text" name="telephone" value="{{ old('telephone', $p->telephone) }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Genre</label><select name="genre" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"><option value="">--</option><option {{ $p->genre == 'Masculin' ? 'selected' : '' }}>Masculin</option><option {{ $p->genre == 'Féminin' ? 'selected' : '' }}>Féminin</option></select></div>
                    <div><label>Date de naissance</label><input type="date" name="date_naissance" value="{{ old('date_naissance', $p->date_naissance) }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Domaine *</label><select name="domaine_id" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl">@foreach($domaines as $d)<option value="{{ $d->id }}" {{ $employe->domaine_id == $d->id ? 'selected' : '' }}>{{ $d->nom }}</option>@endforeach</select></div>
                    <div><label>Site *</label><select name="site_id" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl">@foreach($sites as $s)<option value="{{ $s->id }}" {{ $employe->site_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label>Poste</label><input type="text" name="poste" value="{{ old('poste', $employe->poste) }}" class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                    <div><label>Matricule *</label><input type="text" name="matricule" value="{{ old('matricule', $employe->matricule) }}" required class="w-full px-4 py-3 bg-gray-50 border rounded-xl"></div>
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <a href="{{ route('employes.index') }}" class="px-6 py-3 bg-gray-100 rounded-xl">Annuler</a>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>