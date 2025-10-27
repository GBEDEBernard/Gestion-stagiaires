<x-app-layout>
<div class="py-10 max-w-6xl mx-auto px-6 dark:text-gray-700">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold dark:text-gray-200">Les responsable de la Technologie forever group</h1>
        <a href="{{ route('signataires.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Ajouter</a>
    </div>

    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">Nom</th>
                <th class="px-4 py-2">Poste</th>
                <th class="px-4 py-2">Sigle</th>
                <th class="px-4 py-2">Ordre</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($signataires as $signataire)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $signataire->nom }}</td>
                    <td class="px-4 py-2">{{ $signataire->poste }}</td>
                    <td class="px-4 py-2">{{ $signataire->sigle }}</td>
                    <td class="px-4 py-2">{{ $signataire->ordre ?? '-' }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('signataires.edit', $signataire->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded" data-confirm-edit >Edit</a>
                        <form action="{{ route('signataires.destroy', $signataire->id) }}" method="POST" data-confirm-delete>
                            @csrf @method('DELETE')
                            <button class="bg-red-600 text-white px-2 py-1 rounded">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</x-app-layout>
